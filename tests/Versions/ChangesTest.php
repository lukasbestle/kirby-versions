<?php

namespace LukasBestle\Versions;

/**
 * @covers LukasBestle\Versions\Changes
 */
class ChangesTest extends TestCase
{
    protected $changes;
    protected $instance;

    public function setUp(): void
    {
        parent::setUp();

        $this->instance = new Instance([
            'name'        => 'Test Instance',
            'color'       => 'black',
            'contentRoot' => $this->contentRoot1,
            'plugin'      => $this->plugin
        ]);

        $this->changes = new Changes($this->plugin, $this->instance);
    }

    public function testNoChanges()
    {
        $this->assertSame([], $this->changes->inIndex());
        $this->assertSame([], $this->changes->inWorktree());
        $this->assertSame([], $this->changes->lockFiles());
        $this->assertSame([], $this->changes->overall());
    }

    public function testWithChanges()
    {
        mkdir($this->contentRoot1 . '/dir');
        file_put_contents($this->contentRoot1 . '/dir/modified', 'dir/modified');
        file_put_contents($this->contentRoot1 . '/removed', 'removed');
        file_put_contents($this->contentRoot1 . '/renamed', 'renamed');
        $this->plugin->gitCommand(null, 'add', '-A');
        $this->plugin->gitCommand(null, '-c', 'user.name=Test', '-c', 'user.email=test@example.com', 'commit', '-m', 'Initial commit');

        file_put_contents($this->contentRoot1 . '/dir/modified', 'modified content');
        unlink($this->contentRoot1 . '/removed');
        rename($this->contentRoot1 . '/renamed', $this->contentRoot1 . '/renamed_new');
        file_put_contents($this->contentRoot1 . '/modified_in_worktree', 'modified_in_worktree');
        file_put_contents($this->contentRoot1 . '/added_and_deleted', 'added_and_deleted');
        file_put_contents($this->contentRoot1 . '/.lock', '.lock');
        file_put_contents($this->contentRoot1 . '/dir/.lock', 'dir/.lock');
        file_put_contents($this->contentRoot1 . '/not-a.lock', 'not-a.lock');
        file_put_contents($this->contentRoot1 . '/dir/also-not-a.lock', 'dir/also-not-a.lock');

        $this->plugin->gitCommand(null, 'add', '-A');

        file_put_contents($this->contentRoot1 . '/modified_in_worktree', 'content');
        unlink($this->contentRoot1 . '/added_and_deleted');
        file_put_contents($this->contentRoot1 . '/added_in_worktree', 'added_in_worktree');

        $this->assertSame([
            'added_and_deleted' => '+',
            'dir/also-not-a.lock' => '+',
            'dir/modified' => 'M',
            'modified_in_worktree' => '+',
            'not-a.lock' => '+',
            'removed' => '-',
            'renamed -> renamed_new' => 'R'
        ], $this->changes->inIndex());
        $this->assertSame([
            'added_and_deleted' => '-',
            'added_in_worktree' => '+',
            'modified_in_worktree' => 'M'
        ], $this->changes->inWorktree());
        $this->assertSame([
            '.lock',
            'dir/.lock'
        ], $this->changes->lockFiles());
        $this->assertSame([
            'added_in_worktree' => '+',
            'dir/also-not-a.lock' => '+',
            'dir/modified' => 'M',
            'modified_in_worktree' => '+',
            'not-a.lock' => '+',
            'removed' => '-',
            'renamed -> renamed_new' => 'R'
        ], $this->changes->overall());
    }
}
