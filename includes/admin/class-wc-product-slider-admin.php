<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for admin area.
 *
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/Admin
 * @since      1.0.0
 */

namespace WC_Product_Slider\Admin;

/**
 * Admin class
 *
 * Handles all admin-specific functionality including:
 * - Enqueuing admin styles and scripts
 * - Registering meta boxes
 * - Saving post meta data
 *
 * @since 1.0.0
 */
class WC_Product_Slider_Admin {

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
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Register AJAX handlers.
		add_action( 'wp_ajax_wc_product_slider_search_products', array( $this, 'search_products' ) );
		add_action( 'wp_ajax_wc_product_slider_preview_slider', array( $this, 'ajax_preview_slider' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		// Enqueue only on our CPT edit screen.
		$screen = get_current_screen();
		if ( ! $screen || 'wc_product_slider' !== $screen->post_type ) {
			return;
		}

		// Enqueue Select2 from WooCommerce.
		if ( function_exists( 'wc_enqueue_js' ) ) {
			wp_enqueue_style( 'select2' );
		}

		// Enqueue WordPress Color Picker.
		wp_enqueue_style( 'wp-color-picker' );

		// Enqueue wp-color-picker-alpha CSS for alpha slider display.
		wp_enqueue_style(
			'wp-color-picker-alpha',
			plugin_dir_url( dirname( __DIR__ ) ) . 'assets/css/wp-color-picker-alpha.css',
			array( 'wp-color-picker' ),
			'3.0.4',
			'all'
		);

		// Enqueue CodeMirror for CSS editor.
		wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
		wp_enqueue_style( 'wp-codemirror' );

		// Enqueue custom admin styles.
		wp_enqueue_style(
			'wc-product-slider-admin',
			plugin_dir_url( dirname( __DIR__ ) ) . 'assets/css/admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		// Enqueue only on our CPT edit screen.
		$screen = get_current_screen();
		if ( ! $screen || 'wc_product_slider' !== $screen->post_type ) {
			return;
		}

		// Enqueue WordPress Media Uploader.
		wp_enqueue_media();

		// Enqueue Select2 from WooCommerce.
		if ( function_exists( 'wc_enqueue_js' ) ) {
			wp_enqueue_script( 'select2' );
		}

		// Enqueue WordPress Color Picker with Alpha support.
		// Explicitly enqueue iris first to ensure proper load order.
		wp_enqueue_script( 'iris' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		// Enqueue wp-color-picker-alpha for transparency support.
		wp_enqueue_script(
			'wp-color-picker-alpha',
			plugin_dir_url( dirname( __DIR__ ) ) . 'assets/js/wp-color-picker-alpha.min.js',
			array( 'jquery', 'wp-color-picker' ),
			'3.0.4',
			false
		);

		// Enqueue CodeMirror.
		wp_enqueue_script( 'code-editor' );
		wp_enqueue_script( 'csslint' );

		// Enqueue custom admin script in header to ensure DOM is ready.
		wp_enqueue_script(
			'wc-product-slider-admin',
			plugin_dir_url( dirname( __DIR__ ) ) . 'assets/js/admin.js',
			array( 'jquery', 'select2', 'wp-color-picker-alpha', 'code-editor', 'media-upload', 'media-views' ),
			$this->version,
			false
		);

		// Localize script.
		wp_localize_script(
			'wc-product-slider-admin',
			'wcProductSlider',
			array(
				'restUrl'      => esc_url_raw( rest_url() ),
				'nonce'        => wp_create_nonce( 'wp_rest' ),
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'searchNonce'  => wp_create_nonce( 'wc_product_slider_search_products' ),
				'previewNonce' => wp_create_nonce( 'wc_product_slider_preview_slider' ),
				'pluginUrl'    => plugin_dir_url( dirname( __DIR__ ) ),
			)
		);

		// Enqueue Frontend Assets for Preview.
		wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.5' );
		wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.5', true );

		wp_enqueue_style(
			'wc-product-slider-public',
			plugin_dir_url( dirname( __DIR__ ) ) . 'assets/css/wc-product-slider-public.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Render admin header banner.
	 *
	 * @since 1.0.0
	 */
	public function render_admin_header() {
		$screen = get_current_screen();
		if ( ! $screen || 'wc_product_slider' !== $screen->post_type ) {
			return;
		}
		?>
		<div class="wc-ps-admin-header">
			<div class="wc-ps-header-content">
				<div class="wc-ps-logo">
					<svg width="64px" height="64px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<!-- Coffee Cup -->
						<path d="M6 8H18V10C18 13.3137 15.3137 16 12 16C8.68629 16 6 13.3137 6 10V8Z" stroke="#4A403A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<!-- Handle -->
						<path d="M18 9H19C20.1046 9 21 9.89543 21 11V12C21 13.1046 20.1046 14 19 14H18" stroke="#4A403A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<!-- Saucer -->
						<path d="M4 17H20" stroke="#4A403A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<!-- Steam Left -->
						<path d="M10 3C10 3 10.5 4.5 9.5 6" stroke="#D4A373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<!-- Steam Right -->
						<path d="M14 3C14 3 14.5 4.5 13.5 6" stroke="#D4A373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<div class="wc-ps-header-text">
					<h1><?php esc_html_e( 'Comfy Slider', 'woocommerce-product-slider' ); ?></h1>
					<p><?php esc_html_e( 'Create cozy, warm product showcases for your WooCommerce store', 'woocommerce-product-slider' ); ?></p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Register meta boxes for slider configuration.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
		// Render header banner at top (full width).
		add_action( 'in_admin_header', array( $this, 'render_admin_header' ) );

		add_meta_box(
			'wc_product_slider_preview',
			__( 'Live Preview', 'woocommerce-product-slider' ),
			array( $this, 'render_preview_meta_box' ),
			'wc_product_slider',
			'normal',
			'high',
			array( '__back_compat_meta_box' => false )
		);

		add_meta_box(
			'wc_product_slider_products',
			__( 'Products', 'woocommerce-product-slider' ),
			array( $this, 'render_products_meta_box' ),
			'wc_product_slider',
			'normal',
			'high',
			array( '__back_compat_meta_box' => false )
		);

		add_meta_box(
			'wc_product_slider_design',
			__( 'Design Settings', 'woocommerce-product-slider' ),
			array( $this, 'render_design_meta_box' ),
			'wc_product_slider',
			'normal',
			'high',
			array( '__back_compat_meta_box' => false )
		);

		add_meta_box(
			'wc_product_slider_custom_slides',
			__( 'Custom Slides', 'woocommerce-product-slider' ),
			array( $this, 'render_custom_slides_meta_box' ),
			'wc_product_slider',
			'normal',
			'high',
			array( '__back_compat_meta_box' => false )
		);

		add_meta_box(
			'wc_product_slider_custom_css',
			__( 'Custom CSS', 'woocommerce-product-slider' ),
			array( $this, 'render_custom_css_meta_box' ),
			'wc_product_slider',
			'normal',
			'high', // Changed from 'normal' to 'high' to match other meta boxes.
			array( '__back_compat_meta_box' => false )
		);

		add_meta_box(
			'wc_product_slider_behavior',
			__( 'Behavior Settings', 'woocommerce-product-slider' ),
			array( $this, 'render_behavior_meta_box' ),
			'wc_product_slider',
			'side',
			'default',
			array( '__back_compat_meta_box' => false )
		);

		add_meta_box(
			'wc_product_slider_display_options',
			__( 'Display Options', 'woocommerce-product-slider' ),
			array( $this, 'render_display_options_meta_box' ),
			'wc_product_slider',
			'side',
			'default',
			array( '__back_compat_meta_box' => false )
		);

		add_meta_box(
			'wc_product_slider_shortcode',
			__( 'Shortcode', 'woocommerce-product-slider' ),
			array( $this, 'render_shortcode_meta_box' ),
			'wc_product_slider',
			'side',
			'high',
			array( '__back_compat_meta_box' => false )
		);

		// Add custom CSS classes to metaboxes.
		add_filter( 'postbox_classes_wc_product_slider_wc_product_slider_products', array( $this, 'add_metabox_classes' ) );
		add_filter( 'postbox_classes_wc_product_slider_wc_product_slider_design', array( $this, 'add_metabox_classes' ) );
		add_filter( 'postbox_classes_wc_product_slider_wc_product_slider_behavior', array( $this, 'add_metabox_classes' ) );
		add_filter( 'postbox_classes_wc_product_slider_wc_product_slider_shortcode', array( $this, 'add_metabox_classes' ) );
		add_filter( 'postbox_classes_wc_product_slider_wc_product_slider_custom_css', array( $this, 'add_metabox_classes' ) );
		add_filter( 'postbox_classes_wc_product_slider_wc_product_slider_display_options', array( $this, 'add_metabox_classes' ) );
		add_filter( 'postbox_classes_wc_product_slider_wc_product_slider_custom_slides', array( $this, 'add_metabox_classes' ) );
		add_filter( 'postbox_classes_wc_product_slider_wc_product_slider_custom_slides', array( $this, 'add_metabox_classes' ) );
	}

	/**
	 * Render preview meta box.
	 *
	 * @since 1.1.0
	 * @param \WP_Post $post Current post object.
	 */
	public function render_preview_meta_box( $post ) {
		?>
		<div class="wc-ps-preview-wrapper">
			<div class="wc-ps-help-box">
				<span class="dashicons dashicons-visibility"></span>
				<div>
					<strong><?php esc_html_e( 'Live Preview', 'woocommerce-product-slider' ); ?></strong>
					<p><?php esc_html_e( 'See how your slider looks with current settings. Click "Refresh Preview" to update.', 'woocommerce-product-slider' ); ?></p>
				</div>
				<button type="button" class="button button-primary wc-ps-refresh-preview">
					<span class="dashicons dashicons-update" style="margin-top: 4px;"></span>
					<?php esc_html_e( 'Refresh Preview', 'woocommerce-product-slider' ); ?>
				</button>
			</div>
			
			<div id="wc-ps-preview-container">
				<div class="wc-ps-preview-placeholder">
					<span class="dashicons dashicons-format-gallery"></span>
					<p><?php esc_html_e( 'Click "Refresh Preview" to generate a preview.', 'woocommerce-product-slider' ); ?></p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Add custom CSS classes to metaboxes.
	 *
	 * @since 1.0.0
	 * @param array $classes Existing classes.
	 * @return array Modified classes.
	 */
	public function add_metabox_classes( $classes ) {
		$classes[] = 'wc-ps-metabox';
		return $classes;
	}

	/**
	 * Render products meta box.
	 *
	 * @since 1.0.0
	 * @param \WP_Post $post Current post object.
	 */
	public function render_products_meta_box( $post ) {
		// Add nonce for security.
		wp_nonce_field( 'wc_product_slider_save_products', 'wc_product_slider_products_nonce' );

		// Get saved products.
		$selected_products = get_post_meta( $post->ID, '_wc_ps_products', true );
		if ( ! is_array( $selected_products ) ) {
			$selected_products = array();
		}

		// Get only selected products for initial display.
		$products = array();
		if ( ! empty( $selected_products ) ) {
			$products = wc_get_products(
				array(
					'include' => $selected_products,
					'status'  => array( 'publish', 'draft', 'pending', 'private' ),
					'limit'   => -1,
				)
			);
		}
		?>
		<div class="wc-ps-help-box">
			<span class="dashicons dashicons-info"></span>
			<div>
				<strong><?php esc_html_e( 'Select Your Products', 'woocommerce-product-slider' ); ?></strong>
				<p><?php esc_html_e( 'Choose which WooCommerce products you want to display in this slider. You can combine them with custom slides in the Custom Slides section below.', 'woocommerce-product-slider' ); ?></p>
			</div>
		</div>
		<select name="wc_ps_products[]" id="wc_ps_products" multiple="multiple" style="width:100%; min-height:200px;">
			<?php foreach ( $products as $product ) : ?>
				<option value="<?php echo esc_attr( $product->get_id() ); ?>" selected="selected">
					<?php echo esc_html( $product->get_name() ); ?> (ID: <?php echo esc_html( $product->get_id() ); ?>)
				</option>
			<?php endforeach; ?>
		</select>
		<p class="description">
			<?php esc_html_e( 'Use the search box to quickly find products. You can select multiple products at once.', 'woocommerce-product-slider' ); ?>
		</p>
		<?php
	}

	/**
	 * AJAX handler for searching products.
	 *
	 * @since 1.0.0
	 */
	public function search_products() {
		check_ajax_referer( 'wc_product_slider_search_products', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( __( 'Permission denied.', 'woocommerce-product-slider' ) );
		}

		$term = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';

		if ( empty( $term ) ) {
			wp_send_json_success( array() );
		}

		$args = array(
			'status'  => 'publish',
			'limit'   => 20,
			's'       => $term,
			'orderby' => 'title',
			'order'   => 'ASC',
		);

		$products = wc_get_products( $args );
		$results  = array();

		foreach ( $products as $product ) {
			$results[] = array(
				'id'   => $product->get_id(),
				'text' => $product->get_name() . ' (ID: ' . $product->get_id() . ')',
			);
		}

		wp_send_json_success( $results );
	}

	/**
	 * AJAX handler for previewing slider.
	 *
	 * @since 1.1.0
	 */
	public function ajax_preview_slider() {
		check_ajax_referer( 'wc_product_slider_preview_slider', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( __( 'Permission denied.', 'woocommerce-product-slider' ) );
		}

		// Parse form data.
		parse_str( $_POST['formData'], $form_data );

		// Get config from form data using centralized helper.
		$config = $this->get_config_from_form_data( $form_data );

		try {
			// Instantiate shortcode class to render slider.
			$shortcode = new \WC_Product_Slider\PublicFacing\WC_Product_Slider_Shortcode();

			$html = $shortcode->render_preview( $config );

			// If empty, return helpful message.
			if ( empty( trim( strip_tags( $html ) ) ) ) {
				wp_send_json_error( __( 'Please select at least one product to preview the slider.', 'woocommerce-product-slider' ) );
			}

			wp_send_json_success( $html );
		} catch ( \Exception $e ) {
			wp_send_json_error( sprintf( __( 'Error rendering preview: %s', 'woocommerce-product-slider' ), $e->getMessage() ) );
		}
	}

	/**
	 * Helper to map form data to slider configuration.
	 *
	 * @since 1.1.0
	 * @param array $form_data Parsed form data from $_POST.
	 * @return array Slider configuration array.
	 */
	private function get_config_from_form_data( $form_data ) {
		return array(
			'products'                 => isset( $form_data['wc_ps_products'] ) ? $form_data['wc_ps_products'] : array(),
			'custom_slides'            => array(), // Placeholder for future custom slides support in preview.

			// Design.
			'primary_color'            => isset( $form_data['wc_ps_primary_color'] ) ? sanitize_hex_color( $form_data['wc_ps_primary_color'] ) : '#4A403A',
			'secondary_color'          => isset( $form_data['wc_ps_secondary_color'] ) ? sanitize_hex_color( $form_data['wc_ps_secondary_color'] ) : '#D4A373',
			'button_color'             => isset( $form_data['wc_ps_button_color'] ) ? sanitize_hex_color( $form_data['wc_ps_button_color'] ) : '#4A403A',
			'button_text_color'        => isset( $form_data['wc_ps_button_text_color'] ) ? sanitize_hex_color( $form_data['wc_ps_button_text_color'] ) : '#ffffff',
			'border_radius'            => isset( $form_data['wc_ps_border_radius'] ) ? absint( $form_data['wc_ps_border_radius'] ) : 8,
			'slide_gap'                => isset( $form_data['wc_ps_slide_gap'] ) ? absint( $form_data['wc_ps_slide_gap'] ) : 20,

			// Navigation.
			'nav_arrow_color'          => isset( $form_data['wc_ps_nav_arrow_color'] ) ? sanitize_hex_color( $form_data['wc_ps_nav_arrow_color'] ) : '',
			'nav_arrow_bg_color'       => isset( $form_data['wc_ps_nav_arrow_bg_color'] ) ? sanitize_hex_color( $form_data['wc_ps_nav_arrow_bg_color'] ) : '',
			'nav_arrow_gradient'       => isset( $form_data['wc_ps_nav_arrow_gradient'] ) && '1' === $form_data['wc_ps_nav_arrow_gradient'],
			'nav_arrow_size'           => isset( $form_data['wc_ps_nav_arrow_size'] ) ? absint( $form_data['wc_ps_nav_arrow_size'] ) : 40,
			'nav_progressbar_color'    => isset( $form_data['wc_ps_nav_progressbar_color'] ) ? sanitize_hex_color( $form_data['wc_ps_nav_progressbar_color'] ) : '',
			'nav_progressbar_height'   => isset( $form_data['wc_ps_nav_progressbar_height'] ) ? absint( $form_data['wc_ps_nav_progressbar_height'] ) : 4,
			'nav_progressbar_position' => isset( $form_data['wc_ps_nav_progressbar_position'] ) ? sanitize_text_field( $form_data['wc_ps_nav_progressbar_position'] ) : 'bottom',

			// Behavior.
			'autoplay'                 => isset( $form_data['wc_ps_autoplay'] ) && '1' === $form_data['wc_ps_autoplay'],
			'loop'                     => isset( $form_data['wc_ps_loop'] ) && '1' === $form_data['wc_ps_loop'],
			'speed'                    => isset( $form_data['wc_ps_speed'] ) ? absint( $form_data['wc_ps_speed'] ) : 3000,
			'navigation_type'          => isset( $form_data['wc_ps_navigation_type'] ) ? sanitize_text_field( $form_data['wc_ps_navigation_type'] ) : 'dots',
			'arrow_style'              => isset( $form_data['wc_ps_arrow_style'] ) ? sanitize_text_field( $form_data['wc_ps_arrow_style'] ) : 'default',
			'arrow_position'           => isset( $form_data['wc_ps_arrow_position'] ) ? sanitize_text_field( $form_data['wc_ps_arrow_position'] ) : 'inside',
			'show_arrows'              => isset( $form_data['wc_ps_show_arrows'] ) && '1' === $form_data['wc_ps_show_arrows'],

			// Display Options.
			'show_title'               => isset( $form_data['wc_ps_show_title'] ) && '1' === $form_data['wc_ps_show_title'],
			'show_price'               => isset( $form_data['wc_ps_show_price'] ) && '1' === $form_data['wc_ps_show_price'],
			'show_description'         => isset( $form_data['wc_ps_show_description'] ) && '1' === $form_data['wc_ps_show_description'],
			'show_button'              => isset( $form_data['wc_ps_show_button'] ) && '1' === $form_data['wc_ps_show_button'],
			'show_image'               => isset( $form_data['wc_ps_show_image'] ) && '1' === $form_data['wc_ps_show_image'],
			'show_rating'              => isset( $form_data['wc_ps_show_rating'] ) && '1' === $form_data['wc_ps_show_rating'],
			'button_text'              => isset( $form_data['wc_ps_button_text'] ) ? sanitize_text_field( $form_data['wc_ps_button_text'] ) : 'View Product',
			'slider_heading'           => isset( $form_data['wc_ps_slider_heading'] ) ? sanitize_text_field( $form_data['wc_ps_slider_heading'] ) : '',
			'heading_font_size'        => isset( $form_data['wc_ps_heading_font_size'] ) ? absint( $form_data['wc_ps_heading_font_size'] ) : 24,
			'heading_alignment'        => isset( $form_data['wc_ps_heading_alignment'] ) ? sanitize_text_field( $form_data['wc_ps_heading_alignment'] ) : 'left',
			'heading_typography'       => isset( $form_data['wc_ps_heading_typography'] ) ? sanitize_text_field( $form_data['wc_ps_heading_typography'] ) : 'default',
			'heading_color'            => isset( $form_data['wc_ps_heading_color'] ) ? sanitize_hex_color( $form_data['wc_ps_heading_color'] ) : '',
			'clickable_image'          => isset( $form_data['wc_ps_clickable_image'] ) && '1' === $form_data['wc_ps_clickable_image'],

			// Custom CSS.
			'custom_css'               => isset( $form_data['wc_ps_custom_css'] ) ? wp_strip_all_tags( $form_data['wc_ps_custom_css'] ) : '',
		);
	}

	/**
	 * Render design meta box.
	 *
	 * @since 1.0.0
	 * @param \WP_Post $post Current post object.
	 */
	public function render_design_meta_box( $post ) {
		wp_nonce_field( 'wc_product_slider_save_design', 'wc_product_slider_design_nonce' );

		$primary_color     = get_post_meta( $post->ID, '_wc_ps_primary_color', true );
		$secondary_color   = get_post_meta( $post->ID, '_wc_ps_secondary_color', true );
		$button_color      = get_post_meta( $post->ID, '_wc_ps_button_color', true );
		$button_text_color = get_post_meta( $post->ID, '_wc_ps_button_text_color', true );
		$border_radius     = get_post_meta( $post->ID, '_wc_ps_border_radius', true );
		$slide_gap         = get_post_meta( $post->ID, '_wc_ps_slide_gap', true );

		// Set defaults.
		if ( empty( $primary_color ) ) {
			$primary_color = '#4A403A';
		}
		if ( empty( $secondary_color ) ) {
			$secondary_color = '#D4A373';
		}
		if ( empty( $button_color ) ) {
			$button_color = '#4A403A';
		}
		if ( empty( $button_text_color ) ) {
			$button_text_color = '#ffffff';
		}
		if ( empty( $border_radius ) ) {
			$border_radius = '4';
		}
		if ( empty( $slide_gap ) ) {
			$slide_gap = '20';
		}

		// Navigation Customization.
		$nav_arrow_color          = get_post_meta( $post->ID, '_wc_ps_nav_arrow_color', true );
		$nav_arrow_bg_color       = get_post_meta( $post->ID, '_wc_ps_nav_arrow_bg_color', true );
		$nav_arrow_size           = get_post_meta( $post->ID, '_wc_ps_nav_arrow_size', true );
		$nav_arrow_size           = get_post_meta( $post->ID, '_wc_ps_nav_arrow_size', true );
		$nav_progressbar_color    = get_post_meta( $post->ID, '_wc_ps_nav_progressbar_color', true );
		$nav_progressbar_height   = get_post_meta( $post->ID, '_wc_ps_nav_progressbar_height', true );
		$nav_progressbar_position = get_post_meta( $post->ID, '_wc_ps_nav_progressbar_position', true );

		if ( empty( $nav_arrow_size ) ) {
			$nav_arrow_size = '40';
		}
		if ( empty( $nav_progressbar_height ) ) {
			$nav_progressbar_height = '4';
		}
		if ( empty( $nav_progressbar_position ) ) {
			$nav_progressbar_position = 'bottom';
		}
		?>
		<div class="wc-ps-help-box">
			<span class="dashicons dashicons-art"></span>
			<div>
				<strong><?php esc_html_e( 'Design & Colors', 'woocommerce-product-slider' ); ?></strong>
				<p><?php esc_html_e( 'Customize the colors, spacing, and visual appearance of your slider to match your brand.', 'woocommerce-product-slider' ); ?></p>
			</div>
		</div>

		<h4><?php esc_html_e( 'Typography & Branding', 'woocommerce-product-slider' ); ?></h4>
		<table class="form-table">
			<tr>
				<th><label for="wc_ps_primary_color"><?php esc_html_e( 'Primary Brand Color:', 'woocommerce-product-slider' ); ?></label></th>
				<td>
					<input type="text" name="wc_ps_primary_color" id="wc_ps_primary_color" value="<?php echo esc_attr( $primary_color ); ?>" class="wc-ps-color-picker" data-alpha-enabled="true" data-type="full" />
					<p class="description"><?php esc_html_e( 'Used for heading text and main accents', 'woocommerce-product-slider' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="wc_ps_secondary_color"><?php esc_html_e( 'Secondary Accent Color:', 'woocommerce-product-slider' ); ?></label></th>
				<td>
					<input type="text" name="wc_ps_secondary_color" id="wc_ps_secondary_color" value="<?php echo esc_attr( $secondary_color ); ?>" class="wc-ps-color-picker" data-alpha-enabled="true" data-type="full" />
					<p class="description"><?php esc_html_e( 'Used for gradients and secondary elements', 'woocommerce-product-slider' ); ?></p>
				</td>
			</tr>
		</table>

		<hr>

		<h4><?php esc_html_e( 'Buttons', 'woocommerce-product-slider' ); ?></h4>
		<table class="form-table">
			<tr>
				<th><label for="wc_ps_button_color"><?php esc_html_e( 'Button Background:', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="text" name="wc_ps_button_color" id="wc_ps_button_color" value="<?php echo esc_attr( $button_color ); ?>" class="wc-ps-color-picker" data-alpha-enabled="true" data-type="full" /></td>
			</tr>
			<tr>
				<th><label for="wc_ps_button_text_color"><?php esc_html_e( 'Button Text:', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="text" name="wc_ps_button_text_color" id="wc_ps_button_text_color" value="<?php echo esc_attr( $button_text_color ); ?>" class="wc-ps-color-picker" data-alpha-enabled="true" data-type="full" /></td>
			</tr>
		</table>

		<hr>

		<h4><?php esc_html_e( 'Layout', 'woocommerce-product-slider' ); ?></h4>
		<table class="form-table">
			<tr>
				<th><label for="wc_ps_border_radius"><?php esc_html_e( 'Border Radius (px):', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="number" name="wc_ps_border_radius" id="wc_ps_border_radius" value="<?php echo esc_attr( $border_radius ); ?>" min="0" max="50" /></td>
			</tr>
			<tr>
				<th><label for="wc_ps_slide_gap"><?php esc_html_e( 'Gap Between Slides (px):', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="number" name="wc_ps_slide_gap" id="wc_ps_slide_gap" value="<?php echo esc_attr( $slide_gap ); ?>" min="0" max="100" /></td>
			</tr>
		</table>
		
		<hr>
		
		<h4><?php esc_html_e( 'Navigation Styles', 'woocommerce-product-slider' ); ?></h4>
		<table class="form-table">
			<tr>
				<th><label for="wc_ps_nav_arrow_color"><?php esc_html_e( 'Arrow Color:', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="text" name="wc_ps_nav_arrow_color" id="wc_ps_nav_arrow_color" value="<?php echo esc_attr( $nav_arrow_color ); ?>" class="wc-ps-color-picker" data-alpha-enabled="true" data-type="full" /></td>
			</tr>
			<tr>
				<th><label for="wc_ps_nav_arrow_bg_color"><?php esc_html_e( 'Arrow Background Color:', 'woocommerce-product-slider' ); ?></label></th>
				<td>
					<input type="text" name="wc_ps_nav_arrow_bg_color" id="wc_ps_nav_arrow_bg_color" value="<?php echo esc_attr( $nav_arrow_bg_color ); ?>" class="wc-ps-color-picker" data-alpha-enabled="true" data-type="full" />
					<br>
					<label style="margin-top: 5px; display: inline-block;">
						<input type="checkbox" name="wc_ps_nav_arrow_gradient" value="1" <?php checked( get_post_meta( $post->ID, '_wc_ps_nav_arrow_gradient', true ), '1' ); ?> />
						<?php esc_html_e( 'Use Gradient (Primary to Secondary)', 'woocommerce-product-slider' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th><label for="wc_ps_nav_arrow_size"><?php esc_html_e( 'Arrow Size (px):', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="number" name="wc_ps_nav_arrow_size" id="wc_ps_nav_arrow_size" value="<?php echo esc_attr( $nav_arrow_size ); ?>" min="20" max="100" /></td>
			</tr>
			<tr>
				<th><label for="wc_ps_nav_progressbar_color"><?php esc_html_e( 'Progress Bar Color:', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="text" name="wc_ps_nav_progressbar_color" id="wc_ps_nav_progressbar_color" value="<?php echo esc_attr( $nav_progressbar_color ); ?>" class="wc-ps-color-picker" data-alpha-enabled="true" data-type="full" /></td>
			</tr>
			<tr>
				<th><label for="wc_ps_nav_progressbar_height"><?php esc_html_e( 'Progress Bar Height (px):', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="number" name="wc_ps_nav_progressbar_height" id="wc_ps_nav_progressbar_height" value="<?php echo esc_attr( $nav_progressbar_height ); ?>" min="1" max="20" /></td>
			</tr>
			<tr>
				<th><label for="wc_ps_nav_progressbar_position"><?php esc_html_e( 'Progress Bar Position:', 'woocommerce-product-slider' ); ?></label></th>
				<td>
					<select name="wc_ps_nav_progressbar_position" id="wc_ps_nav_progressbar_position">
						<option value="bottom" <?php selected( $nav_progressbar_position, 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'woocommerce-product-slider' ); ?></option>
						<option value="top" <?php selected( $nav_progressbar_position, 'top' ); ?>><?php esc_html_e( 'Top', 'woocommerce-product-slider' ); ?></option>
					</select>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Render behavior meta box.
	 *
	 * @since 1.0.0
	 * @param \WP_Post $post Current post object.
	 */
	public function render_behavior_meta_box( $post ) {
		wp_nonce_field( 'wc_product_slider_save_behavior', 'wc_product_slider_behavior_nonce' );

		$autoplay        = get_post_meta( $post->ID, '_wc_ps_autoplay', true );
		$loop            = get_post_meta( $post->ID, '_wc_ps_loop', true );
		$speed           = get_post_meta( $post->ID, '_wc_ps_speed', true );
		$navigation_type = get_post_meta( $post->ID, '_wc_ps_navigation_type', true );
		$arrow_style     = get_post_meta( $post->ID, '_wc_ps_arrow_style', true );
		$arrow_style     = get_post_meta( $post->ID, '_wc_ps_arrow_style', true );
		$arrow_position  = get_post_meta( $post->ID, '_wc_ps_arrow_position', true );
		$show_arrows     = get_post_meta( $post->ID, '_wc_ps_show_arrows', true );

		if ( empty( $speed ) ) {
			$speed = 3000;
		}
		if ( empty( $navigation_type ) ) {
			$navigation_type = 'dots';
		}
		if ( empty( $arrow_style ) ) {
			$arrow_style = 'default';
		}
		if ( empty( $arrow_position ) ) {
			$arrow_position = 'inside';
		}
		if ( '' === $show_arrows ) {
			// Default to true for backward compatibility, unless type is 'none' or 'dots' (if we want to be strict, but let's default to true for now to avoid breaking existing sliders too much, OR default to true and let user disable).
			// Actually, to fix the "Dots vs Both" confusion, let's say default is '1' (true).
			$show_arrows = '1';
		}
		if ( empty( $navigation_type ) ) {
			$navigation_type = 'dots';
		}
		if ( empty( $arrow_style ) ) {
			$arrow_style = 'default';
		}
		if ( empty( $arrow_position ) ) {
			$arrow_position = 'inside';
		}
		?>
		<div class="wc-ps-help-box">
			<span class="dashicons dashicons-controls-play"></span>
			<div>
				<strong><?php esc_html_e( 'Animation Settings', 'woocommerce-product-slider' ); ?></strong>
				<p><?php esc_html_e( 'Configure how your slider behaves and animates.', 'woocommerce-product-slider' ); ?></p>
			</div>
		</div>
		<p>
			<label>
				<input type="checkbox" name="wc_ps_autoplay" value="1" <?php checked( $autoplay, '1' ); ?> />
				<?php esc_html_e( 'Enable Autoplay', 'woocommerce-product-slider' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="wc_ps_loop" value="1" <?php checked( $loop, '1' ); ?> />
				<?php esc_html_e( 'Loop Slides', 'woocommerce-product-slider' ); ?>
			</label>
		</p>
		<p>
			<label for="wc_ps_speed"><?php esc_html_e( 'Autoplay Speed (ms):', 'woocommerce-product-slider' ); ?></label><br>
			<input type="number" name="wc_ps_speed" id="wc_ps_speed" value="<?php echo esc_attr( $speed ); ?>" min="1000" max="10000" step="100" />
		</p>
		<hr>
		<p>
			<label for="wc_ps_navigation_type"><?php esc_html_e( 'Pagination Style:', 'woocommerce-product-slider' ); ?></label><br>
			<select name="wc_ps_navigation_type" id="wc_ps_navigation_type" style="width:100%;">
				<option value="dots" <?php selected( $navigation_type, 'dots' ); ?>><?php esc_html_e( 'Dots', 'woocommerce-product-slider' ); ?></option>
				<option value="progressbar" <?php selected( $navigation_type, 'progressbar' ); ?>><?php esc_html_e( 'Progress Bar', 'woocommerce-product-slider' ); ?></option>
				<option value="fraction" <?php selected( $navigation_type, 'fraction' ); ?>><?php esc_html_e( 'Fraction (1/5)', 'woocommerce-product-slider' ); ?></option>
				<option value="none" <?php selected( $navigation_type, 'none' ); ?>><?php esc_html_e( 'None', 'woocommerce-product-slider' ); ?></option>
			</select>
		</p>
		<p>
			<label>
				<input type="checkbox" name="wc_ps_show_arrows" value="1" <?php checked( $show_arrows, '1' ); ?> />
				<?php esc_html_e( 'Show Navigation Arrows', 'woocommerce-product-slider' ); ?>
			</label>
		</p>
		<p>
			<label for="wc_ps_arrow_style"><?php esc_html_e( 'Arrow Style:', 'woocommerce-product-slider' ); ?></label><br>
			<select name="wc_ps_arrow_style" id="wc_ps_arrow_style" style="width:100%;">
				<option value="default" <?php selected( $arrow_style, 'default' ); ?>><?php esc_html_e( 'Default (Circles)', 'woocommerce-product-slider' ); ?></option>
				<option value="square" <?php selected( $arrow_style, 'square' ); ?>><?php esc_html_e( 'Square', 'woocommerce-product-slider' ); ?></option>
				<option value="rounded-square" <?php selected( $arrow_style, 'rounded-square' ); ?>><?php esc_html_e( 'Rounded Squares', 'woocommerce-product-slider' ); ?></option>
				<option value="minimal" <?php selected( $arrow_style, 'minimal' ); ?>><?php esc_html_e( 'Minimal Lines', 'woocommerce-product-slider' ); ?></option>
				<option value="coffee" <?php selected( $arrow_style, 'coffee' ); ?>><?php esc_html_e( '☕ Coffee Theme', 'woocommerce-product-slider' ); ?></option>
			</select>
		</p>
		<p>
			<label for="wc_ps_arrow_position"><?php esc_html_e( 'Arrow Position:', 'woocommerce-product-slider' ); ?></label><br>
			<select name="wc_ps_arrow_position" id="wc_ps_arrow_position" style="width:100%;">
				<option value="inside" <?php selected( $arrow_position, 'inside' ); ?>><?php esc_html_e( 'Inside Slider', 'woocommerce-product-slider' ); ?></option>
				<option value="outside" <?php selected( $arrow_position, 'outside' ); ?>><?php esc_html_e( 'Outside Slider', 'woocommerce-product-slider' ); ?></option>
				<option value="center" <?php selected( $arrow_position, 'center' ); ?>><?php esc_html_e( 'Center Vertical', 'woocommerce-product-slider' ); ?></option>
			</select>
		</p>
		<?php
	}

	/**
	 * Render shortcode meta box.
	 *
	 * @since 1.0.0
	 * @param \WP_Post $post Current post object.
	 */
	public function render_shortcode_meta_box( $post ) {
		// Only show shortcode for published sliders.
		if ( 'publish' !== $post->post_status ) {
			echo '<p class="description">' . esc_html__( 'Publish this slider to generate a shortcode.', 'woocommerce-product-slider' ) . '</p>';
			return;
		}

		$shortcode = '[wc_product_slider id="' . $post->ID . '"]';
		?>
		<div class="wc-ps-shortcode-container">
			<p class="description">
				<?php esc_html_e( 'Copy and paste this shortcode into any post or page:', 'woocommerce-product-slider' ); ?>
			</p>
			<div class="wc-ps-shortcode-wrapper">
				<input
					type="text"
					readonly
					value="<?php echo esc_attr( $shortcode ); ?>"
					class="wc-ps-shortcode-input"
					id="wc-ps-shortcode-input"
					onclick="this.select();"
				/>
				<button
					type="button"
					class="button button-primary wc-ps-copy-shortcode"
					data-shortcode="<?php echo esc_attr( $shortcode ); ?>"
				>
					<?php esc_html_e( 'Copy', 'woocommerce-product-slider' ); ?>
				</button>
			</div>
			<p class="wc-ps-copy-feedback" style="display:none; color: #46b450; margin-top: 8px;">
				<?php esc_html_e( 'Shortcode copied to clipboard!', 'woocommerce-product-slider' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Render display options meta box.
	 *
	 * @since 1.0.0
	 * @param \WP_Post $post Current post object.
	 */
	public function render_display_options_meta_box( $post ) {
		wp_nonce_field( 'wc_product_slider_save_display_options', 'wc_product_slider_display_options_nonce' );

		$show_title       = get_post_meta( $post->ID, '_wc_ps_show_title', true );
		$show_price       = get_post_meta( $post->ID, '_wc_ps_show_price', true );
		$show_description = get_post_meta( $post->ID, '_wc_ps_show_description', true );
		$show_button      = get_post_meta( $post->ID, '_wc_ps_show_button', true );
		$show_image       = get_post_meta( $post->ID, '_wc_ps_show_image', true );
		$show_rating      = get_post_meta( $post->ID, '_wc_ps_show_rating', true );
		$button_text      = get_post_meta( $post->ID, '_wc_ps_button_text', true );
		$button_text      = get_post_meta( $post->ID, '_wc_ps_button_text', true );
		$slider_heading   = get_post_meta( $post->ID, '_wc_ps_slider_heading', true );
		$clickable_image  = get_post_meta( $post->ID, '_wc_ps_clickable_image', true );

		$heading_font_size  = get_post_meta( $post->ID, '_wc_ps_heading_font_size', true );
		$heading_alignment  = get_post_meta( $post->ID, '_wc_ps_heading_alignment', true );
		$heading_typography = get_post_meta( $post->ID, '_wc_ps_heading_typography', true );

		// Set defaults.
		if ( empty( $show_title ) ) {
			$show_title = '1';
		}
		if ( empty( $show_price ) ) {
			$show_price = '1';
		}
		if ( empty( $show_image ) ) {
			$show_image = '1';
		}
		if ( empty( $show_button ) ) {
			$show_button = '1';
		}
		if ( empty( $button_text ) ) {
			$button_text = __( 'View Product', 'woocommerce-product-slider' );
		}
		if ( empty( $clickable_image ) ) {
			$clickable_image = '1';
		}
		if ( empty( $heading_font_size ) ) {
			$heading_font_size = '24';
		}
		if ( empty( $heading_alignment ) ) {
			$heading_alignment = 'left';
		}
		if ( empty( $heading_typography ) ) {
			$heading_typography = 'default';
		}
		?>
		<div class="wc-ps-help-box">
			<span class="dashicons dashicons-visibility"></span>
			<div>
				<strong><?php esc_html_e( 'Customize Display', 'woocommerce-product-slider' ); ?></strong>
				<p><?php esc_html_e( 'Control which elements appear on your slider and configure their behavior.', 'woocommerce-product-slider' ); ?></p>
			</div>
		</div>
		<p>
			<label for="wc_ps_slider_heading">
				<?php esc_html_e( 'Slider Heading:', 'woocommerce-product-slider' ); ?>
			</label><br>
			<input type="text" name="wc_ps_slider_heading" id="wc_ps_slider_heading" value="<?php echo esc_attr( $slider_heading ); ?>" style="width:100%;" />
		</p>
		<div style="display: flex; gap: 15px; margin-bottom: 15px;">
			<div style="flex: 1;">
				<label for="wc_ps_heading_font_size"><?php esc_html_e( 'Font Size (px):', 'woocommerce-product-slider' ); ?></label><br>
				<input type="number" name="wc_ps_heading_font_size" id="wc_ps_heading_font_size" value="<?php echo esc_attr( $heading_font_size ); ?>" style="width:100%;" min="12" max="100" />
			</div>
			<div style="flex: 1;">
				<label for="wc_ps_heading_alignment"><?php esc_html_e( 'Alignment:', 'woocommerce-product-slider' ); ?></label><br>
				<select name="wc_ps_heading_alignment" id="wc_ps_heading_alignment" style="width:100%;">
					<option value="left" <?php selected( $heading_alignment, 'left' ); ?>><?php esc_html_e( 'Left', 'woocommerce-product-slider' ); ?></option>
					<option value="center" <?php selected( $heading_alignment, 'center' ); ?>><?php esc_html_e( 'Center', 'woocommerce-product-slider' ); ?></option>
					<option value="right" <?php selected( $heading_alignment, 'right' ); ?>><?php esc_html_e( 'Right', 'woocommerce-product-slider' ); ?></option>
				</select>
			</div>
			<div style="flex: 1;">
				<label for="wc_ps_heading_typography"><?php esc_html_e( 'Typography:', 'woocommerce-product-slider' ); ?></label><br>
				<select name="wc_ps_heading_typography" id="wc_ps_heading_typography" style="width:100%;">
					<option value="default" <?php selected( $heading_typography, 'default' ); ?>><?php esc_html_e( 'Default (Theme)', 'woocommerce-product-slider' ); ?></option>
					<optgroup label="<?php esc_attr_e( 'System Fonts', 'woocommerce-product-slider' ); ?>">
						<option value="sans-serif" <?php selected( $heading_typography, 'sans-serif' ); ?>><?php esc_html_e( 'Sans-serif (System)', 'woocommerce-product-slider' ); ?></option>
						<option value="serif" <?php selected( $heading_typography, 'serif' ); ?>><?php esc_html_e( 'Serif (System)', 'woocommerce-product-slider' ); ?></option>
						<option value="monospace" <?php selected( $heading_typography, 'monospace' ); ?>><?php esc_html_e( 'Monospace', 'woocommerce-product-slider' ); ?></option>
					</optgroup>
					<optgroup label="<?php esc_attr_e( 'Common Fonts', 'woocommerce-product-slider' ); ?>">
						<option value="arial" <?php selected( $heading_typography, 'arial' ); ?>><?php esc_html_e( 'Arial', 'woocommerce-product-slider' ); ?></option>
						<option value="helvetica" <?php selected( $heading_typography, 'helvetica' ); ?>><?php esc_html_e( 'Helvetica', 'woocommerce-product-slider' ); ?></option>
						<option value="georgia" <?php selected( $heading_typography, 'georgia' ); ?>><?php esc_html_e( 'Georgia', 'woocommerce-product-slider' ); ?></option>
						<option value="times" <?php selected( $heading_typography, 'times' ); ?>><?php esc_html_e( 'Times New Roman', 'woocommerce-product-slider' ); ?></option>
						<option value="courier" <?php selected( $heading_typography, 'courier' ); ?>><?php esc_html_e( 'Courier New', 'woocommerce-product-slider' ); ?></option>
						<option value="verdana" <?php selected( $heading_typography, 'verdana' ); ?>><?php esc_html_e( 'Verdana', 'woocommerce-product-slider' ); ?></option>
						<option value="tahoma" <?php selected( $heading_typography, 'tahoma' ); ?>><?php esc_html_e( 'Tahoma', 'woocommerce-product-slider' ); ?></option>
						<option value="trebuchet" <?php selected( $heading_typography, 'trebuchet' ); ?>><?php esc_html_e( 'Trebuchet MS', 'woocommerce-product-slider' ); ?></option>
					</optgroup>
					<optgroup label="<?php esc_attr_e( 'Decorative Fonts', 'woocommerce-product-slider' ); ?>">
						<option value="palatino" <?php selected( $heading_typography, 'palatino' ); ?>><?php esc_html_e( 'Palatino', 'woocommerce-product-slider' ); ?></option>
						<option value="garamond" <?php selected( $heading_typography, 'garamond' ); ?>><?php esc_html_e( 'Garamond', 'woocommerce-product-slider' ); ?></option>
						<option value="bookman" <?php selected( $heading_typography, 'bookman' ); ?>><?php esc_html_e( 'Bookman', 'woocommerce-product-slider' ); ?></option>
						<option value="comic-sans" <?php selected( $heading_typography, 'comic-sans' ); ?>><?php esc_html_e( 'Comic Sans MS', 'woocommerce-product-slider' ); ?></option>
						<option value="impact" <?php selected( $heading_typography, 'impact' ); ?>><?php esc_html_e( 'Impact', 'woocommerce-product-slider' ); ?></option>
						<option value="lucida" <?php selected( $heading_typography, 'lucida' ); ?>><?php esc_html_e( 'Lucida', 'woocommerce-product-slider' ); ?></option>
					</optgroup>
					<optgroup label="<?php esc_attr_e( 'Theme Fonts', 'woocommerce-product-slider' ); ?>">
						<option value="lora" <?php selected( $heading_typography, 'lora' ); ?>><?php esc_html_e( 'Lora (Serif)', 'woocommerce-product-slider' ); ?></option>
					</optgroup>
				</select>
			</div>
			<div style="flex: 1;">
				<label for="wc_ps_heading_color"><?php esc_html_e( 'Heading Color:', 'woocommerce-product-slider' ); ?></label><br>
				<input type="text" name="wc_ps_heading_color" id="wc_ps_heading_color" value="<?php echo esc_attr( get_post_meta( $post->ID, '_wc_ps_heading_color', true ) ); ?>" class="wc-ps-color-picker" data-alpha-enabled="true" data-type="full" />
				<br><span class="description"><?php esc_html_e( 'Leave empty to use Primary Color', 'woocommerce-product-slider' ); ?></span>
			</div>
		</div>
		<hr>
		<p>
			<label>
				<input type="checkbox" name="wc_ps_clickable_image" value="1" <?php checked( $clickable_image, '1' ); ?> />
				<?php esc_html_e( 'Make Images Clickable', 'woocommerce-product-slider' ); ?>
			</label>
			<br>
			<span class="description">
				<?php esc_html_e( 'Products will link to product page. Custom slides will link to their specified URL.', 'woocommerce-product-slider' ); ?>
			</span>
		</p>
		<hr>
		<p>
			<label>
				<input type="checkbox" name="wc_ps_show_image" value="1" <?php checked( $show_image, '1' ); ?> />
				<?php esc_html_e( 'Show Product Image', 'woocommerce-product-slider' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="wc_ps_show_title" value="1" <?php checked( $show_title, '1' ); ?> />
				<?php esc_html_e( 'Show Product Title', 'woocommerce-product-slider' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="wc_ps_show_price" value="1" <?php checked( $show_price, '1' ); ?> />
				<?php esc_html_e( 'Show Product Price', 'woocommerce-product-slider' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="wc_ps_show_rating" value="1" <?php checked( $show_rating, '1' ); ?> />
				<?php esc_html_e( 'Show Product Rating', 'woocommerce-product-slider' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="wc_ps_show_description" value="1" <?php checked( $show_description, '1' ); ?> />
				<?php esc_html_e( 'Show Short Description', 'woocommerce-product-slider' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="wc_ps_show_button" value="1" <?php checked( $show_button, '1' ); ?> />
				<?php esc_html_e( 'Show Button', 'woocommerce-product-slider' ); ?>
			</label>
		</p>
		<p>
			<label for="wc_ps_button_text">
				<?php esc_html_e( 'Button Text:', 'woocommerce-product-slider' ); ?>
			</label><br>
			<input type="text" name="wc_ps_button_text" id="wc_ps_button_text" value="<?php echo esc_attr( $button_text ); ?>" style="width:100%;" />
		</p>
		<?php
	}

	/**
	 * Render custom slides meta box.
	 *
	 * @since 1.0.0
	 * @param \WP_Post $post Current post object.
	 */
	public function render_custom_slides_meta_box( $post ) {
		wp_nonce_field( 'wc_product_slider_save_custom_slides', 'wc_product_slider_custom_slides_nonce' );

		$custom_slides = get_post_meta( $post->ID, '_wc_ps_custom_slides', true );
		if ( ! is_array( $custom_slides ) ) {
			$custom_slides = array();
		}

		?>
		<div id="wc-ps-custom-slides-container">
			<div class="wc-ps-help-box">
				<span class="dashicons dashicons-images-alt2"></span>
				<div>
					<strong><?php esc_html_e( 'Add Custom Slides', 'woocommerce-product-slider' ); ?></strong>
					<p><?php esc_html_e( 'Create promotional slides with custom images and links. These slides will be mixed with your selected products to create an engaging slider experience.', 'woocommerce-product-slider' ); ?></p>
				</div>
			</div>

			<div id="wc-ps-custom-slides-list">
				<?php
				if ( ! empty( $custom_slides ) ) {
					foreach ( $custom_slides as $index => $slide ) {
						$this->render_custom_slide_row( $index, $slide );
					}
				}
				?>
			</div>

			<button type="button" class="button" id="wc-ps-add-custom-slide">
				<?php esc_html_e( '+ Add Custom Slide', 'woocommerce-product-slider' ); ?>
			</button>
		</div>

		<script type="text/template" id="wc-ps-custom-slide-template">
			<?php $this->render_custom_slide_row( '{{INDEX}}', array() ); ?>
		</script>

		<script>
		jQuery(document).ready(function($) {
			var slideIndex = <?php echo count( $custom_slides ); ?>;

			// Add new slide
			$('#wc-ps-add-custom-slide').on('click', function() {
				var template = $('#wc-ps-custom-slide-template').html();
				var html = template.replace(/\{\{INDEX\}\}/g, slideIndex);
				$('#wc-ps-custom-slides-list').append(html);
				slideIndex++;
			});

			// Remove slide
			$(document).on('click', '.wc-ps-remove-slide', function() {
				$(this).closest('.wc-ps-custom-slide-row').remove();
			});

			// Upload image
			$(document).on('click', '.wc-ps-upload-image', function(e) {
				e.preventDefault();
				var button = $(this);
				var preview = button.siblings('.wc-ps-image-preview');
				var input = button.siblings('.wc-ps-image-id');

				var frame = wp.media({
					title: '<?php esc_html_e( 'Select or Upload Image', 'woocommerce-product-slider' ); ?>',
					button: {
						text: '<?php esc_html_e( 'Use this image', 'woocommerce-product-slider' ); ?>'
					},
					multiple: false
				});

				frame.on('select', function() {
					var attachment = frame.state().get('selection').first().toJSON();
					input.val(attachment.id);
					preview.html('<img src="' + attachment.url + '" style="max-width:150px; height:auto;" />');
				});

				frame.open();
			});

			// Remove image
			$(document).on('click', '.wc-ps-remove-image', function(e) {
				e.preventDefault();
				$(this).siblings('.wc-ps-image-id').val('');
				$(this).siblings('.wc-ps-image-preview').html('');
			});
		});
		</script>

		<style>
		.wc-ps-custom-slide-row {
			border: 1px solid #ddd;
			padding: 15px;
			margin-bottom: 15px;
			background: #f9f9f9;
			position: relative;
		}
		.wc-ps-custom-slide-row .wc-ps-remove-slide {
			position: absolute;
			top: 10px;
			right: 10px;
		}
		.wc-ps-image-preview {
			margin: 10px 0;
		}
		.wc-ps-slide-field {
			margin-bottom: 10px;
		}
		.wc-ps-slide-field label {
			display: block;
			font-weight: 600;
			margin-bottom: 5px;
		}
		.wc-ps-slide-field input[type="text"],
		.wc-ps-slide-field input[type="url"] {
			width: 100%;
		}
		</style>
		<?php
	}

	/**
	 * Render a single custom slide row.
	 *
	 * @since 1.0.0
	 * @param int|string $index Slide index.
	 * @param array      $slide Slide data.
	 */
	private function render_custom_slide_row( $index, $slide = array() ) {
		$image_id = isset( $slide['image_id'] ) ? $slide['image_id'] : '';
		$url      = isset( $slide['url'] ) ? $slide['url'] : '';
		$title    = isset( $slide['title'] ) ? $slide['title'] : '';

		$image_url = '';
		if ( $image_id ) {
			$image_url = wp_get_attachment_url( $image_id );
		}
		?>
		<div class="wc-ps-custom-slide-row">
			<button type="button" class="button wc-ps-remove-slide">
				<?php esc_html_e( 'Remove', 'woocommerce-product-slider' ); ?>
			</button>

			<div class="wc-ps-slide-field">
				<label><?php esc_html_e( 'Image:', 'woocommerce-product-slider' ); ?></label>
				<input type="hidden" class="wc-ps-image-id" name="wc_ps_custom_slides[<?php echo esc_attr( $index ); ?>][image_id]" value="<?php echo esc_attr( $image_id ); ?>" />
				<button type="button" class="button wc-ps-upload-image">
					<?php esc_html_e( 'Upload Image', 'woocommerce-product-slider' ); ?>
				</button>
				<button type="button" class="button wc-ps-remove-image">
					<?php esc_html_e( 'Remove Image', 'woocommerce-product-slider' ); ?>
				</button>
				<div class="wc-ps-image-preview">
					<?php if ( $image_url ) : ?>
						<img src="<?php echo esc_url( $image_url ); ?>" style="max-width:150px; height:auto;" />
					<?php endif; ?>
				</div>
			</div>

			<div class="wc-ps-slide-field">
				<label><?php esc_html_e( 'Link URL:', 'woocommerce-product-slider' ); ?></label>
				<input type="url" name="wc_ps_custom_slides[<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_url( $url ); ?>" placeholder="https://" />
			</div>

			<div class="wc-ps-slide-field">
				<label><?php esc_html_e( 'Title (optional):', 'woocommerce-product-slider' ); ?></label>
				<input type="text" name="wc_ps_custom_slides[<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $title ); ?>" />
			</div>
		</div>
		<?php
	}

	/**
	 * Render custom CSS meta box.
	 *
	 * @since 1.0.0
	 * @param \WP_Post $post Current post object.
	 */
	public function render_custom_css_meta_box( $post ) {
		wp_nonce_field( 'wc_product_slider_save_custom_css', 'wc_product_slider_custom_css_nonce' );

		$custom_css = get_post_meta( $post->ID, '_wc_ps_custom_css', true );
		if ( ! is_string( $custom_css ) ) {
			$custom_css = '';
		}

		?>
		<div class="wc-ps-help-box">
			<span class="dashicons dashicons-editor-code"></span>
			<div>
				<strong><?php esc_html_e( 'Custom Styling', 'woocommerce-product-slider' ); ?></strong>
				<p><?php esc_html_e( 'Add your own CSS to customize the appearance of this slider. Your CSS will be automatically scoped to only affect this slider.', 'woocommerce-product-slider' ); ?></p>
			</div>
		</div>
		<div class="wc-ps-css-editor-wrapper">
			<div class="wc-ps-css-editor-header">
				<span>custom-styles.css</span>
			</div>
			<textarea name="wc_ps_custom_css" id="wc_ps_custom_css" rows="10" style="width:100%; font-family:monospace;"><?php echo esc_textarea( $custom_css ); ?></textarea>
		</div>
		<script>
		jQuery(document).ready(function($) {
			if (typeof wp !== 'undefined' && typeof wp.codeEditor !== 'undefined') {
				var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
				editorSettings.codemirror = _.extend(
					{},
					editorSettings.codemirror,
					{
						indentUnit: 2,
						tabSize: 2,
						mode: 'css',
						theme: 'default',
						lineNumbers: true,
						lineWrapping: true,
						styleActiveLine: true,
						matchBrackets: true,
						autoCloseBrackets: true
					}
				);
				wp.codeEditor.initialize('wc_ps_custom_css', editorSettings);
			} else {
				// Fallback: if CodeMirror is not available, show plain textarea with better styling
				$('#wc_ps_custom_css').css({
					'border': '1px solid #ddd',
					'padding': '10px',
					'background-color': '#f9f9f9',
					'border-radius': '4px'
				});
			}
		});
		</script>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @since 1.0.0
	 * @param int $post_id Post ID.
	 */
	public function save_meta_box( $post_id ) {
		// Check if this is an autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check post type.
		if ( ! isset( $_POST['post_type'] ) || 'wc_product_slider' !== $_POST['post_type'] ) {
			return;
		}

		// Check user permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save products.
		if ( isset( $_POST['wc_product_slider_products_nonce'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc_product_slider_products_nonce'] ) ), 'wc_product_slider_save_products' ) ) {

			if ( isset( $_POST['wc_ps_products'] ) && is_array( $_POST['wc_ps_products'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized via array_map absint below.
				$products_array = array_filter( array_map( 'absint', wp_unslash( $_POST['wc_ps_products'] ) ) );
				update_post_meta( $post_id, '_wc_ps_products', $products_array );
			} else {
				// If no products selected, save empty array.
				update_post_meta( $post_id, '_wc_ps_products', array() );
			}
		}

		// Save design settings.
		if ( isset( $_POST['wc_product_slider_design_nonce'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc_product_slider_design_nonce'] ) ), 'wc_product_slider_save_design' ) ) {

			if ( isset( $_POST['wc_ps_primary_color'] ) ) {
				$primary_color = sanitize_hex_color( wp_unslash( $_POST['wc_ps_primary_color'] ) );
				update_post_meta( $post_id, '_wc_ps_primary_color', $primary_color );
			}

			if ( isset( $_POST['wc_ps_secondary_color'] ) ) {
				$secondary_color = sanitize_hex_color( wp_unslash( $_POST['wc_ps_secondary_color'] ) );
				update_post_meta( $post_id, '_wc_ps_secondary_color', $secondary_color );
			}

			if ( isset( $_POST['wc_ps_button_color'] ) ) {
				$button_color = sanitize_hex_color( wp_unslash( $_POST['wc_ps_button_color'] ) );
				update_post_meta( $post_id, '_wc_ps_button_color', $button_color );
			}

			if ( isset( $_POST['wc_ps_button_text_color'] ) ) {
				$button_text_color = sanitize_hex_color( wp_unslash( $_POST['wc_ps_button_text_color'] ) );
				update_post_meta( $post_id, '_wc_ps_button_text_color', $button_text_color );
			}

			if ( isset( $_POST['wc_ps_border_radius'] ) ) {
				$border_radius = absint( $_POST['wc_ps_border_radius'] );
				update_post_meta( $post_id, '_wc_ps_border_radius', $border_radius );
			}

			if ( isset( $_POST['wc_ps_slide_gap'] ) ) {
				$slide_gap = absint( $_POST['wc_ps_slide_gap'] );
				update_post_meta( $post_id, '_wc_ps_slide_gap', $slide_gap );
			}

			// Save Navigation Customization.
			if ( isset( $_POST['wc_ps_nav_arrow_color'] ) ) {
				$nav_arrow_color = sanitize_hex_color( wp_unslash( $_POST['wc_ps_nav_arrow_color'] ) );
				update_post_meta( $post_id, '_wc_ps_nav_arrow_color', $nav_arrow_color );
			}

			if ( isset( $_POST['wc_ps_nav_arrow_bg_color'] ) ) {
				$nav_arrow_bg_color = sanitize_hex_color( wp_unslash( $_POST['wc_ps_nav_arrow_bg_color'] ) );
				update_post_meta( $post_id, '_wc_ps_nav_arrow_bg_color', $nav_arrow_bg_color );
			}

			if ( isset( $_POST['wc_ps_nav_arrow_size'] ) ) {
				$nav_arrow_size = absint( $_POST['wc_ps_nav_arrow_size'] );
				if ( $nav_arrow_size > 0 && $nav_arrow_size <= 200 ) {
					update_post_meta( $post_id, '_wc_ps_nav_arrow_size', $nav_arrow_size );
				}
			}

			// Save arrow gradient checkbox.
			$nav_arrow_gradient = isset( $_POST['wc_ps_nav_arrow_gradient'] ) ? '1' : '0';
			update_post_meta( $post_id, '_wc_ps_nav_arrow_gradient', $nav_arrow_gradient );
			if ( isset( $_POST['wc_ps_nav_progressbar_color'] ) ) {
				$nav_progressbar_color = sanitize_hex_color( wp_unslash( $_POST['wc_ps_nav_progressbar_color'] ) );
				update_post_meta( $post_id, '_wc_ps_nav_progressbar_color', $nav_progressbar_color );
			}

			if ( isset( $_POST['wc_ps_nav_progressbar_height'] ) ) {
				$nav_progressbar_height = absint( $_POST['wc_ps_nav_progressbar_height'] );
				update_post_meta( $post_id, '_wc_ps_nav_progressbar_height', $nav_progressbar_height );
			}

			if ( isset( $_POST['wc_ps_nav_progressbar_position'] ) ) {
				$nav_progressbar_position = sanitize_text_field( wp_unslash( $_POST['wc_ps_nav_progressbar_position'] ) );
				update_post_meta( $post_id, '_wc_ps_nav_progressbar_position', $nav_progressbar_position );
			}
		}

		// Save display options.
		if ( isset( $_POST['wc_product_slider_display_options_nonce'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc_product_slider_display_options_nonce'] ) ), 'wc_product_slider_save_display_options' ) ) {

			$show_title = isset( $_POST['wc_ps_show_title'] ) ? '1' : '0';
			update_post_meta( $post_id, '_wc_ps_show_title', $show_title );

			$show_price = isset( $_POST['wc_ps_show_price'] ) ? '1' : '0';
			update_post_meta( $post_id, '_wc_ps_show_price', $show_price );

			$show_description = isset( $_POST['wc_ps_show_description'] ) ? '1' : '0';
			update_post_meta( $post_id, '_wc_ps_show_description', $show_description );

			$show_button = isset( $_POST['wc_ps_show_button'] ) ? '1' : '0';
			update_post_meta( $post_id, '_wc_ps_show_button', $show_button );

			$show_image = isset( $_POST['wc_ps_show_image'] ) ? '1' : '0';
			update_post_meta( $post_id, '_wc_ps_show_image', $show_image );

			$show_rating = isset( $_POST['wc_ps_show_rating'] ) ? '1' : '0';
			update_post_meta( $post_id, '_wc_ps_show_rating', $show_rating );

			if ( isset( $_POST['wc_ps_button_text'] ) ) {
				$button_text = sanitize_text_field( wp_unslash( $_POST['wc_ps_button_text'] ) );
				update_post_meta( $post_id, '_wc_ps_button_text', $button_text );
			}

			if ( isset( $_POST['wc_ps_slider_heading'] ) ) {
				$slider_heading = sanitize_text_field( wp_unslash( $_POST['wc_ps_slider_heading'] ) );
				update_post_meta( $post_id, '_wc_ps_slider_heading', $slider_heading );
			}

			if ( isset( $_POST['wc_ps_heading_font_size'] ) ) {
				$heading_font_size = absint( $_POST['wc_ps_heading_font_size'] );
				update_post_meta( $post_id, '_wc_ps_heading_font_size', $heading_font_size );
			}

			if ( isset( $_POST['wc_ps_heading_alignment'] ) ) {
				$heading_alignment = sanitize_text_field( wp_unslash( $_POST['wc_ps_heading_alignment'] ) );
				update_post_meta( $post_id, '_wc_ps_heading_alignment', $heading_alignment );
			}

			if ( isset( $_POST['wc_ps_heading_typography'] ) ) {
				$heading_typography = sanitize_text_field( wp_unslash( $_POST['wc_ps_heading_typography'] ) );
				update_post_meta( $post_id, '_wc_ps_heading_typography', $heading_typography );
			}

			if ( isset( $_POST['wc_ps_heading_color'] ) ) {
				$heading_color = sanitize_hex_color( wp_unslash( $_POST['wc_ps_heading_color'] ) );
				update_post_meta( $post_id, '_wc_ps_heading_color', $heading_color );
			}

			$clickable_image = isset( $_POST['wc_ps_clickable_image'] ) ? '1' : '0';
			update_post_meta( $post_id, '_wc_ps_clickable_image', $clickable_image );
		}

		// Save behavior settings.
		if ( isset( $_POST['wc_product_slider_behavior_nonce'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc_product_slider_behavior_nonce'] ) ), 'wc_product_slider_save_behavior' ) ) {

			$autoplay = isset( $_POST['wc_ps_autoplay'] ) ? '1' : '0';
			update_post_meta( $post_id, '_wc_ps_autoplay', $autoplay );

			$loop = isset( $_POST['wc_ps_loop'] ) ? '1' : '0';
			update_post_meta( $post_id, '_wc_ps_loop', $loop );

			if ( isset( $_POST['wc_ps_speed'] ) ) {
				$speed = absint( $_POST['wc_ps_speed'] );
				update_post_meta( $post_id, '_wc_ps_speed', $speed );
			}

			if ( isset( $_POST['wc_ps_navigation_type'] ) ) {
				$navigation_type = sanitize_text_field( wp_unslash( $_POST['wc_ps_navigation_type'] ) );
				$allowed_types   = array( 'dots', 'progressbar', 'fraction', 'both', 'none' );
				if ( in_array( $navigation_type, $allowed_types, true ) ) {
					update_post_meta( $post_id, '_wc_ps_navigation_type', $navigation_type );
				}
			}

			if ( isset( $_POST['wc_ps_arrow_style'] ) ) {
				$arrow_style    = sanitize_text_field( wp_unslash( $_POST['wc_ps_arrow_style'] ) );
				$allowed_styles = array( 'default', 'square', 'rounded-square', 'minimal', 'coffee' );
				if ( in_array( $arrow_style, $allowed_styles, true ) ) {
					update_post_meta( $post_id, '_wc_ps_arrow_style', $arrow_style );
				}
			}

			if ( isset( $_POST['wc_ps_arrow_position'] ) ) {
				$arrow_position    = sanitize_text_field( wp_unslash( $_POST['wc_ps_arrow_position'] ) );
				$allowed_positions = array( 'inside', 'outside', 'center' );
				if ( in_array( $arrow_position, $allowed_positions, true ) ) {
					update_post_meta( $post_id, '_wc_ps_arrow_position', $arrow_position );
				}
			}

			$show_arrows = isset( $_POST['wc_ps_show_arrows'] ) ? '1' : '0';
			update_post_meta( $post_id, '_wc_ps_show_arrows', $show_arrows );
		}

		// Save custom CSS.
		if ( isset( $_POST['wc_product_slider_custom_css_nonce'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc_product_slider_custom_css_nonce'] ) ), 'wc_product_slider_save_custom_css' ) ) {

			if ( isset( $_POST['wc_ps_custom_css'] ) ) {
				// Sanitize CSS: strip tags but preserve CSS syntax (braces, colons, semicolons, etc).
				// Using wp_kses with no allowed tags strips HTML but keeps CSS intact.
				$custom_css = wp_kses( wp_unslash( $_POST['wc_ps_custom_css'] ), array() );
				update_post_meta( $post_id, '_wc_ps_custom_css', $custom_css );
			}
		}

		// Save custom slides.
		if ( isset( $_POST['wc_product_slider_custom_slides_nonce'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc_product_slider_custom_slides_nonce'] ) ), 'wc_product_slider_save_custom_slides' ) ) {

			$custom_slides = array();

			if ( isset( $_POST['wc_ps_custom_slides'] ) && is_array( $_POST['wc_ps_custom_slides'] ) ) {
				foreach ( $_POST['wc_ps_custom_slides'] as $slide ) {
					// Only save slides that have an image.
					if ( ! empty( $slide['image_id'] ) ) {
						$custom_slides[] = array(
							'image_id' => absint( $slide['image_id'] ),
							'url'      => isset( $slide['url'] ) ? esc_url_raw( wp_unslash( $slide['url'] ) ) : '',
							'title'    => isset( $slide['title'] ) ? sanitize_text_field( wp_unslash( $slide['title'] ) ) : '',
						);
					}
				}
			}

			update_post_meta( $post_id, '_wc_ps_custom_slides', $custom_slides );
		}
	}

	/**
	 * Add settings page to WordPress admin menu.
	 *
	 * @since 1.0.0
	 */
	public function add_settings_page() {
		add_submenu_page(
			'edit.php?post_type=wc_product_slider',
			__( 'Settings', 'woocommerce-product-slider' ),
			__( 'Settings', 'woocommerce-product-slider' ),
			'manage_options',
			'wc-product-slider-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render the settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		// Check user permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Handle form submission.
		if ( isset( $_POST['wc_ps_settings_nonce'] ) ) {
			$this->save_settings();
			// Only show success message if nonce was valid (settings were saved).
			if ( isset( $_POST['wc_ps_settings_nonce'] ) &&
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc_ps_settings_nonce'] ) ), 'wc_ps_save_settings' ) ) {
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved successfully.', 'woocommerce-product-slider' ) . '</p></div>';
			}
		}

		// Get current settings.
		$default_autoplay = get_option( 'wc_ps_default_autoplay', '1' );
		$default_loop     = get_option( 'wc_ps_default_loop', '1' );
		$default_speed    = get_option( 'wc_ps_default_speed', '3000' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="">
				<?php wp_nonce_field( 'wc_ps_save_settings', 'wc_ps_settings_nonce' ); ?>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="wc_ps_default_autoplay">
									<?php esc_html_e( 'Default Autoplay', 'woocommerce-product-slider' ); ?>
								</label>
							</th>
							<td>
								<input type="checkbox" name="wc_ps_default_autoplay" id="wc_ps_default_autoplay" value="1" <?php checked( $default_autoplay, '1' ); ?> />
								<p class="description">
									<?php esc_html_e( 'Enable autoplay by default for new sliders.', 'woocommerce-product-slider' ); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="wc_ps_default_loop">
									<?php esc_html_e( 'Default Loop', 'woocommerce-product-slider' ); ?>
								</label>
							</th>
							<td>
								<input type="checkbox" name="wc_ps_default_loop" id="wc_ps_default_loop" value="1" <?php checked( $default_loop, '1' ); ?> />
								<p class="description">
									<?php esc_html_e( 'Enable loop by default for new sliders.', 'woocommerce-product-slider' ); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="wc_ps_default_speed">
									<?php esc_html_e( 'Default Autoplay Speed (ms)', 'woocommerce-product-slider' ); ?>
								</label>
							</th>
							<td>
								<input type="number" name="wc_ps_default_speed" id="wc_ps_default_speed" value="<?php echo esc_attr( $default_speed ); ?>" min="1000" max="10000" step="100" />
								<p class="description">
									<?php esc_html_e( 'Default autoplay speed in milliseconds for new sliders.', 'woocommerce-product-slider' ); ?>
								</p>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button( __( 'Save Settings', 'woocommerce-product-slider' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Save settings.
	 *
	 * @since 1.0.0
	 */
	private function save_settings() {
		// Verify nonce for security.
		if ( ! isset( $_POST['wc_ps_settings_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc_ps_settings_nonce'] ) ), 'wc_ps_save_settings' ) ) {
			return;
		}

		// Save default autoplay.
		$default_autoplay = isset( $_POST['wc_ps_default_autoplay'] ) ? '1' : '0';
		update_option( 'wc_ps_default_autoplay', $default_autoplay );

		// Save default loop.
		$default_loop = isset( $_POST['wc_ps_default_loop'] ) ? '1' : '0';
		update_option( 'wc_ps_default_loop', $default_loop );

		// Save default speed.
		if ( isset( $_POST['wc_ps_default_speed'] ) ) {
			$default_speed = absint( $_POST['wc_ps_default_speed'] );
			update_option( 'wc_ps_default_speed', $default_speed );
		}
	}

	/**
	 * Add settings link to plugin list.
	 *
	 * @since 1.0.0
	 * @param array $links Existing plugin action links.
	 * @return array Modified plugin action links.
	 */
	public function add_plugin_action_links( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'edit.php?post_type=wc_product_slider&page=wc-product-slider-settings' ) ) . '">' . __( 'Settings', 'woocommerce-product-slider' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
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
