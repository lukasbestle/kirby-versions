<?php

namespace LukasBestle\Versions;

use Kirby\Exception\Exception;
use ReflectionProperty;

/**
 * @coversDefaultClass LukasBestle\Versions\Plugin
 */
class PluginTest extends TestCase
{
    /**
     * @covers ::checkPermission
     */
    public function testCheckPermission()
    {
        $this->kirby->auth()->setUser($this->kirby->user('test-editor'));
        $plugin = new Plugin($this->kirby);

        $this->assertNull($plugin->checkPermission('access'));
        $this->assertNull($plugin->checkPermission('delete'));
    }

    /**
     * @covers ::checkPermission
     */
    public function testCheckPermissionInvalid()
    {
        $this->expectException('Kirby\Exception\PermissionException');
        $this->expectExceptionMessage('You are not allowed to do this (missing create permission)');

        $this->kirby->auth()->setUser($this->kirby->user('test-editor'));
        $plugin = new Plugin($this->kirby);

        $plugin->checkPermission('create');
    }

    /**
     * @covers ::checkPermission
     */
    public function testCheckPermissionNoUser()
    {
        $this->expectException('Kirby\Exception\LogicException');
        $this->expectExceptionMessage('Internal error in the Versions plugin (error code user-not-logged-in)');

        $plugin = new Plugin($this->kirby);

        $plugin->checkPermission('create');
    }

    /**
     * @covers ::cleanExports
     */
    public function testCleanExports()
    {
        $exportDir = $this->tmp . '/media/versions-export';

        // the versions-export directory should be created automatically
        $this->assertDirectoryDoesNotExist($exportDir);
        $this->plugin->cleanExports();
        $this->assertDirectoryExists($exportDir);

        touch($exportDir . '/file_del1', time() - 2 * 60 * 60 - 10);
        touch($exportDir . '/file_del2', time() - 2 * 60 * 60 - 10);
        touch($exportDir . '/file_keep1', time() - 2 * 60 * 60 + 10);
        touch($exportDir . '/file_keep2', time() - 2 * 60 * 60 + 10);

        $this->assertFileExists($exportDir . '/file_del1');
        $this->assertFileExists($exportDir . '/file_del2');
        $this->assertFileExists($exportDir . '/file_keep1');
        $this->assertFileExists($exportDir . '/file_keep2');
        $this->plugin->cleanExports();
        $this->assertFileDoesNotExist($exportDir . '/file_del1');
        $this->assertFileDoesNotExist($exportDir . '/file_del2');
        $this->assertFileExists($exportDir . '/file_keep1');
        $this->assertFileExists($exportDir . '/file_keep2');
    }

    /**
     * @covers ::gitCommand
     */
    public function testGitCommand()
    {
        $instance1 = new Instance([
            'name'        => 'Instance1',
            'contentRoot' => $this->contentRoot1,
            'color'       => 'black',
            'plugin'      => $this->plugin
        ]);
        $instance2 = new Instance([
            'name'        => 'Instance2',
            'contentRoot' => $this->contentRoot2,
            'color'       => 'black',
            'plugin'      => $this->plugin
        ]);
        touch($this->contentRoot1 . '/test1');
        touch($this->contentRoot2 . '/test2');

        // $instance argument
        $this->assertSame('?? test1', $this->plugin->gitCommand(null, 'status', '--porcelain=1'));
        $this->assertSame('?? test1', $this->plugin->gitCommand($instance1, 'status', '--porcelain=1'));
        $this->assertSame('?? test2', $this->plugin->gitCommand($instance2, 'status', '--porcelain=1'));
    }

    /**
     * @covers ::gitCommand
     */
    public function testGitCommandError()
    {
        $this->expectException('Kirby\Exception\Exception');
        $this->expectExceptionMessage('A Git error occurred: git: \'does-not-exist\'');

        $this->plugin->gitCommand(null, 'does-not-exist');
    }

    /**
     * @covers ::hasPermission
     */
    public function testHasPermission()
    {
        $this->kirby->auth()->setUser($this->kirby->user('test-editor'));
        $plugin = new Plugin($this->kirby);

        $this->assertTrue($plugin->hasPermission('access'));
        $this->assertTrue($plugin->hasPermission('delete'));
        $this->assertFalse($plugin->hasPermission('create'));
    }

