<?php

namespace Pan\SkroutzXML;

use Pan\MenuPages\Fields\Range;
use Pan\MenuPages\Fields\Select;
use Pan\MenuPages\Fields\Select2;
use Pan\MenuPages\Fields\Text;
use Pan\MenuPages\PageElements\Components\Tab;
use Pan\MenuPages\PageElements\Components\TabForm;
use Pan\MenuPages\PageElements\Containers\Panel;
use Pan\MenuPages\PageElements\Containers\TabbedSettings;
use Pan\MenuPages\PageElements\Containers\Tabs;
use Pan\MenuPages\Pages\Page;
use Pan\MenuPages\Pages\SubPage;
use Pan\MenuPages\WpMenuPages;
use Pan\SkroutzXML\Logs\Logger;

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
        add_action( 'admin_enqueue_scripts', [ $this, 'actionAdminEnqueueScripts' ], false, true );
        add_action( 'wp_ajax_skz_generate_now', [ new Ajax(), 'generateNow' ] );

        add_action( 'woocommerce_after_register_taxonomy', array( $this, 'setupOptionsPage' ) );

        register_activation_hook( $this->pluginFile, [ $this, 'activation' ] );
    }

    public function actionAdminEnqueueScripts() {
        wp_enqueue_script(
            'skz_gen_now_js',
            plugins_url( 'assets/js/generate-now.min.js', $this->pluginFile ), [ 'jquery' ],
            false,
            true
        );
    }

    public function checkRequest() {
        $generateVar    = $this->options->get( 'xml_generate_var' );
        $generateVarVal = $this->options->get( 'xml_generate_var_value' );

        parse_str( $_SERVER["REQUEST_URI"] );

        if ( isset( $$generateVar ) && $$generateVar === $generateVarVal ) {
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
        delete_option( Logger::LOG_NAME );

        return true;
    }

    public function setupOptionsPage() {
        $wpMenuPages = new WpMenuPages( $this->pluginFile, $this->options );

        $menuPage = new Page($wpMenuPages, 'Skroutz XML Settings');

        $tabs = new TabbedSettings($menuPage, Page::EL_MAIN);

        $panelInfo = new Panel($menuPage, Page::EL_ASIDE);
        $panelGenerate = new Panel($menuPage, Page::EL_ASIDE);

        $tabGeneral = new TabForm($tabs, 'General Settings', true);
        $tabAdvanced = new TabForm($tabs, 'Advanced Settings');
        $tabMap = new TabForm($tabs, 'Map Fields Settings');
        $tabLog = new Tab($tabs, 'Log');

        $xmlLocationFld = new Text($tabGeneral, 'xml_location');
        $xmlFileNameFld = new Text($tabGeneral, 'xml_fileName');
        $xmlIntervalFld = new Range($tabGeneral, 'xml_interval');

        $xmlGenVar = new Text($tabAdvanced, 'xml_generate_var');
        $xmlGenVarVal = new Text($tabAdvanced, 'xml_generate_var_value');

        $productsIncFld = new Select2($tabGeneral, 'products_include');
        $availInStockFld = new Select($tabGeneral, 'avail_inStock');
        $availOutOfStockFld = new Select($tabGeneral, 'avail_outOfStock');
        $availBackOrdersFld = new Select($tabGeneral, 'avail_backorders');
    }
}