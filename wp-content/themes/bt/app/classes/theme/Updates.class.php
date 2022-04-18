<?php namespace app\classes\theme;

defined('ABSPATH') || exit;

class Updates {
	static public function init () {
		add_filter('auto_update_plugin', '__return_false');
		add_filter('auto_update_theme', '__return_false');
	}
}
