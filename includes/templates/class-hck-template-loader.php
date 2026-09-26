<?php
/**
 * Template loader utilities.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Template_Loader
 */
class HCK_Template_Loader {

	/**
	 * Get a layout/template name with fallbacks.
	 *
	 * @param string $group   Template group (cart, checkout, dashboard, ...).
	 * @param string $setting Setting key holding the template name.
	 * @return string
	 */
	public static function get_template_name( $group, $setting ) {
		$name = HCK_Helpers::get( $setting, 'modern' );
		$file = HCK_PLUGIN_DIR . 'templates/' . $group . '/' . $group . '-' . sanitize_file_name( $name ) . '.php';

		if ( ! is_readable( $file ) ) {
			return 'modern';
		}

		return sanitize_file_name( $name );
	}

	/**
	 * Print an opening wrapper with layout classes.
	 *
	 * @param string $base    Base class.
	 * @param string $extra   Extra class (template name).
	 * @param string $layout  Layout class.
	 * @param array  $extra_args Additional data attributes.
	 */
	public static function open_wrapper( $base, $extra = '', $layout = '', $extra_args = array() ) {
		$classes = array( 'hck-wrap', 'hck-' . $base );
		if ( $extra ) {
			$classes[] = 'hck-' . $base . '--' . $extra;
		}
		if ( $layout ) {
			$classes[] = 'hck-' . $base . '--layout-' . $layout;
		}

		printf(
			'<div class="%s"%s>',
			esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) ),
			self::data_attrs( $extra_args )
		);
	}

	/**
	 * Print data-* attributes.
	 *
	 * @param array $attrs Attributes.
	 * @return string
	 */
	public static function data_attrs( $attrs ) {
		$out = '';
		foreach ( (array) $attrs as $key => $value ) {
			$out .= sprintf( ' data-%s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}
		return $out;
	}

	/**
	 * Close wrapper.
	 */
	public static function close_wrapper() {
		echo '</div>';
	}
}
