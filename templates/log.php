<?php
/**
 * Project: anosiapharmacy
 * File: log.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 6/9/2015
 * Time: 3:55 μμ
 * Since: TODO ${VERSION}
 * Copyright: 2015 Panagiotis Vagenas
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/* @var \PanWPCore\Templates $this */
/* @var array $viewData */

?>
<div class="log-container">
	<?php
	foreach ( $viewData['logArray'] as $index => $log ) {
		if ( $viewData['level'] == $log['level'] ) {
			echo $log['message'];
		}
	}
	?>
</div>
