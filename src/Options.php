<?php
/**
 * Project: skroutz-pan
 * File: Options.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 4/9/2015
 * Time: 12:16 πμ
 * Since: TODO ${VERSION}
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace Skroutz;


use Monolog\Logger;
use PanWPCore\Redux;
use RandomLib\Generator;

class Options extends \PanWPCore\Options {
	/**
	 * @var string
	 */
	protected $optName = 'skroutz__options';
	/**
	 * @var array
	 */
	public static $availOptions = array(
		'Άμεση παραλαβή / Παράδοση σε 1-3 ημέρες ',
		'Παράδοση σε 1-3 ημέρες',
		'Παραλαβή από το κατάστημα ή Παράδοση, σε 1-3 ημέρες',
		'Παραλαβή από το κατάστημα ή Παράδοση, σε 4-10 ημέρες',
		'Παράδοση σε 4-10 ημέρες',
		'Κατόπιν παραγγελίας, παραλαβή ή παράδοση έως 30 ημέρες',
	);
	/**
	 * @var array
	 */
	protected $reduxArgs = array(
		'update_notice' => false,
		'admin_bar'     => false,
		'menu_type'     => 'submenu',
		'page_parent'   => 'options-general.php',
		'share_icons'   => array(
			array(
				'url'   => 'https://github.com/panvagenas',
				'title' => 'Visit us on GitHub',
				'icon'  => 'el el-github'
			),
			array(
				'url'   => 'http://twitter.com/panVagenas',
				'title' => 'Follow us on Twitter',
				'icon'  => 'el el-twitter'
			),
			array(
				'url'   => 'http://gr.linkedin.com/in/panvagenas',
				'title' => 'Find us on LinkedIn',
				'icon'  => 'el el-linkedin'
			)
		),
	);

	/**
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public function setup() {
		add_action( 'woocommerce_after_register_taxonomy', array( $this, 'setupOptionsPage' ) );
	}

	/**
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public function setupOptionsPage() {
		$params = array(
			'ajax_nonce' => wp_create_nonce( 'skz_gen_now' ),
		);
		$scriptHandle = $this->Scripts->getScriptHandle( 'skz_gen_now' );
		add_action( 'admin_enqueue_scripts', function () use ( $params, $scriptHandle ) {
			wp_localize_script( $scriptHandle, 'skz', $params );
		} );


		$this->Redux->setArg( 'intro_text',
			'<div class="bskz"><span class="spinner"></span>
				<a class="btn btn-primary btn-md pull-right" id="genarate-now" href="#" target="_blank" role="button" style="min-width: 184px;">
					<i class="glyphicon glyphicon-refresh glyphicon-refresh-animate hidden"></i> '
					. $this->I18n->__( 'Generate XML Now' )
				. '</a>
			</div>
			<div style="clear:both;"></div>' );

		$availOptions = array();
		foreach ( Options::$availOptions as $value => $label ) {
			$availOptions[ (string) $value ] = $label;
		}

		$availOptionsDoNotInclude = $availOptions;

		$availOptionsDoNotInclude[ count( $availOptions ) ] = '<span style="color: red;">' . $this->I18n->__( 'Do not Include' ) . '</span>';

		$this->Redux->addSection( $this->I18n->__( 'General Options' ), 'general-options', false, '',
			array( 'icon' => 'el el-edit' ) );

		$productCats = get_terms( 'product_cat', array( 'orderby' => 'name' ) );

		$prodCatsOptions = array();
		if ( ! is_wp_error( $productCats ) ) {
			foreach ( $productCats as $cat ) {
				$prodCatsOptions[ $cat->term_id ] = $cat->name;
			}
		}


		$this->Redux->addSection( $this->I18n->__( 'Basic Options' ), 'basic-options', true, '',
			array(
				'icon'   => 'el el-home',
				'fields' => array(
					array(
						'id'            => 'xml_interval',
						'type'          => 'slider',
						'title'         => $this->I18n->__( 'XML File Generation Interval' ),
						'desc'          => $this->I18n->__( 'Choose the interval of XML file generation' ),
						"default"       => 6,
						"min"           => 0,
						"step"          => 1,
						"max"           => 24,
						'display_value' => 'Hours'
					),
					array(
						'id'      => 'categories',
						'type'    => 'select',
						'multi'   => true,
						'title'   => $this->I18n->__( 'Product categories to exclude' ),
						'desc'    => $this->I18n->__( 'All products in selected categories will be excluded from XML' ),
						'options' => $prodCatsOptions,
						'default' => array(),
					),
					array(
						'id'       => 'avail_inStock',
						'type'     => 'radio',
						'title'    => $this->I18n->__( 'Product availability when item is in stock' ),
						'desc'     => $this->I18n->__( 'Choose the availability of product when this is in stock' ),
						'options'  => $availOptions,
						'default'  => '0',
						'validate' => 'numeric',
					),
					array(
						'id'       => 'avail_outOfStock',
						'type'     => 'radio',
						'title'    => $this->I18n->__( 'Product availability when item is out of stock' ),
						'desc'     => $this->I18n->__( 'Choose the availability of product when this is out of stock' ),
						'options'  => $availOptionsDoNotInclude,
						'default'  => (string) ( count( $availOptionsDoNotInclude ) - 1 ),
						'validate' => 'numeric',

					),
					array(
						'id'       => 'avail_backorders',
						'type'     => 'radio',
						'title'    => $this->I18n->__( 'Product availability when item is out of stock and backorders are allowed' ),
						'desc'     => $this->I18n->__( 'Choose the availability of product when this is out of stock and backorders are allowed' ),
						'options'  => $availOptionsDoNotInclude,
						'default'  => (string) ( count( $availOptionsDoNotInclude ) - 1 ),
						'validate' => 'numeric',
					),
				)
			)
		);


		$this->Redux->addSection( $this->I18n->__( 'Advanced Options' ), 'advanced-options', true, '',
			array(
				'icon'   => 'el el-home',
				'fields' => array(
					array(
						'id'       => 'xml_generate_var',
						'type'     => 'text',
						'title'    => $this->I18n->__( 'XML Request Generate Variable' ),
						'desc'     => $this->I18n->__( 'Request Variable relative to WordPress URL' ),
						'default'  => 'skroutz',
						'validate' => 'not_empty',
					),
					array(
						'id'       => 'xml_generate_var_value',
						'type'     => 'text',
						'title'    => $this->I18n->__( 'XML Request Generate Variable Value' ),
						'desc'     => $this->I18n->__( 'Request Variable Value' ),
						'default'  => $this->get('xml_generate_var_value'),
						'validate' => 'not_empty',
					),
					array(
						'id'       => 'xml_fileName',
						'type'     => 'text',
						'title'    => $this->I18n->__( 'Cached XML Filename' ),
						'desc'     => $this->I18n->__( 'The name of the generated XML file' ),
						'default'  => 'skroutz.xml',
						'validate' => 'not_empty',
					),
					array(
						'id'                => 'xml_location',
						'type'              => 'text',
						'title'             => $this->I18n->__( 'XML File Location' ),
						'desc'              => $this->I18n->__( 'Enter the location you want the file to be saved, relative to WordPress install dir' ),
						'default'           => '',
						'validate_callback' => array( $this->Paths, 'reduxCallBackValidateLocalPath' ),
					),
				)
			)
		);

		$attrTaxonomies = wc_get_attribute_taxonomies();

		$fields = array();

		$options = array(
			$this->I18n->__( 'Use Product SKU' ),
			$this->I18n->__( 'Use Product ID' )
		);

		$fields[] = array(
			'id'                => 'map_id',
			'type'              => 'select',
			'title'             => $this->I18n->__( 'Product ID' ),
			'desc'              => $this->I18n->__( 'Choose from where the product id should be taken' ),
			'options'           => $options,
			'default'           => 1,
			'validate_callback' => array( $this, 'reduxCallBackValidateMapOptions' ),
			'select2'           => array( 'allowClear' => false ),
		);

		$options = array(
			'product_cat' => $this->I18n->__( 'Use Product Categories' ),
			'product_tag' => $this->I18n->__( 'Use Product Tags' ),
		);

		foreach ( $attrTaxonomies as $taxonomies ) {
			$options[ $taxonomies->attribute_id ] = $taxonomies->attribute_label;
		}

		if ( Skroutz::hasBrandsPlugin() && ( $brandsTax = Skroutz::getBrandsPluginTaxonomy() ) ) {
			$options[ $brandsTax->name ] = 'Use WooCommerce Brands Plugin';
		}

		$fields[] = array(
			'id'                => 'map_manufacturer',
			'type'              => 'select',
			'title'             => $this->I18n->__( 'Product Manufacturer Field' ),
			'desc'              => $this->I18n->__( 'Choose from where the product manufacturer should be taken' ),
			'options'           => $options,
			'default'           => 'product_cat',
			'validate_callback' => array( $this, 'reduxCallBackValidateMapOptions' ),
			'select2'           => array( 'allowClear' => false ),
		);

		$options = array(
			$this->I18n->__( 'Use Product SKU' ),
			$this->I18n->__( 'Use Product ID' )
		);

		foreach ( $attrTaxonomies as $taxonomies ) {
			$options[ $taxonomies->attribute_id ] = $taxonomies->attribute_label;
		}

		$fields[] = array(
			'id'                => 'map_mpn',
			'type'              => 'select',
			'title'             => $this->I18n->__( 'Product Manufacturer SKU' ),
			'desc'              => $this->I18n->__( 'Choose from where the product manufacturer SKU should be taken' ),
			'options'           => $options,
			'default'           => 0,
			'validate_callback' => array( $this, 'reduxCallBackValidateMapOptions' ),
			'select2'           => array( 'allowClear' => false ),
		);

		$options    = array();
		$options[0] = $this->I18n->__( 'Use Product Name' );

		foreach ( $attrTaxonomies as $taxonomies ) {
			$options[ $taxonomies->attribute_id ] = $taxonomies->attribute_label;
		}

		$fields[] = array(
			'id'                => 'map_name',
			'type'              => 'select',
			'title'             => $this->I18n->__( 'Product Name' ),
			'desc'              => $this->I18n->__( 'Which field should be used for generating the product name' ),
			'options'           => $options,
			'default'           => 0,
			'validate_callback' => array( $this, 'reduxCallBackValidateMapOptions' ),
			'select2'           => array( 'allowClear' => false ),
		);

		$fields[] = array(
			'id'      => 'map_name_append_sku',
			'type'    => 'switch',
			'title'   => $this->I18n->__( 'Append SKU to Product Name' ),
			'desc'    => $this->I18n->__( 'If you check this the product SKU will be appended to product name. If no SKU is set then product ID will be used' ),
			'default' => false,
		);

		$options = array(
			$this->I18n->__( 'Regular Price' ),
			$this->I18n->__( 'Sales Price' ),
			$this->I18n->__( 'Price Without Tax' )
		);

		$fields[] = array(
			'id'                => 'map_price_with_vat',
			'type'              => 'select',
			'title'             => $this->I18n->__( 'Product Price' ),
			'desc'              => $this->I18n->__( 'Choose the price you want to use in XML' ),
			'options'           => $options,
			'default'           => 1,
			'validate_callback' => array( $this, 'reduxCallBackValidateMapOptions' ),
			'select2'           => array( 'allowClear' => false ),
		);

		$options = array(
			'product_cat' => $this->I18n->__( 'Use Product Categories' ),
			'product_tag' => $this->I18n->__( 'Use Product Tags' ),
		);

		foreach ( $attrTaxonomies as $taxonomies ) {
			$options[ $taxonomies->attribute_id ] = $taxonomies->attribute_label;
		}

		$fields[] = array(
			'id'                => 'map_category',
			'type'              => 'select',
			'title'             => $this->I18n->__( 'Product Categories' ),
			'desc'              => $this->I18n->__( 'Which taxonomy describes best your products' ),
			'options'           => $options,
			'default'           => 'product_cat',
			'validate_callback' => array( $this, 'reduxCallBackValidateMapOptions' ),
			'select2'           => array( 'allowClear' => false ),
		);

		$fields[] = array(
			'id'      => 'is_fashion_store',
			'type'    => 'switch',
			'title'   => $this->I18n->__( 'This Store Contains Fashion Products' ),
			'desc'    => $this->I18n->__( 'Check this if your store has fashion products' ),
			'default' => false,
		);

		$options = array();

		foreach ( $attrTaxonomies as $taxonomies ) {
			$options[ $taxonomies->attribute_id ] = $taxonomies->attribute_label;
		}

		$fields[] = array(
			'id'       => 'map_size',
			'type'     => 'select',
			'multi'    => true,
			'title'    => $this->I18n->__( 'Product Sizes' ),
			'desc'     => $this->I18n->__( 'Select the attribute that describes product sizes' ),
			'options'  => $options,
			'default'  => array(),
			'required' => array( 'is_fashion_store', '=', true )
		);

		$fields[] = array(
			'id'       => 'map_color',
			'type'     => 'select',
			'multi'    => true,
			'title'    => $this->I18n->__( 'Product Colors' ),
			'desc'     => $this->I18n->__( 'Select the attribute that describes product colors' ),
			'options'  => $options,
			'default'  => array(),
			'required' => array( 'is_fashion_store', '=', true )
		);

		$fields[] = array(
			'id'      => 'is_book_store',
			'type'    => 'switch',
			'title'   => $this->I18n->__( 'This is a Bookstore' ),
			'desc'    => $this->I18n->__( 'Check this if you are selling books. In this case you must set the ISBN of each book' ),
			'default' => false,
		);

		$options = array( $this->I18n->__( 'Use Product SKU' ) );

		foreach ( $attrTaxonomies as $taxonomies ) {
			$options[ $taxonomies->attribute_id ] = $taxonomies->attribute_label;
		}

		$fields[] = array(
			'id'                => 'map_isbn',
			'type'              => 'select',
			'title'             => $this->I18n->__( 'ISBN' ),
			'desc'              => $this->I18n->__( 'Choose the field that contains books ISBN' ),
			'options'           => $options,
			'default'           => 0,
			'required'          => array( 'is_book_store', '=', true ),
			'validate_callback' => array( $this, 'reduxCallBackValidateMapOptions' ),
			'select2'           => array( 'allowClear' => false ),
		);

		$this->Redux->addSection( $this->I18n->__( 'Map Options' ), 'map-options', false, '',
			array(
				'icon'   => 'el el-map-marker',
				'fields' => $fields
			)
		);

		$logArray = (array) get_option( $this->Plugin->getSlug() . '_log' );

		$this->Redux->addSection( $this->I18n->__( 'XML Generation Log' ), 'log-section', false, '',
			array(
				'class' => 'bskz',
				'fields' => array(
					array(
						'id'      => 'show_log',
						'type'    => 'select',
						'multi'   => true,
						'title'   => $this->I18n->__( 'Display log' ),
						'options' => array(
							Logger::INFO    => $this->I18n->__( 'Info' ),
							Logger::NOTICE  => $this->I18n->__( 'Notices' ),
							Logger::WARNING => $this->I18n->__( 'Warnings' ),
							Logger::ERROR   => $this->I18n->__( 'Errors' ),
						),
						'default' => array( Logger::INFO, Logger::WARNING, Logger::ERROR ),
					),
					array(
						'id'       => 'log-info',
						'title'    => '',
						'type'     => 'raw',
						'content'  => $this->Templates->view( 'log.php',
							array( 'logArray' => $logArray, 'level' => Logger::INFO ), false ),
						'required' => array( 'show_log', 'contains', Logger::INFO )
					),
					array(
						'id'       => 'log-notice',
						'title'    => '',
						'type'     => 'raw',
						'content'  => $this->Templates->view( 'log.php',
							array( 'logArray' => $logArray, 'level' => Logger::NOTICE ), false ),
						'required' => array( 'show_log', 'contains', Logger::NOTICE )
					),
					array(
						'id'       => 'log-warning',
						'title'    => '',
						'type'     => 'raw',
						'content'  => $this->Templates->view( 'log.php',
							array( 'logArray' => $logArray, 'level' => Logger::WARNING ), false ),
						'required' => array( 'show_log', 'contains', Logger::WARNING )
					),
					array(
						'id'       => 'log-error',
						'title'    => '',
						'type'     => 'raw',
						'content'  => $this->Templates->view( 'log.php',
							array( 'logArray' => $logArray, 'level' => Logger::ERROR ), false ),
						'required' => array( 'show_log', 'contains', Logger::ERROR )
					),
				)
			)
		);

		$info = $this->XML->getFileInfo();

		$this->Redux->addSection( $this->I18n->__( 'XML Info' ), 'info-section', false, '',
			array(
				'fields' => array(
					array(
						'id'      => 'info-raw',
						'title'   => '',
						'type'    => 'raw',
						'content' => $this->Templates->view( 'info.php', array( 'info' => $info ), false )
					),
				)
			)
		);
	}

	/**
	 * @param $field
	 * @param $value
	 * @param $existing_value
	 *
	 * @return array
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public function reduxCallBackValidateMapOptions( $field, $value, $existing_value ) {
		$return = array();

		if ( ! is_numeric( $value ) && is_string( $value ) && empty( $value ) ) {
			$value           = $existing_value;
			$field['msg']    = $this->I18n->__( 'Please choose an option, this field is required' );
			$return['error'] = $field;
		}

		$return['value'] = $value;

		return $return;
	}
}