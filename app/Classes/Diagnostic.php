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
	 * @var bool
	 */
	protected $shouldUpdate = false;

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

		$this->shouldUpdate = true;

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
			$this->shouldUpdate   = true;
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function get() {
		return $this->log;
	}

	/**
	 *
	 */
	public function __destruct() {
		if ( $this->shouldUpdate ) {
			update_option( $this->logArrayName, $this->log );
		}
	}
}