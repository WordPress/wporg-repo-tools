/**
 * The ESLint rules shared by every WordPress.org Meta project.
 *
 * This is the single source of truth for the rule set. Both the flat config
 * (`eslint.js`, for ESLint 9+ / `@wordpress/scripts` 32+) and the legacy
 * config (`eslintrc.js`, for ESLint 8) build on it, so a rule only ever has to
 * be changed in one place.
 *
 * Rules that differ between the two formats — `ignores`/`ignorePatterns`,
 * `globals`, the test-file overrides — live in those files instead, because
 * there is no useful way to express them once.
 */

/**
 * Internal dependencies
 */
const prettierConfig = require( './prettier' );

/**
 * Build the shared rules.
 *
 * @param {Object} options            Options.
 * @param {string} options.textDomain The project's text domain, used by
 *                                    `@wordpress/i18n-text-domain`.
 *
 * @return {Object} An ESLint `rules` object.
 */
module.exports = ( { textDomain } ) => ( {
	/*
	 * WordPress packages are script dependencies provided at runtime, not installed via npm.
	 */
	'import/no-unresolved': [ 'error', { ignore: [ '^@wordpress/' ] } ],

	/*
	 * Set up our text domain.
	 */
	'@wordpress/i18n-text-domain': [ 'error', { allowedTextDomain: [ textDomain ] } ],

	/*
	 * The rationale behind this rule is that sometimes a variable is defined by a costly operation, but then
	 * the variable is never used, so that operation was wasted. That's a valid point, but in practice that
	 * doesn't happen very often, so the benefit is not significant.
	 *
	 * The benefits of grouping variable assignments at the start of a function outweigh the costs, since it
	 * almost always makes the function easier to quickly grok.
	 *
	 * In the uncommon case where a significant performance penalty would be introduced, the developer is
	 * still free to choose to define the variable after the early returns.
	 */
	'@wordpress/no-unused-vars-before-return': [ 'off' ],

	/*
	 * Turned off wholesale rather than safelisting the snake_case parameters that come in from the
	 * REST API. The `allow` option would let us safelist them instead — a worthwhile improvement:
	 *
	 * 'camelcase' : [
	 *     'error',
	 *     {
	 *         allow: [ // These are variables defined in PHP and exposed via the REST API.
	 *             // Speakers block
	 *  		   'post_ids', 'term_ids', 'grid_columns',
	 *  		   'show_avatars', 'avatar_size', 'avatar_align',
	 *  		   'speaker_link', 'show_session',
	 *         ],
	 *     },
	 * ],
	 */
	camelcase: 'off',

	/*
	 * Short variable names are almost always obscure and non-descriptive, but they should be meaningful,
	 * obvious, and self-documenting.
	 */
	'id-length': [
		'error',
		{
			min: 3,
			exceptions: [ '__', '_n', '_x', 'id', 'a', 'b', 'i' ],
		},
	],

	/*
	 * Force a line-length of 115 characters.
	 *
	 * We ignore URLs, trailing comments, strings, and template literals to prevent awkward fragmenting of
	 * meaningful content.
	 */
	'max-len': [
		'error',
		{
			code: 115,
			ignoreUrls: true,
			ignoreTrailingComments: true,
			ignoreStrings: true,
			ignoreTemplateLiterals: true,
		},
	],

	/*
	 * Objects are harder to quickly scan when the formatting is inconsistent.
	 */
	'object-shorthand': [ 'error', 'consistent-as-needed' ],

	/*
	 * Only prefer const over let when destructuring if all variables in the declaration are never reassigned.
	 *
	 * With the default setting of this rule, to prefer const when any of the destructured variables are never
	 * reassigned, we end up with situations where we have to destructure the same entity twice, which seems
	 * inefficient. E.g. if in the below example 'a' gets reassigned but 'b' doesn't:
	 *
	 * let { a, b } = var;
	 *
	 * Seems better than having to do:
	 *
	 * let { a } = var;
	 * const { b } = var;
	 */
	'prefer-const': [
		'error',
		{
			destructuring: 'all',
		},
	],

	/*
	 * Disallow creating more than one component per file.
	 *
	 * Having one component per file makes components easier to test and easier to document. `ignoreStateless`
	 * allows us to still keep simple stateless components, but these should be used sparingly.
	 */
	'react/no-multi-comp': [
		'error',
		{
			ignoreStateless: true,
		},
	],

	/*
	 * Sort imports alphabetically, at least inside multiple-member imports. Ignores declaration sorting since
	 * this interfers with the External/WordPress/Internal groupings. For example, it will flag the following
	 * as incorrect:
	 *
	 *   import { c, a, b } from 'foo';
	 *
	 * Running `eslint --fix` will update this to
	 *
	 *   import { a, b, c } from 'foo';
	 *
	 */
	'sort-imports': [
		'error',
		{
			ignoreDeclarationSort: true,
		},
	],

	/*
	 * Descriptions are often obvious from the variable and function names, so always requiring them would be
	 * inconvenient. The developer should add one whenever it's not obvious, though.
	 *
	 * @todo `@param` tags should align the variable name and description, just like in PHP.
	 */
	'jsdoc/require-returns-description': 'off',

	/*
	 * Import our local prettier config to be used by the prettier rule.
	 *
	 * `wp-scripts lint-js` does this automatically, but local eslint (ex, in code editors) does not know the
	 * connection, and will default back to vanilla prettier configuration.
	 */
	'prettier/prettier': [ 'error', prettierConfig ],
} );
