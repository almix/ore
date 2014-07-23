
<?php
if(param('qrcode_in_listing_view', 1)) {
	echo '<div class="floatright '.($data->is_special_offer ? 'qrcode_spec' : 'qrcode').'" >';
	$this->widget('application.extensions.qrcode.QRCodeGenerator', array(
		'data' =>$data->URL,
		'filename' => 'listing_'.$data->id.'-'.Yii::app()->language.'.png',
		'matrixPointSize' => 3,
		'fileUrl'=> Yii::app()->getBaseUrl(true) . '/uploads',
		'color' => array(33, 72, 131),
	));
	echo '</div>';
}
?>

	<div class="apartment-description">
		<?php
			if($data->is_special_offer){
				?>
				<div class="big-special-offer">
					<?php
					echo '<h4>'.Yii::t('common', 'Special offer!').'</h4>';

					if($data->is_free_from != '0000-00-00' && $data->is_free_to != '0000-00-00'){
						echo '<p>';
						echo Yii::t('common','Is avaliable');
						if($data->is_free_from != '0000-00-00'){
							echo ' '.Yii::t('common', 'from');
							echo ' '.Booking::getDate($data->is_free_from);

						}
						if($data->is_free_to != '0000-00-00'){
							echo ' '.Yii::t('common', 'to');
							echo ' '.Booking::getDate($data->is_free_to);
						}
						echo '</p>';
					}
					?>
				</div>
				<?php
			}
		?>

		<div class="viewapartment-main-photo">
			<div class="apartment_type"><?php echo Apartment::getNameByType($data->type); ?></div>
			<?php
				$img = null;
				$res = Images::getMainThumb(300, 200, $data->images);
				$img = CHtml::image($res['thumbUrl'], $res['comment']);
				if($res['link']){
					echo CHtml::link($img, $res['link'], array(
						'rel' => 'prettyPhoto[img-gallery]',
						'title' => $res['comment'],
					));
				} else {
					echo $img;
				}
			?>
		</div>

		<div class="viewapartment-description-top">

			<div>
				<strong>
				<?php
					echo utf8_ucfirst($data->objType->name);
					if($data->stationsTitle() && $data->num_of_rooms){
						echo ',&nbsp;';
						echo Yii::t('module_apartments',
							'{n} bedroom|{n} bedrooms|{n} bedrooms near {metro} metro station', array($data->num_of_rooms, '{metro}' => $data->stationsTitle()));
					}
					elseif ($data->num_of_rooms){
						echo ',&nbsp;';
						echo Yii::t('module_apartments',
							'{n} bedroom|{n} bedrooms|{n} bedrooms', array($data->num_of_rooms));
					}
				if (issetModule('location') && param('useLocation', 1)) {
					if($data->locCountry || $data->locRegion || $data->locCity)
						echo "<br>";

					if($data->locCountry){
						echo $data->locCountry->getStrByLang('name');
					}
					if($data->locRegion){
						if($data->locCountry)
							echo ',&nbsp;';
						echo $data->locRegion->getStrByLang('name');
					}
					if($data->locCity){
						if($data->locCountry || $data->locRegion)
							echo ',&nbsp;';
						echo $data->locCity->getStrByLang('name');
					}
				} else {
					if(isset($data->city) && isset($data->city->name)){
						echo ',&nbsp;';
						echo $data->city->name;
					}
				}

				?>
				</strong>
			</div>

			<p class="cost padding-bottom10">
				<?php if ($data->is_price_poa)
						echo tt('is_price_poa', 'apartments');
					else
						echo tt('Price from').': '.$data->getPrettyPrice();
				?>
			</p>
			<div class="overflow-auto">
				<?php
					if(($data->owner_id != Yii::app()->user->getId()) && $data->type == 1){
						echo '<div>'.CHtml::link(tt('Booking'), array('/booking/main/bookingform', 'id' => $data->id), array('class' => 'apt_btn fancy')).'</div>';
					}
					if (param('use_module_request_property') && $data->owner_id != Yii::app()->user->id){
						echo '<div class="clear-left">'.CHtml::link(tt('request_for_property'), $data->getUrlSendEmail(), array('class'=>'fancy')).'</div>';
					}
					echo '<div>'.CHtml::link(tt('all_member_listings', 'apartments'), $this->createUrl('/apartments/main/alllistings', array('id' => $data->user->id))).'</div>';

					if(issetModule('apartmentsComplain')){
						if(($data->owner_id != Yii::app()->user->getId())){ ?>
                    		<div>
								<?php echo CHtml::link(tt('do_complain', 'apartmentsComplain'), $this->createUrl('/apartmentsComplain/main/complain', array('id' => $data->id)), array('class' => 'fancy')); ?>
                    		</div>
							<?php
						}
					}
				?>
            </div>
		</div>

		<?php
			if ($data->images) {
				$this->widget('application.modules.images.components.ImagesWidget', array(
					'images' => $data->images,
					'objectId' => $data->id,
				));
			}
		?>
	</div>

	<div class="clear"></div>

	<div class="viewapartment-description">
		<?php

			$generalContent = $this->renderPartial('//../modules/apartments/views/_tab_general', array(
				'data'=>$data,
			), true);

			if($generalContent){
				$items[tc('General')] = array(
					'content' => $generalContent,
					'id' => 'tab_1',
				);
			}

			if(!param('useBootstrap')){
				Yii::app()->clientScript->scriptMap=array(
					'jquery-ui.css'=>false,
				);
			}

			if(issetModule('bookingcalendar') && $data->type == Apartment::TYPE_RENT){
				Bookingcalendar::publishAssets();

				$items[tt('The periods of booking apartment', 'bookingcalendar')] = array(
					'content' => $this->renderPartial('//../modules/bookingcalendar/views/calendar', array(
						'apartment'=>$data,
					), true),
					'id' => 'tab_2',
				);
			}

            $data->references = $data->getFullInformation($data->id, $data->type);

			if($data->canShowInView('references')){
				$items[tc('Additional info')] = array(
					'content' => $this->renderPartial('//../modules/apartments/views/_tab_addition', array(
						'data'=>$data,
					), true),
					'id' => 'tab_3',
				);
			}

			if (isset($data->video) && $data->video){
				$items[tc('Videos for listing')] = array(
					'content' => $this->renderPartial('//../modules/apartments/views/_tab_video', array(
						'data'=>$data,
					), true),
					'id' => 'tab_4',
				);
			}


			if(!Yii::app()->user->hasState('isAdmin') && (Yii::app()->user->hasFlash('newComment') || $comment->getErrors())){
				Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/scrollto.js', CClientScript::POS_END);
				Yii::app()->clientScript->registerScript('comments','
				setTimeout(function(){
					$("a[href=#tab_5]").click();
				}, 0);
				scrollto("comments");
			',CClientScript::POS_READY);
			}

			if(!isset($comment)){
				$comment = null;
			}

			$items[Yii::t('module_comments','Comments').' ('.$data->commentCount.')'] = array(
				'content' => $this->renderPartial('//../modules/apartments/views/_tab_comments', array(
					'comment'=>$comment,
					'model' => $data,
				), true),
				'id' => 'tab_5',
			);

			if ($data->type != Apartment::TYPE_BUY && $data->type != Apartment::TYPE_RENTING) {
				if($data->lat && $data->lng){
					if(param('useGoogleMap', 1) || param('useYandexMap', 1)){
						$items[tc('Map')] = array(
							'content' => $this->renderPartial('//../modules/apartments/views/_tab_map', array(
								'data' => $data,
							), true),
							'id' => 'tab_6',
						);
					}
				}
			}

			$this->widget('zii.widgets.jui.CJuiTabs', array(
				'tabs' => $items,
				'htmlOptions' => array('class' => 'info-tabs'),
				'headerTemplate' => '<li><a href="{url}" title="{title}" onclick="reInitMap(this);">{title}</a></li>',

				'options' => array(
				),
			));
		?>
	</div>
	<div class="clear">&nbsp;</div>
	<?php
		if(!Yii::app()->user->getState('isAdmin')) {
			if (issetModule('similarads') && param('useSliderSimilarAds') == 1) {
				Yii::import('application.modules.similarads.components.SimilarAdsWidget');
				$ads = new SimilarAdsWidget;
				$ads->viewSimilarAds($data);
			}
		}

		Yii::app()->clientScript->registerScript('reInitMap', '
			var useYandexMap = '.param('useYandexMap', 1).';
			var useGoogleMap = '.param('useGoogleMap', 1).';

			function reInitMap(elem) {
				if($(elem).attr("href") == "#tab_6"){
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
			}
		',
		CClientScript::POS_END);
	?>
<br />
