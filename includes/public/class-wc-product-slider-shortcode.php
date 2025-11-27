<?php
/**
 * Shortcode handler for product sliders.
 *
 * Handles the [wc_product_slider] shortcode rendering.
 *
 * @package    WC_Product_Slider
 * @subpackage WC_Product_Slider/Public
 * @since      1.0.0
 */

namespace WC_Product_Slider\PublicFacing;

use WC_Product_Slider\Core\WC_Product_Slider_Sanitizer;

/**
 * Shortcode class
 *
 * Handles shortcode registration and rendering.
 *
 * @since 1.0.0
 */
class WC_Product_Slider_Shortcode {

	/**
	 * Register the shortcode.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		add_shortcode( 'wc_product_slider', array( $this, 'render' ) );
	}

	/**
	 * Render the shortcode.
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes.
	 * @return string Rendered slider HTML.
	 */
	public function render( $atts ) {
		// Parse shortcode attributes.
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'wc_product_slider'
		);

		// Sanitize slider ID.
		$slider_id = WC_Product_Slider_Sanitizer::sanitize_integer( $atts['id'] );

		if ( ! $slider_id ) {
			return $this->render_error( __( 'Invalid slider ID.', 'woocommerce-product-slider' ) );
		}

		// Check if slider exists and is published.
		$slider = get_post( $slider_id );

		if ( ! $slider || 'wc_product_slider' !== $slider->post_type || 'publish' !== $slider->post_status ) {
			return $this->render_error( __( 'Slider not found or not published.', 'woocommerce-product-slider' ) );
		}

		// Get slider configuration.
		$config = $this->get_slider_config( $slider_id );

		// Validate that we have either products or custom slides.
		if ( empty( $config['products'] ) && empty( $config['custom_slides'] ) ) {
			return $this->render_error( __( 'No products or custom slides selected for this slider.', 'woocommerce-product-slider' ) );
		}

		// Get WooCommerce products.
		$products = $this->get_products( $config['products'] );

		// Merge products and custom slides into a single slides array.
		$slides = $this->merge_slides( $products, $config['custom_slides'] );

		if ( empty( $slides ) ) {
			return $this->render_error( __( 'No valid slides found.', 'woocommerce-product-slider' ) );
		}

