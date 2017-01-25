<?php
/**
 * Created by PhpStorm.
 * User: vagenas
 * Date: 16/10/2014
 * Time: 12:03 μμ
 */

namespace skroutz;

use xd_v141226_dev\exception;

if ( ! defined( 'WPINC' ) ) {
	exit( 'Do NOT access this file directly: ' . basename( __FILE__ ) );
}

class xml extends \xd_v141226_dev\xml {
    const PRINT_XML = 0;
    const PRINT_GZ = 1;
    const PRINT_ZIP = 2;

	/**
	 * @var \SimpleXMLExtended
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
	 * @var array
	 */
	protected $skzXMLFields = array(
		'id',
		'name',
		'link',
		'image',
		'category',
		'price_with_vat',
		'instock',
		'availability',
		'manufacturer',
		'mpn',
		'isbn',
		'size',
		'color',
	);
	/**
	 * @var array
	 */
	protected $skzXMLFieldsLengths = array(
		'id'             => 200,
		'name'           => 300,
		'link'           => 1000,
		'image'          => 400,
		'category'       => 250,
		'price_with_vat' => 0,
		'instock'        => 0,
		'availability'   => 60,
		'manufacturer'   => 100,
		'mpn'            => 80,
		'isbn'           => 80,
		'size'           => 500,
		'color'          => 100,
	);
	/**
	 * @var array
	 */
	protected $skzXMLRequiredFields = array(
		'id',
		'name',
		'link',
		'image',
		'category',
		'price_with_vat',
		'instock',
		'availability',
		'manufacturer',
		'mpn',
	);
	/**
	 * @var string
	 */
	protected $rootElemName = 'mywebstore';
	/**
	 * @var string
	 */
	protected $productsElemWrapper = 'products';
	/**
	 * @var string
	 */
	protected $productElemName = 'product';

	/**
	 * @param array $array
	 *
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  141015
	 */
	public function parseArray( Array $array ) {
		// init simple xml if is not initialized already
		if ( ! $this->simpleXML ) {
			$this->initSimpleXML();
		}

		// parse array
		foreach ( $array as $k => $v ) {
			$this->appendProduct( $v );
		}

		return ! empty( $array ) && $this->saveXML();
	}

	/**
	 * @return $this
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  141015
	 */
	protected function initSimpleXML() {
		$this->fileLocation = $this->getFileLocation();

		$this->simpleXML = new \SimpleXMLExtended( '<?xml version="1.0" encoding="UTF-8"?><' . $this->rootElemName . '></' . $this->rootElemName . '>' );
		$this->simpleXML->addChild( $this->productsElemWrapper );

		return $this;
	}

	/**
	 * Returns the file location based on settings (even if it isn't exists)
	 *
	 * @return string
	 * @throws exception
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  141015
	 */
	public function getFileLocation() {
		$location = $this->©options->get( 'xml_location' );
		$fileName = $this->©options->get( 'xml_fileName' );

		$location = empty( $location ) || $location == '/' ? '' : ( trim( $location, '\\/' ) . '/' );

		return rtrim( ABSPATH, '\\/' ) . '/' . $location . trim( $fileName, '\\/' );
	}

	/**
	 * @param array $p
	 *
	 * @return int
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  150130
	 */
	public function appendProduct( Array $p ) {
		if ( ! $this->simpleXML ) {
			$this->initSimpleXML();
		}

		$products = $this->simpleXML->children();

		$validated = $this->validateArrayKeys( $p );

		if ( ! empty( $validated ) ) {
			$product = $products->addChild( $this->productElemName );

			foreach ( $validated as $key => $value ) {
				if ( $this->isValidXmlName( $value ) ) {
					$product->addChild( $key, $value );
				} else {
					$product->$key = null;
					$product->$key->addCData( $value );
				}
			}

			return 1;
		}

		return 0;
	}

