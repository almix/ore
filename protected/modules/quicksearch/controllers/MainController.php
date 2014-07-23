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

	public $roomsCount;
	public $roomsCountMin;
	public $roomsCountMax;
	public $floorCount;
	public $floorCountMin;
	public $floorCountMax;
	public $squareCount;
	public $squareCountMin;
	public $squareCountMax;
	public $price;
	public $priceSlider = array();
	public $metroStations;
	public $selectedStations;
	public $selectedCountry;
	public $selectedRegion;
	public $selectedCity;
	public $apType;
	public $objType;

	public function actionIndex(){
        $href = Yii::app()->getBaseUrl(true).'/'.Yii::app()->request->getPathInfo();
        Yii::app()->clientScript->registerLinkTag('canonical', null, $href);
        unset($href);

		$criteria = new CDbCriteria;
		$criteria->addCondition('active = ' . Apartment::STATUS_ACTIVE);
		if(param('useUserads')) {
			$criteria->addCondition('owner_active = ' . Apartment::STATUS_ACTIVE);
		}

		if(isset($_POST['is_ajax'])) {
			$this->renderPartial('index', array(
				'criteria' => $criteria,
				'apCount' => null,
			), false, true);
		} else {
			$this->render('index', array(
				'criteria' => $criteria,
				'apCount' => null,
			));
		}
	}

	public function getExistRooms(){
		return Apartment::getExistsRooms();
	}

	public function actionMainsearch($rss = null){
        $href = Yii::app()->getBaseUrl(true).'/'.Yii::app()->request->getPathInfo();
        Yii::app()->clientScript->registerLinkTag('canonical', null, $href);
        unset($href);

		if(Yii::app()->request->getParam('currency')) {
			setCurrency();
			$this->redirect(array('mainsearch'));
		}

		$criteria = new CDbCriteria;
		$criteria->addCondition('active = ' . Apartment::STATUS_ACTIVE);
		if(param('useUserads')) {
			$criteria->addCondition('owner_active = ' . Apartment::STATUS_ACTIVE);
		}

		// rooms
		if(issetModule('selecttoslider') && param('useRoomSlider') == 1) {
			$roomsMin = Yii::app()->request->getParam('roomsMin');
			$roomsMax = Yii::app()->request->getParam('roomsMax');

			if($roomsMin || $roomsMax) {
				$criteria->addCondition('num_of_rooms >= :roomsMin AND num_of_rooms <= :roomsMax');
				$criteria->params[':roomsMin'] = $roomsMin;
				$criteria->params[':roomsMax'] = $roomsMax;

				$this->roomsCountMin = $roomsMin;
				$this->roomsCountMax = $roomsMax;
			}
		} else {
			$rooms = Yii::app()->request->getParam('rooms');
			if($rooms) {
				if($rooms == 4) {
					$criteria->addCondition('num_of_rooms >= :rooms');
				} else {
					$criteria->addCondition('num_of_rooms = :rooms');
				}
				$criteria->params[':rooms'] = $rooms;

				$this->roomsCount = $rooms;
			}
		}

		// floor
		if(issetModule('selecttoslider') && param('useFloorSlider') == 1) {
			$floorMin = Yii::app()->request->getParam('floorMin');
			$floorMax = Yii::app()->request->getParam('floorMax');

			if($floorMin || $floorMax) {
				$criteria->addCondition('floor >= :floorMin AND floor <= :floorMax');
				$criteria->params[':floorMin'] = $floorMin;
				$criteria->params[':floorMax'] = $floorMax;

				$this->floorCountMin = $floorMin;
				$this->floorCountMax = $floorMax;
			}
		} else {
			$floor = Yii::app()->request->getParam('floor');
			if($floor) {
				$criteria->addCondition('floor = :floor');
				$criteria->params[':floor'] = $floor;

				$this->floorCount = $floor;
			}
		}

		// square
		if(issetModule('selecttoslider') && param('useSquareSlider') == 1) {
			$squareMin = Yii::app()->request->getParam('squareMin');
			$squareMax = Yii::app()->request->getParam('squareMax');

			if($squareMin || $squareMax) {
				$criteria->addCondition('square >= :squareMin AND square <= :squareMax');
				$criteria->params[':squareMin'] = $squareMin;
				$criteria->params[':squareMax'] = $squareMax;

				$this->squareCountMin = $squareMin;
				$this->squareCountMax = $squareMax;
			}
		} else {
			$square = Yii::app()->request->getParam('square');
			if($square) {
				$criteria->addCondition('square <= :square');
				$criteria->params[':square'] = $square;

				$this->squareCount = $square;
			}
		}


		if(issetModule('metrostations')) {
			// metro
			$metro = Yii::app()->request->getParam('metro-select');
			if($metro) {
				$apartmentIds = $this->getApIds($metro);
				$this->selectedStations = $metro;
				$criteria->addInCondition('t.id', $apartmentIds);
			}
		}

		if (issetModule('location') && param('useLocation', 1)) {
			$country = Yii::app()->request->getParam('country');
			if($country) {
				$this->selectedCountry = $country;
				$criteria->compare('loc_country', $country);
			}

			$region = Yii::app()->request->getParam('region');
			if($region) {
				$this->selectedRegion = $region;
				$criteria->compare('loc_region', $region);
			}

			$city = Yii::app()->request->getParam('city');
			if($city) {
				$this->selectedCity = $city;
				$criteria->addInCondition('t.loc_city', $city);
			}
		} else {
			$city = Yii::app()->request->getParam('city');
			if($city) {
				$this->selectedCity = $city;
				$criteria->addInCondition('t.city_id', $city);
			}
		}

		$this->objType = Yii::app()->request->getParam('objType');
		if($this->objType) {
			$criteria->addCondition('obj_type_id = :objType');
			$criteria->params[':objType'] = $this->objType;
		}

		// type
		$this->apType = Yii::app()->request->getParam('apType');
		if($this->apType) {
			$criteria->addCondition('price_type = :apType');
			$criteria->params[':apType'] = $this->apType;
		}

		$type = Yii::app()->request->getParam('type');
		if($type) {
			$criteria->addCondition('type = :type');
			$criteria->params[':type'] = $type;
		}

		// price
		if(issetModule('selecttoslider') && param('usePriceSlider') == 1) {
			$priceMin = Yii::app()->request->getParam("price_{$this->apType}_Min");
			$priceMax = Yii::app()->request->getParam("price_{$this->apType}_Max");

			if($priceMin || $priceMax) {
				$criteria->addCondition('(price >= :priceMin AND price <= :priceMax) OR (is_price_poa = 1)');

				if(issetModule('currency')){
					$criteria->params[':priceMin'] = floor(Currency::convertToDefault($priceMin));
					$criteria->params[':priceMax'] = ceil(Currency::convertToDefault($priceMax));
				} else {
					$criteria->params[':priceMin'] = $priceMin;
					$criteria->params[':priceMax'] = $priceMax;
				}

				$this->priceSlider["min_{$this->apType}"] = $priceMin;
				$this->priceSlider["max_{$this->apType}"] = $priceMax;
			}
		} else {
			$price = Yii::app()->request->getParam('price');

			if(issetModule('currency')){
				$priceDefault = ceil(Currency::convertToDefault($price));
			} else {
				$priceDefault = $price;
			}

			if($priceDefault) {
				$criteria->addCondition('(price <= :price) OR (is_price_poa = 1)');
				$criteria->params[':price'] = $priceDefault;

				$this->price = $price;
			}
		}

		// поиск объявлений владельца
		$this->userListingId = Yii::app()->request->getParam('userListingId');
		if($this->userListingId) {
			$criteria->addCondition('owner_id = :userListingId');
			$criteria->params[':userListingId'] = $this->userListingId;
		}

		$filterName = null;
		// Поиск по справочникам - клик в просмотре профиля анкеты
		if(param('useReferenceLinkInView')) {
			if(Yii::app()->request->getQuery('serviceId', false)) {
				$serviceId = Yii::app()->request->getQuery('serviceId', false);
				if($serviceId) {
					$serviceIdArray = explode('-', $serviceId);
					if(is_array($serviceIdArray) && count($serviceIdArray) > 0) {
						$value = (int)$serviceIdArray[0];

						$sql = 'SELECT DISTINCT apartment_id FROM {{apartment_reference}} WHERE reference_value_id = ' . $value;
						$apartmentIds = Yii::app()->db->cache(param('cachingTime', 1209600), Apartment::getDependency())->createCommand($sql)->queryColumn();
						//$apartmentIds = Yii::app()->db->createCommand($sql)->queryColumn();
						$criteria->addInCondition('t.id', $apartmentIds);


						Yii::app()->getModule('referencevalues');

						$sql = 'SELECT title_' . Yii::app()->language . ' FROM {{apartment_reference_values}} WHERE id = ' . $value;
						$filterName = Yii::app()->db->cache(param('cachingTime', 1209600), ReferenceValues::getDependency())->createCommand($sql)->queryScalar();

						if($filterName) {
							$filterName = CHtml::encode($filterName);
						}
					}
				}
			}
		}

		if($rss && issetModule('rss')) {
			$this->widget('application.modules.rss.components.RssWidget', array(
				'criteria' => $criteria,
			));
		}

		// find count
		$apCount = Apartment::model()->count($criteria);

        $countAjax = Yii::app()->request->getParam('countAjax');
        if($countAjax){
            echo CJSON::encode(array(
                'count' => $apCount,
                'string' => Yii::t('common', '{n} listings', array($apCount, '{n}' => $apCount))
            ));
            Yii::app()->end();
        }

        Yii::app()->user->setState('searchUrl', Yii::app()->request->requestUri);

		if(isset($_POST['is_ajax'])) {
			$this->renderPartial('index', array(
				'criteria' => $criteria,
				'apCount' => $apCount,
				'filterName' => $filterName,
			), false, true);
		} else {
			$this->render('index', array(
				'criteria' => $criteria,
				'apCount' => $apCount,
				'filterName' => $filterName,
			));
		}
	}

	public function getApIds($ids){
		return MetroStation::getApartmentsIds($ids);
	}

}
