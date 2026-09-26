<?php
/**
 * Main plugin orchestrator.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK
 */
final class HCK {

	/**
	 * Singleton instance.
	 *
	 * @var HCK|null
	 */
	private static $instance = null;

	/**
	 * Plugin modules.
	 *
	 * @var array
	 */
	public $modules = array();

	/**
	 * Get singleton instance.
	 *
	 * @return HCK
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
		$this->init_hooks();
	}

	/**
	 * Hook everything.
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'load_textdomain' ), 0 );

		// Core services.
		HCK_Settings::instance();
		HCK_Assets::instance();
		HCK_Ajax::instance();
		HCK_Notices::instance();

		// Template / feature modules.
		$this->modules['cart']           = HCK_Cart::instance();
		$this->modules['checkout']       = HCK_Checkout::instance();
		$this->modules['dashboard']      = HCK_Dashboard::instance();
		$this->modules['shop']           = HCK_Shop::instance();
		$this->modules['product']        = HCK_Product::instance();
		$this->modules['header_footer']  = HCK_Header_Footer::instance();
		$this->modules['mobile_nav']     = HCK_Mobile_Nav::instance();
		$this->modules['social']         = HCK_Social_Notifier::instance();

		// Elementor.
		add_action( 'elementor/loaded', array( $this, 'init_elementor' ) );

		// Payment gateways.
		add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );

		// Template lookup for our Woo overrides.
		add_filter( 'wc_get_template', array( $this, 'filter_wc_template' ), 10, 5 );
	}

	/**
	 * Load translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'hooshyar-commerce-kit', false, dirname( HCK_PLUGIN_BASENAME ) . '/languages' );
	}

	/**
	 * Boot Elementor integration.
	 */
	public function init_elementor() {
		HCK_Elementor::instance();
	}

	/**
	 * Register DigiPay gateway.
	 *
	 * @param array $gateways Gateways.
	 * @return array
	 */
	public function register_gateway( $gateways ) {
		$gateways[] = 'HCK_Digipay_Gateway';
		return $gateways;
	}

	/**
	 * Allow plugin templates to override WooCommerce core templates.
	 *
	 * @param string $template      Template path.
	 * @param string $template_name Template name.
	 * @param string $template_path Template path arg.
	 * @param string $default_path  Default path.
	 * @return string
	 */
	public function filter_wc_template( $template, $template_name, $template_path, $default_path ) {
		$overrides = array(
			'content-product.php' => 'woocommerce/content-product.php',
		);

		if ( isset( $overrides[ $template_name ] ) ) {
			$local = HCK_PLUGIN_DIR . 'templates/' . $overrides[ $template_name ];
			if ( is_readable( $local ) ) {
				return $local;
			}
		}

		return $template;
	}

	/**
	 * Get the global settings array.
	 *
	 * @return array
	 */
	public static function get_settings() {
		return HCK_Settings::get_all();
	}

	/**
	 * Get a single setting.
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	public static function setting( $key, $default = '' ) {
		return HCK_Settings::get( $key, $default );
	}
}
