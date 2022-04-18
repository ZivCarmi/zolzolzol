<?php namespace app\classes\plugins\acf;

defined('ABSPATH') || exit;

class OptionPages {
	static public function init () {
		self::create_option_pages();

		add_filter('acf/settings/show_admin', [__CLASS__, 'bt_hide_menu']);
	}
	
	static public function bt_hide_menu ($show) {
		if (BT_SITE_ENVIRONMENT === 'prod') return false;
		return $show;
	}
	
	static public function create_option_pages () {
		if (function_exists('acf_add_options_page')) {
			if (CURRENT_LANG === 'he_IL') {
				$titles = [
					'parent'  => esc_html__('הגדרות כלליות לתבנית', 'bt'),
					'header'  => esc_html__('הגדרות HEADER לתבנית', 'bt'),
					'footer'  => esc_html__('הגדרות FOOTER לתבנית', 'bt'),
					'contact' => esc_html__('הגדרות יצירת קשר לתבנית', 'bt'),
					'social'  => esc_html__('הגדרות מדיה חברתית לתבנית', 'bt'),
					'newsletter'  => esc_html__('הגדרות ניוזלטר', 'bt'),
					'shop'  => esc_html__('הגדרות דף חבילות', 'bt'),
					'404'  => esc_html__('הגדרות דף 404', 'bt'),
				];
			} else {
				$titles = [
					'parent'  => esc_html__('General theme options', 'bt'),
					'header'  => esc_html__('Header theme options', 'bt'),
					'footer'  => esc_html__('Footer theme options', 'bt'),
					'contact' => esc_html__('Contact theme options', 'bt'),
					'social'  => esc_html__('Social media theme options', 'bt'),
					'newsletter'  => esc_html__('Newsletter options', 'bt'),
					'shop'  => esc_html__('Packages page options', 'bt'),
					'404'  => esc_html__('404 page options', 'bt'),
				];
			}
			
			$parent = acf_add_options_page([
				'page_title' 	=> $titles['parent'],
				'menu_title'	=> $titles['parent'],
				'menu_slug' 	=> 'general',
				'capability'	=> 'edit_posts',
				'icon_url' 		=> 'dashicons-admin-settings',
				'redirect'		=> false,
			]);

			foreach ($titles as $page => $desc) {
				if ($page === 'parent') continue;

				acf_add_options_sub_page([
					'page_title' 	=> $desc,
					'menu_title'	=> $desc,
					'menu_slug' 	=> $page,
					'parent_slug' => $parent['menu_slug'],
				]);
			}
		}
	}
}
