<?php

return [
	'view.versions' => 'Versionen',

	'error.versions.git.nonzero' => 'Ein Git-Fehler ist aufgetreten: { message }',
	'error.versions.git.version' => 'Das Versionen-Plugin benötigt Git 2.5+, du hast Git { version }',
	'error.versions.internal' => 'Interner Fehler im Versionen-Plugin (Fehlercode { code })',
	'error.versions.instance.noRepo' => 'Der content-Ordner der Instanz { instance } ist nicht mit einem Git-Repo verbunden, bitte entweder ein neues Repo einrichten oder es als Worktree verbinden',
	'error.versions.instance.noWorktree' => 'Der content-Ordner der Instanz { instance } ist kein Worktree des content-Ordners der aktuellen Site, bitte verbinde die beiden Instanzen als Worktrees',
	'error.versions.instance.onBranch' => 'Der content-Ordner der Instanz { instance } hat noch einen ausgecheckten Branch, bitte führe `git checkout` mit dem aktuellsten Git-Tag aus',
	'error.versions.lockFiles' => 'Aufgrund von ungespeicherten Änderungen an folgenden Seiten und Dateien kann derzeit keine Version erstellt werden:',
	'error.versions.noChanges' => 'Es gibt derzeit keine Änderungen, mit denen eine Version erstellt werden kann',
	'error.versions.notPrepared' => 'Die Version wurde noch nicht vorbereitet',
	'error.versions.notFound.instance' => 'Die Instanz { name } existiert nicht',
	'error.versions.notFound.version' => 'Die Version { name } existiert nicht',
	'error.versions.permission' => 'Du darfst dies nicht tun (fehlende { permission }-Berechtigung)',
	'error.versions.version.inUse' => 'Die Version wird derzeit verwendet',

	'versions.button.copyLink' => 'Link kopieren',
	'versions.button.create' => 'Version erstellen',
	'versions.button.delete' => 'Löschen',
	'versions.button.deploy' => 'Verwenden',
	'versions.button.download' => 'Herunterladen',
	'versions.button.export' => 'Exportieren',

	'versions.label.changes' => 'Änderungen',
	'versions.label.creation' => 'Erstellung',
	'versions.label.creationData' => '{created} ({creator})',
	'versions.label.current' => 'aktuell',
	'versions.label.empty' => 'Noch keine Versionen',
	'versions.label.fileSize' => 'Dateigröße',
	'versions.label.instances' => 'Instanzen',
	'versions.label.label' => 'Beschriftung',
	'versions.label.originInstance' => 'Ursprungsinstanz',
	'versions.label.status.+' => 'Status: erstellt',
	'versions.label.status.-' => 'Status: gelöscht',
	'versions.label.status.C' => 'Status: kopiert',
	'versions.label.status.M' => 'Status: geändert',
	'versions.label.status.R' => 'Status: umbenannt',
	'versions.label.targetInstance' => 'Zielinstanz',
	'versions.label.versionName' => 'Versionsname',
	'versions.label.versions' => 'Versionen',

	'versions.message.delete' => 'Möchtest du diese Version wirklich löschen?',
	'versions.message.exporting' => 'Version wird exportiert...',

	'versions.name.autosave' => 'Automatischer Schnappschuss',
	'versions.name.local' => 'Lokal'
];
