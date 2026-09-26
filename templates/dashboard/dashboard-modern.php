<?php
/**
 * Dashboard template: Modern.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

HCK_Helpers::template( 'dashboard/dashboard-shell.php', array( 'template' => 'modern', 'layout' => $layout, 'user' => $user ) );
