<?php
/**********************************************************************************************
*	copyright			:	(c) 2013 Monoray
*	website				:	http://www.monoray.ru/
*	contact us			:	http://www.monoray.ru/contact
***********************************************************************************************/

class ConfigurationModel extends ParentModel {
	public $allowEmpty = array('adminPhone', 'adminSkype', 'adminICQ', 'adminAddress', 'mailSMTPLogin', 'mailSMTPPass', 'urlExtension', 'priceThousandsSeparator', 'priceDecimalsPoint');

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{configuration}}';
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => null,
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public function rules() {
		return array(
			array('name, value', 'settingsValidator'),
			array('name, value', 'length', 'max' => 255),
            array('value', 'safe', 'on' => 'search'),
		);
	}

	public function settingsValidator() {
		if (!in_array($this->name, $this->allowEmpty) && empty($this->value)) {
			$this->addError('value', tt('Fill a field'));
		}
	}

    public function getTitle(){
        return tt($this->name);
    }

	public function attributeLabels() {
		return array(
			'value' => ConfigurationModule::t('Value'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

        $criteria->condition = 'type != "hidden"';

		$criteria->compare('value', $this->value);

        $section_filter = Yii::app()->request->getQuery('section_filter', 'main');

        if($section_filter != 'all'){
            $criteria->compare('section', $section_filter);
        }

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'section',
			),
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

	public function beforeSave() {
		Configuration::clearCache();
		return parent::beforeSave();
	}

    public static function getAdminValue($model){
        if($model->type == 'bool') {
            $url = Yii::app()->controller->createUrl("activate",
                array(
                    'id' => $model->id,
                    'action' => ($model->value == 1 ? 'deactivate' : 'activate'),
                ));
            $img = CHtml::image(
                Yii::app()->request->baseUrl.'/images/'.($model->value ? '' : 'in').'active.png',
                Yii::t('common', $model->value ? 'Inactive' : 'Active'),
                array('title' => Yii::t('common', $model->value ? 'Deactivate' : 'Activate'))
            );

            $options = array(
                'onclick' => 'ajaxSetStatus(this, "config-table"); return false;',
            );

            return '<div align="left">'.CHtml::link($img, $url, $options).'</div>';
        } else {
            return utf8_substr($model->value, 0, 55);
        }
    }

    public static function getVisible($type){
        return $type == 'text';
    }

	public static function updateValue($key, $value){
		$sql = 'UPDATE {{configuration}} SET value=:value, date_updated=NOW() WHERE name=:name';
		Yii::app()->db->createCommand($sql)->execute(array(
			':value' => $value,
			':name' => $key,
		));

		Configuration::clearCache();
	}
}