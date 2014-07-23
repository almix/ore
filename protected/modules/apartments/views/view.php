<?php
$this->pageTitle .= ' - '.$model->getStrByLang('title');
if (isset($model->city) && isset($model->city->name)) {
	$this->pageTitle .=  ', '.tc('City'). ' ' . $model->city->name;
}

if ($model->getStrByLang('description'))
	$this->pageDescription = truncateText($model->getStrByLang('description'), 20);
?>

<div class='div-pdf-fix'>
	<?php
		echo '<div class="floatleft printicon">';

		$searchUrl = Yii::app()->user->getState('searchUrl');
		if($searchUrl){
			echo CHtml::link('<img src="'.Yii::app()->baseUrl.'/images/design/back2search.png"
				alt="'.tc('Go back to search results').'" title="'.tc('Go back to search results').'"  />',
				$searchUrl);
		}

		echo CHtml::link('<img src="'.Yii::app()->baseUrl.'/images/design/print.png"
				alt="'.tc('Print version').'" title="'.tc('Print version').'"  />',
			array('/apartments/main/view', 'id'=>$model->id, 'printable'=>true), array('target' => '_blank'));


		if(Yii::app()->user->getState('isAdmin')){
			$editLink = Yii::app()->createUrl('/apartments/backend/main/update', array('id' => $model->id));
		}elseif($model->owner_id == Yii::app()->user->id){
			$editLink = Yii::app()->createUrl('/userads/main/update', array('id' => $model->id));
		}

		if(isset($editLink) && $editLink){
			echo CHtml::link('<img src="'.Yii::app()->baseUrl.'/images/design/edit.png"
				alt="'.tt('Update apartment').'" title="'.tt('Update apartment').'"  />',
				$editLink);
		}
		echo '</div>';
	?>

	<div class="floatleft-title">
		<div>
			<div class="div-title">
				<h1 class="h1-ap-title"><?php echo CHtml::encode($model->getStrByLang('title')); ?></h1>
			</div>
			<?php if($model->rating): ?>
			<div class="ratingview-title">
				<?php
				$this->widget('CStarRating',
					array(
						'name'=>'ratingview'.$model->id,
						'id'=>'ratingview'.$model->id,
						'value'=>intval($model->rating),
						'readOnly'=>true,
					));
				?>
			</div>
			<?php endif; ?>
		</div>
		<div class="clear"></div>
		<div class="stat-views">
			<?php if (isset($statistics) && is_array($statistics)) : ?>
			<?php echo tt('Views') ?>: <?php echo tt('views_all') . ' ' . $statistics['all'] ?>, <?php echo tt('views_today') . ' ' . $statistics['today'].'.&nbsp;';?>
			<?php echo '&nbsp;'.tc('Date created') . ': ' . $model->getDateTimeInFormat('date_created'); ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="clear"></div>
<?php
	// show ad
	$this->renderPartial('_view', array(
		'data'=>$model,
		'comment' => $comment,
	));
?>
