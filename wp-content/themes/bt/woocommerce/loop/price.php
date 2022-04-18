<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
?>

<?php if ($product_price = $product->get_price()) : $exploded_price = explode('.', $product_price); ?>
	<span class="price">
        <span class="woocommerce-Price-amount amount">
            <bdi>
                <span class="woocommerce-Price-currencySymbol"><?= get_woocommerce_currency_symbol(); ?></span>
                <span class="main-price"><?= $exploded_price[0] ?></span>
                <?= !empty($exploded_price[1]) ? '<span class="agorot">' . $exploded_price[1] . '</span>' : ''; ?>
            </bdi>
        </span>
    </span>
<?php endif; ?>