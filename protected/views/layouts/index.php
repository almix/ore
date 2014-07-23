<?php $this->beginContent('//layouts/main'); ?>
	<div id="homeheader">
		<div class="slider-wrapper theme-default">
			<div id="slider" class="nivoSlider">
				<?php
				$usePaid = false;
				$imgs = array();

				if(issetModule('paidservices')){
					$imgs = PaidServices::getImgForSlider();
					if ($imgs) {
						$usePaid = true;
						foreach($imgs as $img) { ?>
							<a href="<?php echo $img['url'];?>">
								<img src="<?php echo $img['src'];?>" alt="" width="500" height="310" title="<?php echo CHtml::encode($img['title']);?>" />
							</a>
						<?php }
					}
				}

				if(!$usePaid || count($imgs) < 3){
					if(issetModule('slider') && count(Slider::model()->getActiveImages())){
						$this->widget('application.modules.slider.components.SliderWidget', array());
					} else {
						?>
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/slider/1.jpg" alt="1" width="500" height="310" />
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/slider/2.jpg" alt="2" width="500" height="310" />
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/slider/3.jpg" alt="3" width="500" height="310" />
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/slider/4.jpg" alt="4" width="500" height="310" />
						<?php
					}
				} ?>
			</div>
        </div>

		<?php
			Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/slider/themes/default/default.css');
			Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/slider/nivo-slider.css');

			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/slider/jquery.nivo.slider.pack.js', CClientScript::POS_END);
			Yii::app()->clientScript->registerScript('slider', '
				$("#slider").nivoSlider({effect: "random", randomStart: true});
			', CClientScript::POS_READY);
		?>
		<div id="homeintro">
            <?php Yii::app()->controller->renderPartial('//site/index-search-form'); ?>
		</div>
	</div>

	<?php
		if(issetModule('advertising')) {
			$this->renderPartial('//../modules/advertising/views/advert-top', array());
		}
	?>

	<div class="main-content">
		<div class="main-content-wrapper">
			<?php
				foreach(Yii::app()->user->getFlashes() as $key => $message) {
					if ($key=='error' || $key == 'success' || $key == 'notice'){
						echo "<div class='flash-{$key}'>{$message}</div>";
					}
				}
			?>
			<?php echo $content; ?>
		</div>

	</div>
<?php $this->endContent(); ?>
