<?php
/**
 * PHP the shared standard accepts.
 *
 * Short array syntax and short ternaries are excluded from the standard, and
 * `wporg` is the fixture's text domain, so this file passing proves those
 * settings are in effect rather than merely that PHPCS ran.
 */

declare( strict_types = 1 );

/**
 * Build a greeting.
 *
 * @param string $name The name to greet.
 *
 * @return string The greeting.
 */
function wporg_fixture_greeting( string $name ): string {
	$parts = [ esc_html__( 'Hello', 'wporg' ), $name ?: 'world' ];

	return esc_html( implode( ' ', $parts ) );
}