    /**
     * @covers ::hasPermission
     */
    public function testHasPermissionNoUser()
    {
        $this->expectException('Kirby\Exception\LogicException');
        $this->expectExceptionMessage('Internal error in the Versions plugin (error code user-not-logged-in)');

        $plugin = new Plugin($this->kirby);

        $plugin->hasPermission('create');
    }

    /**
     * @backupStaticAttributes enabled
     * @covers ::instance
     * @covers ::__construct
     * @covers ::kirby
     */
    public function testInstance()
    {
        $property = new ReflectionProperty('LukasBestle\Versions\Plugin', 'instance');
        $property->setAccessible(true);
        $property->setValue(null);

        $kirby = $this->kirby->clone();

        $this->assertSame($this->kirby, $this->plugin->kirby());
        $this->assertNotSame($kirby, $this->plugin->kirby());

        $plugin = Plugin::instance($kirby);
        $this->assertSame($kirby, $plugin->kirby());

        $plugin2 = Plugin::instance();
        $this->assertSame($plugin, $plugin2);

        $plugin3 = Plugin::instance($this->kirby);
        $this->assertNotSame($plugin, $plugin3);
        $this->assertSame($this->kirby, $plugin3->kirby());

        $plugin4 = new Plugin($kirby);
        $this->assertSame($kirby, $plugin4->kirby());
    }

    /**
     * @covers ::instances
     */
    public function testInstances()
    {
        $this->kirby = $this->kirby->clone([
            'options' => [
                'lukasbestle.versions' => [
                    'instances' => [
                        'Instance1' => [
                            'contentRoot' => $this->contentRoot1,
                            'color'       => 'black'
                        ]
                    ]
                ]
            ]
        ]);
        $this->plugin = new Plugin($this->kirby);

        $instances = $this->plugin->instances();
        $this->assertSame('Instance1', $instances->first()->name());

        $this->assertSame($instances, $this->plugin->instances());
    }

    /**
     * @covers ::instances
     */
    public function testInstancesErrorValidate()
    {
        $this->expectException('Kirby\Exception\Exception');
        $this->expectExceptionMessage('The Versions plugin requires Git 2.5+, you have Git 2.4.9');

        $this->kirby = $this->kirby->clone([
            'options' => [
                'lukasbestle.versions' => [
                    'git.path' => __DIR__ . '/fixtures/git_old'
                ]
            ]
        ]);
        $this->plugin = new Plugin($this->kirby);

        $this->plugin->instances();
    }

    /**
     * @covers ::exportRoot
     * @covers ::exportUrl
     * @covers ::option
     */
    public function testOptions()
    {
        $this->assertSame($this->tmp . '/media/versions-export', $this->plugin->exportRoot());
        $this->assertSame('https://example.com/media/versions-export', $this->plugin->exportUrl());
        $this->assertSame(20, $this->plugin->option('autodelete.count'));
    }

    /**
     * @covers ::toApiData
     */
    public function testToApiData()
    {
        // with user without permissions
        $this->kirby->auth()->setUser($this->kirby->user('test-no-access'));
        $plugin = new Plugin($this->kirby);
        $this->assertSame([], $plugin->toApiData());
        $this->assertSame(['status' => 'ok'], $plugin->toApiData(['status' => 'ok']));

        // with authenticated user
        $this->kirby->auth()->setUser($this->kirby->user('test-editor'));
        $plugin = new Plugin($this->kirby);
        $this->assertSame([
            'instances' => [
                'Local' => [
                    'changes'       => [],
                    'color'         => 'var(--color-focus-light)',
                    'isCurrent'     => true,
                    'name'          => 'Local',
                    'version'       => 'initial',
                    'versionLabel'  => 'Initial commit'
                ]
            ],
            'versions' => [
                'initial' => [
                    'created'        => null,
                    'creatorEmail'   => null,
                    'creatorName'    => null,
                    'instances'      => ['Local'],
                    'label'          => 'Initial commit',
                    'name'           => 'initial',
                    'originInstance' => null
                ]
            ]
        ], $plugin->toApiData());
        $this->assertSame([
            'instances' => [
                'Local' => [
                    'changes'       => [],
                    'color'         => 'var(--color-focus-light)',
                    'isCurrent'     => true,
                    'name'          => 'Local',
                    'version'       => 'initial',
                    'versionLabel'  => 'Initial commit'
                ]
            ],
            'versions' => [
                'initial' => [
                    'created'        => null,
                    'creatorEmail'   => null,
                    'creatorName'    => null,
                    'instances'      => ['Local'],
                    'label'          => 'Initial commit',
                    'name'           => 'initial',
                    'originInstance' => null
                ]
            ],
            'status' => 'ok'
        ], $plugin->toApiData(['status' => 'ok']));
    }

