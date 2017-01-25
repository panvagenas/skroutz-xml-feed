<?php
/**
 * Project: skroutz.gr-xml-feed
 * File: main_settings_panel.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 18/10/2014
 * Time: 2:29 μμ
 * Since: 141017
 * Copyright: 2014 Panagiotis Vagenas
 */

if ( ! defined( 'WPINC' ) ) {
	exit( 'Do NOT access this file directly: ' . basename( __FILE__ ) );
}

/* @var \skroutz\menu_pages\panels\main_settings $callee */
/* @var \xd_v141226_dev\views $this */
$showAdvanced     = (bool) $this->©option->get( 'show_advanced' );
$showAdvancedHide = $showAdvanced ? '' : ' style="display:none;"';
$availOptions     = array();
foreach ( $this->©options->availOptions as $value => $label ) {
	$availOptions[] = array(
		'label' => $label,
		'value' => (string) $value
	);
}

$availOptionsDoNotInclude   = $availOptions;
$availOptionsDoNotInclude[] = array(
	'label' => $this->__( 'Do not Include' ),
	'value' => (string) count( $availOptions )
);

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

$compressOptions = [ [ 'label' => $this->__( 'None' ), 'value' => '0' ] ];
if ( $this->©env->supportsGzCompression() ) {
	$compressOptions[] = [ 'label' => $this->__( 'GZ Compress' ), 'value' => '1' ];
}
if ( $this->©env->supportsZipCompression() ) {
	$compressOptions[] = [ 'label' => $this->__( 'ZIP Compress' ), 'value' => '2' ];
}
?>
<div class="form-horizontal main-settings-form-wrapper" role="form">
	<div class="form-group row">
		<label for="show-advanced"
		       class="col-md-3 control-label"><?php echo $this->__( 'Show advanced options' ); ?></label>

		<div class="col-sm-7">
			<?php
			$inputOptions = array(
				'type'  => 'checkbox',
				'name'  => '[show_advanced]',
				'title' => $this->__( 'Show advanced options' ),
				'id'    => 'show-advanced',
			);
			echo $callee->menu_page->option_form_fields->markup( $this->©option->get( 'show_advanced' ),
				$inputOptions );
			?>
		</div>
	</div>
	<?php
	/***********************************************
	 * XML Generate Var
	 ***********************************************/
	?>
	<div class="form-group row advanced" <?php echo $showAdvancedHide; ?>>
		<label for="xml-generate-var"
		       class="col-md-3 control-label"><?php echo $this->__( 'XML Generation Request Variable' ); ?></label>

		<div class="col-sm-7">
			<?php
			$inputOptions = array(
				'type'        => 'text',
				'name'        => '[xml_generate_var]',
				'title'       => $this->__( 'XML Request Generate Variable' ),
				'placeholder' => $this->__( 'Request Variable relative to WordPress URL' ),
				'required'    => true,
				'id'          => 'xml-generate-var',
				'attrs'       => '',
				'classes'     => 'form-control col-md-10'
			);
			echo $callee->menu_page->option_form_fields->markup( $this->©option->get( 'xml_generate_var' ),
				$inputOptions );
			?>
		</div>
	</div>
	<?php
	/***********************************************
	 * XML Generate Var Value
	 ***********************************************/
	?>
	<div class="form-group row advanced" <?php echo $showAdvancedHide; ?>>
		<label for="xml-generate-var-val"
		       class="col-md-3 control-label"><?php echo $this->__( 'XML Generation Request Variable Value' ); ?></label>

		<div class="col-sm-7">
			<?php
			$inputOptions = array(
				'type'        => 'text',
				'name'        => '[xml_generate_var_value]',
				'title'       => $this->__( 'XML Request Generate Variable Value' ),
				'placeholder' => $this->__( 'Request Variable Value' ),
				'required'    => true,
				'id'          => 'xml-generate-var-val',
				'attrs'       => '',
				'classes'     => 'form-control col-md-10'
			);
			echo $callee->menu_page->option_form_fields->markup( $this->©option->get( 'xml_generate_var_value' ),
				$inputOptions );
			?>
		</div>
	</div>
	<?php
	/***********************************************
	 * XML file name
	 ***********************************************/
	?>
	<div class="form-group row advanced" <?php echo $showAdvancedHide; ?>>
		<label for="xml-fileName"
		       class="col-md-3 control-label"><?php echo $this->__( 'Cached XML Filename' ); ?></label>

		<div class="col-sm-7">
			<?php
			$inputOptions = array(
				'type'                => 'text',
				'name'                => '[xml_fileName]',
				'title'               => $this->__( 'XML Filename' ),
				'placeholder'         => $this->__( 'The name of the generated XML file' ),
				'required'            => true,
				'validation_patterns' => array(
					array(
						'name'        => 'xml_file',
						'description' => $this->__( 'The XML file name must have an .xml extension and not containing spaces' ),
						'regex'       => '/^.+(\.xml|\.XML)+$/'
					)
				),
				'id'                  => 'xml-fileName',
				'attrs'               => '',
				'classes'             => 'form-control col-md-9'
			);
			echo $callee->menu_page->option_form_fields->markup( $this->©option->get( 'xml_fileName' ), $inputOptions );
			?>
		</div>
	</div>
	<?php
	/***********************************************
	 * XML File Location
	 ***********************************************/
	?>
	<div class="form-group row advanced" <?php echo $showAdvancedHide; ?>>
		<label for="xml-location"
		       class="col-md-3 control-label"><?php echo $this->__( 'Cached XML File Location' ); ?></label>

		<div class="col-sm-7 input-group" style="padding-left: 15px; padding-right: 15px;">
			<span class="input-group-addon"><?php echo ABSPATH; ?></span>
			<?php
			$inputOptions = array(
				'type'        => 'text',
				'name'        => '[xml_location]',
				'title'       => $this->__( 'XML File Location' ),
				'placeholder' => $this->__( 'Enter the location you want the file to be saved, relative to WordPress install dir' ),
				'id'          => 'xml-location',
				'attrs'       => '',
				'classes'     => 'form-control col-md-10'
			);
			echo $callee->menu_page->option_form_fields->markup( $this->©option->get( 'xml_location' ), $inputOptions );
			?>
		</div>
	</div>

	<div class="form-group row advanced" <?php echo $showAdvancedHide; ?>>
		<label for="xml-compression"
		       class="col-md-3 control-label"><?php echo $this->__( 'XML File Compression' ); ?></label>

		<div class="col-sm-7">
			<?php
			$inputOptions = array(
				'type'        => 'select',
				'name'        => '[xml_compress]',
				'title'       => $this->__( 'XML File Compression' ),
				'placeholder' => $this->__( 'Choose if and how the XML will be compressed' ),
				'required'    => true,
				'id'          => 'xml-compress',
				'attrs'       => '',
				'classes'     => 'form-control col-md-10',
				'options'     => $compressOptions
			);
			echo $callee->menu_page->option_form_fields->markup( $this->©option->get( 'xml_compress' ), $inputOptions );
			?>
		</div>
	</div>
	<?php
	/***********************************************
	 * XML File Generation Interval
	 ***********************************************/
	?>
	<div class="form-group row">
		<label for="xml-interval"
		       class="col-md-3 control-label"><?php echo $this->__( 'XML File Generation Interval' ); ?></label>

		<div class="col-sm-7">
			<?php
			$inputOptions = array(
				'type'        => 'select',
				'name'        => '[xml_interval]',
				'title'       => $this->__( 'XML File Generation Interval' ),
				'placeholder' => $this->__( 'Choose the interval of XML file generation' ),
				'required'    => true,
				'id'          => 'xml-interval',
				'attrs'       => '',
				'classes'     => 'form-control col-md-10',
				'options'     => array(
					array(
						'label' => $this->__( 'Daily' ),
						'value' => 'daily'
					),
					array(
						'label' => $this->__( 'Twice Daily' ),
						'value' => 'twicedaily'
					),
					array(
						'label' => $this->__( 'Hourly' ),
						'value' => 'hourly'
					),
					array(
						'label' => $this->__( 'Every Thirty Minutes' ),
						'value' => 'every30m'
					)
				)
			);
			echo $callee->menu_page->option_form_fields->markup( $this->©option->get( 'xml_interval' ), $inputOptions );
			?>
		</div>
	</div>
	<?php
	/***********************************************
	 * Availability when products are in stock
	 ***********************************************/
	?>
	<div class="form-group row">
		<label for="avail-inStock"
		       class="col-md-3 control-label"><?php echo $this->__( 'Product availability when item is in stock' ); ?></label>

		<div class="col-sm-7">
			<?php
			$inputOptions = array(
				'type'        => 'select',
				'name'        => '[avail_inStock]',
				'title'       => $this->__( 'Product availability when item is in stock' ),
				'placeholder' => $this->__( 'Choose the availability of product when this is in stock' ),
				'required'    => true,
				'id'          => 'avail-inStock',
				'attrs'       => '',
				'classes'     => 'form-control col-md-10',
				'options'     => $availOptions
			);
			echo $callee->menu_page->option_form_fields->markup( $this->©option->get( 'avail_inStock' ),
				$inputOptions );
			?>
		</div>
	</div>
	<?php
	/***********************************************
	 * Availability when products are out of stock
	 ***********************************************/
	?>
	<div class="form-group row">
		<label for="avail-outOfStock"
		       class="col-md-3 control-label"><?php echo $this->__( 'Product availability when item is out of stock' ); ?></label>

		<div class="col-sm-7">
			<?php
			$inputOptions = array(
				'type'        => 'select',
				'name'        => '[avail_outOfStock]',
				'title'       => $this->__( 'Product availability when item is out of stock' ),
				'placeholder' => $this->__( 'Choose the availability of product when this is out of stock' ),
				'required'    => true,
				'id'          => 'avail-outOfStock',
				'attrs'       => '',
				'classes'     => 'form-control col-md-10',
				'options'     => $availOptionsDoNotInclude
			);
			echo $callee->menu_page->option_form_fields->markup( $this->©option->get( 'avail_outOfStock' ),
				$inputOptions );
			?>
		</div>
	</div>

	<div class="form-group row">
		<label for="avail-backorders"
		       class="col-md-3 control-label"><?php echo $this->__( 'Product availability when item is out of stock and backorders are allowed' ); ?></label>

		<div class="col-sm-7">
			<?php
			$inputOptions = array(
				'type'        => 'select',
				'name'        => '[avail_backorders]',
				'title'       => $this->__( 'Product availability when item is out of stock and backorders are allowed' ),
				'placeholder' => $this->__( 'Choose the availability of product when this is out of stock and backorders are allowed' ),
				'required'    => true,
				'id'          => 'avail-backorders',
				'attrs'       => '',
				'classes'     => 'form-control col-md-10',
				'options'     => $availOptionsDoNotInclude
			);
			echo $callee->menu_page->option_form_fields->markup( $this->©option->get( 'avail_backorders' ),
				$inputOptions );
			?>
		</div>
	</div>

	<?php
	if($categories){
		?>
		<div class="form-group row">
			<label for="ex-cats"
			       class="col-md-3 control-label"><?php echo $this->__( 'Exclude products from certain categories <br>(if a product has any of these categories then it will not be included in the XML)' ); ?></label>

			<div class="col-sm-7">
				<?php
				$inputOptions = array(
					'type'        => 'select',
					'multiple'    => true,
					'name'        => '[ex_cats]',
					'title'       => $this->__( 'Exclude products from these categories' ),
					'placeholder' => $this->__( 'Exclude products from these categories' ),
					'required'    => false,
					'id'          => 'ex-cats',
					'attrs'       => '',
					'size'       => 0,
					'classes'     => 'form-control col-md-10',
					'options'     => $categories
				);
				echo $callee->menu_page->option_form_fields->markup( $this->©option->get( 'ex_cats' ), $inputOptions );
				?>
			</div>
		</div>
	<?php
	}
	?>

	<?php
	if($tags){
		?>
		<div class="form-group row">
			<label for="ex-tags"
			       class="col-md-3 control-label"><?php echo $this->__( 'Exclude products from certain tags <br>(if a product has any of these tags then it will not be included in the XML)' ); ?></label>

			<div class="col-sm-7">
				<?php
				$inputOptions = array(
					'type'        => 'select',
					'multiple'    => true,
					'name'        => '[ex_tags]',
					'title'       => $this->__( 'Exclude products from these tags' ),
					'placeholder' => $this->__( 'Exclude products from these tags' ),
					'required'    => false,
					'id'          => 'ex-tags',
					'attrs'       => '',
					'size'       => 0,
					'classes'     => 'form-control col-md-10',
					'options'     => $tags
				);
				echo $callee->menu_page->option_form_fields->markup( $this->©option->get( 'ex_tags' ), $inputOptions );
				?>
			</div>
		</div>
	<?php
	}
	?>

</div>