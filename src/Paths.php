<?php
/**
 * Project: skroutz-pan
 * File: Paths.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 4/9/2015
 * Time: 12:05 πμ
 * Since: TODO ${VERSION}
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace SkroutzXML;


class Paths extends \PanWPCore\Paths {
	/**
	 * @param $field
	 * @param $value
	 * @param $existing_value
	 *
	 * @return array
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	public function reduxCallBackValidateLocalPath( $field, $value, $existing_value ) {
		$return = array();

		$valueTrimed = ltrim( $value, DIRECTORY_SEPARATOR );
		$path        = realpath( ABSPATH . $valueTrimed );

		if ( is_numeric( strpos( $path, rtrim( ABSPATH, '/' ) ) ) ) {
			$value = $valueTrimed;
		} else {
			$value           = $existing_value;
			$field['msg']    = 'Directory don\'t exists or/and it\'s outside of WordPress installation folder';
			$return['error'] = $field;
		}

		$return['value'] = $value;

		return $return;
	}
}