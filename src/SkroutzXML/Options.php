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
    const OPTIONS_NAME = 'skz__options';

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
     * @return $this
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
            'show_advanced'          => 0,
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
            /*********************
             * Custom fields
             ********************/
            'map_id'                 => 0,
            'map_name'               => 0,
            'map_name_append_sku'    => 0,
            'map_link'               => 0, // TODO Deprecated since 151228
            'map_image'              => 'full', // TODO Need translation for the new version
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

    public static function generatePassword( $length = 32 ) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $password = '';
        for ( $i = 0; $i < $length; $i ++ ) {
            $password .= substr( $chars, mt_rand( 0, strlen( $chars ) - 1 ), 1 );
        }

        return $password;
    }

    public function addMenuPages() {
        //create new top-level menu
        add_submenu_page( 'options-general.php',
                          'Skroutz XML Settings',
                          'Skroutz XML Settings',
                          'administrator',
                          $this->optionsBaseName . '-settings',
                          [ $this, 'renderSettingsPage' ] );

        //call register settings function
        add_action( 'admin_init', [ $this, 'registerSettings' ] );
    }

    public function registerSettings() {
        register_setting( $this->optionsBaseName . '-settings-group',
                          $this->optionsBaseName,
                          [ $this, 'validateSettings' ] );
    }

    public function validateSettings( $value ) {
        return $value;
    }

    public function renderSettingsPage() {
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
        ?>
        <div class="wrap">
            <h1>Skroutz XML Feed Settings</h1>

            <form method="post" action="options.php">
                <?php settings_fields( $this->optionsBaseName . '-settings-group' ); ?>
                <?php do_settings_sections( $this->optionsBaseName . '-settings-group' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="show_advanced">Show advanced options</label></th>
                        <td>
                            <input type="hidden"
                                   name="<?php echo $this->getOptionInputName( 'show_advanced' ); ?>"
                                   value="" />
                            <input type="checkbox"
                                   id="show_advanced"
                                   name="<?php echo $this->getOptionInputName( 'show_advanced' ); ?>"
                                <?php echo checked( 'on', $this->get( 'show_advanced' ) ); ?> />
                        </td>
                    </tr>

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
                            <select type="text"
                                   id="xml_interval"
                                   name="<?php echo $this->getOptionInputName( 'xml_interval' ); ?>">
                                <option value="daily" <?php echo selected('daily', $this->get( 'xml_interval' )); ?>>Daily</option>
                                <option value="twicedaily" <?php echo selected('twicedaily', $this->get( 'xml_interval' )); ?>>Twice Daily</option>
                                <option value="hourly" <?php echo selected('hourly', $this->get( 'xml_interval' )); ?>>Hourly</option>
                                <option value="every30m" <?php echo selected('every30m', $this->get( 'xml_interval' )); ?>>Every Thirty Minutes</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>

            </form>
        </div>
        <?php
    }

    protected function getOptionInputName( $optionName ) {
        return "{$this->optionsBaseName}[{$optionName}]";
    }
}