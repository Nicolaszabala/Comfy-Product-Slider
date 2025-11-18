<?php
/**
 * WooCommerce Product Slider
 *
 * @package           WC_Product_Slider
 * @author            Nicolas Zabala
 * @copyright         2025 Nicolas Zabala
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Product Slider
 * Plugin URI:        https://github.com/Nicolaszabala/product-slider-plugin
 * Description:       Professional WooCommerce Product Slider with advanced customization options. Create beautiful, responsive product sliders with intuitive visual controls.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Nicolas Zabala
 * Author URI:        https://github.com/Nicolaszabala
 * Text Domain:       woocommerce-product-slider
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * WC requires at least: 8.2
 * WC tested up to:   10.3
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'WC_PRODUCT_SLIDER_VERSION', '1.0.0' );

/**
 * Plugin directory path.
 */
define( 'WC_PRODUCT_SLIDER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'WC_PRODUCT_SLIDER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 */
define( 'WC_PRODUCT_SLIDER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Composer autoloader (optional).
 */
if ( file_exists( WC_PRODUCT_SLIDER_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'vendor/autoload.php';
}

/**
 * Declare compatibility with WooCommerce features.
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

/**
 * The code that runs during plugin activation.
 */
function wc_product_slider_activate() {
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'includes/class-wc-product-slider-activator.php';
	WC_Product_Slider\WC_Product_Slider_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function wc_product_slider_deactivate() {
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'includes/class-wc-product-slider-deactivator.php';
	WC_Product_Slider\WC_Product_Slider_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'wc_product_slider_activate' );
register_deactivation_hook( __FILE__, 'wc_product_slider_deactivate' );

/**
 * Load plugin classes manually if composer autoloader is not available.
 */
if ( ! class_exists( 'WC_Product_Slider\WC_Product_Slider_Loader' ) ) {
	// Core classes.
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'includes/class-wc-product-slider-loader.php';
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'includes/class-wc-product-slider-activator.php';
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'includes/class-wc-product-slider-deactivator.php';

	// CPT and core functionality.
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'includes/core/class-wc-product-slider-cpt.php';
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'includes/core/class-wc-product-slider-image-handler.php';
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'includes/core/class-wc-product-slider-sanitizer.php';

	// Admin classes.
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'includes/admin/class-wc-product-slider-admin.php';

	// Public classes.
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'includes/public/class-wc-product-slider-public.php';
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'includes/public/class-wc-product-slider-shortcode.php';

	// Main plugin class.
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'includes/class-wc-product-slider.php';
} else {
	/**
	 * The core plugin class (loaded by composer autoloader).
	 */
	require_once WC_PRODUCT_SLIDER_PLUGIN_DIR . 'includes/class-wc-product-slider.php';
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function wc_product_slider_run() {
	$plugin = new WC_Product_Slider\WC_Product_Slider();
	$plugin->run();
}

// Check if WooCommerce is active.
if ( class_exists( 'WooCommerce' ) ) {
	wc_product_slider_run();
} else {
	/**
	 * Display admin notice if WooCommerce is not active.
	 */
	function wc_product_slider_woocommerce_missing_notice() {
		?>
		<div class="notice notice-error">
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						/* translators: %s: WooCommerce plugin name */
						__( '<strong>WooCommerce Product Slider</strong> requires %s to be installed and activated.', 'woocommerce-product-slider' ),
						'<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>'
					)
				);
				?>
			</p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'wc_product_slider_woocommerce_missing_notice' );
}
