<?php

use Kirby\Cms\App;
use Kirby\Exception\Exception;

/**
 * Kirby Versions Plugin
 * Keep track of content changes and switch between
 * different versions from the Kirby Panel
 *
 * @package   Kirby Versions Plugin
 * @author    Lukas Bestle <project-kirbyversions@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-versions
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 */

// validate the Kirby version; the supported versions are
// updated manually when verified to work with the plugin
$kirbyVersion = App::version();
if (
	$kirbyVersion !== null &&
	(
		version_compare($kirbyVersion, '4.0.0-rc.1', '<') === true ||
		version_compare($kirbyVersion, '6.0.0-alpha', '>=') === true
	)
) {
	throw new Exception(
		'The installed version of the Kirby Versions plugin ' .
		'is not compatible with Kirby ' . $kirbyVersion
	);
}

// autoload classes
require_once __DIR__ . '/autoload.php';

// register the plugin
App::plugin('lukasbestle/versions', [
	'api'          => require __DIR__ . '/src/config/api.php',
	'areas'        => require __DIR__ . '/src/config/areas.php',
	'hooks'        => require __DIR__ . '/src/config/hooks.php',
	'options'      => require __DIR__ . '/src/config/options.php',
	'permissions'  => require __DIR__ . '/src/config/permissions.php',
	'translations' => require __DIR__ . '/src/config/translations.php'
]);
