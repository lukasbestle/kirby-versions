<?php

namespace LukasBestle\Versions;

use Kirby\Exception\InvalidArgumentException;
use Kirby\Exception\LogicException;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\I18n;

/**
 * Version
 * A single created version
 *
 * @package   Kirby Versions Plugin
 * @author    Lukas Bestle <project-kirbyversions@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-versions
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 */
class Version
{
	/**
	 * Commit hash of this version
	 */
	protected string $commit;

	/**
	 * Creation timestamp
	 */
	protected int|null $created;

	/**
	 * Email address of the creator
	 */
	protected string|null $creatorEmail;

	/**
	 * Name of the creator
	 */
	protected string|null $creatorName;

	/**
	 * Custom label
	 */
	protected string $label;

	/**
	 * Unique version name
	 */
	protected string $name;

	/**
	 * Name of the instance where the version was
	 * originally created
	 */
	protected string|null $originInstance;

	/**
	 * Instance of the Plugin class
	 */
	protected Plugin $plugin;

	/**
	 * Class constructor
	 */
	public function __construct(array $props)
	{
		$this->commit         = $props['commit'];
		$this->created        = $this->normalizeCreated($props['created'] ?? null);
		$this->creatorEmail   = $this->normalizeCreatorEmail($props['creatorEmail'] ?? null);
		$this->creatorName    = $this->normalizeCreatorName($props['creatorName'] ?? null);
		$this->label          = $props['label'];
		$this->name           = $props['name'];
		$this->originInstance = $props['originInstance'] ?? null;
		$this->plugin         = $props['plugin'];
	}

	/**
	 * Returns the commit hash of this version
	 */
	public function commit(): string
	{
		return $this->commit;
	}

	/**
	 * Returns the creation timestamp;
	 * only set if the tag is annotated
	 */
	public function created(): int|null
	{
		return $this->created;
	}

	/**
	 * Returns the email address of the creator;
	 * only set if the tag is annotated
	 */
	public function creatorEmail(): string|null
	{
		return $this->creatorEmail;
	}

	/**
	 * Returns the name of the creator;
	 * only set if the tag is annotated
	 */
	public function creatorName(): string|null
	{
		return $this->creatorName;
	}

	/**
	 * Deletes the Git tag behind this version
	 *
	 * @throws \Kirby\Exception\LogicException If the version cannot be deleted
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function delete(): void
	{
		if ($this->instances()->count() > 0) {
			throw new LogicException([
				'key' => 'versions.version.inUse'
			]);
		}

		$this->plugin->gitCommand(null, 'tag', '-d', $this->name());

		// update cache
		$this->plugin->versions()->remove($this->name());
	}

	/**
	 * Deploys the version to a specified instance
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 * @throws \Kirby\Exception\Exception If there are changes that cannot
	 *                                    be backed up automatically
	 */
	public function deployTo(Instance $instance): void
	{
		// if there are changes, we first need to create
		// an automatic version to back them up
		if (
			$instance->changes()->overall() !== [] ||
			$instance->changes()->lockFiles() !== []
		) {
			$instance->prepareCreation();

			/** @var string $label */
			$label = I18n::translate('versions.name.autosave');
			$instance->createVersion($label);
		}

		// now we can deploy the version to the instance
		$this->plugin->gitCommand($instance, 'checkout', $this->name());

		// update cache
		$instance->setCurrentCommit($this->commit());
	}

	/**
	 * Exports the version as a ZIP file
	 *
	 * @return array ZIP `url` and `filesize`
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function export(): array
	{
		// generate a hard to guess filename
		$filename = $this->name() . '_' . substr($this->commit(), 0, 7) . '.zip';

		// build the absolute path and URL
		$path = $this->plugin->exportRoot() . '/' . $filename;
		$url  = $this->plugin->exportUrl() . '/' . $filename;

		// check if the export already exists
		if (is_file($path) === true) {
			// ensure that the file is preserved for another two hours
			touch($path);
		} else {
			Dir::make($this->plugin->exportRoot());
			$this->plugin->gitCommand(null, 'archive', '--format=zip', '-o', $path, $this->name());
		}

		return [
			'filesize' => F::niceSize($path),
			'url'      => $url
		];
	}

	/**
	 * Returns the list of instances that
	 * currently use this version
	 */
	public function instances(): Collection
	{
		return $this->plugin->instances()->filterBy('currentCommit', $this->commit());
	}

	/**
	 * Returns the custom label
	 */
	public function label(): string
	{
		return $this->label;
	}

	/**
	 * Returns the unique version name
	 */
	public function name(): string
	{
		return $this->name;
	}

	/**
	 * Returns the name of the instance where
	 * the version was originally created (if known)
	 */
	public function originInstance(): string|null
	{
		return $this->originInstance;
	}

	/**
	 * Returns the version data as array
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function toArray(): array
	{
		return [
			'commit'         => $this->commit(),
			'created'        => $this->created(),
			'creatorEmail'   => $this->creatorEmail(),
			'creatorName'    => $this->creatorName(),
			'instances'      => $this->instances()->keys(),
			'label'          => $this->label(),
			'name'           => $this->name(),
			'originInstance' => $this->originInstance(),
		];
	}

	/**
	 * Normalizes the creation timestamp for the property
	 */
	protected function normalizeCreated(int|string|null $created): int|null
	{
		if (is_string($created) === true) {
			$created = $created ? strtotime($created) : null;
		}

		if (is_int($created) !== true && $created !== null) {
			throw new InvalidArgumentException([
				'key'  => 'versions.internal',
				'data' => ['code' => 'version-invalid-created-value']
			]);
		}

		return $created;
	}

	/**
	 * Normalizes the email address of the creator for the property
	 */
	protected function normalizeCreatorEmail(string|null $creatorEmail): string|null
	{
		// Git will output an empty string if the value is not available
		if ($creatorEmail === '' || $creatorEmail === null) {
			return null;
		}

		// trim the angle brackets around the email address
		// that come from Git's output
		return trim($creatorEmail, '<>');
	}

	/**
	 * Normalizes the name of the creator for the property
	 */
	protected function normalizeCreatorName(string|null $creatorName): string|null
	{
		// Git will output an empty string if the value is not available
		if ($creatorName === '') {
			return null;
		}

		return $creatorName;
	}
}
