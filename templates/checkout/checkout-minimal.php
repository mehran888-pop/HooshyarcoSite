<?php
/**
 * Checkout template: Minimal.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="hck-checkout hck-checkout--minimal hck-checkout--layout-<?php echo esc_attr( $layout ); ?>">
	<?php wc_print_notices(); ?>

	<?php HCK_Helpers::template( 'checkout/_checkout-body.php', array( 'layout' => $layout, 'checkout' => $checkout ) ); ?>
</div>
