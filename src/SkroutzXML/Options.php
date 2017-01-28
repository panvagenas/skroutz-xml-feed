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

use Pan\XmlGenerator\Logger\Handlers\DBHandler;

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
    const OPTIONS_NAME = 'skroutz__options';
    protected $pageHookSuffix;
    protected $pageId = 'skroutz-xml-settings';

    /**
     * @var array Availability options for skroutz.gr
     */
    public static $availOptions
        = array(
            'Άμεση παραλαβή / Παράδοση σε 1-3 ημέρες ',
            'Παράδοση σε 1-3 ημέρες',
            'Παραλαβή από το κατάστημα ή Παράδοση, σε 1-3 ημέρες',
            'Παραλαβή από το κατάστημα ή Παράδοση, σε 4-10 ημέρες',
            'Παράδοση σε 4-10 ημέρες',
            'Κατόπιν παραγγελίας, παραλαβή ή παράδοση έως 30 ημέρες',
        );

    protected $fieldMap
        = [
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

    protected $fieldLengths
        = [
            'id'           => 200,
            'name'         => 300,
            'link'         => 1000,
            'image'        => 400,
            'category'     => 250,
            'price'        => 0,
            'inStock'      => 0,
            'availability' => 60,
            'manufacturer' => 100,
            'mpn'          => 80,
            'isbn'         => 80,
            'size'         => 500,
            'color'        => 100,
        ];
    protected $requiredFields
        = [
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
     * @return \Pan\MenuPages\Options|Options
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     */
    public static function getInstance( $optionsBaseName = '', array $defaults = [] ) {
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
            'xml_generate_var_value' => self::generatePassword( 32 ),
            /*********************
             * Products relative
             ********************/
            // Include products
            'products_include'       => [],
            // Availability when products in stock
            'avail_inStock'          => 0,
            // Availability when products out stock
            'avail_outOfStock'       => 6,
            // Availability when products out stock and backorders are allowed
            'avail_backorders'       => 6,
            // Exclude products
            'ex_cats'                => [],
            'ex_tags'                => [],
            /*********************
             * Custom fields
             ********************/
            'map_id'                 => 0,
            'map_name'               => 0,
            'map_name_append_sku'    => 0,
            'map_link'               => 0, // TODO Deprecated since 151228
            'map_image'              => '3', // TODO Need translation for the new version
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
            'exclCategories'   => $this->get( 'ex_cats' ),
            'exclTags'         => $this->get( 'ex_tags' ),
        ];
    }

    public function getFileLocationOption() {
        return trailingslashit( ABSPATH ) . $this->getXmlRelLocationOption();
    }

    public function getXmlRelLocationOption() {
        return ( $this->get( 'xml_location' ) ? trailingslashit( $this->get( 'xml_location' ) ) : '' )
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

    public function getGenerateXmlUrl() {
        return home_url() . "?{$this->get('xml_generate_var')}={$this->get('xml_generate_var_value')}";
    }

    public function addMenuPages() {
        //create new top-level menu
        $this->pageHookSuffix = add_submenu_page( 'options-general.php',
                                                  'Skroutz XML Feed Settings',
                                                  'Skroutz XML Feed',
                                                  'administrator',
                          $this->optionsBaseName . '-settings',
                                                  [ $this, 'renderSettingsPage' ] );

        //call register settings function
        add_action( 'admin_init', [ $this, 'registerSettings' ] );
        add_action( "admin_footer-{$this->pageHookSuffix}", [$this, 'footerScripts'] );
    }

    /**
     * TODO Move this to JS file
     */
    public function footerScripts(){
        ?>
        <script type="text/javascript">
            jQuery(document).ready( function($) {
                $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                postboxes.add_postbox_toggles( '<?php echo $this->pageHookSuffix; ?>' );
                $('#fx-smb-form').submit( function(){
                    $('#publishing-action').find('.spinner').css('display','inline');
                });
                $('#delete-action').find('.submitdelete').on('click', function() {
                    return confirm(/*'Are you sure want to do this?'*/'Sorry, not yet implemented');
                });
            });
        </script>
        <?php
    }

    public function getPageId(){
        return $this->pageId;
    }

    public function registerSettings() {
        register_setting( $this->optionsBaseName . '-settings-group',
                          $this->optionsBaseName,
                          [ $this, 'validateSettings' ] );
    }

    public function validateSettings( $value ) {
        // FIXME Validate settings
        return $value;
    }

    public function saveBox(){
        ?>
        <div id="submitpost" class="submitbox">

            <div>

                <div id="delete-action">
                    <a href="#" class="submitdelete deletion">Reset Settings</a>
                </div>

                <div id="publishing-action">
                    <span class="spinner"></span>
                    <?php submit_button( esc_attr( 'Save' ), 'primary', 'submit', false );?>
                </div>

                <div class="clear"></div>

            </div>

        </div>

        <?php
    }

    public function infoBox(){
        $skz      = new Skroutz();
        $fileInfo = $skz->getXmlObj()->getFileInfo();

        $genUrl = Options::getInstance()->getGenerateXmlUrl();

        $content = '<div class="row">';
        if ( empty( $fileInfo ) ) {
            $content .= '<p class="alert alert-danger">
                        File not generated yet. Please use the <i>Generate XML Now</i>
                        button to generate a new XML file</p>';
        } else {
            foreach ( $fileInfo as $item ) {
                $content .= '<span class="list-group-item">';
                $content .= $item['label'] . ': </br><strong>' . $item['value'] . '</strong>';
                $content .= '</span><hr>';
            }

            $content .= '<p><a class="button button-primary button-sm action-button" href="'
                        . home_url( Options::getInstance()->getXmlRelLocationOption() )
                        . '" target="_blank" role="button">';
            $content .= 'Open Cached File';
            $content .= '</a></p><hr>';
            $content .= '<p><a class="button button-primary button-sm action-button" href="'
                        . $genUrl
                        . '" target="_blank" role="button">';
            $content .= 'Open Generate URL';
            $content .= '</a></p>';
        }
        $content .= '</div>';

        echo $content;
    }

    public function genNowBox() {
        ?>
        <p>
            <button id="generate"
                    title="Generate XML Now"
                    class="button button-hero button-controls gen-now-button widefat"
                    data-target="#generateNowModal"
                    data-toggle="xd-v141226-dev-modal">Generate XML Now
            </button>
        </p>
        <?php
        wp_nonce_field('skz-gen-now-action', 'skz-gen-now-action');
    }

    public function generalOptionsBox(){
        $availOptions = [];
        foreach ( Options::$availOptions as $value => $label ) {
            $availOptions[ $label ] = (string) $value;
        }

        $availOptionsDoNotInclude                   = $availOptions;
        $availOptionsDoNotInclude['Do not Include'] = count( $availOptions );

        $attrTaxonomies = [];
        foreach ( wc_get_attribute_taxonomies() as $atrTax ) {
            $attrTaxonomies[ $atrTax->attribute_label ] = $atrTax->attribute_id;
        }

        $productCategories = get_categories( [ 'taxonomy' => 'product_cat', 'hide_empty' => 0 ] );
        $categories = [];
        foreach ( $productCategories as $productCategory ) {
            $categories[] = [ 'label' => $productCategory->name, 'value' => (string) $productCategory->term_id ];
        }

        $productTags = get_terms( ['taxonomy' => 'product_tag', 'hide_empty' => 0] );
        $tags = [];
        foreach ( $productTags as $productTag ) {
            $tags[] = [ 'label' => $productTag->name, 'value' => (string) $productTag->term_id ];
        }

        $genUrl = Options::getInstance()->getGenerateXmlUrl();
        ?>
        <table class="form-table general-options">
            <span class="list-group-item">Generate XML URL:
                <strong><?php echo esc_html($genUrl); ?></strong>
            </span>
            <hr>

            <tr valign="top">
                <th scope="row"><label for="xml_generate_var">XML Request Generate Variable</label></th>
                <td>
                    <input type="text"
                           id="xml_generate_var"
                           name="<?php echo $this->getOptionInputName( 'xml_generate_var' ); ?>"
                           value="<?php echo esc_attr( $this->get( 'xml_generate_var' ) ); ?>" />
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="xml_generate_var_value">XML Request Generate Variable Value</label></th>
                <td>
                    <input type="text"
                           id="xml_generate_var_value"
                           name="<?php echo $this->getOptionInputName( 'xml_generate_var_value' ); ?>"
                           value="<?php echo esc_attr( $this->get( 'xml_generate_var_value' ) ); ?>" />
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="xml_fileName">XML Filename</label></th>
                <td>
                    <input type="text"
                           id="xml_fileName"
                           name="<?php echo $this->getOptionInputName( 'xml_fileName' ); ?>"
                           value="<?php echo esc_attr( $this->get( 'xml_fileName' ) ); ?>" />
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="xml_location">XML File Location</label></th>
                <td>
                    <input type="text"
                           id="xml_fileName"
                           name="<?php echo $this->getOptionInputName( 'xml_location' ); ?>"
                           value="<?php echo esc_attr( $this->get( 'xml_location' ) ); ?>" />
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="xml_interval">XML File Generation Interval</label></th>
                <td>
                    <select id="xml_interval"
                            name="<?php echo $this->getOptionInputName( 'xml_interval' ); ?>">
                        <option value="daily" <?php echo selected('daily', $this->get( 'xml_interval' )); ?>>Daily</option>
                        <option value="twicedaily" <?php echo selected('twicedaily', $this->get( 'xml_interval' )); ?>>Twice Daily</option>
                        <option value="hourly" <?php echo selected('hourly', $this->get( 'xml_interval' )); ?>>Hourly</option>
                        <option value="every30m" <?php echo selected('every30m', $this->get( 'xml_interval' )); ?>>Every Thirty Minutes</option>
                        <option value="0" <?php echo selected('0', $this->get( 'xml_interval' )); ?>>Disable caching of file (not recommended for stores with many products)</option>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="avail_inStock">Product availability when item is in stock</label></th>
                <td>
                    <select id="avail_inStock"
                            name="<?php echo $this->getOptionInputName( 'avail_inStock' ); ?>">
                        <?php foreach ( $availOptions as $label => $value ) {
                            ?>
                            <option
                                value="<?php echo $value; ?>"
                                <?php echo selected($value, $this->get( 'avail_inStock' )); ?>
                            ><?php echo $label; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="avail_outOfStock">Product availability when item is out of stock</label></th>
                <td>
                    <select id="avail_outOfStock"
                            name="<?php echo $this->getOptionInputName( 'avail_outOfStock' ); ?>">
                        <?php foreach ( $availOptionsDoNotInclude as $label => $value ) {
                            ?>
                            <option
                                value="<?php echo $value; ?>"
                                <?php echo selected($value, $this->get( 'avail_outOfStock' )); ?>
                            ><?php echo $label; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="avail_backorders">Product availability when item is out of stock and backorders are allowed</label></th>
                <td>
                    <select id="avail_backorders"
                            name="<?php echo $this->getOptionInputName( 'avail_backorders' ); ?>">
                        <?php foreach ( $availOptionsDoNotInclude as $label => $value ) {
                            ?>
                            <option
                                value="<?php echo $value; ?>"
                                <?php echo selected($value, $this->get( 'avail_backorders' )); ?>
                            ><?php echo $label; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <?php
            if($categories){
                ?>
                <tr valign="top">
                    <th scope="row"><label for="ex_cats">
                            Exclude products from certain categories
                            <br>(if a product has any of these categories then it will not be included in the XML)
                        </label></th>
                    <td>
                        <input type="hidden" name="<?php echo $this->getOptionInputName( 'ex_cats' ); ?>" value="">
                        <select multiple
                                id="ex_cats"
                                name="<?php echo $this->getOptionInputName( 'ex_cats' ); ?>">
                            <?php foreach ( $categories as $category ) {
                                ?>
                                <option
                                    value="<?php echo $category['value']; ?>"
                                    <?php echo selected($category['value'], $this->get( 'ex_cats' )); ?>
                                ><?php echo $category['label']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <?php
            }
            ?>

            <?php
            if($tags){
                ?>
                <tr valign="top">
                    <th scope="row"><label for="ex_tags">
                            Exclude products from certain tags
                            <br>(if a product has any of these tags then it will not be included in the XML)
                        </label></th>
                    <td>
                        <input type="hidden" name="<?php echo $this->getOptionInputName( 'ex_tags' ); ?>" value="">
                        <select multiple
                                id="ex_tags"
                                name="<?php echo $this->getOptionInputName( 'ex_tags' ); ?>">
                            <?php foreach ( $tags as $tag ) {
                                ?>
                                <option
                                    value="<?php echo $tag['value']; ?>"
                                    <?php echo selected($tag['value'], $this->get( 'ex_tags' )); ?>
                                ><?php echo $tag['label']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    }

    public function mapOptionsBox(){
        $productCategories = get_categories( [ 'taxonomy' => 'product_cat', 'hide_empty' => 0 ] );
        $categories = [];
        foreach ( $productCategories as $productCategory ) {
            $categories[] = [ 'label' => $productCategory->name, 'value' => (string) $productCategory->term_id ];
        }

        $productTags = get_terms( ['taxonomy' => 'product_tag', 'hide_empty' => 0] );
        $tags = [];
        foreach ( $productTags as $productTag ) {
            $tags[] = [ 'label' => $productTag->name, 'value' => (string) $productTag->term_id ];
        }
        $attrTaxonomies = wc_get_attribute_taxonomies();
        ?>
        <table class="form-table map-fields">
            <tr valign="top">
                <th scope="row"><label for="map_id">Product ID</label></th>
                <td>
                    <select id="map_id"
                            name="<?php echo $this->getOptionInputName( 'map_id' ); ?>">
                        <option value="0" <?php echo selected('0', $this->get( 'map_id' )); ?>>Use Product SKU</option>
                        <option value="1" <?php echo selected('1', $this->get( 'map_id' )); ?>>Use Product ID</option>
                    </select>
                </td>
            </tr>

            <?php
            $options = array();

            $options[] = array(
                'label' => 'Use Product Categories',
                'value' => 'product_cat'
            );

            $options[] = array(
                'label' => 'Use Product Tags',
                'value' => 'product_tag'
            );

            foreach ( $attrTaxonomies as $taxonomies ) {
                $options[] = array(
                    'label' => $taxonomies->attribute_label,
                    'value' => $taxonomies->attribute_id
                );
            }

            if ( Skroutz::hasBrandsPlugin() && ( $brandsTax = Skroutz::getBrandsPluginTaxonomy() ) ) {
                $options[] = array(
                    'label' => 'Use WooCommerce Brands Plugin',
                    'value' => $brandsTax->name
                );
            }
            ?>
            <tr valign="top">
                <th scope="row"><label for="map_id">Product Manufacturer Field</label></th>
                <td>
                    <select id="map_id"
                            name="<?php echo $this->getOptionInputName( 'map_id' ); ?>">
                        <?php
                        foreach ($options as $option){
                            ?>
                            <option value="<?php echo esc_attr($option['value']); ?>"
                                <?php echo selected($option['value'], $this->get( 'map_id' )); ?>>
                                <?php echo esc_html($option['label']); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <?php
            $options = array();

            $options[] = array(
                'label' => 'Use Product SKU',
                'value' => '0'
            );
            foreach ( $attrTaxonomies as $taxonomies ) {
                $options[] = array(
                    'label' => $taxonomies->attribute_label,
                    'value' => $taxonomies->attribute_id
                );
            }
            ?>
            <tr valign="top">
                <th scope="row"><label for="map_mpn">Product Manufacturer SKU</label></th>
                <td>
                    <select id="map_mpn"
                            name="<?php echo $this->getOptionInputName( 'map_mpn' ); ?>">
                        <?php
                        foreach ($options as $option){
                            ?>
                            <option value="<?php echo esc_attr($option['value']); ?>"
                                <?php echo selected($option['value'], $this->get( 'map_mpn' )); ?>>
                                <?php echo esc_html($option['label']); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <?php
            $options = array();

            $options[] = array(
                'label' => 'Use Product Name',
                'value' => '0'
            );

            foreach ( $attrTaxonomies as $taxonomies ) {
                $options[] = array(
                    'label' => $taxonomies->attribute_label,
                    'value' => $taxonomies->attribute_id
                );
            }

            ?>
            <tr valign="top">
                <th scope="row"><label for="map_name">Product Name</label></th>
                <td>
                    <select id="map_name"
                            name="<?php echo $this->getOptionInputName( 'map_name' ); ?>">
                        <?php
                        foreach ($options as $option){
                            ?>
                            <option value="<?php echo esc_attr($option['value']); ?>"
                                <?php echo selected($option['value'], $this->get( 'map_name' )); ?>>
                                <?php echo $option['label']; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="map_name_append_sku">
                        If you check this the product SKU will be appended to product name.
                        If no SKU is set then product ID will be used
                    </label>
                </th>
                <td>
                    <input type="hidden"
                           name="<?php echo $this->getOptionInputName( 'map_name_append_sku' ); ?>"
                           value="" />
                    <input type="checkbox"
                           id="map_name_append_sku"
                           name="<?php echo $this->getOptionInputName( 'map_name_append_sku' ); ?>"
                        <?php echo checked( 'on', $this->get( 'map_name_append_sku' ) ); ?> />
                </td>
            </tr>

            <?php
            $options = array(
                array(
                    'label' => 'Thumbnail',
                    'value' => '0'
                ),
                array(
                    'label' => 'Medium',
                    'value' => '1'
                ),
                array(
                    'label' => 'Large',
                    'value' => '2'
                ),
                array(
                    'label' => 'Full',
                    'value' => '3'
                )
            );
            ?>
            <tr valign="top">
                <th scope="row"><label for="map_image">Product Image</label></th>
                <td>
                    <select id="map_image"
                            name="<?php echo $this->getOptionInputName( 'map_image' ); ?>">
                        <?php
                        foreach ($options as $option){
                            ?>
                            <option value="<?php echo esc_attr($option['value']); ?>"
                                <?php echo selected($option['value'], $this->get( 'map_image' )); ?>>
                                <?php echo esc_html($option['label']); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <?php
            $options = array();

            $options[] = array(
                'label' => 'Use Product Permalink',
                'value' => '0'
            );
            ?>
            <tr valign="top">
                <th scope="row"><label for="map_link">Product Link</label></th>
                <td>
                    <select id="map_link"
                            name="<?php echo $this->getOptionInputName( 'map_link' ); ?>">
                        <?php
                        foreach ($options as $option){
                            ?>
                            <option value="<?php echo esc_attr($option['value']); ?>"
                                <?php echo selected($option['value'], $this->get( 'map_link' )); ?>>
                                <?php echo esc_html($option['label']); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <?php
            $options = array(
                array(
                    'label' => 'Regular Price',
                    'value' => '0'
                ),
                array(
                    'label' => 'Sales Price',
                    'value' => '1'
                ),
                array(
                    'label' => 'Price Without Tax',
                    'value' => '2'
                )
            );
            ?>
            <tr valign="top">
                <th scope="row"><label for="map_price_with_vat">Product Price</label></th>
                <td>
                    <select id="map_price_with_vat"
                            name="<?php echo $this->getOptionInputName( 'map_price_with_vat' ); ?>">
                        <?php
                        foreach ($options as $option){
                            ?>
                            <option value="<?php echo esc_attr($option['value']); ?>"
                                <?php echo selected($option['value'], $this->get( 'map_price_with_vat' )); ?>>
                                <?php echo esc_html($option['label']); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <?php
            $options = array();

            $options[] = array(
                'label' => 'Categories',
                'value' => 'product_cat'
            );

            $options[] = array(
                'label' => 'Tags',
                'value' => 'product_tag'
            );

            foreach ( $attrTaxonomies as $taxonomies ) {
                $options[] = array(
                    'label' => $taxonomies->attribute_label,
                    'value' => $taxonomies->attribute_id
                );
            }
            ?>
            <tr valign="top">
                <th scope="row"><label for="map_category">Product Categories</label></th>
                <td>
                    <select id="map_category"
                            name="<?php echo $this->getOptionInputName( 'map_category' ); ?>">
                        <?php
                        foreach ($options as $option){
                            ?>
                            <option value="<?php echo esc_attr($option['value']); ?>"
                                <?php echo selected($option['value'], $this->get( 'map_category' )); ?>>
                                <?php echo esc_html($option['label']); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="map_category_tree">
                        Include full path to product category
                    </label>
                </th>
                <td>
                    <input type="hidden"
                           name="<?php echo $this->getOptionInputName( 'map_category_tree' ); ?>"
                           value="" />
                    <input type="checkbox"
                           id="map_category_tree"
                           name="<?php echo $this->getOptionInputName( 'map_category_tree' ); ?>"
                        <?php echo checked( 'on', $this->get( 'map_category_tree' ) ); ?> />
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="is_fashion_store">
                        This Store Contains Fashion Products
                    </label>
                </th>
                <td>
                    <input type="hidden"
                           name="<?php echo $this->getOptionInputName( 'is_fashion_store' ); ?>"
                           value="" />
                    <input type="checkbox"
                           id="is_fashion_store"
                           name="<?php echo $this->getOptionInputName( 'is_fashion_store' ); ?>"
                        <?php echo checked( 'on', $this->get( 'is_fashion_store' ) ); ?> />
                </td>
            </tr>

            <?php
            $options = array();

            foreach ( $attrTaxonomies as $taxonomies ) {
                $options[] = array(
                    'label' => $taxonomies->attribute_label,
                    'value' => $taxonomies->attribute_id
                );
            }
            ?>
            <tr valign="top">
                <th scope="row"><label for="map_size">Product Sizes</label></th>
                <td>
                    <select multiple
                            id="map_size"
                            name="<?php echo $this->getOptionInputName( 'map_size' ); ?>">
                        <?php
                        foreach ($options as $option){
                            ?>
                            <option value="<?php echo esc_attr($option['value']); ?>"
                                <?php echo selected($option['value'], $this->get( 'map_size' )); ?>>
                                <?php echo esc_html($option['label']); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="map_color">Product Colors</label></th>
                <td>
                    <select multiple
                            id="map_color"
                            name="<?php echo $this->getOptionInputName( 'map_color' ); ?>">
                        <?php
                        foreach ($options as $option){
                            ?>
                            <option value="<?php echo esc_attr($option['value']); ?>"
                                <?php echo selected($option['value'], $this->get( 'map_color' )); ?>>
                                <?php echo esc_html($option['label']); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="is_book_store">
                        This is a Bookstore
                    </label>
                </th>
                <td>
                    <input type="hidden"
                           name="<?php echo $this->getOptionInputName( 'is_book_store' ); ?>"
                           value="" />
                    <input type="checkbox"
                           id="is_book_store"
                           name="<?php echo $this->getOptionInputName( 'is_book_store' ); ?>"
                        <?php echo checked( 'on', $this->get( 'is_book_store' ) ); ?> />
                </td>
            </tr>

            <?php
            $options = array();

            $options[] = array(
                'label' => 'Use Product SKU',
                'value' => '0'
            );

            foreach ( $attrTaxonomies as $taxonomies ) {
                $options[] = array(
                    'label' => $taxonomies->attribute_label,
                    'value' => $taxonomies->attribute_id
                );
            }
            ?>
            <tr valign="top">
                <th scope="row"><label for="map_isbn">ISBN</label></th>
                <td>
                    <select id="map_isbn"
                            name="<?php echo $this->getOptionInputName( 'map_isbn' ); ?>">
                        <?php
                        foreach ($options as $option){
                            ?>
                            <option value="<?php echo esc_attr($option['value']); ?>"
                                <?php echo selected($option['value'], $this->get( 'map_isbn' )); ?>>
                                <?php echo esc_html($option['label']); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

        </table>
        <?php
    }

    public function logsBox(){
        echo DBHandler::getLogMarkUp(Skroutz::DB_LOG_NAME);
    }

    public function renderSettingsPage() {
        /* global vars */
        global $hook_suffix;

        /* enable add_meta_boxes function in this page. */
        do_action( 'add_meta_boxes', $hook_suffix );

        ?>

        <div class="wrap">
            <h2>Skroutz XML Feed Settings</h2>
            <div class="fx-settings-meta-box-wrap">
                <form method="post" action="options.php">
                    <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
                    <?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
                    <?php settings_fields( $this->optionsBaseName . '-settings-group' ); ?>
                    <?php do_settings_sections( $this->optionsBaseName . '-settings-group' ); ?>
                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
                            <div id="postbox-container-1" class="postbox-container">
                                <?php do_meta_boxes( $hook_suffix, 'side', null ); ?>
                            </div>
                            <div id="postbox-container-2" class="postbox-container">
                                <?php do_meta_boxes( $hook_suffix, 'main', null ); ?>
                            </div>
                        </div>
                        <br class="clear">
                    </div>
                </form>
            </div>
        </div>
        <?php
    }

    protected function getOptionInputName( $optionName ) {
        return esc_attr("{$this->optionsBaseName}[{$optionName}]");
    }

    /**
     * @return mixed
     */
    public function getPageHookSuffix() {
        return $this->pageHookSuffix;
    }
    public static function generatePassword( $length = 32 ) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $password = '';
        for ( $i = 0; $i < $length; $i ++ ) {
            $password .= substr( $chars, mt_rand( 0, strlen( $chars ) - 1 ), 1 );
        }

        return $password;
    }
}