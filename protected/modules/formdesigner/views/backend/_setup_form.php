<?php
/** @var CustomForm $form */
$form=$this->beginWidget('CustomForm', array(
    'id'=>'form-designer-filter'
));

echo CHtml::hiddenField('id', $model->id);

echo CHtml::hiddenField('FormDesigner[save]', $model->id);

echo $form->dropDownListRow($model, 'objTypes', ApartmentObjType::getList(), array('multiple' => 'multiple'));

echo '<div class="clear"></div>';

$this->widget('bootstrap.widgets.TbButton',
    array(
        'type' => 'primary',
        'icon' => 'ok white',
        'label' => tc('Apply'),
        'htmlOptions' => array(
            'onclick' => "formSetup.apply(); return false;",
        )
    ));

$this->endWidget();
?>