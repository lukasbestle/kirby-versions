<?php

namespace LukasBestle\Versions;

use Kirby\Cms\App;
use Kirby\Exception\Exception;
use Kirby\Exception\LogicException;
use Kirby\Exception\PermissionException;
use Kirby\Filesystem\Dir;

/**
 * Plugin
 * Main plugin class responsible for general tasks
 *
 * @package   Kirby Versions Plugin
 * @author    Lukas Bestle <project-kirbyversions@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-versions
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 */
class Plugin
{
	/**
	 * Singleton plugin instance
	 *
	 * @var self|null
	 */
	protected static $instance;

	/**
	 * Collection of configured site instances
	 *
	 * @var \LukasBestle\Versions\Instances|null
	 */
	protected $instances;

	/**
	 * Kirby App instance
	 *
	 * @var \Kirby\Cms\App
	 */
	protected $kirby;

	/**
	 * Whether the environment has already been validated
	 *
	 * @var bool
	 */
	protected $validated = false;

	/**
	 * Collection of the existing versions
	 *
	 * @var \LukasBestle\Versions\Versions|null
	 */
	protected $versions;

	/**
	 * Class constructor
	 *
	 * @param \Kirby\Cms\App|null $kirby Kirby App instance to use (optional)
	 */
	public function __construct(?App $kirby = null)
	{
		/** @psalm-suppress PossiblyNullPropertyAssignmentValue */
		$this->kirby = $kirby ?? App::instance();
	}

	/**
	 * Ensures that the current user has the specified permission
	 *
	 * @param string $permission
	 * @return void
	 *
	 * @throws \Kirby\Exception\LogicException If no user is logged in
	 * @throws \Kirby\Exception\PermissionException If the user does not have the required permission
	 */
	public function checkPermission(string $permission): void
	{
		if ($this->hasPermission($permission) !== true) {
			throw new PermissionException([
				'key'  => 'versions.permission',
				'data' => compact('permission')
			]);
		}
	}

	/**
	 * Ensures that the exports directory exists and
	 * cleans all expired exports from the media folder
	 *
	 * @return void
	 */
	public function cleanExports(): void
	{
		$root = $this->exportRoot();

		// ensure that the directory exists
		Dir::make($root);

		// check for files that have not been modified in the last two hours
		foreach (Dir::files($root, null, true) as $file) {
			$modified = filemtime($file);
			if (is_int($modified) === true && $modified < time() - 2 * 60 * 60) {
				unlink($file);
			}
		}
	}

	/**
	 * Returns the root of the exports directory
	 *
	 * @return string
	 */
	public function exportRoot(): string
	{
		return $this->kirby->roots()->media() . '/versions-export';
	}

	/**
	 * Returns the URL of the exports directory
	 *
	 * @return string
	 */
	public function exportUrl(): string
	{
		return $this->kirby->urls()->media() . '/versions-export';
	}

	/**
	 * Runs a Git command in the specified instance directory
	 *
	 * @param \LukasBestle\Versions\Instance|null $instance Instance or `null` for the current site
	 * @param string ...$arguments List of arguments to pass to the Git binary
	 * @return string STDOUT and STDERR from Git
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function gitCommand(?Instance $instance, ...$arguments): string
	{
		// prepend the Git path and the repo path to the arguments
		$path = $instance ? $instance->contentRoot() : $this->kirby()->roots()->content();
		$git  = [$this->option('git.path'), '-C', $path];
		array_unshift($arguments, ...$git);

		// assemble the command string and escape each argument
		$parts = [];
		foreach ($arguments as $argument) {
			$parts[] = escapeshellarg($argument);
		}
		$command = implode(' ', $parts);

		// execute the command, collect $output (including STDERR) and $returnVar
		$output = $returnVar = null;
		exec($command . ' 2>&1', $output, $returnVar);
		$output = implode("\n", $output); // exec() returns an array of output lines

		// validate that the command succeeded
		if ($returnVar !== 0) {
			throw new Exception([
				'key'  => 'versions.git.nonzero',
				'data' => ['message' => $output]
			]);
		}

		return $output;
	}

	/**
	 * Returns whether the current user has the specified permission
	 *
	 * @param string $permission
	 * @return bool
	 *
	 * @throws \Kirby\Exception\LogicException If no user is logged in
	 */
	public function hasPermission(string $permission): bool
	{
		$user = $this->kirby->user();
		if ($user === null) {
			throw new LogicException([
				'key'  => 'versions.internal',
				'data' => ['code' => 'user-not-logged-in']
			]);
		}

		$permissions = $user->role()->permissions();
		return $permissions->for('lukasbestle.versions', $permission);
	}

