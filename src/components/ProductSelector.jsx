/**
 * Product Selector React Component
 *
 * Allows users to search and select WooCommerce products for the slider.
 *
 * @package
 */

import { useState, useEffect } from '@wordpress/element';
import { SearchControl, Spinner, Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

const ProductSelector = ( { selectedProducts = [], onChange } ) => {
	const [ searchTerm, setSearchTerm ] = useState( '' );
	const [ searchResults, setSearchResults ] = useState( [] );
	const [ isSearching, setIsSearching ] = useState( false );
	const [ selectedItems, setSelectedItems ] = useState( selectedProducts );

	// Debounced search
	useEffect( () => {
		if ( ! searchTerm || searchTerm.length < 2 ) {
			setSearchResults( [] );
			return;
		}

		const timeoutId = setTimeout( () => {
			searchProducts( searchTerm );
		}, 500 );

		return () => clearTimeout( timeoutId );
	}, [ searchTerm ] );

	const searchProducts = async ( term ) => {
		setIsSearching( true );
		try {
			const products = await apiFetch( {
				path: `/wc/v3/products?search=${ encodeURIComponent(
					term
				) }&per_page=10`,
			} );
			setSearchResults( products );
		} catch ( error ) {
			// eslint-disable-next-line no-console
			console.error( 'Error searching products:', error );
			setSearchResults( [] );
		} finally {
			setIsSearching( false );
		}
	};

	const addProduct = ( product ) => {
		if ( ! selectedItems.find( ( item ) => item.id === product.id ) ) {
			const newSelected = [ ...selectedItems, product ];
			setSelectedItems( newSelected );
			onChange( newSelected );
		}
		setSearchTerm( '' );
		setSearchResults( [] );
	};

	const removeProduct = ( productId ) => {
		const newSelected = selectedItems.filter(
			( item ) => item.id !== productId
		);
		setSelectedItems( newSelected );
		onChange( newSelected );
	};

	return (
		<div className="wc-ps-product-selector">
			<SearchControl
				label="Search Products"
				value={ searchTerm }
				onChange={ setSearchTerm }
				placeholder="Type to search WooCommerce products..."
			/>

			{ isSearching && (
				<div className="wc-ps-searching">
					<Spinner />
					<span>Searching...</span>
				</div>
			) }

			{ searchResults.length > 0 && (
				<ul className="wc-ps-search-results">
					{ searchResults.map( ( product ) => (
						<li
							key={ product.id }
							className="wc-ps-search-result-item"
						>
							<div className="wc-ps-product-info">
								{ product.images && product.images[ 0 ] && (
									<img
										src={ product.images[ 0 ].src }
										alt={ product.name }
										className="wc-ps-product-thumb"
									/>
								) }
								<div className="wc-ps-product-details">
									<strong>{ product.name }</strong>
									<span className="wc-ps-product-price">
										{ product.price_html ? (
											<span
												dangerouslySetInnerHTML={ {
													__html: product.price_html,
												} }
											/>
										) : (
											'N/A'
										) }
									</span>
								</div>
							</div>
							<Button
								isSecondary
								onClick={ () => addProduct( product ) }
								disabled={ selectedItems.find(
									( item ) => item.id === product.id
								) }
							>
								{ selectedItems.find(
									( item ) => item.id === product.id
								)
									? 'Added'
									: 'Add' }
							</Button>
						</li>
					) ) }
				</ul>
			) }

			<div className="wc-ps-selected-products">
				<h4>Selected Products ({ selectedItems.length })</h4>
				{ selectedItems.length === 0 ? (
					<p className="wc-ps-no-products">
						No products selected yet.
					</p>
				) : (
					<ul className="wc-ps-selected-list">
						{ selectedItems.map( ( product ) => (
							<li
								key={ product.id }
								className="wc-ps-selected-item"
							>
								{ product.images && product.images[ 0 ] && (
									<img
										src={ product.images[ 0 ].src }
										alt={ product.name }
										className="wc-ps-product-thumb"
									/>
								) }
								<span className="wc-ps-product-name">
									{ product.name }
								</span>
								<Button
									isDestructive
									isSmall
									onClick={ () =>
										removeProduct( product.id )
									}
								>
									Remove
								</Button>
							</li>
						) ) }
					</ul>
				) }
			</div>
		</div>
	);
};

export default ProductSelector;
