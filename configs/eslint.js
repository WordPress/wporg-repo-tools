/**
 * Flat ESLint config for WordPress.org Meta projects, for ESLint 9+.
 *
 * Entries spread from `@wordpress/eslint-plugin` are that plugin's own objects,
 * so append to the returned array rather than editing it.
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
 * @param {Object} options                  Options.
 * @param {string} options.textDomain       The project's text domain.
 * @param {Object} [options.prettierConfig] The project's Prettier settings.
 *
 * @return {Array} A flat ESLint config array.
 */
module.exports = ( { textDomain, prettierConfig } ) => [
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

		rules: sharedRules( { textDomain, prettierConfig } ),
	},

	/*
	 * Unit test files and their helpers only.
	 */
	...wpPlugin.configs[ 'test-unit' ].map( ( config ) => ( {
		...config,
		files: [ '**/@(test|__tests__)/**/*.js', '**/?(*.)test.js' ],
	} ) ),
];
