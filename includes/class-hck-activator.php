<?php
/**
 * Activation routines.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Activator
 */
class HCK_Activator {

	/**
	 * Run on plugin activation.
	 */
	public static function activate() {
		self::check_requirements();
		self::install_tables();
		self::seed_options();
		self::create_pages();
		self::schedule_events();

		update_option( 'hck_version', HCK_VERSION );
		update_option( 'hck_db_version', HCK_VERSION );

		// Make sure rewrite rules are flushed once.
		set_transient( 'hck_flush_rewrite', 1, HOUR_IN_SECONDS );
	}

	/**
	 * Abort activation when requirements are not met.
	 */
	private static function check_requirements() {
		if ( version_compare( PHP_VERSION, HCK_MIN_PHP, '<' ) ) {
			deactivate_plugins( HCK_PLUGIN_BASENAME );
			wp_die(
				esc_html(
					sprintf(
						/* translators: %s: required PHP version */
						__( 'Hooshyar Commerce Kit requires PHP %s or higher.', 'hooshyar-commerce-kit' ),
						HCK_MIN_PHP
					)
				),
				'',
				array( 'back_link' => true )
			);
		}

		if ( ! class_exists( 'WooCommerce' ) ) {
			deactivate_plugins( HCK_PLUGIN_BASENAME );
			wp_die(
				esc_html__( 'Hooshyar Commerce Kit requires WooCommerce. Please install and activate WooCommerce first.', 'hooshyar-commerce-kit' ),
				'',
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * Create custom tables (notification log).
	 */
	private static function install_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table   = $wpdb->prefix . 'hck_notification_log';
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			channel VARCHAR(20) NOT NULL,
			product_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
			status VARCHAR(20) NOT NULL DEFAULT 'pending',
			response TEXT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY channel (channel),
			KEY product_id (product_id)
		) {$charset};";

		dbDelta( $sql );
	}

	/**
	 * Seed default options.
	 */
	private static function seed_options() {
		$defaults = HCK_Settings::get_defaults();
		$current  = get_option( 'hck_settings', array() );

		if ( ! is_array( $current ) ) {
			$current = array();
		}

		update_option( 'hck_settings', wp_parse_args( $current, $defaults ) );
	}

	/**
	 * Ensure core WooCommerce pages exist.
	 */
	private static function create_pages() {
		if ( ! function_exists( 'wc_get_page_id' ) ) {
			return;
		}

		$pages = array(
			'cart'     => array(
				'title'    => __( 'Cart', 'hooshyar-commerce-kit' ),
				'shortcode' => '[woocommerce_cart]',
			),
			'checkout' => array(
				'title'    => __( 'Checkout', 'hooshyar-commerce-kit' ),
				'shortcode' => '[woocommerce_checkout]',
			),
			'myaccount' => array(
				'title'    => __( 'My account', 'hooshyar-commerce-kit' ),
				'shortcode' => '[woocommerce_my_account]',
			),
		);

		foreach ( $pages as $key => $page ) {
			$existing_id = wc_get_page_id( $key );
			if ( $existing_id > 0 && get_post( $existing_id ) ) {
				continue;
			}

			$page_id = wp_insert_post(
				array(
					'post_title'   => $page['title'],
					'post_content' => $page['shortcode'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
				)
			);

			if ( $page_id && ! is_wp_error( $page_id ) ) {
				update_option( 'woocommerce_' . $key . '_page_id', $page_id );
			}
		}
	}

	/**
	 * Schedule background events.
	 */
	private static function schedule_events() {
		if ( ! wp_next_scheduled( 'hck_daily_maintenance' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'hck_daily_maintenance' );
		}
	}
}
