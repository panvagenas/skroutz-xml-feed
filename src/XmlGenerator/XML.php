<?php
/**
 * XML.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     170126
 * @package   Pan\XmlGenerator
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\XmlGenerator;

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Class XML
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     170126
 * @package   Pan\XmlGenerator
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class XML {
    /**
     * @var SimpleXMLExtended
     */
    public $simpleXML = null;
    /**
     * Absolute file path
     *
     * @var string
     */
    public $fileLocation = '';
    /**
     * @var null
     */
    public $createdAt = null;
    /**
     * @var string
     */
    public $createdAtName = 'created_at';
    /**
     * @var string
     */
    protected $rootElemName = 'myWebStore';
    /**
     * @var string
     */
    protected $productsElemWrapper = 'products';
    /**
     * @var string
     */
    protected $productElemName = 'product';

    protected $fieldLengths = [];
    protected $requiredFields = [];
    protected $fieldMap = [];

    public function __construct(
        $fileLocation,
        $createdAtName,
        $rootElemName,
        $productsElemWrapper,
        $productElemName
    ) {
        $this->fileLocation        = $fileLocation;
        $this->createdAtName       = $createdAtName;
        $this->rootElemName        = $rootElemName;
        $this->productsElemWrapper = $productsElemWrapper;
        $this->productElemName     = $productElemName;
    }

    public function parseArray( array $array, array $fieldMap ) {
        $this->fieldMap = $fieldMap;

        // init simple xml if is not initialized already
        if ( ! $this->simpleXML ) {
            $this->initSimpleXML();
        }

        // parse array
        $totalSuccess = 0;
        foreach ( $array as $k => $v ) {
            $totalSuccess += $this->appendProduct( $v );
        }

        $save = false;
        if ( ! empty( $array ) ) {
            $save = $this->saveXML();
        }

        return [ 'save' => $save, 'totalSuccess' => $totalSuccess ];
    }

    protected function initSimpleXML() {
        $this->simpleXML = new SimpleXMLExtended( '<?xml version="1.0" encoding="UTF-8"?><'
                                                  . $this->rootElemName . '></'
                                                  . $this->rootElemName . '>' );
        $this->simpleXML->addChild( $this->productsElemWrapper );

        return $this;
    }

    public function appendProduct( Array $p ) {
        if ( ! $this->simpleXML ) {
            $this->initSimpleXML();
        }

        $products = $this->simpleXML->children();

        if ( ! empty( $p ) ) {
            $product = $products->addChild( $this->productElemName );

            foreach ( $p as $key => $value ) {
                if ( ! isset( $this->fieldMap[ $key ] ) ) {
                    continue;
                }

                if ( $this->isValidXmlName( $value ) ) {
                    $product->addChild( $this->fieldMap[ $key ], $value );
                } else {
                    $product->{$this->fieldMap[ $key ]} = null;
                    $product->{$this->fieldMap[ $key ]}->addCData( $value );
                }
            }

            return 1;
        }

        return 0;
    }

    protected function isValidXmlName( $name ) {
        $getIniDisplayErrors = ini_get( 'display_errors' );
        ini_set( 'display_errors', 0 );
        try {
            new \DOMElement( $name );
            ini_set( 'display_errors', $getIniDisplayErrors );

            return true;
        } catch( \DOMException $e ) {
            ini_set( 'display_errors', $getIniDisplayErrors );

            return false;
        }
    }

    public function saveXML() {
        if ( ! ( $this->simpleXML instanceof SimpleXMLExtended ) ) {
            return false;
        }
        $dir = dirname( $this->fileLocation );
        if ( ! file_exists( $dir ) ) {
            mkdir( $dir, 0755, true );
        }

        if ( $this->simpleXML && ! empty( $this->fileLocation )
             && ( is_writable( $this->fileLocation ) || is_writable( $dir ) )
        ) {
            if ( is_file( $this->fileLocation ) ) {
                unlink( $this->fileLocation );
            }
            $this->simpleXML->addChild( $this->createdAtName, date( 'Y-m-d H:i' ) );

            return $this->simpleXML->asXML( $this->fileLocation );
        }

        return false;
    }

    public function printXML() {
        if ( headers_sent() ) {
            return;
        }

        if ( ! ( $this->simpleXML instanceof SimpleXMLExtended ) ) {
            $fileLocation = $this->fileLocation;
            if ( ! $this->existsAndReadable( $fileLocation ) ) {
                return;
            }
            $this->simpleXML = simplexml_load_file( $fileLocation );
        }

        header( "Content-Type:text/xml" );

        echo $this->simpleXML->asXML();

        exit( 0 );
    }

    protected function existsAndReadable( $file ) {
        return is_string( $file ) && file_exists( $file ) && is_readable( $file );
    }

    public function getFileInfo() {
        $fileLocation = $this->fileLocation;

        if ( $this->existsAndReadable( $fileLocation ) ) {
            $info = array();

            $sXML         = simplexml_load_file( $fileLocation );
            $cratedAtName = $this->createdAtName;

            $info[ $this->createdAtName ] = array(
                'value' => end( $sXML->$cratedAtName ),
                'label' => 'Cached File Creation Datetime',
            );

            $info['productCount'] = array(
                'value' => $this->countProductsInFile( $sXML ),
                'label' => 'Number of Products Included',
            );

            $info['cachedFilePath'] = array( 'value' => $fileLocation, 'label' => 'Cached File Path' );

            $info['url'] = array(
                'value' => get_home_url( null, str_replace( ABSPATH, '', $fileLocation ) ),
                'label' => 'Cached File Url',
            );

            $info['size'] = array( 'value' => filesize( $fileLocation ) . 'B', 'label' => 'Cached File Size' );

            return $info;
        } else {
            return [];
        }
    }

    public function countProductsInFile( $file ) {
        if ( $this->existsAndReadable( $file ) ) {
            $sXML = simplexml_load_file( $file );
        } elseif ( $file instanceof \SimpleXMLElement || $file instanceof SimpleXMLExtended ) {
            $sXML = &$file;
        } else {
            return 0;
        }

        if ( $sXML->getName() == $this->productsElemWrapper ) {
            return $sXML->count();
        } elseif ( $sXML->getName() == $this->rootElemName ) {
            return $sXML->children()->children()->count();
        }

        return 0;
    }
}