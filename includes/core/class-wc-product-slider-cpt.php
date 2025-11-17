<?php
/**
 * Custom Post Type for Product Sliders
 *
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/includes/core
 * @since      1.0.0
 */

namespace WC_Product_Slider\Core;

/**
 * Custom Post Type registration and configuration.
 *
 * Registers the 'wc_product_slider' post type for managing product sliders.
 *
 * @since      1.0.0
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/includes/core
 */
class WC_Product_Slider_CPT {

	/**
	 * Post type slug.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $post_type The post type slug.
	 */
	private $post_type = 'wc_product_slider';

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Constructor can be used to add hooks if needed.
	}

	/**
	 * Get the post type slug.
	 *
	 * @since  1.0.0
	 * @return string The post type slug.
	 */
	public function get_post_type() {
		return $this->post_type;
	}

	/**
	 * Get post type labels.
	 *
	 * @since  1.0.0
	 * @return array Labels for the post type.
	 */
	public function get_labels() {
		return array(
			'name'                  => _x( 'Product Sliders', 'Post Type General Name', 'woocommerce-product-slider' ),
			'singular_name'         => _x( 'Product Slider', 'Post Type Singular Name', 'woocommerce-product-slider' ),
			'menu_name'             => __( 'Product Sliders', 'woocommerce-product-slider' ),
			'name_admin_bar'        => __( 'Product Slider', 'woocommerce-product-slider' ),
			'archives'              => __( 'Slider Archives', 'woocommerce-product-slider' ),
			'attributes'            => __( 'Slider Attributes', 'woocommerce-product-slider' ),
			'parent_item_colon'     => __( 'Parent Slider:', 'woocommerce-product-slider' ),
			'all_items'             => __( 'All Sliders', 'woocommerce-product-slider' ),
			'add_new_item'          => __( 'Add New Slider', 'woocommerce-product-slider' ),
			'add_new'               => __( 'Add New', 'woocommerce-product-slider' ),
			'new_item'              => __( 'New Slider', 'woocommerce-product-slider' ),
			'edit_item'             => __( 'Edit Slider', 'woocommerce-product-slider' ),
			'update_item'           => __( 'Update Slider', 'woocommerce-product-slider' ),
			'view_item'             => __( 'View Slider', 'woocommerce-product-slider' ),
			'view_items'            => __( 'View Sliders', 'woocommerce-product-slider' ),
			'search_items'          => __( 'Search Slider', 'woocommerce-product-slider' ),
			'not_found'             => __( 'Not found', 'woocommerce-product-slider' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'woocommerce-product-slider' ),
			'featured_image'        => __( 'Featured Image', 'woocommerce-product-slider' ),
			'set_featured_image'    => __( 'Set featured image', 'woocommerce-product-slider' ),
			'remove_featured_image' => __( 'Remove featured image', 'woocommerce-product-slider' ),
			'use_featured_image'    => __( 'Use as featured image', 'woocommerce-product-slider' ),
			'insert_into_item'      => __( 'Insert into slider', 'woocommerce-product-slider' ),
			'uploaded_to_this_item' => __( 'Uploaded to this slider', 'woocommerce-product-slider' ),
			'items_list'            => __( 'Sliders list', 'woocommerce-product-slider' ),
			'items_list_navigation' => __( 'Sliders list navigation', 'woocommerce-product-slider' ),
			'filter_items_list'     => __( 'Filter sliders list', 'woocommerce-product-slider' ),
		);
	}

	/**
	 * Get post type arguments.
	 *
	 * @since  1.0.0
	 * @return array Arguments for the post type.
	 */
	public function get_args() {
		$labels = $this->get_labels();

		$args = array(
			'label'               => __( 'Product Slider', 'woocommerce-product-slider' ),
			'description'         => __( 'WooCommerce Product Sliders', 'woocommerce-product-slider' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 56, // Below WooCommerce (55).
			'menu_icon'           => 'dashicons-slides',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => array(
				'slug'       => 'product-slider',
				'with_front' => false,
			),
			'capability_type'     => 'post',
			'show_in_rest'        => true,
			'rest_base'           => 'product-sliders',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);

		return $args;
	}

	/**
	 * Register the custom post type.
	 *
	 * This method should be called on 'init' hook.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$args = $this->get_args();

		register_post_type( $this->post_type, $args );
	}
}
