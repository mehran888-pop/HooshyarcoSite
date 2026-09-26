<?php
/**
 * Plugin settings (WordPress Settings API).
 *
 * Every colour, font, size and layout option for the built-in
 * templates lives here, so the whole system is controllable from
 * the WordPress dashboard without touching code.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Settings
 */
final class HCK_Settings {

	/**
	 * Option name.
	 */
	const OPTION = 'hck_settings';

	/**
	 * Singleton.
	 *
	 * @var HCK_Settings|null
	 */
	private static $instance = null;

	/**
	 * Cached settings.
	 *
	 * @var array|null
	 */
	private static $cache = null;

	/**
	 * Instance.
	 *
	 * @return HCK_Settings
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
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 60 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
	}

	/**
	 * Get all settings merged with defaults.
	 *
	 * @return array
	 */
	public static function get_all() {
		if ( null !== self::$cache ) {
			return self::$cache;
		}

		$saved    = get_option( self::OPTION, array() );
		$saved    = is_array( $saved ) ? $saved : array();
		self::$cache = wp_parse_args( $saved, self::get_defaults() );

		return self::$cache;
	}

	/**
	 * Get one setting.
	 *
	 * @param string $key     Key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	public static function get( $key, $default = '' ) {
		$all = self::get_all();
		return isset( $all[ $key ] ) && '' !== $all[ $key ] ? $all[ $key ] : $default;
	}

	/**
	 * Update settings.
	 *
	 * @param array $values Values to merge.
	 */
	public static function update( $values ) {
		$current = get_option( self::OPTION, array() );
		$current = is_array( $current ) ? $current : array();
		update_option( self::OPTION, array_merge( $current, $values ) );
		self::$cache = null;
	}

	/**
	 * Default settings.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			// Design tokens.
			'primary_color'      => '#6c5ce7',
			'secondary_color'    => '#00cec9',
			'accent_color'       => '#fd79a8',
			'heading_color'      => '#1e1b32',
			'text_color'         => '#4b4b62',
			'muted_color'        => '#8f8fa8',
			'bg_color'           => '#ffffff',
			'surface_color'      => '#f6f5fb',
			'border_color'       => '#e6e4f2',
			'success_color'      => '#16a34a',
			'danger_color'       => '#ef4444',
			'warning_color'      => '#f59e0b',
			'border_radius'      => '14',
			'container_width'    => '1200',
			'font_body'          => '',
			'font_heading'       => '',

			// Cart.
			'cart_template'      => 'modern',
			'cart_layout'        => 'two-column',
			'cart_show_image'    => 'yes',
			'cart_show_stock'    => 'yes',
			'cart_coupon'        => 'yes',
			'cart_note'          => 'yes',
			'cart_free_ship_bar' => 'yes',
			'cart_summary_sticky' => 'yes',

			// Checkout.
			'checkout_template'      => 'modern',
			'checkout_layout'        => 'two-column',
			'checkout_show_image'    => 'yes',
			'checkout_order_summary_sticky' => 'yes',
			'checkout_coupon'        => 'yes',
			'checkout_steps'         => 'yes',
			'checkout_login_note'    => 'yes',

			// Dashboard.
			'dashboard_template'  => 'modern',
			'dashboard_layout'    => 'sidebar-right',
			'dashboard_welcome'   => 'yes',
			'dashboard_stats'     => 'yes',
			'dashboard_recent_orders' => '5',
			'dashboard_show_avatar'   => 'yes',
			'dashboard_header_button' => 'yes',

			// Shop.
			'shop_layout'        => 'catalog',
			'shop_card_style'    => 'boxed',
			'shop_columns'       => '4',
			'shop_sidebar'       => 'none',
			'shop_hover'         => 'zoom',
			'shop_show_result_count' => 'yes',
			'shop_ajax_add'      => 'yes',

			// Product.
			'product_layout'     => 'gallery-right',
			'product_sticky_info' => 'yes',
			'product_tabs_style' => 'tabs',
			'product_related'    => 'yes',
			'product_trust_badges' => 'yes',

			// Header / Footer.
			'header_template'    => 'modern',
			'header_elementor_id' => 0,
			'header_layout'      => 'inline',
			'header_sticky'      => 'yes',
			'header_user_button' => 'yes',
			'header_cart_button' => 'yes',
			'header_search'      => 'yes',
			'header_topbar'      => 'no',
			'header_phone'       => '',
			'footer_template'    => 'modern',
			'footer_elementor_id' => 0,
			'footer_columns'     => '3',
			'footer_about'       => '',
			'footer_copyright'   => '',
			'header_footer_scope' => 'entire_site',
			'header_footer_ids'  => '',

			// Mobile nav.
			'mobile_nav_enable'  => 'yes',
			'mobile_nav_style'   => 'floating',
			'mobile_nav_items'   => 'home,shop,cart,account,search',
			'mobile_nav_replace_footer' => 'yes',
			'mobile_nav_cart_count'     => 'yes',

			// Effects.
			'fly_effect'         => 'fly',
			'fly_duration'       => '850',
			'fly_toast'          => 'yes',
			'fly_cart_bump'      => 'yes',
			'fly_confetti'       => 'no',

			// Telegram.
			'telegram_enabled'   => 'no',
			'telegram_token'     => '',
			'telegram_chat_id'   => '',
			'telegram_template'  => "🛍 محصول جدید\n\n{title}\n💰 قیمت: {price}\n🔗 {link}",

			// Bale.
			'bale_enabled'       => 'no',
			'bale_token'         => '',
			'bale_chat_id'       => '',
			'bale_template'      => "🛍 محصول جدید\n\n{title}\n💰 قیمت: {price}\n🔗 {link}",

			// DigiPay.
			'digipay_environment'   => 'live',
			'digipay_username'      => '',
			'digipay_password'      => '',
			'digipay_client_id'     => '',
			'digipay_client_secret' => '',
			'digipay_preferred'     => 'auto',
			'digipay_amount_unit'   => 'rial',
			'digipay_logging'       => 'no',
		);
	}

	/**
	 * Add the admin menu pages.
	 */
	public function admin_menu() {
		add_menu_page(
			__( 'Hooshyar Kit', 'hooshyar-commerce-kit' ),
			__( 'Hooshyar Kit', 'hooshyar-commerce-kit' ),
			'manage_woocommerce',
			'hck-settings',
			array( $this, 'render_settings_page' ),
			'dashicons-cart',
			56
		);

		add_submenu_page(
			'hck-settings',
			__( 'Settings', 'hooshyar-commerce-kit' ),
			__( 'Settings', 'hooshyar-commerce-kit' ),
			'manage_woocommerce',
			'hck-settings',
			array( $this, 'render_settings_page' )
		);

		add_submenu_page(
			'hck-settings',
			__( 'Notification Log', 'hooshyar-commerce-kit' ),
			__( 'Notification Log', 'hooshyar-commerce-kit' ),
			'manage_woocommerce',
			'hck-log',
			array( $this, 'render_log_page' )
		);
	}

