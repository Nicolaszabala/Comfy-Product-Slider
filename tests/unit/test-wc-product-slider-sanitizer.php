<?php
/**
 * Class Test_WC_Product_Slider_Sanitizer
 *
 * Tests for sanitization and validation (Security Layer)
 *
 * @package WC_Product_Slider
 */

namespace WC_Product_Slider\Tests;

use WC_Product_Slider\Core\WC_Product_Slider_Sanitizer;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test Sanitizer class.
 *
 * Critical security component - must sanitize all inputs
 * following OWASP best practices.
 */
class Test_WC_Product_Slider_Sanitizer extends TestCase {

	/**
	 * Test that sanitizer class exists.
	 */
	public function test_sanitizer_class_exists() {
		$this->assertTrue(
			class_exists( 'WC_Product_Slider\Core\WC_Product_Slider_Sanitizer' ),
			'WC_Product_Slider_Sanitizer class should exist'
		);
	}

	/**
	 * Test sanitize_text_field method.
	 */
	public function test_sanitize_text_field() {
		// Test normal text.
		$this->assertEquals(
			'Hello World',
			WC_Product_Slider_Sanitizer::sanitize_text( 'Hello World' )
		);

		// Test HTML tags are stripped.
		$this->assertEquals(
			'Hello World',
			WC_Product_Slider_Sanitizer::sanitize_text( '<script>alert("xss")</script>Hello World' )
		);

		// Test special characters.
		$this->assertEquals(
			'Hello &amp; World',
			WC_Product_Slider_Sanitizer::sanitize_text( 'Hello & World' )
		);
	}

	/**
	 * Test sanitize_html method (allows safe HTML).
	 */
	public function test_sanitize_html() {
		// Test allowed tags (p, strong, em, a).
		$input = '<p>Hello <strong>World</strong></p>';
		$output = WC_Product_Slider_Sanitizer::sanitize_html( $input );

		$this->assertStringContainsString( '<p>', $output );
		$this->assertStringContainsString( '<strong>', $output );

		// Test dangerous tags are stripped.
		$input = '<script>alert("xss")</script><p>Safe content</p>';
		$output = WC_Product_Slider_Sanitizer::sanitize_html( $input );

		$this->assertStringNotContainsString( '<script>', $output );
		$this->assertStringContainsString( '<p>Safe content</p>', $output );
	}

	/**
	 * Test sanitize_url method.
	 */
	public function test_sanitize_url() {
		// Test valid URL.
		$this->assertEquals(
			'https://example.com',
			WC_Product_Slider_Sanitizer::sanitize_url( 'https://example.com' )
		);

		// Test URL with parameters.
		$this->assertEquals(
			'https://example.com?foo=bar&baz=qux',
			WC_Product_Slider_Sanitizer::sanitize_url( 'https://example.com?foo=bar&baz=qux' )
		);

		// Test dangerous URL schemes are blocked.
		$this->assertEquals(
			'',
			WC_Product_Slider_Sanitizer::sanitize_url( 'javascript:alert("xss")' )
		);

		// Test empty URL.
		$this->assertEquals(
			'',
			WC_Product_Slider_Sanitizer::sanitize_url( '' )
		);
	}

	/**
	 * Test sanitize_integer method.
	 */
	public function test_sanitize_integer() {
		// Test positive integer.
		$this->assertEquals(
			42,
			WC_Product_Slider_Sanitizer::sanitize_integer( 42 )
		);

		// Test string integer.
		$this->assertEquals(
			42,
			WC_Product_Slider_Sanitizer::sanitize_integer( '42' )
		);

		// Test negative integer becomes 0.
		$this->assertEquals(
			0,
			WC_Product_Slider_Sanitizer::sanitize_integer( -5 )
		);

		// Test non-integer.
		$this->assertEquals(
			0,
			WC_Product_Slider_Sanitizer::sanitize_integer( 'not a number' )
		);
	}

	/**
	 * Test sanitize_boolean method.
	 */
	public function test_sanitize_boolean() {
		// Test true.
		$this->assertTrue(
			WC_Product_Slider_Sanitizer::sanitize_boolean( true )
		);

		// Test false.
		$this->assertFalse(
			WC_Product_Slider_Sanitizer::sanitize_boolean( false )
		);

		// Test string 'true'.
		$this->assertTrue(
			WC_Product_Slider_Sanitizer::sanitize_boolean( 'true' )
		);

		// Test string '1'.
		$this->assertTrue(
			WC_Product_Slider_Sanitizer::sanitize_boolean( '1' )
		);

		// Test string 'false'.
		$this->assertFalse(
			WC_Product_Slider_Sanitizer::sanitize_boolean( 'false' )
		);

		// Test integer 1.
		$this->assertTrue(
			WC_Product_Slider_Sanitizer::sanitize_boolean( 1 )
		);
	}

