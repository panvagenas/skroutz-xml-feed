<?php

namespace Pan\XmlGenerator;

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Options {
    protected $mapId = 0;
    protected $mapMpn = 0;
    protected $mapName = 0;
    protected $mapNameAppendSku = 0;
    protected $mapImage = 'full';
    protected $mapCategory = 'product_cat';
    protected $mapCategoryTree = 0;
    protected $mapPrice = 1;
    protected $mapManufacturer = 'product_cat';
    protected $mapColor = [];
    protected $mapSize = [];
    protected $mapIsbn = 0;

    protected $mapColorGlue = ', ';
    protected $mapCategoryGlue = ' - ';
    protected $mapSizeGlue = ', ';

    protected $fashionStore = false;
    protected $bookStore = false;

    protected $inStock_Y = 'Y';
    protected $inStock_N = 'N';

    protected $availability = [
        WooArrayGenerator::AVAIL_OUT_OF_STOCK,
        WooArrayGenerator::AVAIL_OUT_OF_STOCK_BACKORDERS,
        WooArrayGenerator::AVAIL_IN_STOCK,
    ];

    public function __construct(array $options = []) {
        foreach ( $options as $name => $value ) {
            if(property_exists($this, $name)){
                $this->{$name} = $value;
            }
        }
    }

    /**
     * @return int
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$isFashionStore
     * @codeCoverageIgnore
     */
    public function isFashionStore() {
        return $this->fashionStore;
    }

    /**
     * @param int $fashionStore
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setFashionStore( $fashionStore ) {
        $this->fashionStore = $fashionStore;

        return $this;
    }

    /**
     * @return int
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapId
     * @codeCoverageIgnore
     */
    public function getMapId() {
        return $this->mapId;
    }

    /**
     * @param int $mapId
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapId( $mapId ) {
        $this->mapId = $mapId;

        return $this;
    }

    /**
     * @return int
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapMpn
     * @codeCoverageIgnore
     */
    public function getMapMpn() {
        return $this->mapMpn;
    }

    /**
     * @param int $mapMpn
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapMpn( $mapMpn ) {
        $this->mapMpn = $mapMpn;

        return $this;
    }

    /**
     * @return int
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapName
     * @codeCoverageIgnore
     */
    public function getMapName() {
        return $this->mapName;
    }

    /**
     * @param int $mapName
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapName( $mapName ) {
        $this->mapName = $mapName;

        return $this;
    }

    /**
     * @return int
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapNameAppendSku
     * @codeCoverageIgnore
     */
    public function getMapNameAppendSku() {
        return $this->mapNameAppendSku;
    }

    /**
     * @param int $mapNameAppendSku
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapNameAppendSku( $mapNameAppendSku ) {
        $this->mapNameAppendSku = $mapNameAppendSku;

        return $this;
    }

    /**
     * @return int
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapImage
     * @codeCoverageIgnore
     */
    public function getMapImage() {
        return $this->mapImage;
    }

    /**
     * @param int $mapImage
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapImage( $mapImage ) {
        $this->mapImage = $mapImage;

        return $this;
    }

    /**
     * @return string
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapCategory
     * @codeCoverageIgnore
     */
    public function getMapCategory() {
        return $this->mapCategory;
    }

    /**
     * @param string $mapCategory
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapCategory( $mapCategory ) {
        $this->mapCategory = $mapCategory;

        return $this;
    }

    /**
     * @return int
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapCategoryTree
     * @codeCoverageIgnore
     */
    public function getMapCategoryTree() {
        return $this->mapCategoryTree;
    }

    /**
     * @param int $mapCategoryTree
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapCategoryTree( $mapCategoryTree ) {
        $this->mapCategoryTree = $mapCategoryTree;

        return $this;
    }

    /**
     * @return int
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapPrice
     * @codeCoverageIgnore
     */
    public function getMapPrice() {
        return $this->mapPrice;
    }

    /**
     * @param int $mapPrice
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapPrice( $mapPrice ) {
        $this->mapPrice = $mapPrice;

        return $this;
    }

    /**
     * @return int
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapManufacturer
     * @codeCoverageIgnore
     */
    public function getMapManufacturer() {
        return $this->mapManufacturer;
    }

    /**
     * @param int $mapManufacturer
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapManufacturer( $mapManufacturer ) {
        $this->mapManufacturer = $mapManufacturer;

        return $this;
    }

    /**
     * @return array
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapColor
     * @codeCoverageIgnore
     */
    public function getMapColor() {
        return $this->mapColor;
    }

    /**
     * @param array $mapColor
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapColor( $mapColor ) {
        $this->mapColor = $mapColor;

        return $this;
    }

    /**
     * @return array
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapSize
     * @codeCoverageIgnore
     */
    public function getMapSize() {
        return $this->mapSize;
    }

    /**
     * @param array $mapSize
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapSize( $mapSize ) {
        $this->mapSize = $mapSize;

        return $this;
    }

    /**
     * @return int
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapIsbn
     * @codeCoverageIgnore
     */
    public function getMapIsbn() {
        return $this->mapIsbn;
    }

    /**
     * @param int $mapIsbn
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapIsbn( $mapIsbn ) {
        $this->mapIsbn = $mapIsbn;

        return $this;
    }

    /**
     * @return string
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapColorGlue
     * @codeCoverageIgnore
     */
    public function getMapColorGlue() {
        return $this->mapColorGlue;
    }

    /**
     * @param string $mapColorGlue
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapColorGlue( $mapColorGlue ) {
        $this->mapColorGlue = $mapColorGlue;

        return $this;
    }

    /**
     * @return string
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapCategoryGlue
     * @codeCoverageIgnore
     */
    public function getMapCategoryGlue() {
        return $this->mapCategoryGlue;
    }

    /**
     * @param string $mapCategoryGlue
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapCategoryGlue( $mapCategoryGlue ) {
        $this->mapCategoryGlue = $mapCategoryGlue;

        return $this;
    }

    /**
     * @return string
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$mapSizeGlue
     * @codeCoverageIgnore
     */
    public function getMapSizeGlue() {
        return $this->mapSizeGlue;
    }

    /**
     * @param string $mapSizeGlue
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setMapSizeGlue( $mapSizeGlue ) {
        $this->mapSizeGlue = $mapSizeGlue;

        return $this;
    }

    /**
     * @return int
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$isBookStore
     * @codeCoverageIgnore
     */
    public function isBookStore() {
        return $this->bookStore;
    }

    /**
     * @param int $bookStore
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setBookStore( $bookStore ) {
        $this->bookStore = $bookStore;

        return $this;
    }

    /**
     * @return string
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$inStock_Y
     * @codeCoverageIgnore
     */
    public function getInStockY() {
        return $this->inStock_Y;
    }

    /**
     * @param string $inStock_Y
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setInStockY( $inStock_Y ) {
        $this->inStock_Y = $inStock_Y;

        return $this;
    }

    /**
     * @return string
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$inStock_N
     * @codeCoverageIgnore
     */
    public function getInStockN() {
        return $this->inStock_N;
    }

    /**
     * @param string $inStock_N
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setInStockN( $inStock_N ) {
        $this->inStock_N = $inStock_N;

        return $this;
    }

    /**
     * @return array
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @see    Options::$availability
     * @codeCoverageIgnore
     */
    public function getAvailability() {
        return $this->availability;
    }

    /**
     * @param array $availability
     *
     * @return $this
     * @author Panagiotis Vagenas pan.vagenas@gmail.com
     * @codeCoverageIgnore
     */
    public function setAvailability( $availability ) {
        $this->availability = $availability;

        return $this;
    }
}