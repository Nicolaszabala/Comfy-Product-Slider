/**
 * Frontend JavaScript for WooCommerce Product Slider
 *
 * Initializes Swiper.js sliders on the page.
 *
 * @package WC_Product_Slider
 */

(function() {
	'use strict';

	/**
	 * Initialize all sliders on the page
	 */
	function initSliders() {
		const sliders = document.querySelectorAll('.wc-ps-slider .swiper');

		sliders.forEach(function(sliderElement) {
			// Get configuration from data attribute
			const configData = sliderElement.getAttribute('data-config');
			let config = {};

			try {
				config = JSON.parse(configData);
			} catch (error) {
				console.error('WC Product Slider: Invalid configuration', error);
				return;
			}

			// Initialize Swiper
			// eslint-disable-next-line no-undef
			new Swiper(sliderElement, config);
		});
	}

	/**
	 * Initialize on DOM ready
	 */
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initSliders);
	} else {
		initSliders();
	}
})();
