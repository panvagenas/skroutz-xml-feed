<?php
/**
 * Logger.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     170126
 * @package   Pan\XmlGenerator\Logger
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\XmlGenerator\Logger;

if ( ! defined( 'WPINC' ) ) {
    die;
}

use Monolog\Handler\StreamHandler;
use Pan\XmlGenerator\Logger\Handlers\DBHandler;

/**
 * Class Logger
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     170126
 * @package   Pan\XmlGenerator\Logger
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class Logger {
    private static $instances = array();

    protected $logName = '';

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $logFilePath;

    protected function __construct($logName) {
        $this->logger = new \Monolog\Logger( $logName );
        $this->logName = $logName;
    }

    /**
     * @param $logName
     *
     * @return $this
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  170126
     */
    public static function getInstance($logName) {
        if ( ! isset( self::$instances[ $logName ] ) ) {
            self::$instances[ $logName ] = new static($logName);
        }

        return self::$instances[ $logName ];
    }

    public function clearDbLog() {
        foreach ( $this->logger->getHandlers() as $handler ) {
            if ( $handler instanceof DBHandler ) {
                $handler->clear();
            }
        }
    }

    /**
     * @param resource|string $stream
     * @param integer         $level          The minimum logging level at which this handler will be triggered
     * @param Boolean         $bubble         Whether the messages that are handled can bubble up the stack or not
     * @param int|null        $filePermission Optional file permissions (default (0644) are only for owner read/write)
     * @param Boolean         $useLocking     Try to lock log file before doing any writes
     */
    public function addFileHandler(
        $stream,
        $level = \Monolog\Logger::DEBUG,
        $bubble = true,
        $filePermission = null,
        $useLocking = false
    ) {
        if ( empty( $stream ) ) {
            $stream = $this->logFilePath;
        }
        $this->logger->pushHandler( new StreamHandler( $stream, $level, $bubble, $filePermission, $useLocking ) );
    }

    /**
     * @param resource $stream
     * @param integer  $level  The minimum logging level at which this handler will be triggered
     * @param Boolean  $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function addDBHandler( $stream = null, $level = \Monolog\Logger::DEBUG, $bubble = true ) {
        if ( empty( $stream ) ) {
            $stream = new DBHandler( $this->logger->getName(), $level, $bubble );
        }
        $this->logger->pushHandler( $stream );
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * @param  string $message The log message
     * @param  array  $context The log context
     *
     * @return Boolean Whether the record has been processed
     */
    public function addDebug( $message, array $context = array() ) {
        return $this->addRecord( \Monolog\Logger::DEBUG, $message, $context );
    }

    /**
     * Adds a log record at the INFO level.
     *
     * @param  string $message The log message
     * @param  array  $context The log context
     *
     * @return Boolean Whether the record has been processed
     */
    public function addInfo( $message, array $context = array() ) {
        return $this->addRecord( \Monolog\Logger::INFO, $message, $context );
    }

    /**
     * Adds a log record at the NOTICE level.
     *
     * @param  string $message The log message
     * @param  array  $context The log context
     *
     * @return Boolean Whether the record has been processed
     */
    public function addNotice( $message, array $context = array() ) {
        return $this->addRecord( \Monolog\Logger::NOTICE, $message, $context );
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * @param  string $message The log message
     * @param  array  $context The log context
     *
     * @return Boolean Whether the record has been processed
     */
    public function addWarning( $message, array $context = array() ) {
        return $this->addRecord( \Monolog\Logger::WARNING, $message, $context );
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * @param  string $message The log message
     * @param  array  $context The log context
     *
     * @return Boolean Whether the record has been processed
     */
    public function addError( $message, array $context = array() ) {
        return $this->addRecord( \Monolog\Logger::ERROR, $message, $context );
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * @param  string $message The log message
     * @param  array  $context The log context
     *
     * @return Boolean Whether the record has been processed
     */
    public function addCritical( $message, array $context = array() ) {
        return $this->addRecord( \Monolog\Logger::CRITICAL, $message, $context );
    }

    /**
     * Adds a log record at the ALERT level.
     *
     * @param  string $message The log message
     * @param  array  $context The log context
     *
     * @return Boolean Whether the record has been processed
     */
    public function addAlert( $message, array $context = array() ) {
        return $this->addRecord( \Monolog\Logger::ALERT, $message, $context );
    }

    /**
     * Adds a log record at the EMERGENCY level.
     *
     * @param  string $message The log message
     * @param  array  $context The log context
     *
     * @return Boolean Whether the record has been processed
     */
    public function addEmergency( $message, array $context = array() ) {
        return $this->addRecord( \Monolog\Logger::EMERGENCY, $message, $context );
    }

    /**
     * Adds a log record.
     *
     * @param  integer $level   The logging level
     * @param  string  $message The log message
     * @param  array   $context The log context
     *
     * @return Boolean Whether the record has been processed
     */
    public function addRecord( $level, $message, array $context = array() ) {
        return $this->logger->addRecord( $level, $message, $context );
    }
}