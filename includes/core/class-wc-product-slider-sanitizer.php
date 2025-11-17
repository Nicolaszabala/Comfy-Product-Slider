<?php
/**
 * Sanitization and Validation Layer
 *
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/includes/core
 * @since      1.0.0
 */

namespace WC_Product_Slider\Core;

/**
 * Sanitizer class.
 *
 * Provides comprehensive sanitization and validation for all plugin inputs.
 * Follows OWASP security best practices.
 *
 * All methods are static for easy use throughout the plugin.
 *
 * @since      1.0.0
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/includes/core
 */
class WC_Product_Slider_Sanitizer {

	/**
	 * Sanitize text field.
	 *
	 * Strips all HTML tags and special characters.
	 *
	 * @since  1.0.0
	 * @param  string $text Input text.
	 * @return string Sanitized text.
	 */
	public static function sanitize_text( $text ) {
		return sanitize_text_field( $text );
	}

	/**
	 * Sanitize HTML content.
	 *
	 * Allows safe HTML tags (p, strong, em, a, ul, ol, li, br).
	 * Strips dangerous tags and scripts.
	 *
	 * @since  1.0.0
	 * @param  string $html Input HTML.
	 * @return string Sanitized HTML.
	 */
	public static function sanitize_html( $html ) {
		$allowed_tags = array(
			'p'      => array(),
			'strong' => array(),
			'em'     => array(),
			'b'      => array(),
			'i'      => array(),
			'u'      => array(),
			'a'      => array(
				'href'   => array(),
				'title'  => array(),
				'target' => array(),
				'rel'    => array(),
			),
			'br'     => array(),
			'ul'     => array(),
			'ol'     => array(),
			'li'     => array(),
			'span'   => array(
				'class' => array(),
			),
		);

		return wp_kses( $html, $allowed_tags );
	}

	/**
	 * Sanitize URL.
	 *
	 * Validates and sanitizes URLs. Blocks dangerous protocols.
	 *
	 * @since  1.0.0
	 * @param  string $url Input URL.
	 * @return string Sanitized URL or empty string if invalid.
	 */
	public static function sanitize_url( $url ) {
		$url = esc_url_raw( $url );

		// Block dangerous protocols.
		$dangerous_protocols = array( 'javascript', 'data', 'vbscript' );

		foreach ( $dangerous_protocols as $protocol ) {
			if ( strpos( strtolower( $url ), $protocol . ':' ) === 0 ) {
				return '';
			}
		}

		return $url;
	}

	/**
	 * Sanitize integer (positive only).
	 *
	 * Converts to positive integer. Returns 0 if invalid or negative.
	 *
	 * @since  1.0.0
	 * @param  mixed $value Input value.
	 * @return int Positive integer or 0.
	 */
	public static function sanitize_integer( $value ) {
		$int = intval( $value );
		return max( 0, $int );
	}

	/**
	 * Sanitize boolean.
	 *
	 * Converts various formats to boolean.
	 *
	 * @since  1.0.0
	 * @param  mixed $value Input value.
	 * @return bool Boolean value.
	 */
	public static function sanitize_boolean( $value ) {
		return rest_sanitize_boolean( $value );
	}

