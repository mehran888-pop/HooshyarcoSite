<?php
/**
 * DigiPay payment gateway for WooCommerce.
 *
 * Flow:
 *  1. process_payment()      → create ticket → redirect to redirectUrl
 *  2. DigiPay POSTs result   → wc-api callback → verify → complete order
 *  3. process_refund()       → manual reverse
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Digipay_Gateway
 */
class HCK_Digipay_Gateway extends WC_Payment_Gateway {

	/**
	 * API client.
	 *
	 * @var HCK_Digipay_Api
	 */
	protected $api;

	/**
	 * Environment.
	 *
	 * @var string
	 */
	public $environment;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                 = 'hck_digipay';
		$this->icon               = HCK_PLUGIN_URL . 'assets/images/digipay.svg';
		$this->has_fields         = false;
		$this->method_title       = __( 'DigiPay (UPG)', 'hooshyar-commerce-kit' );
		$this->method_description = __( 'DigiPay unified payment gateway: card gateway, wallet and BNPL credit. Docs: mydigipay.com/developers/docs/upg', 'hooshyar-commerce-kit' );
		$this->supports           = array( 'products', 'refunds' );

		$this->init_form_fields();
		$this->init_settings();

		$this->title       = $this->get_option( 'title', __( 'DigiPay', 'hooshyar-commerce-kit' ) );
		$this->description = $this->get_option( 'description', __( 'Pay securely with DigiPay — card, wallet or credit.', 'hooshyar-commerce-kit' ) );
		$this->enabled     = $this->get_option( 'enabled', 'no' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_api_' . $this->id, array( $this, 'handle_callback' ) );
		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );

