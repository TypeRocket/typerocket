const mix = require('laravel-mix');

/*
|--------------------------------------------------------------------------
| Template Assets
|--------------------------------------------------------------------------
|
| All public assets must be compiled to the /wordpress/assets/templates
| directory. Commands like mix.babel and mix.copyDirectory do not use
| the configured public path.
|
| Laravel Mix documentation can be found at https://laravel-mix.com/.
|
| Watch: `npm run watch`
| Production: `npm run prod`
|
*/

// Options
let pub = 'wordpress/assets/templates';
mix.setPublicPath(pub).options({ processCssUrls: false });

// Front-end
mix.js('resources/assets/js/theme.js', 'theme')
    .sass('resources/assets/sass/theme.scss', 'theme');

// Admin
mix.js('resources/assets/js/admin.js', 'admin')
    .sass('resources/assets/sass/admin.scss', 'admin');

// Version
if (mix.inProduction()) {
    mix.version();
}
