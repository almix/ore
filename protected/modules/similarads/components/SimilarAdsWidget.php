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

class SimilarAdsWidget extends CWidget {

	public function getViewPath($checkTheme=false){
		return Yii::getPathOfAlias('application.modules.similarads.views');
	}

	public function viewSimilarAds($data = null) {
		$similarAds = new SimilarAds;

		$criteria = new CDbCriteria;
		$criteria->addCondition('active = '.Apartment::STATUS_ACTIVE);
		if (param('useUserads'))
			$criteria->addCondition('owner_active = '.Apartment::STATUS_ACTIVE);

		if ($data->id) {
			$criteria->addCondition('t.id != :id');
			$criteria->params[':id'] = $data->id;
		}
		if ($data->city_id) {
			$criteria->addCondition('city_id = :city_id');
			$criteria->params[':city_id'] = $data->city_id;
		}
		if ($data->obj_type_id) {
			$criteria->addCondition('obj_type_id = :obj_type_id');
			$criteria->params[':obj_type_id'] = $data->obj_type_id;
		}
		if ($data->type) {
			$criteria->addCondition('type = :type');
			$criteria->params[':type'] = $data->type;
		}
		if ($data->price_type) {
			$criteria->addCondition('price_type = :price_type');
			$criteria->params[':price_type'] = $data->price_type;
		}

		$criteria->limit = param('countListitng'.User::getModeListShow(), 10);
		$criteria->order = 't.id ASC';

		$ads = $similarAds->getSimilarAds($criteria);

		if($ads){
			$similarAds->publishAssets();
		}

		$this->render('widgetSimilarAds_list', array(
			'ads' => $ads,
		));
	}
}