<?php
$this->pageTitle .= ' - '.tc('Control panel');
$this->breadcrumbs = array(
    Yii::t('common', 'Control panel'),
);
?>

<h1><?php echo Yii::t('common', 'Control panel'); ?></h1>

<?php
if (param('useUserads')) {
    echo CHtml::link(tt('Manage apartments', 'apartments'), array('/userads/main/index'));
}
?>

<div class="row">
    <?php
    $errors = $model->getErrors();
    if ($errors && (isset($errors['username']) || isset($errors['email']))) {
        $display = '';
    }
    else {
        $display = 'display:none;';
    }
    ?>
    <?php echo CHtml::link(tt('Change contact info'), '#', array('class' => 'changeinfo-button')); ?>
    <div class="info-form" style="<?php echo $display; ?>">
        <?php $this->renderPartial('_info', array(
        'model' => $model,
    )); ?>
    </div>
</div>

<div class="row">
    <?php
    $errors = $model->getErrors();
    if ($errors && (isset($errors['password']) || isset($errors['password_repeat']))) {
        $display = '';
    }
    else {
        $display = 'display:none;';
    }
    ?>

    <?php echo CHtml::link(tt('Change your password'), '#', array('class' => 'changepassword-button')); ?>
    <div class="password-form" style="<?php echo $display; ?>">
        <?php $this->renderPartial('_password', array(
        'model' => $model,
    )); ?>
    </div>

</div>

<?php if (issetModule('payment')) { ?>
	<?php if(isset($model->payments)){ ?>
<div class="row">
    <?php echo CHtml::link(tc('MODULE of Payments & Payment systems '), Yii::app()->createUrl('/usercpanel/main/payments'), array('class' => 'payments-button')); ?>
</div>
	<?php } ?>
<div class="row">
	<?php echo CHtml::link(tc('Add funds to account'), Yii::app()->createUrl('/paidservices/main/index', array('paid_id' => PaidServices::ID_ADD_FUNDS)), array('class' => 'fancy')); ?>
	<?php echo '(' . $model->balance . ' ' . Currency::getDefaultCurrencyName() . ')'; ?>
</div>
<?php } ?>


<?php
Yii::app()->clientScript->registerScript('showinfo', '
	$(".changeinfo-button").click(function(){
		$(".info-form .errorSummary, .errorMessage").hide();
		$(".info-form input, .required").removeClass("error");
		$(".info-form").toggle();
		return false;
	});
	$(".changepassword-button").click(function(){
		$(".password-form .errorSummary, .errorMessage").hide();
		$(".password-form input, .required").removeClass("error");
		$(".password-form").toggle();
		return false;
	});
');
?>