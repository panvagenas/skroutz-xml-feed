<?php

namespace Pan\SkroutzXML;

if ( ! defined( 'WPINC' ) ) {
    die;
}

use Pan\MenuPages\Fields as Fields;
use Pan\MenuPages\PageElements\Components as Components;
use Pan\MenuPages\PageElements\Containers as Containers;
use Pan\MenuPages\Pages\Page;
use Pan\MenuPages\Pages\SubPage;
use Pan\MenuPages\Scripts\Ifc\IfcScripts;
use Pan\MenuPages\WpMenuPages;
use Pan\XmlGenerator\Logger\Handlers\DBHandler;
use Respect\Validation\Validator;

class Initializer {
    protected $pluginFile;
    /**
     * @var Options
     */
    protected $options;

    public function __construct( $pluginFile ) {
        $this->pluginFile = $pluginFile;

        $this->options = Options::getInstance();

        add_action( 'init', [ $this, 'checkRequest' ] );
//        add_action( 'admin_enqueue_scripts', [ $this, 'actionAdminEnqueueScripts' ], false, true );
        add_action("MenuPages\\Scripts\\Script::printScripts@{$this->options->getOptionsBaseName()}", [ $this, 'actionAdminEnqueueScripts' ], false, true );
        add_action( 'wp_ajax_skz_generate_now', [ new Ajax(), 'generateNow' ] );

        add_action( 'wp_loaded', array( $this, 'setupOptionsPage' ) );

        add_action( 'wp_dashboard_setup', [ $this, 'addDashboardWidget' ] );

        register_activation_hook( $this->pluginFile, [ $this, 'activation' ] );
        register_uninstall_hook( $this->pluginFile, [ '\\Pan\\SkroutzXML\\Initializer', 'uninstall' ] );
    }

    public function addDashboardWidget() {
        wp_add_dashboard_widget(
            'skz-xml-info',
            'Skroutz XML',
            [ $this, 'dashboardWidgetMarkUp' ]
        );
    }

    public function dashboardWidgetMarkUp() {
        echo __METHOD__;
    }

    public function actionAdminEnqueueScripts() {
        wp_enqueue_script(
            'skz_gen_now_js',
            plugins_url( 'assets/js/generate-now.min.js', $this->pluginFile ),
            [ 'jquery', IfcScripts::CORE_JS_SLUG ],
            false,
            true
        );
    }

    public function checkRequest() {
        $generateVar    = $this->options->get( 'xml_generate_var' );
        $generateVarVal = $this->options->get( 'xml_generate_var_value' );

        if ( isset( $_REQUEST[ $generateVar ] ) && $_REQUEST[ $generateVar ] === $generateVarVal ) {
            add_action( 'wp_loaded', [ new Skroutz(), 'generateAndPrint' ], PHP_INT_MAX );
        }
    }

    public function activation() {
        $xmlInterval = $this->options->get( 'xml_interval' );

        if ( ! is_numeric( $xmlInterval ) ) {
            switch ( $xmlInterval ) {
                case 'every30m':
                case 'hourly':
                    $intervalInHours = 1;
                    break;
                case 'twicedaily':
                    $intervalInHours = 12;
                    break;
                case 'daily':
                default:
                    $intervalInHours = 24;
                    break;
            }
            $this->options->set( 'xml_interval', $intervalInHours );
        }

        return true;
    }

    public static function uninstall() {
        delete_option( Options::OPTIONS_NAME );
        delete_option( Skroutz::DB_LOG_NAME );

        return true;
    }

