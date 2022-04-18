<?php namespace app\classes\plugins\woocommerce;

defined('ABSPATH') || exit;

use Timber;
use app\classes\helpers\Debug;

class Shortcodes {
	static public function init () {
		// woocommerce minicart basic template
		add_shortcode('bt_woo_minicart', [__CLASS__, 'minicart_template']);

		add_shortcode('bt_woo_minicart_count', [__CLASS__, 'minicart_count_template']);

		// custom woocommerce filters for show/category/tags
		// add_shortcode('bt_woo_filters', [__CLASS__, 'custom_woocommerce_filters']);
	}

	static public function minicart_count_template () {
		$args = [
			'display_zero' 	   => 1,
			'cart_items_count' => WC()->cart->cart_contents_count
		];

		return Timber::compile('plugins/woocommerce/shortcodes/minicart-count.twig', $args);
	}

	static public function minicart_template () {
		return Timber::compile('plugins/woocommerce/shortcodes/minicart.twig');
	}

	// custom woocommerce filters for show/category/tags
	static public function custom_woocommerce_filters ($atts) {
		// get products ids
		self::get_products_ids($products_ids);
		
		// taxonomies list, which taxonomies to get and in what order
		$taxonomies = [
			'product_cat' => esc_html__('קטגוריות'),
			'product_tag' => esc_html__('תגיות'),
			'bananas' 	  => esc_html__('מותגים'),
			'pa_color' 	  => esc_html__('צבע'),
			'pa_size' 	  => esc_html__('מידה')
		];
		
		// get products taxonomies
		self::get_products_taxonomies($raw_taxonomies, $products_ids, $taxonomies);
		
		// prepare the raw taxonomies for output
		self::prepare_taxonomies($taxonomies, $raw_taxonomies);

		ob_start();
		
		get_template_part('woocommerce/tpl/shop-filters', null, $taxonomies);
		
		return ob_get_clean();
	}

	// get queried products ids
	static private function get_products_ids (&$products_ids) {
		global $wp_query;

		// get current query
		$products_ids_args = $wp_query->query_vars;

		// posts per page (-1 not works so use a high number)
		$products_ids_args['posts_per_page'] = 1000000;

		// get only products ids
		$products_ids_args['fields'] = 'ids';

		// get product ids using the current query args
		$products_ids = get_posts($products_ids_args);
	}
	
	// get all taxonomies of quiered products ids
	static private function get_products_taxonomies (&$raw_taxonomies, $products_ids, $taxonomies) {
		global $wpdb;
		
		$sql = 'SELECT t.term_id, t.name, tt.taxonomy, tt.parent, tr.term_order';
		$sql .= " FROM {$wpdb->terms} t";
		$sql .= " JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id";
		$sql .= " JOIN {$wpdb->term_relationships} tr ON tr.term_taxonomy_id = tt.term_taxonomy_id";
		$sql .= " WHERE tt.taxonomy IN ('" . implode('\', \'', array_keys($taxonomies)) . "')";
		$sql .= ' AND tr.object_id IN (' . implode(',', $products_ids) . ')';
		$sql .= ' GROUP BY t.term_id';

		$raw_taxonomies = $wpdb->get_results($sql);
	}
	
	// prepare the raw taxonomies for output
	static private function prepare_taxonomies (&$taxonomies, $raw_taxonomies) {
		foreach ($raw_taxonomies as $taxonomy) {
			switch ($taxonomy->taxonomy) {
				case 'product_cat':
					// prepare taxonomies with children
					self::prepare_multi_taxonomy($taxonomies, $taxonomy, $taxonomy->taxonomy);

					break;
				default:
					// prepare taxonomies without children
					self::prepare_flat_taxonomy($taxonomies, $taxonomy);
			}
		}
	}
	
	// prepare taxonomies without children
	static private function prepare_multi_taxonomy (&$taxonomies, $taxonomy, $type, $show_children = true) {
		// get current category id, if not found set to 0 (top level categories)
		$current_category_id = ($type === 'product_cat' && is_product_category()) ? get_queried_object_id() : 0;

		// check if child parent relationship is needed
		if ($show_children) {
			// parent child relationship
			if ($current_category_id === 0) {
				if ($taxonomy->parent == 0) {
					if (!is_array($taxonomies[$type])) self::add_tax_into_taxonomies($taxonomies, $taxonomy, $type);
					else {
						// if taxonomy already exists in taxonomies, exit
						if (self::does_tax_exists($taxonomies, $taxonomy, $type)) return;
						
						// prepare taxonomies without children
						self::add_tax_into_taxonomies($taxonomies, $taxonomy, $type);
					}
				} else {
					$tax_ancestors = array_reverse(get_ancestors($taxonomy->term_id, $type));
					
					$tax_ancestors[] = $taxonomy->term_id;
					
					$parent_tax = get_term_by('id', $tax_ancestors[0], $type);
					
					if (!is_array($taxonomies[$type])) self::add_tax_into_taxonomies($taxonomies, $parent_tax, $type);
					else {
						// if taxonomy already exists in taxonomies, exit
						if (!self::does_tax_exists($taxonomies, $parent_tax, $type)) self::add_tax_into_taxonomies($taxonomies, $parent_tax, $type);
					}
					
					foreach ($taxonomies[$type]['terms'] as &$_taxonomy) {
						if ($_taxonomy->term_id != $tax_ancestors[0]) continue;
						
						array_shift($tax_ancestors);
						
						self::prepare_child_parent_taxonomy($taxonomies, $taxonomy, $type, [
							'parent' 	=> $_taxonomy->term_id,
							'ancestors' => $tax_ancestors
						]);
						
// 						if (!isset($_taxonomy)) $_taxonomy->terms = [$tax_ancestors];
// 						else $_taxonomy->terms[] = $tax_ancestors;
					}
				}
			} else {
				
			}
		} else {
			// direct child relationship

			// if not direct child of category id, exit
			if ($current_category_id != $taxonomy->parent) return;

			// add a single taxonomy into taxonomies
			self::add_tax_into_taxonomies($taxonomies, $taxonomy, $type);
		}
	}
	
