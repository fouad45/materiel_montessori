<?php

class FieldMegamenuItemsModel extends ObjectModel
{
    public $id_fieldmegamenu;
    public $active = 1;
    public $nleft;
    public $nright;
    public $depth = 1;
    public $icon_class;
    public $menu_type;
    public $menu_class;
    public $menu_layout;
    public $menu_image;
    public $open_in_new = 0;
    public $show_image = 0;

    //Multilang Fields
    public $title;
    public $description;
    public $link;
    public $content;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'fieldmegamenuitems',
        'primary' => 'id_fieldmegamenuitems',
        'multilang' => true,
        'fields' => array(
            //Fields
            'id_fieldmegamenu'     =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'active'      =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'nleft'       =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'nright'      =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'depth'       =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'icon_class'  =>  array('type' => self::TYPE_STRING, 'size' => 255),
            'menu_type'   =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'menu_class'  =>  array('type' => self::TYPE_STRING, 'size' => 255),
            'menu_layout' =>  array('type' => self::TYPE_STRING, 'size' => 255),
            'menu_image'  =>  array('type' => self::TYPE_STRING, 'size' => 255),
            'open_in_new' =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'show_image'  =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),

            //Multilanguage Fields
            'title'       =>  array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
            'description' =>  array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 125),
            'link'        =>  array('type' => self::TYPE_STRING, 'lang' => true, 'size' => 255),
            'content'     =>  array('type' => self::TYPE_HTML, 'lang' => true, 'size' => 10000),
        )
    );

    /*-------------------------------------------------------------*/
    /*  CONSTRUCT
    /*-------------------------------------------------------------*/
    public function __construct($id_fieldmegamenuitems = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_fieldmegamenuitems, $id_lang, $id_shop);
    }

    /*-------------------------------------------------------------*/
    /*  ADD
    /*-------------------------------------------------------------*/
    public function add($autoddate = true, $null_values = false)
    {
        return parent::add();
    }

    /*-------------------------------------------------------------*/
    /*  DELETE
    /*-------------------------------------------------------------*/
    public function delete()
    {
        return parent::delete();
    }

    /*-------------------------------------------------------------*/
    /*  GET MIN nleft
    /*-------------------------------------------------------------*/
    public static function getMinLeft($id_fieldmegamenu)
    {
        $response = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT MIN(nleft)
            FROM '._DB_PREFIX_.'fieldmegamenuitems
            WHERE id_fieldmegamenu = '.$id_fieldmegamenu
        );

        if ($response['MIN(nleft)'] == null){
            return 0;
        }

        return $response['MIN(nleft)'];
    }

    /*-------------------------------------------------------------*/
    /*  GET MAX nright
    /*-------------------------------------------------------------*/
    public static function getMaxRight($id_fieldmegamenu)
    {
        $response = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT MAX(nright)
            FROM '._DB_PREFIX_.'fieldmegamenuitems
            WHERE id_fieldmegamenu = '.$id_fieldmegamenu
        );

        if ($response['MAX(nright)'] == null){
            return -1;
        }

        return $response['MAX(nright)'];
    }

    /*-------------------------------------------------------------*/
    /*  GET MENU ITEMS COUNT
    /*-------------------------------------------------------------*/
    public static function getMenuItemsCount($id_fieldmegamenu)
    {
        $response = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT COUNT(id_fieldmegamenu)
            FROM '._DB_PREFIX_.'fieldmegamenuitems
            WHERE id_fieldmegamenu = '.$id_fieldmegamenu
        );

        return $response['COUNT(id_fieldmegamenu)'];
    }

    /*-------------------------------------------------------------*/
    /*  GET MENU ITEMS
    /*-------------------------------------------------------------*/
    public static function getMenuItems($id_fieldmegamenu)
    {
        $minNLeft = FieldMegamenuItemsModel::getMinLeft($id_fieldmegamenu);
        $maxNRight = FieldMegamenuItemsModel::getMaxRight($id_fieldmegamenu);

        $response = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT items.id_fieldmegamenuitems
            FROM '._DB_PREFIX_.'fieldmegamenuitems as items
            WHERE items.nleft BETWEEN '.$minNLeft.' AND '.$maxNRight.'
            AND items.id_fieldmegamenu ='.$id_fieldmegamenu.'
            ORDER BY items.nleft'
        );

        return $response;
    }




}