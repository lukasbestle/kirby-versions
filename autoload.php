<?php

use Kirby\Filesystem\F;

F::loadClasses([
	'LukasBestle\Versions\Changes'   => __DIR__ . '/src/classes/Changes.php',
	'LukasBestle\Versions\Instance'  => __DIR__ . '/src/classes/Instance.php',
	'LukasBestle\Versions\Instances' => __DIR__ . '/src/classes/Instances.php',
	'LukasBestle\Versions\Plugin'    => __DIR__ . '/src/classes/Plugin.php',
	'LukasBestle\Versions\Version'   => __DIR__ . '/src/classes/Version.php',
	'LukasBestle\Versions\Versions'  => __DIR__ . '/src/classes/Versions.php'
]);
