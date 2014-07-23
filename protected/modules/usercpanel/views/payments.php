<h1><?php echo tc('MODULE of Payments & Payment systems '); ?></h1>

<?php

if (!isset($model->payments)){

    echo tc('You do not have payments.');

} else {
    Yii::app()->getModule('payment');

    $this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider' => new CArrayDataProvider($model->payments),
            'columns' => array(
                array(
                    'name' => 'id',
                    'htmlOptions' => array(
                        'class' => 'id_column',
                    ),
                ),

                array(
                    'name' => tc('Status'),
                    'type' => 'raw',
                    'value' => '$data->returnStatusHtml()',
                    'htmlOptions' => array(
                        'class' => 'width240',
                    ),
                    //'filter' => CHtml::dropDownList('Payments[status]', $model->status, $model->getStatuses()),
                ),
	            array(
		            'name' => Yii::t('module_comments', 'Apartment_id'),
		            'type' => 'raw',
		            'value' => 'CHtml::link($data->apartment_id, Apartment::getUrlById($data->apartment_id))',
		            'filter' => false,
		            'sortable' => false,
	            ),
                array(
                    'header' => tc('Paid Service'),
                    'type' => 'raw',
                    'value' => 'isset($data->paidservice) ? $data->paidservice->name : ""',
                ),

                array(
                    'header' => tc('Name of Payment system'),
                    'type' => 'raw',
                    'value' => '$data->paysystem->name',
                ),

                array(
                    'header' => tc('Amount'),
                    'type' => 'raw',
                    'value' => '$data->amount . " " . $data->currency_charcode',
                    'htmlOptions' => array('style' => 'width:70px;'),
                ),

                array(
                    'header' => tc('Date created'),
                    'value' => '$data->date_created',
                    'type' => 'raw',
                    'filter' => false,
                    'htmlOptions' => array('style' => 'width:130px;'),
                ),

            ),
        )
    );
}
?>
