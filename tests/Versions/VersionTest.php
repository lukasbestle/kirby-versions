<?php

namespace LukasBestle\Versions;

use Kirby\Data\Data;
use Kirby\Toolkit\F;
use ZipArchive;

/**
 * @coversDefaultClass LukasBestle\Versions\Version
 */
class VersionTest extends TestCase
{
	/**
	 * @covers ::delete
	 */
	public function testDelete()
	{
		touch($this->contentRoot1 . '/test');
		$this->plugin->gitCommand(null, 'add', '-A');
		$this->plugin->gitCommand(null, '-c', 'user.name=Test User', '-c', 'user.email=test@example.com', 'commit', '-m', 'Test Version');
		$this->plugin->gitCommand(null, 'tag', 'test');

		$version = $this->plugin->versions()->get('initial');

		$this->assertSame("initial\ntest", $this->plugin->gitCommand(null, 'tag', '--list'));

		$version->delete();

		$this->assertSame('test', $this->plugin->gitCommand(null, 'tag', '--list'));
		$this->assertNull($this->plugin->versions()->get('initial'));
	}

	/**
	 * @covers ::delete
	 */
	public function testDeleteErrorInUse()
	{
		$this->expectException('Kirby\Exception\LogicException');
		$this->expectExceptionMessage('The version is currently in use');

		$this->plugin->versions()->get('initial')->delete();
	}

	/**
	 * @covers ::deployTo
	 */
	public function testDeployTo()
	{
		$instance       = $this->plugin->instances()->first();
		$initialVersion = $this->plugin->versions()->first();

		touch($this->contentRoot1 . '/test');
		$instance->prepareCreation();
		$newVersion = $instance->createVersion('Test Version');

		$initialCommit = $initialVersion->commit();
		$newCommit     = $newVersion->commit();

		// simple deployment
		$this->assertSame($newCommit, $instance->currentCommit());
		$this->assertSame($newCommit, $this->plugin->gitCommand(null, 'rev-parse', 'HEAD'));
		$initialVersion->deployTo($instance);
		$this->assertSame($initialCommit, $instance->currentCommit());
		$this->assertSame($initialCommit, $this->plugin->gitCommand(null, 'rev-parse', 'HEAD'));

		// with autosave
		touch($this->contentRoot1 . '/another-test');
		$instance->changes()->update();
		$this->assertFileExists($this->contentRoot1 . '/another-test');
		$newVersion->deployTo($instance);
		$this->assertFileDoesNotExist($this->contentRoot1 . '/another-test');
		$this->assertSame($newCommit, $instance->currentCommit());
		$this->assertSame($newCommit, $this->plugin->gitCommand(null, 'rev-parse', 'HEAD'));
		$snapshot = $this->plugin->versions()->sortBy('created', 'asc')->last();
		$this->assertSame('Automatic snapshot', $snapshot->label());

		// the snapshot should include the created file
		$snapshot->deployTo($instance);
		$this->assertFileExists($this->contentRoot1 . '/another-test');
	}

	/**
	 * @covers ::deployTo
	 */
	public function testDeployToErrorCannotAutosave()
	{
		$this->expectException('Kirby\Exception\LogicException');
		$this->expectExceptionMessage('A version cannot be created as some pages or files have unsaved changes:');

		touch($this->contentRoot1 . '/test');
		Data::write($this->contentRoot1 . '/.lock', [
			'/a-page' => [
				'lock' => [
					'user' => 'test-editor'
				]
			]
		], 'yaml');

		$instance = $this->plugin->instances()->first();
		$this->plugin->versions()->first()->deployTo($instance);
	}

	/**
	 * @covers ::export
	 */
	public function testExport()
	{
		$version = $this->plugin->versions()->first();

		$filename = $version->name() . '_' . substr($version->commit(), 0, 7) . '.zip';
		$path     = $this->tmp . '/media/versions-export/' . $filename;
		$url      = 'https://example.com/media/versions-export/' . $filename;

		// new export
		$this->assertFileDoesNotExist($path);
		$export = $version->export();
		$this->assertSame([
			'filesize' => F::niceSize($path),
			'url'      => $url
		], $export);
		$this->assertFileExists($path);

		// already existing file
		touch($path, 1234567890);
		clearstatcache();
		$this->assertSame(1234567890, filemtime($path));
		$export = $version->export();
		clearstatcache();
		$this->assertSame([
			'filesize' => F::niceSize($path),
			'url'      => $url
		], $export);
		$this->assertGreaterThanOrEqual(time() - 5, filemtime($path));

		// verify contents of the export file
		$zip = new ZipArchive();
		$zip->open($path);
		$file = $zip->statIndex(0);
		$this->assertSame('.gitkeep', $file['name']);
	}

