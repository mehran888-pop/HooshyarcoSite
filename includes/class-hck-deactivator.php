<?php
/**
 * Deactivation routines.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Deactivator
 */
class HCK_Deactivator {

	/**
	 * Run on plugin deactivation.
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'hck_daily_maintenance' );
		delete_transient( 'hck_flush_rewrite' );
	}
}
