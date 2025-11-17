<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for public-facing site.
 *
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/Public
 * @since      1.0.0
 */

namespace WC_Product_Slider\PublicFacing;

/**
 * Public class
 *
 * Handles all public-facing functionality including:
 * - Enqueuing public styles and scripts
 * - Frontend slider initialization
 *
 * @since 1.0.0
 */
class WC_Product_Slider_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		// Only enqueue if shortcode is present on the page.
		global $post;

		if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'wc_product_slider' ) ) {
			return;
		}

		// Enqueue Swiper CSS.
		wp_enqueue_style(
			'swiper',
			plugin_dir_url( dirname( __DIR__ ) ) . 'node_modules/swiper/swiper-bundle.min.css',
			array(),
			'11.0.0',
			'all'
		);

		// Enqueue plugin public styles.
		wp_enqueue_style(
			'wc-product-slider-public',
			plugin_dir_url( dirname( __DIR__ ) ) . 'assets/css/wc-product-slider-public.css',
			array( 'swiper' ),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		// Only enqueue if shortcode is present on the page.
		global $post;

		if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'wc_product_slider' ) ) {
			return;
		}

		// Enqueue Swiper JS.
		wp_enqueue_script(
			'swiper',
			plugin_dir_url( dirname( __DIR__ ) ) . 'node_modules/swiper/swiper-bundle.min.js',
			array(),
			'11.0.0',
			true
		);

		// Enqueue plugin public script.
		wp_enqueue_script(
			'wc-product-slider-public',
			plugin_dir_url( dirname( __DIR__ ) ) . 'assets/js/wc-product-slider-public.js',
			array( 'swiper' ),
			$this->version,
			true
		);
	}

	/**
	 * Get the plugin name.
	 *
	 * @since  1.0.0
	 * @return string The plugin name.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Get the plugin version.
	 *
	 * @since  1.0.0
	 * @return string The plugin version.
	 */
	public function get_version() {
		return $this->version;
	}
}
