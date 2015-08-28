<?php
/**
 * Created by PhpStorm.
 * User: vagenas
 * Date: 25/8/2015
 * Time: 3:22 μμ
 */

namespace SkroutzXMLFeed\Controllers;

use Herbert\Framework\Http;
use SkroutzXMLFeed\Classes\Options;
use SkroutzXMLFeed\Classes\Skroutz;

class SkroutzController {
	public function generate(Http $http){
		$options = new Options();

		$generateVar = $options->get('xml_generate_var');
		if(empty($generateVar)){
			$generateVar = 'skroutz_' . uniqid();
			$options->set('xml_generate_var', $generateVar);
		}

		$generateVarVal = $options->get('xml_generate_var_value');
		if(empty($generateVarVal)){
			$generateVarVal = uniqid().uniqid();
			$options->set('xml_generate_var_value', $generateVarVal);
		}

		if ($http->get($generateVar) === $generateVarVal) {
			$skz = new Skroutz();
			$skz->generate_and_print();
			return '';
		}

		wp_die('Nothing here');
	}
}