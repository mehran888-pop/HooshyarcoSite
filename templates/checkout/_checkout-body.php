<?php
/**
 * Shared checkout body markup.
 *
 * Available vars: $template, $layout, $checkout
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

if ( ! $checkout instanceof WC_Checkout ) {
	return;
}

$show_images = 'yes' === HCK_Helpers::get( 'checkout_show_image', 'yes' );
$sticky      = 'yes' === HCK_Helpers::get( 'checkout_order_summary_sticky', 'yes' );
$show_coupon = 'yes' === HCK_Helpers::get( 'checkout_coupon', 'yes' );
$show_steps  = 'yes' === HCK_Helpers::get( 'checkout_steps', 'yes' );
$login_note  = 'yes' === HCK_Helpers::get( 'checkout_login_note', 'yes' );

$cart = WC()->cart;
?>
<div class="hck-checkout__body">

	<?php if ( ! is_user_logged_in() && $login_note && 'modern' === $template ) : ?>
		<div class="hck-checkout__login-note">
			<span class="hck-checkout__login-icon"><?php echo HCK_Helpers::icon( 'user', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<p>
				<?php esc_html_e( 'Returning customer?', 'hooshyar-commerce-kit' ); ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Click here to log in', 'hooshyar-commerce-kit' ); ?></a>
			</p>
		</div>
	<?php endif; ?>

	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

		<div class="hck-checkout__grid hck-checkout__grid--<?php echo esc_attr( $layout ); ?>">

			<div class="hck-checkout__form">
				<?php if ( $show_coupon ) : ?>
					<div class="hck-checkout__coupon">
						<?php wc_checkout_coupon_form(); ?>
					</div>
				<?php endif; ?>

				<?php
				// Billing / shipping fields grouped into cards.
				$billing_fields = $checkout->get_checkout_fields( 'billing' );
				$ship_fields    = $checkout->get_checkout_fields( 'shipping' );
				$account_fields = $checkout->get_checkout_fields( 'account' );
				$order_fields   = $checkout->get_checkout_fields( 'order' );
				?>

				<section class="hck-fieldset">
					<h3 class="hck-fieldset__title">
						<span class="hck-fieldset__num">1</span>
						<?php esc_html_e( 'Billing details', 'hooshyar-commerce-kit' ); ?>
					</h3>
					<div class="hck-fieldset__body hck-fields">
						<?php
						foreach ( $billing_fields as $key => $field ) {
							woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
						}
						?>
					</div>
				</section>

				<?php if ( WC()->cart->needs_shipping() && ! empty( $ship_fields ) ) : ?>
					<section class="hck-fieldset">
						<h3 class="hck-fieldset__title">
							<span class="hck-fieldset__num">2</span>
							<?php esc_html_e( 'Shipping details', 'hooshyar-commerce-kit' ); ?>
						</h3>
						<div class="hck-fieldset__body hck-fields">
							<?php
							foreach ( $ship_fields as $key => $field ) {
								woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
							}
							?>
						</div>
					</section>
				<?php endif; ?>

				<?php if ( ! is_user_logged_in() && ! empty( $account_fields ) ) : ?>
					<section class="hck-fieldset">
						<h3 class="hck-fieldset__title">
							<span class="hck-fieldset__num">+</span>
							<?php esc_html_e( 'Additional information', 'hooshyar-commerce-kit' ); ?>
						</h3>
						<div class="hck-fieldset__body hck-fields">
							<?php
							foreach ( $account_fields as $key => $field ) {
								woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
							}
							?>
						</div>
					</section>
				<?php endif; ?>

				<?php if ( ! empty( $order_fields ) ) : ?>
					<section class="hck-fieldset">
						<div class="hck-fieldset__body hck-fields">
							<?php
							foreach ( $order_fields as $key => $field ) {
								woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
							}
							?>
						</div>
					</section>
				<?php endif; ?>
			</div>

			<div class="hck-checkout__summary <?php echo $sticky ? 'hck-checkout__summary--sticky' : ''; ?>">
				<div class="hck-summary-card">
					<h3 class="hck-summary-card__title"><?php esc_html_e( 'Your order', 'hooshyar-commerce-kit' ); ?></h3>

					<ul class="hck-checkout-review">
						<?php foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) : ?>
							<?php
							$product = $cart_item['data'];
							if ( ! $product instanceof WC_Product ) {
								continue;
							}
							?>
							<li class="hck-checkout-review__item">
								<?php if ( $show_images ) : ?>
									<span class="hck-checkout-review__thumb"><?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?></span>
								<?php endif; ?>
								<span class="hck-checkout-review__name">
									<?php echo esc_html( $product->get_name() ); ?>
									<em>× <?php echo esc_html( $cart_item['quantity'] ); ?></em>
								</span>
								<span class="hck-checkout-review__price"><?php echo wp_kses_post( $cart->get_product_subtotal( $product, $cart_item['quantity'] ) ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>

					<div class="hck-checkout-review__totals">
						<ul class="hck-summary-card__rows">
							<li class="hck-summary-card__row">
								<span><?php esc_html_e( 'Subtotal', 'hooshyar-commerce-kit' ); ?></span>
								<span><?php echo wp_kses_post( $cart->get_subtotal_html() ); ?></span>
							</li>
							<?php foreach ( $cart->get_coupons() as $code => $coupon ) : ?>
								<li class="hck-summary-card__row hck-summary-card__row--coupon">
									<span>
										<?php
										/* translators: %s: coupon code */
										printf( esc_html__( 'Coupon: %s', 'hooshyar-commerce-kit' ), esc_html( $code ) );
										?>
									</span>
									<span><?php echo wp_kses_post( $cart->get_coupon_discount_amount_html( $coupon ) ); ?></span>
								</li>
							<?php endforeach; ?>
							<?php foreach ( $cart->get_fees() as $fee ) : ?>
								<li class="hck-summary-card__row">
									<span><?php echo esc_html( $fee->name ); ?></span>
									<span><?php echo wp_kses_post( wc_price( $fee->amount ) ); ?></span>
								</li>
							<?php endforeach; ?>
							<?php foreach ( $cart->get_tax_totals() as $tax_total ) : ?>
								<li class="hck-summary-card__row">
									<span><?php echo esc_html( $tax_total->label ); ?></span>
									<span><?php echo wp_kses_post( $tax_total->formatted_amount ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>

						<div class="hck-summary-card__total">
							<span><?php esc_html_e( 'Total', 'hooshyar-commerce-kit' ); ?></span>
							<span class="hck-price hck-price--total"><?php echo wp_kses_post( $cart->get_total() ); ?></span>
						</div>
					</div>

					<div id="payment" class="woocommerce-checkout-payment hck-checkout__payment">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput -- core checkout output.
						echo apply_filters( 'woocommerce_checkout_order_review_html', '<div id="order_review" class="woocommerce-checkout-review-order">' . $checkout->get_order_review_html() . '</div>' );
						?>
					</div>
				</div>
			</div>
		</div>
	</form>

	<?php if ( ! is_user_logged_in() && $login_note && 'modern' !== $template ) : ?>
		<div class="hck-checkout__login-note">
			<p>
				<?php esc_html_e( 'Returning customer?', 'hooshyar-commerce-kit' ); ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Click here to log in', 'hooshyar-commerce-kit' ); ?></a>
			</p>
		</div>
	<?php endif; ?>
</div>
