<?php

/* * ********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 * 	version				:	1.5.1
 * 	copyright				:	(c) 2013 Monoray
 * 	website				:	http://www.monoray.ru/
 * 	contact us				:	http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * ********************************************************************************************* */

class SiteController extends Controller {

	public $cityActive;

	public function actions() {
		return array(
			'captcha' => array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
		);
	}

	public function accessRules() {
		return array(
			array('allow',
				'users' => array('*'),
			),
			array('allow',
				'actions' => array('viewreferences'),
				'expression' => 'Yii::app()->user->getState("isAdmin")',
			),
		);
	}

	public function init() {
		parent::init();
		$this->cityActive = SearchForm::cityInit();
	}

	public function actionIndex() {
		//$dependency = new CDbCacheDependency('SELECT date_updated FROM {{menu}} WHERE id = "1"');
		$page = Menu::model()->/* cache(param('cachingTime', 1209600), $dependency)-> */findByPk(1);

        if(!isFree() && !isset($_GET['lang'])){
            $canonicalUrl = Yii::app()->getBaseUrl(true);

            $canonicalUrl .= '/'.Yii::app()->language;
            Yii::app()->clientScript->registerLinkTag('canonical', null, $canonicalUrl);
        }

        Yii::app()->user->setState('searchUrl', NULL);

        if (isset($_POST['is_ajax'])) {
			$this->renderPartial('index', array('page' => $page), false, true);
		} else {
			$this->render('index', array('page' => $page));
		}
	}

