<?php namespace SkroutzXMLFeed;

/** @var \Herbert\Framework\Router $router */
if ( ! defined( 'WPINC' ) ) {
	die;
}

$router->get([
	'as'   => 'skroutzIndexGen',
	'uri'  => '/skroutz',
	'uses' => __NAMESPACE__ . '\Controllers\SkroutzController@generate'
]);