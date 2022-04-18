<?php namespace app\classes\theme;

defined('ABSPATH') || exit;

class Enqueue {
	// scripts dependencies
	const SCRIPTS_DEPS = [
		'jquery',
		'axios'
	];
	
	// the template directories uri
	static private $TEMPLATE_DIRECTORY_URI = '';

	// file versions, helps with cache busting when developing and in production
	static private $FILES_VERSION = '';

	static private function set_properties () {
		self::$TEMPLATE_DIRECTORY_URI = get_template_directory_uri();

		if (current_user_can('editor') || current_user_can('administrator')) {
			self::$FILES_VERSION = '?v=' . time();
		} else {
			self::$FILES_VERSION = '?v=000000';
		}
	}

	static public function init () {
		self::set_properties();

		add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_all']);
	}

	static public function enqueue_all () {
		// wp_deregister_script('jquery');

		self::outer_enqueue_styles();
		self::outer_enqueue_scripts();
		
		self::inner_enqueue_styles();
		self::inner_enqueue_scripts();
		
		self::enqueue_analytics_scripts();
	}
	
	static private function outer_enqueue_styles () {
		wp_enqueue_style('google-font', 'https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;700;900&display=swap');
		wp_enqueue_style('slick', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
	}
	
	static private function outer_enqueue_scripts () {
		wp_enqueue_script('slick', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', [], '', true);
		wp_enqueue_script('axios', 'https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js', [], '', true);
	}

	static private function inner_enqueue_styles () {
		// wp_enqueue_style('bt-icomoon', self::$TEMPLATE_DIRECTORY_URI . '/assets/fonts/style.css' . self::$FILES_VERSION);
		// wp_enqueue_style('bt-main', self::$TEMPLATE_DIRECTORY_URI . '/assets/css/style.css' . self::$FILES_VERSION);

		if (is_front_page()) {
			wp_enqueue_style('bt-front-page', self::$TEMPLATE_DIRECTORY_URI . '/public/css/front-page.css' . self::$FILES_VERSION);
			// wp_enqueue_style('bt-front-page', self::$TEMPLATE_DIRECTORY_URI . '/assets/css/pages/front-page.css' . self::$FILES_VERSION);
		} elseif (is_page_template('pages/disconnections.php')) {
			wp_enqueue_style('bt-disconnections', self::$TEMPLATE_DIRECTORY_URI . '/public/css/disconnections.css' . self::$FILES_VERSION);
			// wp_enqueue_style('bt-disconnections', self::$TEMPLATE_DIRECTORY_URI . '/assets/css/pages/disconnections.css' . self::$FILES_VERSION);
		} elseif (is_page_template('pages/compares.php')) {
			wp_enqueue_style('bt-compares', self::$TEMPLATE_DIRECTORY_URI . '/public/css/compares.css' . self::$FILES_VERSION);
		} elseif (is_page_template('pages/not-frayer.php')) {
			wp_enqueue_style('bt-not-frayer', self::$TEMPLATE_DIRECTORY_URI . '/public/css/not-frayer.css' . self::$FILES_VERSION);
		} elseif (is_page_template('pages/content-page.php')) {
			wp_enqueue_style('bt-content-page', self::$TEMPLATE_DIRECTORY_URI . '/public/css/content-page.css' . self::$FILES_VERSION);
		}  elseif (is_home()) {
			wp_enqueue_style('bt-blog', self::$TEMPLATE_DIRECTORY_URI . '/public/css/blog.css' . self::$FILES_VERSION);
		} elseif (is_single()) {
			wp_enqueue_style('bt-single', self::$TEMPLATE_DIRECTORY_URI . '/public/css/single.css' . self::$FILES_VERSION);
		} elseif (is_404()) {
			wp_enqueue_style('bt-404', self::$TEMPLATE_DIRECTORY_URI . '/public/css/404.css' . self::$FILES_VERSION);
		}

		if (is_product_category() || is_page_template('pages/compares.php') || is_search() || is_tax('companies') || is_product() || is_shop()) {
			wp_enqueue_style('bt-product-category', self::$TEMPLATE_DIRECTORY_URI . '/public/css/product-category.css' . self::$FILES_VERSION);
			// wp_enqueue_style('bt-product-category', self::$TEMPLATE_DIRECTORY_URI . '/assets/woocommerce/css/product-category.css' . self::$FILES_VERSION);
		}

		wp_dequeue_style('berocket_aapf_widget-style');
	}

	static private function inner_enqueue_scripts () {
		// wp_enqueue_script('bt-main', self::$TEMPLATE_DIRECTORY_URI . '/assets/js/script.js' . self::$FILES_VERSION, self::SCRIPTS_DEPS, '', true);
		wp_enqueue_script('bt-main', self::$TEMPLATE_DIRECTORY_URI . '/public/js/script.js' . self::$FILES_VERSION, self::SCRIPTS_DEPS, '', true);

		$system_globals = [
			'ajaxNonce' => wp_create_nonce('bt_site_ajax_nonce'),
			'ajaxUrl' 	=> BPATH . '/wp-admin/admin-ajax.php',
			'BPATH' 	=> BPATH,
			'FPATH' 	=> FPATH,
			'CPATH' 	=> CPATH,
			'TINYGIF' 	=> TINYGIF,
			//'userId' 	=> is_user_logged_in() ? wp_get_current_user()->user_email : false,
			'userId' 	=> is_user_logged_in() ? get_current_user_id() : false
		];
		
		wp_localize_script('bt-main', 'btSystemGlobals', $system_globals);

		if (is_front_page()) {
			// wp_enqueue_script('bt-front-page', self::$TEMPLATE_DIRECTORY_URI . '/public/js/front-page.js' . self::$FILES_VERSION, self::SCRIPTS_DEPS, '', true);
			wp_enqueue_script('bt-front-page', self::$TEMPLATE_DIRECTORY_URI . '/assets/js/pages/front-page.js' . self::$FILES_VERSION, self::SCRIPTS_DEPS, '', true);
		} elseif (is_page_template('pages/disconnections.php')) {
			// wp_enqueue_script('bt-disconnections', self::$TEMPLATE_DIRECTORY_URI . '/public/js/disconnections.js' . self::$FILES_VERSION, self::SCRIPTS_DEPS, '', true);
			wp_enqueue_script('bt-disconnections', self::$TEMPLATE_DIRECTORY_URI . '/assets/js/pages/disconnections.js' . self::$FILES_VERSION, self::SCRIPTS_DEPS, '', true);
		} elseif (is_page_template('pages/compares.php')) {
			// wp_enqueue_script('bt-disconnections', self::$TEMPLATE_DIRECTORY_URI . '/public/js/disconnections.js' . self::$FILES_VERSION, self::SCRIPTS_DEPS, '', true);
			wp_enqueue_script('bt-compares', self::$TEMPLATE_DIRECTORY_URI . '/assets/js/pages/compares.js' . self::$FILES_VERSION, self::SCRIPTS_DEPS, '', true);
		} elseif (is_single()) {
			wp_enqueue_script('bt-single', self::$TEMPLATE_DIRECTORY_URI . '/assets/js/pages/single.js' . self::$FILES_VERSION, self::SCRIPTS_DEPS, '', true);
		}
		
		if (is_product_category() || is_page_template('pages/compares.php') || is_search() || is_tax('companies') || is_product()  || is_shop()) {
			// wp_enqueue_script('bt-product-category', self::$TEMPLATE_DIRECTORY_URI . '/public/js/product-category.js' . self::$FILES_VERSION, self::SCRIPTS_DEPS, '', true);
			wp_enqueue_script('bt-product-category', self::$TEMPLATE_DIRECTORY_URI . '/assets/woocommerce/js/product-category.js' . self::$FILES_VERSION, self::SCRIPTS_DEPS, '', true);
		}
	}

	static private function enqueue_analytics_scripts () {
		if (class_exists('woocommerce') && (!empty(GTAGA_ID) || !empty(GTAG_ID))) {
			wp_enqueue_script('bt-google-woocommerce', self::$TEMPLATE_DIRECTORY_URI . '/assets/analytics/google/woocommerce/woocommerce.js' . self::$FILES_VERSION, self::SCRIPTS_DEPS, '', true);

			wp_localize_script('bt-google-woocommerce', 'btWooGlobals', [
				'currencyCode' => get_woocommerce_currency()
			]);
		}
		
		if (class_exists('WPCF7_Submission')) wp_enqueue_script('bt-google-contact-form-7', self::$TEMPLATE_DIRECTORY_URI . '/assets/analytics/google/contact-form-7/contact-form-7.js' . self::$FILES_VERSION, self::SCRIPTS_DEPS, '', true);
	}
}
