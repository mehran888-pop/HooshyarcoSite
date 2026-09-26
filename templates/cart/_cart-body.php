<?php
/**
 * Shared cart body markup.
 *
 * Available vars: $template, $layout, $cart
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

$cart_items   = $cart->get_cart();
$show_image   = 'yes' === HCK_Helpers::get( 'cart_show_image', 'yes' );
$show_stock   = 'yes' === HCK_Helpers::get( 'cart_show_stock', 'yes' );
$show_coupon  = 'yes' === HCK_Helpers::get( 'cart_coupon', 'yes' );
$show_note    = 'yes' === HCK_Helpers::get( 'cart_note', 'yes' );
$sticky       = 'yes' === HCK_Helpers::get( 'cart_summary_sticky', 'yes' );
?>
<div class="hck-cart__body">

	<?php if ( empty( $cart_items ) ) : ?>

		<div class="hck-empty">
			<div class="hck-empty__icon"><?php echo HCK_Helpers::icon( 'cart', array( 'size' => 56 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<h3 class="hck-empty__title"><?php esc_html_e( 'Your cart is empty', 'hooshyar-commerce-kit' ); ?></h3>
			<p class="hck-empty__text"><?php esc_html_e( 'Looks like you have not added anything to your cart yet.', 'hooshyar-commerce-kit' ); ?></p>
			<a class="hck-btn hck-btn--primary" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Continue shopping', 'hooshyar-commerce-kit' ); ?>
			</a>
		</div>

	<?php else : ?>

		<form class="hck-cart__form woocommerce-cart-form" action="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '' ); ?>" method="post">

			<div class="hck-cart__grid hck-cart__grid--<?php echo esc_attr( $layout ); ?>">

				<div class="hck-cart__items">
					<?php echo HCK_Cart::free_shipping_bar(); // phpcs:ignore WordPress.Security.EscapeOutput ?>

					<?php foreach ( $cart_items as $cart_item_key => $cart_item ) : ?>
						<?php
						$product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

						if ( ! $product instanceof WC_Product || ! $product->exists() ) {
							continue;
						}

						$product_permalink = apply_filters(
							'woocommerce_cart_item_permalink',
							$product->is_visible() ? $product->get_permalink( $cart_item ) : '',
							$cart_item,
							$cart_item_key
						);
						?>
						<div class="hck-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'hck-cart-item wc-cart-item', $cart_item, $cart_item_key ) ); ?>"
							data-key="<?php echo esc_attr( $cart_item_key ); ?>">

							<?php if ( $show_image ) : ?>
								<a class="hck-cart-item__image" href="<?php echo esc_url( $product_permalink ); ?>">
									<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
								</a>
							<?php endif; ?>

							<div class="hck-cart-item__info">
								<a class="hck-cart-item__title" href="<?php echo esc_url( $product_permalink ); ?>">
									<?php echo wp_kses_post( $product->get_name() ); ?>
								</a>

								<?php if ( $product->get_sku() && $show_stock ) : ?>
									<span class="hck-cart-item__sku"><?php esc_html_e( 'SKU:', 'hooshyar-commerce-kit' ); ?> <?php echo esc_html( $product->get_sku() ); ?></span>
								<?php endif; ?>

								<?php
								// Variation data.
								echo wp_kses_post( wc_get_formatted_cart_item_data( $cart_item ) );

								if ( $show_stock ) {
									$availability = $product->get_availability();
									if ( ! empty( $availability['availability'] ) ) {
										printf(
											'<span class="hck-cart-item__stock %s">%s</span>',
											esc_attr( $product->is_in_stock() ? 'in' : 'out' ),
											esc_html( $availability['availability'] )
										);
									}
								}
								?>

								<div class="hck-cart-item__meta">
									<div class="hck-qty">
										<button type="button" class="hck-qty__btn hck-qty__btn--minus" data-hck-qty="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'hooshyar-commerce-kit' ); ?>">−</button>
										<input
											type="number"
											class="hck-qty__input input-text qty text"
											name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]"
											value="<?php echo esc_attr( $cart_item['quantity'] ); ?>"
											min="0"
											step="1"
											data-hck-qty-input
										/>
										<button type="button" class="hck-qty__btn hck-qty__btn--plus" data-hck-qty="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'hooshyar-commerce-kit' ); ?>">+</button>
									</div>

									<?php
									echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a href="%s" class="hck-cart-item__remove" data-hck-remove-item aria-label="%s">%s</a>',
											esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
											esc_attr__( 'Remove this item', 'hooshyar-commerce-kit' ),
											HCK_Helpers::icon( 'trash', array( 'size' => 18 ) )
										),
										$cart_item_key
									);
									?>
								</div>
							</div>

							<div class="hck-cart-item__price">
								<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $product ), $cart_item, $cart_item_key ) ); ?>
							</div>
						</div>
					<?php endforeach; ?>

					<div class="hck-cart__actions">
						<?php if ( $show_coupon ) : ?>
							<div class="hck-coupon">
								<input type="text" name="coupon_code" class="hck-coupon__input" placeholder="<?php esc_attr_e( 'Coupon code', 'hooshyar-commerce-kit' ); ?>" />
								<button type="submit" class="hck-btn hck-btn--ghost" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'hooshyar-commerce-kit' ); ?>">
									<?php esc_html_e( 'Apply coupon', 'hooshyar-commerce-kit' ); ?>
								</button>
							</div>
						<?php endif; ?>

						<?php if ( $show_note ) : ?>
							<div class="hck-cart__note">
								<textarea name="order_comments" class="hck-cart__note-input" placeholder="<?php esc_attr_e( 'Order note (optional)', 'hooshyar-commerce-kit' ); ?>"></textarea>
							</div>
						<?php endif; ?>

						<button type="submit" class="hck-btn hck-btn--ghost hck-cart__update" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'hooshyar-commerce-kit' ); ?>">
							<?php esc_html_e( 'Update cart', 'hooshyar-commerce-kit' ); ?>
						</button>

						<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
					</div>
				</div>

				<div class="hck-cart__summary <?php echo $sticky ? 'hck-cart__summary--sticky' : ''; ?>">
					<div class="hck-summary-card">
						<h3 class="hck-summary-card__title"><?php esc_html_e( 'Cart totals', 'hooshyar-commerce-kit' ); ?></h3>

						<ul class="hck-summary-card__rows">
							<li class="hck-summary-card__row">
								<span><?php esc_html_e( 'Subtotal', 'hooshyar-commerce-kit' ); ?></span>
								<span><?php echo wp_kses_post( WC()->cart->get_subtotal_html() ); ?></span>
							</li>

							<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
								<li class="hck-summary-card__row hck-summary-card__row--coupon">
									<span>
										<?php
										/* translators: %s: coupon code */
										printf( esc_html__( 'Coupon: %s', 'hooshyar-commerce-kit' ), esc_html( $code ) );
										?>
									</span>
									<span><?php echo wp_kses_post( WC()->cart->get_coupon_discount_amount_html( $coupon ) ); ?></span>
								</li>
							<?php endforeach; ?>

							<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
								<li class="hck-summary-card__row">
									<span><?php echo esc_html( $fee->name ); ?></span>
									<span><?php echo wp_kses_post( wc_price( $fee->amount ) ); ?></span>
								</li>
							<?php endforeach; ?>

							<?php if ( WC()->cart->needs_shipping() && function_exists( 'wc_cart_totals_shipping_html' ) ) : ?>
								<li class="hck-summary-card__row">
									<span><?php esc_html_e( 'Shipping', 'hooshyar-commerce-kit' ); ?></span>
									<span><?php wc_cart_totals_shipping_html(); ?></span>
								</li>
							<?php endif; ?>

							<?php foreach ( WC()->cart->get_tax_totals() as $tax_total ) : ?>
								<li class="hck-summary-card__row">
									<span><?php echo esc_html( $tax_total->label ); ?></span>
									<span><?php echo wp_kses_post( $tax_total->formatted_amount ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>

						<div class="hck-summary-card__total">
							<span><?php esc_html_e( 'Total', 'hooshyar-commerce-kit' ); ?></span>
							<span class="hck-price hck-price--total"><?php echo wp_kses_post( WC()->cart->get_total() ); ?></span>
						</div>

						<a href="<?php echo esc_url( function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '#' ); ?>" class="hck-btn hck-btn--primary hck-btn--block hck-btn--lg">
							<?php esc_html_e( 'Proceed to checkout', 'hooshyar-commerce-kit' ); ?>
							<?php echo HCK_Helpers::icon( 'arrow-l', array( 'size' => 18, 'class' => 'hck-icon hck-icon--flip' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</a>

						<a class="hck-summary-card__continue" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">
							<?php esc_html_e( '← Continue shopping', 'hooshyar-commerce-kit' ); ?>
						</a>

						<div class="hck-summary-card__trust">
							<span><?php echo HCK_Helpers::icon( 'check', array( 'size' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Secure checkout', 'hooshyar-commerce-kit' ); ?></span>
							<span><?php echo HCK_Helpers::icon( 'truck', array( 'size' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Fast delivery', 'hooshyar-commerce-kit' ); ?></span>
						</div>
					</div>
				</div>
			</div>
		</form>

	<?php endif; ?>
</div>
