<?php

namespace WordPress_org\Repo_Tools\Global_Header;

defined( 'WPINC' ) || die();

add_action( 'init', __NAMESPACE__ . '\register_assets', 9 );

function register_assets() {
	register_block_type(
		'wordpress-org/global-header',
		array( 'render_callback' => __NAMESPACE__ . '\render_global_header' )
	);
}

function render_global_header( $attributes ) {
	ob_start();
	require_once __DIR__ . '/header.php';
	return ob_get_clean();
}

