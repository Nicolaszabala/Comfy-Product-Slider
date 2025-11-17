<?php
/**
 * Fired during plugin deactivation
 *
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/includes
 * @since      1.0.0
 */

namespace WC_Product_Slider;

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/includes
 */
class WC_Product_Slider_Deactivator {

	/**
	 * Deactivate the plugin.
	 *
	 * - Clear scheduled cron jobs if any
	 * - Clear transients/cache
	 * - Flush rewrite rules
	 *
	 * Note: DO NOT delete data here. Use uninstall.php for that.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		// Clear all plugin transients.
		self::clear_transients();

		// Flush rewrite rules.
		flush_rewrite_rules();

		// Set deactivation timestamp.
		update_option( 'wc_product_slider_deactivated_time', time() );
	}

	/**
	 * Clear all plugin transients.
	 *
	 * @since 1.0.0
	 */
	private static function clear_transients() {
		global $wpdb;

		// Delete all transients with our prefix.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options}
				WHERE option_name LIKE %s
				OR option_name LIKE %s",
				$wpdb->esc_like( '_transient_wc_slider_' ) . '%',
				$wpdb->esc_like( '_transient_timeout_wc_slider_' ) . '%'
			)
		);

		// Clear object cache if available.
		if ( function_exists( 'wp_cache_flush_group' ) ) {
			wp_cache_flush_group( 'wc_product_slider' );
		}
	}
}
