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

/**
 * Class Options
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class Options extends \Pan\MenuPages\Options{
    /**
     * @var array Availability options for skroutz.gr
     */
    public $availOptions = array(
        'Άμεση παραλαβή / Παράδοση σε 1-3 ημέρες ',
        'Παράδοση σε 1-3 ημέρες',
        'Παραλαβή από το κατάστημα ή Παράδοση, σε 1-3 ημέρες',
        'Παραλαβή από το κατάστημα ή Παράδοση, σε 4-10 ημέρες',
        'Παράδοση σε 4-10 ημέρες',
        'Κατόπιν παραγγελίας, παραλαβή ή παράδοση έως 30 ημέρες',
    );

    protected $fieldMap = [
        'id' => 'id',
        'mpn' => 'mpn',
        'name' => 'name',
        'link' => 'link',
        'image' => 'image',
        'category' => 'category',
        'price' => 'price',
        'inStock' => 'inStock',
        'availability' => 'availability',
        'manufacturer' => 'manufacturer',
        'color' => 'color',
        'size' => 'size',
        'isbn' => 'isbn',
    ];

    protected $fieldLengths = [
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
    ];
    protected $requiredFields = [
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
    ];

    protected static $optionsName = 'skz__options';

    public static function getInstance() {
        return parent::getInstance( self::$optionsName, self::getDefaultsArray() );
    }

    public static function getDefaultsArray(){
        return [];
    }

    public function setUp(){}

    public function getFileLocationOption(){}
    public function getCreatedAtOption(){}
    public function getXmlRootElemName(){}
    public function getXmlProductsElemWrapper(){}
    public function getXmlProductElemName(){}

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

    /**
     * @return string
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @see    Options::$optionsName
     * @codeCoverageIgnore
     */
    public static function getOptionsName() {
        return self::$optionsName;
    }
}