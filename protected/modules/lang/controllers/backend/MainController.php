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

class MainController extends ModuleAdminController{
	public $modelName = 'Lang';

    public function actionAdmin(){
   		$this->getMaxSorter();
   		parent::actionAdmin();
   	}

    public function actionIndex(){
        $this->redirect('admin');
    }

    public function actionView($id){
        $this->redirect('admin');
    }
/*
	public function actionCompare(){
		$sql = 'SELECT * FROM {{translate_message}} WHERE translation_de = ""';
		$result = Yii::app()->db->createCommand($sql)->queryAll();

		foreach ($result as $key=>$value) {
			$sql1 = 'SELECT translation_de FROM {{translate_message_de}} WHERE category = :category AND message = :message';
			$result1 = Yii::app()->db->createCommand($sql1)->queryScalar(array(':category'=>$value['category'], ':message'=>$value['message']));
			$result[$key]['translation_de'] = $result1;
			if (!$result1) {
				echo "<pre>";
				print_r($result[$key]);
				echo "</pre>";
			} else {
				$sql = 'UPDATE {{translate_message}} SET translation_de = :translation_de WHERE id="'.$value['id'].'"';
				Yii::app()->db->createCommand($sql)->execute(array(':translation_de' => $result1));
			}

		}


	}
*/
	public function actionSetDefault(){
        $id = (int) Yii::app()->request->getPost('id');
		$admin_mail = (int) Yii::app()->request->getPost('admin_mail');
        $model = Lang::model()->findByPk($id);
        $model->setDefault($admin_mail);

        Yii::app()->end();
    }

	public function actionActivate(){
        $id = (int) $_GET['id'];
        $action = $_GET['action'];
        if($id){
            $model = Lang::model()->findByPk($id);
            if(($model->main == 1 || $model->admin_mail == 1) && $action != 'activate'){
                Yii::app()->end();
            }
        }
        parent::actionActivate();
    }

}
