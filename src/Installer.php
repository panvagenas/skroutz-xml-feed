<?php
/**
 * Project: anosiapharmacy
 * File: Installer.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 10/9/2015
 * Time: 11:27 μμ
 * Since: TODO ${VERSION}
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace Skroutz;

class Installer extends \PanWPCore\Installer{
	/**
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public function activation(){
		$xmlInterval = $this->Options->get('xml_interval', 0);
		if(!is_numeric($xmlInterval)){
			switch($xmlInterval){
				case 'every30m':
				case 'hourly':
					$intervalInHours = 1;
					break;
				case 'twicedaily':
					$intervalInHours = 12;
					break;
				case 'daily':
				default:
					$intervalInHours = 24;
					break;
			}
			$this->Options->set('xml_interval', $intervalInHours);
		}
		return true;
	}

	/**
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public function deactivation(){
		return true;
	}

	/**
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public static function uninstall(){
		$plugin = new Plugin(__NAMESPACE__, dirname(dirname(__FILE__)).'/plugin.php', 'Skroutz.gr XML Feed', '*', 'skroutz-xml-feed', 'skroutz_xml_feed');
		delete_option($plugin->Options->getOptName());
		return true;
	}
}