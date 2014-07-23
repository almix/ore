<?php
Yii::app()->clientScript->registerCoreScript( 'jquery.ui' );
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.multiselect.min.js');

Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/ui/jquery-ui.multiselect.css');
?>

<div class="<?php echo $divClass; ?>">
    <span class="search"><div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'City') ?>:</div></span>

    <?php

	echo CHtml::dropDownList(
		'city[]',
		isset($this->selectedCity)?$this->selectedCity:'',
		(issetModule('location') && param('useLocation', 1)) ?
			(City::getCitiesArray((isset($this->selectedRegion) ? $this->selectedRegion : 0), 0)) :
			$this->cityActive,
		array('class' => $fieldClass.' height17 searchField', 'multiple' => 'multiple')
	);

    Yii::app()->clientScript->registerScript('select-city', '
			$("#city")
				.multiselect({
					noneSelectedText: "'.Yii::t('common', 'select city').'",
					checkAllText: "'.Yii::t('common', 'check all').'",
					uncheckAllText: "'.Yii::t('common', 'uncheck all').'",
					selectedText: "'.Yii::t('common', '# of # selected').'",
					minWidth: '.$minWidth.',
					classes: "search-input-new search-city-height",
					multiple: "false",
					selectedList: 1
				}).multiselectfilter({
					label: "'.Yii::t('common', 'quick search').'",
					placeholder: "'.Yii::t('common', 'enter initial letters').'",
					width: 185
				});
		', CClientScript::POS_READY);
    ?>
</div>