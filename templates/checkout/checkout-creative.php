<?php
/**
 * Checkout template: Creative.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

$steps = HCK_Checkout::get_steps();
?>
<div class="hck-checkout hck-checkout--creative hck-checkout--layout-<?php echo esc_attr( $layout ); ?>">

	<div class="hck-hero">
		<div class="hck-hero__content">
			<h2 class="hck-hero__title"><?php esc_html_e( 'Almost there!', 'hooshyar-commerce-kit' ); ?></h2>
			<p class="hck-hero__text"><?php esc_html_e( 'Fill in your details and finish your order securely.', 'hooshyar-commerce-kit' ); ?></p>
		</div>

		<ol class="hck-steps">
			<?php foreach ( $steps as $index => $label ) : ?>
				<li class="hck-steps__item <?php echo 1 === (int) $index ? 'hck-steps__item--done' : ''; ?>">
					<span class="hck-steps__num"><?php echo esc_html( $index ); ?></span>
					<span class="hck-steps__label"><?php echo esc_html( $label ); ?></span>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>

	<?php wc_print_notices(); ?>

	<?php HCK_Helpers::template( 'checkout/_checkout-body.php', array( 'layout' => $layout, 'checkout' => $checkout ) ); ?>
</div>