		$this->api          = new HCK_Digipay_Api();
		$this->environment  = $this->get_option( 'environment', 'live' );
	}

	/**
	 * Admin form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'        => array(
				'title'   => __( 'Enable/Disable', 'hooshyar-commerce-kit' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable DigiPay gateway', 'hooshyar-commerce-kit' ),
				'default' => 'no',
			),
			'title'          => array(
				'title'       => __( 'Title', 'hooshyar-commerce-kit' ),
				'type'        => 'text',
				'description' => __( 'Payment method title shown on checkout.', 'hooshyar-commerce-kit' ),
				'default'     => __( 'DigiPay', 'hooshyar-commerce-kit' ),
				'desc_tip'    => true,
			),
			'description'    => array(
				'title'       => __( 'Description', 'hooshyar-commerce-kit' ),
				'type'        => 'textarea',
				'default'     => __( 'Pay securely with DigiPay — card, wallet or credit.', 'hooshyar-commerce-kit' ),
				'desc_tip'    => true,
			),
			'environment'    => array(
				'title'   => __( 'Environment', 'hooshyar-commerce-kit' ),
				'type'    => 'select',
				'default' => 'live',
				'options' => array(
					'live' => __( 'Live', 'hooshyar-commerce-kit' ),
					'uat'  => __( 'Staging (UAT)', 'hooshyar-commerce-kit' ),
				),
			),
			'username'       => array(
				'title'       => __( 'Username', 'hooshyar-commerce-kit' ),
				'type'        => 'text',
				'description' => __( 'Provided by DigiPay merchant panel.', 'hooshyar-commerce-kit' ),
				'default'     => '',
			),
			'password'       => array(
				'title'       => __( 'Password', 'hooshyar-commerce-kit' ),
				'type'        => 'password',
				'default'     => '',
			),
			'client_id'      => array(
				'title'       => __( 'Client ID', 'hooshyar-commerce-kit' ),
				'type'        => 'text',
				'default'     => '',
			),
			'client_secret'  => array(
				'title'       => __( 'Client secret', 'hooshyar-commerce-kit' ),
				'type'        => 'password',
				'default'     => '',
			),
			'preferred'      => array(
				'title'       => __( 'Preferred payment tool', 'hooshyar-commerce-kit' ),
				'type'        => 'select',
				'default'     => 'auto',
				'options'     => array(
					'auto'   => __( 'DigiPay selection screen', 'hooshyar-commerce-kit' ),
					'ipg'    => __( 'Direct: card gateway (IPG)', 'hooshyar-commerce-kit' ),
					'wallet' => __( 'Direct: wallet', 'hooshyar-commerce-kit' ),
				),
				'description' => __( 'Optionally skip the DigiPay tool-selection screen.', 'hooshyar-commerce-kit' ),
			),
			'amount_unit'    => array(
				'title'       => __( 'Store currency unit', 'hooshyar-commerce-kit' ),
				'type'        => 'select',
				'default'     => 'rial',
				'options'     => array(
					'rial'  => __( 'Rial (send as-is)', 'hooshyar-commerce-kit' ),
					'toman' => __( 'Toman (multiply by 10)', 'hooshyar-commerce-kit' ),
				),
			),
			'logging'        => array(
				'title'   => __( 'Debug logging', 'hooshyar-commerce-kit' ),
				'type'    => 'checkbox',
				'label'   => __( 'Log gateway events to WooCommerce → Status → Logs', 'hooshyar-commerce-kit' ),
				'default' => 'no',
			),
		);
	}

	/**
	 * Convert the order total to the unit DigiPay expects (Rials).
	 *
	 * @param WC_Order $order Order.
	 * @return int
	 */
	protected function get_order_amount( $order ) {
		$amount = (float) $order->get_total();

		if ( 'toman' === $this->get_option( 'amount_unit', 'rial' ) ) {
			$amount = $amount * 10;
		}

		return (int) round( $amount );
	}

	/**
	 * Unique provider id for this purchase attempt.
	 *
	 * @param WC_Order $order Order.
	 * @return string
	 */
	protected function get_provider_id( $order ) {
		return (string) $order->get_id();
	}

	/**
	 * Process the payment.
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			wc_add_notice( __( 'Order not found.', 'hooshyar-commerce-kit' ), 'error' );
			return array( 'result' => 'failure' );
		}

		// Sync gateway settings with the shared API options when empty.
		$this->sync_shared_settings();

		if ( ! $this->api->is_configured() ) {
			wc_add_notice( __( 'DigiPay is not configured. Please contact the store administrator.', 'hooshyar-commerce-kit' ), 'error' );
			return array( 'result' => 'failure' );
		}

		$phone = $order->get_billing_phone();
		if ( ! $phone ) {
			$phone = get_user_meta( $order->get_customer_id(), 'billing_phone', true );
		}
		$phone = preg_replace( '/[^0-9+]/', '', (string) $phone );

		if ( '' === $phone ) {
			wc_add_notice( __( 'A phone number is required to pay with DigiPay.', 'hooshyar-commerce-kit' ), 'error' );
			return array( 'result' => 'failure' );
		}

		$provider_id = $this->get_provider_id( $order );
		$amount      = $this->get_order_amount( $order );
		$callback    = WC()->api_request_url( $this->id );

		$args = array(
			'amount'      => $amount,
			'cellNumber'  => $phone,
			'providerId'  => $provider_id,
			'callbackUrl' => $callback,
		);

		// Preferred tool selection (additionalInfo.preferredGateway).
		$preferred = $this->get_option( 'preferred', 'auto' );
		if ( 'ipg' === $preferred ) {
			$args['additionalInfo'] = array( 'preferredGateway' => 2 );
		} elseif ( 'wallet' === $preferred ) {
			$args['additionalInfo'] = array( 'preferredGateway' => 0 );
		}

		/**
		 * Filter the DigiPay ticket request payload.
		 *
		 * @param array    $args  Payload.
		 * @param WC_Order $order Order.
		 */
		$args = apply_filters( 'hck_digipay_ticket_args', $args, $order );

		$response = $this->api->create_ticket( $args );

		if ( is_wp_error( $response ) ) {
			wc_add_notice(
				sprintf(
					/* translators: %s: error message */
					esc_html__( 'DigiPay error: %s', 'hooshyar-commerce-kit' ),
					$response->get_error_message()
				),
				'error'
			);
			return array( 'result' => 'failure' );
		}

		$order->update_meta_data( '_hck_digipay_provider_id', $provider_id );
		$order->update_meta_data( '_hck_digipay_ticket', $response['ticket'] );
		$order->update_meta_data( '_hck_digipay_amount', $amount );
		$order->update_status( 'on-hold', __( 'Awaiting DigiPay payment.', 'hooshyar-commerce-kit' ) );
		$order->save();

		// Save order items to session (standard WC practice before redirect).
		if ( WC()->session ) {
			WC()->session->set( 'order_awaiting_payment', $order_id );
		}

		return array(
			'result'   => 'success',
			'redirect' => $response['redirectUrl'],
		);
	}

	/**
	 * Use the shared plugin settings when the gateway fields are empty.
	 */
	protected function sync_shared_settings() {
		$map = array(
			'environment'    => 'digipay_environment',
			'username'       => 'digipay_username',
			'password'       => 'digipay_password',
			'client_id'      => 'digipay_client_id',
			'client_secret'  => 'digipay_client_secret',
			'preferred'      => 'digipay_preferred',
			'amount_unit'    => 'digipay_amount_unit',
			'logging'        => 'digipay_logging',
		);

		foreach ( $map as $gateway_key => $shared_key ) {
			$gateway_value = $this->get_option( $gateway_key, '' );
			if ( '' === $gateway_value || null === $gateway_value ) {
				$this->settings[ $gateway_key ] = HCK_Helpers::get( $shared_key, '' );
			}
		}

		// The shared API client reads the plugin settings; mirror gateway values in.
		if ( '' !== $this->get_option( 'username', '' ) ) {
			HCK_Settings::update(
				array(
					'digipay_environment'    => $this->get_option( 'environment', 'live' ),
					'digipay_username'      => $this->get_option( 'username', '' ),
					'digipay_password'      => $this->get_option( 'password', '' ),
					'digipay_client_id'     => $this->get_option( 'client_id', '' ),
					'digipay_client_secret' => $this->get_option( 'client_secret', '' ),
					'digipay_preferred'     => $this->get_option( 'preferred', 'auto' ),
					'digipay_amount_unit'   => $this->get_option( 'amount_unit', 'rial' ),
					'digipay_logging'       => $this->get_option( 'logging', 'no' ),
				)
			);
		}
	}

	/**
	 * Receipt page (redirect happens immediately, so just show a message).
	 *
	 * @param int $order_id Order ID.
	 */
	public function receipt_page( $order_id ) {
		echo '<div class="hck-notice hck-notice--info">' . esc_html__( 'Redirecting you to DigiPay…', 'hooshyar-commerce-kit' ) . '</div>';
	}

	/**
	 * Handle the payment result POST from DigiPay.
	 */
	public function handle_callback() {
		$result  = isset( $_POST['result'] ) ? sanitize_text_field( wp_unslash( $_POST['result'] ) ) : ( isset( $_GET['result'] ) ? sanitize_text_field( wp_unslash( $_GET['result'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$provider_id = isset( $_POST['providerId'] ) ? sanitize_text_field( wp_unslash( $_POST['providerId'] ) ) : ( isset( $_GET['providerId'] ) ? sanitize_text_field( wp_unslash( $_GET['providerId'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( '' === $provider_id ) {
			wp_die( esc_html__( 'Invalid DigiPay callback.', 'hooshyar-commerce-kit' ) );
		}

		$order = $this->find_order_by_provider_id( $provider_id );

		if ( ! $order ) {
			wp_die( esc_html__( 'Order not found for this payment.', 'hooshyar-commerce-kit' ) );
		}

		$tracking_code = isset( $_POST['trackingCode'] ) ? sanitize_text_field( wp_unslash( $_POST['trackingCode'] ) ) : ( isset( $_GET['trackingCode'] ) ? sanitize_text_field( wp_unslash( $_GET['trackingCode'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$amount        = isset( $_POST['amount'] ) ? (int) wp_unslash( $_POST['amount'] ) : ( isset( $_GET['amount'] ) ? (int) wp_unslash( $_GET['amount'] ) : 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$type          = isset( $_POST['type'] ) ? (int) wp_unslash( $_POST['type'] ) : ( isset( $_GET['type'] ) ? (int) wp_unslash( $_GET['type'] ) : 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$rrn           = isset( $_POST['rrn'] ) ? sanitize_text_field( wp_unslash( $_POST['rrn'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$masked_pan    = isset( $_POST['maskedPan'] ) ? sanitize_text_field( wp_unslash( $_POST['maskedPan'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( 'SUCCESS' !== $result ) {
			$order->update_status( 'failed', __( 'DigiPay payment failed or was cancelled by the user.', 'hooshyar-commerce-kit' ) );
			wp_safe_redirect( $this->get_return_url( $order ) );
			exit;
		}

		// Validate amount.
		$expected = (int) $order->get_meta( '_hck_digipay_amount' );
		if ( ! $expected ) {
			$expected = $this->get_order_amount( $order );
		}

		if ( $amount > 0 && $amount !== $expected ) {
			$order->update_status( 'failed', __( 'DigiPay amount mismatch.', 'hooshyar-commerce-kit' ) );
			wp_safe_redirect( $this->get_return_url( $order ) );
			exit;
		}

		// Verify the purchase (mandatory step).
		$verify = $this->api->verify_purchase( $tracking_code, $provider_id, $type );

		if ( is_wp_error( $verify ) ) {
			$order->update_status( 'failed', sprintf(
				/* translators: %s: error message */
				__( 'DigiPay verification failed: %s', 'hooshyar-commerce-kit' ),
				$verify->get_error_message()
			) );
			wp_safe_redirect( $this->get_return_url( $order ) );
			exit;
		}

		$order->update_meta_data( '_hck_digipay_tracking_code', $tracking_code );
		$order->update_meta_data( '_hck_digipay_type', $type );
		if ( $rrn ) {
			$order->update_meta_data( '_hck_digipay_rrn', $rrn );
		}
		if ( $masked_pan ) {
			$order->update_meta_data( '_hck_digipay_masked_pan', $masked_pan );
		}

		$order->payment_complete( $tracking_code );
		$order->add_order_note( sprintf(
			/* translators: 1: tracking code 2: masked pan */
			__( 'DigiPay payment completed. Tracking code: %1$s %2$s', 'hooshyar-commerce-kit' ),
			$tracking_code,
			$masked_pan ? '(**** ' . $masked_pan . ')' : ''
		) );

		if ( WC()->session ) {
			WC()->session->set( 'order_awaiting_payment', null );
		}

		wp_safe_redirect( $this->get_return_url( $order ) );
		exit;
	}

	/**
	 * Locate an order by the stored provider id.
	 *
	 * @param string $provider_id Provider id.
	 * @return WC_Order|false
	 */
	protected function find_order_by_provider_id( $provider_id ) {
		// Fast path: provider id is the order id.
		if ( is_numeric( $provider_id ) ) {
			$order = wc_get_order( (int) $provider_id );
			if ( $order && (string) $order->get_meta( '_hck_digipay_provider_id' ) === (string) $provider_id ) {
				return $order;
			}
		}

		$orders = wc_get_orders(
			array(
				'limit'      => 1,
				'meta_key'   => '_hck_digipay_provider_id',
				'meta_value' => $provider_id,
				'return'     => 'objects',
			)
		);

		return ! empty( $orders ) ? $orders[0] : false;
	}

	/**
	 * Refund via manual reverse.
	 *
	 * @param int        $order_id Order ID.
	 * @param float|null $amount   Amount (not used — DigiPay reverses the full purchase).
	 * @param string     $reason   Reason.
	 * @return bool|WP_Error
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return new WP_Error( 'hck_digipay', __( 'Order not found.', 'hooshyar-commerce-kit' ) );
		}

		$tracking_code = (string) $order->get_meta( '_hck_digipay_tracking_code' );
		$provider_id   = (string) $order->get_meta( '_hck_digipay_provider_id' );
		$type          = (int) $order->get_meta( '_hck_digipay_type' );

		if ( '' === $tracking_code || '' === $provider_id ) {
			return new WP_Error( 'hck_digipay', __( 'No DigiPay transaction found for this order.', 'hooshyar-commerce-kit' ) );
		}

		$this->sync_shared_settings();

		$result = $this->api->reverse_purchase( $tracking_code, $provider_id, $type );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$order->add_order_note( __( 'DigiPay purchase reversed (refunded).', 'hooshyar-commerce-kit' ) );

		return true;
	}

	/**
	 * Admin payment meta fields on the order screen.
	 *
	 * @param WC_Order $order Order.
	 */
	public function admin_payment_meta( $order ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		return array();
	}
}
