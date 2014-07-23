<h1><?php echo tt('Add apartment', 'apartments');?></h1>

<?php
$this->pageTitle .= ' - '.tt('Add apartment', 'apartments');

$this->widget('zii.widgets.CMenu', array(
	'items' => array(
		array('label'=>tt('Manage apartments', 'apartments'), 'url'=>array('index')),
	)
));

$this->renderPartial('_form',array(
	'model'=>$model,
	'categories' => $categories,
	'supportvideoext' => $supportvideoext,
	'supportvideomaxsize' => $supportvideomaxsize,
));
