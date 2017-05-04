# drupal-library-mapping

A helper for installing library assets for Drupal.

If you're wanting to use asset-packagist.org for installing Drupal libraries, 
this will set things up properly, including renaming some common libraries 
which need to be named differently for Drupal.

## How to use the plugin

First, require "bmcclure/drupal-library-mapping".

Enable Asset Packagist support following their instructions.

Configure the following in extra:

    "installer-types": ["library", "drupal-library", "bower-asset", "npm-asset"],
    "installer-paths": {
        "libraries/{$name}": [
            "type:drupal-library",
            "type:bower-asset",
            "type:npm-asset"
        ]
    }

Now simply require assets from Asset Packagist, and they'll be mapped appropriately.

## Customizing the copy type

By default, the mapped plugins are copied.

Add the following to extras to customize the method used for mapping:

    "drupal-library-mapping-type": "symlink"
    
Accepted values are "copy" (the default), "symlink", and "move".

Note that a side effect of using "move" is that composer will reinstall the package every time it runs.

## Customizing the package name map

You may wish to map other library names to common ones.

You can add new mappings to the "drupal-library-mapping" key in extras.

Example:

    "drupal-library-mapping": {
        "jquery-easing-original": "easing"
    }
