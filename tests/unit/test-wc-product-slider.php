<?php
/**
 * Class Test_WC_Product_Slider
 *
 * @package WC_Product_Slider
 */

namespace WC_Product_Slider\Tests;

use WC_Product_Slider\WC_Product_Slider;
use WC_Product_Slider\WC_Product_Slider_Loader;
use PHPUnit\Framework\TestCase;
use Yoast\PHPUnitPolyfills\TestCases\TestCase as PolyfillTestCase;

/**
 * Test core plugin class.
 */
class Test_WC_Product_Slider extends PolyfillTestCase {

	/**
	 * Instance of the plugin class.
	 *
	 * @var WC_Product_Slider
	 */
	protected $plugin;

	/**
	 * Set up before each test.
	 */
	public function set_up() {
		parent::set_up();
		$this->plugin = new WC_Product_Slider();
	}

	/**
	 * Test that plugin class exists.
	 */
	public function test_plugin_class_exists() {
		$this->assertTrue(
			class_exists( 'WC_Product_Slider\WC_Product_Slider' ),
			'WC_Product_Slider class should exist'
		);
	}

	/**
	 * Test that plugin can be instantiated.
	 */
	public function test_plugin_can_be_instantiated() {
		$this->assertInstanceOf(
			WC_Product_Slider::class,
			$this->plugin,
			'Plugin should be instance of WC_Product_Slider'
		);
	}

	/**
	 * Test get_plugin_name method.
	 */
	public function test_get_plugin_name() {
		$this->assertEquals(
			'woocommerce-product-slider',
			$this->plugin->get_plugin_name(),
			'Plugin name should be "woocommerce-product-slider"'
		);
	}

	/**
	 * Test get_version method.
	 */
	public function test_get_version() {
		$version = $this->plugin->get_version();

		$this->assertNotEmpty( $version, 'Version should not be empty' );
		$this->assertMatchesRegularExpression(
			'/^\d+\.\d+\.\d+$/',
			$version,
			'Version should follow semantic versioning (x.y.z)'
		);
	}

	/**
	 * Test get_loader method.
	 */
	public function test_get_loader() {
		$loader = $this->plugin->get_loader();

		$this->assertInstanceOf(
			WC_Product_Slider_Loader::class,
			$loader,
			'Loader should be instance of WC_Product_Slider_Loader'
		);
	}

	/**
	 * Test that loader is created during construction.
	 */
	public function test_loader_is_created() {
		$loader = $this->plugin->get_loader();

		$this->assertNotNull( $loader, 'Loader should not be null' );
	}

	/**
	 * Test run method exists.
	 */
	public function test_run_method_exists() {
		$this->assertTrue(
			method_exists( $this->plugin, 'run' ),
			'Plugin should have run method'
		);
	}
}
