<?php
/**
 * AJAX endpoints.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Ajax
 */
class HCK_Ajax {

	/**
	 * Singleton.
	 *
	 * @var HCK_Ajax|null
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * @return HCK_Ajax
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
		// Frontend actions.
		add_action( 'wp_ajax_hck_get_mini_cart', array( $this, 'get_mini_cart' ) );
		add_action( 'wp_ajax_nopriv_hck_get_mini_cart', array( $this, 'get_mini_cart' ) );

		// Admin actions.
		add_action( 'wp_ajax_hck_test_message', array( $this, 'test_message' ) );
		add_action( 'wp_ajax_hck_test_digipay', array( $this, 'test_digipay' ) );
	}

	/**
	 * Verify a nonce or bail.
	 *
	 * @param string $action Action name.
	 */
	private function verify( $action ) {
		check_ajax_referer( $action, 'nonce' );
	}

	/**
	 * Return the mini cart HTML.
	 */
	public function get_mini_cart() {
		$this->verify( 'hck_frontend' );

		ob_start();
		HCK_Cart::instance()->render_mini_cart();
		$html = ob_get_clean();

		wp_send_json_success( array( 'html' => $html ) );
	}

	/**
	 * Send a test Telegram/Bale message.
	 */
	public function test_message() {
		$this->verify( 'hck_admin' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( __( 'Permission denied.', 'hooshyar-commerce-kit' ), 403 );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified above.
		$channel = isset( $_POST['channel'] ) ? sanitize_key( wp_unslash( $_POST['channel'] ) ) : 'telegram';

		$message = "✅ Hooshyar Commerce Kit\n\n" . __( 'Test message — the connection works fine.', 'hooshyar-commerce-kit' );

		if ( 'bale' === $channel ) {
			$result = HCK_Bale::instance()->send_text( $message );
		} else {
			$result = HCK_Telegram::instance()->send_text( $message );
		}

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		wp_send_json_success( $result );
	}

	/**
	 * Test DigiPay OAuth connectivity.
	 */
	public function test_digipay() {
		$this->verify( 'hck_admin' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( __( 'Permission denied.', 'hooshyar-commerce-kit' ), 403 );
		}

		$api    = new HCK_Digipay_Api();
		$result = $api->test_connection();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		wp_send_json_success( $result );
	}
}