	/**
	 * Settings tabs.
	 *
	 * @return array
	 */
	public function get_tabs() {
		return array(
			'design'    => __( 'Design', 'hooshyar-commerce-kit' ),
			'cart'      => __( 'Cart', 'hooshyar-commerce-kit' ),
			'checkout'  => __( 'Checkout', 'hooshyar-commerce-kit' ),
			'dashboard' => __( 'Dashboard', 'hooshyar-commerce-kit' ),
			'shop'      => __( 'Shop & Product', 'hooshyar-commerce-kit' ),
			'header'    => __( 'Header & Footer', 'hooshyar-commerce-kit' ),
			'mobile'    => __( 'Mobile Nav', 'hooshyar-commerce-kit' ),
			'effects'   => __( 'Effects', 'hooshyar-commerce-kit' ),
			'telegram'  => __( 'Telegram', 'hooshyar-commerce-kit' ),
			'bale'      => __( 'Bale', 'hooshyar-commerce-kit' ),
			'digipay'   => __( 'DigiPay Gateway', 'hooshyar-commerce-kit' ),
		);
	}

	/**
	 * Register settings and fields.
	 */
	public function register_settings() {
		register_setting(
			'hck_settings_group',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
			)
		);

		foreach ( $this->get_tabs() as $tab => $label ) {
			add_settings_section(
				'hck_section_' . $tab,
				'',
				'__return_false',
				'hck-settings-' . $tab
			);

			foreach ( $this->get_fields( $tab ) as $field ) {
				add_settings_field(
					$field['id'],
					$field['label'],
					array( $this, 'render_field' ),
					'hck-settings-' . $tab,
					'hck_section_' . $tab,
					$field
				);
			}
		}
	}

	/**
	 * Field definitions per tab.
	 *
	 * @param string $tab Tab id.
	 * @return array
	 */
	public function get_fields( $tab ) {
		$yesno = array(
			'yes' => __( 'Enabled', 'hooshyar-commerce-kit' ),
			'no'  => __( 'Disabled', 'hooshyar-commerce-kit' ),
		);

		$fields = array();

		switch ( $tab ) {
			case 'design':
				$fields = array(
					array( 'id' => 'primary_color', 'label' => __( 'Primary color', 'hooshyar-commerce-kit' ), 'type' => 'color', 'desc' => __( 'Main brand colour used across all templates and elements.', 'hooshyar-commerce-kit' ) ),
					array( 'id' => 'secondary_color', 'label' => __( 'Secondary color', 'hooshyar-commerce-kit' ), 'type' => 'color' ),
					array( 'id' => 'accent_color', 'label' => __( 'Accent color', 'hooshyar-commerce-kit' ), 'type' => 'color' ),
					array( 'id' => 'heading_color', 'label' => __( 'Heading color', 'hooshyar-commerce-kit' ), 'type' => 'color' ),
					array( 'id' => 'text_color', 'label' => __( 'Text color', 'hooshyar-commerce-kit' ), 'type' => 'color' ),
					array( 'id' => 'muted_color', 'label' => __( 'Muted text color', 'hooshyar-commerce-kit' ), 'type' => 'color' ),
					array( 'id' => 'bg_color', 'label' => __( 'Background color', 'hooshyar-commerce-kit' ), 'type' => 'color' ),
					array( 'id' => 'surface_color', 'label' => __( 'Surface / card color', 'hooshyar-commerce-kit' ), 'type' => 'color' ),
					array( 'id' => 'border_color', 'label' => __( 'Border color', 'hooshyar-commerce-kit' ), 'type' => 'color' ),
					array( 'id' => 'success_color', 'label' => __( 'Success color', 'hooshyar-commerce-kit' ), 'type' => 'color' ),
					array( 'id' => 'danger_color', 'label' => __( 'Danger color', 'hooshyar-commerce-kit' ), 'type' => 'color' ),
					array( 'id' => 'warning_color', 'label' => __( 'Warning color', 'hooshyar-commerce-kit' ), 'type' => 'color' ),
					array( 'id' => 'border_radius', 'label' => __( 'Border radius (px)', 'hooshyar-commerce-kit' ), 'type' => 'number', 'min' => 0, 'max' => 40 ),
					array( 'id' => 'container_width', 'label' => __( 'Container width (px)', 'hooshyar-commerce-kit' ), 'type' => 'number', 'min' => 900, 'max' => 1800 ),
					array( 'id' => 'font_body', 'label' => __( 'Body font family', 'hooshyar-commerce-kit' ), 'type' => 'text', 'desc' => __( 'Any CSS font family, e.g. Tahoma, Vazirmatn, Roboto. Google fonts load automatically.', 'hooshyar-commerce-kit' ) ),
					array( 'id' => 'font_heading', 'label' => __( 'Heading font family', 'hooshyar-commerce-kit' ), 'type' => 'text' ),
				);
				break;

			case 'cart':
				$fields = array(
					array( 'id' => 'cart_template', 'label' => __( 'Cart template', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => self::template_options() ),
					array( 'id' => 'cart_layout', 'label' => __( 'Cart layout', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'two-column'  => __( 'Two column (items + summary)', 'hooshyar-commerce-kit' ),
						'summary-left' => __( 'Summary on the left', 'hooshyar-commerce-kit' ),
						'one-column'  => __( 'One column', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'cart_show_image', 'label' => __( 'Product images', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'cart_show_stock', 'label' => __( 'Stock hints', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'cart_coupon', 'label' => __( 'Coupon form', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'cart_note', 'label' => __( 'Order note field', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'cart_free_ship_bar', 'label' => __( 'Free shipping progress bar', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'cart_summary_sticky', 'label' => __( 'Sticky summary', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
				);
				break;

			case 'checkout':
				$fields = array(
					array( 'id' => 'checkout_template', 'label' => __( 'Checkout template', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => self::template_options() ),
					array( 'id' => 'checkout_layout', 'label' => __( 'Checkout layout', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'two-column'   => __( 'Two column (form + summary)', 'hooshyar-commerce-kit' ),
						'summary-left' => __( 'Summary on the left', 'hooshyar-commerce-kit' ),
						'one-column'   => __( 'One column', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'checkout_show_image', 'label' => __( 'Product images in summary', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'checkout_order_summary_sticky', 'label' => __( 'Sticky order summary', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'checkout_coupon', 'label' => __( 'Coupon form', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'checkout_steps', 'label' => __( 'Step indicator', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'checkout_login_note', 'label' => __( 'Returning customer notice', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
				);
				break;

			case 'dashboard':
				$fields = array(
					array( 'id' => 'dashboard_template', 'label' => __( 'Dashboard template', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => self::template_options() ),
					array( 'id' => 'dashboard_layout', 'label' => __( 'Dashboard layout', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'sidebar-right' => __( 'Sidebar right', 'hooshyar-commerce-kit' ),
						'sidebar-left'  => __( 'Sidebar left', 'hooshyar-commerce-kit' ),
						'tabs-top'      => __( 'Horizontal tabs', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'dashboard_welcome', 'label' => __( 'Welcome card', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'dashboard_stats', 'label' => __( 'Statistics cards', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'dashboard_recent_orders', 'label' => __( 'Recent orders count', 'hooshyar-commerce-kit' ), 'type' => 'number', 'min' => 0, 'max' => 20 ),
					array( 'id' => 'dashboard_show_avatar', 'label' => __( 'Show avatar', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'dashboard_header_button', 'label' => __( 'User area button in header', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno, 'desc' => __( 'Shows a synced WooCommerce account button in the header.', 'hooshyar-commerce-kit' ) ),
				);
				break;

			case 'shop':
				$fields = array(
					array( 'id' => 'shop_layout', 'label' => __( 'Shop layout', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'catalog'  => __( 'Catalog grid', 'hooshyar-commerce-kit' ),
						'modern'   => __( 'Modern grid with sidebar', 'hooshyar-commerce-kit' ),
						'creative' => __( 'Creative masonry', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'shop_card_style', 'label' => __( 'Product card style', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => self::card_options() ),
					array( 'id' => 'shop_columns', 'label' => __( 'Grid columns', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array( '2' => '2', '3' => '3', '4' => '4', '5' => '5' ) ),
					array( 'id' => 'shop_sidebar', 'label' => __( 'Shop sidebar', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'none'  => __( 'No sidebar', 'hooshyar-commerce-kit' ),
						'right' => __( 'Right', 'hooshyar-commerce-kit' ),
						'left'  => __( 'Left', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'shop_hover', 'label' => __( 'Card hover effect', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'zoom'  => __( 'Image zoom', 'hooshyar-commerce-kit' ),
						'slide' => __( 'Image slide', 'hooshyar-commerce-kit' ),
						'lift'  => __( 'Card lift', 'hooshyar-commerce-kit' ),
						'none'  => __( 'None', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'shop_show_result_count', 'label' => __( 'Result count & ordering bar', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'shop_ajax_add', 'label' => __( 'AJAX add to cart', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'product_layout', 'label' => __( 'Product page layout', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'gallery-right' => __( 'Gallery right', 'hooshyar-commerce-kit' ),
						'gallery-left'  => __( 'Gallery left', 'hooshyar-commerce-kit' ),
						'centered'      => __( 'Centered', 'hooshyar-commerce-kit' ),
						'creative'      => __( 'Creative hero', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'product_sticky_info', 'label' => __( 'Sticky product summary', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'product_tabs_style', 'label' => __( 'Tabs style', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'tabs'     => __( 'Tabs', 'hooshyar-commerce-kit' ),
						'accordion' => __( 'Accordion', 'hooshyar-commerce-kit' ),
						'modern'   => __( 'Modern underline', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'product_related', 'label' => __( 'Related products', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'product_trust_badges', 'label' => __( 'Trust badges', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
				);
				break;

			case 'header':
				$fields = array(
					array( 'id' => 'header_template', 'label' => __( 'Header template', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => self::template_options() ),
					array( 'id' => 'header_elementor_id', 'label' => __( 'Elementor header template ID', 'hooshyar-commerce-kit' ), 'type' => 'number', 'min' => 0, 'max' => 9999999, 'desc' => __( 'Optional. Leave 0 to use the built-in template above.', 'hooshyar-commerce-kit' ) ),
					array( 'id' => 'header_layout', 'label' => __( 'Header layout', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'inline'   => __( 'Inline (logo | menu | actions)', 'hooshyar-commerce-kit' ),
						'centered' => __( 'Centered (logo on top)', 'hooshyar-commerce-kit' ),
						'split'    => __( 'Split menu', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'header_sticky', 'label' => __( 'Sticky header', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'header_user_button', 'label' => __( 'User area button', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'header_cart_button', 'label' => __( 'Cart button', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'header_search', 'label' => __( 'Search form', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'header_topbar', 'label' => __( 'Top bar', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'header_phone', 'label' => __( 'Phone (top bar)', 'hooshyar-commerce-kit' ), 'type' => 'text' ),
					array( 'id' => 'footer_template', 'label' => __( 'Footer template', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => self::template_options() ),
					array( 'id' => 'footer_elementor_id', 'label' => __( 'Elementor footer template ID', 'hooshyar-commerce-kit' ), 'type' => 'number', 'min' => 0, 'max' => 9999999, 'desc' => __( 'Optional. Leave 0 to use the built-in template above.', 'hooshyar-commerce-kit' ) ),
					array( 'id' => 'footer_columns', 'label' => __( 'Footer columns', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ) ),
					array( 'id' => 'footer_about', 'label' => __( 'About text (footer)', 'hooshyar-commerce-kit' ), 'type' => 'textarea' ),
					array( 'id' => 'footer_copyright', 'label' => __( 'Copyright text', 'hooshyar-commerce-kit' ), 'type' => 'text' ),
					array( 'id' => 'header_footer_scope', 'label' => __( 'Display scope', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'entire_site'  => __( 'Entire site', 'hooshyar-commerce-kit' ),
						'shop_only'    => __( 'Shop pages only', 'hooshyar-commerce-kit' ),
						'front_only'   => __( 'Front page only', 'hooshyar-commerce-kit' ),
						'custom_ids'   => __( 'Specific page IDs', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'header_footer_ids', 'label' => __( 'Page IDs (comma separated)', 'hooshyar-commerce-kit' ), 'type' => 'text', 'desc' => __( 'Used with the "Specific page IDs" scope.', 'hooshyar-commerce-kit' ) ),
				);
				break;

			case 'mobile':
				$fields = array(
					array( 'id' => 'mobile_nav_enable', 'label' => __( 'Mobile navigation', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'mobile_nav_style', 'label' => __( 'Style', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'default'  => __( 'Solid bar', 'hooshyar-commerce-kit' ),
						'floating' => __( 'Floating pill', 'hooshyar-commerce-kit' ),
						'modern'   => __( 'Modern with center action', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'mobile_nav_items', 'label' => __( 'Items (comma separated)', 'hooshyar-commerce-kit' ), 'type' => 'text', 'desc' => __( 'Available: home, shop, cart, account, search, categories, wishlist', 'hooshyar-commerce-kit' ) ),
					array( 'id' => 'mobile_nav_replace_footer', 'label' => __( 'Replace footer on mobile', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno, 'desc' => __( 'Hides the footer navigation on mobile in favour of the mobile nav.', 'hooshyar-commerce-kit' ) ),
					array( 'id' => 'mobile_nav_cart_count', 'label' => __( 'Cart badge', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
				);
				break;

			case 'effects':
				$fields = array(
					array( 'id' => 'fly_effect', 'label' => __( 'Add to cart effect', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'fly'     => __( 'Fly to cart', 'hooshyar-commerce-kit' ),
						'arc'     => __( 'Arc to cart', 'hooshyar-commerce-kit' ),
						'zoom'    => __( 'Zoom & fade', 'hooshyar-commerce-kit' ),
						'confetti' => __( 'Confetti burst', 'hooshyar-commerce-kit' ),
						'none'    => __( 'None', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'fly_duration', 'label' => __( 'Effect duration (ms)', 'hooshyar-commerce-kit' ), 'type' => 'number', 'min' => 300, 'max' => 2500 ),
					array( 'id' => 'fly_toast', 'label' => __( 'Success toast', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'fly_cart_bump', 'label' => __( 'Cart icon bump', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'fly_confetti', 'label' => __( 'Extra confetti with any effect', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
				);
				break;

			case 'telegram':
				$fields = array(
					array( 'id' => 'telegram_enabled', 'label' => __( 'Send new products', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'telegram_token', 'label' => __( 'Bot token', 'hooshyar-commerce-kit' ), 'type' => 'text', 'desc' => __( 'Get it from @BotFather.', 'hooshyar-commerce-kit' ) ),
					array( 'id' => 'telegram_chat_id', 'label' => __( 'Channel / chat ID', 'hooshyar-commerce-kit' ), 'type' => 'text', 'desc' => __( 'e.g. @your_channel or -100xxxxxxxxxx', 'hooshyar-commerce-kit' ) ),
					array( 'id' => 'telegram_template', 'label' => __( 'Message template', 'hooshyar-commerce-kit' ), 'type' => 'textarea', 'desc' => __( 'Placeholders: {title} {price} {link} {sku} {categories} {excerpt}', 'hooshyar-commerce-kit' ) ),
				);
				break;

			case 'bale':
				$fields = array(
					array( 'id' => 'bale_enabled', 'label' => __( 'Send new products', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno ),
					array( 'id' => 'bale_token', 'label' => __( 'Bale bot token', 'hooshyar-commerce-kit' ), 'type' => 'text', 'desc' => __( 'Create a bot with @botfather in Bale.', 'hooshyar-commerce-kit' ) ),
					array( 'id' => 'bale_chat_id', 'label' => __( 'Channel / chat ID', 'hooshyar-commerce-kit' ), 'type' => 'text' ),
					array( 'id' => 'bale_template', 'label' => __( 'Message template', 'hooshyar-commerce-kit' ), 'type' => 'textarea', 'desc' => __( 'Placeholders: {title} {price} {link} {sku} {categories} {excerpt}', 'hooshyar-commerce-kit' ) ),
				);
				break;

			case 'digipay':
				$fields = array(
					array( 'id' => 'digipay_environment', 'label' => __( 'Environment', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'live' => __( 'Live', 'hooshyar-commerce-kit' ),
						'uat'  => __( 'Staging (UAT)', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'digipay_username', 'label' => __( 'Username', 'hooshyar-commerce-kit' ), 'type' => 'text' ),
					array( 'id' => 'digipay_password', 'label' => __( 'Password', 'hooshyar-commerce-kit' ), 'type' => 'password' ),
					array( 'id' => 'digipay_client_id', 'label' => __( 'Client ID', 'hooshyar-commerce-kit' ), 'type' => 'text' ),
					array( 'id' => 'digipay_client_secret', 'label' => __( 'Client secret', 'hooshyar-commerce-kit' ), 'type' => 'password' ),
					array( 'id' => 'digipay_preferred', 'label' => __( 'Preferred payment tool', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'auto'   => __( 'DigiPay selection screen', 'hooshyar-commerce-kit' ),
						'ipg'    => __( 'Direct: card gateway (IPG)', 'hooshyar-commerce-kit' ),
						'wallet' => __( 'Direct: wallet', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'digipay_amount_unit', 'label' => __( 'Store currency unit', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => array(
						'rial'  => __( 'Rial (send as-is)', 'hooshyar-commerce-kit' ),
						'toman' => __( 'Toman (multiply by 10)', 'hooshyar-commerce-kit' ),
					) ),
					array( 'id' => 'digipay_logging', 'label' => __( 'Debug logging', 'hooshyar-commerce-kit' ), 'type' => 'select', 'options' => $yesno, 'desc' => __( 'Logs go to WooCommerce → Status → Logs.', 'hooshyar-commerce-kit' ) ),
				);
				break;
		}

		return $fields;
	}

	/**
	 * Template select options.
	 *
	 * @return array
	 */
	public static function template_options() {
		return array(
			'default'  => __( 'Default', 'hooshyar-commerce-kit' ),
			'modern'   => __( 'Modern', 'hooshyar-commerce-kit' ),
			'minimal'  => __( 'Minimal', 'hooshyar-commerce-kit' ),
			'creative' => __( 'Creative', 'hooshyar-commerce-kit' ),
		);
	}

	/**
	 * Card select options.
	 *
	 * @return array
	 */
	public static function card_options() {
		return array(
			'boxed'   => __( 'Boxed', 'hooshyar-commerce-kit' ),
			'minimal' => __( 'Minimal', 'hooshyar-commerce-kit' ),
			'overlay' => __( 'Overlay', 'hooshyar-commerce-kit' ),
			'bordered' => __( 'Bordered', 'hooshyar-commerce-kit' ),
		);
	}

	/**
	 * Render a field.
	 *
	 * @param array $field Field config.
	 */
	public function render_field( $field ) {
		$settings = self::get_all();
		$id       = $field['id'];
		$value    = isset( $settings[ $id ] ) ? $settings[ $id ] : '';
		$name     = self::OPTION . '[' . $id . ']';

		echo '<div class="hck-field">';

		switch ( $field['type'] ) {
			case 'color':
				printf(
					'<input type="text" class="hck-color-picker" id="%1$s" name="%2$s" value="%3$s" data-default="%4$s" />',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_attr( isset( self::get_defaults()[ $id ] ) ? self::get_defaults()[ $id ] : '#000000' )
				);
				break;

			case 'select':
				printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
				foreach ( $field['options'] as $opt_value => $opt_label ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $opt_value ),
						selected( $value, $opt_value, false ),
						esc_html( $opt_label )
					);
				}
				echo '</select>';
				break;

			case 'textarea':
				printf(
					'<textarea id="%1$s" name="%2$s" rows="5" class="large-text">%3$s</textarea>',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_textarea( $value )
				);
				break;

			case 'number':
				printf(
					'<input type="number" id="%1$s" name="%2$s" value="%3$s" min="%4$s" max="%5$s" class="small-text" />',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_attr( isset( $field['min'] ) ? $field['min'] : 0 ),
					esc_attr( isset( $field['max'] ) ? $field['max'] : 9999 )
				);
				break;

			case 'password':
				printf(
					'<input type="password" id="%1$s" name="%2$s" value="%3$s" class="regular-text" autocomplete="off" />',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value )
				);
				break;

			case 'text':
			default:
				printf(
					'<input type="text" id="%1$s" name="%2$s" value="%3$s" class="regular-text" />',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value )
				);
				break;
		}

		if ( ! empty( $field['desc'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['desc'] ) );
		}

		echo '</div>';
	}

	/**
	 * Sanitize all settings on save.
	 *
	 * Fields that are not present in the submitted form (e.g. when saving a
	 * single tab) keep their previously saved values.
	 *
	 * @param array $input Raw input.
	 * @return array
	 */
	public function sanitize( $input ) {
		$clean    = array();
		$defaults = self::get_defaults();
		$input    = is_array( $input ) ? $input : array();
		$existing = get_option( self::OPTION, array() );
		$existing = is_array( $existing ) ? $existing : array();

		foreach ( $this->get_tabs() as $tab => $label ) {
			foreach ( $this->get_fields( $tab ) as $field ) {
				$id = $field['id'];

				if ( array_key_exists( $id, $input ) ) {
					$value = $input[ $id ];
				} elseif ( array_key_exists( $id, $existing ) ) {
					$value = $existing[ $id ];
				} else {
					$value = isset( $defaults[ $id ] ) ? $defaults[ $id ] : '';
				}

				switch ( $field['type'] ) {
					case 'color':
						$clean[ $id ] = HCK_Helpers::sanitize_hex( $value );
						if ( '' === $clean[ $id ] && isset( $defaults[ $id ] ) ) {
							$clean[ $id ] = $defaults[ $id ];
						}
						break;

					case 'select':
						$clean[ $id ] = array_key_exists( $value, $field['options'] ) ? $value : ( isset( $defaults[ $id ] ) ? $defaults[ $id ] : '' );
						break;

					case 'number':
						$min = isset( $field['min'] ) ? (int) $field['min'] : 0;
						$max = isset( $field['max'] ) ? (int) $field['max'] : PHP_INT_MAX;
						$clean[ $id ] = max( $min, min( $max, (int) $value ) );
						break;

					case 'textarea':
						$clean[ $id ] = wp_kses_post( $value );
						break;

					default:
						$clean[ $id ] = sanitize_text_field( $value );
						break;
				}
			}
		}

		self::$cache = null;

		return $clean;
	}

	/**
	 * Admin styles/scripts for the settings screen.
	 *
	 * @param string $hook Current hook.
	 */
	public function admin_assets( $hook ) {
		if ( false === strpos( $hook, 'hck' ) ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'hck-admin', HCK_PLUGIN_URL . 'assets/css/admin.css', array( 'wp-color-picker' ), HCK_VERSION );
		wp_enqueue_script( 'hck-admin', HCK_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), HCK_VERSION, true );
		wp_localize_script(
			'hck-admin',
			'hckAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'hck_admin' ),
				'i18n'    => array(
					'testSending'  => __( 'Sending test message…', 'hooshyar-commerce-kit' ),
					'testSuccess'  => __( 'Test message sent successfully.', 'hooshyar-commerce-kit' ),
					'testFail'     => __( 'Sending failed. Check the log below.', 'hooshyar-commerce-kit' ),
					'confirmReset' => __( 'Are you sure?', 'hooshyar-commerce-kit' ),
				),
			)
		);
	}

	/**
	 * Render the settings page.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$tabs = $this->get_tabs();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$active = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'design';
		if ( ! isset( $tabs[ $active ] ) ) {
			$active = 'design';
		}
		?>
		<div class="wrap hck-settings-wrap">
			<h1 class="hck-settings-title">
				<span class="hck-logo-mark"></span>
				<?php esc_html_e( 'Hooshyar Commerce Kit — Settings', 'hooshyar-commerce-kit' ); ?>
			</h1>

			<nav class="nav-tab-wrapper hck-tabs">
				<?php foreach ( $tabs as $tab => $label ) : ?>
					<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'hck-settings', 'tab' => $tab ), admin_url( 'admin.php' ) ) ); ?>"
						class="nav-tab <?php echo $active === $tab ? 'nav-tab-active' : ''; ?>">
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<form method="post" action="options.php" class="hck-settings-form">
				<?php
				settings_fields( 'hck_settings_group' );
				?>
				<input type="hidden" name="hck_active_tab" value="<?php echo esc_attr( $active ); ?>" />
				<table class="form-table" role="presentation">
					<?php
					foreach ( $this->get_fields( $active ) as $field ) {
						printf(
							'<tr class="hck-row-%1$s"><th scope="row"><label for="%1$s">%2$s</label></th><td>',
							esc_attr( $field['id'] ),
							esc_html( $field['label'] )
						);
						$this->render_field( $field );
						echo '</td></tr>';
					}
					?>
				</table>

				<?php submit_button( __( 'Save settings', 'hooshyar-commerce-kit' ) ); ?>
			</form>

			<?php if ( 'telegram' === $active || 'bale' === $active ) : ?>
				<div class="hck-test-panel">
					<h2><?php esc_html_e( 'Send a test message', 'hooshyar-commerce-kit' ); ?></h2>
					<p>
						<button type="button" class="button button-secondary hck-test-message" data-channel="<?php echo esc_attr( $active ); ?>">
							<?php
							/* translators: %s: channel name */
							printf( esc_html__( 'Send test to %s', 'hooshyar-commerce-kit' ), esc_html( ucfirst( $active ) ) );
							?>
						</button>
					</p>
					<pre class="hck-test-result"></pre>
				</div>
			<?php endif; ?>

			<?php if ( 'digipay' === $active ) : ?>
				<div class="hck-test-panel">
					<h2><?php esc_html_e( 'Connection test', 'hooshyar-commerce-kit' ); ?></h2>
					<p class="description">
						<?php esc_html_e( 'Tests the OAuth login against the DigiPay UPG API using the credentials above.', 'hooshyar-commerce-kit' ); ?>
					</p>
					<p>
						<button type="button" class="button button-secondary hck-test-digipay">
							<?php esc_html_e( 'Test DigiPay connection', 'hooshyar-commerce-kit' ); ?>
						</button>
					</p>
					<pre class="hck-test-result"></pre>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Notification log page.
	 */
	public function render_log_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		global $wpdb;
		$table  = $wpdb->prefix . 'hck_notification_log';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$rows   = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY id DESC LIMIT 200" );
		?>
		<div class="wrap hck-settings-wrap">
			<h1><?php esc_html_e( 'Notification Log', 'hooshyar-commerce-kit' ); ?></h1>
			<p class="description"><?php esc_html_e( 'Last 200 Telegram / Bale notifications.', 'hooshyar-commerce-kit' ); ?></p>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'ID', 'hooshyar-commerce-kit' ); ?></th>
						<th><?php esc_html_e( 'Channel', 'hooshyar-commerce-kit' ); ?></th>
						<th><?php esc_html_e( 'Product', 'hooshyar-commerce-kit' ); ?></th>
						<th><?php esc_html_e( 'Status', 'hooshyar-commerce-kit' ); ?></th>
						<th><?php esc_html_e( 'Response', 'hooshyar-commerce-kit' ); ?></th>
						<th><?php esc_html_e( 'Date', 'hooshyar-commerce-kit' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $rows ) ) : ?>
						<tr><td colspan="6"><?php esc_html_e( 'No notifications yet.', 'hooshyar-commerce-kit' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $rows as $row ) : ?>
							<tr>
								<td><?php echo esc_html( $row->id ); ?></td>
								<td><?php echo esc_html( $row->channel ); ?></td>
								<td>
									<?php
									$product = get_post( $row->product_id );
									if ( $product ) {
										echo esc_html( $product->post_title );
									} else {
										echo esc_html( $row->product_id );
									}
									?>
								</td>
								<td><?php echo esc_html( $row->status ); ?></td>
								<td><code><?php echo esc_html( HCK_Helpers::str_limit( (string) $row->response, 160 ) ); ?></code></td>
								<td><?php echo esc_html( $row->created_at ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