	/**
	 * Sanitize hex color.
	 *
	 * Validates hex color format (#fff or #ffffff).
	 * Returns default (#ffffff) if invalid.
	 *
	 * @since  1.0.0
	 * @param  string $color Input color.
	 * @param  string $default Default color if invalid.
	 * @return string Sanitized hex color.
	 */
	public static function sanitize_hex_color( $color, $default = '#ffffff' ) {
		// Remove whitespace.
		$color = trim( $color );

		// Check if it's a valid hex color.
		if ( preg_match( '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color ) ) {
			return $color;
		}

		return $default;
	}

	/**
	 * Sanitize array of integers.
	 *
	 * Filters array to only positive integers.
	 *
	 * @since  1.0.0
	 * @param  array $array Input array.
	 * @return array Array of positive integers.
	 */
	public static function sanitize_array_of_integers( $array ) {
		if ( ! is_array( $array ) ) {
			return array();
		}

		// Convert all to positive integers (negatives become 0) and filter out zeros.
		$sanitized = array_map( array( __CLASS__, 'sanitize_integer' ), $array );
		$sanitized = array_filter( $sanitized );

		// Re-index array.
		return array_values( $sanitized );
	}

	/**
	 * Sanitize custom CSS.
	 *
	 * Removes dangerous content from CSS.
	 * Strips <script> tags and javascript: protocol.
	 *
	 * @since  1.0.0
	 * @param  string $css Input CSS.
	 * @return string Sanitized CSS.
	 */
	public static function sanitize_css( $css ) {
		// Strip all tags (especially <script>).
		$css = wp_strip_all_tags( $css );

		// Remove javascript: protocol from CSS.
		$css = preg_replace( '/javascript\s*:/i', '', $css );

		// Remove vbscript: protocol.
		$css = preg_replace( '/vbscript\s*:/i', '', $css );

		// Remove data: protocol (can be used for XSS).
		$css = preg_replace( '/data\s*:/i', '', $css );

		return $css;
	}

	/**
	 * Sanitize complete slider configuration.
	 *
	 * Sanitizes all fields in a slider config array.
	 *
	 * @since  1.0.0
	 * @param  array $config Input configuration.
	 * @return array Sanitized configuration.
	 */
	public static function sanitize_slider_config( $config ) {
		$defaults = array(
			'title'            => '',
			'description'      => '',
			'slides_visible'   => 3,
			'autoplay'         => false,
			'speed'            => 300,
			'bg_color'         => '#ffffff',
			'product_ids'      => array(),
			'link_url'         => '',
			'custom_css'       => '',
			'loop'             => true,
			'navigation'       => true,
			'pagination'       => true,
			'lazy_loading'     => true,
			'transition_speed' => 300,
		);

		// Merge with defaults.
		$config = wp_parse_args( $config, $defaults );

		// Sanitize each field.
		$sanitized = array(
			'title'            => self::sanitize_text( $config['title'] ),
			'description'      => self::sanitize_html( $config['description'] ),
			'slides_visible'   => self::sanitize_integer( $config['slides_visible'] ),
			'autoplay'         => self::sanitize_boolean( $config['autoplay'] ),
			'speed'            => self::sanitize_integer( $config['speed'] ),
			'bg_color'         => self::sanitize_hex_color( $config['bg_color'] ),
			'product_ids'      => self::sanitize_array_of_integers( $config['product_ids'] ),
			'link_url'         => self::sanitize_url( $config['link_url'] ),
			'custom_css'       => self::sanitize_css( $config['custom_css'] ),
			'loop'             => self::sanitize_boolean( $config['loop'] ),
			'navigation'       => self::sanitize_boolean( $config['navigation'] ),
			'pagination'       => self::sanitize_boolean( $config['pagination'] ),
			'lazy_loading'     => self::sanitize_boolean( $config['lazy_loading'] ),
			'transition_speed' => self::sanitize_integer( $config['transition_speed'] ),
		);

		// Validate ranges.
		if ( $sanitized['slides_visible'] < 1 ) {
			$sanitized['slides_visible'] = 1;
		}
		if ( $sanitized['slides_visible'] > 6 ) {
			$sanitized['slides_visible'] = 6;
		}

		if ( $sanitized['speed'] < 100 ) {
			$sanitized['speed'] = 100;
		}
		if ( $sanitized['speed'] > 10000 ) {
			$sanitized['speed'] = 10000;
		}

		if ( $sanitized['transition_speed'] < 100 ) {
			$sanitized['transition_speed'] = 100;
		}
		if ( $sanitized['transition_speed'] > 3000 ) {
			$sanitized['transition_speed'] = 3000;
		}

		return $sanitized;
	}
}
