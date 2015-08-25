<?php
/**
 * Created by PhpStorm.
 * User: vagenas
 * Date: 25/8/2015
 * Time: 4:15 μμ
 */

namespace SkroutzXMLFeed\Classes;


class Diagnostic extends Singleton{
	/**
	 * @var array
	 */
	protected $log = array();

	/**
	 *
	 */
	protected function __construct(){
		$options = new Options();
		$this->log = $options->get('log');
	}

	public function clear(){
		$this->log = array();
		return $this;
	}
	public function add($msg){
		$this->log[] = $msg;
		return $this;
	}
	public function get(){
		return $this->log;
	}

}