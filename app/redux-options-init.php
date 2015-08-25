<?php

/**
 * For full documentation, please visit: http://docs.reduxframework.com/
 * For a more extensive sample-config file, you may look at:
 * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
 */

/** @var  \Herbert\Framework\Application $container */


if ( ! class_exists( 'Redux' ) ) {
	return;
}

// This is your option name where all the Redux data is stored.
$opt_name = \SkroutzXMLFeed\Classes\Helper::getOptionsName();

/**
 * ---> SET ARGUMENTS
 * All the possible arguments for Redux.
 * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
 * */

$args = array(
	'opt_name'           => $opt_name,
	'use_cdn'            => true,
	'display_name'       => \SkroutzXMLFeed\Classes\Helper::get( 'Name' ),
	'display_version'    => \SkroutzXMLFeed\Classes\Helper::get( 'Version' ),
	'page_slug'          => \SkroutzXMLFeed\Classes\Helper::get( 'Slug' ),
	'page_title'         => \SkroutzXMLFeed\Classes\Helper::get( 'Name' ) . ' Options',
	'update_notice'      => true,
	'admin_bar'          => false,
	'menu_type'          => 'submenu',
	'page_parent'        => 'options-general.php',
	'menu_title'         => \SkroutzXMLFeed\Classes\Helper::get( 'Name' ),
	'allow_sub_menu'     => true,
	'customizer'         => false,
	'default_mark'       => '*',
	'default_show'       => false,
	'dev_mode'           => false,
	'hints'              => array(
		'icon_position' => 'right',
		'icon_size'     => 'normal',
		'tip_style'     => array(
			'color' => 'light',
		),
		'tip_position'  => array(
			'my' => 'top left',
			'at' => 'bottom right',
		),
		'tip_effect'    => array(
			'show' => array(
				'duration' => '500',
				'event'    => 'mouseover',
			),
			'hide' => array(
				'duration' => '500',
				'event'    => 'mouseleave unfocus',
			),
		),
	),
	'output'             => true,
	'output_tag'         => true,
	'settings_api'       => true,
	'cdn_check_time'     => '1440',
	'compiler'           => true,
	'page_permissions'   => 'manage_options',
	'save_defaults'      => true,
	'show_import_export' => true,
	'database'           => 'options',
	'transient_time'     => '3600',
	'network_sites'      => true,
);

// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
$args['share_icons'][] = array(
	'url'   => 'https://github.com/ReduxFramework/ReduxFramework',
	'title' => 'Visit us on GitHub',
	'icon'  => 'el el-github'
	//'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
);
$args['share_icons'][] = array(
	'url'   => 'http://twitter.com/reduxframework',
	'title' => 'Follow us on Twitter',
	'icon'  => 'el el-twitter'
);
$args['share_icons'][] = array(
	'url'   => 'http://www.linkedin.com/company/redux-framework',
	'title' => 'Find us on LinkedIn',
	'icon'  => 'el el-linkedin'
);

Redux::setArgs( $opt_name, $args );

/*
 * ---> END ARGUMENTS
 */

/*
 * ---> START HELP TABS
 */

$tabs = array(
	array(
		'id'      => 'redux-help-tab-1',
		'title'   => __( 'Theme Information 1', 'admin_folder' ),
		'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'admin_folder' )
	),
	array(
		'id'      => 'redux-help-tab-2',
		'title'   => __( 'Theme Information 2', 'admin_folder' ),
		'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'admin_folder' )
	)
);
Redux::setHelpTab( $opt_name, $tabs );

// Set the help sidebar
$content = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'admin_folder' );
Redux::setHelpSidebar( $opt_name, $content );


/*
 * <--- END HELP TABS
 */


/*
 *
 * ---> START SECTIONS
 *
 */


