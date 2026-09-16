/**
 * Stylelint config for WordPress.org Meta projects.
 *
 * Usage, from a project's `.stylelintrc`, which `bin/update-configs` writes as
 * JSON rather than JavaScript:
 *
 *     { "extends": "./vendor/wporg/wporg-repo-tools/configs/stylelint.js" }
 */

module.exports = {
	extends: '@wordpress/stylelint-config/scss-stylistic',
	rules: {
		'@stylistic/max-line-length': 115,
		'no-descending-specificity': null,
		'rule-empty-line-before': [
			'always-multi-line',
			{
				except: [ 'first-nested', 'after-single-line-comment' ],
			},
		],
		'selector-class-pattern': null,
		'value-keyword-case': [ 'lower', { camelCaseSvgKeywords: true } ],
	},
};
