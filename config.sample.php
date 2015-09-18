<?php
/*
|--------------------------------------------------------------------------
| TypeRocket Folder
|--------------------------------------------------------------------------
|
| The name of the folder containing TypeRocket.
|
*/
define('TR_FOLDER', 'typerocket');

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
| by pipes.
|
*/
define('TR_PLUGINS', 'seo|dev|theme-options');

/*
|--------------------------------------------------------------------------
| Matrix Folder
|--------------------------------------------------------------------------
|
| The folder where you wish to locate your matrix groups. This should be set
| to a location outside of TypeRocket. For example, directly in your
| current theme.
|
*/
define('TR_MATRIX_FOLDER_PATH', __DIR__ . '/matrix');

/*
|--------------------------------------------------------------------------
| Application Folder
|--------------------------------------------------------------------------
|
| The folder where you wish to locate your application code. This could 
| be set to a location outside of TypeRocket. For example, directly in 
| your current theme.
|
*/
define('TR_APP_FOLDER_PATH', __DIR__ . '/app');

/*
|--------------------------------------------------------------------------
| Plugins Folder
|--------------------------------------------------------------------------
|
| The folder where you wish to locate your plugins. This should be set
| to a location outside of TypeRocket. For example, directly in your
| current theme.
|
*/
define('TR_PLUGINS_FOLDER_PATH', __DIR__ . '/plugins');
define('TR_PLUGINS_URL', get_stylesheet_directory_uri() . '/' .TR_FOLDER . '/plugins');

/*
|--------------------------------------------------------------------------
| Seed
|--------------------------------------------------------------------------
|
| A 'random' string of text to help with security from time to time.
|
*/
define('TR_SEED', 'asdfa65739asfl0');

/*
|--------------------------------------------------------------------------
| Assets
|--------------------------------------------------------------------------
|
| The folder where you wish to locate typerocket core assets.
|
*/
define('TR_ASSETS_URL', get_stylesheet_directory_uri() . '/' . TR_FOLDER . '/assets');