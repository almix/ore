<div class="tab-pane active" id="tab-main">
<div class="rowold">
    <?php echo $form->labelEx($model, 'type'); ?>
    <?php echo $form->dropDownList($model, 'type', Apartment::getTypesArray(), array('class' => 'width240', 'id' => 'ap_type')); ?>
    <?php echo $form->error($model, 'type'); ?>
</div>

<div class="rowold">
    <?php echo $form->labelEx($model, 'obj_type_id'); ?>
    <?php echo $form->dropDownList($model, 'obj_type_id', Apartment::getObjTypesArray(), array('class' => 'width240', 'id' => 'obj_type')); ?>
    <?php echo $form->error($model, 'obj_type_id'); ?>
</div>

<?php if (issetModule('location') && param('useLocation', 1)): ?>
    <?php $countries = Country::getCountriesArray();?>
    <div class="rowold">
        <?php echo $form->labelEx($model,'loc_country'); ?>
        <?php echo $form->dropDownList($model,'loc_country',$countries,
            array('id'=>'ap_country',
                'ajax' => array(
                    'type'=>'GET', //request type
                    'url'=>$this->createUrl('/location/main/getRegions'), //url to call.
                    //Style: CController::createUrl('currentController/methodToCall')
                    'data'=>'js:"country="+$("#ap_country").val()',
                    'success'=>'function(result){
								$("#ap_region").html(result);
								$("#ap_region").change();
							}'
                    //leave out the data key to pass all form values through
                )
            )
        ); ?>
        <?php echo $form->error($model,'loc_country'); ?>
    </div>

    <?php
    //при создании города узнаём id первой в дропдауне страны
    if ($model->loc_country) {
        $country = $model->loc_country;
    } else {
        $country_keys = array_keys($countries);
        $country = isset($country_keys[0]) ? $country_keys[0] : 0;
    }

    $regions=Region::getRegionsArray($country);

    if ($model->loc_region) {
        $region = $model->loc_region;
    } else {
        $region_keys = array_keys($regions);
        $region = isset($region_keys[0]) ? $region_keys[0] : 0;
    }

    $cities = City::getCitiesArray($region);

    if ($model->loc_city) {
        $city = $model->loc_city;
    } else {
        $city_keys = array_keys($cities);
        $city = isset($city_keys[0]) ? $city_keys[0] : 0;
    }
    ?>

    <div class="rowold">
        <?php echo $form->labelEx($model,'loc_region'); ?>
        <?php echo $form->dropDownList($model,'loc_region',$regions,
            array('id'=>'ap_region',
                'ajax' => array(
                    'type'=>'GET', //request type
                    'url'=>$this->createUrl('/location/main/getCities'), //url to call.
                    //Style: CController::createUrl('currentController/methodToCall')
                    'data'=>'js:"region="+$("#ap_region").val()',
                    'success'=>'function(result){
								$("#ap_city").html(result);
						}'

                )
            )
        ); ?>
        <?php echo $form->error($model,'loc_region'); ?>
    </div>

    <div class="rowold">
        <?php echo $form->labelEx($model,'loc_city'); ?>
        <?php echo $form->dropDownList($model,'loc_city',$cities,array('id'=>'ap_city')); ?>
        <?php echo $form->error($model,'loc_city'); ?>
    </div>

<?php else: ?>

    <div class="rowold">
        <?php echo $form->labelEx($model, 'city_id'); ?>
        <?php echo $form->dropDownList($model, 'city_id', Apartment::getCityArray(), array('class' => 'width240')); ?>
        <?php echo $form->error($model, 'city_id'); ?>
    </div>

<?php endif; ?>

<?php
if ($model->canShowInForm('address')) {
    $this->widget('application.modules.lang.components.langFieldWidget', array(
        'model' => $model,
        'field' => 'address',
        'type' => 'string'
    ));
}
?>

<div class="rowold no-mrg">
    <?php
    echo $form->label($model, 'price', array('required' => true));
    ?>

    <?php echo $form->checkbox($model, 'is_price_poa'); ?>
    <?php echo $form->labelEx($model, 'is_price_poa', array('class' => 'noblock')); ?>
    <?php echo $form->error($model, 'is_price_poa'); ?>

    <div id="price_fields">
        <?php
        echo CHtml::hiddenField('is_update', 0);

