<div class="video-block">
	<?php

	$videoHtml = array();
	$count = 0;

	foreach ($data->video as $video) :?>
	<?php if ($video->video_file) : ?>
		<?php
		$filePath = Yii::app()->request->baseUrl.'/uploads/video/'.$video->apartment_id.'/'.$video->video_file;
		$fileFolder = Yii::getPathOfAlias('webroot.uploads.video').DIRECTORY_SEPARATOR.$video->apartment_id.'/'.$video->video_file;
		?>
		<?php if (file_exists($fileFolder)) : ?>
			<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/flowplayer/flowplayer-3.2.12.min.js', CClientScript::POS_END); ?>
            <div class="video-file-block">
                <a href="<?php echo $filePath; ?>" style="display:block;width:560px;height:340px" id="player-<?php echo $video->id;?>"></a>
				<?php
				Yii::app()->clientScript->registerScript('player-'.$video->id.'', '
						flowplayer("player-'.$video->id.'", "'.Yii::app()->request->baseUrl."/js/flowplayer/flowplayer-3.2.16.swf".'",
						{
							clip: {
								autoPlay: false
							}
						});
					', CClientScript::POS_END);
				?>
            </div>
			<?php endif; ?>
		<?php elseif ($video->video_html) : ?>
        <div class="video-html-block" id="video-block-html-<?php echo $count; ?>">
			<?php
				$videoHtml[$count] = CHtml::decode($video->video_html);
				$count++;
			?>
        </div>
		<?php endif; ?>
	<?php endforeach; ?>
    <div class="clear"></div>
</div>

<?php

	$script = '';
	if($videoHtml){
		foreach($videoHtml as $key => $value){
			$script .= '$("#video-block-html-'.$key.'").html("'.CJavaScript::quote($value).'");';
		}
	}

	if($script){
		Yii::app()->clientScript->registerScript('chrome-xss-alert-preventer', $script, CClientScript::POS_READY);
	}

