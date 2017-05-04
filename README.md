# drupal-library-mapping

A helper for installing library assets for Drupal.

If you're wanting to use asset-packagist.org for installing Drupal libraries, 
this will set things up properly, including renaming some common libraries 
which need to be named differently for Drupal.

## How to use the plugin

First, require "wikimedia/composer-merge-plugin" and "bmcclure/drupal-library-mapping".

Then, update your merge-plugin settings similar to this. Make sure 
to set merge-extre to true!

(Note: This assumes you're including core's composer.json as well.)

    "merge-plugin": {
        "include": [
            "core/composer.json",
            "vendor/bmcclure/drupal-library-mapping/composer.json"
        ],
        "recurse": false,
        "replace": false,
        "merge-dev": false,
        "merge-extra": true
    }

Next, if you want to enable Asset Packagist support, add the following to your
repositories section:

    {
        "type": "composer",
        "url": "https://asset-packagist.org"
    }

The plugin will automatically handle installing bower and npm assets to the 
"libraries/" directory (but you can override the location in your own 
composer.json if you wish).

There's nothing else you need to do after requiring this plugin
in your main composer.json file, just start requiring things from asset-packagist!

## Customizing the package name map

You may wish to map other library names to common ones.

You can override the "drupal-library-mapping" key in extras. It's recommended to 
copy the default one and then extend it. Example:

    "drupal-library-mapping": {
        "jquery-easing-original": "easing"
    }
