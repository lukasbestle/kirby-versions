<?php

namespace LukasBestle\Versions;

use Kirby\Exception\Exception;
use Kirby\Exception\NotFoundException;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Str;

/**
 * Instances
 * Collection of configured site instances
 *
 * @package   Kirby Versions Plugin
 * @author    Lukas Bestle <project-kirbyversions@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-versions
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 *
 * @method \LukasBestle\Versions\Instance get($key, $default = null)
 */
class Instances extends Collection
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
	 * @throws \Kirby\Exception\Exception If the configuration is invalid
	 */
	public function __construct(Plugin $plugin)
	{
		$this->plugin = $plugin;

		$currentContentRoot  = $plugin->kirby()->roots()->content();
		$config              = $plugin->option('instances');
		$instances           = [];
		$initializeLocalSite = true;

		// initialize the configured instances if set
		if (is_array($config) === true) {
			foreach ($config as $name => $props) {
				$props['name']    = $name;
				$props['plugin']  = $plugin;
				$instances[$name] = new Instance($props);

				// the local site doesn't need to be initialized if already configured
				if ($props['contentRoot'] === $currentContentRoot) {
					$initializeLocalSite = false;
				}
			}
		}

		// prepend the local site if not already configured
		if ($initializeLocalSite === true) {
			/** @var string $name */
			$name = I18n::translate('versions.name.local');
			$instances = [$name => new Instance([
				'contentRoot' => $currentContentRoot,
				'color'       => 'var(--color-focus-light)',
				'name'        => $name,
				'plugin'      => $plugin
			])] + $instances;
		}

		// set the instances in the collection and enable case-sensitive mode
		parent::__construct($instances, true);

		// check the initialized instances for configuration issues
		$this->validate();
	}

	/**
	 * Returns the specified instance or throws an
	 * Exception if not found
	 *
	 * @param string $name
	 * @return \LukasBestle\Versions\Instance
	 *
	 * @throws \Kirby\Exception\NotFoundException If the instance was not found
	 */
	public function findOrException(string $name): Instance
	{
		$instance = $this->find($name);
		if (!$instance) {
			throw new NotFoundException([
				'key'  => 'versions.notFound.instance',
				'data' => ['name' => $name]
			]);
		}

		return $instance;
	}

	/**
	 * Validates that all configured instances are connected
	 * worktrees and have a detached head; the current commit
	 * of each instance is set in the process
	 *
	 * @return void
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 * @throws \Kirby\Exception\Exception If the configuration is invalid
	 */
	protected function validate(): void
	{
		// determine the Git worktrees for validation
		$worktreesRaw = $this->plugin->gitCommand(null, 'worktree', 'list', '--porcelain');
		$worktrees    = [];
		foreach (explode("\n\n", trim($worktreesRaw)) as $worktree) {
			$attributesRaw = explode("\n", $worktree);
			$attributes    = [];
			$path          = null;

			foreach ($attributesRaw as $attribute) {
				if (Str::contains($attribute, ' ')) {
					$label = Str::before($attribute, ' ');
					$value = Str::after($attribute, ' ');
				} else {
					$label = $attribute;
					$value = true;
				}


				if ($label === 'worktree') {
					$path = $value;
				} else {
					$attributes[$label] = $value;
				}
			}

			// if no line has the label "worktree", the output
			// of this worktree is unexpected and we cannot continue
			if (is_string($path) !== true) {
				throw new Exception([
					'key'  => 'versions.internal',
					'data' => ['code' => 'git-worktree-invalid']
				]);
			}

			$worktrees[$path] = $attributes;
		}

		// validate each instance against the worktree setup
		foreach ($this as $instance) {
			/** @var \LukasBestle\Versions\Instance $instance */

			// the configured instance needs to be a connected worktree
			$contentRoot = $instance->contentRoot();
			if (isset($worktrees[$contentRoot]) !== true) {
				throw new Exception([
					'key'  => 'versions.instance.noWorktree',
					'data' => ['instance' => $instance->name()]
				]);
			}

			// the instance must have a detached head
			$worktree = $worktrees[$contentRoot];
			if (isset($worktree['detached']) !== true) {
				throw new Exception([
					'key'  => 'versions.instance.onBranch',
					'data' => ['instance' => $instance->name()]
				]);
			}

			/** @var string $commit */
			$commit = $worktree['HEAD'];

			// set the current commit for later use
			$instance->setCurrentCommit($commit);
		}
	}
}
