<?php
/**
 * Cart template: Default.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="hck-cart hck-cart--default hck-cart--layout-<?php echo esc_attr( $layout ); ?>">
	<h2 class="hck-cart__title hck-cart__title--simple"><?php esc_html_e( 'Cart', 'hooshyar-commerce-kit' ); ?></h2>

	<?php wc_print_notices(); ?>

	<?php HCK_Helpers::template( 'cart/_cart-body.php', array( 'layout' => $layout, 'cart' => $cart ) ); ?>
</div>
