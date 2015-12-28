<?php
/**
 * Options.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\SkroutzXML;

/**
 * Class Options
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class Options extends \Pan\MenuPages\Options{
    /**
     * @var array Availability options for skroutz.gr
     */
    public $availOptions = array(
        'Άμεση παραλαβή / Παράδοση σε 1-3 ημέρες ',
        'Παράδοση σε 1-3 ημέρες',
        'Παραλαβή από το κατάστημα ή Παράδοση, σε 1-3 ημέρες',
        'Παραλαβή από το κατάστημα ή Παράδοση, σε 4-10 ημέρες',
        'Παράδοση σε 4-10 ημέρες',
        'Κατόπιν παραγγελίας, παραλαβή ή παράδοση έως 30 ημέρες',
    );

    public function setUp(){}
}