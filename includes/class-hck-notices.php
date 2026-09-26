<?php
/**
 * Admin notices.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Notices
 */
class HCK_Notices {

	/**
	 * Singleton.
	 *
	 * @var HCK_Notices|null
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * @return HCK_Notices
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
		add_action( 'admin_notices', array( $this, 'render' ) );
	}

	/**
	 * Render pending notices.
	 */
	public function render() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['page'] ) && 'hck-settings' === $_GET['page'] && isset( $_GET['settings-updated'] ) ) {
			printf(
				'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
				esc_html__( 'Hooshyar Commerce Kit settings saved.', 'hooshyar-commerce-kit' )
			);
		}

		if ( ! class_exists( 'Elementor\Plugin' ) ) {
			printf(
				'<div class="notice notice-info is-dismissible"><p>%s <a href="%s">%s</a></p></div>',
				esc_html__( 'Hooshyar Commerce Kit works best with Elementor. Install it to unlock the dedicated widgets.', 'hooshyar-commerce-kit' ),
				esc_url( 'https://wordpress.org/plugins/elementor/' ),
				esc_html__( 'Get Elementor', 'hooshyar-commerce-kit' )
			);
		}
	}
}
