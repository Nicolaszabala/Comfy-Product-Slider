<?php
/**
 * Class Test_CPT_Registration
 *
 * Integration test for Custom Post Type registration.
 *
 * @package WC_Product_Slider
 */

namespace WC_Product_Slider\Tests;

use WC_Product_Slider\WC_Product_Slider;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test CPT registration integration.
 *
 * Tests that the CPT is properly registered when the plugin runs.
 */
class Test_CPT_Registration extends TestCase {

	/**
	 * Plugin instance.
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
	 * Test plugin has CPT handler.
	 */
	public function test_plugin_has_cpt_handler() {
		$cpt = $this->plugin->get_cpt();

		$this->assertNotNull( $cpt, 'Plugin should have CPT handler' );
		$this->assertInstanceOf(
			'WC_Product_Slider\Core\WC_Product_Slider_CPT',
			$cpt,
			'CPT handler should be correct instance'
		);
	}

	/**
	 * Test CPT handler has correct post type.
	 */
	public function test_cpt_handler_has_correct_post_type() {
		$cpt = $this->plugin->get_cpt();

		$this->assertEquals(
			'wc_product_slider',
			$cpt->get_post_type(),
			'Post type should be wc_product_slider'
		);
	}

	/**
	 * Test plugin registers init hook for CPT.
	 */
	public function test_plugin_registers_cpt_on_init() {
		$loader = $this->plugin->get_loader();

		$this->assertNotNull( $loader, 'Loader should exist' );

		// Run the plugin to register hooks.
		$this->plugin->run();

		// Verify has_action returns priority (10 by default).
		// Note: This test requires WordPress to be loaded.
		// If running without WordPress, this will be skipped.
		if ( function_exists( 'has_action' ) ) {
			$cpt = $this->plugin->get_cpt();
			$has_hook = has_action( 'init', array( $cpt, 'register' ) );

			$this->assertNotFalse(
				$has_hook,
				'CPT register method should be hooked to init'
			);
		}
	}
}
