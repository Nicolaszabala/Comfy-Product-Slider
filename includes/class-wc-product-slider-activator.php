<?php
/**
 * Fired during plugin activation
 *
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/includes
 * @since      1.0.0
 */

namespace WC_Product_Slider;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/includes
 */
class WC_Product_Slider_Activator {

	/**
	 * Activate the plugin.
	 *
	 * - Check minimum requirements (PHP, WordPress, WooCommerce)
	 * - Create/update database tables if needed
	 * - Set default options
	 * - Flush rewrite rules
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		// Check PHP version.
		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			deactivate_plugins( WC_PRODUCT_SLIDER_PLUGIN_BASENAME );
			wp_die(
				esc_html__( 'WooCommerce Product Slider requires PHP 7.4 or higher.', 'woocommerce-product-slider' ),
				esc_html__( 'Plugin Activation Error', 'woocommerce-product-slider' ),
				array( 'back_link' => true )
			);
		}

		// Check WordPress version.
		global $wp_version;
		if ( version_compare( $wp_version, '6.2', '<' ) ) {
			deactivate_plugins( WC_PRODUCT_SLIDER_PLUGIN_BASENAME );
			wp_die(
				esc_html__( 'WooCommerce Product Slider requires WordPress 6.2 or higher.', 'woocommerce-product-slider' ),
				esc_html__( 'Plugin Activation Error', 'woocommerce-product-slider' ),
				array( 'back_link' => true )
			);
		}

		// Check if WooCommerce is active.
		if ( ! class_exists( 'WooCommerce' ) ) {
			deactivate_plugins( WC_PRODUCT_SLIDER_PLUGIN_BASENAME );
			wp_die(
				wp_kses_post(
					sprintf(
						/* translators: %s: WooCommerce plugin link */
						__( 'WooCommerce Product Slider requires %s to be installed and activated.', 'woocommerce-product-slider' ),
						'<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>'
					)
				),
				esc_html__( 'Plugin Activation Error', 'woocommerce-product-slider' ),
				array( 'back_link' => true )
			);
		}

		// Set default options.
		self::set_default_options();

		// Register custom post type (needed for flush_rewrite_rules).
		self::register_slider_post_type();

		// Flush rewrite rules.
		flush_rewrite_rules();

		// Set activation timestamp.
		update_option( 'wc_product_slider_activated_time', time() );
		update_option( 'wc_product_slider_version', WC_PRODUCT_SLIDER_VERSION );
	}

	/**
	 * Set default plugin options.
	 *
	 * @since 1.0.0
	 */
	private static function set_default_options() {
		$defaults = array(
			'default_slides_visible'    => 3,
			'default_autoplay'          => false,
			'default_autoplay_speed'    => 3000,
			'default_transition_speed'  => 300,
			'default_loop'              => true,
			'default_navigation'        => true,
			'default_pagination'        => true,
			'default_lazy_loading'      => true,
			'enable_cache'              => true,
			'cache_expiration'          => 3600, // 1 hour.
		);

		foreach ( $defaults as $key => $value ) {
			if ( false === get_option( 'wc_product_slider_' . $key ) ) {
				add_option( 'wc_product_slider_' . $key, $value );
			}
		}
	}

	/**
	 * Register slider custom post type.
	 *
	 * Temporary registration for activation (actual registration happens in CPT class).
	 *
	 * @since 1.0.0
	 */
	private static function register_slider_post_type() {
		$labels = array(
			'name'          => __( 'Product Sliders', 'woocommerce-product-slider' ),
			'singular_name' => __( 'Product Slider', 'woocommerce-product-slider' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => 'product-slider' ),
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 56, // Below WooCommerce.
			'menu_icon'           => 'dashicons-slides',
			'supports'            => array( 'title' ),
			'show_in_rest'        => true,
		);

		register_post_type( 'wc_product_slider', $args );
	}
}
