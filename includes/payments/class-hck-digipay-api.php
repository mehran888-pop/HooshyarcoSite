<?php
/**
 * DigiPay UPG API client.
 *
 * Implements the Unified Payment Gateway (UPG) flow documented at
 * https://www.mydigipay.com/developers/docs/upg/
 *
 * 1. OAuth login      → POST /oauth/token
 * 2. Purchase ticket  → POST /tickets/business?type=11
 * 3. Payment result   → POST to the merchant callbackUrl
 * 4. Verify purchase  → POST /purchases/verify?type={type}
 * 5. Manual reverse   → POST /reverse?type={type}
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Digipay_Api
 */
class HCK_Digipay_Api {

	/**
	 * Live base URL.
	 */
	const BASE_LIVE = 'https://api.mydigipay.com/digipay/api';

	/**
	 * Staging base URL.
	 */
	const BASE_UAT = 'https://uat.mydigipay.info/digipay/api';

	/**
	 * API version header value.
	 */
	const VERSION = '2022-02-02';

	/**
	 * Transient key for the access token.
	 */
	const TOKEN_TRANSIENT = 'hck_digipay_token';

	/**
	 * Ticket type for UPG purchases.
	 */
	const TICKET_TYPE = 11;

	/**
	 * Get the configured environment.
	 *
	 * @return string 'live' or 'uat'.
	 */
	public function get_environment() {
		return 'uat' === HCK_Helpers::get( 'digipay_environment', 'live' ) ? 'uat' : 'live';
	}

	/**
	 * Base URL for the current environment.
	 *
	 * @return string
	 */
	public function get_base_url() {
		return 'uat' === $this->get_environment() ? self::BASE_UAT : self::BASE_LIVE;
	}

	/**
	 * Credentials.
	 *
	 * @return array
	 */
	public function get_credentials() {
		return array(
			'username'      => trim( (string) HCK_Helpers::get( 'digipay_username', '' ) ),
			'password'      => (string) HCK_Helpers::get( 'digipay_password', '' ),
			'client_id'     => trim( (string) HCK_Helpers::get( 'digipay_client_id', '' ) ),
			'client_secret' => (string) HCK_Helpers::get( 'digipay_client_secret', '' ),
		);
	}

	/**
	 * Are credentials configured?
	 *
	 * @return bool
	 */
	public function is_configured() {
		$creds = $this->get_credentials();
		return '' !== $creds['username'] && '' !== $creds['password'] && '' !== $creds['client_id'] && '' !== $creds['client_secret'];
	}

