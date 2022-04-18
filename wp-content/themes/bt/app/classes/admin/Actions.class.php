<?php namespace app\classes\admin;

defined('ABSPATH') || exit;

class Actions {
	static public function init () {
		// remove wordpress welcome panel from dashboard
		remove_action('welcome_panel', 'wp_welcome_panel');
		
		add_action('admin_init', [__CLASS__, 'remove_menu_links']);
	}

	static public function remove_menu_links () {
		if (BT_SITE_ENVIRONMENT === 'prod') remove_menu_page('plugins.php');
	}
}
