<?php defined('ABSPATH') || exit;

// The base path of the site, example http://www.mysite.com
define('BPATH', get_site_url());

// the full path of the site page, query included, example http://www.mysite.com/product-category/pants?size=large&color=red
define('FPATH', filter_var(BPATH . $_SERVER['REQUEST_URI'] , FILTER_SANITIZE_URL));

// the full path of the site page, without query, example http://www.mysite.com/product-category/pants
define('CPATH', explode('?',FPATH )[0]);

// the path to the tiny gif image
define('TINYGIF', BPATH . '/wp-content/themes/bt/assets/images/tinygif.gif');

// the direction of the site
define('IS_RTL', is_rtl());

// get the current language
define('CURRENT_LANG', get_locale());

// alias for directory separator
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

define('IS_COMPARE_PAGE', isset($_GET['compare_packages']));