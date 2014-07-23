<?php $this->beginContent('//layouts/main'); ?>

    <?php if(!isset($this->aData['searchOnMap'])){ ?>

	<form id="search-form" action="<?php echo Yii::app()->controller->createUrl('/quicksearch/main/mainsearch');?>" method="get">
	<?php
		if (isset($this->userListingId) && $this->userListingId) {
			echo CHtml::hiddenField('userListingId', $this->userListingId);
		}

		$loc = (issetModule('location') && param('useLocation', 1)) ? "-loc" : "";
	?>
	<div class="searchform-back">
		<div class="searchform<?php echo $loc;?>" align="left">
			<div class="header-form">
				<div class="header-form-line select-num-of-rooms-inner header-small-search<?php echo $loc;?>">
					<?php

						/*if (isset(Yii::app()->modules['metrostations'])) {
							$this->renderPartial('//site/field-metro-search', array(
								'divClass' => 'small-header-form-line left width450',
								'textClass' => 'width135',
								'fieldClass' => 'width290 search-input-new',
								'minWidth' => '297',
							));
						}*/
						if ($loc) {
							$this->renderPartial('//site/field-country-search', array(
								'divClass' => 'small-header-form-line left width450',
								'textClass' => 'width135',
								'fieldClass' => 'width290 search-input-new',
								'minWidth' => '290',
							));

							$this->renderPartial('//site/field-type-search', array(
								'divClass' => 'small-header-form-line left width450',
								'textClass' => 'width135',
								'fieldClass' => 'width290 search-input-new',
							));

							$this->renderPartial('//site/field-region-search', array(
								'divClass' => 'small-header-form-line left width450',
								'textClass' => 'width135',
								'fieldClass' => 'width290 search-input-new',
								'minWidth' => '290',
							));

							$this->renderPartial('//site/field-objtype-search', array(
								'divClass' => 'small-header-form-line left width450',
								'textClass' => 'width135',
								'fieldClass' => 'width290 search-input-new',
							));

						$this->renderPartial('//site/field-city-search', array(
							'divClass' => 'small-header-form-line left width450',
							'textClass' => 'width135',
							'fieldClass' => 'width290 search-input-new',
							'minWidth' => '290',
						));

							$this->renderPartial('//site/field-rooms-search', array(
								'divClass' => 'small-header-form-line left width450',
								'textClass' => 'width135',
								'fieldClass' => 'width290 search-input-new',
							));

							$this->renderPartial('//site/field-floor-search', array(
								'divClass' => 'small-header-form-line left width450',
								'textClass' => 'width135',
								'fieldClass' => 'width290 search-input-new',
							));

							$this->renderPartial('//site/field-price-search', array(
								'divClass' => 'small-header-form-line left width450',
								'textClass' => 'width135',
								'fieldClass' => 'width70 search-input-new',
							));

						$this->renderPartial('//site/field-square-search', array(
								'divClass' => 'small-header-form-line left width450',
								'textClass' => 'width135',
								'fieldClass' => 'width70 search-input-new',
							));

						} else {  //поля дублируются для красивого расположения

							$this->renderPartial('//site/field-city-search', array(
								'divClass' => 'small-header-form-line left width450',
								'textClass' => 'width135',
								'fieldClass' => 'width290 search-input-new',
								'minWidth' => '290',
							));

							$this->renderPartial('//site/field-square-search', array(
								'divClass' => 'small-header-form-line left width450',
							'textClass' => 'width135',
							'fieldClass' => 'width70 search-input-new',
						));

						$this->renderPartial('//site/field-type-search', array(
							'divClass' => 'small-header-form-line left width450',
							'textClass' => 'width135',
							'fieldClass' => 'width290 search-input-new',
						));

						$this->renderPartial('//site/field-floor-search', array(
								'divClass' => 'small-header-form-line left width450',
							'textClass' => 'width135',
							'fieldClass' => 'width290 search-input-new',
						));

						$this->renderPartial('//site/field-objtype-search', array(
							'divClass' => 'small-header-form-line left width450',
							'textClass' => 'width135',
							'fieldClass' => 'width290 search-input-new',
						));

						$this->renderPartial('//site/field-price-search', array(
								'divClass' => 'small-header-form-line left width450',
							'textClass' => 'width135',
							'fieldClass' => 'width70 search-input-new',
						));

						$this->renderPartial('//site/field-rooms-search', array(
							'divClass' => 'small-header-form-line left width450',
							'textClass' => 'width135',
							'fieldClass' => 'width290 search-input-new',
						));

						}



					?>

					<div>
						<a href="javascript: void(0);" onclick="$('#search-form').submit();" id="btnleft" class="btnsrch btnsrch-inner"><?php echo Yii::t('common', 'Search'); ?></a>
					</div>

				</div>
			</div>
		</div>
	</div>
	</form>

	<?php
    } else {
        echo '<br>';
    }
?>

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