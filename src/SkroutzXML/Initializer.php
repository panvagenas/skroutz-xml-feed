<?php

namespace Pan\SkroutzXML;

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
        $options = Options::getInstance();

        delete_option( $options->getOptionsName() );
        delete_option( $options->getLogName() );

        return true;
    }
}