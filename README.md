# Versions for Kirby 3

[![Kirby 3.5.4+](https://img.shields.io/badge/Kirby-3.5.4%2B-green)](https://getkirby.com)
[![MIT license](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)
[![Release](https://img.shields.io/github/v/release/lukasbestle/kirby-versions)](https://github.com/lukasbestle/kirby-versions/releases/latest)
[![CI Status](https://img.shields.io/github/workflow/status/lukasbestle/kirby-versions/ci?label=CI)](https://github.com/lukasbestle/kirby-versions/actions?query=workflow%3ACI)
[![Coverage Status](https://img.shields.io/codecov/c/gh/lukasbestle/kirby-versions?token=IBYEIB22SM)](https://codecov.io/gh/lukasbestle/kirby-versions)

> Keep track of content changes and switch between different versions from the Kirby 3 Panel

![Screenshot of the Versions view in the Kirby Panel](screenshot.png)

## Overview

> The Versions plugin is completely free and published under the terms of the MIT license. I do not sell licenses or accept donations, but I'm available for contract work regarding feature development for this plugin.  
> ➯ [Read more…](.github/CONTRIBUTING.md#monetary-support)

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
  - [Download](#download)
  - [Git submodule](#git-submodule)
  - [Composer](#composer)
- [Setup](#setup)
  - [1. Create a `content/.gitignore` file](#1-create-a-contentgitignore-file)
  - [2. Create a Git repository with the initial version](#2-create-a-git-repository-with-the-initial-version)
  - [3. Set up a connection to other site instances (optional)](#3-set-up-a-connection-to-other-site-instances-optional)
  - [4. Set up the plugin configuration in your site's `config.php` (if you want to change the defaults)](#4-set-up-the-plugin-configuration-in-your-sites-configphp-if-you-want-to-change-the-defaults)
  - [5. Set up user permissions (optional)](#5-set-up-user-permissions-optional)
  - [6. Set up remote backup (optional, but recommended)](#6-set-up-remote-backup-optional-but-recommended)
  - [7. ADVANCED: Set up automatic version creation (optional)](#7-advanced-set-up-automatic-version-creation-optional)
- [Usage](#usage)
  - [Create a version](#create-a-version)
  - [Export to ZIP file](#export-to-zip-file)
  - [Deploy a version to the same or a different instance](#deploy-a-version-to-the-same-or-a-different-instance)
  - [Delete a version](#delete-a-version)
- [License](#license)
- [Contributing & Monetary Support](#contributing--monetary-support)
- [Credits](#credits)

## Features

- Create and delete content versions directly from the Panel including metadata (author, custom label)
- Export versions as ZIP files for local backup
- Switch between the versions on the fly
- Support for multiple site instances (e.g. production and staging) that share their versions and that can be deployed to from a single Panel instance
- Support for fine-grained user permissions

## Requirements

- Kirby 3.5.4+
- Git 2.5+ (ideally newer for better reliability)

**Note:** Deploying versions to multiple sites currently only works if all sites are hosted on the same server (i.e. if Kirby has access to the file system of all sites).

## Installation

### Download

[Download](https://github.com/lukasbestle/kirby-versions/archive/main.zip) and copy this repository to `/site/plugins/versions`.

### Git submodule

```
git submodule add https://github.com/lukasbestle/kirby-versions.git site/plugins/versions
```

### Composer

```
composer require lukasbestle/kirby-versions
```

## Setup

The Versions plugin uses Git under the hood to store and archive versions. It is **very important** to follow all of these steps in the correct order, otherwise the plugin will not work. You will only need to follow the steps once per Kirby site. Afterwards the plugin will manage the Git repository automatically.

The steps are sorted from the most basic to the most advanced. Steps marked as optional can be skipped entirely if you do not need the mentioned features.

The terminal commands that are listed below should preferrably be run directly on the server for best results.

**Note:** The following commands assume that the content of your Kirby site is not already in a Git repository. Please note that it is *not* recommended to use the Versions plugin with a Git repository that also includes other site files as this will prevent you from deploying code changes without breaking the content versions. You should therefore create a new Git repository just for the `content` directory of your Kirby site.

### 1. Create a `content/.gitignore` file

Before you set up the Versions plugin, you need to think about which files should be managed by it and which files should be ignored.

You need to define all files that you don't want managed by the plugin (e.g. dynamic content created by forms on the site) by listing the file paths in the `content/.gitignore` file. Create the file if it doesn't exist. You can read more about the `.gitignore` format in the [Git documentation](https://git-scm.com/book/en/v2/Git-Basics-Recording-Changes-to-the-Repository#_ignoring).

**Important:** Be careful about ignoring `.lock` files. These are created by Kirby's content locking feature and tell the Versions plugin that someone is still editing pages, which will prevent the plugin from creating and switching versions. Ignoring these files using Git will disable the unsaved changes warning of the Versions plugin. You can use the plugin with the warning disabled, but please note that switching versions while someone still has unsaved changes may lead to lost changes (e.g. when switching to a version where the page that was being edited didn't exist). If that's fine for your use-case, feel free to ignore `.lock` files.

### 2. Create a Git repository with the initial version

```sh
# Ensure you are in the `content` directory of your Kirby site
cd /var/www/your-site/content

# Generate a name for the Git tag based on the current date
tagName="$(date +%Y%m%d_initial)"

# Initialize a new Git repository and add all files to it
git init
git add -A
```

**Important:** Please now verify using the search function of your terminal (<kbd>Ctrl</kbd>/<kbd>Cmd</kbd> <kbd>F</kbd>) that no `.lock` file was added, which would break the plugin's behavior later. If there is a `.lock` file listed in the output, please save or discard the changes and run `git add -A` again to tell Git to remove the `.lock` file from the index. The initial version must not have unsaved changes.

```sh
# Commit the current state as the initial version
git commit -m "Initial version"
git tag $tagName -am "Initial version"

# Switch to the new tag
git checkout $tagName

# Delete the `master` branch as the branch will not be updated;
# WARNING: Only do this if you have created the content repository
# just for the Versions plugin, otherwise keep the `master` branch
git branch -d master
```

### 3. Set up a connection to other site instances (optional)

Following this section will allow you to deploy your versions to other site instances (e.g. staging, production) later. This feature is completely optional.

In the following steps, the "main site" is the site where you have initialized the Git repository by following the steps above. The "other sites" are the sites that you want to integrate into the process.

1. Run the commands from the above section in the `content` directory of your main site to set up the repository.
2. Delete the `content` directories of the other sites. Make sure to keep a backup of the content in case it differs from the content of the main site.
3. Run the following command in the `content` directory of your main site for each other site you want to set up:

```sh
git worktree add /path/to/other/site/content $tagName
```

4. Configure the instances in the Kirby configuration of all sites (see below).

### 4. Set up the plugin configuration in your site's `config.php` (if you want to change the defaults)

The Versions plugin supports the following options in `site/config/config.php`:

```php
<?php

return [
    'lukasbestle.versions' => [
        // time after which versions should be deleted automatically;
        // defaults to one week
        'autodelete.age' => 7 * 24 * 60 * 60,

        // number of versions to preserve at maximum;
        // defaults to 20
        'autodelete.count' => 20,

        // path to the Git binary;
        // autodetected if not set
        'git.path' => '/usr/local/bin/git',

        // list of the site instances that can be managed from the Panel;
        // disabled by default (which will limit the access to the current site);
        // note that you can configure this differently in each instance's
        // `site/config.php` to limit the access from specific instances
        // (e.g. if a test instance shouldn't be able to access production)
        'instances' => [
            // instance name that is displayed in the Panel
            'Staging' => [
                // path to the content directory
                'contentRoot' => '/path/to/staging/site/content',

                // CSS color for display in the Panel
                'color' => '#f5871f'
            ],

            // ...
        ]
    ]
];
```

### 5. Set up user permissions (optional)

You can customize the permissions for each role individually by creating a [user blueprint](https://getkirby.com/docs/guide/users/permissions#role-based-permissions-in-user-blueprints). The Versions plugin provides the following permissions that you can individually enable or disable:

```yaml
title: Editor
permissions:
  lukasbestle.versions:
    access: true
    create: true
    delete: true
    deploy: true
    export: true
```

If you want to allow read access but no changes, you can set the permissions like this:

```yaml
title: Editor
permissions:
  lukasbestle.versions:
    *: false
    access: true
```

You can also entirely disable the Versions view:

```yaml
title: Editor
permissions:
  lukasbestle.versions: false
```

If permissions are not configured at all, the user will have full permissions for every feature the plugin provides.

### 6. Set up remote backup (optional, but recommended)

As the Versions plugin just uses a simple Git repository under the hood, you can backup your site's content using Git.

First, create a Git repository on the backup destination and set it up as a remote in your content repository on the server:

```sh
git remote add origin <URL>
```

Now you can create a cronjob that runs regularly (for example daily at night) that runs the following command to backup all versions to the remote:

```sh
git -C /var/www/your-site/content push --tags origin
```

**Note:** If your site's content is larger than a few hundred MB, please first check with your Git hosting service that they are OK with using them as a backup destination. GitHub for example [doesn't like repos larger than 1 GB](https://help.github.com/en/github/managing-large-files/what-is-my-disk-quota#file-and-repository-size-limitations).

Alternative ways of creating backups of your content are:

- Creating an empty repository on another server (anywhere in the file system) and using it as a remote over SSH (with `user@domain:/path/to/the/repo`).
- Creating an empty repository on your local machine and adding your site's content directory as a remote so you can *fetch* versions from it manually every now and then.
- ...

### 7. ADVANCED: Set up automatic version creation (optional)

The Versions plugin is primarily suited for manual version creation whenever there is a meaningful set of changes. You can however also automatically create versions as an automatic backup in case the editors forget to create versions themselves.

Versions can be created with a simple script like this:

```sh
#!/bin/bash

# ▼────  customize the following four lines
authorName="Automatic snapshot"
authorEmail="snapshot@example.com"
message="Automatic snapshot at $(date "+%Y-%m-%d %H:%M:%S")"
cd /var/www/your-site/content
# ▲────

# check if there are any Kirby lock files
if ls .lock &> /dev/null || ls **/.lock &> /dev/null; then
    echo "Found Kirby .lock files, no automatic snapshot created"
    exit 0
fi

git add -A
git -c "user.name=$authorName" -c "user.email=$authorEmail" commit -m "$message" || exit 0
commit="$(git rev-parse --short HEAD)"
git -c "user.name=$authorName" -c "user.email=$authorEmail" tag "$(date +%Y%m%d_snapshot_$commit)" -am "$message"
```

You can run the script from a cronjob, for example daily at night. Ensure that the `git` binary is available in `cron`'s `$PATH`.

Versions created with the commands from the example script above will automatically be picked up by the Versions plugin just like versions created from the Versions interface.

## Usage

You can access the Versions plugin as a Panel view in the main hamburger menu. The Versions view displays the version of each instance and the open changes of the current instance as well as a list with all versions.

The version list allows exporting a ZIP file of the version, deploying it to any connected site instance and deleting the version.

You can create new versions from the current content state at any time using the "Create Version" button above the list of changes at the top.

### Create a version

Creating a new version is only possible if no user has unsaved changes (unless `.lock` files are ignored in `.gitignore`, see the setup instructions above).

Before the version is created, the plugin will display a list of changes included in the version so that you can verify what has changed. You can then give the version a custom label and create the version with the click of a button.

### Export to ZIP file

Clicking the export button will export a ZIP file with all content files of the selected version and place it in the `media` directory. You can either download the ZIP file directly or copy the URL to the clipboard for sharing.

The ZIP file can be downloaded by anyone knowing the direct URL. It is automatically deleted after two hours.

By creating a `content/.gitattributes` file, you can configure which files are excluded from the export (but still saved in the versions that are created). This can be useful for sensitive information like customer data. You can read more about the `export-ignore` feature in the [Git documentation](https://git-scm.com/book/en/v2/Customizing-Git-Git-Attributes#_exporting_your_repository).

### Deploy a version to the same or a different instance

After clicking on the deploy button, the plugin will ask you what the deploy destination (any of the configured site instances) should be.

The plugin will verify that the target site does not have any unversioned changes (otherwise it will create an automatic snapshot). Once everything is clean, it will switch the target site to the selected version.

### Delete a version

The delete button will (after confirmation) delete the underlying Git tag of the version, which will make the version disappear from the list in the Panel.

Because of how Git works internally, old versions can still be recovered from the Git commit history (`git log`), but only if the old version you want to recover was used as the base for any version that still exists.

**Example:** Based on the initial version, you have created two versions A1 and B1, each changing the prior version (A1 changes the initial version and B1 changes A1). You have then switched back to version A1 and created version B2 based on A1. If version B1 is deleted, it will be gone as no other version still references it – Git will garbage-collect it soon. However it will always be possible to recover the initial version and version A1 even if they are deleted as both B1 and B2 are based on them.

If the content repository gets too large after many larger changes, you can reset it by deleting the `content/.git` directory and following the setup steps 2 and 3 again. Make sure to create a backup of the `.git` directory before deleting it in case you still need changes from it later.

## License

[The MIT License](LICENSE.md)

## Contributing & Monetary Support

See [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Credits

- Author and developer: [Lukas Bestle](https://lukasbestle.com)
- Idea: [Sascha Lack](https://slstudio.de)
