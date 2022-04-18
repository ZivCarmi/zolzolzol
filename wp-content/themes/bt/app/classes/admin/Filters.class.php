<?php namespace app\classes\admin;

defined('ABSPATH') || exit;

class Filters {
	static public function init () {
		// replace the default error message on wordperss login form
		add_filter('login_errors', [__CLASS__, 'replace_login_errors']);

		// replace dashboard footer text
		add_filter('admin_footer_text', [__CLASS__, 'replace_dashboard_text']);
		
		// Activating Classic Editor
		add_filter('use_block_editor_for_post', '__return_false');
	}
	
	// replace the default error message on wordperss login form
	static public function replace_login_errors () {
		if (CURRENT_LANG === 'he_IL') return esc_html__('פרטי משתמש או סיסמה לא נכונים', 'br');
		else return esc_html__('User details or password are incorrect', 'bt');
	}
	
	// replace dashboard footer text
	static public function replace_dashboard_text () {
		if (CURRENT_LANG === 'he_IL') esc_html_e('פותח ומתוחזק על ידי סימפלי אד', 'bt');
		else esc_html_e('Developed and maintained by SimplyAd', 'bt');
	}
}
