var elixir = require('laravel-elixir');

elixir.config.assetsPath = './assets';
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
        'link.coffee',
        'dev.coffee'
    ];

    mix.coffee( coffee_items , elixir.config.assetsPath + '/js/typerocket.js', 'coffee');
    mix.sass(['*.scss'], elixir.config.assetsPath + '/css/typerocket.css');
});