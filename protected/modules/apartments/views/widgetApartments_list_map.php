<?php
$apartments = Apartment::findAllWithCache($criteria);

$ids = array();
foreach($apartments as $apartment){
	$ids[] = $apartment->id;
}
$criteriaForMap = new CDbCriteria();
$criteriaForMap->addInCondition('t.id', $ids);
?>


<div class="apartment_list_map">
	<?php $this->widget('application.modules.viewallonmap.components.ViewallonmapWidget', array('criteria' => $criteriaForMap, 'filterOn' => false, 'withCluster' => false)); ?>
</div>

<?php $this->render('widgetApartments_list_item', array('apartments' => $apartments)); ?>
