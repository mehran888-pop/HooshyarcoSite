<?php
/**
 * نمونه پیکربندی — این فایل را در wp-config.php خود کپی و مقداردهی کنید.
 * یا می‌توانید مقادیر را مستقیماً در این فایل قرار داده و آن را در
 * پوشه mu-plugins وردپرس بارگذاری کنید.
 */

/* ---------- پیامک ---------- */
define( 'HYP_SMS_PROVIDER', 'melipayamak' ); // melipayamak | smsir | ippanel

// ملی‌پیامک
define( 'HYP_SMS_USER', 'USERNAME' );
define( 'HYP_SMS_PASS', 'PASSWORD' );
define( 'HYP_SMS_FROM', '3000XXXX' );

// sms.ir / ippanel
define( 'HYP_SMS_API_KEY', 'YOUR_API_KEY' );
define( 'HYP_SMS_SENDER', '3000XXXX' );

/* ---------- ربات بله ---------- */
define( 'HYP_BALE_TOKEN', 'YOUR_BOT_TOKEN' );
define( 'HYP_BALE_CHANNEL', 'YOUR_CHANNEL_ID' );

/* ---------- درگاه‌های پرداخت ---------- */
define( 'HYP_IDPAY_API_KEY', 'YOUR_IDPAY_API_KEY' );
define( 'HYP_ZARINPAL_MERCHANT', 'YOUR_ZARINPAL_MERCHANT_ID' );
define( 'HYP_NOVINPAY_MERCHANT', 'YOUR_NOVINPAY_MERCHANT' );
define( 'HYP_AQAYE_PIN', 'YOUR_AQAYEPARDAKHT_PIN' );

/* ---------- احراز هویت ---------- */
// در محیط توسعه کد ثابت ۱۲۳۴۵ فعال شود؛ در Production حذف شود.
define( 'HYP_FIXED_OTP', false );
