<?php
/**
 * Project: skroutz-pan
 * File: Logger.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 4/9/2015
 * Time: 1:43 πμ
 * Since: TODO ${VERSION}
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace Skroutz\Log;


class Logger extends \PanWPCore\Log\Logger{
	public function clearDBLog(){
		update_option($this->logger->getName() . '_log', array());
		return $this;
	}
}