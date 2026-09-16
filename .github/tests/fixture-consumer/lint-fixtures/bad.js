/**
 * JavaScript that violates the shared config.
 *
 * Each rule below is one this package customises, not one inherited from
 * `@wordpress/eslint-plugin`, so the assertions prove our config is in effect.
 */

/* `id-length`: we set min 3. */
const ab = 1;

/* `@wordpress/i18n-text-domain`: the allowed domain comes from our config. */
const label = wp.i18n.__( 'Hello', 'not-the-fixture-domain' );

/* `object-shorthand`: we require 'consistent-as-needed'. */
const mixed = { label: label, ab };

module.exports = { mixed };
