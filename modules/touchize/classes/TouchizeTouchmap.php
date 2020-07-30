<?php
/**
 * 2018 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 */

/**
 * Touchmap ObjectModel
 */

class TouchizeTouchmap extends ObjectModel
{
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'touchize_touchmap',
        'primary' => 'id_touchize_touchmap',
        'multilang' => false,
        'fields' => array(
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ),
            'imageurl' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isCleanHtml',
                'required' => false,
            ),
            'name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isCleanHtml',
                'required' => false,
            ),
            'active' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ),
            'mobile' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ),
            'tablet' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ),
            'runonce' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ),
            'new_products' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ),
            'best_sellers' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ),
            'prices_drop' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ),
            'home_page' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ),
            'inslider' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ),
            'position' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ),
            'width' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ),
            'height' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
            ),
        ),
    );
    
    /**
     * @var int
     **/
    public $id_shop;
    
    /**
     * @var string
     **/
    public $imageurl;

    /**
     * @var string
     **/
    public $name;

    /**
     * @var bool
     **/
    public $active;

    /**
     * @var bool
     **/
    public $mobile;

    /**
     * @var bool
     **/
    public $tablet;

    /**
     * @var bool
     **/
    public $runonce;

    /**
     * @var bool
     **/
    public $new_products;

    /**
     * @var bool
     **/
    public $best_sellers;

    /**
     * @var bool
     **/
    public $prices_drop;

    /**
     * @var bool
     **/
    public $home_page;

    /**
     * @var bool
     **/
    public $inslider;

    /**
     * @var int
     **/
    public $position;

    /**
     * @var int
     **/
    public $width;

    /**
     * @var int
     **/
    public $height;

    /**
     * @var string
     **/
    public $date_add;

    /**
     * @var string
     **/
    public $date_upd;

    /**
     * @var array
     **/
    public $categories = array();
    
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->id_shop = (int)Shop::getContextShopID(true);
        return parent::__construct($id, $id_lang, $id_shop);
    }

    /**
     * Get all touchmaps for specified category
     *
     * @param  int      $idCategory
     * @param  bool     $onlyVisible
     * @param  bool     $mobile
     * @param  bool     $tablet
     * @param  bool     $fetch_all        If set to true, will fetch ALL banners,
     *   no matter what has set in the parameters before. Used at SetupWizard.
     *
     * @return array
     */
    public static function getTouchmaps(
        $idCategory = 0,
        $onlyVisible = true,
        $mobile = false,
        $tablet = false,
        $fetch_all = false
    ) {
        if ($idCategory == 'new-products') {
            $categories = ' stm.`new_products` = 1 ';
        } elseif ($idCategory == 'best-sales') {
            $categories = ' stm.`best_sellers` = 1 ';
        } elseif ($idCategory == 'prices-drop') {
            $categories = ' stm.`prices_drop` = 1 ';
        } elseif ($idCategory == '-1') {
            $categories = ' 1 ';
        } else {
            $categories = 'stmc.`id_category` = \'' . pSQL((int)$idCategory) . '\'';
            $join = ' INNER JOIN `'._DB_PREFIX_.'touchize_touchmapcategory` stmc ON (
                stm.`id_touchize_touchmap` = stmc.`id_touchize_touchmap`
            ) ';
        }

        $startCategoryId = (string)Configuration::get(
            'TOUCHIZE_START_CATEGORY_ID'
        );
        if ($idCategory == $startCategoryId) {
            $categories = ' stm.`home_page` = 1 ';
            $join = '';
        }

        $id_shop = Shop::getContextShopID(true);

        $sql = 'SELECT
            stm.`id_touchize_touchmap`,
            stm.`imageurl`,
            stm.`name`,
            stm.`active`,
            stm.`mobile`,
            stm.`tablet`,
            stm.`runonce`,
            stm.`new_products`,
            stm.`best_sellers`,
            stm.`prices_drop`,
            stm.`inslider`,
            stm.`position`,
            stm.`width`,
            stm.`height`
            FROM '._DB_PREFIX_.'touchize_touchmap stm'.
            (isset($join) ? $join : '').'
            WHERE '.$categories.'
            AND (stm.`id_shop` = \''.pSQL((int)$id_shop).'\' OR stm.`id_shop` = 0)';

        if (!$fetch_all) {
            $sql.=  ' AND stm . `active` = \''.pSQL((int)$onlyVisible) . '\'';

            if ($mobile) {
                $sql .= ' AND stm.`mobile` = \'' . pSQL((int)$mobile) . '\'';
            }

            if ($tablet) {
                $sql .= ' AND stm.`tablet` = \'' . pSQL((int)$tablet) . '\'';
            }
        }

        $sql .= ' ORDER BY stm.`position` ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get selected categories for touchmap
     *
     * @param  int $idCategory category id
     *
     * @return array array of ids
     */
    public static function getCategories($idCategory = null)
    {
        $sql = 'SELECT `id_category`
                FROM '._DB_PREFIX_.'touchize_touchmapcategory
                WHERE `id_touchize_touchmap` = \''.pSQL((int)$idCategory).'\'';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Overrides add
     *
     * @see ObjectModel::add
     *
     * @param  bool $autodate
     * @param  bool $nullValues
     *
     * @return bool
     */
    public function add($autodate = true, $nullValues = false)
    {
        if ($this->position <= 0) {
            $this->position = self::getHigherPosition() + 1;
        }
        if (parent::add($autodate, $nullValues)) {
            if (!empty($this->categories)) {
                $this->addCategories($this->categories);
            }
            return true;
        }

        return false;
    }
    
    /**
     * Gets the highest position
     *
     * @return int $position
     */
    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
                FROM `'._DB_PREFIX_.'touchize_touchmap`
                WHERE `id_shop` = \'' . (int)Shop::getContextShopID(true) . '\'';
        $position = DB::getInstance()->getValue($sql);
        return (is_numeric($position)) ? $position : -1;
    }

    /**
     * Update without checking categories
     *
     * @see ObjectModel::update
     *
     * @param  bool $nullValues
     *
     * @return bool
     */
    public function simpleUpdate($nullValues = false)
    {
        if (parent::update($nullValues)) {
            return true;
        }

        return false;
    }

    /**
     * Overrides update
     *
     * @see ObjectModel::update
     *
     * @param  bool $nullValues
     *
     * @return bool
     */
    public function update($nullValues = false)
    {
        if (!$this->updateCategories()) {
            return false;
        }

        if (parent::update($nullValues)) {
            return true;
        }

        return false;
    }

    /**
     * Overrides delete
     *
     * @see ObjectModel::delete
     *
     * @return bool
     */
    public function delete()
    {
        $this->deleteActionAreas();
        $this->deleteCategories();

        if (parent::delete()) {
            return true; # TODO: $this->deleteImage();
        }

        return false;
    }

    /**
     * Adds categories to show touchmap in
     *
     * @param  array $categories Ids of categories
     *
     * @return bool
     */
    public function addCategories($categories)
    {
        $data = array();
        foreach ($categories as $category) {
            $data[] = array(
                'id_touchize_touchmap' => pSQL((int)$this->id),
                'id_category' => pSQL((int)$category),
            );
        }

        return Db::getInstance()->insert('touchize_touchmapcategory', $data);
    }

    /**
     * Updates categories to show touchmap in
     *
     * @param  array $categories Ids of categories
     *
     * @return bool
     */
    public function updateCategories()
    {
        if (!$this->deleteCategories()) {
            return false;
        }

        if (!empty($this->categories) &&
            !$this->addCategories($this->categories)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Deletes all category connections
     *
     * @return bool
     */
    public function deleteCategories()
    {
        return Db::getInstance()->delete(
            'touchize_touchmapcategory',
            'id_touchize_touchmap = \''.pSQL((int)$this->id).'\''
        );
    }

    /**
     * Deletes all associated action areas
     *
     * @return bool
     */
    public function deleteActionAreas()
    {
        return Db::getInstance()->delete(
            'touchize_actionarea',
            'id_touchize_touchmap = \''.pSQL((int)$this->id).'\''
        );
    }
    
    /**
     * Updates the position of the Touchmap
     *
     * @param string $way
     * @param int $position
     * @return boolean
     */
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            'SELECT `id_touchize_touchmap`, `position`
            FROM `'._DB_PREFIX_.'touchize_touchmap`
            ORDER BY `position` ASC'
        )) {
            return false;
        }
            
        foreach ($res as $touchmap) {
            if ((int)$touchmap['id_touchize_touchmap'] == (int)$this->id) {
                $moved_touchmap = $touchmap;
            }
        }
        
        if (!isset($moved_touchmap) || !isset($position)) {
            return false;
        }
        
        return (Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'touchize_touchmap`
            SET `position`= `position` '.($way ? '- 1' : '+ 1').'
            WHERE `id_shop` = \'' . (int)Shop::getContextShopID(true) . '\' AND  
            `position`
            '.($way
                ? '> '.(int)$moved_touchmap['position'].' AND `position` <= '.(int)$position
                : '< '.(int)$moved_touchmap['position'].' AND `position` >= '.(int)$position))
                && Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'touchize_touchmap`
            SET `position` = '.(int)$position.'
            WHERE `id_touchize_touchmap` = '.(int)$moved_touchmap['id_touchize_touchmap']));
    }
}
