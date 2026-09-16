/**
 * Prettier config for WordPress.org Meta projects.
 *
 * Imports the default config for core compatibility, with our overrides on top.
 *
 * Usage, from a project's `.prettierrc.js`:
 *
 *     module.exports = require( './vendor/wporg/wporg-repo-tools/configs/prettier' );
 */

/**
 * WordPress dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/.prettierrc.js' );

module.exports = {
	...defaultConfig,
	printWidth: 115,
};
