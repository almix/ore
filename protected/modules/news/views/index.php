<?php
$this->pageTitle .= ' - '.NewsModule::t('News');
$this->breadcrumbs=array(
    NewsModule::t('News'),
);
?>

<h1><?php echo NewsModule::t('News'); ?></h1>
<?php
    foreach ($items as $item) : ?>
    <div class="news-items">
        <p>
            <span class="date"><?php echo $item->dateCreated; ?></span>
        </p>
		<p>
			<span class="title"><?php echo CHtml::link(CHtml::encode($item->getStrByLang('title')), $item->getUrl()); ?></span>
		</p>
        <p class="desc">
            <?php echo truncateText(
            $item->body,
            param('newsModule_truncateAfterWords', 50)
        ); ?>
        </p>
        <p>
            <?php echo CHtml::link(NewsModule::t('Read more &raquo;'), $item->getUrl()); ?>
        </p>
    </div>
<?php endforeach; ?>

<?php
if(!$items){
    echo NewsModule::t('News list is empty.');
}

if($pages){
    $this->widget('itemPaginator',array('pages' => $pages, 'header' => ''));
}
?>
