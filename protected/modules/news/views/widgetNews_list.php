<?php
foreach ($news as $item) : ?>
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
            <?php echo CHtml::link(tt('Read more &raquo;', 'news'), $item->getUrl()); ?>
        </p>
    </div>
<?php endforeach; ?>

<?php

if(!$news){
	echo tt('News list is empty.', 'news');
}

if($pages){
	$this->widget('itemPaginator',array('pages' => $pages, 'header' => ''));
}
?>
