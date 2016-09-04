var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Assets
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your TypeRocket application. By default, we are compiling the
 | Theme and Admin files for our application, as well as publishing
 | vendor resources.
 |
 */
elixir.config.assetsPath = './resources/assets';
elixir.config.publicPath = './wordpress/assets/templates';
elixir.config.sourcemaps = false;

elixir(function(mix) {
    // Directories
    var templates = elixir.config.publicPath;

    // Your WordPress Templates SASS
    mix.sass('theme.scss');
    mix.sass('admin.scss');

    // Your WordPress Templates JS
    mix.scripts([
        'plugins.js',
        'theme.js'
    ], templates + '/js/theme.js');

    mix.scripts([
        'admin.js'
    ], templates + '/js/admin.js');

});

/*
 |--------------------------------------------------------------------------
 | Update TypeRocket Assets
 |--------------------------------------------------------------------------
 |
 | Uncomment this section if you want to update the TypeRocket assets each
 | time you compile your resources. Run `npm update typerocket-assets`
 | to check for new versions.
 |
 */

// var typerocket = require('typerocket-assets');
// typerocket.compileTypeRocketAssets( './wordpress/assets' );