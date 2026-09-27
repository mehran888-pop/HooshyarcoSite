<?php
/**
 * Plugin Name:       Hooshyar Commerce Kit
 * Plugin URI:        https://hooshyarco.com/
 * Description:       کیت حرفه‌ای فروشگاهی ووکامرس — سبد خرید، صورتحساب، داشبورد کاربری، المان‌های اختصاصی المنتور، هدر/فوتر، افکت‌های خلاقانه، اتصال تلگرام و بله و درگاه دیجی‌پی.
 * Version:           1.0.7
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Hooshyarco
 * Author URI:        https://hooshyarco.com/
 * Text Domain:       hooshyar-commerce-kit
 * Domain Path:       /languages
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * WC requires at least: 8.0
 * WC tested up to:      11.1
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

define( 'HCK_VERSION', '1.0.7' );
define( 'HCK_PLUGIN_FILE', __FILE__ );
define( 'HCK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HCK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HCK_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'HCK_MIN_WC', '8.0.0' );
define( 'HCK_MIN_PHP', '7.4' );

/**
 * Map of HCK classes to their files (relative to /includes).
 *
 * @return array
 */
function hck_class_map() {
	return array(
		'HCK'                        => 'class-hck.php',
		'HCK_Activator'              => 'class-hck-activator.php',
		'HCK_Deactivator'            => 'class-hck-deactivator.php',
		'HCK_Assets'                 => 'class-hck-assets.php',
		'HCK_Settings'               => 'class-hck-settings.php',
		'HCK_Helpers'                => 'class-hck-helpers.php',
		'HCK_Lang_Fa'                => 'class-hck-lang-fa.php',
		'HCK_Ajax'                   => 'class-hck-ajax.php',
		'HCK_Notices'                => 'class-hck-notices.php',
		'HCK_Template_Loader'        => 'templates/class-hck-template-loader.php',
		'HCK_Cart'                   => 'templates/class-hck-cart.php',
		'HCK_Checkout'               => 'templates/class-hck-checkout.php',
		'HCK_Dashboard'              => 'templates/class-hck-dashboard.php',
		'HCK_Shop'                   => 'templates/class-hck-shop.php',
		'HCK_Product'                => 'templates/class-hck-product.php',
		'HCK_Header_Footer'          => 'templates/class-hck-header-footer.php',
		'HCK_Mobile_Nav'             => 'templates/class-hck-mobile-nav.php',
		'HCK_Elementor'              => 'elementor/class-hck-elementor.php',
		'HCK_Widget_Base'            => 'elementor/class-hck-widget-base.php',
		'HCK_Widget_Products'        => 'elementor/widgets/class-hck-widget-products.php',
		'HCK_Widget_Banner'          => 'elementor/widgets/class-hck-widget-banner.php',
		'HCK_Widget_Product_Banner'  => 'elementor/widgets/class-hck-widget-product-banner.php',
		'HCK_Widget_Cart'            => 'elementor/widgets/class-hck-widget-cart.php',
		'HCK_Widget_Checkout'        => 'elementor/widgets/class-hck-widget-checkout.php',
		'HCK_Widget_Dashboard'       => 'elementor/widgets/class-hck-widget-dashboard.php',
		'HCK_Widget_User_Area'       => 'elementor/widgets/class-hck-widget-user-area.php',
		'HCK_Widget_Mobile_Nav'      => 'elementor/widgets/class-hck-widget-mobile-nav.php',
		'HCK_Widget_Category_Menu'   => 'elementor/widgets/class-hck-widget-category-menu.php',
		'HCK_Digipay_Api'            => 'payments/class-hck-digipay-api.php',
		'HCK_Digipay_Gateway'        => 'payments/class-hck-digipay-gateway.php',
		'HCK_Social_Notifier'        => 'social/class-hck-social-notifier.php',
		'HCK_Telegram'               => 'social/class-hck-telegram.php',
		'HCK_Bale'                   => 'social/class-hck-bale.php',
	);
}

/**
 * Classes that could not be loaded (used for diagnostics).
 *
 * @var array
 */
$GLOBALS['hck_missing_classes'] = array();

/**
 * Autoloader for HCK classes.
 *
 * @param string $class Class name.
 */
function hck_autoload( $class ) {
	if ( 0 !== strpos( $class, 'HCK' ) ) {
		return;
	}

	$map = hck_class_map();

	if ( ! isset( $map[ $class ] ) ) {
		return;
	}

	$candidates = array(
		HCK_PLUGIN_DIR . 'includes/' . $map[ $class ],
		// Legacy location (class files once lived in /templates).
		HCK_PLUGIN_DIR . $map[ $class ],
	);

	foreach ( $candidates as $file ) {
		if ( is_readable( $file ) ) {
			require_once $file;
			return;
		}
	}

	$GLOBALS['hck_missing_classes'][] = $class . ' (' . $map[ $class ] . ')';
}
spl_autoload_register( 'hck_autoload' );

