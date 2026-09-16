/**
 * Prettier config for WordPress.org Meta projects.
 *
 * The WordPress default, with our overrides on top.
 */

/**
 * WordPress dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/.prettierrc.js' );

module.exports = {
	...defaultConfig,
	printWidth: 115,
};
