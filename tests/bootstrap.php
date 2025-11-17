<?php
/**
 * PHPUnit bootstrap file for WooCommerce Product Slider
 *
 * @package WC_Product_Slider
 */

// Composer autoloader.
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Load PHPUnit Polyfills.
require_once dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';

// Define test constants.
define( 'WC_PRODUCT_SLIDER_PLUGIN_FILE', dirname( __DIR__ ) . '/woocommerce-product-slider.php' );
define( 'WC_PRODUCT_SLIDER_PLUGIN_DIR', dirname( __DIR__ ) . '/' );
define( 'WC_PRODUCT_SLIDER_VERSION', '1.0.0' );

/*
 * WordPress Test Library setup
 *
 * To run tests with WordPress Test Library:
 * 1. Install WordPress test environment:
 *    bin/install-wp-tests.sh wordpress_test root '' localhost latest
 * 2. Set WP_TESTS_DIR environment variable or uncomment below:
 */

// Check if we're running with WordPress Test Library.
if ( getenv( 'WP_TESTS_DIR' ) ) {
    $_tests_dir = getenv( 'WP_TESTS_DIR' );

    if ( ! $_tests_dir ) {
        $_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
    }

    if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
        echo "Could not find {$_tests_dir}/includes/functions.php\n";
        echo "Please run: bin/install-wp-tests.sh wordpress_test root '' localhost latest\n";
        exit( 1 );
    }

    // Give access to tests_add_filter() function.
    require_once "{$_tests_dir}/includes/functions.php";

    /**
     * Manually load the plugin being tested.
     */
    function _manually_load_plugin() {
        require WC_PRODUCT_SLIDER_PLUGIN_FILE;
    }
    tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

    /**
     * Manually load WooCommerce if needed.
     */
    function _manually_load_woocommerce() {
        if ( file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) {
            require WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';
        }
    }
    tests_add_filter( 'muplugins_loaded', '_manually_load_woocommerce', 0 );

    // Start up the WP testing environment.
    require "{$_tests_dir}/includes/bootstrap.php";
} else {
    // Running unit tests without WordPress (faster for isolated unit tests).
    echo "Running tests without WordPress Test Library (unit tests only)\n";
    echo "To run integration tests, set WP_TESTS_DIR environment variable\n";

    // Mock WordPress functions for unit tests if needed.
    if ( ! function_exists( '__' ) ) {
        function __( $text, $domain = 'default' ) {
            return $text;
        }
    }

    if ( ! function_exists( '_e' ) ) {
        function _e( $text, $domain = 'default' ) {
            echo $text;
        }
    }

    if ( ! function_exists( 'esc_html__' ) ) {
        function esc_html__( $text, $domain = 'default' ) {
            return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
        }
    }

    if ( ! function_exists( 'esc_html' ) ) {
        function esc_html( $text ) {
            return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
        }
    }

    if ( ! function_exists( 'esc_attr' ) ) {
        function esc_attr( $text ) {
            return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
        }
    }

    if ( ! function_exists( 'esc_url' ) ) {
        function esc_url( $url ) {
            return htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
        }
    }
}
