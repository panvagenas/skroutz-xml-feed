<?php
/**
 * Skroutz.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     170126
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\SkroutzXML;

if ( ! defined( 'WPINC' ) ) {
    die;
}

use Pan\XmlGenerator\Logger\Handlers\DBHandler;
use Pan\XmlGenerator\Logger\Handlers\HtmlFormatter;
use Pan\XmlGenerator\Logger\Logger;
use Pan\XmlGenerator\WooArrayGenerator;
use Pan\XmlGenerator\XML;

/**
 * Class Skroutz
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     170126
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class Skroutz {
    const DEV = false;

    const DB_LOG_NAME = 'skz_gen_log';

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

    public function __construct() {
        $this->options = Options::getInstance();

        $this->logger = Logger::getInstance( self::DB_LOG_NAME );

        $dbHandler = new DBHandler( self::DB_LOG_NAME, $this->getLogLevel() );

        $dbHandler->setFormatter( new HtmlFormatter() );
        $this->logger->addDBHandler( $dbHandler );

        $this->xmlObj = new XML(
            $this->options->getFileLocationOption(),
            $this->options->getCreatedAtOption(),
            $this->options->getXmlRootElemName(),
            $this->options->getXmlProductsElemWrapper(),
            $this->options->getXmlProductElemName()
        );
    }

    public function generateXml() {
        $sTime = microtime( true ) - 1;
        ignore_user_abort( true );

        $env = new Env();
        $env->maximize_time_memory_limits();

        $this->logger->clearDBLog();

        $this->logger->addInfo(
            '<strong>SkroutzXML XML generation started at ' . date( 'd M, Y H:i:s' ) . '</strong>'
        );

        $wooGen = new WooArrayGenerator(
            $this->options->translateOptions(),
            [ $this, 'validateProductArray' ],
            self::DB_LOG_NAME
        );

        $genArray = $wooGen->getArray();

        $res = $this->getXmlObj()->parseArray( $genArray, $this->options->getFieldMap() );

        $this->logger->addInfo(
            '<strong>Skroutz XML generation finished at '
            . date( 'd M, Y H:i:s' )
            . '</strong><br>Time taken: ' . $this->timeAbbreviation( floor( microtime( true ) - $sTime ) ) . '<br>
			Mem details: ' . $env->memory_details() );

        $res['logMarkUp']  = DBHandler::getLogMarkUp( Skroutz::DB_LOG_NAME );
        $res['infoMarkUp'] = Initializer::getFileInfoMarkUp();

        return $res;
    }

    protected function timeAbbreviation( $seconds ) {
        $dtF = new \DateTime( "@0" );
        $dtT = new \DateTime( "@$seconds" );
        $dif = $dtF->diff( $dtT );

        $format = '';
        if ( $dif->d ) {
            $format .= ( '%a days, ' );
        }
        if ( $dif->d || $dif->h ) {
            $format .= ( '%h hours, ' );
        }
        if ( $dif->d || $dif->h || $dif->i ) {
            $format .= ( '%i minutes ' );
        }
        if ( $dif->d || $dif->h || $dif->i || $dif->s ) {
            $format .= ( $format ? 'and ' : '' ) . ( '%s seconds' );
        }

        return $dif->format( $format );
    }

    public function validateProductArray( array $array ) {
        $failed = [];
        foreach ( $this->options->getRequiredFields() as $fieldName ) {
            if ( ! isset( $array[ $fieldName ] ) || empty( $array[ $fieldName ] ) ) {
                $failed[] = $fieldName;
            } else {
                $array[ $fieldName ] = $this->trimField( $array[ $fieldName ], $fieldName );
                if ( is_string( $array[ $fieldName ] ) ) {
                    $array[ $fieldName ] = mb_convert_encoding( $array[ $fieldName ], "UTF-8" );
                }
            }
        }

        if ( $failed ) {
            $this->logger->addError(
                'Product <strong>' . $array['name'] . '</strong> not included in XML file because field(s) '
                . implode( ', ', $failed ) . ' is/are missing or is invalid',
                $array
            );

            return [];
        }

        foreach ( $array as $k => $v ) {
            if ( ! array_key_exists( $k, $this->options->getFieldMap() ) ) {
                unset( $array[ $k ] );
            }
        }

        $productAvailability = $array['availability'];

        if ( $productAvailability === WooArrayGenerator::AVAIL_IN_STOCK ) {
            if ( isset( Options::$availOptions[ $this->options->get( 'avail_inStock' ) ] ) ) {
                $array['availability'] = Options::$availOptions[ $this->options->get( 'avail_inStock' ) ];
            } else {
                return [];
            }
        } elseif ( $productAvailability === WooArrayGenerator::AVAIL_OUT_OF_STOCK ) {
            if ( isset( Options::$availOptions[ $this->options->get( 'avail_outOfStock' ) ] ) ) {
                $array['availability'] = Options::$availOptions[ $this->options->get( 'avail_outOfStock' ) ];
            } else {
                return [];
            }
        } elseif ( $productAvailability === WooArrayGenerator::AVAIL_OUT_OF_STOCK_BACKORDERS ) {
            if ( isset( Options::$availOptions[ $this->options->get( 'avail_backorders' ) ] ) ) {
                $array['availability'] = Options::$availOptions[ $this->options->get( 'avail_backorders' ) ];
            } else {
                return [];
            }
        } else {
            $this->logger->addError(
                'Product <strong>' . $array['name']
                . '</strong> not included in XML file due to an incorrect availability',
                $array
            );

            return [];
        }

        return $array;
    }

    protected function trimField( $value, $fieldName ) {
        $fieldLengths = $this->options->getFieldLengths();

        if ( ! isset( $fieldLengths[ $fieldName ] ) ) {
            return false;
        }

        if ( $fieldLengths[ $fieldName ] === 0 ) {
            return $value;
        }

        return mb_substr( (string) $value, 0, $fieldLengths[ $fieldName ] );
    }

    public function generateAndPrint() {
        if ( $this->haveToGenerate() ) {
            $this->generateXml();
        }

        $this->getXmlObj()->printXML();
        exit( 0 );
    }

    public function getLogger() {
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
            } else {
                $xmlInterval = 0;
            }
        }

        $xmlInfo     = $this->xmlObj->getFileInfo();
        $createdTime = isset( $xmlInfo[ $this->xmlObj->createdAtName ] ) ?
            strtotime( $xmlInfo[ $this->xmlObj->createdAtName ]['value'] )
            : 0;

        $nextCreationTime = ( (int) $xmlInterval * 60 * 60 ) + (int) $createdTime;

        return time() > $nextCreationTime;
    }

    public function getXmlObj() {
        return $this->xmlObj;
    }

    /**
     * @return bool
     * @static * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  170126
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
     * @since  170126
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
     * @since  170126
     */
    public static function getBrandsPluginTaxonomy() {
        if ( self::hasBrandsPlugin() ) {
            return get_taxonomy( 'product_brand' );
        }

        return null;
    }
}