<?php
/**
 * Professional cart module.
 *
 * Renders the cart through selectable templates (default / modern /
 * minimal / creative) and layouts, fully styled with CSS variables
 * driven by the plugin settings and the Elementor widget controls.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Cart
 */
class HCK_Cart {

	/**
	 * Singleton.
	 *
	 * @var HCK_Cart|null
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * @return HCK_Cart
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_shortcode( 'hck_cart', array( $this, 'shortcode' ) );
		add_shortcode( 'hck_mini_cart', array( $this, 'shortcode_mini_cart' ) );

		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'cart_fragments' ) );

		// Slide-out cart drawer.
		add_action( 'wp_footer', array( $this, 'render_drawer' ), 5 );
	}

	/**
	 * Shortcode [hck_cart template="modern" layout="two-column"].
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'template' => HCK_Helpers::get( 'cart_template', 'modern' ),
				'layout'   => HCK_Helpers::get( 'cart_layout', 'two-column' ),
			),
			$atts,
			'hck_cart'
		);

		return $this->render( $atts['template'], $atts['layout'], true );
	}

	/**
	 * Shortcode [hck_mini_cart].
	 *
	 * @return string
	 */
	public function shortcode_mini_cart() {
		return $this->render_mini_cart( true );
	}

	/**
	 * Render the cart page.
	 *
	 * @param string $template Template name.
	 * @param string $layout   Layout name.
	 * @param bool   $return   Return string.
	 * @return string|void
	 */
	public function render( $template = '', $layout = '', $return = false ) {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return $return ? '<p class="hck-notice hck-notice--error">' . esc_html__( 'WooCommerce is not available.', 'hooshyar-commerce-kit' ) . '</p>' : '';
		}

		$template = $template ? sanitize_file_name( $template ) : HCK_Template_Loader::get_template_name( 'cart', 'cart_template' );
		$layout   = $layout ? sanitize_file_name( $layout ) : sanitize_file_name( HCK_Helpers::get( 'cart_layout', 'two-column' ) );

		$file = HCK_PLUGIN_DIR . 'templates/cart/cart-' . $template . '.php';
		if ( ! is_readable( $file ) ) {
			$file    = HCK_PLUGIN_DIR . 'templates/cart/cart-modern.php';
			$template = 'modern';
		}

		$args = array(
			'template' => $template,
			'layout'   => $layout,
			'cart'     => WC()->cart,
		);

		return HCK_Helpers::template( 'cart/cart-' . $template . '.php', $args, $return );
	}

	/**
	 * Render the mini cart (drawer body / widget).
	 *
	 * @param bool $return Return string.
	 * @return string|void
	 */
	public function render_mini_cart( $return = false ) {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return $return ? '' : null;
		}

		return HCK_Helpers::template( 'cart/mini-cart.php', array( 'cart' => WC()->cart ), $return );
	}

	/**
	 * Cart count fragments.
	 *
	 * @param array $fragments Fragments.
	 * @return array
	 */
	public function cart_fragments( $fragments ) {
		$fragments['span.hck-cart-count'] = '<span class="hck-cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
		return $fragments;
	}

	/**
	 * Slide-out cart drawer.
	 */
	public function render_drawer() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return;
		}
		?>
		<div class="hck-cart-drawer" id="hck-cart-drawer" aria-hidden="true">
			<div class="hck-cart-drawer__overlay" data-hck-close-cart></div>
			<aside class="hck-cart-drawer__panel" role="dialog" aria-label="<?php esc_attr_e( 'Shopping cart', 'hooshyar-commerce-kit' ); ?>">
				<header class="hck-cart-drawer__head">
					<h3><?php esc_html_e( 'Shopping cart', 'hooshyar-commerce-kit' ); ?></h3>
					<button type="button" class="hck-cart-drawer__close" data-hck-close-cart aria-label="<?php esc_attr_e( 'Close', 'hooshyar-commerce-kit' ); ?>">
						<?php echo HCK_Helpers::icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</button>
				</header>
				<div class="hck-cart-drawer__body" data-hck-mini-cart>
					<?php $this->render_mini_cart(); ?>
				</div>
			</aside>
		</div>
		<?php
	}

	/**
	 * Shared free-shipping progress bar markup.
	 *
	 * @return string
	 */
	public static function free_shipping_bar() {
		if ( 'yes' !== HCK_Helpers::get( 'cart_free_ship_bar', 'yes' ) || ! function_exists( 'WC' ) ) {
			return '';
		}

		$min_amount = 0;
		if ( function_exists( 'wc_get_price_decimals' ) ) {
			$min_amount = (float) get_option( 'woocommerce_free_shipping_min_amount', 0 );
		}

		if ( $min_amount <= 0 || ! WC()->cart ) {
			return '';
		}

		$total    = (float) WC()->cart->get_subtotal();
		$progress = min( 100, round( ( $total / $min_amount ) * 100 ) );
		$remaining = max( 0, $min_amount - $total );

		$html  = '<div class="hck-ship-bar" data-progress="' . esc_attr( $progress ) . '">';
		$html .= '<div class="hck-ship-bar__head">';
		if ( $remaining > 0 ) {
			$html .= '<span class="hck-ship-bar__icon">' . HCK_Helpers::icon( 'truck', array( 'size' => 18 ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
			$html .= '<span class="hck-ship-bar__text">';
			/* translators: %s: amount left for free shipping */
			$html .= sprintf( esc_html__( 'Add %s more to get free shipping', 'hooshyar-commerce-kit' ), HCK_Helpers::format_price( $remaining ) );
			$html .= '</span>';
		} else {
			$html .= '<span class="hck-ship-bar__icon">' . HCK_Helpers::icon( 'check', array( 'size' => 18 ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
			$html .= '<span class="hck-ship-bar__text">' . esc_html__( 'Your order qualifies for free shipping!', 'hooshyar-commerce-kit' ) . '</span>';
		}
		$html .= '</div>';
		$html .= '<div class="hck-ship-bar__track"><div class="hck-ship-bar__fill" style="width:' . esc_attr( $progress ) . '%"></div></div>';
		$html .= '</div>';

		return $html;
	}
}
