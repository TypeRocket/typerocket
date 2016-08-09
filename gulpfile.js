var elixir = require('laravel-elixir');

elixir.config.assetsPath = './resource';
elixir.config.publicPath = './wordpress/assets';
elixir.config.sourcemaps = false;

elixir(function(mix) {

    mix.coffee( [
        'http.coffee',
        'booyah.coffee',
        'typerocket.coffee',
        'items.coffee',
        'media.coffee',
        'matrix.coffee',
        'builder.coffee',
        'seo.coffee',
        'link.coffee',
        'dev.coffee'
    ] );
    mix.sass( [
        'typerocket/typerocket.scss',
        'app.scss'
    ] );
});