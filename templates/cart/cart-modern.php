<?php
/**
 * Cart template: Modern.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="hck-cart hck-cart--modern hck-cart--layout-<?php echo esc_attr( $layout ); ?>">
	<header class="hck-cart__header">
		<h2 class="hck-cart__title"><?php esc_html_e( 'Shopping cart', 'hooshyar-commerce-kit' ); ?></h2>
		<p class="hck-cart__subtitle"><?php esc_html_e( 'Review your items and continue to checkout', 'hooshyar-commerce-kit' ); ?></p>
	</header>

	<?php wc_print_notices(); ?>

	<?php HCK_Helpers::template( 'cart/_cart-body.php', array( 'layout' => $layout, 'cart' => $cart ) ); ?>
</div>
