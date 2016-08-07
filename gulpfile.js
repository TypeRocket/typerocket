var elixir = require('laravel-elixir');

elixir.config.assetsPath = './resource';
elixir.config.typeRocketPublicAssetsPath = './assets';
elixir.config.sourcemaps = false;

elixir(function(mix) {
    var coffee_items = [
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
    ];

    mix.coffee( coffee_items , elixir.config.typeRocketPublicAssetsPath + '/js/typerocket.js', 'coffee');
    mix.sass( ['typerocket.scss'] , elixir.config.typeRocketPublicAssetsPath + '/css/typerocket.css');
});