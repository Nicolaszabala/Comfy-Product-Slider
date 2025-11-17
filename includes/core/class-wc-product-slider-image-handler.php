<?php
/**
 * Image Handler - Upload, Validation, Optimization
 *
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/includes/core
 * @since      1.0.0
 */

namespace WC_Product_Slider\Core;

/**
 * Image Handler class.
 *
 * Handles image upload, validation, optimization, and generation
 * of responsive images (srcset).
 *
 * Integrates with WordPress Media Library and WooCommerce product images.
 *
 * @since      1.0.0
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/includes/core
 */
class WC_Product_Slider_Image_Handler {

	/**
	 * Maximum upload size in bytes (5MB).
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    int
	 */
	const MAX_UPLOAD_SIZE = 5242880; // 5 * 1024 * 1024.

	/**
	 * Minimum image width in pixels.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    int
	 */
	const MIN_WIDTH = 100;

	/**
	 * Minimum image height in pixels.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    int
	 */
	const MIN_HEIGHT = 100;

	/**
	 * Maximum image width in pixels.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    int
	 */
	const MAX_WIDTH = 5000;

	/**
	 * Maximum image height in pixels.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    int
	 */
	const MAX_HEIGHT = 5000;

	/**
	 * Get allowed MIME types for upload.
	 *
	 * @since  1.0.0
	 * @return array Allowed MIME types.
	 */
	public static function get_allowed_mime_types() {
		return array(
			'image/jpeg',
			'image/jpg',
			'image/png',
			'image/webp',
			'image/avif',
			'image/gif',
		);
	}

	/**
	 * Validate MIME type.
	 *
	 * @since  1.0.0
	 * @param  string $mime_type MIME type to validate.
	 * @return bool True if allowed, false otherwise.
	 */
	public static function validate_mime_type( $mime_type ) {
		$allowed = self::get_allowed_mime_types();
		return in_array( $mime_type, $allowed, true );
	}

	/**
	 * Get maximum upload size.
	 *
	 * Returns the lower of plugin max and WordPress max.
	 *
	 * @since  1.0.0
	 * @return int Maximum upload size in bytes.
	 */
	public static function get_max_upload_size() {
		// Get WordPress max upload size.
		$wp_max = wp_max_upload_size();

		// Return the lower of the two.
		return min( self::MAX_UPLOAD_SIZE, $wp_max );
	}

	/**
	 * Validate file size.
	 *
	 * @since  1.0.0
	 * @param  int $size File size in bytes.
	 * @return bool True if valid, false otherwise.
	 */
	public static function validate_file_size( $size ) {
		$max = self::get_max_upload_size();

		return $size > 0 && $size <= $max;
	}

	/**
	 * Validate image dimensions.
	 *
	 * @since  1.0.0
	 * @param  int $width  Image width in pixels.
	 * @param  int $height Image height in pixels.
	 * @return bool True if valid, false otherwise.
	 */
	public static function validate_image_dimensions( $width, $height ) {
		return $width >= self::MIN_WIDTH
			&& $width <= self::MAX_WIDTH
			&& $height >= self::MIN_HEIGHT
			&& $height <= self::MAX_HEIGHT;
	}

	/**
	 * Get WordPress registered image sizes.
	 *
	 * @since  1.0.0
	 * @return array Image sizes.
	 */
	public static function get_image_sizes() {
		$sizes = array(
			'thumbnail' => array(
				'width'  => get_option( 'thumbnail_size_w', 150 ),
				'height' => get_option( 'thumbnail_size_h', 150 ),
				'crop'   => get_option( 'thumbnail_crop', 1 ),
			),
			'medium'    => array(
				'width'  => get_option( 'medium_size_w', 300 ),
				'height' => get_option( 'medium_size_h', 300 ),
				'crop'   => false,
			),
			'large'     => array(
				'width'  => get_option( 'large_size_w', 1024 ),
				'height' => get_option( 'large_size_h', 1024 ),
				'crop'   => false,
			),
		);

		return $sizes;
	}

