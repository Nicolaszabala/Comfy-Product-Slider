/**
 * WooCommerce Product Slider - Admin JavaScript
 */

(function ($) {
	'use strict';

	$(document).ready(function () {

		// Initialize WordPress Color Picker with Alpha
		if (typeof $.fn.wpColorPicker === 'function') {
			console.log('Initializing color pickers...');
			console.log('Found color pickers:', $('.wc-ps-color-picker').length);

			$('.wc-ps-color-picker').each(function() {
				var $input = $(this);
				console.log('Initializing picker for:', $input.attr('id'), 'Alpha enabled:', $input.data('alpha-enabled'));

				// Check if alpha is enabled for this input
				var alphaEnabled = $input.data('alpha-enabled') === true || $input.data('alpha-enabled') === 'true';

				var pickerOptions = {
					palettes: ['#4A403A', '#D4A373', '#ffffff', '#000000', '#2271b1', 'rgba(255,255,255,0)']
				};

				// Add alpha-specific options if enabled
				if (alphaEnabled) {
					pickerOptions.type = 'full';
					pickerOptions.alphaEnabled = true;
					pickerOptions.alphaColorType = 'rgba';
					pickerOptions.alphaColorWithSpace = false;
				}

				console.log('Picker options:', pickerOptions);
				$input.wpColorPicker(pickerOptions);
			});
		} else {
			console.error('wpColorPicker function not available!');
		}

		// Initialize Select2 with AJAX
		// Initialize Select2 with AJAX
		if (typeof $.fn.select2 !== 'undefined' && $('#wc_ps_products').length) {
			$('#wc_ps_products').select2({
				placeholder: 'Search and select products...',
				width: '100%',
				allowClear: true,
				ajax: {
					url: wcProductSlider.ajaxUrl,
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							action: 'wc_ps_search_products',
							term: params.term,
							nonce: wcProductSlider.searchNonce
						};
					},
					processResults: function (data) {
						// Check if the response is successful and has data
						if (data.success && data.data) {
							return {
								results: data.data
							};
						}
						return {
							results: []
						};
					},
					cache: true
				},
				minimumInputLength: 3,
				language: {
					inputTooShort: function () {
						return "Please enter 3 or more characters to search";
					},
					noResults: function () {
						return "No products found";
					},
					searching: function () {
						return "Searching...";
					}
				}
			});
		}

		// Copy shortcode functionality
		$('.wc-ps-copy-shortcode').on('click', function (e) {
			e.preventDefault();

			var input = $('#wc-ps-shortcode-input');
			input.select();
			document.execCommand('copy');

			// Show feedback
			var feedback = $('.wc-ps-copy-feedback');
			feedback.fadeIn();
			setTimeout(function () {
				feedback.fadeOut();
			}, 2000);
		});

		// Preview Functionality
		$('.wc-ps-refresh-preview').on('click', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var $container = $('#wc-ps-preview-container');

			// Add loading state
			$btn.addClass('updating-message').prop('disabled', true);
			$container.css('opacity', '0.5');

			// Collect form data
			// We need to get data from all inputs, selects, and textareas in the form
			// The form ID in WP admin is usually 'post'
			var formData = $('#post').serialize();

			$.ajax({
				url: wcProductSlider.ajaxUrl,
				type: 'POST',
				data: {
					action: 'wc_ps_preview_slider',
					nonce: wcProductSlider.previewNonce,
					formData: formData
				},
				success: function (response) {
					if (response.success) {
						$container.html(response.data);

						// Initialize Swiper
						// We need to find the swiper element and init it
						$container.find('.swiper').each(function () {
							var config = $(this).data('config');
							if (config && typeof Swiper !== 'undefined') {
								new Swiper(this, config);
							}
						});
					} else {
						$container.html('<div class="wc-ps-error">' + (response.data || 'Error generating preview') + '</div>');
					}
				},
				error: function () {
					$container.html('<div class="wc-ps-error">Server error. Please try again.</div>');
				},
				complete: function () {
					$btn.removeClass('updating-message').prop('disabled', false);
					$container.css('opacity', '1');
				}
			});
		});

	});

})(jQuery);
