<?php
/**
 * Project: TODO remove these anosiapharmacy
 * File: info.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 6/9/2015
 * Time: 3:21 μμ
 * Since: TODO ${VERSION}
 * Copyright: 2015 Panagiotis Vagenas
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/* @var \PanWPCore\Templates $this */
/* @var array $viewData */

$info = &$viewData['info'];
?>
<div class="bskz">
	<?php
	if ( empty( $info ) ) {
		echo $this->I18n->__( 'File not generated yet. Please use the <i>Generate XML Now</i> button to generate a new file' );
	} else {
		?>
		<div class="alert alert-success" role="alert">
			<p>
				<?php echo $this->I18n->__( 'Give the following path to skroutz.gr:' ); ?>
			</p>

			<p>
				<strong><?php echo $this->Skroutz->getGenerateXmlUrl(); ?></strong>
			</p>
		</div>

		<ul class="list-group">
			<?php
			foreach ( $info as $k => $v ) {
				echo '<li class="list-group-item">' . $v['label'] . ': <strong>' . $v['value'] . '</strong></li>';
			}
			?>
		</ul>
		<?php
	}
	?>
	<div class="pull-right">
		<a class="btn btn-primary btn-sm" href="<?php echo $info['url']['value']; ?>" target="_blank" role="button">
			<?php $this->I18n->_e( 'Open Cached File' ); ?>
		</a>
		<a class="btn btn-primary btn-sm copy-gen-url"
		   href="<?php echo $this->Skroutz->getGenerateXmlUrl(); ?>" target="_blank" role="button">
			<?php $this->I18n->_e( 'Open Generate URL' ); ?>
		</a>
	</div>
</div>