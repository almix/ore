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

class Notifier {
	private $_rules;
	private $_userRules;
	private $init = 0;
	private $lang;


	public function __construct(){

	}

	public function init(){
		$this->init = 1;

		$this->lang['admin'] = Lang::getAdminMailLang();
		$this->lang['default'] = Lang::getDefaultLang();
		$this->lang['current'] = Yii::app()->language;

		Yii::app()->setLanguage($this->lang['admin']);

		$this->_rules = array(
			'onNewSimpleBookingForRent' => array(
				'fields' => array('username', 'comment', 'useremail', 'phone', 'date_start', 'date_end'),
				'i18nFields' => array('time_inVal', 'time_outVal', 'type'),
				'subject' => tt('New booking (simple order).', 'notifier'),
				'body' => tt('onNewSimpleBookingForRent_body', 'notifier')."\n",
				'active' => param('module_notifier_adminNewBooking', 1),
			),
			'onNewSimpleBookingForBuy' => array(
				'fields' => array('username', 'comment', 'useremail', 'phone'),
				'i18nFields' => array('type'),
				'subject' => tt('New booking (simple order).', 'notifier'),
				'body' => tt('onNewSimpleBookingForBuy_body', 'notifier')."\n",
				'active' => param('module_notifier_adminNewBooking', 1),
			),
			'onNewBooking' => array(
				'fields' => array('apartment_id', 'ownerEmail', 'username', 'comment', 'useremail', 'phone', 'date_start', 'date_end'),
				'i18nFields' => array('time_inVal', 'time_outVal'),
				'subject' => tt('New booking.', 'notifier'),
				'body' => tt('onNewBooking_body', 'notifier')."\n",
				'active' => param('module_notifier_adminNewBooking', 1),
			),
			'onNewUser' => array(
				'fields' => array('email', 'username'),
				'subject' => tt('User registration', 'notifier'),
				'body' => tt('New user ::username ::email registered.', 'notifier')."\n".
					tt('You can view and manage users via:', 'notifier').' ::host::url',
				'url' => array(
					'/users/backend/main/admin',
				),
				'active' => param('module_notifier_adminNewUser', 1),
			),
			'onRegistrationUser' => array(
				'fields' => array('email', 'username'),
				'subject' => tt('User registration', 'notifier'),
				'body' => tt('New user ::username ::email registered.', 'notifier')."\n".
					tt('You can view and manage users via:', 'notifier').' ::host::url',
				'url' => array(
					'/users/backend/main/admin',
				),
				'active' => param('module_notifier_adminNewUser', 1),
			),
			'onNewContactform' => array(
				'fields' => array('name', 'email', 'phone', 'body'),
				'subject' => tt('New message (contact form)', 'notifier'),
				'body' => tt('New message from ::name (::email ::phone). Message text: ::body', 'notifier')."\n",
				'active' => param('module_notifier_adminNewContactform', 1),
			),
			'onOfflinePayment' => array(
				'fields' => array('amount', 'currency_charcode'),
				'subject' => tt('New payment through a bank.', 'notifier'),
				'body' => tt('New payment through a bank in the amount of ::amount ::currency_charcode', 'notifier'),
				'active' => param('module_notifier_adminNewPayment', 1)
			),
			'onRequestProperty' => array(
				'fields' => array('senderName', 'senderEmail', 'senderPhone', 'body', 'ownerName', 'ownerEmail', 'apartmentUrl'),
				'subject' => tt('copy_request_for_property_from', 'notifier'). ' '.Yii::app()->controller->createAbsoluteUrl('/site/index'),
				'body' => tt('Hello, administrator!', 'notifier')."\n"
						.tt('This message has been sent via a contact form on the site ::fullhost', 'notifier')."\n"
						.tt('Sender: ::senderName', 'notifier')."\n"
						.tt('About listing: ::apartmentUrl', 'notifier')."\n"
						.tt('Sender email: ::senderEmail', 'notifier')."\n"
						.tt('Sender phone number: ::senderPhone', 'notifier')."\n"
						.tt('Message: ::body', 'notifier')."\n"
						."\n"."\n"
						.tt('Owner name: ::ownerName', 'notifier')."\n"
						.tt('Owner email: ::ownerEmail', 'notifier')."\n",
				'active' => param('module_request_property_send_admin', 1),
			),
			'onNewComment' => array(
				'fields' => array('rating', 'email', 'body', 'name'),
				'subject' => Yii::t('module_notifier', 'New comment added.'),
				'body' => Yii::t('module_notifier', 'New comment was added. From ::name (::email), rating: ::rating. Message: ::body')."\n".
						Yii::t('module_notifier', 'You can view it at').' ::host'.Yii::app()->controller->createUrl('/comments/backend/main/index'),
				'active' => param('module_notifier_adminNewComment', 1),
			),
			'onNewApartment' => array(
				'fields' => array('id'),
				'subject' => Yii::t('module_notifier', 'New listing added.'),
				'body' => Yii::t('module_notifier', 'New listing was added. ( Apartment ID ::id ).')."\n".
					Yii::t('module_notifier', 'You can view it at').' ::host'.Yii::app()->controller->createUrl('/apartments/backend/main/admin'),
				'active' => param('module_notifier_adminNewApartment', 1),
			),
			'onNewComplain' => array(
				'fields' => array('apartment_id', 'email', 'name', 'body'),
				'subject' => Yii::t('module_notifier', 'New complain added.'),
				'body' => Yii::t('module_notifier', 'New complain was added. From ::name (::email). Complain text: ::body')."\n".
					Yii::t('module_notifier', 'You can view it at').' ::host'.Yii::app()->controller->createUrl('/apartmentsComplain/backend/main/admin'),
				'active' => param('module_notifier_adminNewComplain', 1),
			),
		);

		Yii::app()->setLanguage($this->lang['current']);
		$this->setLang();

		$this->_userRules = array(
			'onNewBookingOwner' => array(
				'fields' => array('apartment_id', 'username', 'comment', 'useremail', 'phone', 'date_start', 'date_end'),
				'i18nFields' => array('time_inVal', 'time_outVal'),
				'subject' => tt('New booking.', 'notifier'),
				'body' => tt('onNewBookingOwner_body', 'notifier')."\n",
				'active' => param('module_notifier_ownerNewBooking', 1),
			),
			'onNewUser' => array(
				'fields' => array('email', 'password', 'activateLink'),
				'subject' => tt('User registration', 'notifier'),
				'body' => tt('Welcome to ::fullhost !', 'notifier')."\n".tt('Your login is: ::email', 'notifier')."\n"
					.tt('Your password is: ::password', 'notifier')."\n"
					.tt('Before use a site', 'notifier')."\n"
					.tt('You should activate the account', 'notifier')."\n"
					.tt('Link to activate account: ::activateLink', 'notifier')."\n"
					.tt('You can login to your control panel via:', 'notifier').' ::host::url'."\n",
				'url' => array(
					'/usercpanel/main/index',
				),
				'active' => param('module_notifier_userNewUser', 1),
			),
			'onRecoveryPassword' => array(
				'fields' => array('email', 'temprecoverpassword', 'recoverPasswordLink'),
				'subject' => tt('Activating a new password', 'notifier'),
				'body' => tt('recover_pass_first_help', 'notifier')."\n"
					.tt('New password: ::temprecoverpassword', 'notifier')."\n"
					.tt('Before you use your new password click: ::recoverPasswordLink', 'notifier')."\n"
					.tt('You can login to your control panel via:', 'notifier').' ::host::url'."\n",
				'url' => array(
					'/usercpanel/main/index',
				),
				'active' => 1,
			),
			'onRegistrationUser' => array(
				'fields' => array('email', 'password', 'activateLink'),
				'subject' => tt('User registration', 'notifier'),
				'body' => tt('Welcome to ::fullhost !', 'notifier')."\n".tt('Your login is: ::email', 'notifier')."\n"
					.tt('Your password is: ::password', 'notifier')."\n"
					.tt('Before use a site', 'notifier')."\n"
					.tt('You should activate the account', 'notifier')."\n"
					.tt('Link to activate account: ::activateLink', 'notifier')."\n"
					.tt('You can login to your control panel via:', 'notifier').' ::host::url'."\n",
				'url' => array(
					'/usercpanel/main/index',
				),
				'active' => param('module_notifier_userNewUser', 1),
			),
			'onRequestProperty' => array(
				'fields' => array('senderName', 'senderEmail', 'senderPhone', 'body', 'ownerName', 'apartmentUrl'),
				'subject' => tt('request_for_property_from', 'notifier'). ' '.Yii::app()->controller->createAbsoluteUrl('/site/index'),
				'body' => tt('Hello, ::ownerName!', 'notifier')."\n"
						.tt('This message has been sent via a contact form on the site ::fullhost', 'notifier')."\n"
						.tt('By the user: ::senderName', 'notifier')."\n"
						.tt('About your listing ::apartmentUrl', 'notifier')."\n"
						.tt('To answer a question asked by the user, you have to send him/her a letter to his/her email address: ::senderEmail', 'notifier')."\n"
						.tt('Or you may call the user’s phone number: ::senderPhone', 'notifier')."\n"
						.tt('Message: ::body', 'notifier')."\n"
						."\n"."\n"
						.tt('additional_info_for_user', 'notifier')."\n",
				'active' => param('module_request_property_send_user', 1),
			),
		);
		$this->restoreLang();
	}