	/**
	 * Get attachment ID from image URL.
	 *
	 * Useful for converting URLs back to attachment IDs.
	 *
	 * @since  1.0.0
	 * @param  string $url Image URL.
	 * @return int|false Attachment ID or false if not found.
	 */
	public static function get_attachment_id_from_url( $url ) {
		global $wpdb;

		// Try to find attachment by URL.
		$attachment_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE guid = %s AND post_type = 'attachment'",
				$url
			)
		);

		return $attachment_id ? (int) $attachment_id : false;
	}

	/**
	 * Generate srcset attribute for responsive images.
	 *
	 * @since  1.0.0
	 * @param  int $attachment_id Attachment ID.
	 * @return string Srcset attribute value.
	 */
	public static function generate_srcset( $attachment_id ) {
		return wp_get_attachment_image_srcset( $attachment_id, 'large' );
	}

	/**
	 * Get image HTML with responsive attributes.
	 *
	 * @since  1.0.0
	 * @param  int    $attachment_id Attachment ID.
	 * @param  string $size          Image size.
	 * @param  bool   $lazy_load     Enable lazy loading.
	 * @param  string $alt           Alt text.
	 * @return string Image HTML.
	 */
	public static function get_image_html( $attachment_id, $size = 'large', $lazy_load = true, $alt = '' ) {
		if ( empty( $attachment_id ) ) {
			return self::get_placeholder_image();
		}

		// Get alt text.
		if ( empty( $alt ) ) {
			$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		}

		// Sanitize alt text.
		$alt = self::sanitize_alt_text( $alt );

		// Build attributes.
		$attrs = array(
			'class'    => 'wc-slider-image',
			'alt'      => $alt,
			'decoding' => 'async',
		);

		// Add lazy loading.
		if ( $lazy_load ) {
			$attrs['loading'] = 'lazy';
		}

		return wp_get_attachment_image( $attachment_id, $size, false, $attrs );
	}

	/**
	 * Get placeholder image HTML.
	 *
	 * Used when no image is available.
	 *
	 * @since  1.0.0
	 * @return string Placeholder image HTML.
	 */
	public static function get_placeholder_image() {
		$placeholder_url = WC_PRODUCT_SLIDER_PLUGIN_URL . 'assets/images/placeholder.png';

		return sprintf(
			'<img src="%s" alt="%s" class="wc-slider-placeholder" loading="lazy" />',
			esc_url( $placeholder_url ),
			esc_attr__( 'Placeholder', 'woocommerce-product-slider' )
		);
	}

	/**
	 * Sanitize alt text.
	 *
	 * @since  1.0.0
	 * @param  string $alt Alt text.
	 * @return string Sanitized alt text.
	 */
	public static function sanitize_alt_text( $alt ) {
		$alt = sanitize_text_field( $alt );

		// Provide default if empty.
		if ( empty( $alt ) ) {
			$alt = __( 'Product Image', 'woocommerce-product-slider' );
		}

		return $alt;
	}

	/**
	 * Get WooCommerce product image ID.
	 *
	 * Helper to get product thumbnail ID.
	 *
	 * @since  1.0.0
	 * @param  int $product_id Product ID.
	 * @return int|false Image attachment ID or false.
	 */
	public static function get_product_image_id( $product_id ) {
		if ( ! function_exists( 'wc_get_product' ) ) {
			return false;
		}

		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return false;
		}

		return $product->get_image_id();
	}

	/**
	 * Validate uploaded file.
	 *
	 * Comprehensive validation for security.
	 *
	 * @since  1.0.0
	 * @param  array $file File array from $_FILES.
	 * @return true|\WP_Error True if valid, WP_Error if invalid.
	 */
	public static function validate_upload( $file ) {
		// Check if file exists.
		if ( empty( $file['tmp_name'] ) ) {
			return new \WP_Error(
				'no_file',
				__( 'No file uploaded.', 'woocommerce-product-slider' )
			);
		}

		// Check file size.
		if ( ! self::validate_file_size( $file['size'] ) ) {
			return new \WP_Error(
				'file_too_large',
				sprintf(
					/* translators: %s: Maximum file size in MB */
					__( 'File size must not exceed %s MB.', 'woocommerce-product-slider' ),
					number_format( self::get_max_upload_size() / 1024 / 1024, 2 )
				)
			);
		}

		// Check MIME type using finfo (security).
		if ( ! function_exists( 'finfo_open' ) ) {
			return new \WP_Error(
				'missing_finfo',
				__( 'Server configuration error (missing finfo).', 'woocommerce-product-slider' )
			);
		}

		$finfo     = finfo_open( FILEINFO_MIME_TYPE );
		$mime_type = finfo_file( $finfo, $file['tmp_name'] );
		finfo_close( $finfo );

		if ( ! self::validate_mime_type( $mime_type ) ) {
			return new \WP_Error(
				'invalid_file_type',
				__( 'Invalid file type. Only JPEG, PNG, WebP, AVIF, and GIF images are allowed.', 'woocommerce-product-slider' )
			);
		}

		// Get image info.
		$image_info = getimagesize( $file['tmp_name'] );

		if ( false === $image_info ) {
			return new \WP_Error(
				'invalid_image',
				__( 'File is not a valid image.', 'woocommerce-product-slider' )
			);
		}

		// Validate dimensions.
		list( $width, $height ) = $image_info;

		if ( ! self::validate_image_dimensions( $width, $height ) ) {
			return new \WP_Error(
				'invalid_dimensions',
				sprintf(
					/* translators: 1: Minimum width, 2: Maximum width, 3: Minimum height, 4: Maximum height */
					__( 'Image dimensions must be between %1$dx%3$d and %2$dx%4$d pixels.', 'woocommerce-product-slider' ),
					self::MIN_WIDTH,
					self::MAX_WIDTH,
					self::MIN_HEIGHT,
					self::MAX_HEIGHT
				)
			);
		}

		return true;
	}
}
