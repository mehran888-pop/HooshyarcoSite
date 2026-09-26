<?php
/**
 * Dashboard template: Default.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

HCK_Helpers::template( 'dashboard/dashboard-shell.php', array( 'template' => 'default', 'layout' => $layout, 'user' => $user ) );
