<?php
/**
 * Bale Bot API client (https://tapi.bale.ai — Bot API compatible).
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Bale
 */
class HCK_Bale {

	/**
	 * API base.
	 */
	const API_BASE = 'https://tapi.bale.ai/bot';

	/**
	 * Singleton.
	 *
	 * @var HCK_Bale|null
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * @return HCK_Bale
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get the bot token.
	 *
	 * @return string
	 */
	protected function get_token() {
		return trim( (string) HCK_Helpers::get( 'bale_token', '' ) );
	}

	/**
	 * Get the chat id.
	 *
	 * @return string
	 */
	protected function get_chat_id() {
		return trim( (string) HCK_Helpers::get( 'bale_chat_id', '' ) );
	}

	/**
	 * Is the integration configured?
	 *
	 * @return bool
	 */
	public function is_configured() {
		return '' !== $this->get_token() && '' !== $this->get_chat_id();
	}

	/**
	 * Call a Bale Bot API method.
	 *
	 * @param string $method Method name.
	 * @param array  $params Parameters.
	 * @return array|WP_Error
	 */
	protected function api( $method, $params ) {
		$token = $this->get_token();
		if ( '' === $token ) {
			return new WP_Error( 'hck_bale', __( 'Bale bot token is not set.', 'hooshyar-commerce-kit' ) );
		}

		$url      = self::API_BASE . $token . '/' . $method;
		$response = wp_remote_post(
			$url,
			array(
				'timeout' => 20,
				'body'    => $params,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $code || empty( $body['ok'] ) ) {
			$message = isset( $body['description'] ) ? $body['description'] : 'HTTP ' . $code;
			return new WP_Error( 'hck_bale', $message );
		}

		return $body;
	}

	/**
	 * Send a text message.
	 *
	 * @param string $text Message text (HTML supported).
	 * @return array|WP_Error
	 */
	public function send_text( $text ) {
		return $this->api(
			'sendMessage',
			array(
				'chat_id'    => $this->get_chat_id(),
				'text'       => $text,
				'parse_mode' => 'HTML',
			)
		);
	}

	/**
	 * Send a photo with caption.
	 *
	 * @param string $photo   Photo URL.
	 * @param string $caption Caption.
	 * @return array|WP_Error
	 */
	public function send_photo( $photo, $caption = '' ) {
		return $this->api(
			'sendPhoto',
			array(
				'chat_id'    => $this->get_chat_id(),
				'photo'      => $photo,
				'caption'    => $caption,
				'parse_mode' => 'HTML',
			)
		);
	}
}
