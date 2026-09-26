<?php
/**
 * Mini cart (drawer / widget body).
 *
 * Available vars: $cart
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

$items = $cart->get_cart();
?>
<div class="hck-mini-cart">
	<?php if ( empty( $items ) ) : ?>
		<div class="hck-mini-cart__empty">
			<div class="hck-empty__icon"><?php echo HCK_Helpers::icon( 'cart', array( 'size' => 42 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<p><?php esc_html_e( 'Your cart is empty', 'hooshyar-commerce-kit' ); ?></p>
		</div>
	<?php else : ?>
		<ul class="hck-mini-cart__items">
			<?php foreach ( $items as $cart_item_key => $cart_item ) : ?>
				<?php
				$product = $cart_item['data'];
				if ( ! $product instanceof WC_Product || ! $product->exists() ) {
					continue;
				}
				?>
				<li class="hck-mini-cart__item" data-key="<?php echo esc_attr( $cart_item_key ); ?>">
					<a class="hck-mini-cart__thumb" href="<?php echo esc_url( $product->get_permalink() ); ?>">
						<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
					</a>
					<div class="hck-mini-cart__info">
						<a class="hck-mini-cart__title" href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<?php echo esc_html( $product->get_name() ); ?>
						</a>
						<span class="hck-mini-cart__qty"><?php echo esc_html( $cart_item['quantity'] ); ?> ×</span>
						<span class="hck-mini-cart__price"><?php echo wp_kses_post( WC()->cart->get_product_price( $product ) ); ?></span>
					</div>
					<a class="hck-mini-cart__remove" href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" aria-label="<?php esc_attr_e( 'Remove', 'hooshyar-commerce-kit' ); ?>">
						<?php echo HCK_Helpers::icon( 'close', array( 'size' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="hck-mini-cart__foot">
			<div class="hck-mini-cart__total">
				<span><?php esc_html_e( 'Subtotal', 'hooshyar-commerce-kit' ); ?></span>
				<span class="hck-price"><?php echo wp_kses_post( $cart->get_subtotal_html() ); ?></span>
			</div>
			<div class="hck-mini-cart__buttons">
				<a class="hck-btn hck-btn--ghost hck-btn--block" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
					<?php esc_html_e( 'View cart', 'hooshyar-commerce-kit' ); ?>
				</a>
				<a class="hck-btn hck-btn--primary hck-btn--block" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
					<?php esc_html_e( 'Checkout', 'hooshyar-commerce-kit' ); ?>
				</a>
			</div>
		</div>
	<?php endif; ?>
</div>
