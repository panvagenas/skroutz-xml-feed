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

    protected $log = [ ];

    protected $options;

    protected $defaults = [
        'map_id'              => 0,
        'map_mpn'             => 0,
        'map_name'            => 0,
        'map_name_append_sku' => 0,
        'map_image'           => 3,
        'map_category'        => 'product',
        'map_category_tree'   => 0,
        'map_category_glue'   => ' - ',
        'map_price'           => 1,
        'inStock_Y'           => 'Y',
        'inStock_N'           => 'N',
        'availability'        => [
            'Out of stock, back-orders **NOT** allowed',
            'Out of stock, back-orders allowed',
            'In stock',
        ],
        'map_manufacturer'    => 0,
        'map_color'           => [ ],
        'map_size'            => [ ],
        'colorGlue'           => ', ',
        'sizeGlue'            => ', ',
        'is_book_store'       => 0,
        'map_isbn'            => 0,
    ];

    public function __construct( array $options ) {
        $this->options = array_merge( $this->defaults, $options );
    }

    protected function getProductIds( $limit = 0, $offset = 0 ) {
        global $wpdb;

        $sql = 'SELECT ID FROM ' . $wpdb->posts
               . ' WHERE post_type="product" AND post_status="publish" ORDER BY ID DESC';

        if ( $limit ) {
            $sql . ' LIMIT ' . absint( $limit );
        }
        if ( $offset ) {
            $sql . ' OFFSET ' . absint( $offset );
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

            if ( ! $product->is_purchasable() || ! $product->is_visible() || $genProduct->getAvailability() < 1 ) {
                $reason = array();
                if ( ! $product->is_purchasable() ) {
                    $reason[] = 'product is not purchasable';
                }
                if ( ! $product->is_visible() ) {
                    $reason[] = 'product is not visible';
                }
                if ( $genProduct->getAvailability() < 1 ) {
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

        $out['id'] = $this->options['map_id'] == 0 ? $product->getSku() : $product->getId();

        $out['mpn'] = $this->options['map_mpn'] == 0
            ? $product->getSku()
            : $product->getAttrValue( $this->options['map_mpn'], $product->getSku() );

        $out['name'] = '';
        if ( $this->options['map_name'] != 0 ) {
            $out['name'] = $product->getAttrValue( $this->options['map_name'], '' );
        }
        if ( empty( $out['name'] ) ) {
            $out['name'] = $product->getTitle();
        }
        $out['name'] = trim( $out['name'] );

        if ( $this->options['map_name_append_sku'] && ! is_numeric( strpos( $out['name'], $out['id'] ) ) ) {
            $out['name'] .= ' ' . $out['id'];
        }

        $out['link'] = $product->getLink();

        $out['image'] = $product->getImageLink(
            $this->options['map_image'] ? $this->options['map_image'] : 'full'
        );

        $out['category'] = '';

        if ( is_numeric( $this->options['map_category'] ) ) {
            $out['category'] = $product->getAttrValue( $this->options['map_category'], '' );
        }

        if ( empty( $out['category'] ) ) {
            $categories      = $product->getTaxonomyTermNames(
                $out['category'],
                (bool) $this->options['map_category_tree'] && ! is_numeric( $this->options['map_category'] )
            );
            $out['category'] = implode( $this->options['map_category_glue'], $categories );
        }

        $out['price'] = $this->options['map_price'] == 2 ? $product->getPrice( false ) : $product->getPrice();

        $out['inStock'] = $product->isInStock() ? $this->options['inStock_Y'] : $this->options['inStock_N'];

        $out['availability'] = $this->options['availability'][ $product->getAvailability() ];

        $out['manufacturer'] = is_numeric( $this->options['map_manufacturer'] )
            ? $product->getAttrValue( $this->options['map_manufacturer'], '' )
            : $product->getTaxonomyTermNames( $this->options['map_manufacturer'], false );

        if ( $product->isProductVariable() && $this->options['is_fashion_store'] ) {

            $colors = $product->getAttrNamesFromIds( $this->options['map_color'] );
            $sizes  = $product->getAttrNamesFromIds( $this->options['map_size'] );

            if ( ! empty( $colors ) ) {
                $out['color'] = implode( $this->options['colorGlue'], $colors );
            }

            if ( ! empty( $sizes ) ) {
                $out['size'] = implode( $this->options['sizeGlue'], $sizes );
            }
        }

        if ( $this->options['is_book_store'] ) {
            $isbn = $this->options['map_isbn'] == 0
                ? $product->getSku()
                : $product->getAttrValue( $this->options['map_isbn'] );

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
}