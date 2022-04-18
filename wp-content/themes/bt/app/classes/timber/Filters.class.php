<?php namespace app\classes\timber;

defined('ABSPATH') || exit;

use Timber;
use app\classes\helpers\Debug;

class Filters {
    static public function init () {
        // add file types to be shown in the theme editor
		add_filter('wp_theme_editor_filetypes', [__CLASS__, 'add_custom_editor_file_types']);
		
		// un-exclude directories in the theme editor
		add_filter('theme_scandir_exclusions', [__CLASS__, 'unexclude_theme_editor_dirs']);

		// add data to timber context
    	add_filter('timber_context', [__CLASS__, 'add_to_context']);

		// add function to twig
		add_filter('timber/twig', [__CLASS__, 'add_to_twig']);

		add_filter('timber_image_src', [__CLASS__, 'update_src_image']);
    }

    // add file types to be shown in the theme editor
	static public function add_custom_editor_file_types ($types) {
		$types[] = 'twig';

		return $types;
	}

	// un-exclude directories in the theme editor
	static public function unexclude_theme_editor_dirs ($exclusions) {
		// default exclusions: 'CVS', 'node_modules', 'vendor', 'bower_components'
		$unExclude = ['vendor'];

		return array_diff($exclusions, $unExclude);
	}

	// add data to timber context
  static public function add_to_context ($context) {
		// add menus
		$context['bt']['menus'] = [
			'header' => new Timber\Menu('main-site-menu'),
			'footer' => new Timber\Menu('main-site-footer-menu')
		];

		// add simplyad logo title
		$context['bt']['simplyad']['title'] = esc_html__('SimplyAd פיתוח וקידום אתרים', 'bt');

		// add current page content
		$context['page'] = Timber::get_post();

		// add pagination with shorter links range
		$context['pagination'] = Timber::get_pagination(3);

		if (count($context['pagination']['pages'])) {
			$context['pagination']['all'] = $context['posts']->found_posts;

			$paged 		 	 = $context['pagination']['current'];
			$posts_count = count($context['posts']);

			if ($paged == $context['pagination']['total']) {
				$context['pagination']['posts_start_count'] = $context['pagination']['all'] - $posts_count + 1;
				$context['pagination']['posts_end_count'] 	= $context['pagination']['all'];
			} else {
				$context['pagination']['posts_start_count'] = ($posts_count * $paged - $posts_count === 0) ? 1 : $posts_count * $paged - $posts_count;
				$context['pagination']['posts_end_count'] 	= $posts_count * $paged;

				if ($paged != 1) $context['pagination']['posts_start_count'] += 1;
			}

		}

		$context['options'] = [
			'site_logo_id' => get_option('site_logo'),
			'logo_text' => get_field('logo_text', 'option'),
			'user_link' => get_field('user_link', 'option'),
			'user_icon' => get_field('user_icon', 'option'),
			'my_compares_link' => get_field('my_compares_link', 'option'),
			'search_placeholder' => get_field('search_placeholder', 'option'),
			'header_links' => get_field('header_links', 'option'),
			'newsletter_bgc' => get_field('newsletter_bgc', 'option'),
			'newsletter_image' => get_field('newsletter_image', 'option'),
			'newsletter_big_title' => get_field('newsletter_big_title', 'option'),
			'newsletter_content' => get_field('newsletter_content', 'option'),
			'footer_bgc' => get_field('footer_bgc', 'option'),
			'sitemap_footer_title' => get_field('sitemap_footer_title', 'option'),
			'about_us_footer_title' => get_field('about_us_footer_title', 'option'),
			'about_us_footer_content' => get_field('about_us_footer_content', 'option'),
			'footer_logo' => get_field('footer_logo', 'option'),
			'default_banner' => get_field('default_banner', 'option'),
			'error_page_image' => get_field('error_page_image', 'option'),
			'error_page_text' => get_field('error_page_text', 'option'),
		];

		return $context;
	}

	// add function to twig
	static public function add_to_twig ($twig) {
		$functions = [
			'_' 				  => 'app\classes\helpers\Debug::_',
			'esc_html__' 		  => 'esc_html__',
			'get_permalink' 	  => 'get_permalink',
			'get_the_permalink'   => 'get_the_permalink',
			'get_category_link'   => 'get_category_link',
			'urldecode' 		  => 'urldecode',
			'get_term_meta'		  => 'get_term_meta',
			'is_product_category' => 'is_product_category',
			'is_product' 		  => 'is_product',
			'is_tax' 			  => 'is_tax',
			'get_queried_object'  => 'get_queried_object',
			'is_home'			  => 'is_home',
			'get_post_meta'		  => 'get_post_meta'
		];

		foreach ($functions as $name => $location) {
			$twig->addFunction(new Timber\Twig_Function($name, $location));
		}

		return $twig;
	}

	static public function update_src_image ($src) {
		if (BT_SITE_ENVIRONMENT == 'dev') return str_replace(BPATH, 'https://zolzolzol.simplyad.co.il', $src);
		return $src;
	}
}
