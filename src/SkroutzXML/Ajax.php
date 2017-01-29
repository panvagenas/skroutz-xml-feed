<?php
/**
 * Ajax.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     170126
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\SkroutzXML;

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Class Ajax
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     170126
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class Ajax {
    public function generateNow() {
        check_ajax_referer( 'skz-gen-now-action', 'nonce' );

        if ( ! is_super_admin() ) {
            wp_die('Not allowed');
        }

        $skroutz  = new Skroutz();
        $included = $skroutz->generateXml();

        $included['msg'] = 'Generation is complete. A total of '
               . $included['totalSuccess']
               . ' items were included in XML, please see the generation log for more details.';

        wp_send_json_success($included);
    }
}