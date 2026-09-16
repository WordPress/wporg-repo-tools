/**
 * Flat ESLint config for WordPress.org Meta projects.
 *
 * For ESLint 9+ / `@wordpress/scripts` 32+, which ignore the legacy
 * `.eslintrc.js` format. Projects on older tooling want `./eslintrc.js`.
 *
 * Usage, from a project's `eslint.config.js`:
 *
 *     const createConfig = require( './vendor/wporg/wporg-repo-tools/configs/eslint' );
 *
 *     module.exports = createConfig( { textDomain: 'wporg' } );
 *
 * Extra entries can be appended to the returned array, and the returned array
 * is a fresh copy on every call, so mutating it is safe.
 */

/**
 * WordPress dependencies
 */
const wpPlugin = require( '@wordpress/eslint-plugin' );

/**
 * Internal dependencies
 */
const sharedRules = require( './rules' );

/**
 * Build the flat config.
 *
 * @param {Object} options            Options.
 * @param {string} options.textDomain The project's text domain.
 *
 * @return {Array} A flat ESLint config array.
 */
module.exports = ( { textDomain } ) => [
	{
		ignores: [ '**/*.min.js' ],
	},

	...wpPlugin.configs.recommended,

	{
		languageOptions: {
			globals: {
				wp: 'readonly', // eslint-disable-line id-length
			},
			parserOptions: {
				requireConfigFile: false,
				babelOptions: {
					presets: [ require.resolve( '@wordpress/babel-preset-default' ) ],
				},
			},
		},

		rules: sharedRules( { textDomain } ),
	},

	/*
	 * Unit test files and their helpers only.
	 */
	...wpPlugin.configs[ 'test-unit' ].map( ( config ) => ( {
		...config,
		files: [ '**/@(test|__tests__)/**/*.js', '**/?(*.)test.js' ],
	} ) ),
];
