<?php
/**
 * Checkout template: Modern.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="hck-checkout hck-checkout--modern hck-checkout--layout-<?php echo esc_attr( $layout ); ?>">
	<header class="hck-checkout__header">
		<h2 class="hck-checkout__title"><?php esc_html_e( 'Checkout', 'hooshyar-commerce-kit' ); ?></h2>
		<p class="hck-checkout__subtitle"><?php esc_html_e( 'Complete your order in a few simple steps', 'hooshyar-commerce-kit' ); ?></p>
	</header>

	<?php wc_print_notices(); ?>

	<?php HCK_Helpers::template( 'checkout/_checkout-body.php', array( 'layout' => $layout, 'checkout' => $checkout ) ); ?>
</div>
