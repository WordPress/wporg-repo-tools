/**
 * JavaScript the shared config accepts.
 *
 * Linted on every run, so an over-strict rule change fails here rather than in
 * a consuming project.
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

/* Past the 80 columns most defaults assume, within the 115 this package sets. */
const defaultPrefixedLabel = addPrefix( 'a label of some length', 'a prefix: ' );

module.exports = { addPrefix, defaultPrefixedLabel };
