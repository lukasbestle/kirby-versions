<?php

namespace LukasBestle\Versions;

use Kirby\Exception\InvalidArgumentException;
use Kirby\Exception\LogicException;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Properties;

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
	use Properties;

	/**
	 * Commit hash of this version
	 *
	 * @var string
	 */
	protected $commit;

	/**
	 * Creation timestamp
	 *
	 * @var int|null
	 */
	protected $created = null;

	/**
	 * Email address of the creator
	 *
	 * @var string|null
	 */
	protected $creatorEmail = null;

	/**
	 * Name of the creator
	 *
	 * @var string|null
	 */
	protected $creatorName = null;

	/**
	 * Custom label
	 *
	 * @var string
	 */
	protected $label;

	/**
	 * Unique version name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Name of the instance where the version was
	 * originally created
	 *
	 * @var string|null
	 */
	protected $originInstance = null;

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
	 */
	public function __construct(array $props)
	{
		$this->setProperties($props);
	}

	/**
	 * Returns the commit hash of this version
	 *
	 * @return string
	 */
	public function commit(): string
	{
		return $this->commit;
	}

	/**
	 * Returns the creation timestamp;
	 * only set if the tag is annotated
	 *
	 * @return int|null
	 */
	public function created(): ?int
	{
		return $this->created;
	}

	/**
	 * Returns the email address of the creator;
	 * only set if the tag is annotated
	 *
	 * @return string|null
	 */
	public function creatorEmail(): ?string
	{
		return $this->creatorEmail;
	}

	/**
	 * Returns the name of the creator;
	 * only set if the tag is annotated
	 *
	 * @return string|null
	 */
	public function creatorName(): ?string
	{
		return $this->creatorName;
	}

	/**
	 * Deletes the Git tag behind this version
	 *
	 * @return void
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
	 * @param \LukasBestle\Versions\Instance $instance
	 * @return void
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
	 *
	 * @return \Kirby\Toolkit\Collection
	 */
	public function instances(): Collection
	{
		return $this->plugin->instances()->filterBy('currentCommit', $this->commit());
	}

	/**
	 * Returns the custom label
	 *
	 * @return string
	 */
	public function label(): string
	{
		return $this->label;
	}

	/**
	 * Returns the unique version name
	 *
	 * @return string
	 */
	public function name(): string
	{
		return $this->name;
	}

	/**
	 * Returns the name of the instance where
	 * the version was originally created (if known)
	 *
	 * @return string|null
	 */
	public function originInstance(): ?string
	{
		return $this->originInstance;
	}

	/**
	 * Returns the version data as array
	 *
	 * @return array
	 *
	 * @throws \Kirby\Exception\Exception If there is a Git error
	 */
	public function toArray(): array
	{
		$array = $this->propertiesToArray();
		$array['instances'] = $this->instances()->keys();

		ksort($array);
		return $array;
	}

	/**
	 * Sets the commit hash of this version
	 *
	 * @param string $commit
	 * @return self
	 */
	protected function setCommit(string $commit): self
	{
		$this->commit = $commit;
		return $this;
	}

	/**
	 * Sets the creation timestamp
	 *
	 * @param int|string|null $created
	 * @return self
	 *
	 * @throws \Kirby\Exception\InvalidArgumentException If the value cannot be parsed
	 */
	protected function setCreated($created = null): self
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

		$this->created = $created;
		return $this;
	}

	/**
	 * Sets the email address of the creator
	 *
	 * @param string|null $creatorEmail
	 * @return self
	 */
	protected function setCreatorEmail(?string $creatorEmail = null): self
	{
		// Git will output an empty string if the value is not available
		if ($creatorEmail === '') {
			$creatorEmail = null;
		} elseif ($creatorEmail !== null) {
			// trim the angle brackets around the email address
			// that come from Git's output
			$creatorEmail = trim($creatorEmail, '<>');
		}

		$this->creatorEmail = $creatorEmail;
		return $this;
	}

	/**
	 * Sets the name of the creator
	 *
	 * @param string|null $creatorName
	 * @return self
	 */
	protected function setCreatorName(?string $creatorName = null): self
	{
		// Git will output an empty string if the value is not available
		if ($creatorName === '') {
			$creatorName = null;
		}

		$this->creatorName = $creatorName;
		return $this;
	}

	/**
	 * Sets the custom label
	 *
	 * @param string $label
	 * @return self
	 */
	protected function setLabel(string $label): self
	{
		$this->label = $label;
		return $this;
	}

	/**
	 * Sets the unique version name
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
	 * Sets the name of the instance where
	 * the version was originally created
	 *
	 * @param string|null $originInstance
	 * @return self
	 */
	protected function setOriginInstance(?string $originInstance = null): self
	{
		$this->originInstance = $originInstance;
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
