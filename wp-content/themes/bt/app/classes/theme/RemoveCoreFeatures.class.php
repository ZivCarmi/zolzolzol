<?php namespace app\classes\theme;

defined('ABSPATH') || exit;

class RemoveCoreFeatures {
	static public function init () {
		global $sitepress;
		
		// remove generator (wordpress version tag)
		remove_action('wp_head', 'wp_generator');
		add_filter('the_generator', [__CLASS__, 'bt_remove_wp_generator']);
		
		// disable access to RSS feed
		remove_action('wp_head', 'feed_links_extra', 3);
		remove_action('wp_head', 'feed_links', 2);
		add_action('do_feed', [__CLASS__, 'bt_disable_rss_feed'], 1);
		add_action('do_feed_rdf', [__CLASS__, 'bt_disable_rss_feed'], 1);
		add_action('do_feed_rss', [__CLASS__, 'bt_disable_rss_feed'], 1);
		add_action('do_feed_rss2', [__CLASS__, 'bt_disable_rss_feed'], 1);
		add_action('do_feed_atom', [__CLASS__, 'bt_disable_rss_feed'], 1);
		add_action('do_feed_rss2_comments', [__CLASS__, 'bt_disable_rss_feed'], 1);
		add_action('do_feed_atom_comments', [__CLASS__, 'bt_disable_rss_feed'], 1);
		
		// remove dns prefetch
		add_filter('emoji_svg_url', [__CLASS__, 'bt_remove_dns_prefetch']);
		
		// remove emojis
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_action('admin_print_styles', 'print_emoji_styles');

		// disable wp-embed
		add_action('wp_footer', [__CLASS__, 'bt_remove_wp_embed']);

		// Disable REST API link tag
		remove_action('wp_head', 'rest_output_link_wp_head', 10);

		// Disable oEmbed Discovery Links
		remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

		// Disable REST API link in HTTP headers
		remove_action('template_redirect', 'rest_output_link_header', 11, 0);

		// Remove Gutenberg Block Library CSS from loading on the frontend
		add_action('wp_enqueue_scripts', [__CLASS__, 'bt_remove_wp_block_library_css']);

		// remove rsd xml link
		remove_action ('wp_head', 'rsd_link');

		// remove wlwmanifest link
		remove_action('wp_head', 'wlwmanifest_link');

		// remove shorlink
		add_filter('after_setup_theme', [__CLASS__, 'bt_remove_shortlink']);

		// remove the version from css and js files
		add_filter('style_loader_src', [__CLASS__, 'remove_css_and_js_versions'], 9999);
		add_filter('script_loader_src', [__CLASS__, 'remove_css_and_js_versions'], 9999);
		
		// remove jquery migrate
		add_action('wp_default_scripts', [__CLASS__, 'bt_remove_jquery_migrate']);
		
		// remove dashicons
		add_action('wp_print_styles', [__CLASS__, 'bt_remove_dashicons'], 100);

		// remove add new content link from top toolbar
		add_action('admin_bar_menu', [__CLASS__, 'bt_remove_wp_nodes'], 999);

		// remove WPML Generator
		remove_action('wp_head', [$sitepress, 'meta_generator_tag']);

		// Remove dns-prefetch Link from WordPress Head (Frontend)
		remove_action('wp_head', 'wp_resource_hints', 2);
	}

	static public function bt_remove_wp_generator () {
		return '';
	}

	static public function bt_disable_rss_feed () {
		wp_redirect('/');
		die;
	}
	
	static public function bt_remove_dns_prefetch () {
		return false;
	}

	static public function bt_remove_wp_embed () {
		wp_dequeue_script('wp-embed');
	}

	static public function bt_remove_wp_block_library_css () {
		wp_dequeue_style('wp-block-library');
		wp_dequeue_style('wp-block-library-theme');
	}

	static public function bt_remove_shortlink () {
		remove_action('wp_head', 'wp_shortlink_wp_head', 10);
		remove_action('template_redirect', 'wp_shortlink_header', 11);
	}
	
	static public function remove_css_and_js_versions ($src) {
		if (!is_admin() && strpos($src, 'ver=')) return remove_query_arg('ver', $src);
		return $src;
	}
	
	static public function bt_remove_jquery_migrate ($scripts) {
		if (!is_admin() && isset($scripts->registered['jquery'])) {
			$script = $scripts->registered['jquery'];

			if ($script->deps) $script->deps = array_diff($script->deps, ['jquery-migrate']);
		}
	}

	static public function bt_remove_dashicons () {
		wp_deregister_style('amethyst-dashicons-style'); 
		//wp_deregister_style('dashicons');
	}

	static public function bt_remove_wp_nodes () {
		global $wp_admin_bar;

		$wp_admin_bar->remove_node('new-content');
	}
}
