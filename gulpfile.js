var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Assets
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your TypeRocket application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */
elixir.config.assetsPath = './resources/assets';
elixir.config.publicPath = './wordpress/assets';
elixir.config.sourcemaps = false;

elixir(function(mix) {
    // Directories
    var assets = elixir.config.publicPath;
    var templates = elixir.config.publicPath + '/templates';

    // Your WordPress Templates SASS
    mix.sass('theme.scss', templates + '/css/theme.css');
    mix.sass('admin.scss', templates + '/css/admin.css' );

    // Your WordPress Templates JS
    mix.scripts([
        'plugins.js',
        'theme.js'
    ], templates + '/js/theme.js');

    mix.scripts([
        'admin.js'
    ], templates + '/js/admin.js');

    // TypeRocket Core Assets
    mix.coffee([
        'typerocket/http.coffee',
        'typerocket/booyah.coffee',
        'typerocket/typerocket.coffee',
        'typerocket/items.coffee',
        'typerocket/media.coffee',
        'typerocket/matrix.coffee',
        'typerocket/builder.coffee',
        'typerocket/seo.coffee',
        'typerocket/link.coffee',
        'typerocket/dev.coffee'
    ], assets + '/typerocket/js/core.js' );
    mix.sass('typerocket/typerocket.scss', assets + '/typerocket/css/core.css' );

});