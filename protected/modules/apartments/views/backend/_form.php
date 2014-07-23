<?php
Yii::app()->getClientScript()->registerScriptFile('https://maps.google.com/maps/api/js??v=3.5&sensor=false&language='.Yii::app()->language.'', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile( Yii::app()->baseUrl. '/js/AMap.js' );
Yii::app()->clientScript->registerCssFile( Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css' );

Yii::app()->clientScript->registerScript('redirectType', "
    $(document).ready(function() {
        var BASE_URL = ".CJavaScript::encode(Yii::app()->baseUrl).";

        $('#obj_type, #ap_type').live('change', function() {
            $('#update_overlay').show();
            $('#is_update').val(1);
            $('#Apartment-form').submit(); return false;
        });
    });
	",
    CClientScript::POS_HEAD);
?>

<div class="form">
    <div id="update_overlay"><p><?php echo tc('Loading content...');?></p></div>

<?php
	$ajaxValidation = false;
	if(!$model->isNewRecord){
		$htmlOptions = array('enctype' => 'multipart/form-data');
	}else{
        $htmlOptions = array();
	}

    /** @var $form BootActiveForm */
	$form = $this->beginWidget('CustomForm', array(
		'id'=>$this->modelName.'-form',
		'enableAjaxValidation'=>$ajaxValidation,
		'htmlOptions'=> $htmlOptions,
	));

	?>

	<?php if(!$model->isNewRecord){ ?>
		<p>
			<strong><?php echo tt('Apartment ID', 'apartments'); ?></strong>: <?php echo $model->id; ?>
		</p>
	<?php } ?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->labelEx($model,'active'); ?>
	<?php echo $form->dropDownList($model, 'active', array(
		'1' => tt('Active', 'apartments'),
		'0' => tt('Inactive', 'apartments'),
	), array('class' => 'width150')); ?>
	<?php echo $form->error($model,'active'); ?>

    <div class="tabbable">
        <ul class="nav nav-tabs" id="myTabs">
            <li class="active"><a href="#tab-main" data-toggle="tab"><?php echo tc('General'); ?></a></li>
            <li><a href="#tab-extended" data-toggle="tab"><?php echo tc('Addition'); ?></a></li>

			<?php if($model->type != Apartment::TYPE_BUY && $model->type != Apartment::TYPE_RENTING) :?>
				<li><a href="#tab-images" data-toggle="tab"><?php echo tc('Photos for listing'); ?></a></li>
			<?php endif; ?>

			<?php if($model->type != Apartment::TYPE_BUY && $model->type != Apartment::TYPE_RENTING) :?>
				<li><a href="#tab-videos" data-toggle="tab"><?php echo tc('Videos for listing'); ?></a></li>
			<?php endif; ?>

			<?php
			if(!$model->isNewRecord  && (param('useGoogleMap', 1) || param('useYandexMap', 1))
				&& $model->type != Apartment::TYPE_BUY
				&& $model->type != Apartment::TYPE_RENTING
			){
				?>
				<li><a href="#tab-map" data-toggle="tab" onclick="reInitMap();"><?php echo tc('Map'); ?></a></li>
			<?php } ?>
        </ul>
    </div>

	<div class="tab-pane" id="tab-map">
	<?php
	if(!$model->isNewRecord  && (param('useGoogleMap', 1) || param('useYandexMap', 1))
			&& $model->type != Apartment::TYPE_BUY
			&& $model->type != Apartment::TYPE_RENTING)
	{
		if(param('useGoogleMap', 1) || param('useYandexMap', 1)){
			echo '<div class="flash-notice">'.tc('Click on the map to set the location of an object or move an existing marker.').'</div>';
		}

		if (param('useGoogleMap', 1)){
			?>

			<div class="clear">&nbsp;</div>
			<div id="gmap">
				<?php echo $this->actionGmap($model->id, $model); ?>
			</div>
			<div class="clear">&nbsp;</div>
			<?php
		} elseif (param('useYandexMap', 1)) {
			?>
			<div class="clear">&nbsp;</div>
			<div id="ymap">
				<?php echo $this->actionYmap($model->id, $model); ?>
			</div>
			<div class="clear">&nbsp;</div>
			<?php
		}?>

		<div class="form-inline">
			<?php
			echo CHtml::textField('address_for_map', '', array('class' => 'width300'));
			$this->widget('bootstrap.widgets.TbButton',
				array(
					'buttonType' => 'link',
					'icon' => 'search',
					'label' => tc('Set a marker by address'),
					'htmlOptions' => array(
						'onclick' => "findByAddress(); return false;",
					)
				));
			?>
		</div>
		<br/>
		<?php } ?>
	</div>

	<?php if($model->type != Apartment::TYPE_BUY && $model->type != Apartment::TYPE_RENTING) : ?>
		<div class="tab-pane" id="tab-images">
			<div class="flash-notice"><?php echo tc('You can change the order of photos, holding and dragging the left area of the block.'); ?></div>
			<?php
			$this->widget('application.modules.images.components.AdminImagesWidget', array(
				'objectId' => $model->id,
			));
			?>
		</div>
	<?php endif; ?>

	<?php if($model->type != Apartment::TYPE_BUY && $model->type != Apartment::TYPE_RENTING) : ?>
		<div class="tab-pane" id="tab-videos">
			<div class="flash-notice"><?php echo tc('You can upload a video or code.'); ?></div>

				<?php if (isset($model->video) && $model->video) : ?>
					<div class="manage-video-block">
						<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/flowplayer/flowplayer-3.2.12.min.js', CClientScript::POS_END); ?>
						<?php foreach ($model->video as $video) :?>
							<div class="manage-video-item">
								<?php
									echo CHtml::link(
										CHtml::image(Yii::app()->baseUrl.'/images/video-file-icon.png'),
										Yii::app()->controller->createUrl('/apartments/main/getvideofile', array('id' => $video->id, 'apId' => $model->id)),
										array(
											'id' => 'video-file-'.$video->id,
											'class' => 'video-fancy',
										)
									);
								?>

								<div><?php echo CHtml::link(tc('Delete'), Yii::app()->controller->createUrl('deletevideo', array('id' => $video->id, 'apId' => $model->id))); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php
					Yii::app()->clientScript->registerScript('video-file-js', '
						$(".video-fancy").fancybox({
							"transitionIn" : "none",
							"transitionOut" : "none"
						});
					', CClientScript::POS_READY);
				?>
				<?php endif; ?>


				<div class="rowold">
					<?php echo $form->labelEx($model,'video_html'); ?>
					<?php echo $form->textArea($model,'video_html',array('class'=>'width500 height100')); ?>
					<br />
					<?php echo $form->error($model,'video_html'); ?>
				</div>

				<div class="rowold">
					<?php echo $form->labelEx($model,'video_file'); ?>
					<?php echo $form->fileField($model, 'video_file'); ?>
					<div class="padding-bottom10">
						<span class="label label-info">
							<?php echo Yii::t('module_apartments', 'Supported file: {supportExt}.', array('{supportExt}' => $supportvideoext));?>
						</span>
									<br />
						<span class="label label-info">
							<?php echo Yii::t('module_apartments', 'videoMaxSite: {size}.', array('{size}' => formatBytes($supportvideomaxsize)));?>
						</span>
					</div>
					<?php echo $form->error($model,'video_file'); ?>
				</div>
		</div>
	<?php endif; ?>

	<?php
	$this->renderPartial('__form', array(
		'form' => $form,
		'model' => $model,
		'categories' => $categories,
	));
	?>

	<?php $this->endWidget(); ?><!-- form -->
</div>

<?php

if(issetModule('paidservices')){
	echo '<div class="well">';
	echo '<h6>'.tc('Current paid services').'</h6>';
	echo $model->getPaidHtml(true, true);
	echo '</div>';
}

if (issetModule('seo') && !$model->isNewRecord && $model->active != Apartment::STATUS_DRAFT) {
	$this->widget('application.modules.seo.components.SeoWidget', array(
		'model' => $model,
	));
} ?>

<?php

	// reInit google map (for preventing incorrect work in hidden tab)
	Yii::app()->clientScript->registerScript('gmap-init', '
		var useYandexMap = '.param('useYandexMap', 1).';
		var useGoogleMap = '.param('useGoogleMap', 1).';
		var lang = "'.Yii::app()->language.'";

		function reInitMap(){
			var address = $("#Apartment_city_id").length ? $("#Apartment_city_id option:selected").html() :
				$("#ap_country option:selected").html()+", "+$("#ap_city option:selected").html();
			address = address + ", " + $("#id_Apartmentaddress_"+lang).val();
			$("#address_for_map").val(address);

			// place code to end of queue
			if(useGoogleMap){
				setTimeout(function(){
					var tmpGmapCenter = mapGMap.getCenter();

					google.maps.event.trigger($("#googleMap")[0], "resize");
					mapGMap.setCenter(tmpGmapCenter);
				}, 0);
			}

			if(useYandexMap){
				setTimeout(function(){
					ymaps.ready(function () {
						globalYMap.container.fitToViewport();
						globalYMap.setCenter(globalYMap.getCenter());
					});
				}, 0);
			}
		}

		function findByAddress(){
			var address = $("#address_for_map").val();
			if(!address){
				error("'.tc('Please enter address').'");
				return false;
			}
			AGeoCoder.getDataByAddress(address, {
				success: function(){
					if(useGoogleMap && typeof markersGMap['.$model->id.'] !== "undefined" && typeof mapGMap !== "undefined"){
						var latLng = new google.maps.LatLng(AGeoCoder.geoData.lat, AGeoCoder.geoData.lng);
						markersGMap['.$model->id.'].setPosition(latLng);
						mapGMap.setCenter(latLng);
					}
					if(useYandexMap && typeof placemark !== "undefined" && typeof globalYMap !== "undefined"){
						placemark.geometry.setCoordinates([AGeoCoder.geoData.lng, AGeoCoder.geoData.lat]);
						globalYMap.setCenter([AGeoCoder.geoData.lng, AGeoCoder.geoData.lat]);
					}
					setMarker(AGeoCoder.geoData.lat, AGeoCoder.geoData.lng);
				}
			});
		}

		function setMarker(lat, lng){
			$.ajax({
				type:"POST",
				url:"'.Yii::app()->controller->createUrl('savecoords', array('id' => $model->id) ).'",
				data:({lat: lat, lng: lng}),
				cache:false
			})
		}
	', CClientScript::POS_END);

	Yii::app()->clientScript->registerScript('show-special', '
		//special-calendar
		if(!$("#Apartment_is_special_offer").is(":checked")){
			$(".special-calendar").hide();
		}
		$("#Apartment_is_special_offer").bind("change", function(){
			if($(this).is(":checked")){
				$(".special-calendar").show();
			} else {
				$(".special-calendar").hide();
			}
		});

		// price poa
		if($("#Apartment_is_price_poa").is(":checked")){
			$("#price_fields").hide();
		}
		$("#Apartment_is_price_poa").bind("change", function(){
			if($(this).is(":checked")){
				$("#price_fields").hide();
			} else {
				$("#price_fields").show();
			}
		});
	', CClientScript::POS_READY);
?>