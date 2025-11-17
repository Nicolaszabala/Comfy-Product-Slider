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

    // Define WordPress constants.
    if ( ! defined( 'WPINC' ) ) {
        define( 'WPINC', 'wp-includes' );
    }

    // Mock WordPress functions for unit tests.
    if ( ! function_exists( '__' ) ) {
        function __( $text, $domain = 'default' ) {
            return $text;
        }
    }

    if ( ! function_exists( '_x' ) ) {
        function _x( $text, $context, $domain = 'default' ) {
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

    if ( ! function_exists( 'esc_attr__' ) ) {
        function esc_attr__( $text, $domain = 'default' ) {
            return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
        }
    }

    if ( ! function_exists( 'esc_url' ) ) {
        function esc_url( $url ) {
            return htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
        }
    }

    if ( ! function_exists( 'esc_url_raw' ) ) {
        function esc_url_raw( $url ) {
            return filter_var( $url, FILTER_SANITIZE_URL );
        }
    }

    if ( ! function_exists( 'plugin_dir_path' ) ) {
        function plugin_dir_path( $file ) {
            return dirname( $file ) . '/';
        }
    }

    if ( ! function_exists( 'plugin_dir_url' ) ) {
        function plugin_dir_url( $file ) {
            return 'http://example.com/wp-content/plugins/' . basename( dirname( $file ) ) . '/';
        }
    }

    if ( ! function_exists( 'plugin_basename' ) ) {
        function plugin_basename( $file ) {
            return basename( dirname( $file ) ) . '/' . basename( $file );
        }
    }

    if ( ! function_exists( 'register_activation_hook' ) ) {
        function register_activation_hook( $file, $callback ) {
            // Mock for unit tests.
        }
    }

    if ( ! function_exists( 'register_deactivation_hook' ) ) {
        function register_deactivation_hook( $file, $callback ) {
            // Mock for unit tests.
        }
    }

    if ( ! function_exists( 'apply_filters' ) ) {
        function apply_filters( $tag, $value ) {
            return $value;
        }
    }

    if ( ! function_exists( 'get_option' ) ) {
        function get_option( $option, $default = false ) {
            // Return empty array for active_plugins.
            if ( 'active_plugins' === $option ) {
                return array();
            }
            return $default;
        }
    }

    if ( ! function_exists( 'add_action' ) ) {
        function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
            // Mock for unit tests.
        }
    }

    if ( ! function_exists( 'add_filter' ) ) {
        function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
            // Mock for unit tests.
        }
    }

    if ( ! function_exists( 'sanitize_text_field' ) ) {
        function sanitize_text_field( $str ) {
            $filtered = wp_check_invalid_utf8( $str );
            // Remove script tags and their content.
            $filtered = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $filtered );
            // Strip remaining tags.
            $filtered = strip_tags( $filtered );
            // Convert special characters.
            $filtered = htmlspecialchars( $filtered, ENT_QUOTES, 'UTF-8', false );
            $filtered = trim( $filtered );
            return $filtered;
        }
    }

    if ( ! function_exists( 'wp_check_invalid_utf8' ) ) {
        function wp_check_invalid_utf8( $string ) {
            // Simplified version for unit tests.
            return $string;
        }
    }

    if ( ! function_exists( 'wp_kses' ) ) {
        function wp_kses( $string, $allowed_html ) {
            if ( empty( $allowed_html ) ) {
                return strip_tags( $string );
            }
            // Simplified - just keep allowed tags.
            $allowed_tags = array_keys( $allowed_html );
            $allowed = '<' . implode( '><', $allowed_tags ) . '>';
            return strip_tags( $string, $allowed );
        }
    }

    if ( ! function_exists( 'wp_kses_post' ) ) {
        function wp_kses_post( $data ) {
            return strip_tags( $data );
        }
    }

    if ( ! function_exists( 'wp_parse_args' ) ) {
        function wp_parse_args( $args, $defaults = array() ) {
            if ( is_array( $args ) ) {
                return array_merge( $defaults, $args );
            }
            return $defaults;
        }
    }

    if ( ! function_exists( 'rest_sanitize_boolean' ) ) {
        function rest_sanitize_boolean( $value ) {
            return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
        }
    }

    if ( ! function_exists( 'wp_strip_all_tags' ) ) {
        function wp_strip_all_tags( $string ) {
            return strip_tags( $string );
        }
    }

    if ( ! function_exists( 'absint' ) ) {
        function absint( $maybeint ) {
            return abs( intval( $maybeint ) );
        }
    }

    if ( ! function_exists( 'wp_max_upload_size' ) ) {
        function wp_max_upload_size() {
            return 10485760; // 10MB default for tests.
        }
    }

    // Load the plugin file (which triggers autoloader).
    require_once WC_PRODUCT_SLIDER_PLUGIN_FILE;
}
