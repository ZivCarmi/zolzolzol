<?php namespace app\classes\plugins\woocommerce;

defined('ABSPATH') || exit;

use Timber;
use app\classes\helpers\Debug;

class Actions {
	static public function init () {
		// dequeue default woocommerce styles/scripts
		//add_action('wp_enqueue_scripts', [__CLASS__, 'bt_dequeue_woocommerce_styles']);
		//add_action('wp_enqueue_scripts', [__CLASS__, 'bt_dequeue_woocommerce_scripts']);
		
		//======================================================================
		// General
		//======================================================================
		// remove sidebar
		remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar');

		// add woocommerce breadcrumbs to all site (hook's locate in views/partials/main-banner)
		add_action('bt_breadcrumbs', 'woocommerce_breadcrumb');
		//======================================================================
		// General - end
		//======================================================================

		//======================================================================
		// Archive product
		//======================================================================
		add_action('woocommerce_before_main_content', [__CLASS__, 'banner_on_archive_product'], 0);

		remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

		remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
		remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
		
		add_action('woocommerce_before_shop_loop', [__CLASS__, 'bt_shop_search'], 10);

		
		add_action('woocommerce_before_shop_loop', [__CLASS__, 'compare_prices_box'], 20);
		
		add_action('woocommerce_before_shop_loop', [__CLASS__, 'bt_before_catalog_ordering'], 25);
		add_action('woocommerce_before_shop_loop', [__CLASS__, 'bt_after_catalog_ordering'], 35);
		
		// Modal offer from companies
		add_action('bt_modal', [__CLASS__, 'bt_offer_from_companies_modal']);
		
		add_action('woocommerce_after_main_content', [__CLASS__, 'category_companies_box'], 10);

		add_action('woocommerce_after_main_content', [__CLASS__, 'bt_archive_newsletter']);
		//======================================================================
		// Archive product - end
		//======================================================================
		
		//======================================================================
		// Content product
		//======================================================================
		remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
		
		remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
		
		remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating ', 5);
		
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

		// Product package
		add_action('woocommerce_before_shop_loop_item', [__CLASS__, 'bt_product_main_block_start'], 0);
		add_action('woocommerce_after_shop_loop_item', [__CLASS__, 'bt_product_main_block_end'], 20);

		// Product image & check package on my compares page (First column start end)
		add_action('woocommerce_before_shop_loop_item_title', [__CLASS__, 'bt_product_first_column_start'], 5);
		add_action('woocommerce_before_shop_loop_item_title', [__CLASS__, 'bt_product_first_column_end'], 20);
		
		// Product title (Middle column start)
		add_action('woocommerce_shop_loop_item_title', [__CLASS__, 'bt_product_mid_column_start'], 0);
		add_action('woocommerce_shop_loop_item_title', [__CLASS__, 'bt_product_title_start'], 0);
		add_action('woocommerce_shop_loop_item_title', [__CLASS__, 'bt_product_title_end'], 20);

		// Product small info 
		add_action('woocommerce_shop_loop_item_title', [__CLASS__, 'bt_product_small_info'], 15);

		// Compare for me button (Middle column end)
		add_action('woocommerce_shop_loop_item_title', [__CLASS__, 'bt_product_last_column_join_btn'], 25);
		add_action('woocommerce_shop_loop_item_title', [__CLASS__, 'bt_package_button_actions_start'], 30);
		add_action('woocommerce_shop_loop_item_title', [__CLASS__, 'bt_share_button'], 35);
		add_action('woocommerce_shop_loop_item_title', [__CLASS__, 'bt_compare_for_me_button'], 35);
		add_action('woocommerce_shop_loop_item_title', [__CLASS__, 'bt_package_button_actions_end'], 40);
		add_action('woocommerce_shop_loop_item_title', [__CLASS__, 'bt_product_mid_column_end'], 45);
		
		// Product price (Last column start)
		add_action('woocommerce_after_shop_loop_item_title', [__CLASS__, 'bt_product_last_column_start'], 0);
		add_action('woocommerce_after_shop_loop_item_title', [__CLASS__, 'bt_product_last_column_join_btn'], 20);
		add_action('woocommerce_after_shop_loop_item_title', [__CLASS__, 'bt_product_last_column_end'], 25);
		
		// Product package dropdown
		add_action('woocommerce_after_shop_loop_item', [__CLASS__, 'bt_product_sub_block_start_end'], 30);

		//======================================================================
		// Content product - end
		//======================================================================

		add_action('bt_single_product', [__CLASS__, 'bt_single_product_tpl'], 0);
	}
	
	static public function bt_dequeue_woocommerce_styles () {
		if (!is_admin()) wp_dequeue_style('wc-block-style');
	}
	
