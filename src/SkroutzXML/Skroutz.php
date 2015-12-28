<?php
/**
 * Skroutz.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\SkroutzXML;

/**
 * Class Skroutz
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class Skroutz {
    /**
     * @var Options
     */
    protected $options;
    public function __construct() {}
    public function init(){}
    public function checkRequest(){}

    /**
     * @return bool
     * @static * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since TODO ${VERSION}
     */
    public static function hasBrandsPlugin() {
        return (in_array( 'woocommerce-brands/woocommerce-brands.php', (array) get_option( 'active_plugins', array() ) )
                || self::isPluginActiveForNetwork( 'woocommerce-brands/woocommerce-brands.php' ))
               && taxonomy_exists( 'product_brand' );
    }

    /**
     * Checks if a plugin is active for the network
     * @param string $plugin plugin basename
     *
     * @return bool
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  TODO ${VERSION}
     */
    private static function isPluginActiveForNetwork( $plugin ) {
        if ( !is_multisite() )
            return false;

        $plugins = get_site_option( 'active_sitewide_plugins');
        if ( isset($plugins[$plugin]) )
            return true;

        return false;
    }

    /**
     * @return false|null|object
     * @static * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since TODO ${VERSION}
     */
    public static function getBrandsPluginTaxonomy() {
        if ( self::hasBrandsPlugin() ) {
            return get_taxonomy( 'product_brand' );
        }

        return null;
    }
}