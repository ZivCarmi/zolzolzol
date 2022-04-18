<?php namespace app\classes\admin;

defined('ABSPATH') || exit;

class Enqueue {
	// the template directories uri
	static private $TEMPLATE_DIRECTORY_URI = '';

	// file versions, helps with cache busting when developing and in production
	static private $FILES_VERSION = '';

	static private function set_properties () {
		self::$TEMPLATE_DIRECTORY_URI = get_template_directory_uri();

		self::$FILES_VERSION = '?v=' . time();
	}

	static public function init () {
		self::set_properties();

		add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_all']);
	}

	static public function enqueue_all () {
		self::outer_enqueue_styles();
		self::outer_enqueue_scripts();
		
		self::inner_enqueue_styles();
		self::inner_enqueue_scripts();
		
		self::enqueue_analytics_scripts();
	}
	
	static public function outer_enqueue_styles () {
		wp_enqueue_style('bt-icomoon', self::$TEMPLATE_DIRECTORY_URI . '/assets/fonts/style.css' . self::$FILES_VERSION);
	}
	
	static public function outer_enqueue_scripts () {
		
	}

	static private function inner_enqueue_styles () {
		wp_enqueue_style('bt-main', self::$TEMPLATE_DIRECTORY_URI . '/assets/admin/css/style.css' . self::$FILES_VERSION);
	}

	static private function inner_enqueue_scripts () {
		wp_enqueue_script('bt-main', self::$TEMPLATE_DIRECTORY_URI . '/assets/admin/js/script.js' . self::$FILES_VERSION, ['jquery'], '', true);
	}

	static private function enqueue_analytics_scripts () {
		if (class_exists('woocommerce') && (get_post_type() === 'shop_order' && (isset($_GET['action']) && $_GET['action'] === 'edit'))) {
			wp_enqueue_script('bt-google-woocommerce', self::$TEMPLATE_DIRECTORY_URI . '/assets/analytics/google/woocommerce/woocommerce.js' . self::$FILES_VERSION, ['jquery'], '', true);

			wp_localize_script('bt-google-woocommerce', 'btWooGlobals', [
				'currencyCode' => get_woocommerce_currency()
			]);
		}
	}
}
