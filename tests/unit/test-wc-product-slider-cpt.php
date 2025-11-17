<?php
/**
 * Class Test_WC_Product_Slider_CPT
 *
 * Tests for Custom Post Type registration
 *
 * @package WC_Product_Slider
 */

namespace WC_Product_Slider\Tests;

use WC_Product_Slider\Core\WC_Product_Slider_CPT;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test Custom Post Type class.
 *
 * Following TDD methodology:
 * 1. Write failing test (RED)
 * 2. Write minimal code to pass (GREEN)
 * 3. Refactor (REFACTOR)
 */
class Test_WC_Product_Slider_CPT extends TestCase {

	/**
	 * Instance of CPT class.
	 *
	 * @var WC_Product_Slider_CPT
	 */
	protected $cpt;

	/**
	 * Set up before each test.
	 */
	public function set_up() {
		parent::set_up();
		$this->cpt = new WC_Product_Slider_CPT();
	}

	/**
	 * Test that CPT class exists.
	 */
	public function test_cpt_class_exists() {
		$this->assertTrue(
			class_exists( 'WC_Product_Slider\Core\WC_Product_Slider_CPT' ),
			'WC_Product_Slider_CPT class should exist'
		);
	}

	/**
	 * Test that CPT can be instantiated.
	 */
	public function test_cpt_can_be_instantiated() {
		$this->assertInstanceOf(
			WC_Product_Slider_CPT::class,
			$this->cpt,
			'CPT should be instance of WC_Product_Slider_CPT'
		);
	}

	/**
	 * Test get_post_type method returns correct slug.
	 */
	public function test_get_post_type_returns_slug() {
		$this->assertEquals(
			'wc_product_slider',
			$this->cpt->get_post_type(),
			'Post type slug should be "wc_product_slider"'
		);
	}

	/**
	 * Test get_labels method returns array.
	 */
	public function test_get_labels_returns_array() {
		$labels = $this->cpt->get_labels();

		$this->assertIsArray( $labels, 'Labels should be an array' );
		$this->assertNotEmpty( $labels, 'Labels should not be empty' );
	}

	/**
	 * Test labels contain required keys.
	 */
	public function test_labels_contain_required_keys() {
		$labels = $this->cpt->get_labels();

		$required_keys = array(
			'name',
			'singular_name',
			'add_new',
			'add_new_item',
			'edit_item',
			'new_item',
			'view_item',
			'search_items',
			'not_found',
			'not_found_in_trash',
		);

		foreach ( $required_keys as $key ) {
			$this->assertArrayHasKey(
				$key,
				$labels,
				sprintf( 'Labels should contain key "%s"', $key )
			);
		}
	}

	/**
	 * Test get_args method returns array.
	 */
	public function test_get_args_returns_array() {
		$args = $this->cpt->get_args();

		$this->assertIsArray( $args, 'Args should be an array' );
		$this->assertNotEmpty( $args, 'Args should not be empty' );
	}

	/**
	 * Test args contain required configuration.
	 */
	public function test_args_contain_required_config() {
		$args = $this->cpt->get_args();

		// Should not be public
		$this->assertFalse(
			$args['public'],
			'Post type should not be public'
		);

		// Should have admin UI
		$this->assertTrue(
			$args['show_ui'],
			'Post type should have admin UI'
		);

		// Should be in menu
		$this->assertTrue(
			$args['show_in_menu'],
			'Post type should be in admin menu'
		);

		// Should support REST API (for Gutenberg)
		$this->assertTrue(
			$args['show_in_rest'],
			'Post type should support REST API'
		);

		// Should have correct capability type
		$this->assertEquals(
			'post',
			$args['capability_type'],
			'Capability type should be "post"'
		);
	}

	/**
	 * Test supports configuration.
	 */
	public function test_supports_configuration() {
		$args = $this->cpt->get_args();

		$this->assertIsArray(
			$args['supports'],
			'Supports should be an array'
		);

		$this->assertContains(
			'title',
			$args['supports'],
			'Should support title'
		);

		// Should NOT support editor (we use custom metaboxes)
		$this->assertNotContains(
			'editor',
			$args['supports'],
			'Should not support editor'
		);
	}

	/**
	 * Test register method exists.
	 */
	public function test_register_method_exists() {
		$this->assertTrue(
			method_exists( $this->cpt, 'register' ),
			'CPT should have register method'
		);
	}

	/**
	 * Test menu icon configuration.
	 */
	public function test_menu_icon_is_configured() {
		$args = $this->cpt->get_args();

		$this->assertArrayHasKey(
			'menu_icon',
			$args,
			'Args should contain menu_icon'
		);

		$this->assertEquals(
			'dashicons-slides',
			$args['menu_icon'],
			'Menu icon should be "dashicons-slides"'
		);
	}

	/**
	 * Test menu position is below WooCommerce.
	 */
	public function test_menu_position_below_woocommerce() {
		$args = $this->cpt->get_args();

		$this->assertArrayHasKey(
			'menu_position',
			$args,
			'Args should contain menu_position'
		);

		// WooCommerce is at 55, we should be at 56
		$this->assertEquals(
			56,
			$args['menu_position'],
			'Menu position should be 56 (below WooCommerce)'
		);
	}

	/**
	 * Test rewrite configuration.
	 */
	public function test_rewrite_configuration() {
		$args = $this->cpt->get_args();

		$this->assertArrayHasKey(
			'rewrite',
			$args,
			'Args should contain rewrite'
		);

		$this->assertIsArray(
			$args['rewrite'],
			'Rewrite should be an array'
		);

		$this->assertArrayHasKey(
			'slug',
			$args['rewrite'],
			'Rewrite should contain slug'
		);

		$this->assertEquals(
			'product-slider',
			$args['rewrite']['slug'],
			'Rewrite slug should be "product-slider"'
		);
	}

	/**
	 * Test hierarchical is false.
	 */
	public function test_not_hierarchical() {
		$args = $this->cpt->get_args();

		$this->assertFalse(
			$args['hierarchical'],
			'Post type should not be hierarchical'
		);
	}

	/**
	 * Test has_archive is false.
	 */
	public function test_no_archive() {
		$args = $this->cpt->get_args();

		$this->assertFalse(
			$args['has_archive'],
			'Post type should not have archive'
		);
	}
}
