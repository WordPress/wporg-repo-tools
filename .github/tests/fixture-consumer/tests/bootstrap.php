<?php
/**
 * PHPUnit bootstrap that hooks into wp-env's preinstalled WordPress test suite.
 *
 * We do NOT load vendor/autoload.php here: yoast/phpunit-polyfills pulls in
 * phpunit/phpunit as a transitive dep, and registering that alongside the
 * cli container's globally-installed PHPUnit causes class/method mismatches.
 * Instead, point WP_TESTS_PHPUNIT_POLYFILLS_PATH at the polyfills directory
 * and let the WP test bootstrap load only its autoloader.
 */
define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', __DIR__ . '/../vendor/yoast/phpunit-polyfills' );
require_once '/wordpress-phpunit/includes/bootstrap.php';
