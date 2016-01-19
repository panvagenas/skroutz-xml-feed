<?php
/**
 * WooArrayGenerator.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\XmlGenerator
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\XmlGenerator;

/**
 * Class WooArrayGenerator
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\XmlGenerator
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class WooArrayGenerator {
    const LOG_INFO = 'info';

    const LOG_ERROR = 'error';

    const AVAIL_IN_STOCK = 'In stock';

    const AVAIL_OUT_OF_STOCK = 'Out of stock, back-orders **NOT** allowed';

    const AVAIL_OUT_OF_STOCK_BACKORDERS = 'Out of stock, back-orders allowed';

    protected $log = [ ];

    /**
     * @var Options
     */
    protected $options;

    public function __construct( array $options = [ ] ) {
        $this->options = new Options( $options );
    }

    public function getOptions() {
        return $this->options;
    }

    protected function getProductIds( $limit = 0, $offset = 0 ) {
        global $wpdb;

        /** @noinspection SqlResolve */
        $sql = "SELECT ID
                FROM {$wpdb->posts}
                WHERE post_type = 'product' AND post_status = 'publish'
                ORDER BY ID DESC";

        if ( $limit ) {
            $sql .= ' LIMIT ' . absint( $limit );
        }
        if ( $offset ) {
            $sql .= ' OFFSET ' . absint( $offset );
        }

        return (array) $wpdb->get_col( $sql );
    }

    protected function log( $type, $message, $data = [ ] ) {
        $this->log[] = [
            'type' => $type,
            'msg'  => $message,
            'data' => $data,
        ];
    }

    public function getArray( $limit = 0, $offset = 0 ) {
        $mem = $this->getMemInM( ini_get( 'memory_limit' ) );

        $memLimit = $mem - ( 10 * 1024 * 1024 );

        $return = [ ];

        foreach ( $this->getProductIds( $limit, $offset ) as $i => $pid ) {

            if ( memory_get_usage() > $memLimit ) {
                wp_cache_flush();
            }

            $product = WC()->product_factory->get_product( (int) $pid );

            if ( ! is_object( $product ) || ! ( $product instanceof \WC_Product ) ) {
                $this->log( self::LOG_ERROR, 'Product failed in ' . __METHOD__, $product );
                continue;
            }

            $genProduct = new Product( $product );

            $notAvailable = ! $this->options->getAvailability()[ $genProduct->getAvailability() ];

            if ( ! $product->is_purchasable() || ! $product->is_visible() || $notAvailable ) {
                $reason = array();
                if ( ! $product->is_purchasable() ) {
                    $reason[] = 'product is not purchasable';
                }
                if ( ! $product->is_visible() ) {
                    $reason[] = 'product is not visible';
                }
                if ( $notAvailable ) {
                    $reason[] = 'product is unavailable';
                }
                $this->log(
                    self::LOG_INFO,
                    'Product <strong>' . $product->get_formatted_name()
                    . '</strong> failed. Reason(s) is(are): ' . implode( ', ', $reason ),
                    array(
                        'id'             => $product->id,
                        'SKU'            => $product->get_sku(),
                        'is_purchasable' => $product->is_purchasable(),
                        'is_visible'     => $product->is_visible(),
                        'availability'   => $genProduct->getAvailability(),
                    )
                );
                continue;
            }

            $return[] = $this->composeProductArray( $genProduct );
        }

        wp_cache_flush();

        return $return;
    }

    protected function composeProductArray( Product $product ) {
        $out = array();

        $out['ID'] = $product->getId();

        $out['id'] = $this->options->getMapId() == 0 ? $product->getSku() : $product->getId();

        $out['mpn'] = $this->options->getMapMpn() == 0
            ? $product->getSku()
            : $product->getAttrValue( $this->options->getMapMpn(), $product->getSku() );

        $out['name'] = '';
        if ( $this->options->getMapName() != 0 ) {
            $out['name'] = $product->getAttrValue( $this->options->getMapName(), '' );
        }
        if ( empty( $out['name'] ) ) {
            $out['name'] = $product->getTitle();
        }
        $out['name'] = trim( $out['name'] );

        if ( $this->options->getMapNameAppendSku()
             && $out['id']
             && ! is_numeric( strpos( $out['name'], $out['id'] ) )
        ) {
            $out['name'] .= ' ' . $out['id'];
        }

        $out['link'] = $product->getLink();

        $out['image'] = $product->getImageLink(
            $this->options->getMapImage() ? $this->options->getMapImage() : 'full'
        );

        $out['category'] = '';

        if ( is_numeric( $this->options->getMapCategory() ) ) {
            $out['category'] = $product->getAttrValue( $this->options->getMapCategory(), '' );
        } else {
            $categories      = $product->getTaxonomyTermNames(
                $this->options->getMapCategory(),
                (bool) $this->options->getMapCategoryTree() && ! is_numeric( $this->options->getMapCategory() )
            );
            $out['category'] = implode( $this->options->getMapCategoryGlue(), $categories );
        }

        if ( empty( $out['category'] ) ) {

        }

        $out['price'] = $this->options->getMapPrice() == 2 ? $product->getPrice( false ) : $product->getPrice();

        $out['inStock'] = $product->isInStock() ? $this->options->getInStockY() : $this->options->getInStockN();

        $out['availability'] = $this->options->getAvailability()[ $product->getAvailability() ];

        $out['manufacturer'] = is_numeric( $this->options->getMapManufacturer() )
            ? $product->getAttrValue( $this->options->getMapManufacturer(), '' )
            : $product->getTaxonomyTermNames( $this->options->getMapManufacturer(), false );

        if(is_array($out['manufacturer'])){
            $out['manufacturer'] = implode(' - ', $out['manufacturer']);
        }

        if ( $product->isProductVariable() && $this->options->isFashionStore() ) {

            $colors = $product->getAttrNamesFromIds( $this->options->getMapColor() );
            $sizes  = $product->getAttrNamesFromIds( $this->options->getMapSize() );

            if ( ! empty( $colors ) ) {
                $out['color'] = implode( $this->options->getMapColorGlue(), $colors );
            }

            if ( ! empty( $sizes ) ) {
                $out['size'] = implode( $this->options->getMapSizeGlue(), $sizes );
            }
        }

        if ( $this->options->isBookStore() ) {
            $isbn = $this->options->getMapIsbn() == 0
                ? $product->getSku()
                : $product->getAttrValue( $this->options->getMapIsbn() );

            if ( $isbn ) {
                $out['isbn'] = $isbn;
            }
        }

        return $out;
    }


    /**
     * @param $mem
     *
     * @return bool
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  TODO ${VERSION}
     */
    protected function getMemInM( $mem ) {
        if ( is_numeric( $mem ) ) {
            return $mem;
        }

        preg_match( '/^(\d+)([MmKkGg]?)$/', $mem, $matches );

        if ( is_string( $mem ) && isset( $matches[2] ) ) {
            switch ( $matches[2] ) {
                case 'k':
                case 'K':
                    return $matches[1] * 1024;
                case 'm':
                case 'M':
                    return $matches[1] * 1024 * 1024;
                case 'g':
                case 'G':
                    return $matches[1] * 1024 * 1024 * 1024;
                default:
                    return $matches[1];
            }
        }

        return false;
    }

    public function getLog() {
        return $this->log;
    }
}