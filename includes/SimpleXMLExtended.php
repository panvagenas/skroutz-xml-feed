<?php

/**
 * Created by PhpStorm.
 * User: vagenas
 * Date: 25/8/2015
 * Time: 2:35 μμ
 */

/**
 * Class SimpleXMLExtended extends SimpleXMLElement so CDATA con be added without enconding
 */
if(!class_exists('SimpleXMLExtended')){
	class SimpleXMLExtended extends SimpleXMLElement {
		public function addCData($cdata_text) {
			$node = dom_import_simplexml($this);
			$no   = $node->ownerDocument;
			$node->appendChild($no->createCDATASection($cdata_text));
		}
	}
}
