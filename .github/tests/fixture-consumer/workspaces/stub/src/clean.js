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

module.exports = { addPrefix };
