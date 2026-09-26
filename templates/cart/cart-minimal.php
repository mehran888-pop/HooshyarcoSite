<?php
/**
 * Cart template: Minimal.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="hck-cart hck-cart--minimal hck-cart--layout-<?php echo esc_attr( $layout ); ?>">
	<?php wc_print_notices(); ?>

	<?php HCK_Helpers::template( 'cart/_cart-body.php', array( 'layout' => $layout, 'cart' => $cart ) ); ?>
</div>
