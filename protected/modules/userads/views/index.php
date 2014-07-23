<h1><?php echo tt('Manage apartments', 'apartments'); ?></h1>

<?php
$this->pageTitle .= ' - '.tt('Manage apartments', 'apartments');

$this->widget('zii.widgets.CMenu', array(
	'items' => array(
		array('label' => tt('Add apartment', 'apartments'), 'url'=>array('create')),
	)
));

Yii::app()->clientScript->registerScript('ajaxSetStatus', "
		function ajaxSetStatus(elem, id){
			$.ajax({
				url: $(elem).attr('href'),
				success: function(){
					$('#'+id).yiiGridView.update(id);
				}
			});
		}
	",
    CClientScript::POS_HEAD);


$columns = array(
	array(
		'name' => 'id',
		'headerHtmlOptions' => array(
			'class'=>'apartments_id_column',
		),
	),
	array(
		'name' => 'active',
		'type' => 'raw',
		'value' => 'Userads::returnStatusHtml($data, "userads-grid", 0)',
		'headerHtmlOptions' => array(
			'class'=>'userads_status_column',
		),
		'filter' => Apartment::getModerationStatusArray(),
		'sortable' => false,
	),

	array(
		'name' => 'owner_active',
		'type' => 'raw',
		'value' => 'Userads::returnStatusOwnerActiveHtml($data, "userads-grid", 1)',
		'headerHtmlOptions' => array(
			'class'=>'userads_owner_status_column',
		),
		'filter' => array(
			'0' => tc('Inactive'),
			'1' => tc('Active'),
		),
		'sortable' => false,
	),
	array(
		'name' => 'city_id',
		'value' => '$data->city_id ? $data->city->name : ""',
		'htmlOptions' => array(
			'style' => 'width: 100px;',
		),
		'sortable' => false,
		'filter' => ApartmentCity::getAllCity(),
	),
	array(
		'name' => 'type',
		'type' => 'raw',
		'value' => 'Apartment::getNameByType($data->type)',
		'filter' => Apartment::getTypesArray(),
		'htmlOptions' => array(
			'style' => 'width: 100px;',
		),
		'sortable' => false,
	),
	array(
		'header' => tc('Name'),
		'name' => 'title_'.Yii::app()->language,
		'type' => 'raw',
		'value' => 'CHtml::link(CHtml::encode($data->{"title_".Yii::app()->language}),array("/apartments/main/view","id" => $data->id))',
	),
);

if(issetModule('paidservices')){
	$columns[] = array(
		'header'=>tc('Paid services'),
		'value'=>'$data->getPaidHtml()',
		'type'=>'raw',
		'htmlOptions' => array(
			'style' => 'width: 200px;',
		),
	);
}

$columns[] = array(
	'class'=>'CButtonColumn',
	'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
	'viewButtonUrl' => "Yii::app()->createUrl('/apartments/main/view', array('id' => \$data->id))",
);

$this->widget('NoBootstrapGridView', array(
	'id'=>'userads-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>$columns
)); ?>
