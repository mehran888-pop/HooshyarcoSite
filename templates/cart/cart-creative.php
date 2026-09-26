<?php
/**
 * Cart template: Creative.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

$steps = array(
	1 => array(
		'label' => __( 'Cart', 'hooshyar-commerce-kit' ),
		'done'  => true,
	),
	2 => array(
		'label' => __( 'Checkout', 'hooshyar-commerce-kit' ),
		'done'  => false,
	),
	3 => array(
		'label' => __( 'Done', 'hooshyar-commerce-kit' ),
		'done'  => false,
	),
);
?>
<div class="hck-cart hck-cart--creative hck-cart--layout-<?php echo esc_attr( $layout ); ?>">

	<div class="hck-hero">
		<div class="hck-hero__content">
			<h2 class="hck-hero__title"><?php esc_html_e( 'Your shopping bag', 'hooshyar-commerce-kit' ); ?></h2>
			<p class="hck-hero__text"><?php esc_html_e( 'Almost there — review your bag and proceed to payment.', 'hooshyar-commerce-kit' ); ?></p>
		</div>

		<ol class="hck-steps">
			<?php foreach ( $steps as $index => $step ) : ?>
				<li class="hck-steps__item <?php echo $step['done'] ? 'hck-steps__item--done' : ''; ?>">
					<span class="hck-steps__num"><?php echo esc_html( $index ); ?></span>
					<span class="hck-steps__label"><?php echo esc_html( $step['label'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>

	<?php wc_print_notices(); ?>

	<?php HCK_Helpers::template( 'cart/_cart-body.php', array( 'layout' => $layout, 'cart' => $cart ) ); ?>
</div>
