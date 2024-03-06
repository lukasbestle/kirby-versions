<?php

namespace LukasBestle\Versions;

return [
	'routes' => [
		[
			/**
			 * All plugin data
			 * Returns all instances and all versions with all data
			 * necessary to display the Versions view in the Panel
			 *
			 * @return array
			 */
			'pattern' => 'versions',
			'method'  => 'GET',
			'action'  => function (): array {
				/** @psalm-scope-this \Kirby\Http\Route */
				$plugin = Plugin::instance($this->kirby());
				$plugin->checkPermission('access');

				return $plugin->toApiData();
			}
		],
		[
			/**
			 * Prepare version creation
			 * Stages all changes and validates that the version
			 * can be created based on the current set of changes
			 *
			 * @param string body:instance Name of the instance to create the version from
			 * @return array List of staged changes
			 */
			'pattern' => 'versions/prepareCreation',
			'method'  => 'POST',
			'action'  => function (): array {
				/** @psalm-scope-this \Kirby\Http\Route */
				$plugin = Plugin::instance($this->kirby());
				$plugin->checkPermission('create');

				$instance = $plugin->instances()->findOrException($this->requestBody('instance'));

				$instance->prepareCreation();
				return $instance->changes()->inIndex();
			}
		],
		[
			/**
			 * Create version
			 * Commits the previously prepared version
			 *
			 * @param string body:instance Name of the instance to create the version from
			 * @param string body:label Custom version label
			 * @return array Updated plugin data
			 */
			'pattern' => 'versions/create',
			'method'  => 'POST',
			'action'  => function (): array {
				/** @psalm-scope-this \Kirby\Http\Route */
				$plugin = Plugin::instance($this->kirby());
				$plugin->checkPermission('create');

				$instance = $plugin->instances()->findOrException($this->requestBody('instance'));

				$instance->createVersion($this->requestBody('label'));
				return $plugin->toApiData(['status' => 'ok']);
			}
		],
		[
			/**
			 * Delete version
			 * Deletes a version's Git tag
			 *
			 * @param string url:version Unique version name
			 * @return array Updated plugin data
			 */
			'pattern' => 'versions/versions/(:any)',
			'method'  => 'DELETE',
			'action'  => function (string $versionName): array {
				/** @psalm-scope-this \Kirby\Http\Route */
				$plugin = Plugin::instance($this->kirby());
				$plugin->checkPermission('delete');

				$version = $plugin->versions()->findOrException($versionName);

				$version->delete();
				return $plugin->toApiData(['status' => 'ok']);
			}
		],
		[
			/**
			 * Deploy version
			 * Deploys a version to a specified instance
			 *
			 * @param string body:instance Name of the instance to deploy to
			 * @param string url:version Unique version name
			 * @return array Updated plugin data
			 */
			'pattern' => 'versions/versions/(:any)/deploy',
			'method'  => 'POST',
			'action'  => function (string $versionName): array {
				/** @psalm-scope-this \Kirby\Http\Route */
				$plugin = Plugin::instance($this->kirby());
				$plugin->checkPermission('deploy');

				$instance = $plugin->instances()->findOrException($this->requestBody('instance'));
				$version  = $plugin->versions()->findOrException($versionName);

				$version->deployTo($instance);
				return $plugin->toApiData(['status' => 'ok']);
			}
		],
		[
			/**
			 * Export version
			 * Returns the URL to a ZIP file of the given version
			 *
			 * @param string url:version Unique version name
			 * @return array ZIP `url`, version `name` and `label`, `filesize`
			 */
			'pattern' => 'versions/versions/(:any)/export',
			'method'  => 'POST',
			'action'  => function (string $versionName): array {
				/** @psalm-scope-this \Kirby\Http\Route */
				$plugin = Plugin::instance($this->kirby());
				$plugin->checkPermission('export');

				$version = $plugin->versions()->findOrException($versionName);

				return array_merge($version->export(), [
					'label' => $version->label(),
					'name'  => $version->name()
				]);
			}
		]
	]
];
