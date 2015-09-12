<?php
/**
 * Project: skroutz-pan
 * File: Initializer.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 3/9/2015
 * Time: 11:41 μμ
 * Since: TODO ${VERSION}
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace SkroutzXML;

use SkroutzXML\Log\Logger;

/**
 * Class Initializer
 *
 * @package SkroutzXML
 * @author  Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @since   TODO ${VERSION}
 *
 * @property Env    $Env
 * @property Logger $Log__Logger
 */
class Initializer extends \PanWPCore\Initializer {
	/**
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	protected function init() {
		add_action( 'init', array( $this, 'checkRequest' ) );
		$this->Scripts->enqueueAdminStyle( $this->Scripts->getScriptHandle( 'bootstrap' ),
			'/' . $this->Paths->pluginDirRel( '/assets/css/bootstrap.css' ) );
		$this->Scripts->enqueueAdminScript( $this->Scripts->getScriptHandle( 'skz_gen_now' ),
			'/' . $this->Paths->pluginDirRel( '/assets/js/genarate-now.min.js' ) );

		add_action( 'wp_ajax_skz_generate_now', array( $this->Ajax, 'generateNow' ) );
	}

	/**
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public function checkRequest() {
		$generateVar = $this->Options->get( 'xml_generate_var' );
		if ( empty( $generateVar ) ) {
			$generateVar = 'skroutz';
			$this->Options->set( 'xml_generate_var', $generateVar );
		}

		if ( isset( $_GET[ $generateVar ] ) && $_GET[ $generateVar ] == $generateVarVal ) {
			add_action( 'wp_loaded', array( $this->Skroutz, 'generate_and_print' ), PHP_INT_MAX );
		}
	}
}