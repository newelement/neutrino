let mix = require('laravel-mix');

mix.options({ processCssUrls: false })
.sass('resources/assets/sass/app.scss', 'publishable/assets/css', { implementation: require('node-sass') })
.sass('resources/assets/sass/theme.scss', 'publishable/assets/css', { implementation: require('node-sass') })
.sass('resources/assets/sass/blocks.scss', 'publishable/assets/css', { implementation: require('node-sass') })
.js(['resources/assets/js/app.js','resources/assets/js/filemanager.js'], 'publishable/assets/js/app.js')
.js('resources/assets/js/analytics.js', 'publishable/assets/js')
.js('resources/assets/js/theme.js', 'publishable/assets/js')
.copy('node_modules/tinymce/skins', 'publishable/assets/js/skins').sourceMaps();
