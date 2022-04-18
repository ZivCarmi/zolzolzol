<?php namespace app\classes\plugins\woocommerce;

defined('ABSPATH') || exit;

use app\classes\helpers\Debug;

class Filters {
	static public function init () {
		// dequeue default woocommerce styles
		add_filter('woocommerce_enqueue_styles', [__CLASS__, 'bt_dequeue_woocommerce_styles']);
		
		// update minicart on any cart action (add to cart/remove, etc...)
		add_filter('woocommerce_add_to_cart_fragments', [__CLASS__, 'update_cart_fragments']);

		// add html classes to cart item
		add_filter('woocommerce_cart_item_class', [__CLASS__, 'add_classes_to_cart_item'], 10, 2);
		add_filter('woocommerce_order_item_class', [__CLASS__, 'add_classes_to_cart_item'], 10, 2);
		add_filter('woocommerce_admin_html_order_item_class', [__CLASS__, 'add_classes_to_cart_item'], 10, 2);
		
		// add html classes to product
		add_filter('post_class', [__CLASS__, 'add_classes_to_product'], 10, 3);
		
		// add html classes to body
		add_filter('body_class', [__CLASS__, 'add_classes_to_body']);

		// Show trailing zeros on prices.
		// add_filter( 'woocommerce_price_trim_zeros', [__CLASS__, 'wc_hide_trailing_zeros'], 10, 1);

		add_filter('woocommerce_product_get_image', [__CLASS__, 'bt_product_main_image_as_company_image'], 10, 2);
	}

	static public function update_cart_fragments ($fragments) {
		$fragments['div.cart-count'] = shortcodes::minicart_count_template();

		return $fragments;
	}

	static public function bt_dequeue_woocommerce_styles ($enqueued_styles) {
		if (!is_admin()) {
			unset($enqueued_styles['woocommerce-general']);
			unset($enqueued_styles['woocommerce-layout']);
			unset($enqueued_styles['woocommerce-smallscreen']);
		}

		return $enqueued_styles;
	}

	// add html classes to cart item
	static public function add_classes_to_cart_item ($class_name, $cart_item) {
		$product_id = $cart_item['product_id'];
		
		if (WOO_PRODUCT_BRAND_EXISTS) {
			// get brand from products
			if (!empty($brands = get_the_terms($product_id, WOO_PRODUCT_BRAND_SLUG))) $class_name .=  ' ' . WOO_CLASS_BRAND_PREFIX . urldecode($brands[0]->name);
		}
		
		// add product sku
		if (!empty($sku = get_post_meta($product_id, '_sku', true))) $class_name .=  ' product_sku-' . $sku;
		
		// add product variations
		if (!empty($cart_item['variation']) || (method_exists($cart_item, 'get_variation_id') && !empty($variation_id = $cart_item->get_variation_id()))) {
			if (!empty($variation_id)) {
				$variation = wc_get_product($variation_id);
				
				foreach ($variation->get_attributes() as $attribute_name => $variation) {
					$class_name .= ' product_attribute_' . $attribute_name . '_' . $variation;
				}
			} else {
				foreach ($cart_item['variation'] as $attribute_name => $variation) {
					$class_name .= ' product_' . $attribute_name . '_' . $variation;
				}
			}
		}
		
		// add product categories (product_cat)
		if (!empty($categories = get_the_terms($product_id, 'product_cat'))) {
			foreach ($categories as $category) {
				$class_name .= ' product_cat-' . $category->slug;
			}
		}
		
		return $class_name;
	}
	
	// add html classes to product
	static public function add_classes_to_product ($post_classes, $class, $product_id) {
		if (get_post_type($product_id) !== 'product') return $post_classes;

		if (WOO_PRODUCT_BRAND_EXISTS) {
			// get brand from products
			if (!empty($brands = get_the_terms($product_id, WOO_PRODUCT_BRAND_SLUG))) $post_classes[] = WOO_CLASS_BRAND_PREFIX . urldecode($brands[0]->name);
		}
		
		// add product sku
		if (!empty($sku = get_post_meta($product_id, '_sku', true))) $post_classes[] = 'product_sku-' . $sku;

		return $post_classes;
	}
	
	// add html classes to body
	static public function add_classes_to_body ($classes) {
		$woo_class_prefix = 'woo-list-name_';
		
		$is_product_category = is_product_category();
		$is_product_tag 	 = is_product_tag();
		
		if (is_shop()) $classes[] = $woo_class_prefix . 'shop';
		elseif ($is_product_category || $is_product_tag) {
			if ($is_product_category) $woo_class_prefix .= 'category-';
			elseif ($is_product_tag) $woo_class_prefix .= 'tag-';
			
			// get current category
			$category = get_queried_object();
			
			// check if has ancestors
			switch ($category->parent) {
				case 0: $classes[] = $woo_class_prefix . $category->slug; break;
				default:
					// get ancestors of category (parents)
					$ancestors = get_ancestors($category->term_id, $category->taxonomy);
					
					foreach (array_reverse($ancestors) as $ancestor_id) {
						// get ancestor object (parent)
						$ancestor = get_term_by('id', $ancestor_id, $category->taxonomy);
						
						$woo_class_prefix .= $ancestor->slug . '-';
					}
					
					$classes[] = $woo_class_prefix . $category->slug;
			}
		}
		
		return $classes;
	}

	static public function wc_hide_trailing_zeros ($trim) {
		return true;
	}

	static public function bt_product_main_image_as_company_image ($image, $product) {
		// if product doesn't have thumbnail, get it's company image
		if (!$product->get_image_id()) {
			if (!$company = get_the_terms($product->get_id(), 'companies')) return;

			$company_logo_id = get_term_meta($company[0]->term_id, 'company_logo', true);

			$image = wp_get_attachment_image($company_logo_id, 'medium');
		}

		return $image;
	}
}
