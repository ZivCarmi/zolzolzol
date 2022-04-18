let mix = require('laravel-mix');

mix.setPublicPath('public');
mix.setResourceRoot('../');

mix.js('./assets/js/script.js', './public/js/script.js');

mix.css('./assets/css/pages/front-page.css', './public/css/front-page.css');
// mix.js('./assets/js/pages/front-page.js', './public/js/front-page.js');

mix.css('./assets/css/pages/disconnections.css', './public/css/disconnections.css');
// mix.js('./assets/js/pages/disconnections.js', './public/js/disconnections.js');

mix.css('./assets/css/pages/compares.css', './public/css/compares.css');

mix.css('./assets/css/pages/blog.css', './public/css/blog.css');

mix.css('./assets/css/pages/not-frayer.css', './public/css/not-frayer.css');

mix.css('./assets/css/pages/content-page.css', './public/css/content-page.css');

mix.css('./assets/css/pages/single.css', './public/css/single.css');

mix.css('./assets/css/pages/404.css', './public/css/404.css');

mix.css('./assets/woocommerce/css/product-category.css', './public/css/product-category.css');
// mix.js('./assets/woocommerce/js/product-category.js', './public/js/product-category.js');