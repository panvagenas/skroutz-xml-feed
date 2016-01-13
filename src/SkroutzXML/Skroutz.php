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

    /**
     * @var Options
     */
    protected $options;
    /**
     * @var Logger
     */
    protected $logger;
    protected $logName = 'skz_gen_log';
    /**
     * @var XML
     */
    protected $xmlObj;

    public function __construct() {
        $this->init();
    }

    public function init() {
        $this->options = Options::getInstance();

        add_action( 'init', [ $this, 'checkRequest' ] );
    }

    public function checkRequest() {
        $generateVar    = $this->options->get( 'xml_generate_var' );
        $generateVarVal = $this->options->get( 'xml_generate_var_value' );

        parse_str( $_SERVER["REQUEST_URI"] );

        if ( isset( $$generateVar ) && $$generateVar === $generateVarVal ) {
            add_action( 'wp_loaded', [ $this, 'generateAndPrint' ], PHP_INT_MAX );
        }
    }

    public function generateXml() {
        // TODO Set options
        $wooGen = new WooArrayGenerator( [ ] );

        $genArray = $wooGen->getArray();

        return $this->getXmlObj()->parseArray(
            $genArray,
            $this->options->getFieldMap(),
            $this->options->getFieldLengths(),
            $this->options->getRequiredFields()
        );
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
            $this->logger = new Logger( $this->logName );

            $dbHandler = new DBHandler( $this->logName, $this->getLogLevel() );
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