<?php

namespace LukasBestle\Versions;

use Kirby\Data\Data;
use Kirby\Exception\LogicException;

/**
 * @coversDefaultClass LukasBestle\Versions\Instance
 */
class InstanceTest extends TestCase
{
	protected $instance;

	public function setUp(): void
	{
		parent::setUp();

		$this->instance = new Instance([
			'name'        => 'Test Instance',
			'contentRoot' => $this->contentRoot1,
			'color'       => 'black',
			'plugin'      => $this->plugin
		]);
	}

	/**
	 * @covers ::changes
	 */
	public function testChanges()
	{
		touch($this->contentRoot1 . '/test');

		$changes = $this->instance->changes();
		$this->assertSame(['test' => '+'], $changes->inWorktree());
		$this->assertSame($changes, $this->instance->changes());
	}

	/**
	 * @covers ::__construct
	 */
	public function testConstructErrorNoGitRepo()
	{
		$this->expectException('Kirby\Exception\Exception');
		$this->expectExceptionMessage(
			'The content directory of instance Test Instance is not connected to a ' .
			'Git repo, please either initialize a new repo or connect it as a worktree'
		);

		$this->instance->clone(['contentRoot' => '/dev/null']);
	}

	/**
	 * @covers ::createVersion
	 */
	public function testCreateVersion()
	{
		file_put_contents($this->contentRoot1 . '/test', 'test');
		touch($this->contentRoot1 . '/.lock');
		$this->plugin->gitCommand(null, 'add', '-A');
		$this->plugin->gitCommand(null, 'reset', '--', '.lock');

		// without logged-in user
		$version1 = $this->instance->createVersion('Test Label');

		$this->assertSame(date('Ymd') . '_001', $version1->name());
		$this->assertSame('versions@example.com', $version1->creatorEmail());
		$this->assertSame('Versions', $version1->creatorName());
		$this->assertSame('Test Label', $version1->label());
		$this->assertSame('Test Instance', $version1->originInstance());

		$this->assertSame($version1, $this->plugin->versions()->get(date('Ymd') . '_001'));
		$this->assertSame($version1->commit(), $this->instance->currentCommit());
		$this->assertSame([], $this->instance->changes()->overall());

		// with logged-in user
		file_put_contents($this->contentRoot1 . '/test', 'another-test');
		$this->plugin->gitCommand(null, 'add', '-A');
		$this->plugin->gitCommand(null, 'reset', '--', '.lock');
		$this->instance->changes()->update();

		$this->kirby->auth()->setUser($this->kirby->user('test-editor'));
		$version2 = $this->instance->createVersion('Another Test:::Label');

		$this->assertSame(date('Ymd') . '_002', $version2->name());
		$this->assertSame('test-editor@example.com', $version2->creatorEmail());
		$this->assertSame('Test Editor', $version2->creatorName());
		$this->assertSame('Another Test:::Label', $version2->label());
		$this->assertSame('Test Instance', $version2->originInstance());

		$this->assertSame($version2, $this->plugin->versions()->get(date('Ymd') . '_002'));
		$this->assertSame($version2->commit(), $this->instance->currentCommit());
		$this->assertSame([], $this->instance->changes()->overall());

		// automatically increment the version name past all existing ones
		$version1->delete();
		file_put_contents($this->contentRoot1 . '/test', 'a-third-test');
		$this->plugin->gitCommand(null, 'add', '-A');
		$this->plugin->gitCommand(null, 'reset', '--', '.lock');
		$this->instance->changes()->update();

		$version3 = $this->instance->createVersion('Third Test');

		$this->assertSame(date('Ymd') . '_003', $version3->name());
	}

	/**
	 * @covers ::createVersion
	 */
	public function testCreateVersionErrorNoChanges()
	{
		$this->expectException('Kirby\Exception\LogicException');
		$this->expectExceptionMessage('The version has not been prepared yet');

		$this->instance->createVersion('Test Label');
	}

	/**
	 * @covers ::isCurrent
	 */
	public function testIsCurrent()
	{
		$this->assertTrue($this->instance->isCurrent());

		mkdir($this->contentRoot1 . '/dir');
		$instance = $this->instance->clone([
			'contentRoot' => $this->contentRoot1 . '/dir'
		]);
		$this->assertFalse($instance->isCurrent());
	}