		// Render slider HTML.
		return $this->render_slider( $slider_id, $slides, $config );
	}

	/**
	 * Get slider configuration from post meta.
	 *
	 * @since 1.0.0
	 * @param int $slider_id Slider post ID.
	 * @return array Slider configuration.
	 */
	protected function get_slider_config( $slider_id ) {
		$products        = get_post_meta( $slider_id, '_wc_ps_products', true );
		$custom_slides   = get_post_meta( $slider_id, '_wc_ps_custom_slides', true );
		$primary_color   = get_post_meta( $slider_id, '_wc_ps_primary_color', true );
		$secondary_color = get_post_meta( $slider_id, '_wc_ps_secondary_color', true );
		$speed           = absint( get_post_meta( $slider_id, '_wc_ps_speed', true ) );
		$custom_css      = get_post_meta( $slider_id, '_wc_ps_custom_css', true );

		// Get display options.
		$show_title       = get_post_meta( $slider_id, '_wc_ps_show_title', true );
		$show_price       = get_post_meta( $slider_id, '_wc_ps_show_price', true );
		$show_description = get_post_meta( $slider_id, '_wc_ps_show_description', true );
		$show_button      = get_post_meta( $slider_id, '_wc_ps_show_button', true );
		$show_image       = get_post_meta( $slider_id, '_wc_ps_show_image', true );
		$show_rating      = get_post_meta( $slider_id, '_wc_ps_show_rating', true );
		$button_text      = get_post_meta( $slider_id, '_wc_ps_button_text', true );
		$slider_heading   = get_post_meta( $slider_id, '_wc_ps_slider_heading', true );
		$clickable_image  = get_post_meta( $slider_id, '_wc_ps_clickable_image', true );

		return array(
			'products'         => ! empty( $products ) ? $products : array(),
			'custom_slides'    => ! empty( $custom_slides ) ? $custom_slides : array(),
			'primary_color'    => ! empty( $primary_color ) ? $primary_color : '#000000',
			'secondary_color'  => ! empty( $secondary_color ) ? $secondary_color : '#ffffff',
			'autoplay'         => get_post_meta( $slider_id, '_wc_ps_autoplay', true ) === '1',
			'loop'             => get_post_meta( $slider_id, '_wc_ps_loop', true ) === '1',
			'speed'            => ! empty( $speed ) ? $speed : 3000,
			'custom_css'       => ! empty( $custom_css ) ? $custom_css : '',
			'show_title'       => $show_title !== '0',
			'show_price'       => $show_price !== '0',
			'show_description' => $show_description === '1',
			'show_button'      => $show_button !== '0',
			'show_image'       => $show_image !== '0',
			'show_rating'      => $show_rating === '1',
			'button_text'      => ! empty( $button_text ) ? $button_text : __( 'View Product', 'woocommerce-product-slider' ),
			'slider_heading'   => ! empty( $slider_heading ) ? $slider_heading : '',
			'clickable_image'  => $clickable_image !== '0',
		);
	}

	/**
	 * Get WooCommerce products by IDs.
	 *
	 * @since 1.0.0
	 * @param array $product_ids Array of product IDs.
	 * @return array Array of WC_Product objects.
	 */
	protected function get_products( $product_ids ) {
		if ( ! function_exists( 'wc_get_product' ) ) {
			return array();
		}

		$products = array();

		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );

			if ( $product && 'publish' === $product->get_status() ) {
				$products[] = $product;
			}
		}

		return $products;
	}

	/**
	 * Merge WooCommerce products and custom slides into a single array.
	 *
	 * @since 1.0.0
	 * @param array $products      Array of WC_Product objects.
	 * @param array $custom_slides Array of custom slide data.
	 * @return array Merged array of slides with type indicators.
	 */
	protected function merge_slides( $products, $custom_slides ) {
		$slides = array();

		// Add WooCommerce products.
		foreach ( $products as $product ) {
			$slides[] = array(
				'type' => 'product',
				'data' => $product,
			);
		}

		// Add custom slides.
		foreach ( $custom_slides as $slide ) {
			$slides[] = array(
				'type' => 'custom',
				'data' => $slide,
			);
		}

		return $slides;
	}

	/**
	 * Render slider HTML.
	 *
	 * @since 1.0.0
	 * @param int   $slider_id Slider post ID.
	 * @param array $slides    Array of slides (products and custom slides).
	 * @param array $config    Slider configuration.
	 * @return string Rendered slider HTML.
	 */
	protected function render_slider( $slider_id, $slides, $config ) {
		// Start output buffering.
		ob_start();

		// Add inline styles if custom CSS is set.
		if ( ! empty( $config['custom_css'] ) ) {
			echo '<style type="text/css">' . wp_kses_post( $config['custom_css'] ) . '</style>';
		}

		// Render slider container.
		?>
		<div class="wc-ps-slider wc-ps-slider-<?php echo esc_attr( $slider_id ); ?>" data-slider-id="<?php echo esc_attr( $slider_id ); ?>">
			<?php if ( ! empty( $config['slider_heading'] ) ) : ?>
				<h2 class="wc-ps-slider-heading"><?php echo esc_html( $config['slider_heading'] ); ?></h2>
			<?php endif; ?>
			<div class="swiper" data-config='<?php echo esc_attr( wp_json_encode( $this->get_swiper_config( $config ) ) ); ?>'>
				<div class="swiper-wrapper">
					<?php foreach ( $slides as $slide ) : ?>
						<div class="swiper-slide">
							<?php
							if ( 'product' === $slide['type'] ) {
								$this->render_product_slide( $slide['data'], $config );
							} elseif ( 'custom' === $slide['type'] ) {
								$this->render_custom_slide( $slide['data'], $config );
							}
							?>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- Navigation -->
				<div class="swiper-button-prev" style="color: <?php echo esc_attr( $config['primary_color'] ); ?>;"></div>
				<div class="swiper-button-next" style="color: <?php echo esc_attr( $config['primary_color'] ); ?>;"></div>

				<!-- Pagination -->
				<div class="swiper-pagination" style="--swiper-pagination-color: <?php echo esc_attr( $config['primary_color'] ); ?>;"></div>
			</div>
		</div>
		<?php

		// Return buffered content.
		return ob_get_clean();
	}

	/**
	 * Render a product slide.
	 *
	 * @since 1.0.0
	 * @param \WC_Product $product WooCommerce product object.
	 * @param array       $config  Slider configuration.
	 */
	protected function render_product_slide( $product, $config ) {
		?>
		<div class="wc-ps-product">
			<?php
			$link_url      = $product->get_permalink();
			$clickable     = $config['clickable_image'];
			$wrapper_tag   = $clickable ? 'a' : 'div';
			$wrapper_attrs = $clickable ? 'href="' . esc_url( $link_url ) . '" class="wc-ps-product-link"' : 'class="wc-ps-product-content"';
			?>
			<<?php echo esc_attr( $wrapper_tag ); ?> <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?>>
				<?php if ( $config['show_image'] ) : ?>
					<?php
					// Product image.
					$image_id = $product->get_image_id();
					if ( $image_id ) {
						echo wp_get_attachment_image(
							$image_id,
							'woocommerce_thumbnail',
							false,
							array(
								'class' => 'wc-ps-product-image',
								'alt'   => $product->get_name(),
							)
						);
					} else {
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce core function with built-in escaping.
						echo wc_placeholder_img( 'woocommerce_thumbnail', array( 'class' => 'wc-ps-product-image' ) );
					}
					?>
				<?php endif; ?>

				<div class="wc-ps-product-info">
					<?php if ( $config['show_title'] ) : ?>
						<h3 class="wc-ps-product-title"><?php echo esc_html( $product->get_name() ); ?></h3>
					<?php endif; ?>

					<?php if ( $config['show_price'] ) : ?>
						<div class="wc-ps-product-price">
							<?php echo wp_kses_post( $product->get_price_html() ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $config['show_rating'] && $product->get_average_rating() > 0 ) : ?>
						<div class="wc-ps-product-rating">
							<?php echo wc_get_rating_html( $product->get_average_rating() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce core function. ?>
						</div>
					<?php endif; ?>

					<?php if ( $config['show_description'] && $product->get_short_description() ) : ?>
						<div class="wc-ps-product-description">
							<?php echo wp_kses_post( $product->get_short_description() ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $product->is_on_sale() ) : ?>
						<span class="wc-ps-product-badge wc-ps-product-badge-sale">
							<?php esc_html_e( 'Sale!', 'woocommerce-product-slider' ); ?>
						</span>
					<?php endif; ?>

					<?php if ( $config['show_button'] ) : ?>
						<div class="wc-ps-product-actions">
							<a href="<?php echo esc_url( $link_url ); ?>" class="button wc-ps-view-product">
								<?php echo esc_html( $config['button_text'] ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</<?php echo esc_attr( $wrapper_tag ); ?>>
		</div>
		<?php
	}

	/**
	 * Render a custom slide.
	 *
	 * @since 1.0.0
	 * @param array $slide  Custom slide data.
	 * @param array $config Slider configuration.
	 */
	protected function render_custom_slide( $slide, $config ) {
		$image_id = isset( $slide['image_id'] ) ? absint( $slide['image_id'] ) : 0;
		$url      = isset( $slide['url'] ) ? esc_url( $slide['url'] ) : '';
		$title    = isset( $slide['title'] ) ? esc_html( $slide['title'] ) : '';

		if ( ! $image_id ) {
			return;
		}

		$image_url = wp_get_attachment_url( $image_id );
		if ( ! $image_url ) {
			return;
		}

		$clickable     = $config['clickable_image'] && ! empty( $url );
		$wrapper_tag   = $clickable ? 'a' : 'div';
		$wrapper_attrs = $clickable ? 'href="' . esc_url( $url ) . '" class="wc-ps-custom-slide-link"' : 'class="wc-ps-custom-slide-content"';
		?>
		<div class="wc-ps-custom-slide">
			<<?php echo esc_attr( $wrapper_tag ); ?> <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?>>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="wc-ps-custom-slide-image" />
				<?php if ( ! empty( $title ) ) : ?>
					<div class="wc-ps-custom-slide-title"><?php echo esc_html( $title ); ?></div>
				<?php endif; ?>
			</<?php echo esc_attr( $wrapper_tag ); ?>>
		</div>
		<?php
	}

	/**
	 * Get Swiper.js configuration.
	 *
	 * @since 1.0.0
	 * @param array $config Slider configuration.
	 * @return array Swiper configuration array.
	 */
	protected function get_swiper_config( $config ) {
		return array(
			'slidesPerView' => 1,
			'spaceBetween'  => 20,
			'loop'          => $config['loop'],
			'autoplay'      => $config['autoplay'] ? array(
				'delay'                => $config['speed'],
				'disableOnInteraction' => false,
			) : false,
			'pagination'    => array(
				'el'        => '.swiper-pagination',
				'clickable' => true,
			),
			'navigation'    => array(
				'nextEl' => '.swiper-button-next',
				'prevEl' => '.swiper-button-prev',
			),
			'breakpoints'   => array(
				640  => array(
					'slidesPerView' => 2,
					'spaceBetween'  => 20,
				),
				768  => array(
					'slidesPerView' => 3,
					'spaceBetween'  => 30,
				),
				1024 => array(
					'slidesPerView' => 4,
					'spaceBetween'  => 40,
				),
			),
		);
	}

	/**
	 * Render error message.
	 *
	 * @since 1.0.0
	 * @param string $message Error message.
	 * @return string Rendered error HTML.
	 */
	protected function render_error( $message ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return ''; // Hide errors from non-admins.
		}

		return sprintf(
			'<div class="wc-ps-error">%s</div>',
			esc_html( $message )
		);
	}
}
