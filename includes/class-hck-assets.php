<?php
/**
 * Frontend / shared asset registration.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Assets
 */
class HCK_Assets {

	/**
	 * Singleton.
	 *
	 * @var HCK_Assets|null
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * @return HCK_Assets
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 20 );
		add_action( 'wp_head', array( 'HCK_Helpers', 'print_custom_fonts_css' ), 4 );
		add_action( 'wp_head', array( 'HCK_Helpers', 'print_css_variables' ), 5 );
	}

	/**
	 * Enqueue frontend assets.
	 */
	public function enqueue() {
		// Core styles.
		wp_enqueue_style( 'hck-frontend', HCK_PLUGIN_URL . 'assets/css/frontend.css', array(), HCK_VERSION );
		wp_enqueue_style( 'hck-templates', HCK_PLUGIN_URL . 'assets/css/templates.css', array( 'hck-frontend' ), HCK_VERSION );
		wp_enqueue_style( 'hck-widgets', HCK_PLUGIN_URL . 'assets/css/widgets.css', array( 'hck-frontend' ), HCK_VERSION );
		wp_enqueue_style( 'hck-mobile-nav', HCK_PLUGIN_URL . 'assets/css/mobile-nav.css', array( 'hck-frontend' ), HCK_VERSION );

		// RTL is handled natively by CSS logical properties; add a small fix layer.
		if ( is_rtl() ) {
			wp_enqueue_style( 'hck-rtl', HCK_PLUGIN_URL . 'assets/css/rtl.css', array( 'hck-frontend' ), HCK_VERSION );
		}

		// Core scripts.
		wp_enqueue_script(
			'hck-frontend',
			HCK_PLUGIN_URL . 'assets/js/frontend.js',
			array(),
			HCK_VERSION,
			true
		);

		wp_enqueue_script(
			'hck-widgets',
			HCK_PLUGIN_URL . 'assets/js/widgets.js',
			array( 'hck-frontend' ),
			HCK_VERSION,
			true
		);

		$settings = HCK_Helpers::design_tokens();

		wp_localize_script(
			'hck-frontend',
			'hckData',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'hck_frontend' ),
				'cartUrl'      => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '',
				'checkoutUrl'  => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '',
				'accountUrl'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : '',
				'shopUrl'      => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
				'homeUrl'      => home_url( '/' ),
				'loggedIn'     => is_user_logged_in(),
				'effects'      => array(
					'fly'        => HCK_Helpers::get( 'fly_effect', 'fly' ),
					'duration'   => (int) HCK_Helpers::get( 'fly_duration', 850 ),
					'toast'      => HCK_Helpers::get( 'fly_toast', 'yes' ) === 'yes',
					'cartBump'   => HCK_Helpers::get( 'fly_cart_bump', 'yes' ) === 'yes',
					'confetti'   => HCK_Helpers::get( 'fly_confetti', 'no' ) === 'yes',
				),
				'i18n'         => array(
					'added'          => __( 'Product added to cart', 'hooshyar-commerce-kit' ),
					'viewCart'       => __( 'View cart', 'hooshyar-commerce-kit' ),
					'error'          => __( 'Something went wrong. Please try again.', 'hooshyar-commerce-kit' ),
					'loading'        => __( 'Loading…', 'hooshyar-commerce-kit' ),
					'emptyCart'      => __( 'Your cart is empty', 'hooshyar-commerce-kit' ),
					'copied'         => __( 'Copied!', 'hooshyar-commerce-kit' ),
				),
				'currency'     => array(
					'symbol' => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '',
				),
				'design'       => $settings,
			)
		);

		// Mobile nav script.
		if ( 'yes' === HCK_Helpers::get( 'mobile_nav_enable', 'yes' ) ) {
			wp_enqueue_script( 'hck-mobile-nav', HCK_PLUGIN_URL . 'assets/js/mobile-nav.js', array( 'hck-frontend' ), HCK_VERSION, true );
		}
	}

	/**
	 * Get registered style handles (used by Elementor previews).
	 *
	 * @return array
	 */
	public static function get_style_handles() {
		return array( 'hck-frontend', 'hck-templates', 'hck-widgets', 'hck-mobile-nav' );
	}
}
