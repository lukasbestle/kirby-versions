<?php

namespace LukasBestle\Versions;

use ReflectionProperty;

/**
 * @coversDefaultClass LukasBestle\Versions\Versions
 */
class VersionsTest extends TestCase
{
    /**
     * @covers ::autodelete
     */
    public function testAutodelete()
    {
        $this->kirby = $this->kirby->clone([
            'options' => [
                'lukasbestle.versions' => [
                    'autodelete.age'   => 100,
                    'autodelete.count' => 3
                ]
            ]
        ]);
        $this->plugin = new Plugin($this->kirby);
        $versions     = $this->plugin->versions();

        $this->assertCount(1, $versions);

        // create four dummy versions for testing
        for ($i = 1; $i <= 4; $i++) {
            $date = date('c', time() + 120 * $i);
            touch($this->contentRoot1 . '/test' . $i);
            $this->plugin->gitCommand(null, 'add', '-A');
            $this->plugin->gitCommand(null, '-c', 'user.name=Test User', '-c', 'user.email=test@example.com', 'commit', '--date=' . $date, '-m', 'Test Version ' . $i);
            exec('GIT_COMMITTER_DATE="' . $date . '" git -C ' . escapeshellarg($this->contentRoot1) . ' -c "user.name=Test User" -c user.email=test@example.com tag test' . $i . ' -a -m "Test Version ' . $i . '"');
        }

        // make sure that one of the versions is currently in use
        $this->plugin->gitCommand(null, 'checkout', 'test2');

        // reload the plugin data
        $instancesProp = new ReflectionProperty('LukasBestle\Versions\Plugin', 'instances');
        $instancesProp->setAccessible(true);
        $instancesProp->setValue($this->plugin, null);
        $versions->update();
        $this->assertCount(5, $versions);

        // test delete by count
        $versions->autodelete();
        $this->assertCount(3, $versions);
        $this->assertSame("initial\ntest2\ntest4", $this->plugin->gitCommand(null, 'tag', '--list'));

        // create a fifth test version in the past
        $date = date('c', time() - 120);
        touch($this->contentRoot1 . '/test5');
        $this->plugin->gitCommand(null, 'add', '-A');
        $this->plugin->gitCommand(null, '-c', 'user.name=Test User', '-c', 'user.email=test@example.com', 'commit', '--date=' . $date, '-m', 'Test Version 5');
        exec('GIT_COMMITTER_DATE="' . $date . '" git -C ' . escapeshellarg($this->contentRoot1) . ' -c "user.name=Test User" -c user.email=test@example.com tag test5 -a -m "Test Version 5"');

        // get rid of one of the versions to ensure that we don't
        // test deletion by count again
        $versions->get('test4')->delete();

        $versions->update();
        $this->assertCount(3, $versions);
        $this->assertSame("initial\ntest2\ntest5", $this->plugin->gitCommand(null, 'tag', '--list'));

        // test delete by age
        $versions->autodelete();
        $this->assertCount(2, $versions);
        $this->assertSame("initial\ntest2", $this->plugin->gitCommand(null, 'tag', '--list'));
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $versions = new Versions($this->plugin);

        $this->assertCount(1, $versions);
        $this->assertSame('initial', $versions->first()->name());
    }

    /**
     * @covers ::findOrException
     */
    public function testFindOrException()
    {
        $versions = new Versions($this->plugin);

        $this->assertSame('initial', $versions->findOrException('initial')->name());
    }

