<?php
$this->pageTitle .= ' - '.NewsModule::t('News').' - '.CHtml::encode($model->getStrByLang('title'));

?>

<h2><?php echo CHtml::encode($model->getStrByLang('title'));?></h2>
<font class="date"><?php echo NewsModule::t('Created on').' '.$model->dateCreated; ?></font>
<p>
	<?php
		echo $model->body;
	?>
</p>