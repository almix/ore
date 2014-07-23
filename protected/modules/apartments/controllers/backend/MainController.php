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

class MainController extends ModuleAdminController {

	public $modelName = 'Apartment';

	public function actionView($id = 0) {
		//$this->layout='//layouts/inner';

		Yii::app()->bootstrap->plugins['tooltip'] = array(
			'selector'=>' ', // bind the plugin tooltip to anchor tags with the 'tooltip' class
			'options'=>array(
				'placement'=>'top', // place the tooltips below instead
			),
		);

		$this->render('view', array(
			'model' =>  $this->loadModelWith(array('windowTo', 'objType', 'city')),
			'statistics' => Apartment::getApartmentVisitCount($id),
		));
	}

    public function actionAdmin(){

        $countNewsProduct = NewsProduct::getCountNoShow();
        if($countNewsProduct > 0){
            Yii::app()->user->setFlash('info', Yii::t('common', 'There are new product news') . ': '
                . CHtml::link(Yii::t('common', '{n} news', $countNewsProduct), array('/news/backend/main/product')));
        }

		$this->rememberPage();


		$this->getMaxSorter();

		$model = new Apartment('search');
		$model = $model->with(array('user'));

		$this->render('admin',array_merge(array('model'=>$model), $this->params));


    }

	public function actionUpdate($id){

        $this->_model = $this->loadModel($id);
		if(!$this->_model){
			throw404();
		}

		if(issetModule('bookingcalendar')) {
			$this->_model = $this->_model->with(array('bookingCalendar'));
		}
        if(isset($_GET['type'])){
            $type = self::getReqType();

            $this->_model->type = $type;
        }

        $categories = Apartment::getCategories($this->_model->id, $this->_model->type);

		$originalActive = $this->_model->active;

		if(isset($_POST[$this->modelName])){
			$this->_model->attributes=$_POST[$this->modelName];

			if ($this->_model->type != Apartment::TYPE_BUY && $this->_model->type != Apartment::TYPE_RENTING) {
				// video
				$firstValidate = true;
				if((isset($_FILES[$this->modelName]['name']['video_file']) && $_FILES[$this->modelName]['name']['video_file'])){
					$this->_model->scenario = 'video_file';
					if ($this->_model->validate()) {
						$this->_model->videoUpload = CUploadedFile::getInstance($this->_model, 'video_file');
						$videoFile = md5(uniqid()).'.'.$this->_model->videoUpload->extensionName;
						$pathVideo = Yii::getPathOfAlias('webroot.uploads.video').DIRECTORY_SEPARATOR.$id;

						if (newFolder($pathVideo)) {
							$this->_model->videoUpload->saveAs($pathVideo.'/'.$videoFile);

							$sql = 'INSERT INTO {{apartment_video}} (apartment_id, video_file, 	video_html, date_updated)
								VALUES ("'.$id.'", "'.$videoFile.'", "", NOW())';
							Yii::app()->db->createCommand($sql)->execute();
						}
						else {
							Yii::app()->user->setFlash('error', tt('not_create_folder_to_save.', 'apartments'));
							$this->redirect(array('update', 'id' => $id));
						}
					}
					else {
						$firstValidate = false;
					}
				}
				// html code
				if (isset($_POST[$this->modelName]['video_html']) && $_POST[$this->modelName]['video_html']) {
					$this->_model->video_html = $_POST[$this->modelName]['video_html'];
					$this->_model->scenario = 'video_html';
					if ($this->_model->validate()) {
						$sql = 'INSERT INTO {{apartment_video}} (apartment_id, video_file, 	video_html, date_updated)
							VALUES ("'.$id.'", "", "'.CHtml::encode($this->_model->video_html).'", NOW())';
						Yii::app()->db->createCommand($sql)->execute();
					}
					else {
						$firstValidate = false;
					}
				}

				$city = "";
				if (issetModule('location') && param('useLocation', 1)) {
					$city .= $this->_model->locCountry ? $this->_model->locCountry->getStrByLang('name') : "";
					$city .= ($city && $this->_model->locCity) ? ", " : "";
					$city .= $this->_model->locCity ? $this->_model->locCity->getStrByLang('name') : "";
				} else
					$city = $this->_model->city ? $this->_model->city->getStrByLang('name') : "";

				// data
				if ($firstValidate) {
					if(($this->_model->address && $city) && (param('useGoogleMap', 1) || param('useYandexMap', 1))){
						if (!$this->_model->lat && !$this->_model->lng) { # уже есть

							$coords = Geocoding::getCoordsByAddress($this->_model->address, $city);

							if(isset($coords['lat']) && isset($coords['lng'])){
								$this->_model->lat = $coords['lat'];
								$this->_model->lng = $coords['lng'];
							}
						}
					}
				}
			}

			$this->_model->scenario = 'savecat';

            $isUpdate = Yii::app()->request->getPost('is_update');
            if($isUpdate){
                $this->_model->save(false);
            }elseif($this->_model->validate()){
				$this->_model->active = Apartment::STATUS_ACTIVE;
				$this->_model->save(false);

				$this->redirect(array('view','id'=>$this->_model->id));
			}
			$this->_model->active = $originalActive;
		}

		if($this->_model->active == Apartment::STATUS_DRAFT){
			Yii::app()->user->setState('menu_active', 'apartments.create');
			$this->render('create', array(
				'model' => $this->_model,
				'categories' => $categories,
				'supportvideoext' => ApartmentVideo::model()->supportExt,
				'supportvideomaxsize' => ApartmentVideo::model()->fileMaxSize,
			));
			return;
		}

		$this->render('update', array(
			'model' => $this->_model,
			'categories' => $categories,
			'supportvideoext' => ApartmentVideo::model()->supportExt,
			'supportvideomaxsize' => ApartmentVideo::model()->fileMaxSize,
		));
	}

    private static function getReqType(){
        $type = Yii::app()->getRequest()->getQuery('type');
        $existType = array_keys(Apartment::getTypesArray());
        if(!in_array($type, $existType)){
            $type = Apartment::TYPE_DEFAULT;
        }
        return $type;
    }

	public function actionCreate(){
		$model = new $this->modelName;
		$model->active = Apartment::STATUS_DRAFT;
		$model->type = Apartment::TYPE_RENT;
		$model->save(false);

		$this->redirect(array('update', 'id' => $model->id));
	}

	public function getWindowTo(){
		$sql = 'SELECT id, title_'.Yii::app()->language.' as title FROM {{apartment_window_to}}';
		$results = Yii::app()->db->createCommand($sql)->queryAll();
		$return = array();
		$return[0] = '';
		if($results){
			foreach($results as $result){
				$return[$result['id']] = $result['title'];
			}
		}
		return $return;
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

	public function actionGmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomGMap::actionGmap($id, $model, $this->renderPartial('_marker', array('model' => $model), true));

		if($result){
			return $this->renderPartial('_gmap', $result, true);
		}
		return '';
	}

	public function actionYmap($id, $model = null){

		if($model === null){
			$model = $this->loadModel($id);
		}

		$result = CustomYMap::init()->actionYmap($id, $model, $this->renderPartial('_marker', array('model' => $model), true));

		if($result){
			//return $this->renderPartial('backend/_ymap', $result, true);
		}
		return '';
	}
}