//        if (issetModule('currency')) {
//            echo '<div class="padding-bottom10"><small>' . tt('Price will be saved (converted) in the default currency on the site', 'apartments') . ' - ' . Currency::getDefaultCurrencyModel()->name . '</small></div>';
//        }

        if ($model->isPriceFromTo()) {
            echo tc('price_from') . ' ' . $form->textField($model, 'price', array('class' => 'width100 noblock'));
            echo ' ' .tc('price_to') . ' ' . $form->textField($model, 'price_to', array('class' => 'width100'));
        } else {
            echo $form->textField($model, 'price', array('class' => 'width100'));
        }

        if(issetModule('currency')){
            // Даем вводить ценую только в дефолтной валюте
            echo '&nbsp;' . Currency::getDefaultCurrencyName();
            $model->in_currency = Currency::getDefaultCurrencyModel()->char_code;
            echo $form->hiddenField($model, 'in_currency');
            // $form->dropDownList($model, 'in_currency', Currency::getActiveCurrencyArray(2), array('class' => 'width120'))
        } else {
            echo '&nbsp;'.param('siteCurrency', '$');
        }

        if($model->type == Apartment::TYPE_RENT){
            $priceArray = Apartment::getPriceArray($model->type);
            if(!in_array($model->price_type, array_keys($priceArray))){
                $model->price_type = Apartment::PRICE_PER_MONTH;
            }
            echo '&nbsp;'.$form->dropDownList($model, 'price_type', Apartment::getPriceArray($model->type), array('class' => 'width150'));
        }
        ?>
    </div>

    <?php echo $form->error($model, 'price'); ?>
</div>
<div class="clear"></div>
<?php
$this->widget('application.modules.lang.components.langFieldWidget', array(
    'model' => $model,
    'field' => 'title',
    'type' => 'string'
));

echo '<br/>';

if ($model->canShowInForm('description')) {
    $this->widget('application.modules.lang.components.langFieldWidget', array(
        'model' => $model,
        'field' => 'description',
        'type' => 'text'
    ));
    echo '<div class="clear">&nbsp;</div>';
}
?>

<?php if($model->canShowInForm('square')){ ?>
    <div class="rowold">
        <?php echo $form->labelEx($model, 'square'); ?>
        <?php echo Apartment::getTip('square');?>
        <?php echo $form->textField($model, 'square', array('size' => 5, 'class' => 'width70')).' '.tc('site_square'); ?>
        <?php echo $form->error($model, 'square'); ?>
    </div>
<?php } ?>

<?php if($model->canShowInForm('land_square')){ ?>
    <div class="rowold">
        <?php echo $form->labelEx($model, 'land_square'); ?>
        <?php echo Apartment::getTip('land_square');?>
        <?php echo $form->textField($model, 'land_square', array('size' => 5, 'class' => 'width70')).' '.tc('site_land_square'); ?>
        <?php echo $form->error($model, 'land_square'); ?>
    </div>
<?php } ?>

<?php
if ($model->type == Apartment::TYPE_CHANGE) {
    echo '<div class="clear">&nbsp;</div>';
    $this->widget('application.modules.lang.components.langFieldWidget', array(
        'model' => $model,
        'field' => 'exchange_to',
        'type' => 'text'
    ));
}

if(issetModule('formeditor')){
    Yii::import('application.modules.formeditor.models.HFormEditor');
    $rows = HFormEditor::getGeneralFields();
    HFormEditor::renderFormRows($rows, $model);
}

$canSet = $model->canSetPeriodActivity() ? 1 : 0;

echo '<div class="rowold" id="set_period" ' . ( !$canSet ? 'style="display: none;"' : '' ) . '>';
echo $form->labelEx($model, 'period_activity');
echo $form->dropDownList($model, 'period_activity', Apartment::getPeriodActivityList());
echo CHtml::hiddenField('set_period_activity', $canSet);
echo $form->error($model, 'period_activity');
echo '</div>';

if(!$canSet) {
    echo '<div id="date_end_activity"><b>'.Yii::t('common', 'The listing will be active till {DATE}', array('{DATE}' => $model->getDateEndActivityLongFormat())).'</b>';
    echo '&nbsp;' . CHtml::link(tc('Change'), 'javascript:;', array(
            'onclick' => '$("#date_end_activity").hide(); $("#set_period_activity").val(1); $("#set_period").show();',
        ));
    echo '</div>';
}

?>

</div>