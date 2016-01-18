<?php
/**
 * Product.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\XmlGenerator
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\XmlGenerator;

/**
 * Class Product
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\XmlGenerator
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class Product {
    /**
     * @var \WC_Product|\WC_Product_Variable
     */
    protected $product;
    protected $wcHelper;

    /**
     * Product constructor.
     *
     * @param \WC_Product $product
     *
     * @since  TODO ${VERSION}
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     */
    public function __construct( \WC_Product $product ) {
        $this->product  = $product;
        $this->wcHelper = new WcHelper();
    }

    /**
     * @param array $getFromAttrIds
     *
     * @return array
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  TODO ${VERSION}
     */
    public function getAttrNamesFromIds( array $getFromAttrIds ) {
        if ( ! $this->isProductVariable() ) {
            return [];
        }

        $variations = $this->product->get_available_variations();
        $names      = array();

        foreach ( $getFromAttrIds as $attrId ) {
            $taxonomy = $this->wcHelper->getTaxonomyById( $attrId );

            if ( ! $taxonomy ) {
                break;
            }

            foreach ( $variations as $variation ) {
                $attrName = wc_attribute_taxonomy_name( $taxonomy->attribute_name );
                $key      = sanitize_title( 'attribute_' . $attrName );

                if (
                    isset( $variation['attributes'][ $key ] )
                    && $variation['is_in_stock']
                    && $variation['is_purchasable']
                ) {
                    if ( empty( $variation['attributes'][ $key ] ) ) {
                        $attr = $this->product->get_attribute( $attrName );
                        if ( is_string( $attr ) && ! empty( $attr ) ) {
                            $attrValues = array_map( 'trim', explode( ',', $attr ) );
                            $names      = array_merge( $names, $attrValues );
                            break;
                        }
                    } else {
                        $term = get_terms(
                            $attrName,
                            array( 'fields' => 'names', 'slug' => $variation['attributes'][ $key ] )
                        );

                        if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
                            $names[] = array_pop( $term );
                        }
                    }
                }
            }
        }

        return array_unique( $names );
    }

    public function getAttrValue( $attrId, $default = null ) {
        $value = $this->product->get_attribute( $this->wcHelper->getAttributeNameFromId( $attrId ) );

        return empty( $value ) ? $default : $value;
    }

    public function getTaxonomyTermNames( $taxonomy, $includeParents = false, $parentsSep = ' > ' ) {
        $terms = get_the_terms( $this->product->id, $taxonomy );
        $out   = array();

        if ( is_array( $terms ) ) {
            $occurredTaxIds = array();
            foreach ( $terms as $term ) {
                if ( $includeParents && in_array( $term->term_id, $occurredTaxIds ) ) {
                    continue;
                }

                if ( $includeParents ) {
                    $ancestors = get_ancestors( $term->term_id, $taxonomy );
                    foreach ( $ancestors as $ancestor ) {
                        if ( array_key_exists( $ancestor, $out ) ) {
                            unset( $out[ $ancestor ] );
                        }
                    }

                    $taxAncestorsTree = $this->wcHelper->taxonomyAncestorsTree( $term->term_id,
                        $taxonomy,
                        $parentsSep );

                    if ( $taxAncestorsTree && ! is_wp_error( $taxAncestorsTree ) ) {
                        $name = $taxAncestorsTree;
                    } else {
                        continue;
                    }
                } else {
                    $name = rtrim( ltrim( $term->name ) );
                }

                $occurredTaxIds = array_merge( $occurredTaxIds, get_ancestors( $term->term_id, $taxonomy ) );

                $out[ $term->term_id ] = $name;
            }
        }

        return $out;
    }

    public function isInStock() {
        return $this->product->is_in_stock();
    }

    public function isProductVariable() {
        return $this->product instanceof \WC_Product_Variable;
    }

    /**
     * If a sale price is set then this price is returned regardless $withTax
     *
     * @param bool $withTax
     *
     * @return string
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  TODO ${VERSION}
     */
    public function getPrice( $withTax = true ) {
        if ( $salePrice = $this->product->get_sale_price() ) {
            return $salePrice;
        }

        return $withTax ? $this->product->get_price_including_tax() : $this->product->get_price_excluding_tax();
    }

    public function getImageLink( $size = 'full' ) {
        $imageLink = wp_get_attachment_image_src( $this->product->get_image_id(), $size );

        return is_array( $imageLink ) ? $imageLink[0] : '';
    }

    public function getId() {
        return $this->product->id;
    }

    public function getSku() {
        return $this->product->get_sku();
    }

    public function getLink() {
        return $this->product->get_permalink();
    }

    public function getTitle() {
        return $this->product->get_title();
    }

    /**
     * Returns status codes for the availability of the product as explained bellow:
     * * `-1` Something went wrong
     * * `0` Out of stock, back-orders **NOT** allowed
     * * `1` Out of stock, back-orders allowed
     * * `2` In stock
     *
     * @return int The status as explained in method description
     *
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  TODO ${VERSION}
     */
    public function getAvailability() {
        $stockStatusInStock = $this->product->stock_status === 'instock';
        $manageStock        = $this->product->managing_stock();
        $backOrdersAllowed  = $this->product->backorders_allowed();
        $hasQuantity        = $this->product->get_stock_quantity() > 0;

        if ( $manageStock ) {
            if ( $hasQuantity ) {
                return 2;
            } elseif ( $backOrdersAllowed ) {
                return 1;
            }

            return 0;
        }

        if ( $stockStatusInStock ) {
            return 2;
        } elseif ( $backOrdersAllowed ) {
            return 1;
        }

        return -1;
    }
}