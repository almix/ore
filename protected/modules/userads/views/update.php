<h1><?php echo tt('Update apartment', 'apartments'); ?></h1>

<?php
$this->pageTitle .= ' - '.tt('Update apartment', 'apartments');

if(!Yii::app()->user->isGuest){
    $menuItems = array(
    		array('label' => tt('Manage apartments', 'apartments'), 'url'=>array('index')),
    		array('label' => tt('Add apartment', 'apartments'), 'url'=>array('create')),
    		array(
    			'label' => tt('Delete apartment', 'apartments'),
    			'url'=>'#',
    			'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>tc('Are you sure you want to delete this item?'))
    ));
} else {
    $menuItems = array();
}

if(issetModule('paidservices')){
	$paidServices = PaidServices::model()->findAll('active = 1');

	foreach($paidServices as $paid){
		$menuItems[] = array(
			'label' => $paid->name,
			'url'=>array('/paidservices/main/index',
				'id'=>$model->id,
				'paid_id'=>$paid->id,
			),
			'linkOptions'=>array('class'=>'fancy')
		);
	}
}


$this->widget('zii.widgets.CMenu', array(
	'items' => $menuItems
));

if(isset($show) && $show){
	Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/scrollto.js', CClientScript::POS_END);
	Yii::app()->clientScript->registerScript('scroll-to','
			scrollto("'.CHtml::encode($show).'");
		',CClientScript::POS_READY
	);
}

//$model->metroStations = $model->getMetroStations();
$this->renderPartial('_form',array(
	'model'=>$model,
	'categories' => $categories,
	'supportvideoext' => $supportvideoext,
	'supportvideomaxsize' => $supportvideomaxsize,
));

