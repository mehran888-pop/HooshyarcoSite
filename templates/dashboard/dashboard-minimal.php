<?php
/**
 * Dashboard template: Minimal.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

HCK_Helpers::template( 'dashboard/dashboard-shell.php', array( 'template' => 'minimal', 'layout' => $layout, 'user' => $user ) );
