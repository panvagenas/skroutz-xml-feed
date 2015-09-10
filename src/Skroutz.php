<?php
/**
 * Project: skroutz-pan
 * File: Skroutz.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 4/9/2015
 * Time: 12:16 πμ
 * Since: TODO ${VERSION}
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace Skroutz;


use PanWPCore\Core;
use PanWPCore\Log\Handlers\DBHandler;
use Skroutz\Log\Handlers\HtmlFormater;
use Skroutz\Log\Logger;

/**
 * Class Skroutz
 * @package Skroutz
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @since TODO ${VERSION}
 *
 * @property Logger $Log__Logger
 *
 * @property Env $Env
 * @property XML $XML
 */
class Skroutz extends Core{
	/**
	 * @return mixed
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	public function do_your_woo_stuff() {
		$sTime = microtime( true );
		ignore_user_abort( true );

		$this->Log__Logger->clearDBLog();
		$logHandler = new DBHandler($this->Plugin->getSlug().'_log');
		$logHandler->setFormatter(new HtmlFormater());
		$this->Log__Logger->addDBHandler($logHandler, \Monolog\Logger::INFO);

		$this->Log__Logger->addInfo( '<strong>Skroutz XML generation started at ' . date( 'd M, Y H:i:s' ) . '</strong>' );

		$prodInXml = $this->processProducts();

		$this->Log__Logger->addInfo(
			'<strong>Skroutz XML generation finished at '
			. date( 'd M, Y H:i:s' )
			. '</strong><br>Time taken: ' . round( microtime( true ) - $sTime, 20 ) . ' sec<br>
			Mem details: ' . $this->Env->memory_details() );

		return $prodInXml;
	}

	/**
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	public function generate_and_print() {
		$interval         = $this->Options->get( 'xml_interval' ) * 60 * 60;
		$xmlCreation      = $this->XML->getFileInfo();
		$createdTime      = strtotime( $xmlCreation[ $this->XML->createdAtName ]['value'] );
		$nextCreationTime = $interval + $createdTime;
		$time             = time();

		if ( $time > $nextCreationTime ) {
			$this->do_your_woo_stuff();
		}

		$this->XML->printXML();
		exit( 0 );
	}

	/**
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	public function getGenerateXmlUrl() {
		return home_url() . '/?' . $this->Options->get( 'xml_generate_var' ) . '=' . $this->Options->get( 'xml_generate_var_value' );
	}

	/**
	 * @return int
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	public function processProducts() {
		$queryArgs = array(
			'fields' => 'ids',
			'post_status' => 'publish',
			'post_type' => 'product',
			'posts_per_page' => -1
		);

		$excluded = $this->Options->get('categories');

		if(!empty($excluded)){
			$queryArgs['tax_query']	= array(
				array(
					'taxonomy'  => 'product_cat',
					'field'     => 'term_id',
					'terms'     => $excluded,
					'operator'  => 'NOT IN')
			);
		}


		$query = new \WP_Query($queryArgs);

		$prodArray = $query->get_posts();

		$this->Env->maximize_time_memory_limits();

		$mem = $this->Env->getMemInM( ini_get( 'memory_limit' ) ) / 1024 / 1024;

		$memLimit = ( $mem - 10 ) * 1024 * 1024;

		foreach ( $prodArray as $i => $pid ) {

			if ( memory_get_usage() > $memLimit ) {
				wp_cache_flush();
			}

			$product = WC()->product_factory->get_product( (int) $pid );

			if ( ! is_object( $product ) || ! ( $product instanceof \WC_Product ) ) {
				$this->Log__Logger->addWarning( 'Product failed in ' . __METHOD__ );
				continue;
			}

			if ( ! $product->is_purchasable() || ! $product->is_visible() || $this->getAvailabilityString( $product ) === false ) {
				$reason = array();
				if ( ! $product->is_purchasable() ) {
					$reason[] = 'product is not purchasable';
				}
				if ( ! $product->is_visible() ) {
					$reason[] = 'product is not visible';
				}
				if ( $this->getAvailabilityString( $product ) === false ) {
					$reason[] = 'product is unavailable';
				}
				$this->Log__Logger->addNotice( 'Product <strong>' . $product->get_formatted_name() . '</strong> failed. Reason(s) is(are): ' . implode( ', ', $reason ) );
				continue;
			}

			$this->XML->appendProduct( $this->getProductArray( $product ) );
		}
		wp_cache_flush();

		return $this->XML->saveXML() ? $this->XML->countProductsInFile( $this->XML->simpleXML ) : 0;
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return array
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getProductArray( \WC_Product &$product ) {
		$out = array();

		$out['id']             = $this->getProductId( $product );
		$out['mpn']            = $this->getProductMPN( $product );
		$out['name']           = $this->getProductName( $product );
		$out['link']           = $this->getProductLink( $product );
		$out['image']          = $this->getProductImageLink( $product );
		$out['category']       = $this->getProductCategories( $product );
		$out['price_with_vat'] = $this->getProductPrice( $product );
		$out['instock']        = $this->isInStock( $product );
		$out['availability']   = $this->getAvailabilityString( $product );
		$out['manufacturer']   = $this->getProductManufacturer( $product );

		if ( $product->product_type == 'variable' && (bool) $this->Options->get( 'is_fashion_store' ) ) {
			$product = new \WC_Product_Variable( $product );

			$colors = $this->getProductColors( $product );
			$sizes  = $this->getProductSizes( $product );

			if ( ! empty( $colors ) ) {
				$out['color'] = $colors;
			}

			if ( ! empty( $sizes ) ) {
				$out['size'] = $sizes;
			}
		} elseif ( (bool) $this->Options->get( 'is_book_store' ) ) {
			$isbn = $this->getProductISBN( $product );
			if ( $isbn ) {
				$out['isbn'] = $isbn;
			}
		}

		return $out;
	}

	/**
	 * @param \WC_Product_Variable $product
	 *
	 * @return null|string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getProductColors( \WC_Product_Variable &$product ) {
		$map = $this->Options->get( 'map_color' );
		if ( empty( $map ) ) {
			return null;
		}

		$variations = $product->get_available_variations();
		$colors     = array();
		foreach ( $map as $attrId ) {
			$taxonomy = $this->getTaxonomyById( $attrId );

			if ( ! $taxonomy ) {
				break;
			}

			foreach ( $variations as $variation ) {
				$attrName = wc_attribute_taxonomy_name( $taxonomy->attribute_name );
				$key      = 'attribute_' . $attrName;
				if ( isset( $variation['attributes'][ $key ] ) && $variation['is_in_stock'] && $variation['is_purchasable'] ) {
					if ( empty( $variation['attributes'][ $key ] ) ) {
						$attr = $product->get_attribute( $attrName );
						if ( is_string( $attr ) && ! empty( $attr ) ) {
							$attrValues = array_map( 'trim', explode( ',', $attr ) );
							$colors     = array_merge( $colors, $attrValues );
							break;
						}
					} else {
						$term = get_terms( $attrName, array(
							'fields' => 'names',
							'slug'   => $variation['attributes'][ $key ]
						) );
						if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
							$colors[] = array_pop( $term );
						}
					}
				}
			}
		}

		$colors = array_unique( $colors );

		return implode( ', ', $colors );
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getProductISBN( \WC_Product &$product ) {
		$map = $this->Options->get( 'map_isbn' );
		if ( $map == 0 ) {
			return $product->get_sku();
		}

		return $this->getProductAttrValue( $product, $map, false );
	}

	/**
	 * @param $taxonomyId
	 *
	 * @return null
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getTaxonomyById( $taxonomyId ) {
		foreach ( wc_get_attribute_taxonomies() as $taxonomy ) {
			if ( $taxonomyId == $taxonomy->attribute_id ) {
				return $taxonomy;
			}
		}

		return null;
	}

	/**
	 * @param \WC_Product_Variable $product
	 *
	 * @return null|string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getProductSizes( \WC_Product_Variable &$product ) {
		$map = $this->Options->get( 'map_size' );
		if ( empty( $map ) ) {
			return null;
		}

		$variations = $product->get_available_variations();
		$sizes      = array();
		foreach ( $map as $attrId ) {
			$taxonomy = $this->getTaxonomyById( $attrId );

			if ( ! $taxonomy ) {
				break;
			}

			foreach ( $variations as $variation ) {
				$attrName = wc_attribute_taxonomy_name( $taxonomy->attribute_name );
				$key      = 'attribute_' . $attrName;
				if ( isset( $variation['attributes'][ $key ] ) && $variation['is_in_stock'] && $variation['is_purchasable'] ) {
					if ( empty( $variation['attributes'][ $key ] ) ) {
						$attr = $product->get_attribute( $attrName );
						if ( is_string( $attr ) && ! empty( $attr ) ) {
							$attrValues = array_map( 'trim', explode( ',', $attr ) );
							$sizes      = array_merge( $sizes, $attrValues );
							break;
						}
					} else {
						$term = get_terms( $attrName, array(
							'fields' => 'names',
							'slug'   => $variation['attributes'][ $key ]
						) );
						if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
							$sizes[] = array_pop( $term );
						}
					}
				}
			}
		}
		$sizes = array_unique( $sizes );

		return implode( ', ', $sizes );
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getProductManufacturer( \WC_Product &$product ) {
		$option = $this->Options->get( 'map_manufacturer' );

		$manufacturer = '';
		if ( is_numeric( $option ) ) {
			$manufacturer = $this->getProductAttrValue( $product, $option, '' );
		}
		if ( empty( $manufacturer ) ) {
			$manufacturer = $this->getFormatedTextFromTerms( $product, $option );
		}

		return $manufacturer;
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function isInStock( \WC_Product &$product ) {
		return $product->is_in_stock() ? 'Y' : 'N';
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getProductPrice( \WC_Product &$product ) {
		$option = $this->Options->get( 'map_price_with_vat' );

		switch ( $option ) {
			case 1:
				$price = $product->get_sale_price();
				break;
			case 2:
				$price = $product->get_price_excluding_tax();
				break;
			default:
				$price = $product->get_price();
				break;
		}
		// Fallback to product price in case other options return empty string
		if ( empty( $price ) ) {
			$price = $product->get_price();
		}

		return $price;
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getProductCategories( \WC_Product &$product ) {
		$option     = $this->Options->get( 'map_category' );
		$categories = '';
		if ( is_numeric( $option ) ) {
			$categories = $this->getProductAttrValue( $product, $option, '' );
		}
		if ( empty( $categories ) ) {
			$categories = $this->getFormatedTextFromTerms( $product, $option );
		}

		return $categories;
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getProductImageLink( \WC_Product &$product ) {
		$option = $this->Options->get( 'map_image' );

		// Maybe we will implement some additional functionality in the future
		$imageLink = '';
		if ( true || $option == 0 ) {
			$imageLink = wp_get_attachment_image_src( $product->get_image_id() );
			$imageLink = is_array( $imageLink ) ? $imageLink[0] : '';
		}

		return urldecode( $imageLink );
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getProductId( \WC_Product &$product ) {
		$option = $this->Options->get( 'map_id' );
		if ( $option == 0 ) {
			return $product->get_sku();
		} else {
			return $product->id;
		}
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getProductMPN( \WC_Product &$product ) {
		$option = $this->Options->get( 'map_mpn' );

		if ( $option == 0 ) {
			return $product->get_sku();
		}

		return $this->getProductAttrValue( $product, $option, $product->get_sku() );
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getProductLink( \WC_Product &$product ) {
		$option = $this->Options->get( 'map_link' );

		// Maybe we will implement some additional functionality in the future
		$link = '';
		if ( true || $option == 0 ) {
			$link = $product->get_permalink();
		}

		return urldecode( $link );
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getProductName( \WC_Product &$product ) {
		$option    = $this->Options->get( 'map_name' );
		$appendSKU = $this->Options->get( 'map_name_append_sku' );
		$name      = '';

		if ( $option != 0 ) {
			$name = $this->getProductAttrValue( $product, $option, '' );
		}

		if ( empty( $name ) ) {
			$name = $product->get_title();
		}

		$name = trim( $name );
		$pid  = $this->getProductId( $product );
		if ( $appendSKU && ! empty( $pid ) && ! is_numeric( strpos( $product->get_title(), $pid ) ) ) {
			$name .= ' ' . $pid;
		}

		return $name;
	}

	/**
	 * @param \WC_Product $product
	 * @param $attrId
	 * @param null $defaultValue
	 *
	 * @return null
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getProductAttrValue( \WC_Product &$product, $attrId, $defaultValue = null ) {
		$return = $product->get_attribute( $this->getAttributeNameFromId( $attrId ) );

		return empty( $return ) ? $defaultValue : $return;
	}

	/**
	 * @param $attrId
	 *
	 * @return bool|string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getAttributeNameFromId( $attrId ) {
		foreach ( wc_get_attribute_taxonomies() as $taxonomy ) {
			if ( $taxonomy->attribute_id == $attrId ) {
				return trim( $taxonomy->attribute_name );
			}
		}

		return false;
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getAvailabilityString( \WC_Product &$product ) {
		$stockStatusInStock = $product->stock_status === 'instock';
		$manageStock        = $product->managing_stock();
		$backOrdersAllowed  = $product->backorders_allowed();
		$hasQuantity        = $product->get_stock_quantity() > 0;

		if ( $manageStock ) {
			if ( $hasQuantity ) {
				return Options::$availOptions[ $this->Options->get( 'avail_inStock' ) ];
			} elseif ( ! $backOrdersAllowed ) {
				if ( $this->Options->get( 'avail_outOfStock' ) == count( Options::$availOptions ) ) {
					return false;
				}

				return Options::$availOptions[ $this->Options->get( 'avail_outOfStock' ) ];
			} else {
				if ( $this->Options->get( 'avail_backorders' ) == count( Options::$availOptions ) ) {
					return false;
				}

				return Options::$availOptions[ $this->Options->get( 'avail_backorders' ) ];
			}
		} else {
			if ( $stockStatusInStock ) {
				return Options::$availOptions[ $this->Options->get( 'avail_inStock' ) ];
			} elseif ( $backOrdersAllowed ) {
				if ( $this->Options->get( 'avail_backorders' ) == count( Options::$availOptions ) ) {
					return false;
				}

				return Options::$availOptions[ $this->Options->get( 'avail_backorders' ) ];
			}
		}

		return false;
	}

	/**
	 * @param $string
	 *
	 * @return mixed|string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function formatSizeColorStrings( $string ) {
		if ( is_array( $string ) ) {
			foreach ( $string as $k => $v ) {
				$string[ $k ] = $this->formatSizeColorStrings( $v );
			}

			return implode( ',', $string );
		}

		$patterns        = array();
		$patterns[0]     = '/\|/';
		$patterns[1]     = '/\s+/';
		$replacements    = array();
		$replacements[2] = ',';
		$replacements[1] = '';

		return preg_replace( $patterns, $replacements, $string );
	}

	/**
	 * @param \WC_Product $product
	 * @param $term
	 *
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	protected function getFormatedTextFromTerms( \WC_Product &$product, $term ) {
		$terms = get_the_terms( $product->id, $term );
		$out   = array();
		if ( is_array( $terms ) ) {
			foreach ( $terms as $k => $term ) {
				$name  = rtrim( ltrim( $term->name ) );
				$out[] = $name;
			}
		}

		return implode( ' - ', array_unique( $out ) );
	}

	/**
	 * @return bool
	 * @static * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	public static function hasBrandsPlugin() {
		return is_plugin_active( 'woocommerce-brands/woocommerce-brands.php' ) && taxonomy_exists( 'product_brand' );
	}

	/**
	 * @return false|null|object
	 * @static * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since TODO ${VERSION}
	 */
	public static function getBrandsPluginTaxonomy() {
		if ( self::hasBrandsPlugin() ) {
			return get_taxonomy( 'product_brand' );
		}

		return null;
	}
}