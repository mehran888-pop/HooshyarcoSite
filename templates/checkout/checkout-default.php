<?php
/**
 * Checkout template: Default.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="hck-checkout hck-checkout--default hck-checkout--layout-<?php echo esc_attr( $layout ); ?>">
	<h2 class="hck-checkout__title hck-checkout__title--simple"><?php esc_html_e( 'Checkout', 'hooshyar-commerce-kit' ); ?></h2>

	<?php wc_print_notices(); ?>

	<?php HCK_Helpers::template( 'checkout/_checkout-body.php', array( 'layout' => $layout, 'checkout' => $checkout ) ); ?>
</div>