    /**
     * @covers ::findOrException
     */
    public function testFindOrExceptionErrorNotFound()
    {
        $this->expectException('Kirby\Exception\NotFoundException');
        $this->expectExceptionMessage('The version Does Not Exist does not exist');

        $versions = new Versions($this->plugin);

        $versions->findOrException('Does Not Exist');
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $date = date('c', 1234567890);

        $versions = $this->plugin->versions();
        $this->plugin->gitCommand(null, 'tag', '-d', 'initial');
        $versions->update();
        $this->assertCount(0, $versions);

        // create non-annotated version
        touch($this->contentRoot1 . '/test1');
        $this->plugin->gitCommand(null, 'add', '-A');
        $this->plugin->gitCommand(null, '-c', 'user.name=Test User', '-c', 'user.email=test@example.com', 'commit', '--date=' . $date, '-m', 'Test Version 1');
        $this->plugin->gitCommand(null, 'tag', 'TeSt1');

        // create annotated version
        touch($this->contentRoot1 . '/test2');
        $this->plugin->gitCommand(null, 'add', '-A');
        $this->plugin->gitCommand(null, '-c', 'user.name=Test User', '-c', 'user.email=test@example.com', 'commit', '--date=' . $date, '-m', 'Test Version 2');
        exec('GIT_COMMITTER_DATE="' . $date . '" git -C ' . escapeshellarg($this->contentRoot1) . ' -c "user.name=Test User" -c user.email=test@example.com tag test2 -a -m "Test Version 2"');

        // create annotated version with origin instance
        touch($this->contentRoot1 . '/test3');
        $this->plugin->gitCommand(null, 'add', '-A');
        $this->plugin->gitCommand(null, '-c', 'user.name=Test User', '-c', 'user.email=test@example.com', 'commit', '--date=' . $date, '-m', 'Test Instance:::Test Version 3');
        exec('GIT_COMMITTER_DATE="' . $date . '" git -C ' . escapeshellarg($this->contentRoot1) . ' -c "user.name=Test User" -c user.email=test@example.com tag test3 -a -m "Test Instance:::Test Version 2"');

        // collect the commit hashes from all created versions
        $commits = $this->plugin->gitCommand(null, 'tag', '--list', '--format', '%(objectname)	%(object)');
        $commits = explode("\n", $commits);
        $commits = array_map(function ($line) {
            return explode('	', $line);
        }, $commits);
        $this->plugin->instances()->first()->setCurrentCommit($commits[2][1]);

        $versions->update();
        
        $this->assertCount(3, $versions);
        $this->assertSame([
            'TeSt1' => [
                'commit'         => $commits[0][0],
                'created'        => null,
                'creatorEmail'   => null,
                'creatorName'    => null,
                'instances'      => [],
                'label'          => 'Test Version 1',
                'name'           => 'TeSt1',
                'originInstance' => null
            ],
            'test2' => [
                'commit'         => $commits[1][1],
                'created'        => 1234567890,
                'creatorEmail'   => 'test@example.com',
                'creatorName'    => 'Test User',
                'instances'      => [],
                'label'          => 'Test Version 2',
                'name'           => 'test2',
                'originInstance' => null
            ],
            'test3' => [
                'commit'         => $commits[2][1],
                'created'        => 1234567890,
                'creatorEmail'   => 'test@example.com',
                'creatorName'    => 'Test User',
                'instances'      => ['Local'],
                'label'          => 'Test Version 2',
                'name'           => 'test3',
                'originInstance' => 'Test Instance'
            ]
        ], $versions->toArray(function ($version) {
            return $version->toArray();
        }));

        // delete a version
        $this->plugin->gitCommand(null, 'tag', '-d', 'test2');
        $versions->update();
        $this->assertCount(2, $versions);
        $this->assertSame([
            'TeSt1' => [
                'commit'         => $commits[0][0],
                'created'        => null,
                'creatorEmail'   => null,
                'creatorName'    => null,
                'instances'      => [],
                'label'          => 'Test Version 1',
                'name'           => 'TeSt1',
                'originInstance' => null
            ],
            'test3' => [
                'commit'         => $commits[2][1],
                'created'        => 1234567890,
                'creatorEmail'   => 'test@example.com',
                'creatorName'    => 'Test User',
                'instances'      => ['Local'],
                'label'          => 'Test Version 2',
                'name'           => 'test3',
                'originInstance' => 'Test Instance'
            ]
        ], $versions->toArray(function ($version) {
            return $version->toArray();
        }));
    }
}
