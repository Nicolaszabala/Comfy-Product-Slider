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
 * Initialize Product Selector when DOM is ready
 */
document.addEventListener( 'DOMContentLoaded', () => {
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
