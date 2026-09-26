<?php
/**
 * Professional checkout module.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Checkout
 */
class HCK_Checkout {

	/**
	 * Singleton.
	 *
	 * @var HCK_Checkout|null
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * @return HCK_Checkout
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
		add_shortcode( 'hck_checkout', array( $this, 'shortcode' ) );
	}

	/**
	 * Shortcode [hck_checkout template="modern" layout="two-column"].
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'template' => HCK_Helpers::get( 'checkout_template', 'modern' ),
				'layout'   => HCK_Helpers::get( 'checkout_layout', 'two-column' ),
			),
			$atts,
			'hck_checkout'
		);

		return $this->render( $atts['template'], $atts['layout'], true );
	}

	/**
	 * Render checkout.
	 *
	 * @param string $template Template name.
	 * @param string $layout   Layout name.
	 * @param bool   $return   Return string.
	 * @return string|void
	 */
	public function render( $template = '', $layout = '', $return = false ) {
		if ( ! function_exists( 'WC' ) || ! WC()->checkout ) {
			return $return ? '<p class="hck-notice hck-notice--error">' . esc_html__( 'WooCommerce is not available.', 'hooshyar-commerce-kit' ) . '</p>' : '';
		}

		$template = $template ? sanitize_file_name( $template ) : HCK_Template_Loader::get_template_name( 'checkout', 'checkout_template' );
		$layout   = $layout ? sanitize_file_name( $layout ) : sanitize_file_name( HCK_Helpers::get( 'checkout_layout', 'two-column' ) );

		$file = HCK_PLUGIN_DIR . 'templates/checkout/checkout-' . $template . '.php';
		if ( ! is_readable( $file ) ) {
			$file     = HCK_PLUGIN_DIR . 'templates/checkout/checkout-modern.php';
			$template = 'modern';
		}

		$args = array(
			'template' => $template,
			'layout'   => $layout,
			'checkout' => WC()->checkout(),
		);

		return HCK_Helpers::template( 'checkout/checkout-' . $template . '.php', $args, $return );
	}

	/**
	 * Steps for the creative template.
	 *
	 * @return array
	 */
	public static function get_steps() {
		$steps = array(
			1 => __( 'Billing details', 'hooshyar-commerce-kit' ),
			2 => __( 'Payment', 'hooshyar-commerce-kit' ),
			3 => __( 'Confirmation', 'hooshyar-commerce-kit' ),
		);

		return apply_filters( 'hck_checkout_steps', $steps );
	}
}
