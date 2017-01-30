<?php

namespace Pan\SkroutzXML;

if ( ! defined( 'WPINC' ) ) {
    die;
}

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

        add_action( 'wp_ajax_skz-gen-now-action', [ new Ajax(), 'generateNow' ] );

        add_action( 'wp_dashboard_setup', [ $this, 'addDashboardWidget' ] );

        add_action( 'admin_menu', [ $this->options, 'addMenuPages' ] );

        register_activation_hook( $this->pluginFile, [ $this, 'activation' ] );
        register_uninstall_hook( $this->pluginFile, [ '\\Pan\\SkroutzXML\\Initializer', 'uninstall' ] );

        add_filter( 'screen_layout_columns', [ $this, 'screenLayoutColumnHook' ], 10, 2 );

        add_action( 'add_meta_boxes', [ $this, 'addMetaboxesHook' ] );
    }

    public function screenLayoutColumnHook( $columns, $screen ) {
        $page_hook_id = $this->options->getPageId();
        if ( $screen == $page_hook_id ) {
            $columns[ $page_hook_id ] = 2;
        }

        return $columns;
    }

    public function addMetaboxesHook() {
        $page_hook_id = $this->options->getPageHookSuffix();

        add_meta_box(
            'save_box',
            'Save Options',
            [ $this->options, 'saveBox' ],
            $page_hook_id,
            'side',
            'high'
        );

        add_meta_box(
            'info_box',
            'Info',
            [ $this->options, 'infoBox' ],
            $page_hook_id,
            'side',
            'high'
        );

        add_meta_box(
            'gen_now',
            'Generate Now',
            [ $this->options, 'genNowBox' ],
            $page_hook_id,
            'side'
        );

        add_meta_box(
            'general_options',
            'General Options',
            [ $this->options, 'generalOptionsBox' ],
            $page_hook_id,
            'main'
        );

        add_meta_box(
            'map_options',
            'Map Fields',
            [ $this->options, 'mapOptionsBox' ],
            $page_hook_id,
            'main'
        );

        add_meta_box(
            'logs',
            'Last Generation XML Log',
            [ $this->options, 'logsBox' ],
            $page_hook_id,
            'main'
        );
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

    public function actionAdminEnqueueScripts( $hook_suffix ) {
        $page_hook_id = $this->options->getPageHookSuffix();
        if ( $hook_suffix == $page_hook_id ) {
            wp_enqueue_script( 'common' );
            wp_enqueue_script( 'wp-lists' );
            wp_enqueue_script( 'postbox' );

            wp_enqueue_script(
                'skz__js',
                plugins_url( 'assets/js/scripts.min.js', $this->pluginFile ),
                [ 'jquery' ],
                false,
                true
            );

            wp_localize_script( 'skz__js', 'SKZ', [ 'pageHookSuffix' => $this->options->getPageHookSuffix() ] );

            wp_enqueue_style(
                'skz_gen_now_css',
                plugins_url( 'assets/css/style.min.css', $this->pluginFile ),
                [],
                true
            );
        }
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
                case '0':
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

    public static function getFileInfoMarkUp() {
        $skz      = new Skroutz();
        $fileInfo = $skz->getXmlObj()->getFileInfo();

        $genUrl = Options::getInstance()->getGenerateXmlUrl();

        $content = '<div class="row">';
        if ( empty( $fileInfo ) ) {
            $content
                .= '<p class="alert alert-danger">
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