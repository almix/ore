<div class="<?php echo $divClass; ?>">
    <span class="search"><div class="<?php echo $textClass; ?>"><?php echo tc('Region') ?>:</div></span>

    <?php
    echo CHtml::dropDownList(
        'region',
        isset($this->selectedRegion)?$this->selectedRegion:'',
        Region::getRegionsArray((isset($this->selectedCountry) ? $this->selectedCountry : 0), 2),
        array('class' => $fieldClass . ' searchField', 'id' => 'region',
			'ajax' => array(
				'type'=>'POST', //request type
				'url'=>$this->createUrl('/location/main/getCities'), //url to call.
				//Style: CController::createUrl('currentController/methodToCall')
				'data'=>'js:"region="+$("#region").val()+"&type=0"',
				'success'=>'function(result){
							$("#city").html(result);
							$("#city").multiselect("refresh");
						}'
				//leave out the data key to pass all form values through
			)
		)
    );

    ?>
</div>