	static public function bt_dequeue_woocommerce_scripts () {
		if (!is_admin()) {
			wp_dequeue_script('jquery-blockui');
			wp_dequeue_script('wc-add-to-cart');
			wp_dequeue_script('js-cookie');
			wp_dequeue_script('woocommerce');
			wp_dequeue_script('wc-cart-fragments');
		}
	}

	static public function banner_on_archive_product () {
		$is_companies_tax = is_tax('companies');
		if (!is_product_category() && !$is_companies_tax) return;

		if (($companies_ids = br_get_selected_term('companies')) || $is_companies_tax) {
			if (!empty($companies_ids)) {
				if (count($companies_ids) != 1) return;
				else $company_id = $companies_ids[0];
			} else {
				$company_id = get_queried_object_id();
			}

			$title = get_term_meta($company_id, 'partial_banner_page_title', true);

			Timber::render('partials/main-banner.twig', [
				'bgc' => get_term_meta($company_id, 'partial_banner_banner_bgc', true),
				'title' => $title ? $title : get_term_by('term_taxonomy_id', $company_id)->name,
				'subtitle' => get_term_meta($company_id, 'partial_banner_page_subtitle', true),
				'banner' => get_term_meta($company_id, 'partial_banner_banner', true),
			]);
		} else {
			Timber::render('partials/main-banner.twig');
		}
	}

	static public function bt_before_catalog_ordering () {
		$html = '<div class="sorts-heading">';
		$html .= '<div class="sorts-inner">';
		$html .= '<div class="sorts">';
		$html .= '<h2 class="sort-title">' . esc_html__('מיין לפי:', 'bt') . '</h2>';

		echo $html;
	}

	static public function bt_offer_from_companies_modal () {
		if (!is_product_category()) return;

		global $wpdb;

		$category = get_queried_object();

		$products_ids = implode(',', get_posts([
			'post_type' => 'product',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'fields' => 'ids',
			'tax_query' => [
				[
					'taxonomy' => 'product_cat',
					'terms' => [$category->term_id],
					'operator' => 'IN',
				]
			],
		]));

		if (!$products_ids) return;

		$sql = "SELECT t.term_id, t.name FROM wpuc_terms t, wpuc_term_taxonomy tt, wpuc_term_relationships tr 
		WHERE t.term_id=tt.term_id 
		AND tt.term_taxonomy_id=tr.term_taxonomy_id 
		AND tr.object_id IN({$products_ids}) 
		AND tt.taxonomy='companies' 
		GROUP BY t.name";

		$companies = $wpdb->get_results($sql);

		Timber::render('partials/companies-reps-modal.twig', [
			'category' => $category->name,
			'companies' => $companies,
		]);
	}

	static public function bt_archive_newsletter () {
		Timber::render('partials/newsletter.twig', [
			'options' => Timber::context()['options'],
		]);
	}

	static public function bt_after_catalog_ordering () {
		$html = '</div>';

		if (!is_search()) {
			$html .= '<div class="company-represents">';
			$html .= '<button type="button" class="modal-form use-modal" data-modal="companies-reps">' . esc_html__('לנציגי כל החברות', 'bt') . '</button>';
			$html .= '</div>';
		}
		
		$html .= '</div>';
		$html .= '</div>';

		echo $html ;
	}

	static public function bt_shop_search () {
		$search_query = get_search_query();

		if ($search_query) {
			$html = '<div class="search-head">';
			$html .= '<h1>' . esc_html__('תוצאות חיפוש ל - ' . $search_query, 'bt') . '</h1>';
			$html .= '</div>';

			echo $html;
		}
	}

	static public function category_companies_box () {
		if (!is_product_category()) return;

		$category = get_queried_object();
		
		$html = '<div class="category-companies container-1140">';

		$html .= Timber::compile('partials/heading-with-toggle.twig', [
			'tag' => 'h2',
			'name' => $category->name,
			'thumbnail_id' => get_term_meta($category->term_id, 'thumbnail_id', true),
		]);

		$companies = [];
		$companies_ids = get_term_meta($category->term_id, 'product_cat_companies', true);

		if ($companies_ids) {
			foreach ($companies_ids as $company_id) {
				$category = get_term($company_id);
				if ($category->count > 0) $companies[] = $category;
			}
		}

		$html .= Timber::compile('partials/companies-grid.twig', [
			'companies' => $companies,
		]);

		$html .= '</div>';

		echo $html;
	}

