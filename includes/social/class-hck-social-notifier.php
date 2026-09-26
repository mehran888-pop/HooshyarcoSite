<?php
/**
 * Social notifier — publishes new products to Telegram & Bale.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Social_Notifier
 */
class HCK_Social_Notifier {

	/**
	 * Singleton.
	 *
	 * @var HCK_Social_Notifier|null
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * @return HCK_Social_Notifier
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
		add_action( 'woocommerce_new_product', array( $this, 'on_new_product' ), 20, 1 );
		add_action( 'hck_daily_maintenance', array( $this, 'cleanup_logs' ) );
	}

	/**
	 * Fires when a product is created — send once it is published.
	 *
	 * @param int $product_id Product ID.
	 */
	public function on_new_product( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product || 'publish' !== $product->get_status() ) {
			return;
		}

		// Avoid duplicates (the hook can fire more than once).
		if ( get_post_meta( $product_id, '_hck_social_sent', true ) ) {
			return;
		}

		$this->notify( $product );
		update_post_meta( $product_id, '_hck_social_sent', time() );
	}

	/**
	 * Build the message body from the template.
	 *
	 * @param WC_Product $product Product.
	 * @return string
	 */
	public function build_message( $product ) {
		$template = (string) HCK_Helpers::get( 'telegram_template', '' );

		$categories = array();
		$terms      = get_the_terms( $product->get_id(), 'product_cat' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$categories[] = $term->name;
			}
		}

		$replacements = array(
			'{title}'      => $product->get_name(),
			'{price}'      => wp_strip_all_tags( wc_price( $product->get_price() ) ),
			'{link}'       => get_permalink( $product->get_id() ),
			'{sku}'        => (string) $product->get_sku(),
			'{categories}' => implode( '، ', $categories ),
			'{excerpt}'    => wp_trim_words( wp_strip_all_tags( $product->get_short_description() ), 18 ),
		);

		return str_replace( array_keys( $replacements ), array_values( $replacements ), $template );
	}

	/**
	 * Send notifications to all enabled channels.
	 *
	 * @param WC_Product $product Product.
	 */
	public function notify( $product ) {
		$message = $this->build_message( $product );
		$photo   = wp_get_attachment_url( $product->get_image_id() );

		// Telegram.
		if ( 'yes' === HCK_Helpers::get( 'telegram_enabled', 'no' ) ) {
			$telegram = HCK_Telegram::instance();
			if ( $telegram->is_configured() ) {
				if ( $photo ) {
					$result = $telegram->send_photo( $photo, $message );
				} else {
					$result = $telegram->send_text( $message );
				}
				$this->log( 'telegram', $product->get_id(), $result );
			}
		}

		// Bale.
		if ( 'yes' === HCK_Helpers::get( 'bale_enabled', 'no' ) ) {
			$bale = HCK_Bale::instance();
			if ( $bale->is_configured() ) {
				$bale_template = (string) HCK_Helpers::get( 'bale_template', '' );
				if ( $bale_template ) {
					$message = str_replace(
						array_keys( array( '{title}' => '', '{price}' => '', '{link}' => '', '{sku}' => '', '{categories}' => '', '{excerpt}' => '' ) ),
						array_values(
							array(
								'{title}'      => $product->get_name(),
								'{price}'      => wp_strip_all_tags( wc_price( $product->get_price() ) ),
								'{link}'       => get_permalink( $product->get_id() ),
								'{sku}'        => (string) $product->get_sku(),
								'{categories}' => '',
								'{excerpt}'    => wp_trim_words( wp_strip_all_tags( $product->get_short_description() ), 18 ),
							)
						),
						$bale_template
					);
				}

				if ( $photo ) {
					$result = $bale->send_photo( $photo, $message );
				} else {
					$result = $bale->send_text( $message );
				}
				$this->log( 'bale', $product->get_id(), $result );
			}
		}
	}

	/**
	 * Persist a notification attempt.
	 *
	 * @param string          $channel  Channel name.
	 * @param int             $product_id Product ID.
	 * @param array|WP_Error  $result   Result.
	 */
	public function log( $channel, $product_id, $result ) {
		global $wpdb;

		$status   = is_wp_error( $result ) ? 'error' : 'sent';
		$response = is_wp_error( $result )
			? $result->get_error_message()
			: wp_json_encode( isset( $result['result'] ) ? $result['result'] : $result );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->insert(
			$wpdb->prefix . 'hck_notification_log',
			array(
				'channel'    => $channel,
				'product_id' => (int) $product_id,
				'status'     => $status,
				'response'   => HCK_Helpers::str_limit( (string) $response, 2000 ),
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%d', '%s', '%s', '%s' )
		);
	}

	/**
	 * Keep the log table small.
	 */
	public function cleanup_logs() {
		global $wpdb;

		$table = $wpdb->prefix . 'hck_notification_log';
		// phpcs:ignore WordPress.DB
		$wpdb->query( "DELETE FROM {$table} WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)" );
	}
}
