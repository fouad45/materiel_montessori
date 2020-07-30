<?php

class FieldTabProductsIsotopeModel extends ObjectModel
{
    public $id_fieldtabproductsisotope;
    public $active = 1;
    public $position;
    public $tab_type;
    public $tab_content;
    public $banner_image;
    public $banner_link;
    public $title_image;
    public $countdown_from;
    public $countdown_to;

    //Multilang Fields
    public $title;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'fieldtabproductsisotope',
        'primary' => 'id_fieldtabproductsisotope',
        'multilang' => true,
        'fields' => array(
            //Fields
            'active'          =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'position'        =>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'tab_type'        =>  array('type' => self::TYPE_STRING),
            'tab_content'     =>  array('type' => self::TYPE_STRING),
            'banner_image'    =>  array('type' => self::TYPE_STRING, 'size' => 250),
            'banner_link'     =>  array('type' => self::TYPE_STRING, 'size' => 250),
            'title_image'     =>  array('type' => self::TYPE_STRING, 'size' => 250),
            'countdown_from'  =>  array('type' => self::TYPE_DATE),
            'countdown_to'    =>  array('type' => self::TYPE_DATE),

            //Multilanguage Fields
            'title'           =>  array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 250)
        )
    );

    /*-------------------------------------------------------------*/
    /*  CONSTRUCT
    /*-------------------------------------------------------------*/
    public function __construct($id_fieldtabproductsisotope = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation('fieldtabproductsisotope', array('type' => 'shop'));
        parent::__construct($id_fieldtabproductsisotope, $id_lang, $id_shop);
    }

    /*-------------------------------------------------------------*/
    /*  ADD
    /*-------------------------------------------------------------*/
    public function add($autoddate = true, $null_values = false)
    {
        $this->position = (int) $this->getMaxPosition() + 1;
        return parent::add();
    }

    /*-------------------------------------------------------------*/
    /*  DELETE
    /*-------------------------------------------------------------*/
    public function delete()
    {
        $response = parent::delete();
        $this->reorderTabs();

        return $response;
    }

    /*-------------------------------------------------------------*/
    /*  GET ALL ROWS
    /*-------------------------------------------------------------*/
    public static function getAll()
	{
		$response = Db::getInstance()->executeS('
            SELECT *
			FROM `'._DB_PREFIX_.'fieldtabproductsisotope`'
        );
        
        return $response;
	}

    /*-------------------------------------------------------------*/
    /*  GET TAB IDs
    /*-------------------------------------------------------------*/
    public function getTabIds($id_shop)
    {
        $response = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT a.id_fieldtabproductsisotope, b.id_fieldtabproductsisotope
            FROM '._DB_PREFIX_.'fieldtabproductsisotope as a,
                 '._DB_PREFIX_.'fieldtabproductsisotope_shop as b
            WHERE a.id_fieldtabproductsisotope = b.id_fieldtabproductsisotope
            AND b.id_shop = '.$id_shop.'
            AND a.active = 1
            ORDER BY a.position ASC'
        );

        return $response;
    }

    /*-------------------------------------------------------------*/
    /*  GET MAX POSITION
    /*-------------------------------------------------------------*/
    public static function getMaxPosition()
    {
        $response = Db::getInstance()->getRow('
            SELECT MAX(position)
			FROM `'._DB_PREFIX_.'fieldtabproductsisotope`'
        );

        if ($response['MAX(position)'] == null){
            return -1;
        }

        return $response['MAX(position)'];

    }

    /*-------------------------------------------------------------*/
    /*  UPDATE POSITION
    /*-------------------------------------------------------------*/
    public function updatePosition($way, $position)
    {
        if (!$tabs = Db::getInstance()->executeS('
			SELECT `id_fieldtabproductsisotope`, `position`
			FROM `'._DB_PREFIX_.'fieldtabproductsisotope`
			ORDER BY `position` ASC'
        ))
            return false;

        foreach ($tabs as $tab)
            if ((int)$tab['id_fieldtabproductsisotope'] == (int)$this->id)
                $moved_tab = $tab;

        if (!isset($moved_tab) || !isset($position))
            return false;

        return (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'fieldtabproductsisotope`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
                       ? '> '.(int)$moved_tab['position'].' AND `position` <= '.(int)$position
                       : '< '.(int)$moved_tab['position'].' AND `position` >= '.(int)$position
			))
            && Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'fieldtabproductsisotope`
			SET `position` = '.(int)$position.'
			WHERE `id_fieldtabproductsisotope` = '.(int)$moved_tab['id_fieldtabproductsisotope']));
    }

    /*-------------------------------------------------------------*/
    /*  REORDER TABS AFTER DELETION
    /*-------------------------------------------------------------*/
    public static function reorderTabs()
    {
        $return = true;

        $sql = 'SELECT `id_fieldtabproductsisotope`
		        FROM `'._DB_PREFIX_.'fieldtabproductsisotope`
		        ORDER BY `position` ASC';

        $result = Db::getInstance()->executeS($sql);

        $i = 0;
        foreach ($result as $value) {
            $return = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'fieldtabproductsisotope`
			SET `position` = '.(int)$i++.'
			WHERE `id_fieldtabproductsisotope` = '.(int)$value['id_fieldtabproductsisotope']);
        }

        return $return;
    }
}