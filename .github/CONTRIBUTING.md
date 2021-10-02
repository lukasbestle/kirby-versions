# Contributing to the Versions plugin

👍️🎉 First off, thanks for taking the time to contribute! 🎉👍️

There are a few ways how you can contribute to the development of the Versions plugin:

## Report issues & suggest features

While the Versions plugin was originally created for a client project, the goal is to make it useful for other projects as well. For that I need to know if something isn't working or if something can be improved.

It's already a huge help to receive detailed bug reports and thought-through feature requests. You can submit any of these by [creating a new issue](https://github.com/lukasbestle/kirby-versions/issues/new) and using the issue templates, which are set up to guide you through providing all information. The more relevant information you give, the easier it is to find a solution for the bug or feature/enhancement request.

## Help with translations

The Panel interface of the Versions plugin uses the configured user language for all the displayed text. Of course I don't speak all languages of the world, so I need your help.

You can contribute additional translations by forking this repo and adding a new file in the [`src/config/i18n` directory](https://github.com/lukasbestle/kirby-versions/tree/main/src/config/i18n). You can copy any of the existing translation files and use them as the base (however the English translation in `en.php` is considered the "original" and complete version). Please also add your translation to the list in [`src/config/translations.php`](https://github.com/lukasbestle/kirby-versions/blob/main/src/config/translations.php). Once you are ready, send a pull request to this repo.

If you want to improve existing translations, you can change the translation files directly and send a pull request.

Thank you for your help! ❤️

## Code contributions

Pull requests for bug fixes or enhancements are always welcome!

Please note that I can't guarantee that your pull request will be merged, especially if it's for a larger feature. If you want to make sure, please create an issue first (or comment in an existing issue, if one already exists for the task you want to work on) to discuss your idea.

### How to set up the project for local development

If you want to contribute, please first fork this repo.

Now set up a local installation of one of the Kirby kits (I recommend the [Starterkit](https://github.com/getkirby/starterkit) or [Demokit](https://github.com/getkirby/demokit)). Create the directory `/site/plugins` if it doesn't already exist and then clone your fork of the Versions plugin into `/site/plugins/versions`.

If you want to work on frontend code, please run the following commands inside `/site/plugins/versions`:

```sh
npm install
npm run dev
```

This will run the kirbyup bundler, which will listen for changes to the files inside the `src/frontend` directory. You can now open the Panel of your Kirby installation.

If you want to work on backend code and want to run the automated tests, you need the following command:

```sh
composer install
```

The tests assume that you have PHPUnit installed globally. I also use a few other analysis tools that each have their commands listed in `/composer.json`. With `composer ci` you can run all tools at once (which assumes that all tools have been installed globally). If you don't want to install tools, don't worry – all tools will also be run automatically once you create your pull request.

**Note:** Never commit the changes to the compiled dist files `/index.css` and `/index.js`. Including these files in your PR will lead to merge conflicts down the road. Instead, I will build the dist files for each plugin release.

## Monetary support

Most of my development time for this plugin has already been paid for, which is why I'm offering the plugin for free under the terms of the MIT license. For the same reason I do not sell licenses or accept donations.

However you can support my work by commissioning the development of a feature of your choice: If your project requires a specific feature, you can pay me to build it for you. In exchange for your support you will get the feature more quickly and it will be designed with your requirements in mind. Additionally I can mention you as the sponsor of the feature in the Credits section of the plugin's `README`.

Features developed in this way will also be published to this repo so that the community can benefit from them as well. This approach also enables future improvements and fixes to the feature.

If you are interested, please get in touch directly via the [Kirby Forum](https://forum.getkirby.com/u/lukasbestle), [Discord](https://chat.getkirby.com) or [email](mailto:project-kirbyversions@codesignd.de) to discuss the details.