/**
 * Verify that all class files exist before booting the plugin.
 *
 * @return array List of missing files (empty when everything is present).
 */
function hck_verify_files() {
	$missing = array();

	foreach ( hck_class_map() as $class => $relative ) {
		if ( ! is_readable( HCK_PLUGIN_DIR . 'includes/' . $relative ) && ! is_readable( HCK_PLUGIN_DIR . $relative ) ) {
			$missing[] = 'includes/' . $relative;
		}
	}

	return $missing;
}

/**
 * Show an admin error notice when requirements are missing.
 *
 * @param string $message Message to display.
 */
function hck_requirement_error( $message ) {
	add_action(
		'admin_notices',
		function () use ( $message ) {
			printf( '<div class="notice notice-error"><p><strong>Hooshyar Commerce Kit:</strong> %s</p></div>', esc_html( $message ) );
		}
	);
}

/* -------------------------------------------------------------------------
 * Environment checks
 * ---------------------------------------------------------------------- */
if ( version_compare( PHP_VERSION, HCK_MIN_PHP, '<' ) ) {
	hck_requirement_error( sprintf( 'PHP %s+ is required. You are running %s.', HCK_MIN_PHP, PHP_VERSION ) );
	return;
}

/**
 * Main bootstrap: waits for WooCommerce, then boots the plugin.
 */
function hck_bootstrap() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		hck_requirement_error( __( 'WooCommerce is required. Please install and activate WooCommerce 8.0 or newer.', 'hooshyar-commerce-kit' ) );
		return;
	}

	if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, HCK_MIN_WC, '<' ) ) {
		hck_requirement_error( sprintf( 'WooCommerce %s+ is required.', HCK_MIN_WC ) );
		return;
	}

	// Preflight: never let a partial upload crash the site.
	$missing_files = hck_verify_files();
	if ( ! empty( $missing_files ) ) {
		hck_requirement_error(
			sprintf(
				/* translators: %s: file list */
				__( 'Plugin files are incomplete (%s). Please delete the "hooshyar-commerce-kit" folder and upload the plugin again — all files and folders must be uploaded.', 'hooshyar-commerce-kit' ),
				implode( ', ', $missing_files )
			)
		);
		return;
	}

	try {
		HCK::instance();
	} catch ( \Throwable $e ) {
		hck_requirement_error(
			sprintf(
				/* translators: %s: error message */
				__( 'Hooshyar Commerce Kit could not start: %s. Please re-upload the plugin files.', 'hooshyar-commerce-kit' ),
				$e->getMessage()
			)
		);
	}
}
add_action( 'plugins_loaded', 'hck_bootstrap', 20 );

/* -------------------------------------------------------------------------
 * Activation / deactivation
 * ---------------------------------------------------------------------- */
register_activation_hook( __FILE__, array( 'HCK_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'HCK_Deactivator', 'deactivate' ) );

/**
 * Declare compatibility with WooCommerce features (HPOS / cart-checkout blocks).
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', HCK_PLUGIN_FILE, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', HCK_PLUGIN_FILE, true );
		}
	}
);

/**
 * Render the "توضیحات افزونه" style action links on the plugins screen.
 *
 * @param array  $links Plugin action links.
 * @param string $file  Plugin basename.
 * @return array
 */
function hck_plugin_action_links( $links, $file ) {
	if ( HCK_PLUGIN_BASENAME === $file ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=hck-settings' ) ),
			esc_html__( 'Settings', 'hooshyar-commerce-kit' )
		);
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_filter( 'plugin_action_links_' . HCK_PLUGIN_BASENAME, 'hck_plugin_action_links', 10, 2 );

/**
 * Register the plugin row meta links.
 *
 * @param array  $meta  Row meta.
 * @param string $file  Plugin basename.
 * @return array
 */
function hck_plugin_row_meta( $meta, $file ) {
	if ( HCK_PLUGIN_BASENAME === $file ) {
		$meta[] = sprintf(
			'<a href="%s" target="_blank" rel="noopener">%s</a>',
			esc_url( 'https://hooshyarco.com/docs' ),
			esc_html__( 'Documentation', 'hooshyar-commerce-kit' )
		);
		$meta[] = sprintf(
			'<a href="%s" target="_blank" rel="noopener">%s</a>',
			esc_url( 'https://hooshyarco.com/support' ),
			esc_html__( 'Support', 'hooshyar-commerce-kit' )
		);
	}
	return $meta;
}
add_filter( 'plugin_row_meta', 'hck_plugin_row_meta', 10, 2 );
