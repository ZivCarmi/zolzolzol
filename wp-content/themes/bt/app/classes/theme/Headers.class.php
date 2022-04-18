<?php namespace app\classes\theme;

defined('ABSPATH') || exit;

class Headers {
	static public function init () {
		add_action('send_headers', [__CLASS__, 'add_headers']);
	}

	static public function add_headers () {
		header('Server: bt');
		header('x-Powered-By: bt');
		header('X-Frame-Options: SAMEORIGIN');
	}
}