    /**
     * @covers ::toApiData
     */
    public function testToApiDataNoUser()
    {
        $this->expectException('Kirby\Exception\LogicException');
        $this->expectExceptionMessage('Internal error in the Versions plugin (error code user-not-logged-in)');

        $plugin = new Plugin($this->kirby);

        $this->plugin->toApiData();
    }

    /**
     * @covers ::toArray
     */
    public function testToArray()
    {
        $commit = $this->plugin->gitCommand(null, 'show-ref', '--hash', 'initial');

        $this->assertSame([
            'instances' => [
                'Local' => [
                    'changes'       => [],
                    'color'         => 'var(--color-focus-light)',
                    'contentRoot'   => $this->contentRoot1,
                    'currentCommit' => $commit,
                    'isCurrent'     => true,
                    'name'          => 'Local',
                    'version'       => 'initial',
                    'versionLabel'  => 'Initial commit'
                ]
            ],
            'versions' => [
                'initial' => [
                    'commit'         => $commit,
                    'created'        => null,
                    'creatorEmail'   => null,
                    'creatorName'    => null,
                    'instances'      => ['Local'],
                    'label'          => 'Initial commit',
                    'name'           => 'initial',
                    'originInstance' => null
                ]
            ]
        ], $this->plugin->toArray());
    }

    /**
     * @covers ::validate
     */
    public function testValidate()
    {
        $this->assertNull($this->plugin->validate());
    }

    /**
     * @covers ::validate
     */
    public function testValidateErrorGitTooOldAndOnlyOnce()
    {
        $this->kirby = $this->kirby->clone([
            'options' => [
                'lukasbestle.versions' => [
                    'git.path' => __DIR__ . '/fixtures/git_old'
                ]
            ]
        ]);
        $this->plugin = new Plugin($this->kirby);

        $caught = false;
        try {
            $this->plugin->validate();
        } catch (Exception $e) {
            $caught = true;

            $this->assertSame('The Versions plugin requires Git 2.5+, you have Git 2.4.9', $e->getMessage());
        }

        $this->assertTrue($caught);

        // the exception shouldn't be thrown a second time
        // (otherwise the validation would have run twice)
        $this->plugin->validate();
    }

    /**
     * @covers ::validate
     */
    public function testValidateErrorGitUnparseable()
    {
        $this->expectException('Kirby\Exception\Exception');
        $this->expectExceptionMessage('Internal error in the Versions plugin (error code git-version-unparseable)');

        $this->kirby = $this->kirby->clone([
            'options' => [
                'lukasbestle.versions' => [
                    'git.path' => __DIR__ . '/fixtures/git_unparseable'
                ]
            ]
        ]);
        $this->plugin = new Plugin($this->kirby);

        $this->plugin->validate();
    }

    /**
     * @covers ::validate
     */
    public function testValidateErrorInstances()
    {
        $this->expectException('Kirby\Exception\Exception');
        $this->expectExceptionMessage(
            'The content directory of instance Test Instance is not connected to a ' .
            'Git repo, please either initialize a new repo or connect it as a worktree'
        );

        $this->kirby = $this->kirby->clone([
            'options' => [
                'lukasbestle.versions' => [
                    'instances' => [
                        'Test Instance' => [
                            'contentRoot' => '/dev/null',
                            'color'       => 'black'
                        ]
                    ]
                ]
            ]
        ]);
        $this->plugin = new Plugin($this->kirby);

        $this->plugin->validate();
    }

    /**
     * @covers ::versions
     */
    public function testVersions()
    {
        $versions = $this->plugin->versions();
        $this->assertSame('initial', $versions->first()->name());

        $this->assertSame($versions, $this->plugin->versions());
    }

    /**
     * @covers ::versions
     */
    public function testVersionsErrorValidate()
    {
        $this->expectException('Kirby\Exception\Exception');
        $this->expectExceptionMessage('The Versions plugin requires Git 2.5+, you have Git 2.4.9');

        $this->kirby = $this->kirby->clone([
            'options' => [
                'lukasbestle.versions' => [
                    'git.path' => __DIR__ . '/fixtures/git_old'
                ]
            ]
        ]);
        $this->plugin = new Plugin($this->kirby);

        $this->plugin->versions();
    }
}
