<?php
/**
 * PHP that violates the shared standard.
 *
 * The text domain comes from the project ruleset, the PHP version target from
 * the standard. Note the latter is only a warning, so the non-zero exit the
 * assertions check rests on the text domain error. Keep an error in this file.
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
