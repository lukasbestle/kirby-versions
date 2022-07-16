<?php

namespace LukasBestle\Versions;

use Kirby\Cms\App;
use Kirby\Filesystem\Dir;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
	protected $contentRoot1 = __DIR__ . '/tmp/content1';
	protected $contentRoot2 = __DIR__ . '/tmp/content2';
	protected $kirby;
	protected $plugin;
	protected $tmp = __DIR__ . '/tmp';

	public function setUp(): void
	{
		$this->kirby = new App([
			'blueprints' => [
				'users/editor' => [
					'name' => 'editor',
					'permissions' => [
						'lukasbestle.versions' => [
							'access' => true,
							'create' => false
						]
					]
				],
				'users/no-access' => [
					'name' => 'no-access',
					'permissions' => [
						'lukasbestle.versions' => false
					]
				]
			],
			'roots' => [
				'index'   => '/dev/null',
				'content' => $this->contentRoot1,
				'media'   => $this->tmp . '/media'
			],
			'urls' => [
				'media' => 'https://example.com/media'
			],
			'users' => [
				[
					'id'    => 'test-editor',
					'name'  => 'Test Editor',
					'email' => 'test-editor@example.com',
					'role'  => 'editor'
				],
				[
					'id'    => 'test-no-access',
					'name'  => 'Test No Access',
					'email' => 'test-no-access@example.com',
					'role'  => 'no-access'
				]
			]
		]);

		Dir::remove($this->tmp);
		Dir::make($this->contentRoot1);
		touch($this->contentRoot1 . '/.gitkeep');
		Dir::make($this->contentRoot2);

		$this->plugin = new Plugin($this->kirby);
		$this->plugin->gitCommand(null, 'init', '-b', 'main');
		$this->plugin->gitCommand(null, 'add', '-A');
		$this->plugin->gitCommand(null, '-c', 'user.name=Test', '-c', 'user.email=test@example.com', 'commit', '-m', 'Initial commit');
		$this->plugin->gitCommand(null, 'tag', 'initial');
		$this->plugin->gitCommand(null, 'checkout', 'initial');
		$this->plugin->gitCommand(null, 'worktree', 'add', $this->contentRoot2, 'initial');
	}

	public function tearDown(): void
	{
		Dir::remove($this->tmp);
	}
}
