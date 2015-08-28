<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

$pluginFile = __DIR__ . DIRECTORY_SEPARATOR . 'plugin.php';

$pluginData = get_plugin_data( $pluginFile );

$baseName = plugin_basename( $pluginFile );

$stringyBaseName = \Stringy\Stringy::create( preg_replace( '/[^\da-zA-Z]/i', '_', $baseName ) );

$customOptions = [
	'slug'     => (string) $stringyBaseName->underscored()->toLowerCase(),
	'baseName' => $baseName
];

$herbertOptions = [
	'constraint'   => '~0.9.9',
	'requires'     => [
		__DIR__ . '/includes/redux/admin-init.php',
		__DIR__ . '/app/redux-options-init.php',
		__DIR__ . '/includes/SimpleXMLExtended.php',
	],
	'tables'       => [ ],
	'activators'   => [
		__DIR__ . '/app/activate.php'
	],
	'deactivators' => [
		__DIR__ . '/app/deactivate.php'
	],
	'shortcodes'   => [ ],
	'widgets'      => [ ],
	'enqueue'      => [ ],
	'routes'       => [
		'SkroutzXMLFeed' => __DIR__ . '/app/routes.php'
	],
	'panels'       => [ ],
	'apis'         => [
		'SkroutzXMLFeed' => __DIR__ . '/app/api.php'
	],
	'views'        => [ ],
	'viewGlobals'  => [ ],
	'assets'       => '/resources/assets/'
];

return array_merge( $pluginData, $herbertOptions, $customOptions );