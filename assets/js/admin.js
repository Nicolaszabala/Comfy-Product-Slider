/**
 * WooCommerce Product Slider - Admin JavaScript
 */

(function($) {
	'use strict';

	$(document).ready(function() {

		// Copy shortcode functionality
		$('.wc-ps-copy-shortcode').on('click', function(e) {
			e.preventDefault();

			var input = $('#wc-ps-shortcode-input');
			input.select();
			document.execCommand('copy');

			// Show feedback
			var feedback = $('.wc-ps-copy-feedback');
			feedback.fadeIn();
			setTimeout(function() {
				feedback.fadeOut();
			}, 2000);
		});

	});

})(jQuery);
