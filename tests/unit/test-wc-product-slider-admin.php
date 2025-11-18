<?php
/**
 * Tests for WC_Product_Slider_Admin class
 *
 * @package WC_Product_Slider
 * @subpackage Tests
 */

namespace WC_Product_Slider\Tests;

use PHPUnit\Framework\TestCase;
use WC_Product_Slider\Admin\WC_Product_Slider_Admin;
use Yoast\PHPUnitPolyfills\TestCases\TestCase as Polyfill_TestCase;

/**
 * Test Admin class
 */
class Test_WC_Product_Slider_Admin extends Polyfill_TestCase {

	/**
	 * Admin instance
	 *
	 * @var WC_Product_Slider_Admin
	 */
	private $admin;

	/**
	 * Set up test
	 */
	public function set_up() {
		parent::set_up();
		$this->admin = new WC_Product_Slider_Admin( 'woocommerce-product-slider', '1.0.0' );
	}

	/**
	 * Test admin class exists
	 */
	public function test_admin_class_exists() {
		$this->assertTrue(
			class_exists( 'WC_Product_Slider\Admin\WC_Product_Slider_Admin' ),
			'Admin class should exist'
		);
	}

	/**
	 * Test admin can be instantiated
	 */
	public function test_admin_can_be_instantiated() {
		$this->assertInstanceOf(
			'WC_Product_Slider\Admin\WC_Product_Slider_Admin',
			$this->admin,
			'Should create admin instance'
		);
	}

	/**
	 * Test enqueue_styles method exists
	 */
	public function test_enqueue_styles_method_exists() {
		$this->assertTrue(
			method_exists( $this->admin, 'enqueue_styles' ),
			'Admin should have enqueue_styles method'
		);
	}

	/**
	 * Test enqueue_scripts method exists
	 */
	public function test_enqueue_scripts_method_exists() {
		$this->assertTrue(
			method_exists( $this->admin, 'enqueue_scripts' ),
			'Admin should have enqueue_scripts method'
		);
	}

	/**
	 * Test add_meta_boxes method exists
	 */
	public function test_add_meta_boxes_method_exists() {
		$this->assertTrue(
			method_exists( $this->admin, 'add_meta_boxes' ),
			'Admin should have add_meta_boxes method'
		);
	}

	/**
	 * Test save_meta_box method exists
	 */
	public function test_save_meta_box_method_exists() {
		$this->assertTrue(
			method_exists( $this->admin, 'save_meta_box' ),
			'Admin should have save_meta_box method'
		);
	}

	/**
	 * Test get_plugin_name returns correct value
	 */
	public function test_get_plugin_name() {
		$this->assertEquals(
			'woocommerce-product-slider',
			$this->admin->get_plugin_name(),
			'Should return correct plugin name'
		);
	}

	/**
	 * Test get_version returns correct value
	 */
	public function test_get_version() {
		$this->assertEquals(
			'1.0.0',
			$this->admin->get_version(),
			'Should return correct version'
		);
	}
}
