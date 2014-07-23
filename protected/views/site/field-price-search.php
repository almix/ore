<div class="<?php echo $divClass; ?>">
	<?php

	if(isFree()){
		$currency = param('siteCurrency', '$');
	} else {
		$currency = Currency::getCurrentCurrencyName();
	}

	if (issetModule('selecttoslider') && param('usePriceSlider') == 1) {
		?>
		<span class="search"><div class="<?php echo $textClass; ?>" id="currency-title"><?php echo tc('Price range'); ?>:</div> </span>
		<span class="search">
			<?php
			$apTypes = SearchForm::apTypes();

			if (is_array($apTypes) && count($apTypes['propertyType'] > 0)) {

				$propertyType = CJavaScript::encode($apTypes['propertyType']);
				Yii::app()->clientScript->registerScript('propertyType', "var propertyType = " . $propertyType . ";", CClientScript::POS_END);

				foreach ($apTypes['propertyType'] as $key => $value) {
					if ($key > 0)
						$priceAll = Apartment::model()->getPriceMinMax($key);
					else
						$priceAll = Apartment::model()->getPriceMinMax(null, true);


					if(isFree()){
						$priceMi = $priceAll['price_min'];
						$priceMa = $priceAll['price_max'];
					} else {
						$priceMi = floor(Currency::convertFromDefault($priceAll['price_min']));
						$priceMa = ceil(Currency::convertFromDefault($priceAll['price_max']));
					}

					$priceAll['price_min'] = isset($priceAll['price_min']) ? $priceMi : 0;
					$priceAll['price_max'] = isset($priceAll['price_max']) ? $priceMa : 1000;

					$diffPrice = $priceAll['price_max'] - $priceAll['price_min'];

					if ($diffPrice <= 10)
						$step = 1;
					else
						$step = 10;

					if ($diffPrice > 100) {
						$step = 10;
					}
					if ($diffPrice > 1000) {
						$step = 100;
					}
					if ($diffPrice > 10000) {
						$step = 1000;
					}
					if ($diffPrice > 100000) {
						$step = 10000;
					}
					if ($diffPrice > 1000000) { // 1 million
						$step = 300000;
					}
					if ($diffPrice > 10000000) { // 10 millions
						$step = 1000000;
					}
					if ($diffPrice > 100000000) { // 100 millions
						$step = 10000000;
					}

					$priceItems = array_combine(
							range($priceAll['price_min'], $priceAll['price_max'], $step), range($priceAll['price_min'], $priceAll['price_max'], $step)
					);

					// add last element if step less
					if (max($priceItems) != $priceAll["price_max"]) {
						$priceItems[$priceAll["price_max"]] = $priceAll["price_max"];
					}

					$priceMin = (isset($this->priceSlider) && isset($this->priceSlider["min_{$key}"])) ? $this->priceSlider["min_{$key}"] : $priceAll["price_min"];
					$priceMax = (isset($this->priceSlider) && isset($this->priceSlider["max_{$key}"])) ? $this->priceSlider["max_{$key}"] : max($priceItems);

					$priceMinSel = Apartment::priceFormat($priceMin);
					$priceMaxSel = Apartment::priceFormat($priceMax);

					foreach ($priceItems as $priceItemsKey => $priceItemsVal) {
						$priceItems[$priceItemsKey] = Apartment::priceFormat($priceItemsVal);
					}

					$selecttoslider = new SelectToSlider;
					$selecttoslider->publishAssets();

					echo '<div style="display: none;" id="price-search-'.$key.'" class="index-search-form price-search-select">';
						echo CHtml::dropDownList('price_'.$key.'_Min', $priceMin, $priceItems, array('style' => 'display: none;', 'class' => 'searchField'));
						echo CHtml::dropDownList('price_'.$key.'_Max', $priceMax, $priceItems, array('style' => 'display: none;', 'class' => 'searchField'));
						echo '<div class="vals">';
							echo '<div id="price_'.$key.'_Min_selected_val" class="left">' . $priceMinSel . '</div>';
							echo '<div id="price_'.$key.'_Max_selected_val" class="right">' . $priceMaxSel . '</div>';
						echo '</div>';
					echo '</div>';

					Yii::app()->clientScript->registerScript('price_'.$key.'', '
							$("select#price_'.$key.'_Min, select#price_'.$key.'_Max").selectToUISlider({labels: 2, tooltip: false, tooltipSrc : "text", labelSrc: "text",  sliderOptions: { stop: function(e,ui) {  changeSearch(); } } });
						', CClientScript::POS_READY);

					echo '<div style="display: none;" id="price-currency-'.$key.'" class="slider-price-currency">'.$currency.'</div>';

					unset($priceItems);
					unset($priceAll);
					unset($priceMin);
					unset($priceMax);
				}
			}
			echo '</span>';
		} else {
			?>
			<span class="search"><div class="<?php echo $textClass; ?>" id="currency-title"><?php echo tc('Price up to'); ?>:</div> </span>
			<span class="search">
				<input type="text" id="priceTo" name="price" class="width70 search-input-new" value="<?php echo isset($this->price) && $this->price ? CHtml::encode($this->price) : ""; ?>"/>&nbsp;
				<span id="price-currency"><?php echo $currency; ?></span>
			</span>
	<?php

	Yii::app()->clientScript->registerScript('priceTo', '
		focusSubmit($("input#priceTo"));
	', CClientScript::POS_READY);
}
?>
</div>