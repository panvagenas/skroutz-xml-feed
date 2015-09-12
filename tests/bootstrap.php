<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

if ( ! defined( 'AUTH_KEY' ) ) {
	define( 'AUTH_KEY', '(C4XMfGbE@,AG~C[ Zt%41EzI?</L]QEU~<$fIa|#`N~yYUD^[(&wcwe,DX_K27F' );
}
if ( ! defined( 'SECURE_AUTH_KEY' ) ) {
	define( 'SECURE_AUTH_KEY', '=Mcv1;Gxb-WY5xg;wHE|s^lg%1eUuu+_qExGP{oM@y4caWh~z4&c@6LK@Yb~K_iP' );
}

function _manually_load_plugin() {
	require_once dirname( dirname( __FILE__ ) ) . '/vendor/autoload.php';
	global $skroutzPlugin;

	$skroutzPlugin = new \SkroutzXML\Plugin(
		'SkroutzXML',
		dirname( dirname( __FILE__ ) ) . '/skroutz-xml-feed.php',
		'Skroutz.gr XML Feed',
		'150903',
		'skroutz-xml-feed',
		'skroutz_xml_feed' );
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
