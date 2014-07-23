<?php
$this->pageTitle .= ' - '.Yii::t('module_specialoffers', 'Special offers');
$this->breadcrumbs=array(
	Yii::t('module_specialoffers', 'Special offers'),
);
?>
<?php
$this->widget('application.modules.apartments.components.ApartmentsWidget', array(
	'criteria' => $criteria,
	'widgetTitle' => Yii::t('common', 'Special offers'),
));
?>
