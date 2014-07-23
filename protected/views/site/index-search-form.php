<form id="search-form" action="<?php echo Yii::app()->controller->createUrl('/quicksearch/main/mainsearch');?>" method="get">
    <div class="searchform-back">
        <div class="searchform-index" align="left">
            <div class="header-form">
                <?php
					$loc = (issetModule('location') && param('useLocation', 1)) ? "-loc" : "";

					if ($loc) {
						$this->renderPartial('//site/field-country-search', array(
							'divClass' => 'header-form-line'.$loc,
							'textClass' => 'width135',
							'fieldClass' => 'width290 search-input-new',
							'minWidth' => '290',
						));

						$this->renderPartial('//site/field-region-search', array(
							'divClass' => 'header-form-line'.$loc,
							'textClass' => 'width135',
							'fieldClass' => 'width290 search-input-new',
							'minWidth' => '290',
						));
					}

                    $this->renderPartial('//site/field-city-search', array(
                        'divClass' => 'header-form-line'.$loc,
                        'textClass' => 'width135',
                        'fieldClass' => 'width290 search-input-new',
                        'minWidth' => '290',
                    ));

					$this->renderPartial('//site/field-type-search', array(
						'divClass' => 'header-form-line'.$loc,
						'textClass' => 'width135',
						'fieldClass' => 'width290 search-input-new',
					));

					$this->renderPartial('//site/field-objtype-search', array(
						'divClass' => 'header-form-line'.$loc,
						'textClass' => 'width135',
						'fieldClass' => 'width290 search-input-new',
					));
					if (!$loc) {
						$this->renderPartial('//site/field-rooms-search', array(
							'divClass' => 'header-form-line'.$loc,
							'textClass' => 'width135',
							'fieldClass' => 'width290 search-input-new',
						));
					}

					$this->renderPartial('//site/field-price-search', array(
						'divClass' => 'header-form-line'.$loc,
						'textClass' => 'width135',
						'fieldClass' => 'width290 search-input-new',
					));

					echo '<div id="more-options-form">';
						if ($loc) {
							$this->renderPartial('//site/field-rooms-search', array(
								'divClass' => 'header-form-line'.$loc,
								'textClass' => 'width135',
								'fieldClass' => 'width290 search-input-new',
							));
						}

						$this->renderPartial('//site/field-square-search', array(
							'divClass' => 'header-form-line'.$loc,
							'textClass' => 'width135',
							'fieldClass' => 'width70 search-input-new',
						));

						$this->renderPartial('//site/field-floor-search', array(
							'divClass' => 'header-form-line'.$loc,
							'textClass' => 'width135',
							'fieldClass' => 'width290 search-input-new',
						));
					echo '</div>';

					Yii::app()->clientScript->registerScript('more-options', '
						$("#more-options-link").click(function(){
							if ($("#more-options-form").is(":hidden")) {
								$("#homeintro").css({"height" : "'.(($loc) ? "382" : "360").'"});
								$("#more-options-form").show();
								$("#more-options-link").html("'.tc("Less options").'");
							} else {
								$("#homeintro").css({"height" : "270"});
								$("#more-options-form").hide();
								$("#more-options-link").html("'.tc("More options").'");
							}
						});
					', CClientScript::POS_READY);
				?>

                <div class="header-form-line">
					<a href="javascript: void(0);" id="more-options-link"><?php echo tc('More options'); ?></a>
                    <a href="javascript: void(0);" onclick="$('#search-form').submit();" id="btnleft" class="btnsrch"><?php echo Yii::t('common', 'Search'); ?></a>
                </div>
            </div>
        </div>
    </div>
</form>