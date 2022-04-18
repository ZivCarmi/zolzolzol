<?php namespace app\classes\admin;

defined('ABSPATH') || exit;

use app\classes\helpers\Debug;

class Pixels {
	static public function init () {
		// add google tag manager
		add_filter('admin_title', [__CLASS__, 'set_google_tag_manager']);
		
		// add google tag manager
		add_filter('admin_body_class', [__CLASS__, 'set_google_tag_manager_noscript']);
	}

	// add google tag manager
	static public function set_google_tag_manager ($title) {
		if (empty(GTAG_ID)) return $title;
		if (!is_admin() || get_post_type() !== 'shop_order' || !isset($_GET['action']) || $_GET['action'] !== 'edit') return $title;
	?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?= GTAG_ID; ?>');</script>
<?php	
		return $title;
	}
	
	// add google tag manager
	static public function set_google_tag_manager_noscript ($class) {
		if (empty(GTAG_ID)) return $class;
		if (!is_admin() || get_post_type() !== 'shop_order' || !isset($_GET['action']) || $_GET['action'] !== 'edit') return $class;
?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= GTAG_ID; ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php
		return $class;
	}
}
