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
						'type'=>'POST', //request type
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
						'type'=>'POST', //request type
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

			if (!isFree()) {
				echo '<div class="padding-bottom10"><small>' . tt('Price will be saved (converted) in the default currency on the site', 'apartments') . ' - ' . Currency::getDefaultCurrencyModel()->name . '</small></div>';
			}

			if ($model->isPriceFromTo()) {
				echo tc('price_from') . ' ' . $form->textField($model, 'price', array('class' => 'width100 noblock'));
				echo ' ' .tc('price_to') . ' ' . $form->textField($model, 'price_to', array('class' => 'width100'));
			} else {
				echo $form->textField($model, 'price', array('class' => 'width100'));
			}

			if(!isFree()){
				echo '&nbsp;'.$form->dropDownList($model, 'in_currency', Currency::getActiveCurrencyArray(2), array('class' => 'width120'));
			} else {
				echo '&nbsp;'.param('siteCurrency', '$');
			}


			if($model->type == Apartment::TYPE_RENT){
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
	?>

	<?php
	if ($model->type == Apartment::TYPE_CHANGE) {
		echo '<div class="clear">&nbsp;</div>';
		$this->widget('application.modules.lang.components.langFieldWidget', array(
			'model' => $model,
			'field' => 'exchange_to',
			'type' => 'text'
		));
	}
	?>

    <?php
    if ($model->canShowInForm('note')){
        echo '<div class="clear">&nbsp;</div>';

        echo $form->label($model, 'note');

        $options = array();

        if (Yii::app()->user->getState('isAdmin')) { // if admin - enable upload image
            $options = array(
                'filebrowserUploadUrl' => CHtml::normalizeUrl(array('/site/uploadimage?type=imageUpload'))
            );
        }

        echo $form->textArea($model, 'note', array(
            'class' => 'width500',
        ));
    }
    ?>
</div>

<div class="tab-pane" id="tab-extended">
	<?php
	if ($model->is_free_from == '0000-00-00') {
		$model->is_free_from = '';
	}
	if ($model->is_free_to == '0000-00-00') {
		$model->is_free_to = '';
	}
	?>

	<?php if (Yii::app()->user->getState('isAdmin')) { ?>
	<div class="rowold">
		<?php echo $form->checkboxRow($model, 'is_special_offer'); ?>
	</div>
	<?php } ?>

	<?php if (Yii::app()->user->getState('isAdmin')) { ?>
	<div class="special-calendar">
		<?php echo $form->labelEx($model, 'is_free_from', array('class' => 'noblock')); ?> /
		<?php echo $form->labelEx($model, 'is_free_to', array('class' => 'noblock')); ?><br/>
		<?php
		$this->widget('application.extensions.FJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'is_free_from',
			'range' => 'eval_period',
			'language' => Yii::app()->language,

			'options' => array(
				'showAnim' => 'fold',
				'dateFormat' => 'yy-mm-dd',
				'minDate' => 'new Date()',
			),
			'htmlOptions' => array(
				'class' => 'width100 eval_period'
			),
		));
		?>
		/
		<?php
		$this->widget('application.extensions.FJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'is_free_to',
			'range' => 'eval_period',
			'language' => Yii::app()->language,

			'options' => array(
				'showAnim' => 'fold',
				'dateFormat' => 'yy-mm-dd',
				'minDate' => 'new Date()',
			),
			'htmlOptions' => array(
				'class' => 'width100 eval_period'
			),
		));
		?>
		<?php echo $form->error($model, 'is_free_from'); ?>
		<?php echo $form->error($model, 'is_free_to'); ?>
	</div>
	<?php } ?>


	<?php
	if (!isset($element)) {
		$element = 0;
	}

	if (issetModule('bookingcalendar')) {
		$this->renderPartial('//../modules/bookingcalendar/views/_form', array('apartment' => $model, 'element' => $element));
	}
	?>

