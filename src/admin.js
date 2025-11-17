/**
 * Admin JavaScript Entry Point
 *
 * Initializes React components for the admin interface.
 *
 * @package
 */

import { render } from '@wordpress/element';
import ProductSelector from './components/ProductSelector';
import './admin.css';

/**
 * Copy shortcode to clipboard
 */
function copyShortcodeToClipboard() {
	const copyButton = document.querySelector( '.wc-ps-copy-shortcode' );

	if ( ! copyButton ) {
		return;
	}

	const feedback = document.querySelector( '.wc-ps-copy-feedback' );

	copyButton.addEventListener( 'click', async () => {
		const shortcode = copyButton.getAttribute( 'data-shortcode' );

		try {
			// Use modern Clipboard API
			// eslint-disable-next-line no-undef
			await navigator.clipboard.writeText( shortcode );

			// Show success feedback
			if ( feedback ) {
				feedback.style.display = 'block';
				setTimeout( () => {
					feedback.style.display = 'none';
				}, 3000 );
			}

			// Change button text temporarily
			const originalText = copyButton.textContent;
			copyButton.textContent = 'Copied!';
			copyButton.disabled = true;

			setTimeout( () => {
				copyButton.textContent = originalText;
				copyButton.disabled = false;
			}, 2000 );
		} catch ( error ) {
			// Fallback for older browsers
			const input = document.getElementById( 'wc-ps-shortcode-input' );
			if ( input ) {
				input.select();
				document.execCommand( 'copy' );

				if ( feedback ) {
					feedback.style.display = 'block';
					setTimeout( () => {
						feedback.style.display = 'none';
					}, 3000 );
				}
			}
		}
	} );
}

/**
 * Initialize Product Selector when DOM is ready
 */
document.addEventListener( 'DOMContentLoaded', () => {
	// Initialize shortcode copy functionality
	copyShortcodeToClipboard();
	const selectorRoot = document.getElementById( 'wc-ps-product-selector' );

	if ( selectorRoot ) {
		// Get hidden input for saving product IDs
		const hiddenInput = document.getElementById( 'wc_ps_products' );

		// TODO: Fetch initial product data from hidden input
		// For now, start with empty selection
		const initialProducts = [];

		// Handle product selection changes
		const handleChange = ( selectedProducts ) => {
			// Update hidden input with product IDs
			if ( hiddenInput ) {
				const ids = selectedProducts.map( ( product ) => product.id );
				hiddenInput.value = ids.join( ',' );
			}
		};

		// Render the Product Selector component
		render(
			<ProductSelector
				selectedProducts={ initialProducts }
				onChange={ handleChange }
			/>,
			selectorRoot
		);
	}
} );
