/**
 * JavaScript that satisfies the shared configuration.
 *
 * Linted by the `lint` composite action on every run, so that an over-strict
 * rule change fails CI here rather than in a consuming project.
 */

/**
 * Add a prefix to a label.
 *
 * @param {string} label  The label to prefix.
 * @param {string} prefix The prefix to apply.
 *
 * @return {string} The prefixed label.
 */
function addPrefix( label, prefix ) {
	return `${ prefix }${ label }`;
}

/*
 * Comfortably past the 80 columns most defaults assume, and within the 115 this
 * package sets. Note that Prettier's own width is not what distinguishes the two
 * configs here: Prettier resolves `.prettierrc.js` by walking up from the linted
 * file, so it applies whichever ESLint config is loaded. What the `--config`
 * flag in this workspace's lint script buys is the ESLint rules themselves.
 */
const defaultPrefixedLabel = addPrefix( 'a label of some length', 'a prefix: ' );

module.exports = { addPrefix, defaultPrefixedLabel };
