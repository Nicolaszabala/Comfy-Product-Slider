<?php
/**
 * Class Test_WC_Product_Slider_Deactivator
 *
 * @package WC_Product_Slider
 */

namespace WC_Product_Slider\Tests;

use WC_Product_Slider\WC_Product_Slider_Deactivator;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test deactivator class.
 */
class Test_WC_Product_Slider_Deactivator extends TestCase {

	/**
	 * Test that deactivator class exists.
	 */
	public function test_deactivator_class_exists() {
		$this->assertTrue(
			class_exists( 'WC_Product_Slider\WC_Product_Slider_Deactivator' ),
			'WC_Product_Slider_Deactivator class should exist'
		);
	}

	/**
	 * Test that deactivate method exists.
	 */
	public function test_deactivate_method_exists() {
		$this->assertTrue(
			method_exists( WC_Product_Slider_Deactivator::class, 'deactivate' ),
			'Deactivator should have static deactivate method'
		);
	}

	/**
	 * Test that deactivate method is static.
	 */
	public function test_deactivate_method_is_static() {
		$reflection = new \ReflectionMethod( WC_Product_Slider_Deactivator::class, 'deactivate' );
		$this->assertTrue(
			$reflection->isStatic(),
			'deactivate method should be static'
		);
	}
}