    public function setupOptionsPage() {
        if ( ! is_admin() ) {
            return;
        }
        $wpMenuPages = new WpMenuPages( $this->pluginFile, $this->options );

        $menuPage = new SubPage( $wpMenuPages, SubPage::PARENT_SETTINGS, 'Skroutz XML Settings' );

        $availOptions = [ ];
        foreach ( Options::$availOptions as $value => $label ) {
            $availOptions[ $label ] = (string) $value;
        }

        $availOptionsDoNotInclude                   = $availOptions;
        $availOptionsDoNotInclude['Do not Include'] = count( $availOptions );

        $attrTaxonomies = [ ];
        foreach ( wc_get_attribute_taxonomies() as $atrTax ) {
            $attrTaxonomies[ $atrTax->attribute_label ] = $atrTax->attribute_id;
        }

        $tabs = new Containers\CnrTabbedSettings( $menuPage, Page::POSITION_MAIN );

        if(!extension_loaded('mbstring')){
            $alert = new Components\CmpAlert($tabs, Containers\Abs\AbsCnrComponents::CNR_HEAD);
            $alert->setContent('The PHP extension "mbstring" must be enabled or this plugin will not work properly.');
            $alert->setType(Components\CmpAlert::TYPE_DANGER);
            $alert->setDismissible(false);
            $menuPage->addAlert($alert);
        }

        $colInfo     = new Containers\CnrCollapsible( $menuPage, Page::POSITION_ASIDE, 'XML File Information' );
        $panelGenNow = new Containers\CnrCollapsible( $menuPage, Page::POSITION_ASIDE, 'Generate Now' );

        $tabGeneral  = new Components\CmpTabForm( $tabs, 'General Settings', true );
        $tabAdvanced = new Components\CmpTabForm( $tabs, 'Advanced Settings' );
        $tabMap      = new Components\CmpTabForm( $tabs, 'Map Fields Settings' );
        $tabLog      = new Components\CmpTab( $tabs, 'Log' );

        $xmlLocationFld = new Fields\Text( $tabGeneral, 'xml_location' );
        $xmlLocationFld->setLabel( 'Cached XML File Location' )
                       ->attachValidator( Validator::stringType() );

        $xmlFileNameFld = new Fields\Text( $tabGeneral, 'xml_fileName' );
        $xmlFileNameFld->setLabel( 'XML Filename' )
                       ->attachValidator( Validator::stringType()->notEmpty() );

        $xmlIntervalFld = new Fields\Range( $tabGeneral, 'xml_interval' );
        $xmlIntervalFld->setLabel( 'XML File Generation Interval' )
                       ->setMin( 1 )
                       ->setMax( 24 );

        $xmlGenVarFld = new Fields\Text( $tabAdvanced, 'xml_generate_var' );
        $xmlGenVarFld->setLabel( 'XML Generation Request Variable' )
                     ->attachValidator( Validator::stringType()->length( 1 )->alnum() );

        $xmlGenVarValFld = new Fields\Text( $tabAdvanced, 'xml_generate_var_value' );
        $xmlGenVarValFld->setLabel( 'XML Generation Request Variable Value' )
                        ->attachValidator( Validator::stringType()->length( 8 )->alnum() );

        // TODO Are we gonna use this? Better use a procuct_exclude
        $productsIncFld = new Fields\Taxonomies( $tabGeneral, 'products_include', 'product' );
        $productsIncFld->setLabel( '' )
                       ->setMultiple( true )
                       ->validate( Validator::arrayType() );

        $availInStockFld = new Fields\Select( $tabGeneral, 'avail_inStock' );
        $availInStockFld->setLabel( 'Product availability when item is in stock' )
                        ->setOptions( $availOptions )
                        ->attachValidator( Validator::numeric()
                                                    ->min( min( $availOptions ) )
                                                    ->max( max( $availOptions ) ) );

        $availOutOfStockFld = new Fields\Select( $tabGeneral, 'avail_outOfStock' );
        $availOutOfStockFld->setLabel( 'Product availability when item is out of stock' )
                           ->setOptions( $availOptionsDoNotInclude )
                           ->attachValidator( Validator::numeric()
                                                       ->min( min( $availOptionsDoNotInclude ) )
                                                       ->max( max( $availOptionsDoNotInclude ) ) );

        $availBackOrdersFld = new Fields\Select( $tabGeneral, 'avail_backorders' );
        $availBackOrdersFld->setLabel( 'Product availability when item is out of stock and backorders are allowed' )
                           ->setOptions( $availOptionsDoNotInclude )
                           ->attachValidator( Validator::numeric()
                                                       ->min( min( $availOptionsDoNotInclude ) )
                                                       ->max( max( $availOptionsDoNotInclude ) ) );

        $mapIdFld = new Fields\SwitchField( $tabMap, 'map_id' );
        $mapIdFld->setLabel( 'Product ID' )
                 ->setOptions( [ 'Use Product SKU' => 0, 'Use Product ID' => 1 ] )
                 ->attachValidator( Validator::numeric()->min( 0 )->max( 1 ) );


        $options = array_merge(
            [ 'Product Categories' => 'product_cat', 'Product Tags' => 'product_tag' ],
            $attrTaxonomies
        );

        $mapProductCatFld = new Fields\Select2( $tabMap, 'map_manufacturer' );
        $mapProductCatFld->setLabel( 'Product Manufacturer Field' )
                         ->setOptions( $options )
                         ->attachValidator( Validator::in( $options ) );

        $options = array_merge(
            [ 'Use Product SKU' => 0 ],
            $attrTaxonomies
        );

        $mapMpnFld = new Fields\Select2( $tabMap, 'map_mpn' );
        $mapMpnFld->setLabel( 'Product Manufacturer SKU' )
                  ->setOptions( $options )
                  ->attachValidator( Validator::in( $options ) );

        $options = array_merge(
            [ 'Use Product Name' => 0 ],
            $attrTaxonomies
        );

        $mapMpnName = new Fields\Select2( $tabMap, 'map_name' );
        $mapMpnName->setLabel( 'Product Name' )
                   ->setOptions( $options )
                   ->attachValidator( Validator::in( $options ) );

        $mapAppendSkuFld = new Fields\SwitchField( $tabMap, 'map_name_append_sku' );
        $mapAppendSkuFld->setLabel( 'Append SKU to Product Name' )
                        ->attachValidator( Validator::numeric()->between( 0, 1 ) );

        $options = [
            'Thumbnail' => 'thumbnail',
            'Medium'    => 'medium',
            'Large'     => 'large',
            'Full'      => 'full',
        ];

        $mapProdImgFld = new Fields\Select2( $tabMap, 'map_image' );
        $mapProdImgFld->setLabel( 'Product Image' )
                      ->setOptions( $options )
                      ->validate( Validator::in( $options ) );

        $options = [
            'Regular Price'     => 0,
            'Sales Price'       => 1,
            'Price Without Tax' => 2,
        ];

        $mapPriceFld = new Fields\Select2( $tabMap, 'map_price_with_vat' );
        $mapPriceFld->setLabel( 'Product Price' )
                    ->setOptions( $options )
                    ->attachValidator( Validator::in( $options ) );

        $options = array_merge(
            [ 'Product Categories' => 'product_cat', 'Product Tags' => 'product_tag' ],
            $attrTaxonomies
        );

        $mapCatFld = new Fields\Select2( $tabMap, 'map_category' );
        $mapCatFld->setLabel( 'Product Categories' )
                  ->setOptions( $options )
                  ->attachValidator( Validator::in( $options ) );

        $mapAppendSkuFld = new Fields\SwitchField( $tabMap, 'map_category_tree' );
        $mapAppendSkuFld->setLabel( 'Include full path to product category' )
                        ->attachValidator( Validator::numeric()->between( 0, 1 ) );

        $mapAppendSkuFld = new Fields\SwitchField( $tabMap, 'is_fashion_store' );
        $mapAppendSkuFld->setLabel( 'This Store Contains Fashion Products' )
                        ->attachValidator( Validator::numeric()->between( 0, 1 ) );

        $mapSizeFld = new Fields\Select2( $tabMap, 'map_size' );
        $mapSizeFld->setLabel( 'Product Sizes' )
                   ->setOptions( $attrTaxonomies )
                   ->setMultiple( true )
                   ->attachValidator( Validator::in( $attrTaxonomies ) );

        $mapSizeFld = new Fields\Select2( $tabMap, 'map_color' );
        $mapSizeFld->setLabel( 'Product Colors' )
                   ->setOptions( $attrTaxonomies )
                   ->setMultiple( true )
                   ->setSelect2option( 'allowClear', true )
                   ->attachValidator( Validator::in( $attrTaxonomies ) );

        $mapAppendSkuFld = new Fields\SwitchField( $tabMap, 'is_book_store' );
        $mapAppendSkuFld->setLabel( 'This is a Bookstore' )
                        ->attachValidator( Validator::numeric()->between( 0, 1 ) );

        $options = array_merge(
            [ 'Use Product SKU' => 0 ],
            $attrTaxonomies
        );

        $mapSizeFld = new Fields\Select2( $tabMap, 'map_isbn' );
        $mapSizeFld->setLabel( 'ISBN' )
                   ->setOptions( $attrTaxonomies )
                   ->attachValidator( Validator::in( $options ) );

        $tabLog->setContent( DBHandler::getLogMarkUp( Skroutz::DB_LOG_NAME ) );

        $cmpGenNow    = new Components\CmpFields( $panelGenNow );
        $skzGenUrlFld = new Fields\Raw( $cmpGenNow );
        $skzGenUrlFld->setContent(
            '<p class="alert alert-info">Skroutz XML Generation URL: ' . $this->options->getGenerateXmlUrl() . '</p>'
        );
        $genNowBtn = new Fields\Button( $cmpGenNow, 'Generate XML Now' );
        $genNowBtn->setClass( 'btn btn-success col-md-9 gen-now-button' );
        new Fields\Nonce( $cmpGenNow, 'skz_generate_now', 'nonce' );

        $cmpFields = new Components\CmpFields( $colInfo );
        $raw       = new Fields\Raw( $cmpFields );
        $raw->setClass( $raw->getClass() . ' info-panel' );
        $raw->setContent( self::getFileInfoMarkUp() );

        $panelDonate = new Containers\CnrPanelComponents( $menuPage, $menuPage::POSITION_ASIDE );
        $panelDonate->setTitle( 'Support this Plugin' );

        $donateForm   = new Components\CmpForm( $panelDonate );
        $donateSelect = new Fields\Select( $donateForm, 'donate' );
        $donateSelect->setOptions( [ '1' => 0, '2' => 1 ] );
    }

