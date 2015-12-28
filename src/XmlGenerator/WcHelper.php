<?php
/**
 * WcHelper.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\XmlGenerator
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\XmlGenerator;

/**
 * Class WcHelper
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\XmlGenerator
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class WcHelper {
    protected static $wcTaxonomiesById = [];

    public function getTaxonomyById( $taxonomyId ) {
        if(isset(self::$wcTaxonomiesById[$taxonomyId])){
            return self::$wcTaxonomiesById[$taxonomyId];
        }

        foreach ( wc_get_attribute_taxonomies() as $taxonomy ) {
            if ( $taxonomyId == $taxonomy->attribute_id ) {
                self::$wcTaxonomiesById[$taxonomyId] = $taxonomy;
                return $taxonomy;
            }
        }

        return null;
    }

    public function getAttributeNameFromId($attrId) {
        if(isset(self::$wcTaxonomiesById[$attrId])){
            return trim(self::$wcTaxonomiesById[$attrId]->attribute_name);
        }

        foreach ( wc_get_attribute_taxonomies() as $taxonomy ) {
            if ( $taxonomy->attribute_id == $attrId ) {
                self::$wcTaxonomiesById[$attrId] = $taxonomy;
                return trim( $taxonomy->attribute_name );
            }
        }

        return '';
    }

    /**
     * @param        $taxId
     * @param        $taxonomy
     * @param string $separator
     * @param array  $visited
     *
     * @return array|null|object|string|\WP_Error
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  TODO ${VERSION}
     */
    public function taxonomyAncestorsTree( $taxId, $taxonomy, $separator = ' > ', $visited = array() ) {
        $chain  = '';
        $parent = get_term( $taxId, $taxonomy );
        if ( is_wp_error( $parent ) ) {
            return $parent;
        }

        $name = $parent->name;

        if ( $parent->parent && ( $parent->parent != $parent->term_id ) && ! in_array( $parent->parent, $visited ) ) {
            $visited[] = $parent->parent;
            $chain .= $this->taxonomyAncestorsTree( $parent->parent, $taxonomy, $separator, $visited ) . $separator;
        }

        $chain .= $name;

        return $chain;
    }
}