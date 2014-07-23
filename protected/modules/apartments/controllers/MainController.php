<?php
/**********************************************************************************************
*                            CMS Open Real Estate
*                              -----------------
*	version				:	1.5.1
*	copyright			:	(c) 2013 Monoray
*	website				:	http://www.monoray.ru/
*	contact us			:	http://www.monoray.ru/contact
*
* This file is part of CMS Open Real Estate
*
* Open Real Estate is free software. This work is licensed under a GNU GPL.
* http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
* Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
***********************************************************************************************/

class MainController extends ModuleUserController {

	public $modelName = 'Apartment';

	public function actions() {
		return array(
			'captcha' => array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
		);
	}

	public function actionIndex(){
		throw new CHttpException(404,'The requested page does not exist.');
	}

	public function actionView($id = 0, $url = '', $printable = 0) {
		// если админ - делаем редирект на просмотр в админку
//		if(Yii::app()->user->getState('isAdmin')){
//			$this->redirect(array('backend/main/view', 'id' => $id));
//		}
        $apartment = NULL;

		if( ($id || $url) && issetModule('seo') ){
            $url = $url ? $url : $id;
			$seo = SeoFriendlyUrl::getForView($url, $this->modelName);

            if($seo){
                $this->setSeo($seo);
                $id = $seo->model_id;
            }
		}

        if($id) {
            $apartment = Apartment::model()->with(array('windowTo', 'objType', 'city'))->findByPk($id);
        }

		if(!$apartment){
			throw404();
		}

        // Был ли отправлен комментарий? обрабатываем
		$comment = new Comment;
		if (isset($_POST['Comment'])) {
			$comment->attributes = $_POST['Comment'];

			$comment->apartment_id = $id;

			if(!Yii::app()->user->isGuest){
				$comment->username = Yii::app()->user->username;
				$comment->email = Yii::app()->user->email;
			}

            if ($comment->validate()) {
                if ($comment->save(false)) {
                    if ($comment->active == Comment::STATUS_PENDING){
                        Yii::app()->user->setFlash(
                            'newComment',
                            Yii::t('module_comments','Thank you for your comment. Your comment will be posted once it is approved.')
                        );
                    }
                }
            }
            else {
                $comment->unsetAttributes(array('verifyCode'));
            }
		}

		// "Толстый" запрос из-за JOIN'ов. Кешируем его.
		// Зависимость кеша - выбираем дату последней модификации из 4 таблиц
//		$apartment = Apartment::model()
//			->cache(param('cachingTime', 1209600), Apartment::getFullDependency($id))
//			->with('windowTo', 'comments', 'images', 'objType', 'city')
//			->findByPk($id);
//
//        if (!$apartment)
//            throw404();

		if( $apartment->owner_id != 1 && $apartment->owner_active == Apartment::STATUS_INACTIVE) {
			if (!(isset(Yii::app()->user->id ) && Yii::app()->user->id == $apartment->owner_id) && !Yii::app()->user->getState('isAdmin')) {
				Yii::app()->user->setFlash('notice', tt('apartments_main_index_propertyNotAvailable', 'apartments'));
				throw404();
			}
		}

		if(($apartment->active == Apartment::STATUS_INACTIVE || $apartment->active == Apartment::STATUS_MODERATION)
		&& !Yii::app()->user->getState('isAdmin')
		&& !(isset(Yii::app()->user->id ) && Yii::app()->user->id == $apartment->owner_id)){
			Yii::app()->user->setFlash('notice', tt('apartments_main_index_propertyNotAvailable', 'apartments'));
			//$this->redirect(Yii::app()->homeUrl);
			throw404();
		}

		if($apartment->active == Apartment::STATUS_MODERATION && $apartment->owner_active == Apartment::STATUS_ACTIVE && $apartment->owner_id == Yii::app()->user->id){
			Yii::app()->user->setFlash('error', tc('Awaiting moderation'));
		}

		$dateFree = CDateTimeParser::parse($apartment->is_free_to, 'yyyy-MM-dd');
		if($dateFree && $dateFree < (time()-60*60*24)){
			$apartment->is_special_offer = 0;
			$apartment->update(array('is_special_offer'));
		}

		if($apartment === null){
			if($comment->apartment_id){
				$comment->delete();
			}
			throw new CHttpException(404,'The requested page does not exist.');
		}

		// попытка комментария к несуществующему объявлению?
		if($comment->apartment_id && $apartment->id != $comment->apartment_id){
			$comment->delete();
		}

		$ipAddress = Yii::app()->request->userHostAddress;
		$userAgent = Yii::app()->request->userAgent;
		Apartment::setApartmentVisitCount($id, $ipAddress, $userAgent);

		if ($printable) {
			$this->layout='//layouts/print';
			$this->render('view_print', array(
				'model' => $apartment,
			));
		} else {
			$this->render('view', array(
				'model' => $apartment,
				'comment' => $comment,
				'statistics' => Apartment::getApartmentVisitCount($id),
			));
		}
	}

