<?php
/**
 * Project: anosiapharmacy
 * File: Ajax.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 7/9/2015
 * Time: 11:53 μμ
 * Since: TODO ${VERSION}
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace Skroutz;


use PanWPCore\Core;

/**
 * Class Ajax
 *
 * @package Skroutz
 * @author  Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @since   TODO ${VERSION}
 *
 * @property \Skroutz\Skroutz $Skroutz
 */
class Ajax extends Core{
	public function generateNow(){
		check_ajax_referer( 'skz_gen_now', 'nonce' );

		if(!is_super_admin()){
			$this->sendJSONResult(array(
				'included' => 0,
				'msg' => 'Action not permited',
				'posts_per_page' => -1
			));
		}

		$included = $this->Skroutz->do_your_woo_stuff();

		$this->sendJSONResult(array(
			'included' => $included,
			'msg' => sprintf($this->I18n->__('Generation is complete. A total of %d items were included in XML, please see the generation log for more details.'), $included)
		));
	}

	/**
	 * @param     $response
	 * @param int $responseCode
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public function sendJSONResult($response, $responseCode = 200){
		header( 'Content-Type: application/json', $responseCode );
		echo json_encode( $response );
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			wp_die();
		else
			die;
	}

	/**
	 * @param     $data
	 * @param int $responseCode
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public function sendJSONError($data, $responseCode = 400){
		$this->sendJSONResult(array('data' => $data, 'success' => false), $responseCode);
	}

	/**
	 * @param     $data
	 * @param int $responseCode
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public function sendJSONSuccess($data, $responseCode = 200){
		$this->sendJSONResult(array('data' => $data, 'success' => true), $responseCode);
	}
}