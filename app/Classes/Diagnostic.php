<?php
/**
 * Created by PhpStorm.
 * User: vagenas
 * Date: 25/8/2015
 * Time: 4:15 μμ
 */

namespace SkroutzXMLFeed\Classes;


class Diagnostic extends Singleton {
	/**
	 * @var array
	 */
	protected $log = array(
		'info'    => array(),
		'warning' => array(),
		'error'   => array(),
	);
	/**
	 * @var string
	 */
	protected $logArrayName = 'log';

	/**
	 *
	 */
	protected function __construct() {
		$this->logArrayName = Helper::getOptionsName() . '_xml_gen_log';
		$this->log          = (array) get_option( $this->logArrayName );
	}

	/**
	 * @return $this
	 */
	public function clear() {
		$this->log          = array(
			'info'    => array(),
			'warning' => array(),
			'error'   => array(),
		);

		return $this;
	}

	/**
	 * @param $msg
	 *
	 * @return $this
	 */
	public function add( $msg, $type = 'info' ) {
		if ( is_string( $msg ) ) {
			$this->log[ $type ][] = $msg;
			update_option( $this->logArrayName, $this->log );
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function get() {
		return $this->log;
	}
}