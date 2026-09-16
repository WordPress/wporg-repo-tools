<?php
/**
 * PHP that violates the shared standard.
 *
 * Both violations come from something this package configures: the text domain
 * is set by the project ruleset, and the PHP version target by the standard.
 *
 * Not linted by the `lint` action; see `.github/tests/assert-lint-fixtures.sh`.
 */

declare( strict_types = 1 );

/**
 * Render a label.
 *
 * @param string $suffix A suffix.
 *
 * @return string The label.
 */
function wporg_fixture_bad_label( string $suffix = null ): string {
	return esc_html__( 'Label', 'not-the-fixture-domain' ) . $suffix;
}
