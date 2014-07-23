<?php
/**********************************************************************************************
*                            CMS Open Real Estate
*                              -----------------
*	version				:	1.5.1
*	copyright			:	(c) 2012 Monoray
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
	public $modelName = 'FormDesigner';

    public function actionAdmin() {
        $model = new $this->modelName('search');
        $model->resetScope();

        if($this->scenario){
            $model->scenario = $this->scenario;
        }

        if($this->with){
            $model = $model->with($this->with);
        }

        $model->unsetAttributes();  // clear any default values
        if(isset($_GET[$this->modelName])){
            $model->attributes=$_GET[$this->modelName];
        }
        $this->render('admin',
            array_merge(array('model'=>$model), $this->params)
        );
    }

    public function actionVisible() {
        $id = Yii::app()->request->getParam('id', null);

        $model = $this->loadModel($id);

        $model->visible = $model->visible ? 0 : 1;
        $model->update('visible');

        if(!Yii::app()->request->isAjaxRequest){
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
    }

    public function actionSetup($id = 0) {
        $id = $id ? $id : (isset($_POST['id']) ? $_POST['id'] : 0);

        /** @var FormDesigner $model */
        $model = FormDesigner::model()->findByPk($id);

        $request = Yii::app()->request;

        $data = $request->getPost('FormDesigner');

        if($data){

			if(isset($data['objTypes'])){
				$model->saveObjTypes = $data['objTypes'];
			} else {
				$model->saveObjTypes = array();
			}

            $model->scenario = 'save_types';

            if($model->save()){
                echo CJSON::encode(array(
                    'status' => 'ok',
                    'id' => $model->id,
                    'html' => $model->getTypesHtml()
                ));
                Yii::app()->end();
            } else {
                echo CJSON::encode(array(
                    'status' => 'err',
                    'html' => $this->renderPartial('_setup_form', array(
                        'id' => $model->id,
                        'model' => $model,
                    ), true)
                ));
                Yii::app()->end();
            }
        }

        $data['model'] = $model;

        if(Yii::app()->request->isAjaxRequest){
            $this->renderPartial('setup', $data);
        }else{
            $this->render('setup', $data);
        }
    }
}