	public function actionGmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomGMap::actionGmap($id, $model, $this->renderPartial('backend/_marker', array('model' => $model), true));

		if($result){
			return $this->renderPartial('backend/_gmap', $result, true);
		}
		return '';
	}

	public function actionYmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomYMap::init()->actionYmap($id, $model, $this->renderPartial('backend/_marker', array('model' => $model), true));

		if($result){
			//return $this->renderPartial('backend/_ymap', $result, true);
		}
		return '';
	}

	public function actionGeneratePhone($id = null, $width=240, $font=5) {
		if ($id && param('useShowUserInfo')) {
			$apartmentInfo = Apartment::model()->findByPk($id, array('select' => 'owner_id'));

			if ($apartmentInfo->owner_id)
				$userInfo = User::model()->findByPk($apartmentInfo->owner_id, array('select' => 'phone'));

			if ($userInfo->phone) {
				$image = imagecreate($width, 20);

				$bg = imagecolorallocate($image, 255, 255, 255);
				$textcolor = imagecolorallocate($image, 37, 75, 137);

				imagestring($image, $font, 0, 0, $userInfo->phone, $textcolor);

				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Content-Transfer-Encoding: binary');
				header("Content-type: image/png");
				imagepng($image);
				//echo $image;
				imagedestroy($image);
			}
		}
	}

	public function actionAllListings() {
		$userId = (int) Yii::app()->request->getParam('id');
		if ($userId) {
			$this->userListingId = $userId;

			$criteria = new CDbCriteria;
			$criteria->addCondition('active = '.Apartment::STATUS_ACTIVE);
			if (param('useUserads'))
				$criteria->addCondition('owner_active = '.Apartment::STATUS_ACTIVE);

			$criteria->addCondition('owner_id = "'.$userId.'"');
			$criteria->order = 't.id ASC';

			$userModel = User::model()->findByPk($userId);
			$userName = $userModel->username;

			// find count
			$apCount = Apartment::model()->count($criteria);

			if(isset($_POST['is_ajax'])){
				$this->renderPartial('_user_listings', array(
					'criteria' => $criteria,
					'apCount' => $apCount,
					'username' => $userName,
				), false, true);
			}else{
				$this->render('_user_listings', array(
					'criteria' => $criteria,
					'apCount' => $apCount,
					'username' => $userName,
				));
			}
		}
	}

	public function actionSendEmail($id, $isFancy = 0){
		$apartment = Apartment::model()->findByPk($id);

		if (!$apartment) {
			throw404();
		}

		if (!param('use_module_request_property'))
			throw404();

		$model = new SendMailForm;

		if(isset($_POST['SendMailForm'])){
			$model->attributes = $_POST['SendMailForm'];

			if(!Yii::app()->user->isGuest){
				$model->senderEmail = Yii::app()->user->email;
				$model->senderName = Yii::app()->user->username;
			}

			$model->ownerId = $apartment->user->id;
			$model->ownerEmail = $apartment->user->email;
			$model->ownerName = $apartment->user->username;

			$model->apartmentUrl = $apartment->getUrl();

			if($model->validate()){
				$notifier = new Notifier;
				$notifier->raiseEvent('onRequestProperty', $model, 0, $model->ownerEmail);

				Yii::app()->user->setFlash('success', tt('Thanks_for_request', 'apartments'));
				$model = new SendMailForm; // clear fields
			} else {
				$model->unsetAttributes(array('verifyCode'));
				Yii::app()->user->setFlash('error', tt('Error_send_request', 'apartments'));
			}
		}

		if($isFancy){
			//Yii::app()->clientscript->scriptMap['*.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery-ui.min.js'] = false;

			$this->renderPartial('send_email', array(
				'apartment' => $apartment,
				'isFancy' => true,
				'model' => $model,
			), false, true);
		}
		else{
			$this->render('send_email', array(
				'apartment' => $apartment,
				'isFancy' => false,
				'model' => $model,
			));
		}
	}

	public function actionSavecoords($id){
		if(param('useGoogleMap', 1) || param('useYandexMap', 1)){
			$apartment = $this->loadModel($id);
			if(isset($_POST['lat']) && isset($_POST['lng'])){
				$apartment->lat = $_POST['lat'];
				$apartment->lng = $_POST['lng'];
				$apartment->save(false);
			}
			Yii::app()->end();
		}
	}

	public function actionGetVideoFile() {
		$id = Yii::app()->request->getParam('id');
		$apId = Yii::app()->request->getParam('apId');

		if ($id && $apId) {
			$sql = 'SELECT video_file, video_html
					FROM {{apartment_video}}
					WHERE id = "'.$id.'"
					AND apartment_id = "'.$apId.'"';

			$result = Yii::app()->db->createCommand($sql)->queryRow();

			if ($result['video_file']) {
				$this->renderPartial('_video_file',
					array(
						'video'=>$result['video_file'],
						'apartment_id' => $apId,
						'id' => $id,
					), false, true
				);
			}
			elseif ($result['video_html']) {
				echo CHtml::decode($result['video_html']);
			}
		}
	}
}