<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib/includes';
}

require_once $_tests_dir . '/functions.php';

function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/metamaterial.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/bootstrap.php';
