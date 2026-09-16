/**
 * Legacy (eslintrc) ESLint config for WordPress.org Meta projects.
 *
 * For ESLint 8 / `@wordpress/scripts` below 32. Projects on newer tooling want
 * `./eslint.js`, since ESLint 9 ignores this format entirely.
 *
 * Usage, from a project's `.eslintrc.js`:
 *
 *     const createConfig = require( './vendor/wporg/wporg-repo-tools/configs/eslintrc' );
 *
 *     module.exports = createConfig( { textDomain: 'wporg' } );
 */

/**
 * Internal dependencies
 */
const sharedRules = require( './rules' );

/**
 * Build the legacy config.
 *
 * @param {Object} options                  Options.
 * @param {string} options.textDomain       The project's text domain.
 * @param {Object} [options.prettierConfig] The project's Prettier settings, for
 *                                          the `prettier/prettier` rule.
 *
 * @return {Object} An eslintrc config object.
 */
module.exports = ( { textDomain, prettierConfig } ) => ( {
	extends: 'plugin:@wordpress/eslint-plugin/recommended',

	root: true,

	parserOptions: {
		requireConfigFile: false,
		babelOptions: {
			presets: [ require.resolve( '@wordpress/babel-preset-default' ) ],
		},
	},

	globals: {
		wp: true, // eslint-disable-line id-length
	},

	ignorePatterns: [ '*.min.js' ],

	rules: sharedRules( { textDomain, prettierConfig } ),

	overrides: [
		{
			// Unit test files and their helpers only.
			files: [ '**/@(test|__tests__)/**/*.js', '**/?(*.)test.js' ],
			extends: [ 'plugin:@wordpress/eslint-plugin/test-unit' ],
		},
	],
} );
