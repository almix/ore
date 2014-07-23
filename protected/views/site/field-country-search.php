<div class="<?php echo $divClass; ?>">
    <span class="search"><div class="<?php echo $textClass; ?>"><?php echo tc('Country') ?>:</div></span>

    <?php
    echo CHtml::dropDownList(
        'country',
        isset($this->selectedCountry)?$this->selectedCountry:'',
        Country::getCountriesArray(2),
        array('class' => $fieldClass . ' searchField', 'id' => 'country',
			'ajax' => array(
				'type'=>'POST', //request type
				'url'=>$this->createUrl('/location/main/getRegions'), //url to call.
				//Style: CController::createUrl('currentController/methodToCall')
				'data'=>'js:"country="+$("#country").val()+"&type=2"',
				'success'=>'function(result){
							$("#region").html(result);
							$("#region").change();
						}'
				//leave out the data key to pass all form values through
			)
		)
    );

    ?>
</div>