	/**
	 * Test sanitize_hex_color method.
	 */
	public function test_sanitize_hex_color() {
		// Test valid 6-char hex.
		$this->assertEquals(
			'#ff0000',
			WC_Product_Slider_Sanitizer::sanitize_hex_color( '#ff0000' )
		);

		// Test valid 3-char hex.
		$this->assertEquals(
			'#f00',
			WC_Product_Slider_Sanitizer::sanitize_hex_color( '#f00' )
		);

		// Test uppercase.
		$this->assertEquals(
			'#FF0000',
			WC_Product_Slider_Sanitizer::sanitize_hex_color( '#FF0000' )
		);

		// Test invalid color returns default.
		$this->assertEquals(
			'#ffffff',
			WC_Product_Slider_Sanitizer::sanitize_hex_color( 'red' )
		);

		// Test missing hash.
		$this->assertEquals(
			'#ffffff',
			WC_Product_Slider_Sanitizer::sanitize_hex_color( 'ff0000' )
		);
	}

	/**
	 * Test sanitize_array_of_integers method.
	 */
	public function test_sanitize_array_of_integers() {
		// Test valid array.
		$this->assertEquals(
			array( 1, 2, 3 ),
			WC_Product_Slider_Sanitizer::sanitize_array_of_integers( array( 1, 2, 3 ) )
		);

		// Test mixed array (removes non-integers).
		$this->assertEquals(
			array( 1, 2, 3 ),
			WC_Product_Slider_Sanitizer::sanitize_array_of_integers( array( 1, 'two', 2, 'three', 3 ) )
		);

		// Test string integers.
		$this->assertEquals(
			array( 1, 2, 3 ),
			WC_Product_Slider_Sanitizer::sanitize_array_of_integers( array( '1', '2', '3' ) )
		);

		// Test negative integers removed.
		$this->assertEquals(
			array( 1, 2, 3 ),
			WC_Product_Slider_Sanitizer::sanitize_array_of_integers( array( 1, -2, 2, -3, 3 ) )
		);

		// Test empty array.
		$this->assertEquals(
			array(),
			WC_Product_Slider_Sanitizer::sanitize_array_of_integers( array() )
		);
	}

	/**
	 * Test sanitize_css method (for custom CSS).
	 */
	public function test_sanitize_css() {
		// Test valid CSS.
		$css = '.my-class { color: red; }';
		$output = WC_Product_Slider_Sanitizer::sanitize_css( $css );

		$this->assertStringContainsString( 'color: red', $output );

		// Test dangerous CSS is stripped (javascript:).
		$css = '.my-class { background: url(javascript:alert("xss")); }';
		$output = WC_Product_Slider_Sanitizer::sanitize_css( $css );

		$this->assertStringNotContainsString( 'javascript:', $output );

		// Test script tags in CSS.
		$css = '<script>alert("xss")</script>.my-class { color: red; }';
		$output = WC_Product_Slider_Sanitizer::sanitize_css( $css );

		$this->assertStringNotContainsString( '<script>', $output );
	}

	/**
	 * Test sanitize_slider_config method (complete config).
	 */
	public function test_sanitize_slider_config() {
		$input = array(
			'title'          => '<script>alert("xss")</script>My Slider',
			'description'    => '<p>Description</p><script>evil</script>',
			'slides_visible' => '3',
			'autoplay'       => 'true',
			'speed'          => '300',
			'bg_color'       => '#ff0000',
			'product_ids'    => array( '1', '2', 'invalid', '3' ),
			'link_url'       => 'javascript:alert("xss")',
			'custom_css'     => '.class { color: red; }',
		);

		$output = WC_Product_Slider_Sanitizer::sanitize_slider_config( $input );

		// Check text is sanitized.
		$this->assertEquals( 'My Slider', $output['title'] );

		// Check HTML is sanitized.
		$this->assertStringContainsString( '<p>Description</p>', $output['description'] );
		$this->assertStringNotContainsString( '<script>', $output['description'] );

		// Check integers.
		$this->assertEquals( 3, $output['slides_visible'] );
		$this->assertEquals( 300, $output['speed'] );

		// Check boolean.
		$this->assertTrue( $output['autoplay'] );

		// Check color.
		$this->assertEquals( '#ff0000', $output['bg_color'] );

		// Check array of integers.
		$this->assertEquals( array( 1, 2, 3 ), $output['product_ids'] );

		// Check dangerous URL blocked.
		$this->assertEquals( '', $output['link_url'] );

		// Check CSS.
		$this->assertStringContainsString( 'color: red', $output['custom_css'] );
	}

	/**
	 * Test sanitize_slider_config with missing fields.
	 */
	public function test_sanitize_slider_config_with_defaults() {
		$input = array(
			'title' => 'My Slider',
		);

		$output = WC_Product_Slider_Sanitizer::sanitize_slider_config( $input );

		// Check defaults are applied.
		$this->assertEquals( '', $output['description'] );
		$this->assertEquals( 3, $output['slides_visible'] );
		$this->assertFalse( $output['autoplay'] );
		$this->assertEquals( '#ffffff', $output['bg_color'] );
		$this->assertEquals( array(), $output['product_ids'] );
	}
}
