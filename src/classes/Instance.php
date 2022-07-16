<?php

namespace LukasBestle\Versions;

use Kirby\Data\Data;
use Kirby\Exception\Exception;
use Kirby\Exception\LogicException;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Properties;

/**
 * Instance
 * A single configured instance
 *
 * @package   Kirby Versions Plugin
 * @author    Lukas Bestle <project-kirbyversions@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-versions
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 */
class Instance
{
	use Properties;

	/**
	 * Changes object for this instance
	 *
	 * @var \LukasBestle\Versions\Changes|null
	 */
	protected $changes;

	/**
	 * CSS color for display in the Panel
	 *
	 * @var string
	 */
	protected $color;

	/**
	 * Path to the content directory
	 *
	 * @var string
	 */
	protected $contentRoot;

	/**
	 * Current commit of this instance
	 *
	 * @var string|null
	 */
	protected $currentCommit = null;

	/**
	 * Instance name that is displayed in the Panel
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Instance of the Plugin class
	 *
	 * @var \LukasBestle\Versions\Plugin
	 */
	protected $plugin;

	/**
	 * Class constructor
	 *
	 * @param array $props
	 *
	 * @throws \Kirby\Exception\Exception If the configuration is invalid
	 */
	public function __construct(array $props)
	{
		$this->setProperties($props);

		// validate that the instance has a Git repo set up
		try {
			/** @var \LukasBestle\Versions\Plugin $this->plugin */
			if ($this->plugin->gitCommand($this, 'rev-parse', '--is-inside-work-tree') !== 'true') {
				throw new Exception(); // @codeCoverageIgnore
			}
		} catch (Exception $e) {
			throw new Exception([
				'key'      => 'versions.instance.noRepo',
				'data'     => ['instance' => $this->name()],
				'previous' => $e
			]);
		}
	}

	/**
	 * Returns the Changes object for this instance
	 *
	 * @return \LukasBestle\Versions\Changes
	 */
	public function changes(): Changes
	{
		if ($this->changes !== null) {
			return $this->changes;
		}

		return $this->changes = new Changes($this->plugin, $this);
	}

	/**
	 * Returns the CSS color for display in the Panel
	 *
	 * @return string
	 */
	public function color(): string
	{
		return $this->color;
	}

	/**
	 * Returns the path to the content directory
	 *
	 * @return string
	 */
	public function contentRoot(): string
	{
		return $this->contentRoot;
	}

	/**
	 * Creates a new version based on the already
	 * prepared changes with `prepareCreation()`
	 *
	 * @param string $label Custom version label
	 * @return \LukasBestle\Versions\Version
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 * @throws \Kirby\Exception\LogicException If there are no staged files
	 */
	public function createVersion(string $label): Version
	{
		// we can only create a version if there are staged changes
		if ($this->changes()->inIndex() === []) {
			throw new LogicException([
				'key' => 'versions.notPrepared'
			]);
		}

		// collect user data
		$user = $this->plugin->kirby()->user();
		if ($user !== null) {
			$userEmail = $user->email();

			/** @var string $userName */
			$userName = $user->name()->or($userEmail)->value();
		} else {
			$userEmail = 'versions@' . $this->plugin->kirby()->request()->url()->domain();

			/** @var string $userName */
			$userName = I18n::translate('view.versions');
		}

		// auto-generate a unique name
		$date = date('Ymd');
		$nextNumber = count($this->plugin->versions()->filterBy('name', '^=', $date . '_')) + 1;
		do {
			$name = $date . '_' . str_pad((string)($nextNumber), 3, '0', STR_PAD_LEFT);
			$nextNumber++;
		} while ($this->plugin->versions()->has($name) === true);

		// build the label
		$label = $this->name() . ':::' . $label;

		// create a commit and tag with the identity of the current user
		$this->plugin->gitCommand($this, '-c', 'user.name=' . $userName, '-c', 'user.email=' . $userEmail, 'commit', '-m', $label);
		$this->plugin->gitCommand($this, '-c', 'user.name=' . $userName, '-c', 'user.email=' . $userEmail, 'tag', $name, '-a', '-m', $label);

		// update the Versions collection
		$this->plugin->versions()->update();
		$version = $this->plugin->versions()->get($name);

		// update cache
		$this->setCurrentCommit($version->commit());
		$this->changes()->update();

		return $version;
	}

	/**
	 * Returns the current commit of this instance
	 *
	 * @return string|null
	 */
	public function currentCommit(): ?string
	{
		return $this->currentCommit;
	}

