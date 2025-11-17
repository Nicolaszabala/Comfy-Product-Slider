<?php
/**
 * Class Test_WC_Product_Slider_Activator
 *
 * @package WC_Product_Slider
 */

namespace WC_Product_Slider\Tests;

use WC_Product_Slider\WC_Product_Slider_Activator;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test activator class.
 */
class Test_WC_Product_Slider_Activator extends TestCase {

	/**
	 * Test that activator class exists.
	 */
	public function test_activator_class_exists() {
		$this->assertTrue(
			class_exists( 'WC_Product_Slider\WC_Product_Slider_Activator' ),
			'WC_Product_Slider_Activator class should exist'
		);
	}

	/**
	 * Test that activate method exists.
	 */
	public function test_activate_method_exists() {
		$this->assertTrue(
			method_exists( WC_Product_Slider_Activator::class, 'activate' ),
			'Activator should have static activate method'
		);
	}

	/**
	 * Test that activate method is static.
	 */
	public function test_activate_method_is_static() {
		$reflection = new \ReflectionMethod( WC_Product_Slider_Activator::class, 'activate' );
		$this->assertTrue(
			$reflection->isStatic(),
			'activate method should be static'
		);
	}
}
