<?php

return [
	// time after which versions should be deleted automatically;
	// defaults to one week
	'autodelete.age' => 7 * 24 * 60 * 60,

	// number of versions to preserve at maximum;
	// defaults to 20
	'autodelete.count' => 20,

	// path to the Git binary;
	// autodetected from PHP's `$PATH` if not set
	'git.path' => 'git',

	// list of the site instances that can be managed from the Panel;
	// disabled by default (which will limit the access to the current site);
	// note that you can configure this differently in each instance's
	// `site/config.php` to limit the access from specific instances
	// (e.g. if a test instance shouldn't be able to access production)
	'instances' => false
];