	/**
	 * Returns whether the instance is the one of the current site
	 *
	 * @return bool
	 */
	public function isCurrent(): bool
	{
		return $this->contentRoot() === $this->plugin->kirby()->roots()->content();
	}

	/**
	 * Returns the instance name that is displayed in the Panel
	 *
	 * @return string
	 */
	public function name(): string
	{
		return $this->name;
	}

	/**
	 * Prepares version creation by staging all
	 * changes and checking if there are any lock files
	 *
	 * @return void
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 * @throws \Kirby\Exception\LogicException If there are no changed files
	 * @throws \Kirby\Exception\LogicException If there are Kirby lock files
	 */
	public function prepareCreation(): void
	{
		// only do something if there are changes at all
		if ($this->changes()->overall() === []) {
			throw new LogicException([
				'key' => 'versions.noChanges'
			]);
		}

		// first stage everything to ensure that files in
		// untracked directories are listed in the changes
		$this->plugin->gitCommand($this, 'add', '-A');
		$this->changes()->update();

		// now ensure that there no Kirby models are locked
		$lockFiles = $this->changes()->lockFiles();
		if ($lockFiles !== []) {
			$lockedModels = [];

			// parse each lock file and figure out what is locked by whom
			foreach ($lockFiles as $lockFile) {
				/** @var array<string, array{
				 *         lock: array{user: string, email: string, time: int, unlockable: bool},
				 *         unlock: list<string>
				 *       }> $data
				 */
				$data = Data::read($this->contentRoot() . '/' . $lockFile, 'yaml');

				foreach ($data as $modelId => $model) {
					$users = [];

					if (isset($model['lock']['user']) === true) {
						$users[] = $model['lock']['user'];
					}

					if (isset($model['unlock']) === true) {
						$users = array_merge($users, $model['unlock']);
					}

					$lockedModels[$modelId] = array_map(function (string $user) {
						$userObject = $this->plugin->kirby()->user($user);

						if ($userObject !== null) {
							return $userObject->name()->or($userObject->email())->value();
						}

						return $user;
					}, array_values(array_unique($users)));
				}
			}

			if (empty($lockedModels) !== true) {
				// unstage everything again
				$this->plugin->gitCommand($this, 'reset');
				$this->changes()->update();

				throw new LogicException([
					'key'     => 'versions.lockFiles',
					'details' => compact('lockedModels')
				]);
			}
		}

		// ensure that lock files are never staged, even if empty
		$this->plugin->gitCommand($this, 'reset', '--', '.lock', '**/.lock');
		$this->changes()->update();
	}

	/**
	 * Sets the current commit of this instance
	 * @internal
	 *
	 * @param string|null $currentCommit
	 * @return self
	 */
	public function setCurrentCommit(?string $currentCommit = null): self
	{
		$this->currentCommit = $currentCommit;
		return $this;
	}

	/**
	 * Returns the instance data as array
	 *
	 * @return array
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function toArray(): array
	{
		$array = $this->propertiesToArray();
		$array['changes']   = $this->changes()->overall();
		$array['isCurrent'] = $this->isCurrent();

		$version = $this->version();
		$array['version']      = ($version !== null)? $version->name() : null;
		$array['versionLabel'] = ($version !== null)? $version->label() : null;

		ksort($array);
		return $array;
	}

	/**
	 * Returns the current version of this instance
	 *
	 * @return \LukasBestle\Versions\Version|null
	 */
	public function version(): ?Version
	{
		if ($this->currentCommit === null) {
			return null;
		}

		return $this->plugin->versions()->findBy('commit', $this->currentCommit());
	}

	/**
	 * Sets the CSS color for display in the Panel
	 *
	 * @param string $color
	 * @return self
	 */
	protected function setColor(string $color): self
	{
		$this->color = $color;
		return $this;
	}

	/**
	 * Sets the path to the content directory
	 *
	 * @param string $contentRoot
	 * @return self
	 */
	protected function setContentRoot(string $contentRoot): self
	{
		$this->contentRoot = rtrim($contentRoot, '/\\');
		return $this;
	}

	/**
	 * Sets the instance name that is displayed in the Panel
	 *
	 * @param string $name
	 * @return self
	 */
	protected function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Sets the instance of the Plugin class
	 *
	 * @param \LukasBestle\Versions\Plugin $plugin
	 * @return self
	 */
	protected function setPlugin(Plugin $plugin): self
	{
		$this->plugin = $plugin;
		return $this;
	}
}
