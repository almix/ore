<?php
$this->adminTitle = tc('The forms designer');

Yii::app()->clientScript->registerScript('search', "
$('#form-designer-filter').submit(function(){
    $('#form-designer-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});

function ajaxSetVisible(elem){
	$.ajax({
		url: $(elem).attr('href'),
		success: function(){
			$('#form-designer-grid').yiiGridView.update('form-designer-grid');
		}
	});
}
");

$this->widget('CustomGridView', array(
    'id'=>'form-designer-grid',
    'dataProvider'=>$model->search(),
    'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
    //'filter'=>$model,
    'columns'=>array(
        array(
            'name' => 'field',
            'value' => 'Apartment::model()->getAttributeLabel($data->field)'
        ),
        array(
            'header' => 'Показывать для',
            'value' => '$data->getTypesHtml()',
            'type' => 'raw',
            'sortable' => false,
        ),
        array(
            'name' => 'visible',
            'value' => '$data->getVisibleHtml()',
            'type' => 'raw',
            'sortable' => false,
        ),

//        array(
//            'class'=>'CButtonColumn',
//        ),
    ),
));
?>

<script type="text/javascript">
    $('.formd_link').live('click', function(){
        console.log($(this).data('id'));
    });
</script>