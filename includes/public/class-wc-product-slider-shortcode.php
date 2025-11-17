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

		if ( empty( $config['products'] ) ) {
			return $this->render_error( __( 'No products selected for this slider.', 'woocommerce-product-slider' ) );
		}

		// Get WooCommerce products.
		$products = $this->get_products( $config['products'] );

		if ( empty( $products ) ) {
			return $this->render_error( __( 'No valid products found.', 'woocommerce-product-slider' ) );
		}

		// Render slider HTML.
		return $this->render_slider( $slider_id, $products, $config );
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
		$primary_color   = get_post_meta( $slider_id, '_wc_ps_primary_color', true );
		$secondary_color = get_post_meta( $slider_id, '_wc_ps_secondary_color', true );
		$speed           = absint( get_post_meta( $slider_id, '_wc_ps_speed', true ) );
		$custom_css      = get_post_meta( $slider_id, '_wc_ps_custom_css', true );

		return array(
			'products'        => ! empty( $products ) ? $products : array(),
			'primary_color'   => ! empty( $primary_color ) ? $primary_color : '#000000',
			'secondary_color' => ! empty( $secondary_color ) ? $secondary_color : '#ffffff',
			'autoplay'        => get_post_meta( $slider_id, '_wc_ps_autoplay', true ) === '1',
			'loop'            => get_post_meta( $slider_id, '_wc_ps_loop', true ) === '1',
			'speed'           => ! empty( $speed ) ? $speed : 3000,
			'custom_css'      => ! empty( $custom_css ) ? $custom_css : '',
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
	 * Render slider HTML.
	 *
	 * @since 1.0.0
	 * @param int   $slider_id Slider post ID.
	 * @param array $products  Array of WC_Product objects.
	 * @param array $config    Slider configuration.
	 * @return string Rendered slider HTML.
	 */
	protected function render_slider( $slider_id, $products, $config ) {
		// Start output buffering.
		ob_start();

		// Add inline styles if custom CSS is set.
		if ( ! empty( $config['custom_css'] ) ) {
			echo '<style type="text/css">' . wp_kses_post( $config['custom_css'] ) . '</style>';
		}

		// Render slider container.
		?>
		<div class="wc-ps-slider wc-ps-slider-<?php echo esc_attr( $slider_id ); ?>" data-slider-id="<?php echo esc_attr( $slider_id ); ?>">
			<div class="swiper" data-config='<?php echo esc_attr( wp_json_encode( $this->get_swiper_config( $config ) ) ); ?>'>
				<div class="swiper-wrapper">
					<?php foreach ( $products as $product ) : ?>
						<div class="swiper-slide">
							<div class="wc-ps-product">
								<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="wc-ps-product-link">
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

									<div class="wc-ps-product-info">
										<h3 class="wc-ps-product-title"><?php echo esc_html( $product->get_name() ); ?></h3>

										<div class="wc-ps-product-price">
											<?php echo wp_kses_post( $product->get_price_html() ); ?>
										</div>

										<?php if ( $product->is_on_sale() ) : ?>
											<span class="wc-ps-product-badge wc-ps-product-badge-sale">
												<?php esc_html_e( 'Sale!', 'woocommerce-product-slider' ); ?>
											</span>
										<?php endif; ?>

										<div class="wc-ps-product-actions">
											<?php
											// Add to cart button.
											// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
											echo apply_filters( // woocommerce_ core hook
												'woocommerce_loop_add_to_cart_link',
												sprintf(
													'<a href="%s" data-product_id="%s" class="button product_type_%s">%s</a>',
													esc_url( $product->add_to_cart_url() ),
													esc_attr( $product->get_id() ),
													esc_attr( $product->get_type() ),
													esc_html( $product->add_to_cart_text() )
												),
												$product
											);
											// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
											?>
										</div>
									</div>
								</a>
							</div>
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
