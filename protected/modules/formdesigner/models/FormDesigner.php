<?php

/**
 * This is the model class for table "{{formdesigner}}".
 *
 * The followings are the available columns in table '{{formdesigner}}':
 * @property integer $id
 * @property string $field
 * @property integer $is_i18n
 * @property integer $visible
 */
class FormDesigner extends CActiveRecord
{
    const VISIBLE_OWNER_OR_ADMIN = 1;

    public function behaviors(){
        return array(
            'AutoTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => NULL,
                'updateAttribute' => 'date_updated',
            ),
        );
    }

    public $saveObjTypes = array();

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FormDesigner the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{formdesigner}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('field, is_i18n, visible', 'required'),
			array('is_i18n, visible', 'numerical', 'integerOnly'=>true),
			array('field', 'length', 'max'=>100),
            array('saveTypes, saveObjTypes', 'safe'),

			array('id, field, is_i18n, visible', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'types' => array(self::HAS_MANY, 'FormDesignerType', 'formdesigner_id'),
            'objTypes' => array(self::MANY_MANY, 'ApartmentObjType', '{{formdesigner_obj_type}}(formdesigner_id, obj_type_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'field' => tt('Field', 'formdesigner'),
			'is_i18n' => 'Is I18n',
			'visible' => tt('Visible only to the owner, admin', 'formdesigner'),
            'filterObjTypes' => tt('Object type', 'apartments'),
            'objTypes' => tt('Object type', 'apartments'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('field',$this->field,true);
		$criteria->compare('is_i18n',$this->is_i18n);
		$criteria->compare('visible',$this->visible);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function getVisibleHtml(){
        $url = Yii::app()->controller->createUrl("visible", array( "id" => $this->id ));

        $img = CHtml::image(
            Yii::app()->request->baseUrl.'/images/'.($this->visible ? '' : 'in').'active.png',

            Yii::t('common', $this->visible ? 'Active' : 'Inactive'),

            array('title' => Yii::t('common', $this->visible ? 'Deactivate' : 'Activate'))
        );

        $options = array(
            'onclick' => 'ajaxSetVisibleForm(this); return false;',
        );

        return '<div align="center">'.CHtml::link($img, $url, $options).'</div>';
    }

    public function getTypesHtml(){

        $objTypesName = array();

        foreach($this->objTypes as $type){
            $objTypesName[] = $type->name;
        }

        $html = '<div align="center">'.implode(', ', $objTypesName).'</div>';
        $html .= CHtml::link(tc('Configure'),
            Yii::app()->createUrl('/formdesigner/backend/main/setup', array('id' => $this->id)),
            array('class' => 'tempModal'));

        return CHtml::tag('div', array('id' => 'form_el_'.$this->id), $html);
    }

    public function visibleForm(){
        $objTypes = array();

        foreach($this->objTypes as $type){
            $objTypes[] = $type->id;
        }

        if(array_intersect($this->filterObjTypes, $objTypes)){
            return true;
        }

        return false;
    }

    public function afterSave() {
        if($this->scenario == 'save_types'){
            $sql = "DELETE FROM {{formdesigner_obj_type}} WHERE formdesigner_id=:formdesigner_id";
            Yii::app()->db->createCommand($sql)->execute(array(
                ':formdesigner_id' => $this->id,
            ));
            if($this->saveObjTypes){
                foreach($this->saveObjTypes as $typeID){
                    $formDesignerType = new FormDesignerObjType();
                    $formDesignerType->formdesigner_id = $this->id;
                    $formDesignerType->obj_type_id = $typeID;
                    $formDesignerType->save();
                }
            }
        }

        return parent::afterSave();
    }

    public static function getDependency(){
        return new CDbCacheDependency('SELECT MAX(date_updated) FROM {{formdesigner}}');
    }

    private static $_cache;

    public static function canShow($field, Apartment $apartment) {
        if(!isset(self::$_cache)){
            self::setCache();
        }

        if(!isset(self::$_cache[$field])){
            return true;
        }

        if(self::$_cache[$field]['visible'] == self::VISIBLE_OWNER_OR_ADMIN && !Yii::app()->user->getState('isAdmin')
            && $apartment->owner_id != Yii::app()->user->id){
            return false;
        }

        return in_array($apartment->obj_type_id, self::$_cache[$field]['objTypes']);
    }

    private static function setCache(){
        $fields = FormDesigner::model()->cache(param('cachingTime', 1209600), self::getDependency())->with(array('objTypes'))->findAll();

        /** @var $field FormDesigner */
        foreach($fields as $field){
            self::$_cache[$field->field]['visible'] = $field->visible;

            self::$_cache[$field->field]['objTypes'] = array();
            foreach($field->objTypes as $type){
                self::$_cache[$field->field]['objTypes'][] = $type->id;
            }
        }
    }
}