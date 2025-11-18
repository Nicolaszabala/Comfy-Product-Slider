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

		// Enqueue CodeMirror.
		wp_enqueue_script( 'code-editor' );
		wp_enqueue_script( 'csslint' );

		// Enqueue custom admin script.
		wp_enqueue_script(
			'wc-product-slider-admin',
			plugin_dir_url( dirname( __DIR__ ) ) . 'assets/js/admin.js',
			array( 'jquery', 'select2', 'code-editor', 'media-upload', 'media-views' ),
			$this->version,
			true
		);

		// Localize script.
		wp_localize_script(
			'wc-product-slider-admin',
			'wcProductSlider',
			array(
				'restUrl'   => esc_url_raw( rest_url() ),
				'nonce'     => wp_create_nonce( 'wp_rest' ),
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'pluginUrl' => plugin_dir_url( dirname( __DIR__ ) ),
			)
		);
	}

	/**
	 * Register meta boxes for slider configuration.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'wc_product_slider_products',
			__( 'Products', 'woocommerce-product-slider' ),
			array( $this, 'render_products_meta_box' ),
			'wc_product_slider',
			'normal',
			'high'
		);

		add_meta_box(
			'wc_product_slider_design',
			__( 'Design Settings', 'woocommerce-product-slider' ),
			array( $this, 'render_design_meta_box' ),
			'wc_product_slider',
			'normal',
			'default'
		);

		add_meta_box(
			'wc_product_slider_behavior',
			__( 'Behavior Settings', 'woocommerce-product-slider' ),
			array( $this, 'render_behavior_meta_box' ),
			'wc_product_slider',
			'side',
			'default'
		);

		add_meta_box(
			'wc_product_slider_shortcode',
			__( 'Shortcode', 'woocommerce-product-slider' ),
			array( $this, 'render_shortcode_meta_box' ),
			'wc_product_slider',
			'side',
			'high'
		);

		add_meta_box(
			'wc_product_slider_custom_css',
			__( 'Custom CSS', 'woocommerce-product-slider' ),
			array( $this, 'render_custom_css_meta_box' ),
			'wc_product_slider',
			'normal',
			'low'
		);

		add_meta_box(
			'wc_product_slider_display_options',
			__( 'Display Options', 'woocommerce-product-slider' ),
			array( $this, 'render_display_options_meta_box' ),
			'wc_product_slider',
			'side',
			'default'
		);

		add_meta_box(
			'wc_product_slider_custom_slides',
			__( 'Custom Slides', 'woocommerce-product-slider' ),
			array( $this, 'render_custom_slides_meta_box' ),
			'wc_product_slider',
			'normal',
			'default'
		);
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

		// Get all published products.
		$products = wc_get_products(
			array(
				'status' => 'publish',
				'limit'  => -1,
			)
		);

		?>
		<p><?php esc_html_e( 'Select products to display in this slider.', 'woocommerce-product-slider' ); ?></p>
		<select name="wc_ps_products[]" id="wc_ps_products" multiple="multiple" style="width:100%; min-height:200px;">
			<?php foreach ( $products as $product ) : ?>
				<option value="<?php echo esc_attr( $product->get_id() ); ?>"
					<?php selected( in_array( $product->get_id(), $selected_products, true ) ); ?>>
					<?php echo esc_html( $product->get_name() ); ?> (ID: <?php echo esc_html( $product->get_id() ); ?>)
				</option>
			<?php endforeach; ?>
		</select>
		<p class="description">
			<?php esc_html_e( 'Hold Ctrl (Cmd on Mac) to select multiple products.', 'woocommerce-product-slider' ); ?>
		</p>
		<script>
		jQuery(document).ready(function($) {
			if (typeof $.fn.select2 !== 'undefined') {
				$('#wc_ps_products').select2({
					placeholder: '<?php esc_attr_e( 'Select products...', 'woocommerce-product-slider' ); ?>',
					width: '100%'
				});
			}
		});
		</script>
		<?php
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
			$primary_color = '#000000';
		}
		if ( empty( $secondary_color ) ) {
			$secondary_color = '#ffffff';
		}
		if ( empty( $button_color ) ) {
			$button_color = '#0073aa';
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
		?>
		<table class="form-table">
			<tr>
				<th><label for="wc_ps_primary_color"><?php esc_html_e( 'Primary Color:', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="color" name="wc_ps_primary_color" id="wc_ps_primary_color" value="<?php echo esc_attr( $primary_color ); ?>" /></td>
			</tr>
			<tr>
				<th><label for="wc_ps_secondary_color"><?php esc_html_e( 'Secondary Color:', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="color" name="wc_ps_secondary_color" id="wc_ps_secondary_color" value="<?php echo esc_attr( $secondary_color ); ?>" /></td>
			</tr>
			<tr>
				<th><label for="wc_ps_button_color"><?php esc_html_e( 'Button Color:', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="color" name="wc_ps_button_color" id="wc_ps_button_color" value="<?php echo esc_attr( $button_color ); ?>" /></td>
			</tr>
			<tr>
				<th><label for="wc_ps_button_text_color"><?php esc_html_e( 'Button Text Color:', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="color" name="wc_ps_button_text_color" id="wc_ps_button_text_color" value="<?php echo esc_attr( $button_text_color ); ?>" /></td>
			</tr>
			<tr>
				<th><label for="wc_ps_border_radius"><?php esc_html_e( 'Border Radius (px):', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="number" name="wc_ps_border_radius" id="wc_ps_border_radius" value="<?php echo esc_attr( $border_radius ); ?>" min="0" max="50" /></td>
			</tr>
			<tr>
				<th><label for="wc_ps_slide_gap"><?php esc_html_e( 'Gap Between Slides (px):', 'woocommerce-product-slider' ); ?></label></th>
				<td><input type="number" name="wc_ps_slide_gap" id="wc_ps_slide_gap" value="<?php echo esc_attr( $slide_gap ); ?>" min="0" max="100" /></td>
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

		$autoplay = get_post_meta( $post->ID, '_wc_ps_autoplay', true );
		$loop     = get_post_meta( $post->ID, '_wc_ps_loop', true );
		$speed    = get_post_meta( $post->ID, '_wc_ps_speed', true );

		if ( empty( $speed ) ) {
			$speed = 3000;
		}
		?>
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
		$slider_heading   = get_post_meta( $post->ID, '_wc_ps_slider_heading', true );
		$clickable_image  = get_post_meta( $post->ID, '_wc_ps_clickable_image', true );

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
		?>
		<p>
			<label for="wc_ps_slider_heading">
				<?php esc_html_e( 'Slider Heading:', 'woocommerce-product-slider' ); ?>
			</label><br>
			<input type="text" name="wc_ps_slider_heading" id="wc_ps_slider_heading" value="<?php echo esc_attr( $slider_heading ); ?>" style="width:100%;" />
		</p>
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
			<p class="description">
				<?php esc_html_e( 'Add custom images with URLs. These will be added to the slider along with selected products.', 'woocommerce-product-slider' ); ?>
			</p>

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
		<p class="description">
			<?php esc_html_e( 'Add custom CSS to style this slider. CSS will be wrapped with a unique slider class.', 'woocommerce-product-slider' ); ?>
		</p>
		<textarea name="wc_ps_custom_css" id="wc_ps_custom_css" rows="10" style="width:100%; font-family:monospace;"><?php echo esc_textarea( $custom_css ); ?></textarea>
		<script>
		jQuery(document).ready(function($) {
			if (typeof wp !== 'undefined' && typeof wp.codeEditor !== 'undefined') {
				wp.codeEditor.initialize('wc_ps_custom_css', {
					type: 'text/css',
					codemirror: {
						indentUnit: 2,
						tabSize: 2,
						mode: 'css'
					}
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

			if ( isset( $_POST['wc_ps_products'] ) ) {
				$products       = sanitize_text_field( wp_unslash( $_POST['wc_ps_products'] ) );
				$products_array = array_filter( array_map( 'absint', explode( ',', $products ) ) );
				update_post_meta( $post_id, '_wc_ps_products', $products_array );
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
		}

		// Save custom CSS.
		if ( isset( $_POST['wc_product_slider_custom_css_nonce'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc_product_slider_custom_css_nonce'] ) ), 'wc_product_slider_save_custom_css' ) ) {

			if ( isset( $_POST['wc_ps_custom_css'] ) ) {
				$custom_css = wp_strip_all_tags( wp_unslash( $_POST['wc_ps_custom_css'] ) );
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
