<?php
/**
 * Product loop card (overrides WooCommerce content-product.php).
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
	return;
}

// Keep WooCommerce loop counters intact.
wc_set_loop_prop( 'loop', wc_get_loop_prop( 'loop' ) + 1 );
?>
<li <?php wc_product_class( 'hck-product-item', $product ); ?>>
	<?php
	echo HCK_Product::render_card( // phpcs:ignore WordPress.Security.EscapeOutput
		$product,
		array(
			'card_style' => HCK_Helpers::get( 'shop_card_style', 'boxed' ),
			'hover'      => HCK_Helpers::get( 'shop_hover', 'zoom' ),
		)
	);
	?>
</li>
