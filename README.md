# drupal-library-mapping

A helper for installing library assets for Drupal.

If you're wanting to use asset-packagist.org for installing Drupal libraries, 
this will set things up properly, including renaming some common libraries 
which need to be named differently for Drupal.

## How to use the plugin

First, require "bmcclure/drupal-library-mapping".

Enable Asset Packagist support, add the following to your
repositories section:

    {
        "type": "composer",
        "url": "https://asset-packagist.org"
    }

Set up your installer paths in extras, like this:

    "installer-types": ["library", "drupal-library", "bower-asset", "npm-asset"],
    "installer-paths": {
        "libraries/{$name}": [
            "type:drupal-library",
            "type:bower-asset",
            "type:npm-asset"
        ]
    },

The plugin will automatically handle installing bower and npm assets to the 
"libraries/" directory (but you can override the location in your own 
composer.json if you wish).

There's nothing else you need to do after requiring this plugin
in your main composer.json file, just start requiring things from asset-packagist!

## Customizing the package name map

You may wish to map other library names to common ones.

You can add new mappings to the "drupal-library-mapping" key in extras.

Example:

    "drupal-library-mapping": {
        "jquery-easing-original": "easing"
    }
