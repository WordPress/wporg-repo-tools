<?php
namespace WordPressdotorg\Bin\Create\ChildTheme;

/**
 * Convert a string value into a slug (kebab-case).
 * Forked and trimmed down from sanitize_title_with_dashes.
 *
 * @param string $value
 * @return string
 */
function sanitize_slug( $value ) {
	$value = strip_tags( $value );
	$value = strtolower( $value );
	$value = str_replace( '.', '-', $value );
	$value = preg_replace( '/[^%a-z0-9 _-]/', '', $value );
	$value = preg_replace( '/\s+/', '-', $value );
	$value = preg_replace( '|-+|', '-', $value );
	$value = trim( $value, '-' );

	return $value;
}

return array(
	'title' => array(
		'message' => 'The display title for the theme',
		'default' => 'Child Theme',
		'validate' => false,
	),
	'slug' => array(
		'message' => 'The theme slug used for identification (also the output folder name)',
		'default' => 'wporg-child-theme',
		'validate' => __NAMESPACE__ . '\sanitize_slug',
	),
	'description' => array(
		'message' => 'The short description for the theme',
		'default' => '',
		'validate' => false,
	),
	'textdomain' => array(
		'message' => 'The textdomain used for this theme',
		'default' => 'wporg',
		'validate' => __NAMESPACE__ . '\sanitize_slug',
	),
);