	public function actionError() {
		if ($error = Yii::app()->errorHandler->error) {
			if (Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else {
				$this->render('error', $error);
			}

		}
	}

	public function actionLogin() {
		$this->layout = '//layouts/inner';
		$model = new LoginForm;

		if (Yii::app()->request->getQuery('soc_error_save'))
			Yii::app()->user->setFlash('error', tt('Error saving data. Please try again later.', 'socialauth'));
		if (Yii::app()->request->getQuery('deactivate'))
			showMessage(tc('Login'), tt('Your account not active. Administrator deactivate your account.', 'socialauth'), null, true);

		$service = Yii::app()->request->getQuery('service');
		if (isset($service)) {
			$authIdentity = Yii::app()->eauth->getIdentity($service);
			$authIdentity->redirectUrl = Yii::app()->user->returnUrl;
			$authIdentity->cancelUrl = $this->createAbsoluteUrl('site/login');

			if ($authIdentity->authenticate()) {
				$identity = new EAuthUserIdentity($authIdentity);

				// успешная авторизация
				if ($identity->authenticate()) {
					//Yii::app()->user->login($identity);

					$uid = $identity->id;
					$firstName = $identity->firstName;
					$email = $identity->email;
					$service = $identity->serviceName;
					$mobilePhone = $identity->mobilePhone;
					$homePhone = $identity->homePhone;
					$isNewUser = false;

					$existId = User::getIdByUid($uid, $service);

					if (!$existId) {
						$isNewUser = true;
						$email = (!$email) ? User::getRandomEmail() : $email;
						$phone = '';
						if ($mobilePhone)
							$phone = $mobilePhone;
						elseif ($homePhone)
							$phone = $homePhone;

						$user = $this->createUser($email, $firstName, $phone, '', true);

						if (!$user && isset($user['id'])) {
							$authIdentity->redirect(Yii::app()->createAbsoluteUrl('/site/login') . '?soc_error_save=1');
						}

						$success = User::setSocialUid($user['id'], $uid, $service);

						if (!$success) {
							User::model()->findByPk($user['id'])->delete();
							$authIdentity->redirect(Yii::app()->createAbsoluteUrl('/site/login') . '?soc_error_save=1');
						}

						$existId = User::getIdByUid($uid, $service);
					}

					if ($existId) {
						$result = $model->loginSocial($existId);
						if ($result) {
							//						Yii::app()->user->clearState('id');
							//						Yii::app()->user->clearState('first_name');
							//						Yii::app()->user->clearState('nickname');
							if ($result === 'deactivate')
								$authIdentity->redirect(Yii::app()->createAbsoluteUrl('/site/login') . '?deactivate=1');
							if ($isNewUser)
								$authIdentity->redirect(Yii::app()->createAbsoluteUrl('/usercpanel/main/index') . '?soc_success=1');
							else
								$authIdentity->redirect(Yii::app()->createAbsoluteUrl('/usercpanel/main/index'));
						}
					}
					// специальное перенаправления для корректного закрытия всплывающего окна
					$authIdentity->redirect();
				}
				else {
					// закрытие всплывающего окна и перенаправление на cancelUrl
					$authIdentity->cancel();
				}
			}

			// авторизация не удалась, перенаправляем на страницу входа
			$this->redirect(array('site/login'));
		}

		if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['LoginForm'])) {
			$model->attributes = $_POST['LoginForm'];
			if ($model->validate() && $model->login()) {
				if (Yii::app()->user->getState('isAdmin')) {
					NewsProduct::getProductNews();
					$this->redirect(array('/apartments/backend/main/admin'));
					Yii::app()->end();
				}

				if (Yii::app()->user->isGuest) {
					$this->redirect(Yii::app()->user->returnUrl);
				} else {
					if (!Yii::app()->user->getState('returnedUrl')) {
						$this->redirect(array('/usercpanel/main/index'));
					} else {
						$this->redirect(Yii::app()->user->getState('returnedUrl'));
					}
				}
			}
		}
		$this->render('login', array('model' => $model));
	}

	public function actionLogout() {
		Yii::app()->user->logout();

		if (isset(Yii::app()->request->cookies['itemsSelectedImport']))
			unset(Yii::app()->request->cookies['itemsSelectedImport']);

		if (isset(Yii::app()->request->cookies['itemsSelectedExport']))
			unset(Yii::app()->request->cookies['itemsSelectedExport']);

		if (isset(Yii::app()->session['importAds']))
			unset(Yii::app()->session['importAds']);

		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionViewreferences() {
		$this->layout = '//layouts/admin';
		$this->render('view_reference');
	}

	public function actionRecover() {
		$modelRecover = new RecoverForm;

		$key = Yii::app()->request->getParam('key');
		if ($key) {
			$user = User::model()->find('recoverPasswordKey = :key', array(':key' => $key));
			if ($user) {
				$password = $user->temprecoverpassword;

				// set salt pass
				$user->setPassword($password);
				$user->temprecoverpassword =  $user->recoverPasswordKey = '';

				// set new password in db
				$user->update(array('password', 'salt', 'temprecoverpassword', 'recoverPasswordKey'));

				showMessage(tc('Recover password'), tc('Password successfully changed'));
			}
			else
				throw new CHttpException(403, tc('User not exists'));
		}
		else {
			if (isset($_POST['ajax']) && $_POST['ajax'] === 'recover-form') {
				echo CActiveForm::validate($modelRecover);
				Yii::app()->end();
			}

			if (isset($_POST['RecoverForm'])) {
				$modelRecover->attributes = $_POST['RecoverForm'];

				if ($modelRecover->validate()) {
					$model = User::model()->findByAttributes(array('email' => $modelRecover->email));

					if ($model !== null) {

						if(demo()){
							Yii::app()->user->setFlash('notice', tc('Sorry, this action is not allowed on the demo server.'));
							$this->refresh();
						}

						$tempRecoverPassword = $model->randomString();
						$recoverPasswordKey = $this->generateActivateKey();

						$model->temprecoverpassword = $tempRecoverPassword;
						$model->recoverPasswordKey = $recoverPasswordKey;
						$model->update(array('temprecoverpassword', 'recoverPasswordKey'));

						$model->recoverPasswordLink = Yii::app()->createAbsoluteUrl('/site/recover?key='.$recoverPasswordKey);

						// send email
						$notifier = new Notifier;
						$notifier->raiseEvent('onRecoveryPassword', $model, $model->id);

						showMessage(tc('Recover password'), tc('recover_pass_temp_send'));
					} else {
						showMessage(tc('Recover password'), tc('User does not exist'));
					}
				}
				else {
					$modelRecover->unsetAttributes(array('verifyCode'));
				}
			}
		}
		$this->render('recover', array('model' => $modelRecover));
	}

	public function actionRegister() {
		if (Yii::app()->user->isGuest && param('useUserads')) {
			$model = new User('register');

			if (isset($_POST['User'])) {
				$model->attributes = $_POST['User'];
				if ($model->validate()) {
					$activateKey = $this->generateActivateKey();
					$user = $this->createUser($model->email, $model->username, $model->phone, $activateKey);

					if ($user) {
						$model->id = $user['id'];
						$model->password = $user['password'];
						$model->email = $user['email'];
						$model->username = $user['username'];
						$model->activatekey = $user['activateKey'];
						$model->activateLink = $user['activateLink'];

						$notifier = new Notifier;
						$notifier->raiseEvent('onRegistrationUser', $model, $model->id);
						showMessage(Yii::t('common', 'Registration'), Yii::t('common', 'You were successfully registered. The letter for account activation has been sent on {useremail}', array('{useremail}' => $user['email'])));
					} else {
						showMessage(Yii::t('common', 'Registration'), Yii::t('common', 'Error. Repeat attempt later'));
					}
				} else {
					$model->unsetAttributes(array('verifyCode'));
				}
			}
			$this->render('register', array('model' => $model));
		} else {
			$this->redirect('index');
		}
	}

	public function generateActivateKey() {
		return md5(uniqid());
	}

	public function createUser($email, $username = '', $phone = '', $activateKey = '', $isActive = false) {
		$model = new User;
		$model->email = $email;
		if ($username)
			$model->username = $username;
		if ($phone)
			$model->phone = $phone;
		if ($isActive)
			$model->active = 1;
		if ($activateKey)
			$model->activatekey = $activateKey;

		$password = $model->randomString();
		$model->setPassword($password);

		$return = array();

		if ($model->save()) {
			$return = array(
				'email' => $model->email,
				'username' => $model->username,
				'password' => $password,
				'id' => $model->id,
				'active' => $model->active,
				'activateKey' => $activateKey,
				'activateLink' => Yii::app()->createAbsoluteUrl('/site/activation?key=' . $activateKey)
			);
		}
		else {
			$errors = $model->getErrors();
			if ($errors) {
				foreach($errors as $error) {
					if ($error && is_array($error)) {
						foreach($error as $item) {
							echo '<div class="alert alert-block alert-error fade in">'.$item.'</div>';
						}
					}
				}
			}
			exit;
		}
		return $return;
	}

	public function actionActivation() {
		$key = Yii::app()->request->getParam('key');
		if ($key) {
			$user = User::model()->find('activatekey = :activatekey', array(':activatekey' => $key));

			if (!empty($user)) {
				if ($user->active == '1') {
					showMessage(Yii::t('common', 'Activate account'), Yii::t('common', 'Your status account already is active'));
				} else {
					$user->active = '1';
					//$user->activatekey = '';
					//$user->save();
					$user->update(array('active'));
					showMessage(Yii::t('common', 'Activate account'), Yii::t('common', 'Your account successfully activated'));
				}
			} else {
				throw new CHttpException(403, Yii::t('common', 'User not exists'));
			}
		}
		else
			$this->redirect(array('/site/index'));
	}

	public function actionAddLang() {

		$modelNameArr = array(
			'Apartment',
			'MetroStation',
			'ReferenceCategories',
			'ReferenceValues',
			'WindowTo',
			'Lang',
			'TimesIn',
			'TimesOut',
			'ApartmentCity',
			'ApartmentObjType',
			'News',
			'Menu',
			'Article'
		);

		$db = Yii::app()->db;
		$langNew = 'en';

		Yii::import('application.modules.referencecategories.models.ReferenceCategories');
		Yii::import('application.modules.referencevalues.models.ReferenceValues');
		Yii::import('application.modules.windowto.models.WindowTo');
		Yii::import('application.modules.articles.models.Article');
		foreach ($modelNameArr as $modelName) {
			deb($modelName);
			$model = new $modelName;
			$table = $model->tableName();
			$i18nFields = $model->i18nFields();

			foreach ($i18nFields as $field => $type) {
				$fieldAdd = $field . '_' . $langNew;
				deb($fieldAdd);
				$sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME='{$table}' AND COLUMN_NAME='{$fieldAdd}'";
				$fieldExist = $db->createCommand($sql)->queryScalar();
				if (!$fieldExist) {
					$db->createCommand()->addColumn($table, $fieldAdd, $type);
				}
			}
		}
	}

	public function actionVersion() {
		echo param('version_name') . ' ' . param('version');
	}

	public function actionUploadImage() {
		$allowExtension = array('png','jpg','gif','jpeg');

		if(Yii::app()->user->getState("isAdmin")){
			$type = Yii::app()->request->getQuery('type');
			Controller::disableProfiler(); // yii-debug-toolbar disabler

			if($type == 'imageUpload'){
				if (!empty($_FILES['upload']['name']) && !Yii::app()->user->isGuest) {
					//$dir = Yii::getPathOfAlias('webroot.upload') . '/' . Yii::app()->user->id . '/';
					$dir = Yii::getPathOfAlias('webroot.uploads.editor') . '/';
					if (!is_dir($dir))
						@mkdir($dir, '0777', true);

					$file = CUploadedFile::getInstanceByName('upload');
					if ($file) {
						$newName = md5(time()) . '.' . $file->extensionName;

						$error = '';
						$callback = $_GET['CKEditorFuncNum'];

						if (in_array($file->extensionName, $allowExtension)) {
							if ($file->saveAs($dir . $newName)) {
								$httpPath = Yii::app()->getBaseUrl(true).'/uploads/editor/' . $newName;
							}
							else {
								$error = 'Some error occured please try again later';
								$httpPath = '';
							}
						}
						else {
							$error = 'The file is not the image';
							$httpPath = '';
						}

						echo "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(".$callback.",  \"".$httpPath."\", \"".$error."\" );</script>";
					}
				}
			}
		}
	}

	public function actionTest(){
		@ini_set('max_execution_time', 3600);

		$sql = 'SELECT id, message, translation_en FROM {{translate_message}} WHERE status=0 OR status=1';
		$messages = Yii::app()->db->createCommand($sql)->queryAll();

		//$messages = array('The required fields are marked with an asterisk (<span class="required">*</span>).');

		$options = array(
			'fileTypes' => array('php'),
		);
		$files = CFileHelper::findFiles(realpath(Yii::getPathOfAlias('application')), $options);

		foreach($files as $file){
			$subject = file_get_contents($file);
			foreach($messages as $message){
				//$tmp = preg_match_all('@\''.preg_quote($message['message']).'\'@', $subject, $matches);

				$message['translation_en'] = str_replace('\'', '\\\'', $message['translation_en']);
				$count = 0;
				$tmp = preg_replace('@tc\(\''.preg_quote($message['message']).'\'\)@', 'tc(\''.$message['translation_en'].'\')', $subject, -1, $count);

				if($tmp !== NULL){
					$subject = $tmp;
				}
				if($count){
					$sql = 'UPDATE {{translate_message}} SET message=translation_en WHERE id="'.$message['id'].'"';
					Yii::app()->db->createCommand($sql)->execute();
				}
				/*if($tmp > 0){
					$sql = 'UPDATE {{translate_message}} SET status=4 WHERE id="'.$message['id'].'"';
					Yii::app()->db->createCommand($sql)->execute();
				}*/
			}
			file_put_contents($file, $subject);
		}
	}

}