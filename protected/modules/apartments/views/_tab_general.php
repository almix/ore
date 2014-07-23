<?php

	echo '<dl class="ap-descr">';
	echo '<dt>'.tt('Apartment ID').':</dt><dd>'.$data->id.'</dd>';

	if(param('useShowUserInfo')){
 		if (isset($data->user->phone) && $data->user->phone){ ?>
			<dt>
				<?php echo Yii::t('module_apartments', 'Owner phone')?>:
			</dt>
		 	<dd>
				 <span id="owner-phone"><?php echo CHtml::link(Yii::t('module_apartments', 'Show'), 'javascript: void(0);', array('onclick' => 'generatePhone()')); ?></span>&nbsp;
			</dd>
			<?php
			Yii::app()->clientScript->registerScript('generate-phone', '
				function generatePhone(){
					$("span#owner-phone").html(\'<img src="'.Yii::app()->controller->createUrl('/apartments/main/generatephone', array('id' => $data->id)).'" style="vertical-align: text-top;"/>\');
				}
			', CClientScript::POS_END);
		}

		$additionalInfo = 'additional_info_'.Yii::app()->language;
		if (isset($data->user->$additionalInfo) && !empty($data->user->$additionalInfo)){ ?>
			<dt>
				<?php echo tt('Owner additional info', 'common')?>:
			</dt>
			<dd>
				<?php echo CHtml::encode($data->user->$additionalInfo);?>
			</dd>
			<?php
		}
	}


	if($data->canShowInView('floor_all') ){
		if($data->floor || $data->floor_total){
			if($data->floor && $data->floor_total){
				echo '<dt>'.tc('Floor').':</dt>';
				echo '<dd>'.Yii::t('module_apartments', '{n} floor of {total} total', array($data->floor, '{total}' => $data->floor_total)).'</dd>';
			} else {
				if($data->floor){
					echo '<dt>'.tc('Floor').':</dt>';
					echo '<dd>'.$data->floor.'</dd>';
				}
				if($data->floor_total){
					echo '<dt>'.tt('Total number of floors', 'apartments').':</dt>';
					echo '<dd>'.$data->floor_total.'</dd>';
				}
			}
		}
	}

	if($data->canShowInView('square')){
		echo '<dt>'.Yii::t('module_apartments', 'Total square').':</dt><dd>'.$data->square.' '.tc('site_square').'</dd>';
	}

	if($data->canShowInView('berths') && $data->berths){
		echo '<dt>'.Yii::t('module_apartments', 'Number of berths').':</dt><dd>'.CHtml::encode($data->berths).'</dd>';
	}

	if($data->canShowInView('window_to') && $data->windowTo->getTitle()){
		echo '<dt>'.tt('window to').':</dt><dd>'.CHtml::encode($data->windowTo->getTitle()).'</dd>';
	}

	if($data->canShowInView('description')){
		echo '<dt>'.tt('Description').':</dt><dd>'.CHtml::encode($data->getStrByLang('description')).'</dd>';
	}

	if($data->canShowInView('description_near')){
		echo '<dt>'.tt('Near').':</dt><dd>'.CHtml::encode($data->getStrByLang('description_near')).'</dd>';
	}

	if($data->stationsTitle() || $data->canShowInView('address')){
		if($data->stationsTitle()){
			echo '<dt>'.tt('Metro').':</dt><dd>'.CHtml::encode($data->stationsTitle()).'</dd>';
		}
		$adressFull = '';

		if (issetModule('location') && param('useLocation', 1)) {
			if($data->locCountry || $data->locRegion || $data->locCity)
				$adressFull = ' ';

			if($data->locCountry){
				$adressFull .= $data->locCountry->getStrByLang('name');
			}
			if($data->locRegion){
				if($data->locCountry)
					$adressFull .=  ',&nbsp;';
				$adressFull .=  $data->locRegion->getStrByLang('name');
			}
			if($data->locCity){
				if($data->locCountry || $data->locRegion)
					$adressFull .=  ',&nbsp;';
				$adressFull .=  $data->locCity->getStrByLang('name');
			}
		} else {
			if(isset($data->city) && isset($data->city->name)){
				$cityName = $data->city->name;
				if($cityName) {
					$adressFull = ' '.$cityName;
				}
			}
		}
		$adress = CHtml::encode($data->getStrByLang('address'));
		if($adress){
			$adressFull .= ', '.$adress;
		}
		if($adressFull){
			echo '<dt>'.tt('Address').':</dt><dd>'.$adressFull.'</dd>';
		}
	}

	if($data->type == Apartment::TYPE_CHANGE && $data->getStrByLang('exchange_to')){
		echo '<dt>'.tt('Exchange to', 'apartments').':</dt><dd>'.CHtml::encode($data->getStrByLang('exchange_to')).'</dd>';
	}

    if($data->canShowInView('note')){
        echo '<dt>'.Yii::t('module_apartments', 'Note').':</dt><dd>'.$data->note.'</dd>';
    }

	echo '</dl>';