	/**
	 * Returns the singleton plugin instance
	 *
	 * @param \Kirby\Cms\App|null $kirby Kirby App instance to use (optional)
	 * @return self
	 */
	public static function instance(?App $kirby = null): self
	{
		if (
			self::$instance !== null &&
			($kirby === null || self::$instance->kirby() === $kirby)
		) {
			return self::$instance;
		}

		return self::$instance = new self($kirby);
	}

	/**
	 * Returns the collection of configured site instances
	 *
	 * @return \LukasBestle\Versions\Instances
	 *
	 * @throws \Kirby\Exception\Exception If the environment validation failed
	 */
	public function instances(): Instances
	{
		if ($this->instances !== null) {
			return $this->instances;
		}

		$this->validate();
		return $this->instances = new Instances($this);
	}

	/**
	 * Returns the Kirby App instance
	 *
	 * @return \Kirby\Cms\App
	 */
	public function kirby(): App
	{
		return $this->kirby;
	}

	/**
	 * Returns a plugin option value
	 *
	 * @param string $key Option key
	 * @return mixed
	 */
	public function option(string $key)
	{
		return $this->kirby()->option('lukasbestle.versions.' . $key);
	}

	/**
	 * Returns the plugin state as array filtered
	 * to the necessary data for the API
	 *
	 * @param array $additionalData Data that gets merged with the plugin
	 *                              data or returned when the user doesn't
	 *                              have the `access` permission
	 * @return array
	 */
	public function toApiData(array $additionalData = []): array
	{
		if ($this->hasPermission('access') !== true) {
			return $additionalData;
		}

		$instances = $this->instances()
			->toArray(function (Instance $instance) {
				$data = $instance->toArray();

				// don't leak internal data
				unset($data['contentRoot'], $data['currentCommit']);

				return $data;
			});

		$versions = $this->versions()
			->sortBy('created', 'desc', 'name', 'asc')
			->toArray(function (Version $version) {
				$data = $version->toArray();

				// don't leak internal data
				unset($data['commit']);
				return $data;
			});

		return array_merge(compact('instances', 'versions'), $additionalData);
	}

	/**
	 * Returns the plugin state as array
	 *
	 * @return array
	 */
	public function toArray(): array
	{
		$map = function (object $object): array {
			/** @var Instance|Version $object */
			return $object->toArray();
		};

		return [
			'instances' => $this->instances()->toArray($map),
			'versions'  => $this->versions()->toArray($map)
		];
	}

	/**
	 * Validates the site's environment against
	 * plugin requirements
	 *
	 * @return void
	 *
	 * @throws \Kirby\Exception\Exception If the environment validation failed
	 */
	public function validate(): void
	{
		// only run the validations once;
		// immediately set the flag to true to avoid infinite loops
		if ($this->validated === true) {
			return;
		}
		$this->validated = true;

		// try to get and parse the Git version
		$version = $this->gitCommand(null, 'version');
		$matches = null;
		if (preg_match('/^git version ([0-9]+\.[0-9]+\.[0-9]+)/', $version, $matches) !== 1) {
			throw new Exception([
				'key'  => 'versions.internal',
				'data' => ['code' => 'git-version-unparseable']
			]);
		}

		// ensure that the Git version is recent enough
		if (version_compare($matches[1], '2.5.0', '<') === true) {
			throw new Exception([
				'key'  => 'versions.git.version',
				'data' => ['version' => $matches[1]]
			]);
		}

		// initialize the Instances object
		// (which runs additional validations)
		$this->instances();
	}

	/**
	 * Returns the collection of the existing versions
	 *
	 * @return \LukasBestle\Versions\Versions
	 *
	 * @throws \Kirby\Exception\Exception If the environment validation failed
	 */
	public function versions(): Versions
	{
		if ($this->versions !== null) {
			return $this->versions;
		}

		$this->validate();
		return $this->versions = new Versions($this);
	}
}
