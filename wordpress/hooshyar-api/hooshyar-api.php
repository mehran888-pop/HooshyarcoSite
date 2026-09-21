<?php
/**
 * Plugin Name: هوشیار API (Hooshyar REST API)
 * Plugin URI:  https://github.com/mehran888-pop/HooshyarcoSite
 * Description: اندپوینت‌های اختصاصی hooshyar/v1 برای قالب هوشیار پاری‌نگر — ورود با موبایل (OTP)، تیکت، فاکتور، CRM، پیامک (ملی‌پیامک/sms.ir/ippanel)، ربات بله و پرداخت ایرانی (آیدی‌پی، زرین‌پال و…).
 * Version:     1.0.0
 * Author:      Hooshyar Parinegar
 * Text Domain: hooshyar-api
 * Requires at least: 5.8
 * Requires PHP: 7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // خروج مستقیم ممنوع
}

define( 'HYP_NS', 'hooshyar/v1' );
define( 'HYP_VERSION', '1.0.0' );
define( 'HYP_FILE', __FILE__ );

require_once __DIR__ . '/class-hooshyar-api.php';

/**
 * راه‌اندازی افزونه — فقط یک نمونه سراسری ساخته می‌شود.
 */
function HYP() {
	return Hooshyar_API::instance();
}

register_activation_hook( __FILE__, array( 'Hooshyar_API', 'on_activate' ) );

HYP();