<?php if($model->canShowInForm('num_of_rooms')){ ?>
	<div class="rowold">
		<?php echo $form->labelEx($model, 'num_of_rooms'); ?>
		<?php echo $form->dropDownList($model, 'num_of_rooms',
		array_merge(
			array(0 => ''),
			range(1, param('moduleApartments_maxRooms', 8))
		), array('class' => 'width50')); ?>
		<?php echo $form->error($model, 'num_of_rooms'); ?>
	</div>
	<div class="clear5"></div>
<?php } ?>

    <?php if($model->canShowInForm('floor_all')){ ?>
	<div class="rowold">
		<?php echo $form->labelEx($model, 'floor', array('class' => 'noblock')); ?> /
		<?php echo $form->labelEx($model, 'floor_total', array('class' => 'noblock')); ?><br/>
		<?php echo $form->dropDownList($model, 'floor',
		array_merge(
			array('0' => ''),
			range(1, param('moduleApartments_maxFloor', 30))
		), array('class' => 'width50')); ?> /
		<?php echo $form->dropDownList($model, 'floor_total',
		array_merge(
			array('0' => ''),
			range(1, param('moduleApartments_maxFloor', 30))
		), array('class' => 'width50')); ?>
		<?php echo $form->error($model, 'floor'); ?>
		<?php echo $form->error($model, 'floor_total'); ?>
	</div>
    <?php } ?>

    <?php if($model->canShowInForm('square')){ ?>
	<div class="rowold">
		<?php echo $form->labelEx($model, 'square'); ?>
		<?php echo $form->textField($model, 'square', array('size' => 10)); ?>
		<?php echo $form->error($model, 'square'); ?>
	</div>
	<?php } ?>

    <?php if($model->canShowInForm('window_to')){ ?>
	<div class="rowold">
		<?php echo $form->labelEx($model, 'window_to'); ?>
		<?php echo $form->dropDownList($model, 'window_to', WindowTo::getWindowTo(), array('class' => 'width150')); ?>
		<?php echo $form->error($model, 'window_to'); ?>
	</div>
    <?php } ?>

	<?php if (issetModule('metrostations')) { ?>
	<div class="rowold">
		<?php echo $form->labelEx($model, 'metroStations'); ?>
		<?php echo tt('(press and hold SHIFT button for multiply select)', 'apartments'); ?><br/>
		<?php
		echo $form->listBox($model, 'metroStations', MetroStation::getAllStations(), array('class' => 'width300', 'size' => 20, 'multiple' => 'multiple'));
		?>
		<?php echo $form->error($model, 'metroStations'); ?>
	</div>
	<?php } ?>

    <?php if($model->canShowInForm('berths')){ ?>
	<div class="rowold">
		<?php echo $form->labelEx($model, 'berths'); ?>
		<?php echo $form->textField($model, 'berths', array('class' => 'width150', 'maxlength' => 255)); ?>
		<?php echo $form->error($model, 'berths'); ?>
	</div>
    <?php } ?>

    <?php if ($model->canShowInForm('references')) { ?>
	<div class="apartment-description-item">
		<?php
		if ($categories) {
			$prev = '';
			$column1 = 0;
			$column2 = 0;
			$column3 = 0;

			$count = 0;
			foreach ($categories as $catId => $category) {
				if (isset($category['values']) && $category['values'] && isset($category['title'])) {

					if ($prev != $category['style']) {
						$column2 = 0;
						$column3 = 0;
						echo '<div class="clear">&nbsp;</div>';
					}
					$$category['style']++;
					$prev = $category['style'];
					echo '<div class="' . $category['style'] . '">';
					echo '<span class="viewapartment-subheader">' . $category['title'] . '</span>';
					echo '<ul class="no-disk">';
					foreach ($category['values'] as $valId => $value) {
						if ($value) {
							$checked = $value['selected'] ? 'checked="checked"' : '';
							echo '<li><input type="checkbox"  class="s-categorybox" id="category[' . $catId . '][' . $valId . ']" name="category[' . $catId . '][' . $valId . ']" ' . $checked . '/>
										<label for="category[' . $catId . '][' . $valId . ']" />' . $value['title'] . '</label></li>';
						}
					}
					echo '</ul>';
					echo '</div>';
					if (($category['style'] == 'column2' && $column2 == 2) || $category['style'] == 'column3' && $column3 == 3) {
						echo '<div class="clear"></div>';
					}
				}

			}
		}
		?>
		<div class="clear"></div>
	</div>

	<div class="clear">&nbsp;</div>
    <?php } ?>

	<?php
    if ($model->canShowInForm('description')) {
	$this->widget('application.modules.lang.components.langFieldWidget', array(
		'model' => $model,
		'field' => 'description',
		'type' => 'text'
	));
        echo '<div class="clear">&nbsp;</div>';
	}

    if ($model->canShowInForm('description_near')) {
	$this->widget('application.modules.lang.components.langFieldWidget', array(
		'model' => $model,
		'field' => 'description_near',
		'type' => 'text'
	));
        echo '<div class="clear">&nbsp;</div>';
    }

    if ($model->canShowInForm('address')) {
	$this->widget('application.modules.lang.components.langFieldWidget', array(
		'model' => $model,
		'field' => 'address',
		'type' => 'string'
	));
    }
	?>

</div>

	<?php

	/*if ($model->isNewRecord) {
		echo '<p>' . tt('After pressing the button "Create", you will be able to load photos for the listing and to mark the property on the map.', 'apartments') . '</p>';
	}*/

	if (Yii::app()->user->getState('isAdmin')) {
		$this->widget('bootstrap.widgets.TbButton',
			array('buttonType' => 'submit',
				'type' => 'primary',
				'icon' => 'ok white',
				'label' => $model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'),
				'htmlOptions' => array(
					'onclick' => "$('#Apartment-form').submit(); return false;",
				)
			));
	} else {
		echo '<div class="row buttons save">';
		echo CHtml::button($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'), array(
			'onclick' => "$('#Apartment-form').submit(); return false;", 'class' => 'big_button',
		));
		echo '</div>';
	}
?>


