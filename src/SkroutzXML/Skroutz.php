<?php
/**
 * Skroutz.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\SkroutzXML;

use Pan\SkroutzXML\Logs\Handlers\DBHandler;
use Pan\SkroutzXML\Logs\Handlers\HtmlFormatter;
use Pan\SkroutzXML\Logs\Logger;
use Pan\XmlGenerator\WooArrayGenerator;
use Pan\XmlGenerator\XML;

/**
 * Class Skroutz
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class Skroutz {
    const DEV = false;
    const VERSION = '151227';

    /**
     * @var Options
     */
    protected $options;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var XML
     */
    protected $xmlObj;

    public function __construct( ) {
        $this->options = Options::getInstance();
    }

    public function generateXml() {
        $sTime = microtime( true );
        ignore_user_abort( true );

        $env = new Env();
        $env->maximize_time_memory_limits();

        // TODO Set options
        $wooGen = new WooArrayGenerator( [ ] );
        $this->logger->clearDBLog();

        $genArray = $wooGen->getArray();

        $this->logger->addInfo(
            '<strong>SkroutzXML XML generation started at ' . date( 'd M, Y H:i:s' ) . '</strong>'
        );

        $this->parseStoreLog($wooGen->getLog());

        $res = $this->getXmlObj()->parseArray(
            $genArray,
            $this->options->getFieldMap(),
            $this->options->getFieldLengths(),
            $this->options->getRequiredFields()
        );

        $this->logger->addInfo(
            '<strong>SkroutzXML XML generation finished at '
            . date( 'd M, Y H:i:s' )
            . '</strong><br>Time taken: ' . round( microtime( true ) - $sTime, 20 ) . ' sec<br>
			Mem details: ' . $env->memory_details() );

        return $res;
    }

    protected function parseStoreLog(array $log){
        // TODO Implement
    }

    public function generateAndPrint() {
        if ( $this->haveToGenerate() ) {
            $this->generateXml();
        }

        $this->getXmlObj()->printXML();
        exit( 0 );
    }

    public function getLogger() {
        if ( ! $this->logger ) {
            $this->logger = new Logger( );

            $dbHandler = new DBHandler( Logger::LOG_NAME, $this->getLogLevel() );
            $dbHandler->setFormatter( new HtmlFormatter() );
            $this->logger->addDBHandler( $dbHandler );
        }

        return $this->logger;
    }

    protected function getLogLevel() {
        if ( self::DEV ) {
            return \Monolog\Logger::DEBUG;
        }

        return \Monolog\Logger::INFO;
    }

    protected function haveToGenerate() {
        $xmlInterval = $this->options->get( 'xml_interval' );

        if ( ! is_numeric( $xmlInterval ) ) {
            $schedules = wp_get_schedules();
            if ( isset( $schedules[ $xmlInterval ] ) ) {
                $xmlInterval = $schedules[ $xmlInterval ]['interval'];
            }
        }

        $xmlInfo     = $this->xmlObj->getFileInfo();
        $createdTime = strtotime( $xmlInfo[ $this->xmlObj->createdAtName ]['value'] );

        $nextCreationTime = (int) $xmlInterval + (int) $createdTime;

        return time() > $nextCreationTime;
    }

    protected function getXmlObj() {
        if ( ! $this->xmlObj ) {
            $this->xmlObj = new XML(
                $this->options->getFileLocationOption(),
                $this->options->getCreatedAtOption(),
                $this->options->getXmlRootElemName(),
                $this->options->getXmlProductsElemWrapper(),
                $this->options->getXmlProductElemName()
            );
        }

        return $this->xmlObj;
    }

    /**
     * @return bool
     * @static * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  TODO ${VERSION}
     */
    public static function hasBrandsPlugin() {
        return ( in_array( 'woocommerce-brands/woocommerce-brands.php',
                (array) get_option( 'active_plugins', array() ) )
                 || self::isPluginActiveForNetwork( 'woocommerce-brands/woocommerce-brands.php' ) )
               && taxonomy_exists( 'product_brand' );
    }

    /**
     * Checks if a plugin is active for the network
     *
     * @param string $plugin plugin basename
     *
     * @return bool
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  TODO ${VERSION}
     */
    private static function isPluginActiveForNetwork( $plugin ) {
        if ( ! is_multisite() ) {
            return false;
        }

        $plugins = get_site_option( 'active_sitewide_plugins' );
        if ( isset( $plugins[ $plugin ] ) ) {
            return true;
        }

        return false;
    }

    /**
     * @return false|null|object
     * @static * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  TODO ${VERSION}
     */
    public static function getBrandsPluginTaxonomy() {
        if ( self::hasBrandsPlugin() ) {
            return get_taxonomy( 'product_brand' );
        }

        return null;
    }
}