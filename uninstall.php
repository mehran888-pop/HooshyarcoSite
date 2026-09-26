<?php
/**
 * Uninstall handler — removes plugin data when deleted from the dashboard.
 *
 * @package HooshyarCommerceKit
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Remove options.
delete_option( 'hck_settings' );
delete_option( 'hck_version' );
delete_option( 'hck_db_version' );

// Remove transients.
global $wpdb;
// phpcs:ignore WordPress.DB
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_hck_%' OR option_name LIKE '_transient_timeout_hck_%'" );

// Drop custom table.
$table = $wpdb->prefix . 'hck_notification_log';
// phpcs:ignore WordPress.DB
$wpdb->query( "DROP TABLE IF EXISTS {$table}" );

// Clear scheduled events.
wp_clear_scheduled_hook( 'hck_daily_maintenance' );