    public static function getFileInfoMarkUp() {
        $skz      = new Skroutz();
        $fileInfo = $skz->getXmlObj()->getFileInfo();

        $genUrl = Options::getInstance()->getGenerateXmlUrl();

        $content = '<div class="row">';
        if ( empty( $fileInfo ) ) {
            $content .= '<p class="alert alert-danger">
                        File not generated yet. Please use the <i>Generate XML Now</i>
                        button to generate a new XML file</p>';
        } else {
            $content .= '<ul class="list-group">';
            foreach ( $fileInfo as $item ) {
                $content .= '<li class="list-group-item">';
                $content .= $item['label'] . ': <strong>' . $item['value'] . '</strong>';
                $content .= '</li>';
            }
            $content .= '</ul>';

            $content .= '<a class="btn btn-primary btn-sm" href="'
                        . home_url( Options::getInstance()->getXmlRelLocationOption() )
                        . '" target="_blank" role="button">';
            $content .= 'Open Cached File';
            $content .= '</a>';
            $content .= '<a class="btn btn-primary btn-sm copy-gen-url pull-right" href="'
                        . $genUrl
                        . '" target="_blank" role="button">';
            $content .= 'Open Generate URL';
            $content .= '</a>';
        }
        $content .= '</div>';

        return $content;
    }
}