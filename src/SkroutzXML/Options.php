<?php
/**
 * Options.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\SkroutzXML;

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Class Options
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class Options extends \Pan\MenuPages\Options {
    const OPTIONS_NAME = 'skz__options';

    /**
     * @var array Availability options for skroutz.gr
     */
    public static $availOptions = array(
        'Άμεση παραλαβή / Παράδοση σε 1-3 ημέρες ',
        'Παράδοση σε 1-3 ημέρες',
        'Παραλαβή από το κατάστημα ή Παράδοση, σε 1-3 ημέρες',
        'Παραλαβή από το κατάστημα ή Παράδοση, σε 4-10 ημέρες',
        'Παράδοση σε 4-10 ημέρες',
        'Κατόπιν παραγγελίας, παραλαβή ή παράδοση έως 30 ημέρες',
    );

    protected $fieldMap = [
        'id'           => 'id',
        'mpn'          => 'mpn',
        'name'         => 'name',
        'link'         => 'link',
        'image'        => 'image',
        'category'     => 'category',
        'price'        => 'price_with_vat',
        'inStock'      => 'instock',
        'availability' => 'availability',
        'manufacturer' => 'manufacturer',
        'color'        => 'color',
        'size'         => 'size',
        'isbn'         => 'isbn',
    ];

    protected $fieldLengths = [
        'id'             => 200,
        'name'           => 300,
        'link'           => 1000,
        'image'          => 400,
        'category'       => 250,
        'price' => 0,
        'inStock'        => 0,
        'availability'   => 60,
        'manufacturer'   => 100,
        'mpn'            => 80,
        'isbn'           => 80,
        'size'           => 500,
        'color'          => 100,
    ];
    protected $requiredFields = [
        'id',
        'name',
        'link',
        'image',
        'category',
        'price',
        'inStock',
        'availability',
        'manufacturer',
        'mpn',
    ];

    public function __construct( $optionsBaseName, array $defaults ) {
        parent::__construct( $optionsBaseName, $defaults );
    }


    /**
     * @param string $optionsBaseName
     * @param array  $defaults
     *
     * @return $this
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     */
    public static function getInstance($optionsBaseName = '', array $defaults = []) {
        return parent::getInstance( self::OPTIONS_NAME, self::getDefaultsArray() );
    }

    public function getDefaults() {
        if ( ! $this->defaults ) {
            $this->defaults = self::getDefaultsArray();
        }

        return parent::getDefaults();
    }

    public static function getDefaultsArray() {
        return [
            'donate'                 => 1,
            /*********************
             * XML File relative
             ********************/
            // File location
            'xml_location'           => '',
            // File name
            'xml_fileName'           => 'skroutz.xml',
            // Generation interval
            'xml_interval'           => 86399, // TODO Changed to int from 151228
            // XML Generate Request Var
            'xml_generate_var'       => 'skroutz',
            // XML Generate Request Var Value
            'xml_generate_var_value' => wp_generate_password(32),
            /*********************
             * Products relative
             ********************/
            // Include products
            'products_include'       => array( 'product' ),
            // Availability when products in stock
            'avail_inStock'          => 0,
            // Availability when products out stock
            'avail_outOfStock'       => 6,
            // Availability when products out stock and backorders are allowed
            'avail_backorders'       => 6,
            /*********************
             * Custom fields
             ********************/
            'map_id'                 => 0,
            'map_name'               => 0,
            'map_name_append_sku'    => 0,
            'map_link'               => 0, // TODO Deprecated since 151228
            'map_image'              => 'full', // TODO Need translation for the new version
            'map_category'           => 'product_cat',
            'map_category_tree'      => 0,
            'map_price_with_vat'     => 1,
            'map_manufacturer'       => 'product_cat',
            'map_mpn'                => 0,
            'map_size'               => array(),
            'map_size_use'           => 0, // TODO Deprecated since 151228
            'map_color'              => array(),
            'map_color_use'          => 0, // TODO Deprecated since 151228
            /***********************************************
             * Fashion store
             ***********************************************/
            'is_fashion_store'       => 0,
            /***********************************************
             * ISBN
             ***********************************************/
            'map_isbn'               => 0,
            'is_book_store'          => 0,
        ];
    }

    public function translateOptions() {
        return [
            'mapId'            => $this->get( 'map_id' ),
            'mapMpn'           => $this->get( 'map_mpn' ),
            'mapName'          => $this->get( 'map_name' ),
            'mapNameAppendSku' => $this->get( 'map_name_append_sku' ),
            'mapImage'         => $this->get( 'map_image' ),
            'mapCategory'      => $this->get( 'map_category' ),
            'mapCategoryTree'  => $this->get( 'map_category_tree' ),
            'mapPrice'         => $this->get( 'map_price_with_vat' ),
            'mapManufacturer'  => $this->get( 'map_manufacturer' ),
            'mapColor'         => $this->get( 'map_color' ),
            'mapSize'          => $this->get( 'map_size' ),
            'mapIsbn'          => $this->get( 'map_isbn' ),
            'fashionStore'     => $this->get( 'is_fashion_store' ),
            'bookStore'        => $this->get( 'is_book_store' ),
        ];
    }

    public function getFileLocationOption() {
        return trailingslashit( ABSPATH ) . $this->getXmlRelLocationOption();
    }

    public function getXmlRelLocationOption(){
        return ($this->get( 'xml_location' ) ? trailingslashit( $this->get( 'xml_location' ) ) : '')
               . $this->get( 'xml_fileName' );
    }

    public function getCreatedAtOption() {
        return 'created_at';
    }

    public function getXmlRootElemName() {
        return 'mywebstore';
    }

    public function getXmlProductsElemWrapper() {
        return 'products';
    }

    public function getXmlProductElemName() {
        return 'product';
    }

    /**
     * @return array
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @see    Options::$fieldMap
     * @codeCoverageIgnore
     */
    public function getFieldMap() {
        return $this->fieldMap;
    }

    /**
     * @return array
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @see    Options::$fieldLengths
     * @codeCoverageIgnore
     */
    public function getFieldLengths() {
        return $this->fieldLengths;
    }

    /**
     * @return array
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @see    Options::$requiredFields
     * @codeCoverageIgnore
     */
    public function getRequiredFields() {
        return $this->requiredFields;
    }

    public function getGenerateXmlUrl(){
        return home_url() . "?{$this->get('xml_generate_var')}={$this->get('xml_generate_var_value')}";
    }
}