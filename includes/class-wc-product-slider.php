<?php
/**
 * The core plugin class
 *
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/includes
 * @since      1.0.0
 */

namespace WC_Product_Slider;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/includes
 */
class WC_Product_Slider {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    WC_Product_Slider_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The custom post type handler.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    Core\WC_Product_Slider_CPT $cpt Custom post type handler.
	 */
	protected $cpt;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->version     = defined( 'WC_PRODUCT_SLIDER_VERSION' ) ? WC_PRODUCT_SLIDER_VERSION : '1.0.0';
		$this->plugin_name = 'woocommerce-product-slider';

		$this->load_dependencies();
		$this->set_locale();
		$this->register_post_type();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		$this->loader = new WC_Product_Slider_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WC_Product_Slider_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function set_locale() {
		$this->loader->add_action( 'init', $this, 'load_plugin_textdomain' );
	}

	/**
	 * Register the custom post type for sliders.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function register_post_type() {
		$this->cpt = new Core\WC_Product_Slider_CPT();
		$this->loader->add_action( 'init', $this->cpt, 'register' );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'woocommerce-product-slider',
			false,
			dirname( WC_PRODUCT_SLIDER_PLUGIN_BASENAME ) . '/languages/'
		);
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_admin_hooks() {
		// Admin hooks will be added here as we develop.
		// Example: $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' ).
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_public_hooks() {
		// Public hooks will be added here as we develop.
		// Example: $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' ).
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since  1.0.0
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return WC_Product_Slider_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  1.0.0
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Get the custom post type handler.
	 *
	 * @since  1.0.0
	 * @return Core\WC_Product_Slider_CPT The CPT handler.
	 */
	public function get_cpt() {
		return $this->cpt;
	}
}
