<?php
/**
 * Created by PhpStorm.
 * User: vagenas
 * Date: 16/10/2014
 * Time: 3:46 μμ
 */

namespace skroutz;

use xd_v141226_dev\exception;

if ( ! defined( 'WPINC' ) ) {
    exit( 'Do NOT access this file directly: ' . basename( __FILE__ ) );
}

class options extends \xd_v141226_dev\options {
    /**
     * @var array Availability options for skroutz.gr
     */
    public $availOptions
        = array(
            'Άμεση παραλαβή / Παράδοση σε 1-3 ημέρες ',
            'Παράδοση σε 1-3 ημέρες',
            'Παραλαβή από το κατάστημα ή Παράδοση, σε 1-3 ημέρες',
            'Παραλαβή από το κατάστημα ή Παράδοση, σε 4-10 ημέρες',
            'Παράδοση σε 4-10 ημέρες',
            'Κατόπιν παραγγελίας, παραλαβή ή παράδοση έως 30 ημέρες',
        );

    /**
     * Sets up default options and validators.
     *
     * @extenders Can be overridden by class extenders (i.e. to override the defaults/validators);
     *    or to add additional default options and their associated validators.
     *
     * @param array $defaults   An associative array of default options.
     * @param array $validators An array of validators (can be a combination of numeric/associative keys).
     *
     * @return array The current array of options.
     * @throws exception If invalid types are passed through arguments list.
     * @throws exception If `count($defaults) !== count($validators)`.
     */
    public function setup( $defaults, $validators ) {
        $defaultCompress = 0;
        $compressRange = [ 0 ];
        if ( $this->©env->supportsGzCompression() ) {
            $compressRange[] = 1;
            $defaultCompress = 1;
        }
        if ( $this->©env->supportsZipCompression() ) {
            $compressRange[] = 2;
        }

        $skroutzDefaults = array(
            'encryption.key'                             => 'jkiabOKBNJO89347KJBKJBasfd',
            'support.url'                                => 'https://github.com/panvagenas/skroutz-xml-feed/issues',
            'styles.front_side.theme'                    => 'yeti',
            'crons.config'                               => array(),
            'menu_pages.theme'                           => 'yeti',
            'captchas.google.public_key'                 => '6LeCANsSAAAAAIIrlB3FrXe42mr0OSSZpT0pkpFK',
            'captchas.google.private_key'                => '6LeCANsSAAAAAGBXMIKAirv6G4PmaGa-ORxdD-oZ',
            'url_shortener.default_built_in_api'         => 'goo_gl',
            'url_shortener.custom_url_api'               => '',
            'url_shortener.api_keys.goo_gl'              => '',
            'menu_pages.panels.email_updates.action_url' => '',
            'menu_pages.panels.community_forum.feed_url' => '',
            'menu_pages.panels.news_kb.feed_url'         => '',
            'menu_pages.panels.videos.yt_playlist'       => '',
            /*********************
             * XML File relative
             ********************/
            // Internal, indicates XML generation progress
            'xml.progress'                               => 0,
            // File location
            'xml_location'                               => '',
            // File name
            'xml_fileName'                               => 'skroutz.xml',
            // Generation interval
            'xml_interval'                               => 'daily',
            // XML Generate Request Var
            'xml_generate_var'                           => 'skroutz',
            // XML Generate Request Var Value
            'xml_generate_var_value'                     => '',
            // advanced options
            'show_advanced'                              => 0,
            // compress options
            'xml_compress'                               => $defaultCompress,
            /*********************
             * Products relative
             ********************/
            // Include products
            'products_include'                           => array( 'product' ),
            // Exclude product categories
            'ex_cats'                                    => [],
            'ex_tags'                                    => [],
            // Availability when products in stock
            'avail_inStock'                              => 0,
            // Availability when products out stock
            'avail_outOfStock'                           => 6,
            // Availability when products out stock and backorders are allowed
            'avail_backorders'                           => 6,
            /*********************
             * Custom fields
             ********************/
            'map_id'                                     => 0,
            'map_name'                                   => 0,
            'map_name_append_sku'                        => 1,
            'map_link'                                   => 0,
            'map_image'                                  => 3,
            'map_category'                               => 'product',
            'map_category_tree'                          => 0,
            'map_price_with_vat'                         => 1,
            'map_manufacturer'                           => 0,
            'map_mpn'                                    => 0,
            'map_size'                                   => array(),
            'map_size_use'                               => 0,
            'map_color'                                  => array(),
            'map_color_use'                              => 0,
            /***********************************************
             * Fashion store
             ***********************************************/
            'is_fashion_store'                           => 0,
            /***********************************************
             * ISBN
             ***********************************************/
            'map_isbn'                                   => 0,
            'is_book_store'                              => 0,
        );

        $skroutzDefaultsValidators = array(
            // Internal
            'xml.progress'           => array( 'string:numeric >=' => 0 ),
            /***********************************************
             * Main Option Validators
             ***********************************************/
            'xml_location'           => array( 'string:!empty' ),
            'xml_fileName'           => array( 'string:!empty' ),
            'xml_generate_var'       => array( 'string:!empty' ),
            'xml_generate_var_value' => array( 'string:!empty' ),
            'xml_interval'           => array(
                'string:in_array' => array(
                    'every30m',
                    'hourly',
                    'twicedaily',
                    'daily',
                ),
            ),
            'xml_compress'           => array( 'string:in_array' => $compressRange ),
            'show_advanced'          => array( 'string:numeric >=' => 0, 'string:numeric <=' => 1 ),
            'products_include'       => array( 'array' ), // Not implemented yet
            'avail_inStock'          => array(
                'string:numeric >=' => 0,
                'string:numeric <=' => count( $this->availOptions ) - 1,
            ),
            'avail_outOfStock'       => array(
                'string:numeric >=' => 0,
                'string:numeric <=' => count( $this->availOptions ),
            ),
            'avail_backorders'       => array(
                'string:numeric >=' => 0,
                'string:numeric <=' => count( $this->availOptions ),
            ),
            'ex_cats'                => array( 'array' ),
            'ex_tags'                => array( 'array' ),
            'map_id'                 => array( 'string:numeric >=' => 0 ),
            'map_name'               => array( 'string:numeric >=' => 0 ),
            'map_name_append_sku'    => array( 'string:numeric >=' => 0, 'string:numeric <=' => 1 ),
            'map_link'               => array( 'string:numeric >=' => 0 ),
            'map_image'              => array( 'string:numeric >=' => 0 ),
            'map_category'           => array( 'string' ),
            'map_category_tree'      => array( 'string:numeric >=' => 0, 'string:numeric <=' => 1 ),
            'map_price_with_vat'     => array( 'string:numeric >=' => 0, 'string:numeric <=' => 3 ),
            'map_manufacturer'       => array( 'string' ),
            'map_mpn'                => array( 'string:numeric >=' => 0 ),
            'map_size'               => array( 'array' ),
            'map_size_use'           => array( 'string:numeric >=' => 0, 'string:numeric <=' => 1 ),
            'map_color'              => array( 'array' ),
            'map_color_use'          => array( 'string:numeric >=' => 0, 'string:numeric <=' => 1 ),
            'is_fashion_store'       => array( 'string:numeric >=' => 0, 'string:numeric <=' => 1 ),
            'map_isbn'               => array( 'string' ),
            'is_book_store'          => array( 'string:numeric >=' => 0, 'string:numeric <=' => 1 ),
        );

        $defaults   = array_merge( $defaults, $skroutzDefaults );
        $validators = array_merge( $validators, $skroutzDefaultsValidators );

        $this->_setup( $defaults, $validators );
    }

    /**
     * Fires when new options are saved. Then based on plugin page we use the appropriate method.
     * Always call the parent at the end.
     *
     * @param array $new_options
     */
    public function ®update( $new_options = array() ) {
        $bools = array(
            'is_fashion_store',
            'map_name_append_sku',
            'map_color_use',
            'map_size_use',
            'show_advanced',
            'map_category_tree',
        );
        foreach ( $bools as $v ) {
            if ( ! isset( $new_options[ $v ] ) ) {
                $new_options[ $v ] = 0;
            }
        }

        // Delete old file if XML file path changed
        if ( isset( $new_options['xml_location'] )
             && ( $new_options['xml_location'] != $this->get( 'xml_location' )
                  || $new_options['xml_fileName'] != $this->get( 'xml_fileName' ) )
        ) {
            if ( is_file( $this->©xml->getFileLocation() )
                 && mime_content_type( $this->©xml->getFileLocation() ) == 'application/xml'
            ) {
                $this->©file->delete( $this->©xml->getFileLocation() );
            }
        }

        parent::®update( $new_options );
    }
}