<?php
class ModelFieldSlideShow extends ObjectModel
{
 	/** @var string Name */
                public $id_fieldslideshow;
                public $images;
		public $title1;
                public $title2;
                public $btntext;
                public $description;
                public $link;
                public $porder;
		public $active;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'fieldslideshow',
		'primary' => 'id_fieldslideshow',
		'multilang' => TRUE,
		'fields' => array(
                    'porder' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false),
                    'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
                    'images' => array('type' => self::TYPE_STRING, 'validate'=> 'isGenericName', 'required' => false, 'size' => 250),
                    //lang field
                    'title1' => array('type' => self::TYPE_STRING,'lang' => true, 'validate' => 'isGenericName', 'required' => false, 'size' => 265),
                    'title2' => array('type' => self::TYPE_STRING,'lang' => true, 'validate' => 'isGenericName', 'required' => false, 'size' => 265),
                    'btntext' => array('type' => self::TYPE_STRING,'lang' => true, 'validate' => 'isGenericName', 'required' => false, 'size' => 265),
                    'link' => array('type' => self::TYPE_STRING,'lang' => true, 'validate' => 'isGenericName', 'required' => false, 'size' => 265),
                    'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString', 'size' => 3999999999999),
                ),
	);
        public function __construct($id = NULL, $id_lang = NULL){
		parent::__construct($id, $id_lang);
	}
        public function update($null_values = false){
		return parent::update($null_values);
	}
        public function delete(){
		$res=true ;
		$res &= parent::delete();
		$res &= Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'fieldslideshow_shop`
			WHERE `id_shop` = '.(int)$this->id
		);
		if($res){
			if(file_exists(_PS_MODULE_DIR_ . 'fieldslideshow/images/' .$this->images))
			@unlink(_PS_MODULE_DIR_ . 'fieldslideshow/images/' .$this->images);
			return true;
		}
	}
        public function deleteImage($force_delete = false) {
		$res = parent::deleteImage($force_delete);
		if ($res) {
		if(file_exists(_PS_MODULE_DIR_ . 'fieldslideshow/images/' .$this->images))
			@unlink(_PS_MODULE_DIR_ . 'fieldslideshow/images/' .$this->images);
			return true;
		}
		return $res;
	}
}