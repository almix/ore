<?php
if(empty($apartments)){
	$apartments = Apartment::findAllWithCache($criteria);
}

foreach ($apartments as $item) {
	$addClass = '';

	if ($item->is_special_offer) {
		$addClass = 'special_offer_highlight';
	} elseif ($item->date_up_search != '0000-00-00 00:00:00'){
		$addClass = 'up_in_search';
	}
	?>
	<div class="appartment_item <?php echo $addClass; ?>" lat="<?php echo $item->lat;?>" lng="<?php echo $item->lng;?>" ap_id="<?php echo $item->id; ?>" >
		<div class="offer">
			<div class="offer-photo" align="left">
				<div class="apartment_type"><?php echo Apartment::getNameByType($item->type); ?></div>
				<?php
					$res = Images::getMainThumb(150,100, $item->images);
					$img = CHtml::image($res['thumbUrl'], $item->getStrByLang('title'), array(
						'title' => $item->getStrByLang('title'),
					));
					echo CHtml::link($img, $item->getUrl(), array('title' =>  $item->getStrByLang('title')));
				?>
			</div>
			<div class="offer-text">
				<div class="apartment-title">
						<?php
							if($item->rating && !isset($booking)){
								$title = truncateText($item->getStrByLang('title'), 5);
							}
							else {
								$title = truncateText($item->getStrByLang('title'), 10);
							}
							echo CHtml::link($title,
							$item->getUrl(), array('class' => 'offer'));
						?>
				</div>
				<?php
					if($item->rating && !isset($booking)){
						echo '<div class="ratingview">';
						$this->widget('CStarRating',array(
							'model'=>$item,
							'attribute' => 'rating',
							'readOnly'=>true,
							'id' => 'rating_' . $item->id,
							'name'=>'rating'.$item->id,
						));
						echo '</div>';
					}
				?>
				<div class="clear"></div>
				<p class="cost">
					<?php
						if ($item->is_price_poa)
							echo tt('is_price_poa', 'apartments');
						else
							echo $item->getPrettyPrice();
					?>
				</p>
				<?php
					if( $item->floor || $item->floor_total || $item->square || $item->berths){
						echo '<p class="desc">';

						$echo = array();

                        if($item->canShowInView('floor_all')){
                            if($item->floor && $item->floor_total){
                                $echo[] = Yii::t('module_apartments', '{n} floor of {total} total', array($item->floor, '{total}' => $item->floor_total));
                            } else {
                                if($item->floor){
                                    $echo[] = $item->floor.' '.tt('floor', 'common');
                                }
                                if($item->floor_total){
                                    $echo[] = tt('floors', 'common').': '.$item->floor_total;
                                }
                            }
                        }

						if($item->canShowInView('square')){
							$echo[] = '<span class="nobr">'.Yii::t('module_apartments', 'total square: {n}', $item->square)." ".tc('site_square')."</span>";
						}
						if($item->canShowInView('berths')){
							$echo[] = '<span class="nobr">'.Yii::t('module_apartments', 'berths').': '.CHtml::encode($item->berths)."</span>";
						}
						echo implode(', ', $echo);
						unset($echo);

						echo '</p>';
					}
				?>
			</div>
		</div>
	</div>
<?php
}
