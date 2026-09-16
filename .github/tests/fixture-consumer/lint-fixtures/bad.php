<?php
/**
 * PHP that violates the shared standard.
 *
 * Both violations come from something this package configures: the text domain
 * is set by the project ruleset, and the PHP version target by the standard.
 *
 * Note that the text domain mismatch is an error while the PHP version finding
 * is only a warning. The non-zero exit the assertions check therefore rests on
 * the error alone: under `-n`, or with `ignore_warnings_on_exit` set, the
 * warning would stop contributing to it. Keep an error in this file.
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
