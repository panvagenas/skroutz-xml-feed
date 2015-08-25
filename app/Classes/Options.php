<?php
/**
 * Created by PhpStorm.
 * User: vagenas
 * Date: 24/8/2015
 * Time: 1:51 μμ
 */

namespace SkroutzXMLFeed\Classes;
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Options {
	protected $optionsName = '';
	public static $availOptions = array(
		'Άμεση παραλαβή (από το κατάστημα) ή Παράδοση σε 1-3 ημέρες',
		'Παράδοση σε 1-3 ημέρες',
		'Παραλαβή (από το κατάστημα) ή Παράδοση σε 1-3 ημέρες',
		'Παραλαβή (από το κατάστημα) ή Παράδοση σε 4-10 ημέρες',
		'Παράδοση σε 4-10 ημέρες',
		'Κατόπιν παραγγελίας, παραλαβή ή παράδοση έως 30 ημέρες',
	);
	/**
	 * @var \ReduxFramework
	 */
	protected $reduxInstance = null;

	/**
	 *
	 */
	public function __construct() {
		$this->optionsName   = Helper::getOptionsName();
		$this->reduxInstance = get_redux_instance( $this->optionsName );
	}

	/**
	 * @param $optionName
	 * @param null $default
	 *
	 * @return mixed
	 */
	public function get( $optionName, $default = null ) {
		$options = $this->getAll();
		return ( isset ( $options[ $optionName ] ) )
			? $options[ $optionName ]
			: $this->reduxInstance->_get_default( $optionName, $default );
	}

	/**
	 * @param $optionName
	 * @param $value
	 */
	public function set( $optionName, $value ) {
		$this->reduxInstance->set( $optionName, $value );
	}

	/**
	 * @return array
	 */
	public function getAll() {
		return $this->reduxInstance->options;
	}
}