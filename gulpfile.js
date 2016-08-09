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

    // Your WordPress App
    mix.sass('theme.scss');
    mix.sass('admin.scss');

    mix.scripts([
        'plugins.js',
        'theme.js'
    ], elixir.config.publicPath + '/js/theme.js');

    mix.scripts([
        'plugins.js',
        'admin.js'
    ], elixir.config.publicPath + '/js/admin.js');

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
    ], elixir.config.publicPath + '/js/typerocket.js' );
    mix.sass('typerocket/typerocket.scss');

});