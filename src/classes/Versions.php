<?php

namespace LukasBestle\Versions;

use Kirby\Exception\NotFoundException;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\Str;

/**
 * Versions
 * Collection of the existing versions
 *
 * @package   Kirby Versions Plugin
 * @author    Lukas Bestle <project-kirbyversions@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-versions
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 *
 * @method \LukasBestle\Versions\Version findBy(string $attribute, $value)
 * @method \LukasBestle\Versions\Version get($key, $default = null)
 */
class Versions extends Collection
{
	/**
	 * Instance of the Plugin class
	 *
	 * @var \LukasBestle\Versions\Plugin
	 */
	protected $plugin;

	/**
	 * Class constructor
	 *
	 * @param \LukasBestle\Versions\Plugin $plugin
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function __construct(Plugin $plugin)
	{
		$this->plugin = $plugin;

		// set case-sensitive mode
		parent::__construct([], true);

		$this->update();
		$this->autodelete();
	}

	/**
	 * Automatically cleans up old versions
	 *
	 * @return void
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function autodelete(): void
	{
		$autodeleteAge   = $this->plugin->option('autodelete.age');
		$autodeleteCount = $this->plugin->option('autodelete.count');

		// never delete non-annotated versions automatically and
		// always keep versions that are still in use
		$candidates = $this
			->filterBy('created', '!=', null)
			->filter(function ($version) {
				return $version->instances()->count() === 0;
			});

		// first delete by age
		if (is_int($autodeleteAge) === true) {
			$toDelete = $candidates->filterBy('created', '<', time() - $autodeleteAge);
			foreach ($toDelete as $key => $version) {
				$version->delete();
				unset($candidates->$key);
			}
		}

		// now check how many old versions we still need to delete by count
		if (is_int($autodeleteCount) === true) {
			// ensure that the number is never negative
			$numDelete = max(count($this) - $autodeleteCount, 0);
			if ($numDelete === 0) {
				return;
			}

			// delete from the oldest version
			foreach ($candidates->sortBy('created', SORT_ASC) as $key => $version) {
				$version->delete();

				$numDelete--;
				if ($numDelete === 0) {
					break;
				}
			}
		}
	}

	/**
	 * Returns the specified version or throws an
	 * Exception if not found
	 *
	 * @param string $name
	 * @return \LukasBestle\Versions\Version
	 *
	 * @throws \Kirby\Exception\NotFoundException If the instance was not found
	 */
	public function findOrException(string $name): Version
	{
		$version = $this->find($name);
		if (!$version) {
			throw new NotFoundException([
				'key'  => 'versions.notFound.version',
				'data' => ['name' => $name]
			]);
		}

		return $version;
	}

	/**
	 * Loads the current versions from the repository
	 *
	 * @return void
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function update(): void
	{
		$command     = ['tag', '--list', '--format', '%(refname:short)	%(object)	%(objectname)	%(taggerdate)	%(taggeremail)	%(taggername)	%(contents:subject)'];
		$versionsRaw = $this->plugin->gitCommand(null, ...$command);

		// return if there are no versions
		if ($versionsRaw === '') {
			$this->data = [];
			return;
		}

		// parse each version line
		$versions = [];
		foreach (explode("\n", $versionsRaw) as $version) {
			list($name, $commit1, $commit2, $created, $creatorEmail, $creatorName, $label) = explode('	', $version);

			// depending if the tag is annotated or not,
			// either one of these two fields is set
			$commit = $commit1 ? $commit1 : $commit2;

			// extract the origin instance name from the label
			$originInstance = null;
			if (Str::contains($label, ':::') === true) {
				$originInstance = Str::before($label, ':::');
				$label          = Str::after($label, ':::');
			}

			$props = compact('name', 'commit', 'created', 'creatorEmail', 'creatorName', 'label', 'originInstance');
			$props['plugin'] = $this->plugin;
			$versions[$name] = new Version($props);
		}

		// set the new versions in the collection
		$this->data = $versions;
	}
}
