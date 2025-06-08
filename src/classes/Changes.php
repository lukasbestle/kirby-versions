<?php

namespace LukasBestle\Versions;

use Kirby\Toolkit\Str;

/**
 * Changes
 * Loads the Git changeset from the repository
 *
 * @package   Kirby Versions Plugin
 * @author    Lukas Bestle <project-kirbyversions@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-versions
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 */
class Changes
{
	/**
	 * Cache for changes in the index
	 *
	 * @var array<string, string|null>
	 */
	protected array $inIndex;

	/**
	 * Configured instance to get changes for
	 */
	protected Instance $instance;

	/**
	 * Cache for changes in the worktree
	 *
	 * @var array<string, string|null>
	 */
	protected array $inWorktree;

	/**
	 * Cache for changed Kirby lock files
	 *
	 * @var array<string>
	 */
	protected array $lockFiles;

	/**
	 * Instance of the Plugin class
	 */
	protected Plugin $plugin;

	/**
	 * Class constructor
	 */
	public function __construct(Plugin $plugin, Instance $instance)
	{
		$this->plugin   = $plugin;
		$this->instance = $instance;

		// initialize the remaining properties
		$this->update();
	}

	/**
	 * Returns the changes in the index
	 *
	 * @return array<string, string|null>
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function inIndex(): array
	{
		return $this->inIndex;
	}

	/**
	 * Returns the changes in the worktree
	 *
	 * @return array<string, string|null>
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function inWorktree(): array
	{
		return $this->inWorktree;
	}

	/**
	 * Returns the changed Kirby lock files
	 *
	 * @return array<string>
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function lockFiles(): array
	{
		return $this->lockFiles;
	}

	/**
	 * Returns the overall changes
	 *
	 * @return array<string, string|null>
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function overall(): array
	{
		// let the worktree override the index because
		// the worktree information is more recent
		$merged = array_merge($this->inIndex, $this->inWorktree);

		// double-check for edge-cases
		foreach (array_keys($merged) as $file) {
			if (
				isset($this->inIndex[$file]) === true &&
				isset($this->inWorktree[$file]) === true
			) {
				$index    = $this->inIndex[$file];
				$worktree = $this->inWorktree[$file];

				// if the file was added but since modified,
				// it was overall still added
				if ($index === '+' && $worktree === 'M') {
					$merged[$file] = '+';
				}

				// if the file was added and deleted,
				// nothing has changed
				if ($index === '+' && $worktree === '-') {
					unset($merged[$file]);
				}
			}
		}

		ksort($merged);
		return $merged;
	}

	/**
	 * Loads the current changes from the worktree
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function update(): void
	{
		// clear the current cache
		$this->inIndex    = [];
		$this->lockFiles  = [];
		$this->inWorktree = [];

		// collect all changed files
		$changes = $this->plugin->gitCommand($this->instance, 'status', '--porcelain=1');

		// return early if there are no changes
		if ($changes === '') {
			return;
		}

		foreach (explode("\n", $changes) as $change) {
			$index    = static::normalizeStatus(substr($change, 0, 1), 'index');
			$worktree = static::normalizeStatus(substr($change, 1, 1), 'worktree');
			$file     = substr($change, 3);

			// collect Kirby lock files separately
			if ($file === '.lock' || Str::endsWith($file, '/.lock') === true) {
				$this->lockFiles[] = $file;
				continue;
			}

			if ($index !== null) {
				$this->inIndex[$file] = $index;
			}

			if ($worktree !== null) {
				$this->inWorktree[$file] = $worktree;
			}
		}

		// sort the arrays by file path
		ksort($this->inIndex);
		ksort($this->inWorktree);
		sort($this->lockFiles);
	}

	/**
	 * Normalizes Git status characters into a human-readable form
	 *
	 * @param string $status A single character Git status
	 * @param string $mode `index` or `worktree`
	 * @return string|null A single human-readable character or `null` for unmodified
	 */
	protected static function normalizeStatus(string $status, string $mode): string|null
	{
		// unmodified
		if ($status === ' ') {
			return null;
		}

		// added
		if ($status === 'A') {
			return '+';
		}

		// unknown (= added to worktree, but not to index)
		if ($status === '?') {
			return ($mode === 'worktree') ? '+' : null;
		}

		// deleted
		if ($status === 'D') {
			return '-';
		}

		// keep all other values
		return $status;
	}
}
