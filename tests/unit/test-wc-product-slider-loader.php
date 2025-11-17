<?php
/**
 * Class Test_WC_Product_Slider_Loader
 *
 * @package WC_Product_Slider
 */

namespace WC_Product_Slider\Tests;

use WC_Product_Slider\WC_Product_Slider_Loader;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test loader class.
 */
class Test_WC_Product_Slider_Loader extends TestCase {

	/**
	 * Instance of the loader.
	 *
	 * @var WC_Product_Slider_Loader
	 */
	protected $loader;

	/**
	 * Set up before each test.
	 */
	public function set_up() {
		parent::set_up();
		$this->loader = new WC_Product_Slider_Loader();
	}

	/**
	 * Test that loader class exists.
	 */
	public function test_loader_class_exists() {
		$this->assertTrue(
			class_exists( 'WC_Product_Slider\WC_Product_Slider_Loader' ),
			'WC_Product_Slider_Loader class should exist'
		);
	}

	/**
	 * Test that loader can be instantiated.
	 */
	public function test_loader_can_be_instantiated() {
		$this->assertInstanceOf(
			WC_Product_Slider_Loader::class,
			$this->loader,
			'Loader should be instance of WC_Product_Slider_Loader'
		);
	}

	/**
	 * Test add_action method exists.
	 */
	public function test_add_action_method_exists() {
		$this->assertTrue(
			method_exists( $this->loader, 'add_action' ),
			'Loader should have add_action method'
		);
	}

	/**
	 * Test add_filter method exists.
	 */
	public function test_add_filter_method_exists() {
		$this->assertTrue(
			method_exists( $this->loader, 'add_filter' ),
			'Loader should have add_filter method'
		);
	}

	/**
	 * Test run method exists.
	 */
	public function test_run_method_exists() {
		$this->assertTrue(
			method_exists( $this->loader, 'run' ),
			'Loader should have run method'
		);
	}
}
