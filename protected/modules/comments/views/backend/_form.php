<div class="form">

<?php $form=$this->beginWidget('CustomForm', array(
	'id'=>$this->modelName.'-form',
	'enableAjaxValidation'=>false,
));

if(!Yii::app()->user->getState('isAdmin') && !Yii::app()->user->isGuest){
	$model->name = Yii::app()->user->username;
	$model->email = Yii::app()->user->email;
}

?>
	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>128, 'class' => 'width500')); ?>
		<?php echo $form->error($model,'name'); ?>

		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128, 'class' => 'width500')); ?>
		<?php echo $form->error($model,'email'); ?>

		<?php echo $form->labelEx($model,'body'); ?>
		<?php echo $form->textArea($model,'body',array('rows'=>3, 'cols'=>50, 'class' => 'width500')); ?>
		<?php echo $form->error($model,'body'); ?>

		<?php echo $form->labelEx($model,'rating'); ?>
		<?php $this->widget('CStarRating',array('name'=>'Comment[rating]', 'value'=>$model->rating, 'resetText' => tt('Remove the rate', 'comments'))); ?>
		<?php echo $form->error($model,'rating'); ?>

	<div class="clear">&nbsp;</div>
	<?php
	if (Yii::app()->user->isGuest){
	?>
		<?php echo $form->labelEx($model, 'verifyCode');?>
		<?php $this->widget('CCaptcha', array('captchaAction' => '/apartments/main/captcha', 'buttonOptions' => array('style' => 'display:block;') ));?><br/>
		<?php echo $form->textField($model, 'verifyCode');?><br/>
		<?php echo $form->error($model, 'verifyCode');?>
	<?php
	}
	?>

	<br/>
	<div class="clear"></div>

    <?php if (param('useBootstrap')) : ?>
        <?php $this->widget('bootstrap.widgets.TbButton',
            array('buttonType'=>'submit',
                'type'=>'primary',
                'icon'=>'ok white',
                'label'=> $model->isNewRecord ? tc('Add') : tc('Save'),
            )
        );
    else : ?>
        <div class="row buttons">
            <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('common', 'Add') : Yii::t('common', 'Save')); ?>
        </div>
    <?php endif; ?>

<?php $this->endWidget(); ?>

</div><!-- form -->