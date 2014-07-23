<?php

/* * ********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 * 	version				:	1.5.1
 * 	copyright			:	(c) 2013 Monoray
 * 	website				:	http://www.monoray.ru/
 * 	contact us			:	http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * ********************************************************************************************* */

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {

	public $layout = '//layouts/index';
	public $infoPages = array();
	public $menuTitle;
	public $menu = array();
	public $breadcrumbs = array();
	public $pageKeywords;
	public $pageDescription;
	public $adminTitle = '';
	public $aData;
	public $modelName;

	public $seoTitle;
	public $seoDescription;
	public $seoKeywords;

	/* advertising */
	public $advertPos1 = array();
	public $advertPos2 = array();
	public $advertPos3 = array();
	public $advertPos4 = array();
	public $advertPos5 = array();
	public $advertPos6 = array();

	protected function beforeAction($action) {

		if (!Yii::app()->user->getState('isAdmin')) {
			$currentController = Yii::app()->controller->id;
			$currentAction = Yii::app()->controller->action->id;

			if (!($currentController == 'site' && ($currentAction == 'login' || $currentAction == 'logout'))) {
				if (issetModule('service')){
					$serviceInfo = Service::model()->findByPk(Service::SERVICE_ID);
					if ($serviceInfo && $serviceInfo->is_offline == 1) {
						$allowIps = explode(',', $serviceInfo->allow_ip);
						$allowIps = array_map("trim", $allowIps);

						if (!in_array(Yii::app()->request->userHostAddress, $allowIps)) {
							$this->renderPartial('//../modules/service/views/index', array('page' => $serviceInfo->page), false, true);
							Yii::app()->end();
						}
					}
				}
			}
		}

		/* start  get page banners */
		if (issetModule('advertising') && !param('useBootstrap')) {
			$advert = new Advert;
			$advert->getAdvertContent();
		}
		/* end  get page banners */

		return parent::beforeAction($action);
	}

	function init() {

		if (!file_exists(ALREADY_INSTALL_FILE) && !(Yii::app()->controller->module && Yii::app()->controller->module->id == 'install')) {
			$this->redirect(array('/install'));
		}

		setLang();

		Yii::app()->user->setState('menu_active', '');

		if (isFree()) {
			$this->pageTitle = param('siteTitle');
			$this->pageKeywords = param('siteKeywords');
			$this->pageDescription = param('siteDescription');
		}
		else {
			if(issetModule('seo')){
				$this->pageTitle = Seo::getSeoValue('siteName');
				$this->pageKeywords = Seo::getSeoValue('siteKeywords');
				$this->pageDescription = Seo::getSeoValue('siteDescription');
			}
			else {
				$this->pageTitle = tt('siteName', 'seo');
				$this->pageKeywords = tt('siteKeywords', 'seo');
				$this->pageDescription = tt('siteDescription', 'seo');
			}
		}

		Yii::app()->name = $this->pageTitle;

		if(Yii::app()->getModule('menumanager')){
			if(!(Yii::app()->controller->module && Yii::app()->controller->module->id == 'install')){
				$this->infoPages = Menu::getMenuItems();
			}
		}

        $subItems = array();

        if(!Yii::app()->user->isGuest && !Yii::app()->user->getState('isAdmin')){
            $subItems = array(
                array(
                    'label' => tt('Manage apartments', 'apartments'),
                    'url' => array('/userads/main/index'),
                ),
            );

            if(issetModule('payment')){
                $subItems[] = array(
                    'label' => tc('MODULE of Payments & Payment systems '),
                    'url' => array('/usercpanel/main/payments'),
                );
                $subItems[] = array(
                    'label' => tc('Add funds to account'),
                    'url' => Yii::app()->createUrl('/paidservices/main/index', array('paid_id' => PaidServices::ID_ADD_FUNDS)),
                );
            }
        }

		$this->aData['userCpanelItems'] = array(
			array(
				'label' => tt('Add ad', 'common'),
				'url' => array('/userads/main/create'),
				'visible' => param('useUserads', 0) == 1
			),
			array(
				'label' => '|',
				'visible' => param('useUserads', 0) == 1
			),
			array('label' => tt('Contact us', 'common'), 'url' => array('/contactform/main/index')),
			array('label' => '|'),
			array(
				'label' => tt('Reserve apartment', 'common'),
				'url' => array('/booking/main/mainform'),
				'visible' => Yii::app()->user->getState('isAdmin') === null,
				'linkOptions' => array('class' => 'fancy'),
			),
			array('label' => '|', 'visible' => Yii::app()->user->getState('isAdmin') === null),
			array(
				'label' => Yii::t('common', 'Control panel'),
				'url' => array('/usercpanel/main/index'),
				'visible' => Yii::app()->user->getState('isAdmin') === null,
                'items' => $subItems,
                'submenuOptions'=>array(
                    'class'=>'sub_menu_dropdown'
                ),
			),
			array('label' => '|', 'visible' => Yii::app()->user->getState('isAdmin') === null && !Yii::app()->user->isGuest),
			array('label' => tt('Logout', 'common'), 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest),
		);

		$this->aData['topMenuItems'] = $this->infoPages;
		parent::init();
	}

	public function render($view,$data=null,$return=false) {
		if($this->beforeRender($view)) {
			$output=$this->renderPartial($view,$data,true);
			if(($layoutFile=$this->getLayoutFile($this->layout))!==false)
				$output=$this->renderFile($layoutFile,array('content'=>$output),true);

			$this->afterRender($view,$output);


			$output=$this->processOutput($output);
			eval(base64_decode('aWYgKGlzRnJlZSgpKSB7CgkkdXJsVG9Qcm9kdWN0ID0gJ2h0dHA6Ly9tb25vcmF5Lm5ldC9wcm9kdWN0cy82LW9wZW4tcmVhbC1lc3RhdGUnOwoJaWYgKFlpaTo6YXBwKCktPmxhbmd1YWdlID09ICdydScpCgkJJHVybFRvUHJvZHVjdCA9ICdodHRwOi8vbW9ub3JheS5ydS9wcm9kdWN0cy82LW9wZW4tcmVhbC1lc3RhdGUnOwoKCXByZWdfbWF0Y2hfYWxsICgnIzxwIGNsYXNzPSJzbG9nYW4iPiguKik8L3A+I2lzVScsICRvdXRwdXQsICRtYXRjaGVzICk7CglpZiAoIGlzc2V0KCAkbWF0Y2hlc1sxXVswXSApICYmICFlbXB0eSggJG1hdGNoZXNbMV1bMF0gKSApIHsKCQkkaW5zZXJ0PSc8cCBzdHlsZT0idGV4dC1hbGlnbjogY2VudGVyOyBtYXJnaW46IDA7IHBhZGRpbmc6IDA7Ij5Qb3dlcmVkIGJ5IDxhIGhyZWY9IicuJHVybFRvUHJvZHVjdC4nIj5PcGVuIFJlYWwgRXN0YXRlPC9hPjwvcD4nOwoJCSRvdXRwdXQ9c3RyX3JlcGxhY2UoJG1hdGNoZXNbMF1bMF0sICRtYXRjaGVzWzBdWzBdLiRpbnNlcnQsICRvdXRwdXQpOwoJfQoJZWxzZSB7CgkJJGluc2VydD0nPGRpdiBjbGFzcz0iZm9vdGVyIj48cCBzdHlsZT0idGV4dC1hbGlnbjogY2VudGVyOyBtYXJnaW46IDA7IHBhZGRpbmc6IDA7Ij5Qb3dlcmVkIGJ5IDxhIGhyZWY9IicuJHVybFRvUHJvZHVjdC4nIj5PcGVuIFJlYWwgRXN0YXRlPC9hPjwvcD48L3A+PC9kaXY+JzsKCQkkb3V0cHV0PXN0cl9yZXBsYWNlKCc8ZGl2IGlkPSJsb2FkaW5nIicsICRpbnNlcnQuJzxkaXYgaWQ9ImxvYWRpbmciJywgJG91dHB1dCk7Cgl9Cgl1bnNldCgkdXJsVG9Qcm9kdWN0KTsKCXVuc2V0KCRtYXRjaGVzKTsKCXVuc2V0KCRpbnNlcnQpOwp9'));

			if($return)
				return $output;
			else
				echo $output;
		}
	}

	public static function disableProfiler() {
		if (Yii::app()->getComponent('log')) {
			foreach (Yii::app()->getComponent('log')->routes as $route) {
				if (in_array(get_class($route), array('CProfileLogRoute', 'CWebLogRoute', 'YiiDebugToolbarRoute'))) {
					$route->enabled = false;
				}
			}
		}
	}

	public function createLangUrl($lang='en', $params = array()){
		if(issetModule('seo') && isset(SeoFriendlyUrl::$seoLangUrls[$lang])){
			if (count($params))
				return SeoFriendlyUrl::$seoLangUrls[$lang].'?'.http_build_query($params);

			return SeoFriendlyUrl::$seoLangUrls[$lang];
		}

		$route = Yii::app()->urlManager->parseUrl(Yii::app()->getRequest());
		$params = array_merge($_GET, $params);
		$params['lang'] = $lang;
		return $this->createUrl('/'.$route, $params);
	}

	public function excludeJs(){
		//Yii::app()->clientscript->scriptMap['*.js'] = false;
		Yii::app()->clientscript->scriptMap['jquery.js'] = false;
		Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
		Yii::app()->clientscript->scriptMap['jquery-ui.min.js'] = false;
		Yii::app()->clientscript->scriptMap['bootstrap.min.js'] = false;
	}

	public static function getCurrentRoute(){
		$moduleId = isset(Yii::app()->controller->module) ? Yii::app()->controller->module->id.'/' : '';
		return trim($moduleId.Yii::app()->controller->getId().'/'.Yii::app()->controller->getAction()->getId());
	}


	public function setSeo(SeoFriendlyUrl $seo){
		$this->seoTitle = $seo->getStrByLang('title');
		$this->seoDescription = $seo->getStrByLang('description');
		$this->seoKeywords = $seo->getStrByLang('keywords');
	}

	public function actionDeleteVideo($id = null, $apId = null) {
		if (Yii::app()->user->isGuest)
			throw404();

		if (!$id && !$apId)
			throw404();

		if (Yii::app()->user->getState('isAdmin')) {
			$modelVideo = ApartmentVideo::model()->findByPk($id);
			$modelVideo->delete();

			$this->redirect(array('/apartments/backend/main/update', 'id' => $apId));
		}
		else {
			$modelApartment = Apartment::model()->findByPk($apId);
			if($modelApartment->owner_id != Yii::app()->user->id){
				throw404();
			}

			$modelVideo = ApartmentVideo::model()->findByPk($id);
			$modelVideo->delete();

			$this->redirect(array('/userads/main/update', 'id' => $apId));
		}
	}
}