	/**
	 * @param array $array
	 *
	 * @return array
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  141015
	 */
	protected function validateArrayKeys( Array $array ) {
		foreach ( $this->skzXMLRequiredFields as $fieldName ) {
			if ( ! isset( $array[ $fieldName ] ) || empty( $array[ $fieldName ] ) ) {
				$fields = array();
				foreach ( $this->skzXMLRequiredFields as $f ) {
					if ( ! isset( $array[ $f ] ) || empty( $array[ $f ] ) ) {
						array_push( $fields, $f );
					}
				}
				$name = isset( $array['name'] ) ? $array['name'] : ( isset( $array['id'] ) ? 'with id ' . $array['id'] : '' );
				if ( isset( $array['link'] ) ) {
					$name = '<a href="' . $array['link'] . '" target="_blank">' . $name . '</a>';
				}
				$this->©error->forceDBLog(
					'product',
					$array,
					'Product <strong>' . $name . '</strong> not included in XML file because field(s) ' . implode( ', ',
						$fields ) . ' is/are missing or is invalid'
				);

				return array();
			} else {
				$array[ $fieldName ] = $this->trimField( $array[ $fieldName ], $fieldName );
				if ( is_string( $array[ $fieldName ] ) ) {
					$array[ $fieldName ] = mb_convert_encoding( $array[ $fieldName ], "UTF-8" );
				}
			}
		}

		foreach ( $array as $k => $v ) {
			if ( ! in_array( $k, $this->skzXMLFields ) ) {
				unset( $array[ $k ] );
			}
		}

		return $array;
	}

	/**
	 * @param $value
	 * @param $fieldName
	 *
	 * @return bool|string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  141015
	 */
	protected function trimField( $value, $fieldName ) {
		if ( ! isset( $this->skzXMLFieldsLengths[ $fieldName ] ) ) {
			return false;
		}

		if ( $this->skzXMLFieldsLengths[ $fieldName ] === 0 ) {
			return $value;
		}

		return mb_substr( (string) $value, 0, $this->skzXMLFieldsLengths[ $fieldName ] );
	}

	protected function isValidXmlName( $name ) {
		try {
			new \DOMElement( $name );

			return true;
		} catch ( \DOMException $e ) {
			return false;
		}
	}

	/**
	 * @return bool|mixed
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  141015
	 */
	public function saveXML() {
		if ( ! ( $this->simpleXML instanceof \SimpleXMLExtended ) ) {
			return false;
		}
		$dir = dirname( $this->fileLocation );
		if ( ! file_exists( $dir ) ) {
			mkdir( $dir, 0755, true );
		}

		if ( $this->simpleXML && ! empty( $this->fileLocation ) && ( is_writable( $this->fileLocation ) || is_writable( $dir ) ) ) {
			if ( is_file( $this->fileLocation ) ) {
				unlink( $this->fileLocation );
			}
			$this->simpleXML->addChild( $this->createdAtName, date( 'Y-m-d H:i' ) );

            $compression = $this->©option->get('xml_compress');

            if($compression == 1 && $this->©env->supportsGzCompression()){
                $path = $this->fileLocation.'.gz';
                if ( is_file( $path ) ) {
                    unlink( $path );
                }
                $contents = gzencode($this->simpleXML->asXML());
                if(!$contents){
                    $this->©error->forceDBLog(
                        'error',
                        [],
                        'Failed to compress file'
                    );
                } else {
                    if(!file_put_contents($path, $contents)){
                        $this->©error->forceDBLog(
                            'error',
                            [],
                            'Failed to save compressed file'
                        );
                    }
                }
            } elseif ($compression == 2 && $this->©env->supportsZipCompression()){
                $path = $this->fileLocation.'.zip';
                if ( is_file( $path ) ) {
                    unlink( $path );
                }
                touch($path);

                $za = new \ZipArchive();
                if($za->open($path)){
                    $za->addFromString($this->©option->get('xml_fileName'), $this->simpleXML->asXML());
                    $za->close();
                } else {
                    $this->©error->forceDBLog(
                        'error',
                        [],
                        'Failed to save compressed file'
                    );
                }
            }

			return $this->simpleXML->asXML( $this->fileLocation );
		}

		return false;
	}

	/**
	 * @param       $prodId
	 * @param array $newValues
	 *
	 * @return bool|mixed
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  141015
	 */
	public function updateProductInXML( $prodId, Array $newValues ) {
		$newValues = $this->validateArrayKeys( $newValues );
		if ( empty( $newValues ) ) {
			return false;
		}
		// init simple xml if is not initialized already
		if ( ! $this->simpleXML ) {
			$this->initSimpleXML();
		}

		$p = $this->locateProductNode( $prodId );
		if ( ! $p ) {
			$p = $this->simpleXML->products->addChild( $this->productElemName );
		}
		foreach ( $newValues as $key => $value ) {
			if ( $this->isValidXmlName( $value ) ) {
				$p->addChild( $key, $value );
			} else {
				$p->$key = null;
				$p->$key->addCData( $value );
			}
		}

		return $this->saveXML();
	}

