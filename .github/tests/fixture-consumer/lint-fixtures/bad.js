/**
 * JavaScript that violates the shared configuration.
 *
 * Every rule tripped here is one this package customises rather than one
 * inherited from `@wordpress/eslint-plugin`, so the assertions prove the
 * shared config is the one in effect — not merely that some linter ran.
 *
 * Not linted by the `lint` action; see `.github/tests/assert-lint-fixtures.sh`.
 */

/* `id-length`: we set min 3, with an exception list. */
const ab = 1;

/* `@wordpress/i18n-text-domain`: the allowed domain comes from our config. */
const label = wp.i18n.__( 'Hello', 'not-the-fixture-domain' );

/* `object-shorthand`: we require 'consistent-as-needed'. */
const mixed = { label: label, ab };

module.exports = { mixed };
