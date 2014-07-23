<div id="comments">
	<?php
		echo '<h2>'.Yii::t('module_comments','Comments').'</h2>';


		if(!Yii::app()->user->hasState('isAdmin')){
			echo '<a href="#" onclick="$(\'#comments_form\').toggle(); return false;">'.Yii::t('module_comments','Leave a Comment').'</a>';

			// Draw a hidden form to add a comment. If there is a validation error - that the form is not hidden
			echo '<div id="comments_form" class="'.($comment->getErrors()?'':'hidden').'">';
			$this->renderPartial('application.modules.comments.views.backend._form',array(
				'model'=>$comment,
			));
			echo '</div>';

			if(Yii::app()->user->hasFlash('newComment')){
				echo "<div class='flash-success'>".Yii::app()->user->getFlash('newComment')."</div>";
			}
		}

		// If you have comments - shows the number and the comments themselves
		echo '<div id="comments-list">';
		if($model->commentCount){
			$this->renderPartial('//../modules/apartments/views/_comments',array(
				'apartment'=>$model,
				'comments'=>$model->comments,
			));
		} else {
			echo Yii::t('module_comments', 'There are no comments');
		}
		echo '</div>';
	?>
</div>