	public function setLang(){
		if(Yii::app()->user->getState('isAdmin')){
			Yii::app()->setLanguage($this->lang['default']);
		}
	}
	public function restoreLang(){
		if(Yii::app()->user->getState('isAdmin') && $this->lang['current']){
			Yii::app()->setLanguage($this->lang['current']);
		}
	}

	public function raiseEvent($eventName, $model = null, $userId = 0, $forceEmail = false){
		if($this->init == 0){
			$this->init();
		}
		if(isset($this->_rules[$eventName])){
			$active = isset($this->_rules[$eventName]['active']) ? $this->_rules[$eventName]['active'] : 0;
			if($active){
				$this->_processEvent($this->_rules[$eventName], $model, null, true);
			}
		}

		if($userId)
			$user = User::model()->findByPk($userId);
		else
			$user = Yii::app()->user;

		if(isset($this->_userRules[$eventName]) && $user){
			$active = isset($this->_userRules[$eventName]['active']) ? $this->_userRules[$eventName]['active'] : 0;
			if($active){
				$this->_processEvent($this->_userRules[$eventName], $model, $user, false, $forceEmail);
			}
		}
	}

	private function _processEvent($rule, $model, $user = null, $toAdmin = false, $forceEmail = false){

		if($toAdmin)
			$lang = 'admin';
		else
			$lang = Yii::app()->user->getState('isAdmin') ? 'default' : 'current';

		$body = '';
		if(isset($rule['body'])){
			$body = $rule['body'];
			$body = str_replace('::host', Yii::app()->request->hostInfo, $body);
			$body = str_replace('::fullhost', Yii::app()->controller->createAbsoluteUrl('/site/index'), $body);

			if($user && !isset($model->username) && !isset($model->ownerName)){
				$body = str_replace('::username', $user->username, $body);
			}
			if(isset($rule['url']) && $model){
				$params = array();
				if(isset($rule['url'][1])){
					foreach($rule['url'][1] as $param){
						$params[$param] = $model->$param;
					}
					$params['lang'] = $lang;
				}
				$url = Yii::app()->controller->createUrl($rule['url'][0], $params);
				$body = str_replace('::url', $url, $body);
			}

			if(isset($rule['fields']) && $model){
				foreach($rule['fields'] as $field){
					$body = str_replace('::'.$field, CHtml::encode($model->$field), $body);
				}
			}

			if(isset($rule['i18nFields']) && $model){
				foreach($rule['i18nFields'] as $field){
					$field_val = $model->$field;
					$body = str_replace(':i18n:'.$field, CHtml::encode($field_val[$lang]), $body);
				}
			}
			$body = str_replace("\n.", "\n..", $body);

		}

		if ($forceEmail) {
			$to = $forceEmail;
		}
		elseif ($toAdmin) {
		    $to = param('adminEmail');
		}
		else {
		    if (isset($model->useremail) && $model->useremail) {
				$to = $model->useremail;
		    } elseif ($user){
				$to = $user->email;
		    } else {
				$to = param('adminEmail');
		    }
		}

        if($body){
	        Yii::import('application.extensions.mailer.EMailer');
	        $mailer = new EMailer();

            if (param('mailUseSMTP', 0)) {
	            $mailer->IsSMTP();
				$mailer->SMTPAuth = true;

	            $mailer->Host = param('mailSMTPHost', 'localhost');
	            $mailer->Port = param('mailSMTPPort', 25);

	            $mailer->Username = param('mailSMTPLogin');  // SMTP login
	            $mailer->Password = param('mailSMTPPass'); // SMTP password
            }

	        $mailer->From = param('adminEmail');
	        $mailer->FromName = User::getAdminName();

		    $mailer->AddAddress($to);

            if(isset($rule['subject']))
	            $mailer->Subject = $rule['subject'];

	        $mailer->Body = $body;
	        $mailer->CharSet = 'UTF-8';
	        $mailer->IsHTML(false);

            if (!$mailer->Send()){
				throw new CHttpException(503, tt('message_not_send', 'notifier'));
                //showMessage(tc('Error'), tt('message_not_send', 'notifier'));
            }
        }
	}

}