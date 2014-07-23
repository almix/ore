<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<title><?php echo CHtml::encode($this->seoTitle ? $this->seoTitle : $this->pageTitle); ?></title>
	<meta name="description" content="<?php echo CHtml::encode($this->seoDescription ? $this->seoDescription : $this->pageDescription); ?>" />
	<meta name="keywords" content="<?php echo CHtml::encode($this->seoKeywords ? $this->seoKeywords : $this->pageKeywords); ?>" />

	<?php Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/form.css', 'screen, projection'); ?>

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />-->
	<link media="screen, projection" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/styles.css" rel="stylesheet" />

	<!--[if IE]> <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->

	<link rel="icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />

	<?php
	if(param('useYandexMap') == 1){
		Yii::app()->getClientScript()->registerScriptFile(
			'http://api-maps.yandex.ru/2.0/?load=package.standard,package.clusters&coordorder=longlat&lang='.CustomYMap::getLangForMap(),
			CClientScript::POS_END);
	} else if (param('useGoogleMap') == 1){
		Yii::app()->getClientScript()->registerScriptFile('https://maps.google.com/maps/api/js??v=3.5&sensor=false&language='.Yii::app()->language.'', CClientScript::POS_END);
		Yii::app()->getClientScript()->registerScriptFile('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js', CClientScript::POS_END);
	}
	?>
</head>

<body>
	<div id="container">
		<noscript><div class="noscript"><?php echo Yii::t('common', 'Allow javascript in your browser for comfortable use site.'); ?></div></noscript>
		<div class="logo">
			<a title="<?php echo Yii::t('common', 'Go to main page'); ?>" href="<?php echo Yii::app()->controller->createAbsoluteUrl('/'); ?>">
				<img width="291" height="54" alt="<?php echo CHtml::encode($this->pageDescription); ?>" src="<?php echo Yii::app()->request->baseUrl; ?>/images/pages/logo-open-re.png" id="logo" />
			</a>
		</div>

		<?php
		if(!isFree()){
            if(count(Lang::getActiveLangs()) > 1){
                $this->widget('application.modules.lang.components.langSelectorWidget', array( 'type' => 'links' ));
            }
            if(count(Currency::getActiveCurrency()) >1){
                $this->widget('application.modules.currency.components.currencySelectorWidget');
            }
		}
		?>

		<div id="user-cpanel"  class="menu_item">
			<?php
			   if(!isset($adminView)){
					$this->widget('zii.widgets.CMenu',array(
						'id' => 'nav',
						'items'=>$this->aData['userCpanelItems'],
						'htmlOptions' => array('class' => 'dropDownNav'),
					));
				} else {
					$this->widget('zii.widgets.CMenu',array(
						'id' => 'dropDownNav',
						'items'=>CMap::mergeArray($this->aData['topMenuItems'], array(array('label' => Yii::t('common', 'Logout'), 'url'=>array('/site/logout')))),
						'htmlOptions' => array('class' => 'dropDownNav adminTopNav'),
					));
				}
			?>
		</div>

		<?php
		if(!isset($adminView)){
		?>
			<div id="search" class="menu_item">
				<?php
					$this->widget('application.extensions.YandexShareApi', array(
						'services' => param('shareItems', 'yazakladki,moikrug,linkedin,vkontakte,facebook,twitter,odnoklassniki')
					));

					$this->widget('zii.widgets.CMenu',array(
						'id' => 'dropDownNav',
						'items'=>$this->aData['topMenuItems'],
						'htmlOptions' => array('class' => 'dropDownNav'),
					));
				?>
			</div>
		<?php
		} else {
			echo '<hr />';
			?>

			<div class="admin-top-menu">
				<?php
				$this->widget('zii.widgets.CMenu', array(
					'items'=>$this->aData['adminMenuItems'],
					'encodeLabel' => false,
					'submenuHtmlOptions' => array('class' => 'admin-submenu'),
					'htmlOptions' => array('class' => 'adminMainNav')
				));
				?>
			</div>
		<?php
		}
		?>

		<div class="content">
			<?php echo $content; ?>
			<div class="clear"></div>
		</div>

		<?php
			if(issetModule('advertising')) {
				$this->renderPartial('//../modules/advertising/views/advert-bottom', array());
			}
		?>

		<div class="footer">
			<?php echo getGA(); ?>
			<p class="slogan">&copy;&nbsp;<?php echo CHtml::encode(Yii::app()->name).', '.date('Y'); ?></p>
			<!-- <?php echo param('version_name').' '.param('version'); ?> -->
		</div>
	</div>

	<div id="loading" style="display:none;"><?php echo Yii::t('common', 'Loading content...'); ?></div>
	<?php
    Yii::app()->clientScript->registerScript('main-vars', '
            var BASE_URL = '.CJavaScript::encode(Yii::app()->baseUrl).';
            var params = {
                change_search_ajax: '.param("change_search_ajax", 1).'
            }
		', CClientScript::POS_HEAD);

	Yii::app()->clientScript->registerCoreScript('jquery');
	Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.dropdownPlain.js', CClientScript::POS_HEAD);
	Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/common.js', CClientScript::POS_HEAD);
	Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/habra_alert.js', CClientScript::POS_END);

	$this->widget('application.modules.fancybox.EFancyBox', array(
		'target'=>'a.fancy',
		'config'=>array(
				'ajax' => array('data'=>"isFancy=true"),
				'titlePosition' => 'inside',
			),
		)
	);

	if(Yii::app()->user->getState('isAdmin')){
		Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/tooltip/jquery.tipTip.minified.js', CClientScript::POS_HEAD);
		Yii::app()->clientScript->registerScript('adminMenuToolTip', '
			$(function(){
				$(".adminMainNavItem").tipTip({maxWidth: "auto", edgeOffset: 10, delay: 200});
			});
		', CClientScript::POS_READY);
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/tooltip/tipTip.css" />

		<div class="admin-menu-small" onclick="location.href='<?php echo Yii::app()->request->baseUrl; ?>/apartments/backend/main/admin'" style="cursor: pointer;">
			<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/adminmenu/administrator.png" alt="<?php echo Yii::t('common','Administration'); ?>" title="<?php echo Yii::t('common','Administration'); ?>" class="adminMainNavItem" />
		</div>
	<?php } ?>
</body>
</html>