	/**
	 * @covers ::__construct
	 * @covers ::color
	 * @covers ::contentRoot
	 * @covers ::currentCommit
	 * @covers ::name
	 * @covers ::setColor
	 * @covers ::setContentRoot
	 * @covers ::setCurrentCommit
	 * @covers ::setName
	 * @covers ::setPlugin
	 */
	public function testMeta()
	{
		$this->assertSame('black', $this->instance->color());
		$this->assertSame($this->contentRoot1, $this->instance->contentRoot());
		$this->assertNull($this->instance->currentCommit());
		$this->assertSame('Test Instance', $this->instance->name());

		// optional property
		$instance = $this->instance->clone([
			'currentCommit' => 'abcdefg'
		]);
		$this->assertSame('abcdefg', $instance->currentCommit());
	}

	/**
	 * @covers ::prepareCreation
	 */
	public function testPrepareCreation()
	{
		file_put_contents($this->contentRoot1 . '/test', 'test');
		touch($this->contentRoot1 . '/.lock');
		mkdir($this->contentRoot1 . '/dir');
		file_put_contents($this->contentRoot1 . '/dir/another-test', 'another-test');
		touch($this->contentRoot1 . '/dir/.lock');

		$this->assertSame([], $this->instance->changes()->inIndex());
		$this->assertSame([
			'dir/' => '+',
			'test' => '+'
		], $this->instance->changes()->inWorktree());

		$this->instance->prepareCreation();

		$this->assertSame([
			'dir/another-test' => '+',
			'test' => '+'
		], $this->instance->changes()->inIndex());
		$this->assertSame([], $this->instance->changes()->inWorktree());
		$changes = "A  dir/another-test\nA  test\n?? .lock\n?? dir/.lock";
		$this->assertSame($changes, $this->plugin->gitCommand(null, 'status', '--porcelain=1'));
	}

	/**
	 * @covers ::prepareCreation
	 */
	public function testPrepareCreationErrorLocks()
	{
		touch($this->contentRoot1 . '/file');
		Data::write($this->contentRoot1 . '/.lock', [
			'/a-page' => [
				'lock' => [
					'user' => 'test-editor'
				]
			],
			'/another-page' => [
				'lock' => [
					'user' => 'another-user'
				]
			],
			'/a-third-page' => [
				'lock'   => [
					'user' => 'test-editor'
				],
				'unlock' => ['test-editor', 'another-user']
			]
		], 'yaml');
		mkdir($this->contentRoot1 . '/dir');
		Data::write($this->contentRoot1 . '/dir/.lock', [
			'/a-fourth-page' => [
				'unlock' => ['test-editor']
			]
		], 'yaml');

		$caught = false;
		try {
			$this->instance->prepareCreation();
		} catch (LogicException $e) {
			$caught = true;

			$this->assertSame(
				'A version cannot be created as some pages or files have unsaved changes:',
				$e->getMessage()
			);
			$this->assertSame([
				'/a-page'       => ['Test Editor'],
				'/another-page' => ['another-user'],
				'/a-third-page' => ['Test Editor', 'another-user'],
				'/a-fourth-page' => ['Test Editor'],
			], $e->getDetails()['lockedModels']);

			$this->assertSame([], $this->instance->changes()->inIndex());
			$changes = "?? .lock\n?? dir/\n?? file";
			$this->assertSame($changes, $this->plugin->gitCommand(null, 'status', '--porcelain=1'));
		}

		$this->assertTrue($caught);
	}

	/**
	 * @covers ::prepareCreation
	 */
	public function testPrepareCreationErrorNoChanges()
	{
		$this->expectException('Kirby\Exception\LogicException');
		$this->expectExceptionMessage('There are no changes to create a version from');

		$this->instance->prepareCreation();
	}

	/**
	 * @covers ::toArray
	 */
	public function testToArray()
	{
		file_put_contents($this->contentRoot1 . '/test', 'test');

		$this->assertSame([
			'changes'       => ['test' => '+'],
			'color'         => 'black',
			'contentRoot'   => $this->contentRoot1,
			'currentCommit' => null,
			'isCurrent'     => true,
			'name'          => 'Test Instance',
			'version'       => null,
			'versionLabel'  => null,
		], $this->instance->toArray());

		$this->instance->prepareCreation();
		$version = $this->instance->createVersion('Test Label');

		$this->assertSame([
			'changes'       => [],
			'color'         => 'black',
			'contentRoot'   => $this->contentRoot1,
			'currentCommit' => $version->commit(),
			'isCurrent'     => true,
			'name'          => 'Test Instance',
			'version'       => $version->name(),
			'versionLabel'  => $version->label(),
		], $this->instance->toArray());
	}

	/**
	 * @covers ::version
	 */
	public function testVersion()
	{
		$this->assertNull($this->instance->version());

		file_put_contents($this->contentRoot1 . '/test', 'test');
		$this->instance->prepareCreation();
		$version = $this->instance->createVersion('Test Label');

		$this->assertSame($version, $this->instance->version());
	}
}