	static private function prepare_child_parent_taxonomy (&$taxonomies, $taxonomy, $type, $options) {
		if (is_array($taxonomies) && isset($taxonomies[$type]) && isset($taxonomies[$type]['terms'])) {
			foreach ($taxonomies[$type]['terms'] as &$_taxonomy) {
				if ($_taxonomy->term_id != $options['parent'] || $taxonomy->parent != $options['parent']) continue;

				if (!isset($_taxonomy->terms)) $_taxonomy->terms = [$taxonomy];
				else $_taxonomy->terms[] = $taxonomy;
			}
		}
		
// 		if (is_array($taxonomies) && isset($taxonomies[$type]) && isset($taxonomies[$type]['terms'])) $terms =& $taxonomies[$type]['terms'];
// 		elseif (isset($taxonomies->terms)) $terms =& $taxonomies->terms;
		
// 		if (!isset($terms)) return;
		
// 		foreach ($terms as &$_taxonomy) {
// 			if ($_taxonomy->term_id != $taxonomy->term_id) continue;
			
// 			if (!isset($_taxonomy->terms)) $_taxonomy->terms = [$taxonomy];
// 			else $_taxonomy->terms[] = $taxonomy;
// 		}
	}
	
	// prepare taxonomies without children
	static private function prepare_flat_taxonomy (&$taxonomies, $taxonomy) {
		// add a single taxonomy into taxonomies
		self::add_tax_into_taxonomies($taxonomies, $taxonomy, $taxonomy->taxonomy);
	}
	
	// add a single taxonomy into taxonomies
	static private function add_tax_into_taxonomies (&$taxonomies, $taxonomy, $type) {
		if (!is_array($taxonomies[$type]))
			$taxonomies[$type] = [
				'title' => $taxonomies[$type],
				'terms' => [$taxonomy]
			];
		else
			// TODO - NEED TO ADD EXLUDE LOGIC
			
			if (isset($taxonomies[$type]['terms'])) $taxonomies[$type]['terms'][] = $taxonomy;
			else $taxonomies[$type]['terms'] = [$taxonomy];
	}
	
	// if taxonomy already exists in taxonomies, exit
	static private function does_tax_exists ($taxonomies, $taxonomy, $type = '') {
		if (is_array($taxonomies) && isset($taxonomies[$type]) && isset($taxonomies[$type]['terms'])) {
			foreach ($taxonomies[$type]['terms'] as &$_taxonomy) {
// 				if (isset($_taxonomy->terms)) return self::does_tax_exists($_taxonomy, $taxonomy);
				if ($_taxonomy->term_id == $taxonomy->term_id) return true;
			}
		} else if (isset($taxonomies->terms)) {
			foreach ($taxonomies->terms as &$_taxonomy) {
// 				if (isset($_taxonomy->terms)) return self::does_tax_exists($_taxonomy, $taxonomy);
				if ($_taxonomy->term_id == $taxonomy->term_id) return true;
			}
		}
		
		return false;
	}
	
	// get min max prices of queried products
	static private function get_filtered_price () {
		global $wpdb;

		$args = wc()->query->get_main_query();

		$tax_query  = isset($args->tax_query->queries) ? $args->tax_query->queries : [];
		$meta_query = isset($args->query_vars['meta_query']) ? $args->query_vars['meta_query'] : [];

		foreach ($meta_query + $tax_query as $key => $query) {
			if (!empty($query['price_filter']) || !empty($query['rating_filter'])) unset($meta_query[$key]);
		}

		$meta_query = new \WP_Meta_Query($meta_query);
		$tax_query  = new \WP_Tax_Query($tax_query);

		$meta_query_sql = $meta_query->get_sql('post', $wpdb->posts, 'ID');
		$tax_query_sql  = $tax_query->get_sql($wpdb->posts, 'ID');

		$sql  = "SELECT min( FLOOR( price_meta.meta_value ) ) as min_price, max( CEILING( price_meta.meta_value ) ) as max_price FROM {$wpdb->posts} ";
		$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
		$sql .= " WHERE {$wpdb->posts}.post_type IN ('product') AND {$wpdb->posts}.post_status = 'publish' AND price_meta.meta_key IN ('_price') AND price_meta.meta_value > '' ";
		$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

		$search = \WC_Query::get_main_search_query_sql();
		
		if ($search) $sql .= ' AND ' . $search;

		$prices = $wpdb->get_row($sql); // WPCS: unprepared SQL ok.

		return [
			'min' => floor($prices->min_price),
			'max' => ceil($prices->max_price)
		];
	}
}
