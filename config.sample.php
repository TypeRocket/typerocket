<?php
/*
|--------------------------------------------------------------------------
| TypeRocket URL
|--------------------------------------------------------------------------
|
| The folder where you wish to locate typerocket core assets.
|
*/
define('TR_URL', get_stylesheet_directory_uri() . '/typerocket');

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
| Enabled Plugins
|--------------------------------------------------------------------------
|
| The folder names of the TypeRocket plugins you wish to enable separated
| by pipes: seo|dev|theme-options|builder
|
| Plugins are located at: https://github.com/TypeRocket/plugins
|
*/
define('TR_PLUGINS', 'theme-options');

/*
|--------------------------------------------------------------------------
| Seed
|--------------------------------------------------------------------------
|
| A 'random' string of text to help with security from time to time.
|
*/
define('TR_SEED', 'PUT_TYPEROCKET_SEED_HERE');