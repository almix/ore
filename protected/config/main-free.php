<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

require_once(dirname(__FILE__) . '/../helpers/common.php');
require_once(dirname(__FILE__) . '/../helpers/strings.php');
Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/bootstrap');

$config = array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'Open Real Estate',

	'sourceLanguage' => 'en',
	'language' => 'ru',

	'preload' => array(
		'log',
		'configuration', // preload configuration
	),

	'onBeginRequest' => array('BeginRequest', 'updateStatusAd'),

	// autoloading model and component classes
	'import' => array(
		'ext.eoauth.*',
		'ext.eoauth.lib.*',
		'ext.lightopenid.*',
		'ext.eauth.*',
		'ext.eauth.services.*',
		'ext.eauth.custom_services.CustomGoogleService',
		'ext.eauth.custom_services.CustomVKService',
		'ext.eauth.custom_services.CustomFBService',
		'ext.eauth.custom_services.CustomTwitterService',

		'application.models.*',
		'application.components.*',

		'application.modules.configuration.components.*',
		'application.modules.notifier.components.Notifier',
		'application.modules.booking.models.*',

		'application.modules.comments.models.Comment',
		'application.modules.windowto.models.WindowTo',
		'application.modules.apartments.models.*',
		'application.modules.news.models.*',
		'application.extensions.image.Image',
		'application.modules.selecttoslider.models.SelectToSlider',
		'application.modules.similarads.models.SimilarAds',
		'application.modules.menumanager.models.Menu',
		'application.modules.windowto.models.WindowTo',
		'application.modules.apartments.components.*',
		'application.modules.apartmentCity.models.ApartmentCity',
		'application.modules.apartmentObjType.models.ApartmentObjType',
		'application.modules.translateMessage.models.TranslateMessage',

		'application.components.behaviors.ERememberFiltersBehavior',
		'application.modules.service.models.Service',

		'application.modules.socialauth.models.SocialauthModel',
		'application.modules.antispam.components.MathCCaptchaAction',

		'application.modules.images.models.*',
		'application.modules.images.components.*',

		'application.modules.lang.models.Lang',

		'zii.behaviors.CTimestampBehavior',
		'application.modules.apartmentsComplain.models.ApartmentsComplain',
		'application.modules.apartmentsComplain.models.ApartmentsComplainReason',
	),

	'modules' => array(
		'news',
		'referencecategories',
		'referencevalues',
		'apartments',
		'apartmentObjType',
		'apartmentCity',
		'comments',
		'booking',
		'windowto',
		'contactform',
		'articles',
		'usercpanel',
		'users',
		'quicksearch',
		'configuration',
		'timesin',
		'timesout',
		'adminpass',
		'specialoffers',
		'install',
		'selecttoslider',
		'similarads',
		'menumanager',
		'userads',
		'translateMessage',
		'service',
		'socialauth',
		'antispam',
		'rss',
		'images',
		'apartmentsComplain',
        'formdesigner',

		// uncomment the following to enable the Gii tool
		/*'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'admin1',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
			'generatorPaths'=>array(
				'bootstrap.gii', // since 0.9.1
			),
		),*/

	),

	'components' => array(
		'loid' => array(
			'class' => 'application.extensions.lightopenid.loid',
		),
		'eauth' => array(
			// yii-eauth-1.1.8
			'class' => 'ext.eauth.EAuth',
			'popup' => true, // Use popup windows instead of redirect to site of provider
		),

		'user' => array(
			// enable cookie-based authentication
			'allowAutoLogin' => true,
		),

		'configuration' => array(
			'class' => 'Configuration',
			'cachingTime' => 0, // caching configuration for 180 days
		),

		// uncomment to minify js and css
		/*'clientScript' => array(
			'class' => 'ext.minify.EClientScript',
			'combineScriptFiles' => true, // By default this is set to false, set this to true if you'd like to combine the script files
			'combineCssFiles' => true, // By default this is set to false, set this to true if you'd like to combine the css files
			'optimizeScriptFiles' => false,	// @since: 1.1
			'optimizeCssFiles' => false,	// @since: 1.1
			'cssForIgnore' => array('bootstrap.min.css', 'jquery-ui-1.7.1.custom.css', 'jquery-ui.multiselect.css',
				'bootstrap-responsive.min.css'),
			'scriptsForIgnore' => array('jquery.js', 'jquery.min.js', 'jquery.ui.js', 'jquery-ui.min.js',
				'bootstrap.min.js', 'jquery-ui-i18n.min.js', 'jquery.jcarousel.min.js'),
		),*/

		'cache' => array(
			'class' => 'system.caching.CFileCache',
			/*'class'=>'system.caching.CMemCache',
			//'useMemcached' => true,
			'servers'=>array(
				array('host'=>'127.0.0.1', 'port'=>11211),
			),*/
		),

		'urlManager' => array(
			'urlFormat' => 'path',
			'showScriptName' => false,
			'class' => 'application.components.CustomUrlManager',
			'rules' => array(
				'sitemap.xml' => 'sitemap/main/viewxml',

				'version' => '/site/version',

				'sell' => 'quicksearch/main/mainsearch/type/2',
				'rent' => 'quicksearch/main/mainsearch/type/1',

				'property/<id:\d+>' => 'apartments/main/view',
				'property/<url:[-a-zA-Z0-9_+\.]{1,255}>'=>'apartments/main/view',
				'news' => 'news/main/index',
				'news/<id:\d+>' => 'news/main/view',
				'faq' => 'articles/main/index',
				'faq/<id:\d+>' => 'articles/main/view',
				'contact-us' => 'contactform/main/index',
				'specialoffers' => 'specialoffers/main/index',
				'sitemap' => 'sitemap/main/index',
				'page/<id:\d+>' => 'menumanager/main/view',

				'rss' => 'rss/main/subscribe',
				'rss/<feed:\w+>' => 'rss/main/read',

				'service-<serviceId:\d+>' => 'quicksearch/main/mainsearch',
				'install/config' => 'install/main/config',

				'<controller:(quicksearch|specialoffers)>/main/index' => '<controller>/main/index',
				'' => 'site/index',
				//'<_m>/<_c>/<_a>*' => '<_m>/<_c>/<_a>',
				//'<_c>/<_a>*' => '<_c>/<_a>',
				//'<_c>' => '<_c>',


				'/property/'=>'quicksearch/main/mainsearch',
				'<module:\w+>/backend/<controller:\w+>/<action:\w+>' => '<module>/backend/<controller>/<action>', // CGridView ajax
			),
		),

		'mailer' => array(
			'class' => 'application.extensions.mailer.EMailer',
		),

		//'db'=>require(dirname(__FILE__) . '/db.php'),

		'errorHandler' => array(
			'errorAction' => 'site/error',
		),
//		'log'=>array(
//			'class'=>'CLogRouter',
//			'routes'=>array(
//				array(
//					'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
//					'ipFilters'=>array('127.0.0.1'),
//				),
//			),
//		),
		/*'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages

				//array(
				//	'class'=>'CWebLogRoute',
				//),
			),
		),*/
		'messages' => array(
			'class' => 'DbMessageSource',
			'forceTranslation' => true,
			'onMissingTranslation' => array('CustomEventHandler', 'handleMissingTranslation'),
		),

		'messagesInFile' => array(
			'class' => 'CPhpMessageSource',
			'forceTranslation' => true,
		),

		'bootstrap'=>array(
			'class'=>'bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
		),
	),

	'params' => array(
		'module_rss_itemsPerFeed' => 20,
		'allowedImgExtensions' => array('jpg', 'jpeg', 'gif', 'png'),
		'maxImgFileSize' => 8 * 1024 * 1024, // maximum file size in bytes
		'minImgFileSize' => 5 * 1024, // min file size in bytes
		'langToInstall' => 'ru',
	),
);

$db = require(dirname(__FILE__) . '/db.php');
if($db === 1){
	$db = array();
}

return CMap::mergeArray($config, $db);