	/**
	 * @param $nodeId
	 *
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  141015
	 */
	protected function locateProductNode( $nodeId ) {
		if ( ! ( $this->simpleXML instanceof \SimpleXMLElement ) ) {
			return false;
		}

		foreach ( $this->simpleXML->products->product as $k => $p ) {
			if ( $p->id == $nodeId ) {
				return $p;
			}
		}

		return false;
	}

    /**
     * Print SimpleXMLElement $this->simpleXML to screen
     *
     * @param int $printFormat
     *
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  141015
     */
    public function printXML($printFormat = self::PRINT_XML) {
        if ( headers_sent() ) {
            return;
        }

        if ( ! ( $this->simpleXML instanceof \SimpleXMLExtended ) ) {
            $fileLocation = $this->getFileLocation();
            if ( ! $this->existsAndReadable( $fileLocation ) ) {
                return;
            }
            $this->simpleXML = simplexml_load_file( $fileLocation );
        }

        $unlinkPath = false;
        if ( $printFormat == self::PRINT_GZ && $this->©env->supportsGzCompression() ) {
            $path = $this->getFileLocation() . '.gz';

            if ( !$this->existsAndReadable( $path ) ) {
                $path = tempnam(sys_get_temp_dir(), uniqid());
                $file = fopen($path, 'w');
                fwrite($file, gzencode($this->simpleXML->asXML()));
                fclose($file);
                $unlinkPath = true;
            }

            header('Content-Disposition: attachment; filename="'.$this->©option->get('xml_fileName').'.gz"');
        } elseif ( $printFormat == self::PRINT_ZIP && $this->©env->supportsZipCompression() ) {
            $path = $this->getFileLocation() . '.zip';

            if ( !$this->existsAndReadable( $path ) ) {
                $path = tempnam(sys_get_temp_dir(), uniqid());
                $za = new \ZipArchive();
                if($za->open($path)){
                    $za->addFromString($this->©option->get('xml_fileName'), $this->simpleXML->asXML());
                    $za->close();
                }
                $unlinkPath = true;
            }

            header('Content-Disposition: attachment; filename="'.$this->©option->get('xml_fileName').'.zip"');
        } else {
            header( "Content-Type:text/xml" );
            $path = $this->getFileLocation();
        }

        header( "Content-Type:".mime_content_type($path)."" );
        readfile( $path );
        if($unlinkPath){
            unlink($path);
        }
    }

	/**
	 * Checks if file exists and is readable
	 *
	 * @param $file string File location
	 *
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  141015
	 */
	protected function existsAndReadable( $file ) {
		return is_string( $file ) && file_exists( $file ) && is_readable( $file );
	}

	/**
	 * Get XML file info
	 *
	 * @return array|null
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  141015
	 */
	public function getFileInfo() {
		$fileLocation = $this->getFileLocation();

		if ( $this->existsAndReadable( $fileLocation ) ) {
			$info = array();

			$sXML         = simplexml_load_file( $fileLocation );
			$cratedAtName = $this->createdAtName;

			$info[ $this->createdAtName ] = array(
				'value' => end( $sXML->$cratedAtName ),
				'label' => 'Cached File Creation Datetime'
			);

			$info['productCount'] = array(
				'value' => $this->countProductsInFile( $sXML ),
				'label' => 'Number of Products Included'
			);

			$info['cachedFilePath'] = array( 'value' => $fileLocation, 'label' => 'Cached File Path' );

			$info['url'] = array(
				'value' => $this->©url->to_wp_site_uri( str_replace( ABSPATH, '', $fileLocation ) ),
				'label' => 'Cached File Url'
			);

			$info['size'] = array( 'value' => filesize( $fileLocation ), 'label' => 'Cached File Size' );

			return $info;
		} else {
			return null;
		}
	}

	/**
	 * Counts total products in file
	 *
	 * @param $file string|\SimpleXMLExtended|\SimpleXMLElement
	 *
	 * @return int Total products in file
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  141015
	 */
	public function countProductsInFile( $file ) {
		if ( $this->existsAndReadable( $file ) ) {
			$sXML = simplexml_load_file( $file );
		} elseif ( $file instanceof \SimpleXMLElement || $file instanceof \SimpleXMLExtended ) {
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