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

    protected $log = [];

    protected function getProductIds($limit = 0, $offset = 0){
        global $wpdb;

        $sql = 'SELECT ID FROM ' . $wpdb->posts
               . ' WHERE post_type="product" AND post_status="publish" ORDER BY ID DESC';

        if($limit){
            $sql . ' LIMIT ' . absint($limit);
        }
        if($offset){
            $sql . ' OFFSET ' . absint($offset);
        }

        return (array) $wpdb->get_col( $sql );
    }

    protected function log($type, $message, $data = []){
        $this->log[] = [
            'type' => $type,
            'msg' => $message,
            'data' => $data
        ];
    }

    public function getArray($limit = 0, $offset = 0){
        $mem = $this->getMemInM( ini_get( 'memory_limit' ) ) / 1024 / 1024;

        $memLimit = ( $mem - 10 ) * 1024 * 1024;

        $return = [];

        foreach ( $this->getProductIds($limit, $offset) as $i => $pid ) {

            if ( memory_get_usage() > $memLimit ) {
                wp_cache_flush();
            }

            $product = WC()->product_factory->get_product( (int) $pid );

            if ( ! is_object( $product ) || ! ( $product instanceof \WC_Product ) ) {
                $this->log( self::LOG_ERROR, 'Product with failed in ' . __METHOD__, $product );
                continue;
            }

            $genProduct = new Product($product);

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
                        'availability'   => $genProduct->getAvailability()
                    )
                );
                continue;
            }

            $return[] = $genProduct->composeProductArray();
        }

        wp_cache_flush();

        return $return;
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
        if ( is_string( $mem ) ) {
            if ( isset( $matches[2] ) ) {
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
        }

        return false;
    }
}