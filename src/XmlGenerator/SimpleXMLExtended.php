<?php
/**
 * SimpleXMLExtended.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-28
 * @version   $Id$
 * @package   Pan\XmlGenerator
 * @copyright Copyright (c) 2015 Interactive Data Managed Solutions Ltd
 */


namespace Pan\XmlGenerator;

/**
 * Class SimpleXMLExtended
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-28
 * @version   $Id$
 * @package   Pan\XmlGenerator
 * @copyright Copyright (c) 2015 Interactive Data Managed Solutions Ltd
 */
class SimpleXMLExtended extends \SimpleXMLElement {
    public function addCData($cdata_text) {
        $node = dom_import_simplexml($this);
        $no   = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
    }
}