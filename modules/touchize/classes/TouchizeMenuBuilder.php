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
* Menu builder objectModel.
*/

class TouchizeMenuBuilder extends ObjectModel
{
    const TABLE_NAME = 'touchize_main_menu';
    const TITLE_TABLE_NAME = 'touchize_main_menu_lang';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_menu_item',
        'multilang' => false,
        'fields' => array(
            'type' => array(
                'type' => self::TYPE_STRING,
            ),
            'action' => array(
                'type' => self::TYPE_STRING,
            ),
            'title' => array(
                'type' => self::TYPE_STRING,
            ),
            'page' => array(
                'type' => self::TYPE_INT,
            ),
            'cms_page' => array(
                'type' => self::TYPE_INT,
            ),
            'url' => array(
                'type' => self::TYPE_STRING,
            ),
            'external' => array(
                'type' => self::TYPE_INT,
            ),
            'event' => array(
                'type' => self::TYPE_STRING,
            ),
            'event_input' => array(
                'type' => self::TYPE_STRING,
            ),
            'page_url' => array(
                'type' => self::TYPE_STRING,
            ),
            'position' => array(
                'type' => self::TYPE_INT,
            ),
        ),
    );

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $action;
    
    /**
     * @var string
     */
    public $title;
    
    /**
     * @var int
     */
    public $page;
    
    /**
     * @var int
     */
    public $cms_page;
    
    /**
     * @var string
     */
    public $url;
    
    /**
     * @var int
     */
    public $external;
    
    /**
     * @var string
     */
    public $event;
    
    /**
     * @var string
     */
    public $event_input;
    
    /**
     * @var string
     */
    public $page_url;
    
    /**
     * @var int
     */
    public $position;

    /**
     * We receive the array that includes pages data from the section
     * Preferences -> SEO & URLs or one page if the parameter $idMeta was sent.
     *
     * @param  int      $langId
     * @param  null|int $idMeta
     *
     * @return array
     */
    public static function getSeoAndUrlsPages($langId, $idMeta = null)
    {
        $sql = 'SELECT 
                    `'._DB_PREFIX_.'meta`.`id_meta`, 
                    `'._DB_PREFIX_.'meta`.`page`, 
                    `'._DB_PREFIX_.'meta_lang`.`title` 
                FROM `'._DB_PREFIX_.'meta` 
                INNER JOIN `'._DB_PREFIX_.'meta_lang` 
                ON `'._DB_PREFIX_.'meta`.`id_meta` = 
                `'._DB_PREFIX_.'meta_lang`.`id_meta`
                WHERE `'._DB_PREFIX_.'meta_lang`.`id_lang` = \''.pSQL($langId).'\'';

        if ($idMeta) {
            $sql .= ' AND `'._DB_PREFIX_.'meta`.`id_meta` = \''.pSQL($idMeta).'\'';
        }

        $sql .= ' ORDER BY `'._DB_PREFIX_.'meta`.`page` ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * We receive the array that includes pages data from the section
     * Preferences -> CMS or one page if the parameter $idCms was sent.
     *
     * @param  int      $langId
     * @param  null|int $idCms
     *
     * @return array
     */
    public static function getCmsPages($langId, $idCms = null)
    {
        $sql = 'SELECT `id_cms`, `meta_title` 
                FROM `'._DB_PREFIX_.'cms_lang`
                WHERE `'._DB_PREFIX_.'cms_lang`.`id_lang` = \''.pSQL($langId).'\'';

        if ($idCms) {
            $sql .= ' AND `id_cms` = \''.pSQL($idCms).'\'';
        }

        $sql .= ' ORDER BY `'._DB_PREFIX_.'cms_lang`.`meta_title` ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * To add the current object to the database.
     *
     * @param  bool $autoDate
     * @param  bool $nullValues
     *
     * @return bool
     */
    public function add($autoDate = true, $nullValues = false)
    {
        // Automatically fill dates
        if ($autoDate && property_exists($this, 'date_add')) {
            $this->date_add = date('Y-m-d H:i:s');
        }
        if ($autoDate && property_exists($this, 'date_upd')) {
            $this->date_upd = date('Y-m-d H:i:s');
        }

        switch ($this->type) {
            case 'menu-item':
                $menuItem = self::setItemData();
                break;
            case 'menu-header':
                $menuItem = self::setHeaderData();
                break;
            case 'menu-divider':
                $menuItem = self::setDividerData();
                break;
            default:
                return false;
        }

        if ($menuItem) {
            $queryResult = Db::getInstance()->insert(
                self::TABLE_NAME,
                $menuItem,
                (bool)$nullValues
            );

            if ($queryResult &&
                ('menu-item' == $this->type || 'menu-header' == $this->type)
            ) {
                $menuItemId = Db::getInstance()->Insert_ID();
                self::addTitleForMenuElement($menuItemId);
            }

            # Generate json for main menu.
            self::generateJson();

            return $queryResult;
        }

        return false;
    }

    /**
     * To edit the current object in the database.
     *
     * @param  bool $nullValues
     *
     * @return bool
     */
    public function update($nullValues = null)
    {
        switch ($this->type) {
            case 'menu-item':
                $menuItem = self::setItemData('update');
                break;
            case 'menu-header':
                $menuItem = self::setHeaderData('update');
                break;
            case 'menu-divider':
                $menuItem = self::setDividerData('update');
                break;
            default:
                return false;
        }

        if ($menuItem) {
            $queryResult = Db::getInstance()->update(
                self::TABLE_NAME,
                $menuItem,
                'id_menu_item = \''.pSQL($this->id).'\'',
                0,
                (bool)$nullValues
            );

            if ($queryResult &&
                ('menu-item' == $this->type || 'menu-header' == $this->type)
            ) {
                self::addTitleForMenuElement($this->id, 'update');
            }

            # Generate json for main menu.
            self::generateJson();

            return $queryResult;
        }

        return false;
    }

    /**
     * To remove the current object from the database.
     *
     * @return bool
     */
    public function delete()
    {
        # Removes current object from database.
        $deleteProcess = parent::delete();
        # Updates positions of menu elements after deleting.
        self::refreshMenuPositions();
        # Updates of configuration variable `TOUCHIZE_MAIN_MENU`.
        self::generateJson();
        # Clean up all unused titles.
        Db::getInstance()->delete(
            self::TITLE_TABLE_NAME,
            'id_menu_item = \''.pSQL($this->id).'\''
        );

        return $deleteProcess;
    }

    /**
     * To remove several objects from the database.
     *
     * @param  array $ids Array of objects IDs.
     *
     * @return bool
     */
    public function deleteSelection($ids)
    {
        # Removes all provided objects from database.
        $deleteProcess = parent::deleteSelection($ids);
        # Clean up all unused titles.
        foreach ($ids as $id) {
            DB::getInstance()->delete(
                self::TITLE_TABLE_NAME,
                'id_menu_item = \''.pSQL($id).'\''
            );
        }
        # Updates positions of menu elements after deleting.
        self::refreshMenuPositions();
        # Updates of `TOUCHIZE_MAIN_MENU` variable .
        self::generateJson();

        return $deleteProcess;
    }

    /**
     * To update the number of the element position in the menu.
     *
     * @param  array $positions
     *
     * @return bool
     */
    public function updatePositions($positions)
    {
        if ($positions) {
            foreach ($positions as $position => $item) {
                $expItem = explode('_', $item);

                if (!Db::getInstance()->update(
                    self::TABLE_NAME,
                    array(
                        'position' => pSQL((int)$position),
                    ),
                    'id_menu_item = \''.pSQL((int)$expItem[2]).'\''
                )) {
                    return false;
                }
            }

            # Generate json for main menu.
            self::generateJson();

            return true;
        }

        return false;
    }

    /**
     * To return element data array of the menu with the type 'menu-item'.
     *
     * @param  string $action
     *
     * @return bool|array
     */
    private function setItemData($action = 'add')
    {
        # Basic item properties.
        # Required: type, action.
        $item = array(
            'type' => $this->type,
            'action' => $this->action,
        );

        # Set non-required params according to item action.
        switch ($this->action) {
            case 'page':
                if (!empty($this->page)) {
                    $item['page'] = $this->page;
                } else {
                    return false;
                }
                break;
            case 'cms_page':
                if (!empty($this->cms_page)) {
                    $item['cms_page'] = $this->cms_page;
                } else {
                    return false;
                }
                break;
            case 'url':
                if (!empty($this->url) &&
                    self::validateTitle()
                ) {
                    $item['url'] = $this->url;
                    $item['external'] = $this->external;
                } else {
                    return false;
                }
                break;
            case 'event':
                if (!empty($this->event) &&
                    !empty($this->event_input) &&
                    !empty($this->page_url) &&
                    self::validateTitle()
                ) {
                    $item['event'] = $this->event;
                    $item['event_input'] = $this->event_input;
                    $item['page_url'] = $this->page_url;
                } else {
                    return false;
                }
                break;
        }

        # Sets item position in menu.
        # Do not use setPosition() method in the `update` action.
        if ('add' === $action) {
            $item['position'] = self::setPosition();
        }

        return $item;
    }

    /**
     * To return element data array of the menu with the type 'menu-header'.
     *
     * @param  string $action
     *
     * @return bool|array
     */
    private function setHeaderData($action = 'add')
    {
        # Required attribute: title.
        $header = array(
            'type' => $this->type,
        );
        # Set item position in menu.
        # Do not use it in `update` action.
        if ('add' === $action) {
            $header['position'] = self::setPosition();
        }

        # Check if `title` was provided for `header` element.
        return self::validateTitle() ? $header : false;
    }

    /**
     * To return element data array of the menu with the type 'menu-divider'.
     *
     * @param  string $action
     *
     * @return array
     */
    private function setDividerData($action = 'add')
    {
        $divider = array(
            'type' => $this->type,
        );
        # Set item position in menu.
        # Do not use it in `update` action.
        if ('add' === $action) {
            $divider['position'] = self::setPosition();
        }

        return $divider;
    }

    /**
     * To set an ordinal number for a new menu element in the list.
     *
     * @return int $max
     */
    private function setPosition()
    {
        $max = Db::getInstance()
            ->executeS('SELECT MAX(position) as position 
                        FROM `'._DB_PREFIX_.self::TABLE_NAME.'`');

        return (isset($max[0]['position']) && !is_null($max[0]['position']))
            ? ($max[0]['position'] + 1)
            : 0;
    }

    /**
     * To update all elements position numbers of the menu after
     * changing position number (at least one position number).
     *
     * @return bool
     */
    private function refreshMenuPositions()
    {
        $menuItems = Db::getInstance()
            ->executeS('SELECT `id_menu_item` 
                        FROM `'._DB_PREFIX_.self::TABLE_NAME.'`
                        ORDER BY `position` ASC');

        if ($menuItems) {
            foreach ($menuItems as $key => $value) {
                if (!Db::getInstance()->update(
                    self::TABLE_NAME,
                    array('position' => pSQL($key)),
                    'id_menu_item = \''.pSQL($value['id_menu_item']).'\''
                )) {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * To generate json string for menu.
     *
     * @return bool
     */
    public function generateJson()
    {
        $jsonArr = array(
            'items' => array(),
        );

        $menuElements = Db::getInstance()
            ->executeS('SELECT * 
                        FROM `'._DB_PREFIX_.self::TABLE_NAME.'`
                        ORDER BY `position` ASC');

        if ($menuElements) {
            foreach ($menuElements as $element) {
                switch ($element['type']) {
                    case 'menu-item':
                        $jsonArr['items'][] = self::addItemElement($element);
                        break;
                    case 'menu-header':
                        $jsonArr['items'][] = self::addHeaderElement($element);
                        break;
                    case 'menu-divider':
                        $jsonArr['items'][] = array('type' => 'menu-divider');
                        break;
                    default:
                        # Skip item.
                        break;
                }
            }

            # Filters elements of an array.
            $jsonArr = array_filter($jsonArr);
        }

        return Configuration::updateGlobalValue(
            'TOUCHIZE_MAIN_MENU',
            json_encode($jsonArr)
        );
    }

    /**
     * To return the data array for menu elements with the
     * type 'menu-item' for json string generation.
     *
     * @param  array $element
     *
     * @return array|null
     */
    private function addItemElement($element)
    {
        $response = array(
            'type' => $element['type'],
        );

        $languages = Language::getLanguages(true);
        $titles = array();

        foreach ($languages as $lang) {
            $sql = 'SELECT `title` 
                    FROM '._DB_PREFIX_.self::TITLE_TABLE_NAME.'
                    WHERE id_menu_item = \''.pSQL($element['id_menu_item']).'\'
                    AND id_lang = \''.pSQL($lang['id_lang']).'\'';

            $title = Db::getInstance()->executeS($sql);

            if (isset($title[0]['title']) &&
                !empty($title[0]['title'])
            ) {
                $titles[$lang['id_lang']] = $this->l($title[0]['title']);
            } else {
                $titles[$lang['id_lang']] = null;
            }
        }

        $unique = array_unique(array_filter($titles));

        if (isset($unique[key($unique)]) &&
            null != $unique[key($unique)]
        ) {
            $response['title'] = $titles;
        }

        switch ($element['action']) {
            case 'page':
                $response['pagecontroller'] = $element['page'];
                break;
            case 'cms_page':
                $response['cmsid'] = $element['cms_page'];
                break;
            case 'url':
                $response['url'] = $element['url'];
                if ($element['external'] == 1) {
                    $response['external'] = 'true';
                }
                break;
            case 'event':
                $response['event'] = $element['event'];
                $response['event-input'] = $element['event_input'];
                $response['pageurl'] = $element['page_url'];
                break;
            default:
                return null;
        }

        return $response;
    }

    /**
     * To return the data array for menu elements with the
     * type 'menu-header' for json string generation.
     *
     * @param  array $element
     *
     * @return array|null
     */
    private function addHeaderElement($element)
    {
        $languages = Language::getLanguages(true);
        $titles = array();

        foreach ($languages as $lang) {
            $sql = 'SELECT `title` 
                    FROM '._DB_PREFIX_.self::TITLE_TABLE_NAME.'
                    WHERE id_menu_item = \''.pSQL($element['id_menu_item']).'\'
                    AND id_lang = \''.pSQL($lang['id_lang']).'\'';

            $title = Db::getInstance()->executeS($sql);

            if (isset($title[0]['title']) &&
                !empty($title[0]['title'])
            ) {
                $titles[$lang['id_lang']] = $this->l($title[0]['title']);
            } else {
                $titles[$lang['id_lang']] = null;
            }
        }

        return array(
            'type' => $element['type'],
            'title' => $titles,
        );
    }

    /**
     * To check if `title` was provided.
     *
     * @return bool
     */
    private function validateTitle()
    {
        foreach ($_POST as $key => $value) {
            if (is_int(strpos($key, 'title_')) &&
                !empty($value)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * To add multilanguage title for current object.
     *
     * @param   int     $elementId
     * @param   string  $action
     *
     * @return  bool
     */
    private function addTitleForMenuElement($elementId, $action = 'add')
    {
        $titles = array();

        foreach ($_POST as $key => $value) {
            if (is_int(strpos($key, 'title_'))) {
                $titles[] = array(
                    'id_lang' => pSQL(str_replace('title_', '', $key)),
                    'id_menu_item' => pSQL($elementId),
                    'title' => pSQL($this->l($value)),
                );
            }
        }

        if ('add' === $action) {
            return Db::getInstance()->insert(
                self::TITLE_TABLE_NAME,
                $titles
            );
        } else {
            if (!empty($titles)) {
                foreach ($titles as $title) {
                    Db::getInstance()->update(
                        self::TITLE_TABLE_NAME,
                        array(
                            'title' => pSQL($this->l($title['title'])),
                        ),
                        'id_menu_item = \''.pSQL($elementId).'\' AND id_lang = \'
                            '.pSQL($title['id_lang']).'\''
                    );
                }
            }
        }

        return true;
    }

    /**
     * Non-static method which uses AdminController::translate()
     *
     * @param  string       $string       Term or expression in english
     * @param  string|null  $class        Name of the class
     * @param  bool         $addslashes   If set to true,
     *                                    the return value will pass
     *                                    through addslashes().
     *                                    Otherwise, stripslashes().
     * @param  bool         $htmlentities If set to true(default),
     *                                    the return value will pass through
     *                                    htmlentities(
     *                                        $string,
     *                                        ENT_QUOTES,
     *                                        'utf-8'
     *                                    ).
     *
     * @return string
     */
    private function l(
        $string,
        $class = null,
        $addslashes = false,
        $htmlentities = true
    ) {
        if (null === $class || 'AdminTab' == $class) {
            $adminObj = new AdminMenuBuilderController();
            $class = Tools::substr(get_class($adminObj), 0, -10);
        } elseif ('controller' == Tools::strtolower(
            Tools::substr($class, -10)
        )) {
            /*
                classname has changed, from AdminXXX to AdminXXXController,
                so we remove 10 characters and we keep same keys
            */
            $class = Tools::substr($class, 0, -10);
        }

        return Translate::getAdminTranslation(
            $string,
            $class,
            $addslashes,
            $htmlentities
        );
    }
}
