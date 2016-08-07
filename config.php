<?php
/*
|--------------------------------------------------------------------------
| Enabled Plugins
|--------------------------------------------------------------------------
|
| The folder names of the TypeRocket plugins you wish to enable separated
| by pipes: seo|dev|theme-options|builder
|
*/
define('TR_PLUGINS', 'seo|dev|theme-options|builder');

/*
|--------------------------------------------------------------------------
| Debug
|--------------------------------------------------------------------------
|
| Turn on Debugging for TypeRocket. Set to false to disable.
|
*/
define('TR_DEBUG', true);

/*
|--------------------------------------------------------------------------
| TypeRocket Assets URL
|--------------------------------------------------------------------------
|
| The URL where TypeRocket assets can be found.
|
*/
define('TR_ASSETS_URL', get_stylesheet_directory_uri() . '/typerocket/assets');

/*
|--------------------------------------------------------------------------
| Seed
|--------------------------------------------------------------------------
|
| A 'random' string of text to help with security from time to time.
|
*/
define('TR_SEED', 'PUT_TYPEROCKET_SEED_HERE');