	static public function compare_prices_box () {
		if (!is_product_category()) return;

		$category = get_queried_object();

		$html = '<div class="compare-prices">';

		$html .= Timber::compile('partials/heading-with-toggle.twig', [
			'tag' => 'h2',
			'name' => esc_html__('השוואת מחירים', 'bt'),
			'subtitle' => $category->name,
			'colors_inverted' => true,
		]);
		
		$html .= '<ul class="compares-list">';

		$html .= '<li class="tracks compare-box">';
		$html .= '<h3 class="compare-name">' . esc_html__('בחר מסלולים', 'bt') . '</h3>';
		$html .= '<div class="choose-box">';
		$html .= do_shortcode('[br_filters_group group_id=298]');
		$html .= '</div>';
		$html .= '</li>';
		
		$html .= '<li class="companies compare-box">';
		$html .= '<h3 class="compare-name">' . esc_html__('בחר חברות', 'bt') . '</h3>';
		$html .= '<div class="choose-box">';
		$html .= do_shortcode('[br_filters_group group_id=301]');
		$html .= '</div>';
		$html .= '</li>';

		$html .= '</ul>';
		$html .= '</div>';

		echo $html;
	}

	static public function bt_product_main_block_start () {
		echo '<div class="main-block">';
	}

	static public function bt_product_main_block_end () {
		echo '</div>';
	}

	static public function bt_product_first_column_start () {
		global $product;

		$html = '<div class="first-column">';

		if (strpos(CPATH, '/admin-ajax.php') !== false) {
			$product_cats = get_the_terms($product->get_id(), 'product_cat');
			$categories = [];

			foreach ($product_cats as $product_cat) $categories[] = $product_cat->name;

			$value = esc_html__('קטגוריות: ' . implode(', ', $categories) . ' | חבילה: ' . $product->get_name());

			$html .= '<label class="check-package">';
			$html .= '<input type="checkbox" value="' . $value . '">';
			$html .= '<span class="custom-cb"></span>';
			$html .= '</label>';
		}

		echo $html;
	}

	static public function bt_product_first_column_end () {
		echo '</div>';
	}

	static public function bt_product_mid_column_start () {
		echo '<h2 class="mid-column-mobile">' . get_the_title() . '</h2>';
		echo '<div class="mid-column">';
	}

	static public function bt_product_mid_column_end () {
		echo '</div>';
	}

	static public function bt_product_title_start () {
		echo '<div class="package-title">';
	}

	static public function bt_product_title_end () {
		echo '</div>';
	}

	static public function bt_product_small_info () {
		global $product;

		Timber::render('plugins/woocommerce/templates/package-small-details.twig', [
			'small_package_info' => get_field('s_package_info'),
		]);
	}

	static public function bt_package_button_actions_start () {
		echo '<div class="package-action-buttons" data-aos="fade-left">';
	}

	static public function bt_compare_for_me_button () {
		if (IS_COMPARE_PAGE) return;

		Timber::render('plugins/woocommerce/templates/compare-for-me.twig', [
			'product_id' => get_the_ID(),
			'compare_btn' => get_field('compare_for_me_btn', 'option'),
		]);
	}

	static public function bt_share_button () {
		if (IS_COMPARE_PAGE) return;

		Timber::render('plugins/woocommerce/templates/share-button.twig', [
			'product_id' => get_the_ID(),
		]);
	}

	static public function bt_package_button_actions_end () {
		echo '</div>';
	}

	static public function bt_product_last_column_start () {
		$html = '';

		if (IS_COMPARE_PAGE) {
			$html .= '<button class="remove-package" type="button" data-package-id="' . get_the_ID() . '">X</button>';
		}

		$html .= '<div class="last-column">';

		echo $html;
	}

	static public function bt_product_last_column_join_btn () {
		$html = '<div class="to-join">';
		$html .= '<button type="button" class="toggle-package">' . esc_html__('לפרטים נוספים והצטרפות', 'bt') . '';
		$html .= '<span class="icon-chevron-down"></span>';
		$html .= '</button>';
		$html .= '</div>';

		echo $html;
	}

	static public function bt_product_last_column_end () {
		echo '</div>';
	}

	static public function bt_product_sub_block_start_end () {
		global $product;

		$_categories = get_the_terms($product->get_id(), 'product_cat');

		$categories = [];

		foreach ($_categories as $category) {
			$categories[] = $category->name;
		}

		Timber::render('plugins/woocommerce/templates/package-dropdown.twig', [
			'categories' => implode(',', $categories),
			'big_package_info' => get_field('b_package_info'),
		]);
	}

	static public function bt_single_product_tpl () {
		$product_id = get_post(get_the_ID());

		setup_postdata($GLOBALS['post'] =& $product_id);
		
		woocommerce_product_loop_start();
		wc_get_template_part('content', 'product');
		woocommerce_product_loop_end();
	}
}
