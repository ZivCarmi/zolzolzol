<?php namespace app\classes\theme;

defined('ABSPATH') || exit;

use Timber;
use app\classes\helpers\Debug;

class Ajax {	
	static public function init () {
		add_action('wp_ajax_bt_get_compared_packages', [__CLASS__, 'bt_get_compared_packages']);
		add_action('wp_ajax_nopriv_bt_get_compared_packages', [__CLASS__, 'bt_get_compared_packages']);
	}

	static public function bt_get_compared_packages () {
		check_ajax_referer('bt_site_ajax_nonce', 'security');

		$packages = (array) json_decode(stripslashes($_POST['packages']));

		if (empty($packages)) die;

		echo '<ul class="all-compares">';

		foreach ($packages as $cat => $package) {
			echo '<li class="packages-list">';

			Timber::render('partials/heading-with-toggle.twig', [
				'tag' => 'h2',
				'name' => $cat,
			]);

			woocommerce_product_loop_start();

			foreach ($package as $package_id) {
				if (!is_numeric($package_id)) continue;
	
				$package = get_post($package_id);
	
				setup_postdata($GLOBALS['post'] =& $package);
				
				wc_get_template_part('content', 'product');
			}

			woocommerce_product_loop_end();

			echo '</li>';
		}

		echo '</ul>';
		die;
	}
}