add_action('woocommerce_loaded', 'setSkroutzOptionSections');
function setSkroutzOptionSections() {
	$opt_name = \SkroutzXMLFeed\Classes\Helper::getOptionsName();

	$availOptions = array();
	foreach ( \SkroutzXMLFeed\Classes\Options::$availOptions as $value => $label ) {
		$availOptions[ (string) $value ] = $label;
	}

	$availOptionsDoNotInclude = $availOptions;

	$availOptionsDoNotInclude[ count( $availOptions ) ] = __( 'Do not Include', 'redux-framework-demo' );

	Redux::setSection( $opt_name, array(
		'title'            => __( 'General Options', 'redux-framework-demo' ),
		'id'               => 'general-options',
		'icon'             => 'el el-edit',
	) );

	Redux::setSection( $opt_name, array(
		'title'      => __( 'Basic Options', 'redux-framework-demo' ),
		'id'         => 'basic-options',
		'desc'       => __( '', 'redux-framework-demo' ),
		'icon'       => 'el el-home',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'            => 'xml_interval',
				'type'          => 'slider',
				'title'         => __( 'XML File Generation Interval', 'redux-framework-demo' ),
				'subtitle'      => __( '', 'redux-framework-demo' ),
				'desc'          => __( 'Choose the interval of XML file generation', 'redux-framework-demo' ),
				"default"       => 6,
				"min"           => 0,
				"step"          => 1,
				"max"           => 24,
				'display_value' => 'Hours'
			),
			array(
				'id'       => 'avail_inStock',
				'type'     => 'select',
				'title'    => __( 'Product availability when item is in stock', 'redux-framework-demo' ),
				'subtitle' => __( '', 'redux-framework-demo' ),
				'desc'     => __( 'Choose the availability of product when this is in stock', 'redux-framework-demo' ),
				'options'  => $availOptions,
				'default'  => '0',
			),
			array(
				'id'       => 'avail_outOfStock',
				'type'     => 'select',
				'title'    => __( 'Product availability when item is out of stock', 'redux-framework-demo' ),
				'subtitle' => __( '', 'redux-framework-demo' ),
				'desc'     => __( 'Choose the availability of product when this is out of stock', 'redux-framework-demo' ),
				'options'  => $availOptionsDoNotInclude,
				'default'  => (string) ( count( $availOptionsDoNotInclude ) - 1 ),
			),
			array(
				'id'       => 'avail_backorders',
				'type'     => 'select',
				'title'    => __( 'Product availability when item is out of stock and backorders are allowed', 'redux-framework-demo' ),
				'subtitle' => __( '', 'redux-framework-demo' ),
				'desc'     => __( 'Choose the availability of product when this is out of stock and backorders are allowed', 'redux-framework-demo' ),
				'options'  => $availOptionsDoNotInclude,
				'default'  => (string) ( count( $availOptionsDoNotInclude ) - 1 ),
			),
		)
	) );

	Redux::setSection( $opt_name, array(
		'title'      => __( 'Advanced Options', 'redux-framework-demo' ),
		'id'         => 'advanced-options',
		'desc'       => __( '', 'redux-framework-demo' ),
		'icon'       => 'el el-home',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'xml_generate_var',
				'type'     => 'text',
				'title'    => __( 'XML Request Generate Variable', 'redux-framework-demo' ),
				'subtitle' => __( '', 'redux-framework-demo' ),
				'desc'     => __( 'Request Variable relative to WordPress URL', 'redux-framework-demo' ),
				'default'  => 'skroutz',
				'validate' => 'not_empty',
			),
			array(
				'id'       => 'xml_generate_var_value',
				'type'     => 'text',
				'title'    => __( 'XML Request Generate Variable Value', 'redux-framework-demo' ),
				'subtitle' => __( '', 'redux-framework-demo' ),
				'desc'     => __( 'Request Variable Value', 'redux-framework-demo' ),
				'default'  => uniqid() . uniqid(),
				'validate' => 'not_empty',
			),
			array(
				'id'       => 'xml_fileName',
				'type'     => 'text',
				'title'    => __( 'Cached XML Filename', 'redux-framework-demo' ),
				'subtitle' => __( '', 'redux-framework-demo' ),
				'desc'     => __( 'The name of the generated XML file', 'redux-framework-demo' ),
				'default'  => 'skroutz.xml',
				'validate' => 'not_empty',
			),
			array(
				'id'       => 'xml_location',
				'type'     => 'text',
				'title'    => __( 'XML File Location', 'redux-framework-demo' ),
				'subtitle' => __( '', 'redux-framework-demo' ),
				'desc'     => __( 'Enter the location you want the file to be saved, relative to WordPress install dir' ),
				'default'  => '',
				'validate_callback' => 'validateLocalPath',
			),
		)
	) );

	if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
		$attrTaxonomies = wc_get_attribute_taxonomies();

		$fields = array();

		$options = array(
			__( 'Use Product SKU', 'redux-framework-demo' ),
			__( 'Use Product ID', 'redux-framework-demo' )
		);

		$fields[] = array(
			'id'       => 'map_id',
			'type'     => 'select',
			'title'    => __( 'Product Link', 'redux-framework-demo' ),
			'subtitle' => __( '', 'redux-framework-demo' ),
			'desc'     => __( 'Choose from where the product id should be taken', 'redux-framework-demo' ),
			'options'  => $options,
			'default'  => 1,
		);

		$options = array(
			'product_cat' => __( 'Use Product Categories' ),
			'product_tag' => __( 'Use Product Tags' ),
		);

		global $wp_taxonomies;

		foreach ( $attrTaxonomies as $taxonomies ) {
			$options[ $taxonomies->attribute_id ] = $taxonomies->attribute_label;
		}

		if ( \SkroutzXMLFeed\Classes\Skroutz::hasBrandsPlugin() && ( $brandsTax = \SkroutzXMLFeed\Classes\Skroutz::getBrandsPluginTaxonomy() ) ) {
			$options[ $brandsTax->name ] = 'Use WooCommerce Brands Plugin';
		}

		$fields[] = array(
			'id'       => 'map_manufacturer',
			'type'     => 'select',
			'title'    => __( 'Product Manufacturer Field', 'redux-framework-demo' ),
			'subtitle' => __( '', 'redux-framework-demo' ),
			'desc'     => __( 'Choose from where the product manufacturer should be taken', 'redux-framework-demo' ),
			'options'  => $options,
			'default'  => 'product_cat',
		);

		$options = array(
			__( 'Use Product SKU', 'redux-framework-demo' ),
			__( 'Use Product ID', 'redux-framework-demo' )
		);

		foreach ( $attrTaxonomies as $taxonomies ) {
			$options[ $taxonomies->attribute_id ] = $taxonomies->attribute_label;
		}

		$fields[] = array(
			'id'       => 'map_mpn',
			'type'     => 'select',
			'title'    => __( 'Product Manufacturer SKU', 'redux-framework-demo' ),
			'subtitle' => __( '', 'redux-framework-demo' ),
			'desc'     => __( 'Choose from where the product manufacturer SKU should be taken', 'redux-framework-demo' ),
			'options'  => $options,
			'default'  => 0,
		);

		$options = array();
		$options[] = array( __( 'Use Product Name', 'redux-framework-demo' ) );

		foreach ( $attrTaxonomies as $taxonomies ) {
			$options[$taxonomies->attribute_id] =  $taxonomies->attribute_label;
		}

		$fields[] = array(
			'id'       => 'map_name',
			'type'     => 'select',
			'title'    => __( 'Product Name', 'redux-framework-demo' ),
			'subtitle' => __( '', 'redux-framework-demo' ),
			'desc'     => __( 'Which field should be used for generating the product name', 'redux-framework-demo' ),
			'options'  => $options,
			'default'  => 0,
		);

		$fields[] = array(
			'id'       => 'map_name_append_sku',
			'type'     => 'switch',
			'title'    => __('Append SKU to Product Name', 'redux-framework-demo'),
			'subtitle' => __('', 'redux-framework-demo'),
			'desc'     => __( 'If you check this the product SKU will be appended to product name. If no SKU is set then product ID will be used', 'redux-framework-demo' ),
			'default'  => false,
		);

		$options = array(
			__( 'Regular Price', 'redux-framework-demo' ),
			__( 'Sales Price', 'redux-framework-demo' ),
			__( 'Price Without Tax', 'redux-framework-demo' )
		);

		$fields[] = array(
			'id'       => 'map_price_with_vat',
			'type'     => 'select',
			'title'    => __( 'Product Price', 'redux-framework-demo' ),
			'subtitle' => __( '', 'redux-framework-demo' ),
			'desc'     => __( 'Choose the price you want to use in XML', 'redux-framework-demo' ),
			'options'  => $options,
			'default'  => 1,
		);

		$options = array(
			'product_cat' => __( 'Use Product Categories' ),
			'product_tag' => __( 'Use Product Tags' ),
		);

		foreach ( $attrTaxonomies as $taxonomies ) {
			$options[$taxonomies->attribute_id] = $taxonomies->attribute_label;
		}

		$fields[] = array(
			'id'       => 'map_category',
			'type'     => 'select',
			'title'    => __( 'Product Categories', 'redux-framework-demo' ),
			'subtitle' => __( '', 'redux-framework-demo' ),
			'desc'     => __( 'Which taxonomy describes best your products', 'redux-framework-demo' ),
			'options'  => $options,
			'default'  => 0,
		);

		$fields[] = array(
			'id'       => 'is_fashion_store',
			'type'     => 'switch',
			'title'    => __('This Store Contains Fashion Products', 'redux-framework-demo'),
			'subtitle' => __('', 'redux-framework-demo'),
			'desc'     => __( 'Check this if your store has fashion products', 'redux-framework-demo' ),
			'default'  => false,
		);

		$options = array();

		foreach ( $attrTaxonomies as $taxonomies ) {
			$options[$taxonomies->attribute_id] = $taxonomies->attribute_label;
		}

		$fields[] = array(
			'id'       => 'map_size',
			'type'     => 'select',
			'multi'    => true,
			'title'    => __( 'Product Sizes', 'redux-framework-demo' ),
			'subtitle' => __( '', 'redux-framework-demo' ),
			'desc'     => __( 'Select the attribute that describes product sizes' ),
			'options'  => $options,
			'default'  => [],
			'required' => array('is_fashion_store','=',true)
		);

		$fields[] = array(
			'id'       => 'map_color',
			'type'     => 'select',
			'multi'    => true,
			'title'    => __( 'Product Colors', 'redux-framework-demo' ),
			'subtitle' => __( '', 'redux-framework-demo' ),
			'desc'     => __( 'Select the attribute that describes product colors' ),
			'options'  => $options,
			'default'  => [],
			'required' => array('is_fashion_store','=',true)
		);

		$fields[] = array(
			'id'       => 'is_book_store',
			'type'     => 'switch',
			'title'    => __('This is a Bookstore', 'redux-framework-demo'),
			'subtitle' => __('', 'redux-framework-demo'),
			'desc'     => __( 'Check this if you are selling books. In this case you must set the ISBN of each book', 'redux-framework-demo' ),
			'default'  => false,
		);

		$options = array( __( 'Use Product SKU', 'redux-framework-demo' ) );

		foreach ( $attrTaxonomies as $taxonomies ) {
			$options[$taxonomies->attribute_id] = $taxonomies->attribute_label;
		}

		$fields[] = array(
			'id'       => 'map_isbn',
			'type'     => 'select',
			'title'    => __( 'ISBN', 'redux-framework-demo' ),
			'subtitle' => __( '', 'redux-framework-demo' ),
			'desc'     => __( 'Choose the field that contains books ISBN' ),
			'options'  => $options,
			'default'  => 0,
			'required' => array('is_book_store','=',true)
		);

	} else {
		$fields = array(
			array(
				'id'       => 'opt-raw',
				'type'     => 'raw',
				'title'    => __('', 'redux-framework-demo'),
				'subtitle' => __('', 'redux-framework-demo'),
				'desc'     => __('', 'redux-framework-demo'),
				'content'  => file_get_contents(dirname(__FILE__) . '/../readme.txt'),
			)
		);
	}

	Redux::setSection( $opt_name, array(
		'title'  => __( 'Map Options', 'redux-framework-demo' ),
		'desc'   => __( 'For full documentation on this field, visit: ', 'redux-framework-demo' ) . '<a href="http://docs.reduxframework.com/core/fields/textarea/" target="_blank">http://docs.reduxframework.com/core/fields/textarea/</a>',
		'id'     => 'map-options',
		'icon'   => 'el el-map-marker',
		'fields' => $fields
	) );

	Redux::setSection( $opt_name, array(
		'title'      => __( 'XML Generation Log', 'redux-framework-demo' ),
		'desc'       => __( 'For full documentation on this field, visit: ', 'redux-framework-demo' ) . '<a href="http://docs.reduxframework.com/core/fields/textarea/" target="_blank">http://docs.reduxframework.com/core/fields/textarea/</a>',
		'id'         => 'log-section',
		'fields'     => array(
			array(
				'id'       => 'opt-raw',
				'type'     => 'raw',
				'title'    => __('Raw output', 'redux-framework-demo'),
				'subtitle' => __('Subtitle text goes here.', 'redux-framework-demo'),
				'desc'     => __('This is the description field for additional info.', 'redux-framework-demo'),
				'content'  => file_get_contents(dirname(__FILE__) . '/../readme.txt')
			),
		)
	) );

	Redux::setSection( $opt_name, array(
		'title'      => __( 'Info', 'redux-framework-demo' ),
		'desc'       => __( 'For full documentation on this field, visit: ', 'redux-framework-demo' ) . '<a href="http://docs.reduxframework.com/core/fields/textarea/" target="_blank">http://docs.reduxframework.com/core/fields/textarea/</a>',
		'id'         => 'info-section',
		'fields'     => array(
			array(
				'id'       => 'opt-raw',
				'type'     => 'raw',
				'title'    => __('Raw output', 'redux-framework-demo'),
				'subtitle' => __('Subtitle text goes here.', 'redux-framework-demo'),
				'desc'     => __('This is the description field for additional info.', 'redux-framework-demo'),
				'content'  => file_get_contents(dirname(__FILE__) . '/../readme.txt')
			),
		)
	) );
}

/*
 * <--- END SECTIONS
 */

if (!function_exists('validateLocalPath')) {

	function validateLocalPath( $field, $value, $existing_value ) {
		$return = array();

		$valueTrimed = ltrim($value, DIRECTORY_SEPARATOR);
		$path = realpath(ABSPATH.$valueTrimed);

		if(is_numeric(strpos($path, rtrim(ABSPATH, '/')))){
			$value = $valueTrimed;
		} else {
			$value = $existing_value;
			$field['msg'] = 'Directory don\'t exists or/and it\'s outside of WordPress installation folder';
			$return['error'] = $field;
		}

		$return['value'] = $value;

		return $return;
	}
}