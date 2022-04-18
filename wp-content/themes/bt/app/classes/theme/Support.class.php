<?php namespace app\classes\theme;

defined('ABSPATH') || exit;

class Support {
	static public function init () {
		self::add_theme_support();

		// add additional mime types for media
		add_filter('upload_mimes', [__CLASS__, 'add_allowed_media_mime_types'], 1, 1);

		// add additional media image file sizes
		add_action('after_setup_theme', [__CLASS__, 'add_media_image_sizes']);
	}

	static public function add_theme_support () {
		// add menus support
		add_theme_support('menus');

		// add thumbnails support
		add_theme_support('post-thumbnails');

		// make script and styles tags in html5 format
		add_theme_support('html5', ['script', 'style']);

		// lets the wordpress manage the documnet title
		add_theme_support('title-tag');
		
		// add custom site logo support
		add_theme_support('custom-logo');

		// add support for woocommerce plugin
		if (class_exists('woocommerce')) add_theme_support('woocommerce');
	}

	// add additional mime types for media
	static public function add_allowed_media_mime_types ($mime_types) {
		if (current_user_can('administrator')) {
			$mime_types['svg'] = 'image/svg+xml';
			$mime_types['svgz'] = 'image/svg+xml';
		};

		return $mime_types;
	}

	// add additional media image file sizes
	static public function add_media_image_sizes () {
		add_image_size('bt-image-400', 400);
		add_image_size('bt-image-500', 500);
	}
}
