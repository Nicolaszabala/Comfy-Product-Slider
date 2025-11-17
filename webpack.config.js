/**
 * Webpack configuration for WooCommerce Product Slider
 *
 * Extends the default @wordpress/scripts webpack config.
 *
 * @package
 */

const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		admin: path.resolve( process.cwd(), 'src', 'admin.js' ),
	},
};
