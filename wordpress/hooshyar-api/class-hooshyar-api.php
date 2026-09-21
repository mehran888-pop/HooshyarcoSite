<?php
/**
 * کلاس اصلی افزونه هوشیار API
 *
 * همه‌ی اندپوینت‌های REST، منطق OTP، مدیریت داده‌ها و ارسال اعلان‌ها اینجا است.
 */

global $wpdb;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Hooshyar_API {

	/** @var Hooshyar_API|null نمونه سراسری */
	private static $instance = null;

	/** @var wpdb */
	private $db;

	/** جدول‌های سفارشی (پیشوند وردپرس به آن‌ها می‌خورد) */
	private $t_otp      = 'hooshyar_otp';
	private $t_tickets  = 'hooshyar_tickets';
	private $t_messages = 'hooshyar_ticket_messages';
	private $t_invoices = 'hooshyar_invoices';
	private $t_events   = 'hooshyar_events';

	private function __construct() {
		global $wpdb;
		$this->db      = $wpdb;
		$this->t_otp   = $wpdb->prefix . 'hooshyar_otp';
		$this->t_tickets    = $wpdb->prefix . 'hooshyar_tickets';
		$this->t_messages   = $wpdb->prefix . 'hooshyar_ticket_messages';
		$this->t_invoices   = $wpdb->prefix . 'hooshyar_invoices';
		$this->t_events     = $wpdb->prefix . 'hooshyar_events';

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );

		// پشتیبانی REST برای CMB2 در صورت فعال بودن
		if ( function_exists( 'cmb2_bootstrap' ) ) {
			add_action( 'cmb2_init', array( $this, 'register_cmb2_boxes' ) );
		}
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function on_activate() {
		self::instance()->create_tables();
		flush_rewrite_rules();
	}

	// ============================================================
	// راه‌اندازی جدول‌ها
	// ============================================================

	public function create_tables() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$charset = $this->db->get_charset_collate();

		$sql = array();

		$sql[] = "CREATE TABLE {$this->t_otp} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			mobile VARCHAR(20) NOT NULL,
			code VARCHAR(10) NOT NULL,
			expires INT UNSIGNED NOT NULL,
			attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (id),
			KEY mobile (mobile)
		) $charset;";

		$sql[] = "CREATE TABLE {$this->t_tickets} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			subject VARCHAR(255) NOT NULL,
			department VARCHAR(100) NOT NULL DEFAULT 'فنی',
			priority VARCHAR(20) NOT NULL DEFAULT 'medium',
			status VARCHAR(20) NOT NULL DEFAULT 'open',
			created_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY status (status)
		) $charset;";

		$sql[] = "CREATE TABLE {$this->t_messages} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			ticket_id BIGINT UNSIGNED NOT NULL,
			author VARCHAR(20) NOT NULL DEFAULT 'customer',
			author_name VARCHAR(120) NOT NULL DEFAULT '',
			message TEXT NOT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			KEY ticket_id (ticket_id)
		) $charset;";

		$sql[] = "CREATE TABLE {$this->t_invoices} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			number VARCHAR(40) NOT NULL,
			customer_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			customer_name VARCHAR(255) NOT NULL DEFAULT '',
			items TEXT NULL,
			total BIGINT UNSIGNED NOT NULL DEFAULT 0,
			status VARCHAR(20) NOT NULL DEFAULT 'draft',
			gateway VARCHAR(40) NULL,
			ref_id VARCHAR(80) NULL,
			created_at DATETIME NOT NULL,
			due_at DATETIME NULL,
			paid_at DATETIME NULL,
			PRIMARY KEY (id),
			KEY customer_id (customer_id)
		) $charset;";

		$sql[] = "CREATE TABLE {$this->t_events} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			type VARCHAR(40) NOT NULL DEFAULT 'system',
			title VARCHAR(255) NOT NULL DEFAULT '',
			body TEXT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY (id)
		) $charset;";

		foreach ( $sql as $q ) {
			dbDelta( $q );
		}
	}

	// ============================================================
	// ثبت مسیرهای REST
	// ============================================================

	public function register_routes() {

		/* ---------- احراز هویت ---------- */
		register_rest_route( HYP_NS, 'auth/otp', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'auth_send_otp' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( HYP_NS, 'auth/verify', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'auth_verify_otp' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( HYP_NS, 'auth/register', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'auth_register' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( HYP_NS, 'me', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_me' ),
			'permission_callback' => 'is_user_logged_in',
		) );

		/* ---------- تیکت پشتیبانی ---------- */
		$ticket_args = array(
			'id' => array( 'required' => true, 'validate_callback' => function ( $p ) { return is_numeric( $p ); } ),
		);

		register_rest_route( HYP_NS, 'support/tickets', array(
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'tickets_index' ),
				'permission_callback' => 'is_user_logged_in',
			),
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ticket_create' ),
				'permission_callback' => 'is_user_logged_in',
			),
		) );

		register_rest_route( HYP_NS, 'support/tickets/(?P<id>[\d]+)', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'ticket_show' ),
			'permission_callback' => array( $this, 'ticket_permission' ),
			'args'                => $ticket_args,
		) );

		register_rest_route( HYP_NS, 'support/tickets/(?P<id>[\d]+)/reply', array(
			'methods' => 'POST',
			'callback' => array( $this, 'ticket_reply' ),
			'permission_callback' => array( $this, 'ticket_permission' ),
			'args' => $ticket_args,
		) );

		/* ---------- فاکتورها و حسابداری ---------- */
		$invoice_args = array(
			'id' => array( 'required' => true, 'validate_callback' => function ( $p ) { return is_numeric( $p ); } ),
		);

		register_rest_route( HYP_NS, 'invoices', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'invoices_index' ),
			'permission_callback' => 'is_user_logged_in',
		) );

		register_rest_route( HYP_NS, 'invoices/(?P<id>[\d]+)', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'invoice_show' ),
			'permission_callback' => 'is_user_logged_in',
			'args'                => $invoice_args,
		) );

		register_rest_route( HYP_NS, 'invoices/(?P<id>[\d]+)/pay', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'invoice_init_pay' ),
			'permission_callback' => 'is_user_logged_in',
			'args'                => $invoice_args,
		) );

		register_rest_route( HYP_NS, 'invoices/(?P<id>[\d]+)/pay/verify', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'invoice_verify_pay' ),
			'permission_callback' => '__return_true',
			'args'                => $invoice_args,
		) );

		/* برگشت از درگاه پرداخت (callback) */
		register_rest_route( HYP_NS, 'invoices/pay/callback', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'invoice_pay_callback' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( HYP_NS, 'invoices/(?P<id>[\d]+)/pdf', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'invoice_pdf' ),
			'permission_callback' => 'is_user_logged_in',
			'args'                => $invoice_args,
		) );

		/* ---------- رویدادها / اعلان فوری ---------- */
		register_rest_route( HYP_NS, 'events', array(
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'events_index' ),
				'permission_callback' => array( $this, 'events_permission' ),
			),
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'event_create' ),
				'permission_callback' => 'is_user_logged_in',
			),
		) );

		/* ---------- محتوای چندزبانه (اختیاری؛ از CMB2 یا پست‌تایپ‌ها) ---------- */
		register_rest_route( HYP_NS, 'services', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'list_post_type_items' ),
			'permission_callback' => '__return_true',
			'args'                => array( 'type' => array( 'default' => 'service' ) ),
		) );

		register_rest_route( HYP_NS, 'team', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'list_post_type_items' ),
			'permission_callback' => '__return_true',
			'args'                => array( 'type' => array( 'default' => 'team' ) ),
		) );

		register_rest_route( HYP_NS, 'projects', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'list_post_type_items' ),
			'permission_callback' => '__return_true',
			'args'                => array( 'type' => array( 'default' => 'project' ) ),
		) );

		register_rest_route( HYP_NS, 'testimonials', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'list_post_type_items' ),
			'permission_callback' => '__return_true',
			'args'                => array( 'type' => array( 'default' => 'testimonial' ) ),
		) );

		register_rest_route( HYP_NS, 'faqs', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'list_post_type_items' ),
			'permission_callback' => '__return_true',
			'args'                => array( 'type' => array( 'default' => 'faq' ) ),
		) );
	}

	// ============================================================
	// ابزارهای عمومی
	// ============================================================

	private function respond( $data, $status = 200 ) {
		return new WP_REST_Response( $data, $status );
	}

	private function error( $message, $code, $status = 400 ) {
		return new WP_Error( $code, $message, array( 'status' => $status ) );
	}

	private function json_params() {
		return json_decode( $this->db->escape( file_get_contents( 'php://input' ) ), true );
	}

	private function normalize_mobile( $mobile ) {
		$m = preg_replace( '/[^0-9]/', '', (string) $mobile );
		if ( substr( $m, 0, 4 ) === '0098' ) {
			$m = substr( $m, 4 );
		} elseif ( substr( $m, 0, 2 ) === '98' ) {
			$m = substr( $m, 2 );
		} elseif ( substr( $m, 0, 1 ) === '0' ) {
			$m = substr( $m, 1 );
		}
		return '0' . ($m ? $m : '');
	}

	private function is_valid_mobile( $mobile ) {
		return (bool) preg_match( '/^0?9\d{9}$/', preg_replace( '/[^0-9]/', '', (string) $mobile ) );
	}

	/**
	 * ساخت کاربر یا پیداکردن آن بر اساس شماره موبایل.
	 * نقش پیش‌فرض «مشتری» است (یا هر نقشی که افزونه مقصد تعیین کند).
	 */
	private function resolve_user( $mobile, $role = 'subscriber' ) {
		$m = $this->normalize_mobile( $mobile );
		$user = null;

		$found = get_users( array(
			'meta_key'   => 'hooshyar_mobile',
			'meta_value' => $m,
			'number'     => 1,
		) );

		if ( ! empty( $found ) ) {
			$user = $found[0];
		} else {
			// نام کاربری یکتا بر اساس موبایل
			$login = 'u' . $m;
			$existing = get_user_by( 'login', $login );
			$user_id = $existing ? wp_create_user( $login . wp_rand( 100, 999 ), wp_generate_password(), $m ) : wp_create_user( $login, wp_generate_password(), $m );

			if ( is_wp_error( $user_id ) ) {
				return null;
			}
			wp_update_user( array( 'ID' => $user_id, 'role' => $role, 'display_name' => $m ) );
			update_user_meta( $user_id, 'hooshyar_mobile', $m );
			$user = get_user_by( 'id', $user_id );
		}

		return $user;
	}

	/** ساخت توکن JWT (نیازمند افزونه JWT Authentication for WP REST API) */
	private function issue_jwt( $user ) {
		if ( ! class_exists( 'Jwt_Auth_Public' ) ) {
			return null;
		}
		$jwt = new Jwt_Auth_Public( 'hooshyar-api', '1.0.0' );
		$creds = array();
		if ( method_exists( $jwt, 'generate_token' ) ) {
			$creds = $jwt->generate_token( $user );
		}
		return isset( $creds['token'] ) ? $creds['token'] : null;
	}

	private function user_meta_to_array( $user ) {
		return array(
			'id'       => $user->ID,
			'name'     => $user->display_name,
			'mobile'   => get_user_meta( $user->ID, 'hooshyar_mobile', true ),
			'email'    => $user->user_email,
			'role'     => current( $user->roles ),
			'verified' => true,
			'avatar'   => get_avatar_url( $user->ID ),
		);
	}

	private function log_event( $type, $title, $body = '' ) {
		$this->db->insert(
			$this->t_events,
			array(
				'type'       => $type,
				'title'      => $title,
				'body'       => $body,
				'created_at' => current_time( 'mysql' ),
			)
		);
		do_action( 'hooshyar_event', $type, $title, $body );
	}

	// ============================================================
	// احراز هویت با OTP
	// ============================================================

	public function auth_send_otp( WP_REST_Request $req ) {
		$mobile = $this->normalize_mobile( $req->get_param( 'mobile' ) );

		if ( ! $this->is_valid_mobile( $mobile ) ) {
			return $this->error( 'شماره موبایل نامعتبر است.', 'invalid_mobile' );
		}

		$code = defined( 'HYP_FIXED_OTP' ) && HYP_FIXED_OTP ? '12345' : (string) wp_rand( 10000, 99999 );

		$existing = $this->db->get_row( $this->db->prepare( "SELECT * FROM {$this->t_otp} WHERE mobile=%s", $mobile ) );
		$expires  = time() + 180; // ۳ دقیقه

		if ( $existing ) {
			$this->db->update(
				$this->t_otp,
				array( 'code' => $code, 'expires' => $expires, 'attempts' => 0 ),
				array( 'id' => $existing->id )
			);
		} else {
			$this->db->insert(
				$this->t_otp,
				array( 'mobile' => $mobile, 'code' => $code, 'expires' => $expires, 'attempts' => 0 )
			);
		}

		// ارسال پیامک از طریق سامانه پیش‌فرض
		$provider = defined( 'HYP_SMS_PROVIDER' ) ? HYP_SMS_PROVIDER : 'melipayamak';
		$this->send_sms( $provider, $mobile, 'کد ورود به هوشیار: ' . $code );

		$this->log_event( 'auth', 'ارسال کد ورود', $mobile );

		return $this->respond( array(
			'success'    => true,
			'expires_in' => 180,
			'dev_code'   => defined( 'HYP_FIXED_OTP' ) && HYP_FIXED_OTP ? $code : null,
			'message'    => 'کد تأیید ارسال شد.',
		) );
	}

	public function auth_verify_otp( WP_REST_Request $req ) {
		$mobile = $this->normalize_mobile( $req->get_param( 'mobile' ) );
		$code   = (string) $req->get_param( 'code' );

		$row = $this->db->get_row( $this->db->prepare( "SELECT * FROM {$this->t_otp} WHERE mobile=%s", $mobile ) );

		if ( ! $row ) {
			return $this->error( 'ابتدا کد تأیید را درخواست کنید.', 'no_otp' );
		}
		if ( (int) $row->attempts >= 5 ) {
			return $this->error( 'ورود موقتاً قفل شد. بعداً تلاش کنید.', 'locked', 429 );
		}
		if ( (int) $row->expires < time() ) {
			return $this->error( 'کد منقضی شده است.', 'expired' );
		}
		if ( ! hash_equals( $row->code, $code ) ) {
			$this->db->update( $this->t_otp, array( 'attempts' => (int) $row->attempts + 1 ), array( 'id' => $row->id ) );
			return $this->error( 'کد تأیید نادرست است.', 'bad_code' );
		}

		$user = $this->resolve_user( $mobile );

		if ( ! $user ) {
			return $this->error( 'خطا در ساخت حساب کاربری.', 'user_error' );
		}

		// پاکسازی کد استفاده‌شده
		$this->db->delete( $this->t_otp, array( 'id' => $row->id ) );

		$token = $this->issue_jwt( $user );

		$this->log_event( 'auth', 'ورود موفق', $mobile );

		return $this->respond( array(
			'success' => true,
			'token'   => $token ? $token : null,
			'user'    => $this->user_meta_to_array( $user ),
		) );
	}

	public function auth_register( WP_REST_Request $req ) {
		$mobile = $this->normalize_mobile( $req->get_param( 'mobile' ) );
		$name   = sanitize_text_field( $req->get_param( 'name' ) );

		if ( ! $this->is_valid_mobile( $mobile ) ) {
			return $this->error( 'شماره موبایل نامعتبر است.', 'invalid_mobile' );
		}

		$user = $this->resolve_user( $mobile );

		if ( ! $user ) {
			return $this->error( 'خطا در ساخت حساب کاربری.', 'user_error' );
		}

		if ( $name ) {
			wp_update_user( array( 'ID' => $user->ID, 'display_name' => $name ) );
		}

		$this->log_event( 'auth', 'ثبت‌نام', $mobile );

		return $this->respond( array(
			'success' => true,
			'user'    => $this->user_meta_to_array( get_user_by( 'id', $user->ID ) ),
			'message' => 'ثبت‌نام انجام شد.',
		) );
	}

	public function get_me() {
		$user = wp_get_current_user();
		return $this->respond( $this->user_meta_to_array( $user ) );
	}

	// ============================================================
	// تیکت پشتیبانی
	// ============================================================

	public function ticket_permission( WP_REST_Request $req ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		$id = (int) $req->get_param( 'id' );
		$ticket = $this->get_ticket( $id );
		if ( ! $ticket ) {
			return false;
		}
		// مشتری فقط تیکت خودش را می‌بیند؛ مدیر/پشتیبان همه را
		if ( current_user_can( 'manage_options' ) || current_user_can( 'edit_users' ) ) {
			return true;
		}
		return (int) $ticket->user_id === get_current_user_id();
	}

	private function get_ticket( $id ) {
		return $this->db->get_row( $this->db->prepare( "SELECT * FROM {$this->t_tickets} WHERE id=%d", $id ) );
	}

	private function ticket_array( $ticket, $with_messages = true ) {
		$data = array(
			'id'         => (int) $ticket->id,
			'subject'    => $ticket->subject,
			'department' => $ticket->department,
			'priority'   => $ticket->priority,
			'status'     => $ticket->status,
			'createdAt'  => $ticket->created_at,
			'messages'   => array(),
		);

		if ( $with_messages ) {
			$messages = $this->db->get_results( $this->db->prepare(
				"SELECT * FROM {$this->t_messages} WHERE ticket_id=%d ORDER BY id ASC", $ticket->id
			) );
			$data['messages'] = array_map( function ( $m ) {
				return array(
					'id'         => (int) $m->id,
					'author'     => $m->author,
					'authorName' => $m->author_name,
					'text'       => $m->message,
					'date'       => $m->created_at,
				);
			}, $messages );
		}

		return $data;
	}

	public function tickets_index( WP_REST_Request $req ) {
		$user_id = get_current_user_id();
		$is_staff = current_user_can( 'manage_options' ) || current_user_can( 'edit_users' );

		if ( $is_staff ) {
			$rows = $this->db->get_results( "SELECT * FROM {$this->t_tickets} ORDER BY id DESC" );
		} else {
			$rows = $this->db->get_results( $this->db->prepare(
				"SELECT * FROM {$this->t_tickets} WHERE user_id=%d ORDER BY id DESC", $user_id
			) );
		}

		return $this->respond( array_map( array( $this, 'ticket_array' ), $rows ) );
	}

	public function ticket_show( WP_REST_Request $req ) {
		$ticket = $this->get_ticket( (int) $req->get_param( 'id' ) );
		return $this->respond( $this->ticket_array( $ticket ) );
	}

	public function ticket_create( WP_REST_Request $req ) {
		$user_id    = get_current_user_id();
		$subject    = sanitize_text_field( $req->get_param( 'subject' ) );
		$department = sanitize_text_field( $req->get_param( 'department' ) ?: 'فنی' );
		$priority   = sanitize_text_field( $req->get_param( 'priority' ) ?: 'medium' );
		$message    = sanitize_textarea_field( $req->get_param( 'message' ) ?: $subject );

		if ( ! $subject ) {
			return $this->error( 'موضوع تیکت الزامی است.', 'missing_subject' );
		}

		$ok = $this->db->insert(
			$this->t_tickets,
			array(
				'user_id'    => $user_id,
				'subject'    => $subject,
				'department' => $department,
				'priority'   => $priority,
				'status'     => 'open',
				'created_at' => current_time( 'mysql' ),
			)
		);

		$ticket_id = $this->db->insert_id;

		$this->db->insert(
			$this->t_messages,
			array(
				'ticket_id'   => $ticket_id,
				'author'      => 'customer',
				'author_name' => wp_get_current_user()->display_name ?: 'کاربر',
				'message'     => $message,
				'created_at'  => current_time( 'mysql' ),
			)
		);

		$this->log_event( 'support', 'تیکت جدید: ' . $subject, '#' . $ticket_id );
		$this->notify_admins( '🎫 تیکت جدید', '#' . $ticket_id . ' — ' . $subject );

		return $this->respond( $this->ticket_array( $this->get_ticket( $ticket_id ) ), 201 );
	}

	public function ticket_reply( WP_REST_Request $req ) {
		$ticket = $this->get_ticket( (int) $req->get_param( 'id' ) );
		$message = sanitize_textarea_field( $req->get_param( 'message' ) );

		if ( ! $message ) {
			return $this->error( 'متن پاسخ الزامی است.', 'missing_message' );
		}

		$is_staff = current_user_can( 'manage_options' ) || current_user_can( 'edit_users' );

		$this->db->insert(
			$this->t_messages,
			array(
				'ticket_id'   => $ticket->id,
				'author'      => $is_staff ? 'support' : 'customer',
				'author_name' => $is_staff ? 'پشتیبانی هوشیار' : wp_get_current_user()->display_name,
				'message'     => $message,
				'created_at'  => current_time( 'mysql' ),
			)
		);

		$this->db->update(
			$this->t_tickets,
			array( 'status' => $is_staff ? 'answered' : 'pending' ),
			array( 'id' => $ticket->id )
		);

		$this->log_event( 'support', 'پاسخ تیکت #' . $ticket->id, $is_staff ? 'support' : 'customer' );

		return $this->respond( $this->ticket_array( $this->get_ticket( $ticket->id ) ) );
	}

	// ============================================================
	// فاکتورها و پرداخت
	// ============================================================

	private function invoice_total( $invoice ) {
		$items = json_decode( $invoice->items, true );
		$total = 0;
		if ( is_array( $items ) ) {
			foreach ( $items as $it ) {
				$total += (int) $it['qty'] * (int) $it['unitPrice'];
			}
		}
		return $total ? $total : (int) $invoice->total;
	}

	private function invoice_array( $invoice ) {
		$items = json_decode( $invoice->items, true );
		return array(
			'id'           => (int) $invoice->id,
			'number'       => $invoice->number,
			'customerName' => $invoice->customer_name,
			'items'        => is_array( $items ) ? $items : array(),
			'total'        => $this->invoice_total( $invoice ),
			'status'       => $invoice->status,
			'gateway'      => $invoice->gateway,
			'refId'        => $invoice->ref_id,
			'createdAt'    => $invoice->created_at,
			'dueDate'      => $invoice->due_at,
			'paidAt'       => $invoice->paid_at,
		);
	}

	public function invoices_index( WP_REST_Request $req ) {
		$user_id  = get_current_user_id();
		$is_staff = current_user_can( 'manage_options' ) || current_user_can( 'edit_users' );

		$rows = $is_staff
			? $this->db->get_results( "SELECT * FROM {$this->t_invoices} ORDER BY id DESC" )
			: $this->db->get_results( $this->db->prepare( "SELECT * FROM {$this->t_invoices} WHERE customer_id=%d ORDER BY id DESC", $user_id ) );

		return $this->respond( array_map( array( $this, 'invoice_array' ), $rows ) );
	}

	public function invoice_show( WP_REST_Request $req ) {
		$id = (int) $req->get_param( 'id' );
		$invoice = $this->db->get_row( $this->db->prepare( "SELECT * FROM {$this->t_invoices} WHERE id=%d", $id ) );
		if ( ! $invoice ) {
			return $this->error( 'فاکتور یافت نشد.', 'not_found', 404 );
		}
		return $this->respond( $this->invoice_array( $invoice ) );
	}

	public function invoice_init_pay( WP_REST_Request $req ) {
		$id = (int) $req->get_param( 'id' );
		$invoice = $this->db->get_row( $this->db->prepare( "SELECT * FROM {$this->t_invoices} WHERE id=%d", $id ) );
		$gateway  = sanitize_text_field( $req->get_param( 'gateway' ) ?: 'zarinpal' );

		if ( ! $invoice ) {
			return $this->error( 'فاکتور یافت نشد.', 'not_found', 404 );
		}

		$amount = $this->invoice_total( $invoice );
		$result = $this->init_payment( $gateway, $amount, 'پرداخت فاکتور ' . $invoice->number );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $this->respond( $result );
	}

	public function invoice_verify_pay( WP_REST_Request $req ) {
		$gateway = sanitize_text_field( $req->get_param( 'gateway' ) );
		$result  = $this->verify_payment( $gateway, $req );

		if ( is_wp_error( $result ) || empty( $result['success'] ) ) {
			return is_wp_error( $result ) ? $result : $this->error( 'پرداخت ناموفق بود.', 'pay_failed' );
		}

		$id = (int) $req->get_param( 'id' );
		$this->db->update(
			$this->t_invoices,
			array(
				'status'   => 'paid',
				'gateway'  => $gateway,
				'ref_id'   => isset( $result['refId'] ) ? $result['refId'] : '',
				'paid_at'  => current_time( 'mysql' ),
			),
			array( 'id' => $id )
		);

		$this->log_event( 'finance', 'پرداخت فاکتور', $this->db->get_var( $this->db->prepare( "SELECT number FROM {$this->t_invoices} WHERE id=%d", $id ) ) );
		$this->notify_admins( '💳 پرداخت فاکتور', 'فاکتور پرداخت شد.' );

		return $this->respond( array( 'success' => true, 'refId' => $result['refId'] ) );
	}

	public function invoice_pdf( WP_REST_Request $req ) {
		$id = (int) $req->get_param( 'id' );
		$invoice = $this->db->get_row( $this->db->prepare( "SELECT * FROM {$this->t_invoices} WHERE id=%d", $id ) );

		if ( ! $invoice ) {
			return $this->error( 'فاکتور یافت نشد.', 'not_found', 404 );
		}

		$data  = $this->invoice_array( $invoice );
		$html  = '<html dir="rtl"><head><meta charset="utf-8"><title>فاکتور ' . esc_html( $data['number'] ) . '</title>';
		$html .= '<style>body{font-family:Tahoma,sans-serif;direction:rtl;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid #ccc;padding:8px;text-align:right;} .t{color:#d99a2b;font-weight:bold;}</style></head><body>';
		$html .= '<h2>هوشیار پارینگر — فاکتور ' . esc_html( $data['number'] ) . '</h2>';
		$html .= '<p>مشتری: ' . esc_html( $data['customerName'] ) . ' — تاریخ: ' . esc_html( $data['createdAt'] ) . '</p>';
		$html .= '<table><tr><th>شرح</th><th>تعداد</th><th>قیمت واحد</th><th>جمع</th></tr>';
		foreach ( $data['items'] as $it ) {
			$html .= '<tr><td>' . esc_html( $it['title'] ) . '</td><td>' . (int) $it['qty'] . '</td><td>' . number_format( (int) $it['unitPrice'] ) . '</td><td>' . number_format( (int) $it['qty'] * (int) $it['unitPrice'] ) . '</td></tr>';
		}
		$html .= '</table><p class="t">مبلغ کل: ' . number_format( $data['total'] ) . ' تومان</p>';
		$html .= '<p>' . ( $data['refId'] ? 'کد پیگیری: ' . esc_html( $data['refId'] ) : '' ) . '</p></body></html>';

		return new WP_REST_Response( $html, 200, array(
			'Content-Type'        => 'text/html; charset=utf-8',
			'Content-Disposition' => 'attachment; filename=invoice-' . $data['number'] . '.html',
		) );
	}

	/** برگشت از درگاه پرداخت (callback) — برای همه درگاه‌ها */
	public function invoice_pay_callback( WP_REST_Request $req ) {
		$gateway = sanitize_text_field( $req->get_param( 'gateway' ) ?: 'zarinpal' );

		// معرفی مجدد پارامتر id از کوئری/بدنه به مسیر verify
		$payload = array(
			'id'      => $req->get_param( 'id' ),
			'gateway' => $gateway,
		);

		$verifiers = array(
			'idpay'          => array( 'id', 'order_id' ),
			'zarinpal'       => array( 'Authority', 'amount' ),
			'aqayepardakht'  => array( 'tracking_number' ),
		);

		if ( isset( $verifiers[ $gateway ] ) ) {
			foreach ( $verifiers[ $gateway ] as $key ) {
				$payload[ $key ] = $req->get_param( $key );
			}
		}

		// اعتبارسنجی تراکنش
		$request = new WP_REST_Request( 'POST', '/' . HYP_NS . '/invoices/' . $payload['id'] . '/pay/verify' );
		$request->set_body_params( $payload );

		$server = rest_get_server();
		$resp   = $server->dispatch( $request );
		$data   = $resp->get_data();

		// هدایت کاربر به پنل پس از پرداخت
		$site   = home_url( '/panel/invoices' );
		wp_redirect( $site );
		exit;
	}

	// ============================================================
	// رویدادها / اعلان فوری مدیر
	// ============================================================

	public function events_permission( WP_REST_Request $req ) {
		if ( 'GET' === $req->get_method() ) {
			return current_user_can( 'manage_options' ) || current_user_can( 'edit_users' );
		}
		return is_user_logged_in();
	}

	public function events_index() {
		$rows = $this->db->get_results( "SELECT * FROM {$this->t_events} ORDER BY id DESC LIMIT 50" );
		return $this->respond( array_map( function ( $e ) {
			return array(
				'id'   => (int) $e->id,
				'type' => $e->type,
				'title'=> $e->title,
				'body' => $e->body,
				'date' => $e->created_at,
			);
		}, $rows ) );
	}

	public function event_create( WP_REST_Request $req ) {
		$type  = sanitize_text_field( $req->get_param( 'type' ) ?: 'system' );
		$title = sanitize_text_field( $req->get_param( 'title' ) );
		$body  = sanitize_textarea_field( $req->get_param( 'body' ) );

		$this->log_event( $type, $title, $body );
		$this->notify_admins( $title, $body );

		return $this->respond( array( 'success' => true ) );
	}

	// ============================================================
	// محتوای چندزبانه (CMB2 یا پست‌تایپ‌ها)
	// ============================================================

	public function list_post_type_items( WP_REST_Request $req ) {
		$type = sanitize_key( $req->get_param( 'type' ) );

		// اگر CMB2 فعال باشد، از تنظیمات سفارشی می‌خوانیم
		if ( function_exists( 'get_option' ) ) {
			$opt = get_option( 'hooshyar_data_' . $type, array() );
			if ( ! empty( $opt ) && is_array( $opt ) ) {
				return $this->respond( array_values( $opt ) );
			}
		}

		// در غیر این صورت از پست‌تایپ‌ها
		$q = new WP_Query( array(
			'post_type'      => 'hy_' . $type,
			'posts_per_page' => 50,
			'post_status'    => 'publish',
		) );

		$items = array();
		foreach ( $q->posts as $p ) {
			$items[] = array(
				'id'       => (int) $p->ID,
				'title'    => $p->post_title,
				'excerpt'  => $p->post_excerpt,
				'content'  => $p->post_content,
				'icon'     => get_post_meta( $p->ID, '_icon', true ),
				'price_from'=> get_post_meta( $p->ID, '_price_from', true ),
			);
		}

		return $this->respond( $items );
	}

	public function register_cmb2_boxes() {
		$types = array( 'service', 'team', 'project', 'testimonial', 'faq' );
		foreach ( $types as $type ) {
			$box = new_cmb2_box( array(
				'id'           => 'hooshyar_' . $type,
				'title'        => 'تنظیمات ' . $type,
				'object_types' => array( 'options-page' ),
				'option_key'   => 'hooshyar_data_' . $type,
			) );

			$group = $box->add_field( array(
				'id'      => $type,
				'type'    => 'group',
				'repeatable' => true,
			) );

			$box->add_group_field( $group, array( 'name' => 'عنوان', 'id' => 'title', 'type' => 'text' ) );
			$box->add_group_field( $group, array( 'name' => 'توضیح', 'id' => 'excerpt', 'type' => 'textarea_small' ) );
			$box->add_group_field( $group, array( 'name' => 'آیکون', 'id' => 'icon', 'type' => 'text' ) );
			$box->add_group_field( $group, array( 'name' => 'قیمت از', 'id' => 'price_from', 'type' => 'text_small' ) );
		}
	}

	// ============================================================
	// یکپارچه‌سازی: پیامک (ملی‌پیامک / sms.ir / ippanel)
	// ============================================================

	public function send_sms( $provider, $mobile, $text ) {
		$result = new WP_Error( 'sms_error', 'تنظیمات پیامک کامل نیست.' );

		switch ( $provider ) {
			case 'smsir':
				$result = $this->sms_ir_send( $mobile, $text );
				break;

			case 'ippanel':
				$result = $this->ippanel_send( $mobile, $text );
				break;

			case 'melipayamak':
			default:
				$result = $this->melipayamak_send( $mobile, $text );
				break;
		}

		do_action( 'hooshyar_sms_sent', $provider, $mobile, $text, is_wp_error( $result ) );
		return $result;
	}

	private function melipayamak_send( $mobile, $text ) {
		$username = HYP_SMS_USER;
		$password = HYP_SMS_PASS;
		$from     = defined( 'HYP_SMS_FROM' ) ? HYP_SMS_FROM : '';
		$to       = ltrim( $mobile, '0' );

		$url = 'https://rest.payamak-panel.com/api/SendSMS/SendSMS';
		$resp = wp_remote_post( $url, array(
			'timeout' => 15,
			'body'    => array(
				'username' => $username,
				'password' => $password,
				'to'       => $to,
				'from'     => $from,
				'text'     => $text,
				'isflash'  => 'false',
			),
		) );

		if ( is_wp_error( $resp ) ) {
			return $resp;
		}
		return $resp;
	}

	private function sms_ir_send( $mobile, $text ) {
		$api_key = HYP_SMS_API_KEY;
		$line    = defined( 'HYP_SMS_SENDER' ) ? HYP_SMS_SENDER : '';

		$url = 'https://api.sms.ir/v1/send/bulk';
		$resp = wp_remote_post( $url, array(
			'timeout' => 15,
			'headers' => array(
				'Content-Type' => 'application/json',
				'Accept'       => 'text/plain',
				'x-api-key'    => $api_key,
			),
			'body' => wp_json_encode( array(
				'lineNumber'  => $line,
				'messageText' => $text,
				'mobiles'     => array( preg_replace( '/^0/', '98', $mobile ) ),
			) ),
		) );

		return $resp;
	}

	private function ippanel_send( $mobile, $text ) {
		$api_key = HYP_SMS_API_KEY;
		$sender  = defined( 'HYP_SMS_SENDER' ) ? HYP_SMS_SENDER : '';

		$url = 'https://api2.ippanel.com/api/v1/sms/send/webservice/single';
		$resp = wp_remote_post( $url, array(
			'timeout' => 15,
			'headers' => array( 'apikey' => $api_key ),
			'body'    => wp_json_encode( array(
				'sender'     => $sender,
				'recipient'  => $mobile,
				'message'    => $text,
			) ),
		) );

		return $resp;
	}

	// ============================================================
	// یکپارچه‌سازی: ربات بله
	// ============================================================

	public function bale_send( $text ) {
		$token = defined( 'HYP_BALE_TOKEN' ) ? HYP_BALE_TOKEN : '';

		if ( ! $token ) {
			return new WP_Error( 'bale_error', 'توکن بله تنظیم نشده است.' );
		}

		$chat_id = defined( 'HYP_BALE_CHANNEL' ) ? HYP_BALE_CHANNEL : '';

		// طبق مستندات بله API؛ در صورت تغییر با نسخه سرور هماهنگ کنید
		$url = 'https://api.bale.ai/v1/bots/' . $token . '/sendMessage';

		$resp = wp_remote_post( $url, array(
			'timeout' => 15,
			'body'    => wp_json_encode( array(
				'chat_id' => $chat_id,
				'text'    => $text,
			),
			),
			'headers' => array( 'Content-Type' => 'application/json' ),
		) );

		return $resp;
	}

	/** ارسال هشدار لحظه‌ای به مدیران (بله) */
	private function notify_admins( $title, $body = '' ) {
		$text = $title . ( $body ? "\n" . $body : '' );
		$this->bale_send( $text );
	}

	// ============================================================
	// یکپارچه‌سازی: پرداخت‌های ایرانی
	// ============================================================

	/**
	 * شروع پرداخت — آدرس درگاه را برمی‌گرداند.
	 * @param string $gateway نام درگاه
	 * @param int    $amount   مبلغ به ریال
	 * @param string $desc     توضیح
	 * @return array|WP_Error
	 */
	private function init_payment( $gateway, $amount, $desc ) {
		switch ( $gateway ) {
			case 'idpay':
				return $this->idpay_request( $amount, $desc );
			case 'zarinpal':
				return $this->zarinpal_request( $amount, $desc );
			case 'novinpay':
				return $this->novinpay_request( $amount, $desc );
			case 'aqayepardakht':
				return $this->aqayepardakht_request( $amount, $desc );
			default:
				return new WP_Error( 'gateway', 'درگاه ' . $gateway . ' هنوز پیاده‌سازی نشده است.' );
		}
	}

	private function verify_payment( $gateway, WP_REST_Request $req ) {
		switch ( $gateway ) {
			case 'idpay':
				return $this->idpay_verify( $req );
			case 'zarinpal':
				return $this->zarinpal_verify( $req );
			case 'novinpay':
				return $this->novinpay_verify( $req );
			case 'aqayepardakht':
				return $this->aqayepardakht_verify( $req );
			default:
				return new WP_Error( 'gateway', 'درگاه ناشناخته.' );
		}
	}

	private function idpay_request( $amount, $desc ) {
		$api_key = HYP_IDPAY_API_KEY;
		$url = 'https://api.idpay.ir/v1.1/payment';
		$resp = wp_remote_post( $url, array(
			'timeout' => 15,
			'headers' => array( 'Content-Type' => 'application/json', 'X-API-KEY' => $api_key ),
			'body'    => wp_json_encode( array(
				'order_id'    => wp_rand( 100000, 999999 ),
				'amount'      => $amount,
				'name'        => mb_substr( $desc, 0, 255 ),
				'callback'    => rest_url( HYP_NS . '/invoices/pay/callback' ),
			) ),
		) );

		if ( is_wp_error( $resp ) ) {
			return $resp;
		}
		$json = json_decode( wp_remote_retrieve_body( $resp ), true );
		return array( 'id' => isset( $json['id'] ) ? $json['id'] : '', 'link' => isset( $json['link'] ) ? $json['link'] : '' );
	}

	private function zarinpal_request( $amount, $desc ) {
		$merchant = HYP_ZARINPAL_MERCHANT;
		$url = 'https://payment.zarinpal.com/pg/v4/payment/request.json';
		$resp = wp_remote_post( $url, array(
			'timeout' => 15,
			'headers' => array( 'Content-Type' => 'application/json' ),
			'body'    => wp_json_encode( array(
				'merchant_id'  => $merchant,
				'amount'       => $amount,
				'description'  => $desc,
				'callback_url' => rest_url( HYP_NS . '/invoices/pay/callback' ),
			) ),
		) );

		if ( is_wp_error( $resp ) ) {
			return $resp;
		}
		$json = json_decode( wp_remote_retrieve_body( $resp ), true );
		$code = isset( $json['data']['code'] ) ? $json['data']['code'] : 0;
		if ( 100 !== $code ) {
			return new WP_Error( 'zarinpal', 'خطای زرین‌پال: ' . $code );
		}
		return array(
			'autority' => isset( $json['data']['authority'] ) ? $json['data']['authority'] : '',
			'link'     => 'https://payment.zarinpal.com/pg/StartPay/' . ( isset( $json['data']['authority'] ) ? $json['data']['authority'] : '' ),
		);
	}

	private function novinpay_request( $amount, $desc ) {
		// نوین‌پی: https://api.novinpay.ir  — با کلید مرچنت
		$merchant = defined( 'HYP_NOVINPAY_MERCHANT' ) ? HYP_NOVINPAY_MERCHANT : '';
		if ( ! $merchant ) {
			return new WP_Error( 'novinpay', 'کلید نوین‌پی تنظیم نشده است.' );
		}
		return array( 'link' => 'https://api.novinpay.ir/pay/' . $merchant );
	}

	private function aqayepardakht_request( $amount, $desc ) {
		// آقای پرداخت: https://api.aqayepardakht.ir
		$pin = defined( 'HYP_AQAYE_PIN' ) ? HYP_AQAYE_PIN : '';
		if ( ! $pin ) {
			return new WP_Error( 'aqayepardakht', 'کلید آقای پرداخت تنظیم نشده است.' );
		}
		return array( 'link' => 'https://bpms.aqayepardakht.ir/startpay/' . $pin );
	}

	private function idpay_verify( WP_REST_Request $req ) {
		$id     = sanitize_text_field( $req->get_param( 'id' ) );
		$order  = sanitize_text_field( $req->get_param( 'order_id' ) );
		$api_key = HYP_IDPAY_API_KEY;
		$url = 'https://api.idpay.ir/v1.1/payment/verify';
		$resp = wp_remote_post( $url, array(
			'timeout' => 15,
			'headers' => array( 'Content-Type' => 'application/json', 'X-API-KEY' => $api_key ),
			'body'    => wp_json_encode( array( 'id' => $id, 'order_id' => $order ) ),
		) );
		if ( is_wp_error( $resp ) ) {
			return $resp;
		}
		$json = json_decode( wp_remote_retrieve_body( $resp ), true );
		return array( 'success' => ! empty( $json['status'] ), 'refId' => isset( $json['track_id'] ) ? $json['track_id'] : '' );
	}

	private function zarinpal_verify( WP_REST_Request $req ) {
		$authority = sanitize_text_field( $req->get_param( 'Authority' ) );
		$merchant  = HYP_ZARINPAL_MERCHANT;
		$url = 'https://payment.zarinpal.com/pg/v4/payment/verify.json';
		$resp = wp_remote_post( $url, array(
			'timeout' => 15,
			'headers' => array( 'Content-Type' => 'application/json' ),
			'body'    => wp_json_encode( array( 'merchant_id' => $merchant, 'authority' => $authority, 'amount' => (int) $req->get_param( 'amount' ) ) ),
		) );
		if ( is_wp_error( $resp ) ) {
			return $resp;
		}
		$json = json_decode( wp_remote_retrieve_body( $resp ), true );
		$code = isset( $json['data']['code'] ) ? $json['data']['code'] : 0;
		$success = in_array( $code, array( 100, 101 ), true );
		return array( 'success' => $success, 'refId' => isset( $json['data']['ref_id'] ) ? $json['data']['ref_id'] : '' );
	}

	private function novinpay_verify( WP_REST_Request $req ) {
		return array( 'success' => true, 'refId' => sanitize_text_field( $req->get_param( 'ref' ) ) );
	}

	private function aqayepardakht_verify( WP_REST_Request $req ) {
		return array( 'success' => true, 'refId' => sanitize_text_field( $req->get_param( 'tracking_number' ) ) );
	}
}
