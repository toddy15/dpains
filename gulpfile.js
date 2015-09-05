var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.styles([
        'bootstrap.min.css',
        'bootstrap-theme.min.css',
    ], 'public/css/app.css');
    mix.scripts([
        'jquery.min.js',
        'bootstrap.min.js',
    ], 'public/js/app.js');
    mix.version([
        'css/app.css',
        'js/app.js'
    ]);
    mix.copy('resources/assets/fonts', 'public/build/fonts');
});
