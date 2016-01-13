<?php

namespace Pan\SkroutzXML\Logs\Handlers;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * Class DBHandler
 *
 * @package WPluginCore003\Logs\Handlers
 * @author  Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @since   0.0.2
 */
class DBHandler extends AbstractProcessingHandler {
	/**
	 * @var int
	 */
	private $logName;
	/**
	 * @var array
	 */
	private $logs = array();

	/**
	 * @param int       $logName
	 * @param bool|int  $level
	 * @param bool|true $bubble
	 */
	public function __construct( $logName, $level = Logger::DEBUG, $bubble = true ) {
		$this->logName = $logName;

		$log        = get_option( $this->logName );
		$this->logs = is_array( $log ) ? $log : array();

		parent::__construct( $level, $bubble );
	}

	/**
	 * @param array $record
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  0.0.2
	 */
	protected function write( array $record ) {
		/* @var \DateTime $datetime */
		$datetime = $record['datetime'];

		$this->logs[] = array(
			'channel' => $record['channel'],
			'level'   => $record['level'],
			'message' => $record['formatted'],
			'time'    => $datetime->format( 'U' ),
		);
		update_option( $this->logName, $this->logs );
	}
}