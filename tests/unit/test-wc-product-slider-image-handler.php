<?php
/**
 * Class Test_WC_Product_Slider_Image_Handler
 *
 * Tests for image handling and optimization
 *
 * @package WC_Product_Slider
 */

namespace WC_Product_Slider\Tests;

use WC_Product_Slider\Core\WC_Product_Slider_Image_Handler;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test Image Handler class.
 *
 * Handles image upload, validation, optimization, and WooCommerce integration.
 */
class Test_WC_Product_Slider_Image_Handler extends TestCase {

	/**
	 * Test that image handler class exists.
	 */
	public function test_image_handler_class_exists() {
		$this->assertTrue(
			class_exists( 'WC_Product_Slider\Core\WC_Product_Slider_Image_Handler' ),
			'WC_Product_Slider_Image_Handler class should exist'
		);
	}

	/**
	 * Test get_allowed_mime_types method.
	 */
	public function test_get_allowed_mime_types() {
		$mimes = WC_Product_Slider_Image_Handler::get_allowed_mime_types();

		$this->assertIsArray( $mimes, 'Allowed mime types should be array' );
		$this->assertNotEmpty( $mimes, 'Should have allowed mime types' );

		// Should allow standard image formats.
		$this->assertContains( 'image/jpeg', $mimes );
		$this->assertContains( 'image/png', $mimes );
		$this->assertContains( 'image/webp', $mimes );
		$this->assertContains( 'image/avif', $mimes );
	}

	/**
	 * Test validate_mime_type method with valid types.
	 */
	public function test_validate_mime_type_valid() {
		$this->assertTrue(
			WC_Product_Slider_Image_Handler::validate_mime_type( 'image/jpeg' )
		);

		$this->assertTrue(
			WC_Product_Slider_Image_Handler::validate_mime_type( 'image/png' )
		);

		$this->assertTrue(
			WC_Product_Slider_Image_Handler::validate_mime_type( 'image/webp' )
		);
	}

	/**
	 * Test validate_mime_type method with invalid types.
	 */
	public function test_validate_mime_type_invalid() {
		$this->assertFalse(
			WC_Product_Slider_Image_Handler::validate_mime_type( 'image/svg+xml' ),
			'SVG should not be allowed (security risk)'
		);

		$this->assertFalse(
			WC_Product_Slider_Image_Handler::validate_mime_type( 'application/pdf' )
		);

		$this->assertFalse(
			WC_Product_Slider_Image_Handler::validate_mime_type( 'text/html' )
		);
	}

	/**
	 * Test get_max_upload_size method.
	 */
	public function test_get_max_upload_size() {
		$max_size = WC_Product_Slider_Image_Handler::get_max_upload_size();

		$this->assertIsInt( $max_size, 'Max size should be integer' );
		$this->assertGreaterThan( 0, $max_size, 'Max size should be positive' );
		$this->assertLessThanOrEqual(
			10 * 1024 * 1024,
			$max_size,
			'Max size should not exceed 10MB'
		);
	}

	/**
	 * Test validate_file_size method.
	 */
	public function test_validate_file_size() {
		// Valid size (1MB).
		$this->assertTrue(
			WC_Product_Slider_Image_Handler::validate_file_size( 1024 * 1024 )
		);

		// Too large (11MB).
		$this->assertFalse(
			WC_Product_Slider_Image_Handler::validate_file_size( 11 * 1024 * 1024 )
		);

		// Empty file.
		$this->assertFalse(
			WC_Product_Slider_Image_Handler::validate_file_size( 0 )
		);

		// Negative (should not happen but handle gracefully).
		$this->assertFalse(
			WC_Product_Slider_Image_Handler::validate_file_size( -100 )
		);
	}

	/**
	 * Test get_image_sizes method.
	 */
	public function test_get_image_sizes() {
		$sizes = WC_Product_Slider_Image_Handler::get_image_sizes();

		$this->assertIsArray( $sizes, 'Image sizes should be array' );
		$this->assertNotEmpty( $sizes, 'Should have image sizes' );

		// Should have required sizes.
		$this->assertArrayHasKey( 'thumbnail', $sizes );
		$this->assertArrayHasKey( 'medium', $sizes );
		$this->assertArrayHasKey( 'large', $sizes );
	}

	/**
	 * Test get_attachment_id_from_url method signature.
	 */
	public function test_get_attachment_id_from_url_method_exists() {
		$this->assertTrue(
			method_exists(
				WC_Product_Slider_Image_Handler::class,
				'get_attachment_id_from_url'
			),
			'Should have get_attachment_id_from_url method'
		);
	}

	/**
	 * Test generate_srcset method signature.
	 */
	public function test_generate_srcset_method_exists() {
		$this->assertTrue(
			method_exists(
				WC_Product_Slider_Image_Handler::class,
				'generate_srcset'
			),
			'Should have generate_srcset method'
		);
	}

	/**
	 * Test get_image_html method signature.
	 */
	public function test_get_image_html_method_exists() {
		$this->assertTrue(
			method_exists(
				WC_Product_Slider_Image_Handler::class,
				'get_image_html'
			),
			'Should have get_image_html method'
		);
	}

	/**
	 * Test validate_image_dimensions method.
	 */
	public function test_validate_image_dimensions() {
		// Valid dimensions.
		$this->assertTrue(
			WC_Product_Slider_Image_Handler::validate_image_dimensions( 800, 600 )
		);

		// Too small width.
		$this->assertFalse(
			WC_Product_Slider_Image_Handler::validate_image_dimensions( 50, 600 )
		);

		// Too small height.
		$this->assertFalse(
			WC_Product_Slider_Image_Handler::validate_image_dimensions( 800, 50 )
		);

		// Too large width.
		$this->assertFalse(
			WC_Product_Slider_Image_Handler::validate_image_dimensions( 10000, 600 )
		);

		// Too large height.
		$this->assertFalse(
			WC_Product_Slider_Image_Handler::validate_image_dimensions( 800, 10000 )
		);
	}

	/**
	 * Test get_placeholder_image method.
	 */
	public function test_get_placeholder_image() {
		$placeholder = WC_Product_Slider_Image_Handler::get_placeholder_image();

		$this->assertIsString( $placeholder, 'Placeholder should be string' );
		$this->assertNotEmpty( $placeholder, 'Placeholder should not be empty' );
		$this->assertStringContainsString(
			'placeholder',
			$placeholder,
			'Should contain "placeholder" in path'
		);
	}

	/**
	 * Test sanitize_alt_text method.
	 */
	public function test_sanitize_alt_text() {
		// Normal text.
		$this->assertEquals(
			'Product Image',
			WC_Product_Slider_Image_Handler::sanitize_alt_text( 'Product Image' )
		);

		// HTML should be stripped.
		$this->assertEquals(
			'Product Image',
			WC_Product_Slider_Image_Handler::sanitize_alt_text( '<script>alert("xss")</script>Product Image' )
		);

		// Empty should return default.
		$result = WC_Product_Slider_Image_Handler::sanitize_alt_text( '' );
		$this->assertNotEmpty( $result, 'Empty alt should get default' );
	}
}
