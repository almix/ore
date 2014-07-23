<?php
$modeListShow = User::getModeListShow();

Yii::app()->clientScript->registerScript('ajaxSetStatus', "
	var updateText = '" . Yii::t('common', 'Loading ...') . "';
	var resultBlock = 'appartment_box';
	var indicator = '" . Yii::app()->request->baseUrl . "/images/pages/indicator.gif';
	var bg_img = '" . Yii::app()->request->baseUrl . "/images/pages/opacity.png';

	var useGoogleMap = ".param('useGoogleMap', 0).";
	var useYandexMap = ".param('useYandexMap', 0).";

	var list = {
		lat: 0,
		lng: 0,

		apply: function(){}
	}
",
CClientScript::POS_END);

if ($modeListShow == 'map') {
	Yii::app()->clientScript->registerScript('apartmentPlacemarksOnMap', "
		var placemarksYmap = [];

		var list = {
			lat: 0,
			lng: 0,

			apply: function(){
				$('div.appartment_item').each(function(){
					var item = $(this);

					item.mouseover(function(){

						var ad = $(this);
						var lat = ad.attr('lat') + 0;
						var lng = ad.attr('lng') + 0;
						var id = ad.attr('ap_id');

						if((list.lat != lat || list.lng != lng) && lat > 0 && lng > 0 ){
							list.lat = lat;
							list.lng = lng;

							if(useGoogleMap){
								if(typeof infoWindowsGMap !== 'undefined' && typeof infoWindowsGMap[id] !== 'undefined'){
									for(var key in infoWindowsGMap){
										if(key == id){
											infoWindowsGMap[key].open();
										}else{
											infoWindowsGMap[key].close();
										}
									}
									var latLng = new google.maps.LatLng(lat, lng);

									mapGMap.panTo(latLng);
									infoWindowsGMap[id].open(mapGMap, markersGMap[id]);
								}
							}

							if(useYandexMap){
								if(typeof placemarksYMap[id] !== 'undefined'){
									placemarksYMap[id].balloon.open();
								}
							}
						}
					});
				});
			}
		}

		$(function(){
			if(useGoogleMap){
				if(typeof list !== 'undefined'){
					list.apply();
				}
			}
		});

		",
		CClientScript::POS_END);
	}
?>

<div class="title_list">
    <h2>
		<?php
		if ($this->widgetTitle !== null) {
			echo $this->widgetTitle . (isset($count) && $count ? ' (' . $count . ')' : '');
		} else {
			echo Yii::t('module_apartments', 'Apartments list') . (isset($count) && $count ? ' (' . $count . ')' : '');
		}
		?>
    </h2>

	<?php
	$route = Controller::getCurrentRoute();

	$urlsSwitching = array(
		'block' => Yii::app()->createUrl($route, array('ls'=>'block') + $_GET, '&'),
		'table' => Yii::app()->createUrl($route, array('ls'=>'table') + $_GET, '&'),
		'map' => Yii::app()->createUrl($route, array('ls'=>'map') + $_GET, '&'),
	);

	Yii::app()->clientScript->registerScript('setListShow', "
			function setListShow(mode){
				var urlsSwitching = ".CJavaScript::encode($urlsSwitching).";
				reloadApartmentList(urlsSwitching[mode]);
			};
		",
		CClientScript::POS_END);
	?>

	<div class="change_list_show">
		<a href="<?php echo $urlsSwitching['block']; ?>" <?php if ($modeListShow == 'block') {
			echo 'class="active_ls"';
		} ?>
		   onclick="setListShow('block'); return false;">
			<img src="<?php echo Yii::app()->getBaseUrl(); ?>/images/pages/block.png">
		</a>

		<a href="<?php echo $urlsSwitching['table']; ?>" <?php if ($modeListShow == 'table') {
			echo 'class="active_ls"';
		} ?>
		   onclick="setListShow('table'); return false;">
			<img src="<?php echo Yii::app()->getBaseUrl(); ?>/images/pages/table.png">
		</a>

		<a href="<?php echo $urlsSwitching['map']; ?>" <?php if ($modeListShow == 'map') {
			echo 'class="active_ls"';
		} ?>
		   onclick="setListShow('map'); return false;">
			<img src="<?php echo Yii::app()->getBaseUrl(); ?>/images/pages/map.png">
		</a>
	</div>
</div>

<div class="clear"></div>

<?php
if ($modeListShow != 'map' && $sorterLinks) {
	foreach ($sorterLinks as $link) {
		echo '<div class="sorting">' . $link . '</div>';
	}
}
?>

<div class="appartment_box" id="appartment_box">
	<?php
	if ($apCount) {

		if ($modeListShow == 'block') {

			$this->render('widgetApartments_list_item', array('criteria' => $criteria));

		} elseif ($modeListShow == 'map') {

			$this->render('widgetApartments_list_map', array('criteria' => $criteria));

			//$this->widget('application.modules.viewallonmap.components.ViewallonmapWidget', array('criteria' => $criteria, 'filterOn' => false));

		} else {

			$this->widget('zii.widgets.grid.CGridView', array(
					'dataProvider' => new CActiveDataProvider('Apartment', array(
						'criteria'=>$criteria,
						'pagination'=>false,
					)),
					'rowCssClassExpression' => '$data->getRowCssClass()',
					'enablePagination'=>false,
					'afterAjaxUpdate' => "function() {
						jQuery('#News_date_created').datepicker(jQuery.extend(jQuery.datepicker.regional['ru'],{'showAnim':'fold','dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true'}));
					}",
					'template' => '{items}{pager}',
					'columns' => array(
						/*array(
							'name' => 'id',
						),
						array(
							'header' => tc('Photo'),
							'value' => '(isset($data->images) && count($data->images) > 0) ? \'<img alt="'.tc('With photo').'" src="'.Yii::app()->baseUrl.'/images/with-photo.png">\' : tc("No")',
							'type' => 'raw'
						),*/
						array(
							'header' => tt('Type', 'apartments'),
							'value' => 'Apartment::getNameByType($data->type)'
						),
						array(
							'header' => tt('Apartment title', 'apartments'),
							'value' => 'CHtml::link($data->getTitle(), $data->url)',
							'type' => 'raw'
						),
						array(
							'header' => tt('Address', 'apartments'),
							'value' => '$data->getStrByLang("address")'
						),
						array(
							'header' => tt('Object type', 'apartments'),
							'type' => 'raw',
							'value' => '$data->getObjType4table()'
						),
						array(
							'header' => tt('Square', 'apartments'),
							'type' => 'raw',
							'value' => '$data->square." ".tc("site_square")',
							//'value' => 'Yii::t("module_apartments", "total square: {n}", $data->square)'
						),
						array(
							'header' => tt('Price', 'apartments'),
							'value' => '$data->getPrettyPrice()'
						),
						array(
							'header' => tt('Floor', 'apartments'),
							'type' => 'raw',
							'value' => '$data->floor == 0 ? tc("floors").":&nbsp;".$data->floor_total : $data->floor."/".$data->floor_total ;',
						),
					)
				)
			);
		}
	}
	?>

</div>


<?php
if (!$apCount) {
	echo Yii::t('module_apartments', 'Apartments list is empty.');
}

if ($pages) {
	$this->widget('itemPaginator', array('pages' => $pages, 'header' => '', 'htmlOption' => array('onClick' => 'reloadApartmentList(this.href); list.apply(); return false;')));
}
?>