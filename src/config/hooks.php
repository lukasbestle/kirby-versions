<?php

namespace LukasBestle\Versions;

return [
	'system.loadPlugins:after' => function () {
		/** @psalm-scope-this \Kirby\Cms\App */
		Plugin::instance($this)->cleanExports();
	}
];