	/**
	 * @covers ::instances
	 */
	public function testInstances()
	{
		$instance       = $this->plugin->instances()->first();
		$initialVersion = $this->plugin->versions()->first();

		$this->assertCount(1, $initialVersion->instances());
		$this->assertSame('Local', $initialVersion->instances()->first()->name());

		touch($this->contentRoot1 . '/test');
		$instance->prepareCreation();
		$instance->createVersion('Test Version');

		$this->assertCount(0, $initialVersion->instances());
	}
	/**
	 * @covers ::__construct
	 * @covers ::commit
	 * @covers ::created
	 * @covers ::creatorEmail
	 * @covers ::creatorName
	 * @covers ::label
	 * @covers ::name
	 * @covers ::originInstance
	 * @covers ::setCommit
	 * @covers ::setCreated
	 * @covers ::setCreatorEmail
	 * @covers ::setCreatorName
	 * @covers ::setLabel
	 * @covers ::setName
	 * @covers ::setOriginInstance
	 * @covers ::setPlugin
	 */
	public function testMeta()
	{
		$version = new Version([
			'commit' => 'abcdefg',
			'label'  => 'Test Version',
			'name'   => 'test',
			'plugin' => $this->plugin
		]);

		$this->assertSame('abcdefg', $version->commit());
		$this->assertNull($version->created());
		$this->assertNull($version->creatorEmail());
		$this->assertNull($version->creatorName());
		$this->assertSame('Test Version', $version->label());
		$this->assertSame('test', $version->name());
		$this->assertNull($version->originInstance());

		// optional properties
		$version = $version->clone([
			'created'        => 1234567890,
			'creatorEmail'   => 'test@example.com',
			'creatorName'    => 'Test User',
			'originInstance' => 'Test Instance'
		]);
		$this->assertSame(1234567890, $version->created());
		$this->assertSame('test@example.com', $version->creatorEmail());
		$this->assertSame('Test User', $version->creatorName());
		$this->assertSame('Test Instance', $version->originInstance());

		// property normalization I
		$version = $version->clone([
			'created'      => '2020-01-02 03:04',
			'creatorEmail' => '<another-test@example.com>'
		]);
		$this->assertSame(strtotime('2020-01-02 03:04'), $version->created());
		$this->assertSame('another-test@example.com', $version->creatorEmail());

		// property normalization II
		$version = $version->clone([
			'creatorEmail' => '',
			'creatorName'  => ''
		]);
		$this->assertNull($version->creatorEmail());
		$this->assertNull($version->creatorName());
	}

	/**
	 * @covers ::setCreated
	 */
	public function testSetCreatedError1()
	{
		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('Internal error in the Versions plugin (error code version-invalid-created-value)');

		new Version([
			'commit' => 'abcdefg',
			'created' => 'this is not a timestamp',
			'label'  => 'Test Version',
			'name'   => 'test',
			'plugin' => $this->plugin
		]);
	}

	/**
	 * @covers ::setCreated
	 */
	public function testSetCreatedError2()
	{
		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('Internal error in the Versions plugin (error code version-invalid-created-value)');

		new Version([
			'commit' => 'abcdefg',
			'created' => [],
			'label'  => 'Test Version',
			'name'   => 'test',
			'plugin' => $this->plugin
		]);
	}

	/**
	 * @covers ::toArray
	 */
	public function testToArray()
	{
		$commit = $this->plugin->gitCommand(null, 'show-ref', '--hash', 'initial');

		$this->assertSame([
			'commit'         => $commit,
			'created'        => null,
			'creatorEmail'   => null,
			'creatorName'    => null,
			'instances'      => ['Local'],
			'label'          => 'Initial commit',
			'name'           => 'initial',
			'originInstance' => null
		], $this->plugin->versions()->first()->toArray());
	}
}
