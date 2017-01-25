<?php
/**
 * env.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2016-11-21
 * @since     TODO ${VERSION}
 * @package   skroutz
 * @copyright Copyright (c) 2016 Panagiotis Vagenas
 */

namespace skroutz;

/**
 * Class env
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2016-11-21
 * @since     TODO ${VERSION}
 * @package   skroutz
 * @copyright Copyright (c) 2016 Panagiotis Vagenas
 */
class env extends \xd_v141226_dev\env {
    public function supportsGzCompression(){
        return extension_loaded('zlib');
    }
    public function supportsZipCompression(){
        return extension_loaded('zip');
    }
}