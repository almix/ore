<?php
if ($data->type != Apartment::TYPE_BUY && $data->type != Apartment::TYPE_RENTING) {
	if(($data->lat && $data->lng) || Yii::app()->user->getState('isAdmin')){
		if(param('useGoogleMap', 1)){
			?>
        <div id="gmap">
			<?php echo $this->actionGmap($data->id, $data); ?>
        </div>
		<?php
		}
		if(param('useYandexMap', 1)){
			?>
        <div class="row" id="ymap">
			<?php echo $this->actionYmap($data->id, $data); ?>
        </div>
		<?php
		}
	}
}
