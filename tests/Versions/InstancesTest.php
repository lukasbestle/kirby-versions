<?php

namespace LukasBestle\Versions;

/**
 * @coversDefaultClass LukasBestle\Versions\Instances
 */
class InstancesTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::validate
     */
    public function testConstruct1NoInstances()
    {
        $instances = new Instances($this->plugin);

        $commit = $this->plugin->gitCommand(null, 'show-ref', '--hash', 'initial');
        $this->assertCount(1, $instances);
        $this->assertSame('Local', $instances->first()->name());
        $this->assertSame($instances->first(), $instances->get('Local'));
        $this->assertSame('var(--color-focus-light)', $instances->first()->color());
        $this->assertSame($commit, $instances->first()->currentCommit());
        $this->assertSame($this->contentRoot1, $instances->first()->contentRoot());
        $this->assertTrue($instances->first()->isCurrent());
    }

    /**
     * @covers ::__construct
     * @covers ::validate
     */
    public function testConstruct2AllInstancesConfigured()
    {
        $this->kirby = $this->kirby->clone([
            'options' => [
                'lukasbestle.versions' => [
                    'instances' => [
                        'Instance1' => [
                            'contentRoot' => $this->contentRoot1,
                            'color'       => 'black'
                        ],
                        'Instance2' => [
                            'contentRoot' => $this->contentRoot2,
                            'color'       => 'black'
                        ]
                    ]
                ]
            ]
        ]);
        $this->plugin = new Plugin($this->kirby);
        $instances    = new Instances($this->plugin);

        $commit = $this->plugin->gitCommand(null, 'show-ref', '--hash', 'initial');
        $this->assertCount(2, $instances);
        $this->assertSame('Instance1', $instances->first()->name());
        $this->assertSame($commit, $instances->first()->currentCommit());
        $this->assertSame($this->contentRoot1, $instances->first()->contentRoot());
        $this->assertTrue($instances->first()->isCurrent());
        $this->assertSame('Instance2', $instances->last()->name());
        $this->assertSame($commit, $instances->last()->currentCommit());
        $this->assertSame($this->contentRoot2, $instances->last()->contentRoot());
        $this->assertFalse($instances->last()->isCurrent());
    }

    /**
     * @covers ::__construct
     * @covers ::validate
     */
    public function testConstruct3OnlySecondInstance()
    {
        $this->kirby = $this->kirby->clone([
            'options' => [
                'lukasbestle.versions' => [
                    'instances' => [
                        'Instance2' => [
                            'contentRoot' => $this->contentRoot2,
                            'color'       => 'black'
                        ]
                    ]
                ]
            ]
        ]);
        $this->plugin = new Plugin($this->kirby);
        $instances    = new Instances($this->plugin);

        $commit = $this->plugin->gitCommand(null, 'show-ref', '--hash', 'initial');
        $this->assertCount(2, $instances);
        $this->assertSame('Local', $instances->first()->name());
        $this->assertSame($instances->first(), $instances->get('Local'));
        $this->assertSame('var(--color-focus-light)', $instances->first()->color());
        $this->assertSame($commit, $instances->first()->currentCommit());
        $this->assertSame($this->contentRoot1, $instances->first()->contentRoot());
        $this->assertTrue($instances->first()->isCurrent());
        $this->assertSame('Instance2', $instances->last()->name());
        $this->assertSame($commit, $instances->last()->currentCommit());
        $this->assertSame($this->contentRoot2, $instances->last()->contentRoot());
        $this->assertFalse($instances->last()->isCurrent());
    }

    /**
     * @covers ::__construct
     * @covers ::validate
     */
    public function testConstructErrorInvalidGitOutput()
    {
        $this->expectException('Kirby\Exception\Exception');
        $this->expectExceptionMessage('Internal error in the Versions plugin (error code git-worktree-invalid)');

        $this->kirby = $this->kirby->clone([
            'options' => [
                'lukasbestle.versions' => [
                    'git.path' => __DIR__ . '/fixtures/git_invalid-worktree',
                    'instances' => [
                        'Instance1' => [
                            'contentRoot' => $this->contentRoot1,
                            'color'       => 'black'
                        ],
                        'Instance2' => [
                            'contentRoot' => $this->contentRoot2,
                            'color'       => 'black'
                        ]
                    ]
                ]
            ]
        ]);
        $this->plugin = new Plugin($this->kirby);
        new Instances($this->plugin);
    }

    /**
     * @covers ::__construct
     * @covers ::validate
     */
    public function testConstructErrorNoWorktree()
    {
        $this->expectException('Kirby\Exception\Exception');
        $this->expectExceptionMessage(
            'The content directory of instance Instance2 is not a worktree of the content ' .
            'directory of the current site, please connect the two instances as worktrees'
        );

        $instance2 = new Instance([
            'name'        => 'Instance2',
            'contentRoot' => $this->contentRoot2,
            'color'       => 'black',
            'plugin'      => $this->plugin
        ]);
        $this->plugin->gitCommand(null, 'worktree', 'remove', $this->contentRoot2);
        mkdir($this->contentRoot2);
        $this->plugin->gitCommand($instance2, 'init');

        $this->kirby = $this->kirby->clone([
            'options' => [
                'lukasbestle.versions' => [
                    'instances' => [
                        'Instance1' => [
                            'contentRoot' => $this->contentRoot1,
                            'color'       => 'black'
                        ],
                        'Instance2' => [
                            'contentRoot' => $this->contentRoot2,
                            'color'       => 'black'
                        ]
                    ]
                ]
            ]
        ]);
        $this->plugin = new Plugin($this->kirby);
        new Instances($this->plugin);
    }

    /**
     * @covers ::__construct
     * @covers ::validate
     */
    public function testConstructErrorOnBranch()
    {
        $this->expectException('Kirby\Exception\Exception');
        $this->expectExceptionMessage(
            'The content directory of instance Local still has a checked ' .
            'out branch, please run `git checkout` with the latest Git tag'
        );

        $this->plugin->gitCommand(null, 'checkout', 'master');

        new Instances($this->plugin);
    }

    /**
     * @covers ::findOrException
     */
    public function testFindOrException()
    {
        $instances = new Instances($this->plugin);

        $this->assertSame('Local', $instances->findOrException('Local')->name());
    }

    /**
     * @covers ::findOrException
     */
    public function testFindOrExceptionErrorNotFound()
    {
        $this->expectException('Kirby\Exception\NotFoundException');
        $this->expectExceptionMessage('The instance Does Not Exist does not exist');

        $instances = new Instances($this->plugin);

        $instances->findOrException('Does Not Exist');
    }
}