	/**
	 * HTTP helper.
	 *
	 * @param string $path    Path beginning with /.
	 * @param array  $args    wp_remote_post args.
	 * @return array|WP_Error Decoded JSON body.
	 */
	protected function request( $path, $args = array() ) {
		$url = $this->get_base_url() . $path;

		$defaults = array(
			'timeout' => 30,
		);

		$response = wp_remote_post( $url, wp_parse_args( $args, $defaults ) );

		if ( is_wp_error( $response ) ) {
			$this->log( 'REQUEST ERROR ' . $path . ': ' . $response->get_error_message() );
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		$this->log( sprintf( '%s → HTTP %d: %s', $path, $code, HCK_Helpers::str_limit( wp_remote_retrieve_body( $response ), 500 ) ) );

		if ( ! is_array( $body ) ) {
			return new WP_Error( 'hck_digipay', sprintf( 'Invalid response (HTTP %d).', $code ) );
		}

		$body['_http_code'] = $code;

		return $body;
	}

	/**
	 * Build the Basic authorization header value.
	 *
	 * @return string
	 */
	protected function basic_auth_header() {
		$creds = $this->get_credentials();
		return 'Basic ' . base64_encode( $creds['client_id'] . ':' . $creds['client_secret'] ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	/**
	 * OAuth login (password grant).
	 *
	 * @return array|WP_Error Token payload {access_token, refresh_token, expires_in}.
	 */
	public function login() {
		$creds = $this->get_credentials();

		if ( ! $this->is_configured() ) {
			return new WP_Error( 'hck_digipay', __( 'DigiPay credentials are not configured.', 'hooshyar-commerce-kit' ) );
		}

		$result = $this->request(
			'/oauth/token',
			array(
				'headers' => array(
					'Authorization' => $this->basic_auth_header(),
				),
				'body'    => array(
					'username'   => $creds['username'],
					'password'   => $creds['password'],
					'grant_type' => 'password',
				),
			)
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( empty( $result['access_token'] ) ) {
			$message = isset( $result['error_description'] ) ? $result['error_description'] : __( 'DigiPay login failed.', 'hooshyar-commerce-kit' );
			return new WP_Error( 'hck_digipay', $message );
		}

		$token = array(
			'access_token'  => $result['access_token'],
			'refresh_token' => isset( $result['refresh_token'] ) ? $result['refresh_token'] : '',
			'expires_at'    => time() + ( isset( $result['expires_in'] ) ? max( 60, (int) $result['expires_in'] - 60 ) : 3540 ),
		);

		set_transient( self::TOKEN_TRANSIENT, $token, HOUR_IN_SECONDS );

		return $token;
	}

	/**
	 * Refresh the access token.
	 *
	 * @return array|WP_Error
	 */
	public function refresh_token() {
		$token = get_transient( self::TOKEN_TRANSIENT );
		if ( empty( $token['refresh_token'] ) ) {
			return $this->login();
		}

		$creds = $this->get_credentials();

		$result = $this->request(
			'/oauth/token',
			array(
				'headers' => array(
					'Authorization' => $this->basic_auth_header(),
				),
				'body'    => array(
					'refresh_token' => $token['refresh_token'],
					'grant_type'    => 'refresh_token',
				),
			)
		);

		if ( is_wp_error( $result ) || empty( $result['access_token'] ) ) {
			// Fall back to a fresh password login.
			return $this->login();
		}

		$new = array(
			'access_token'  => $result['access_token'],
			'refresh_token' => isset( $result['refresh_token'] ) ? $result['refresh_token'] : $token['refresh_token'],
			'expires_at'    => time() + ( isset( $result['expires_in'] ) ? max( 60, (int) $result['expires_in'] - 60 ) : 3540 ),
		);

		set_transient( self::TOKEN_TRANSIENT, $new, HOUR_IN_SECONDS );

		return $new;
	}

	/**
	 * Get a valid access token (cached).
	 *
	 * @return string|WP_Error
	 */
	public function get_access_token() {
		$token = get_transient( self::TOKEN_TRANSIENT );

		if ( ! empty( $token['access_token'] ) && ( empty( $token['expires_at'] ) || $token['expires_at'] > time() ) ) {
			return $token['access_token'];
		}

		if ( ! empty( $token['refresh_token'] ) ) {
			$result = $this->refresh_token();
		} else {
			$result = $this->login();
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $result['access_token'];
	}

	/**
	 * Standard UPG headers.
	 *
	 * @param string $access_token Bearer token.
	 * @return array
	 */
	protected function upg_headers( $access_token ) {
		return array(
			'Authorization'    => 'Bearer ' . $access_token,
			'Content-Type'     => 'application/json; charset=UTF-8',
			'Agent'            => 'WEB',
			'Digipay-Version'  => self::VERSION,
		);
	}

	/**
	 * Create a purchase ticket.
	 *
	 * @param array $args {
	 *     @type int    $amount       Amount in Rials.
	 *     @type string $cellNumber   User mobile number.
	 *     @type string $providerId   Unique merchant-side purchase id.
	 *     @type string $callbackUrl  Return URL.
	 *     @type array  $additionalInfo Optional extra payload.
	 * }
	 * @return array|WP_Error {ticket, redirectUrl, result}.
	 */
	public function create_ticket( $args ) {
		$token = $this->get_access_token();
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$defaults = array(
			'amount'         => 0,
			'cellNumber'     => '',
			'providerId'     => '',
			'callbackUrl'    => '',
			'additionalInfo' => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		$payload = array(
			'amount'      => (int) $args['amount'],
			'cellNumber'  => $args['cellNumber'],
			'providerId'  => (string) $args['providerId'],
			'callbackUrl' => $args['callbackUrl'],
		);

		if ( ! empty( $args['additionalInfo'] ) && is_array( $args['additionalInfo'] ) ) {
			$payload['additionalInfo'] = $args['additionalInfo'];
		}

		$result = $this->request(
			'/tickets/business?type=' . self::TICKET_TYPE,
			array(
				'headers' => $this->upg_headers( $token ),
				'body'    => wp_json_encode( $payload ),
			)
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( empty( $result['ticket'] ) || empty( $result['redirectUrl'] ) ) {
			$message = isset( $result['result']['message'] ) ? $result['result']['message'] : __( 'DigiPay ticket creation failed.', 'hooshyar-commerce-kit' );
			return new WP_Error( 'hck_digipay', $message );
		}

		return $result;
	}

	/**
	 * Verify a successful purchase.
	 *
	 * @param string $trackingCode Tracking code from the payment result.
	 * @param string $providerId   Merchant purchase id.
	 * @param int    $type         Purchase type (0 IPG, 11 WALLET, 5 CREDIT, 13 BNPL, 24 CREDIT-CARD).
	 * @return array|WP_Error
	 */
	public function verify_purchase( $tracking_code, $provider_id, $type = 0 ) {
		$token = $this->get_access_token();
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$result = $this->request(
			'/purchases/verify?type=' . absint( $type ),
			array(
				'headers' => $this->upg_headers( $token ),
				'body'    => wp_json_encode(
					array(
						'trackingCode' => (string) $tracking_code,
						'providerId'   => (string) $provider_id,
					)
				),
			)
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( ! isset( $result['result']['status'] ) || 0 !== (int) $result['result']['status'] ) {
			$message = isset( $result['result']['message'] ) ? $result['result']['message'] : __( 'DigiPay verification failed.', 'hooshyar-commerce-kit' );
			return new WP_Error( 'hck_digipay', $message );
		}

		return $result;
	}

	/**
	 * Manual reverse (refund) of a verified purchase (within the allowed window).
	 *
	 * @param string $tracking_code Tracking code.
	 * @param string $provider_id   Merchant purchase id.
	 * @param int    $type          Purchase type.
	 * @return array|WP_Error
	 */
	public function reverse_purchase( $tracking_code, $provider_id, $type = 0 ) {
		$token = $this->get_access_token();
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$result = $this->request(
			'/reverse?type=' . absint( $type ),
			array(
				'headers' => $this->upg_headers( $token ),
				'body'    => wp_json_encode(
					array(
						// The UPG docs use "purchaseTrackingCode" in samples and
						// "trackingCode" in the field table — send both.
						'purchaseTrackingCode' => (string) $tracking_code,
						'trackingCode'         => (string) $tracking_code,
						'providerId'           => (string) $provider_id,
					)
				),
			)
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( ! isset( $result['result']['status'] ) || 0 !== (int) $result['result']['status'] ) {
			$message = isset( $result['result']['message'] ) ? $result['result']['message'] : __( 'DigiPay reverse failed.', 'hooshyar-commerce-kit' );
			return new WP_Error( 'hck_digipay', $message );
		}

		return $result;
	}

	/**
	 * Test the connection (used by the settings screen).
	 *
	 * @return array|WP_Error
	 */
	public function test_connection() {
		$result = $this->login();

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return array(
			'message'       => __( 'Connected to DigiPay successfully.', 'hooshyar-commerce-kit' ),
			'environment'   => $this->get_environment(),
			'token_expires' => isset( $result['expires_at'] ) ? gmdate( 'Y-m-d H:i', $result['expires_at'] ) : '',
		);
	}

	/**
	 * Debug logger (WooCommerce logs).
	 *
	 * @param string $message Message.
	 */
	protected function log( $message ) {
		if ( 'yes' !== HCK_Helpers::get( 'digipay_logging', 'no' ) ) {
			return;
		}

		if ( function_exists( 'wc_get_logger' ) ) {
			wc_get_logger()->debug( $message, array( 'source' => 'hck-digipay' ) );
		}
	}
}
