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

class TouchizeTopMenuHelper extends TouchizeBaseHelper
{
    const ITEM_DELIMITER = ',';

    const TOP_MENU_OPTION_NAME = 'TOUCHIZE_TOP_MENU_ITEMS';

    /**
     * @var array
     */
    protected $allowed_items;

    /**
     * @param $menu_items
     *
     * @return bool
     */
    public function saveItems($menu_items)
    {
        $prepared_items= $this->getPreparedToStore($menu_items);
        if (is_string($prepared_items)) {
            return Configuration::updateValue(self::TOP_MENU_OPTION_NAME, $prepared_items);
        }
    }

    /**
     * @param $menu_items
     *
     * @return string
     */
    public function getPreparedToStore($menu_items)
    {
        $stored_data = Tools::jsonDecode($menu_items, true);
        if (is_array($stored_data)) {
            return $menu_items;
        }
        return array();
    }

    /**
     * @return array
     */
    public function getSelectedItems()
    {
        $menu_items = $this->getMenuItems();
        return $menu_items;
    }

    /**
     * @return array
     */
    public function getMenuItems()
    {
        $selected_items = Configuration::get(self::TOP_MENU_OPTION_NAME);
        if (empty($selected_items)) {
            return array();
        }
        $selected_array = Tools::jsonDecode($selected_items, true);

        return $selected_array;
    }


    /**
     * @param bool $flat
     *
     * @return array
     */
    public function getAllowedItems($flat = false)
    {
        if (is_null($this->allowed_items)) {
            $allowed_items = $this->getNestedCategories(
                $this->context->shop->id,
                null,
                (int)$this->context->language->id,
                false,
                $flat
            );

            $extra_items = $this->getExtraItems();
            $manufacturer_items = $this->getManufacturerItems($flat);
            $this->allowed_items = $extra_items + $allowed_items + $manufacturer_items;
        }
        return $this->allowed_items;
    }

    /**
     * @return array
     */
    public function getJsAllowedItems()
    {
        $allowed_items = $this->getAllowedItems();
        return $this->prepareForJs($allowed_items);
    }

    public function prepareForJs($items)
    {
        $js_allowed_items = array();
        if ($items) {
            foreach ($items as $item) {
                $node = array(
                    'name' => $item['name'],
                    'id' => $item['id_category'],
                );

                if (isset($item['children']) && $item['children']) {
                    $node['children'] = $this->prepareForJs($item['children']);
                }
                $js_allowed_items[] = $node;
            }
        }
        return $js_allowed_items;
    }

    /**
     * @return array
     */
    public function getExtraItems()
    {
        $extra_items = array(
            'prices-drop' => array(
                'id_category' => 'prices-drop',
                'name' => $this->l('Specials'),
                'level_depth' => 0,
                'id_parent' => 0,
                'description' => '',
                'active' => true,
                'position' => 0,
                'url' => TouchizeControllerHelper::getRelativeURL(
                    $this->context->link->getPageLink('prices-drop')
                )
            ),
            'best-sales' => array(
                'id_category' => 'best-sales',
                'name' => $this->l('Best sellers'),
                'level_depth' => 0,
                'id_parent' => 0,
                'description' => '',
                'active' => true,
                'position' => 0,
                'url' => TouchizeControllerHelper::getRelativeURL(
                    $this->context->link->getPageLink('best-sales')
                )
            ),
            'new-products' => array(
                'id_category' => 'new-products',
                'name' => $this->l('New arrivals'),
                'level_depth' => 0,
                'id_parent' => 0,
                'description' => '',
                'active' => true,
                'position' => 0,
                'url' => TouchizeControllerHelper::getRelativeURL(
                    $this->context->link->getPageLink('new-products')
                )
            ),
        ) ;

        return $extra_items;
    }

    /**
     * @return array
     */
    public function getManufacturerItems($flat)
    {
        $manufacturersMenu = array(
            'manufacturer' => array(
                'id_category' => 'manufacturer',
                'name' => $this->l('Manufacturers'),
                'level_depth' => 0,
                'id_parent' => 0,
                'description' => '',
                'active' => true,
                'position' => 0,
                'url' => $this->context->link->getPageLink('manufacturer')
            )
        );
        $manufacturersMenu['manufacturer']['children'] = array();
        $manufacturers = Manufacturer::getManufacturers(false, $this->context->language->id, true, false, false, false);
        foreach ($manufacturers as $manufacturer) {
            $man = $this->createManufacturerMenuItem($manufacturer);
            if (!$flat) {
                $manufacturersMenu['manufacturer']['children'][$man['id_category']] = $man;
            } else {
                $manufacturersMenu[$man['id_category']] = $man;
            }
        }
        return $manufacturersMenu;
    }

    public function createManufacturerMenuItem($manufacturer)
    {
        return array(
            'id_category' => 'manufacturer'.$manufacturer['id_manufacturer'],
            'name' => $manufacturer['name'],
            'level_depth' => 0,
            'id_parent' => 0,
            'description' => '',
            'active' => true,
            'position' => 0,
            'url' => $this->context->link->getmanufacturerLink(
                $manufacturer['id_manufacturer'],
                $manufacturer['link_rewrite']
            )
        );
    }

    public function getNestedCategories(
        $shop_id,
        $root_category = null,
        $id_lang = false,
        $active = false,
        $flat = false,
        $groups = null,
        $use_shop_restriction = true,
        $sql_filter = '',
        $sql_sort = '',
        $sql_limit = ''
    ) {
        if (isset($root_category) && !Validate::isInt($root_category)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
            $groups = (array)$groups;
        }

        if (!isset($root_category)) {
            $root_category = Configuration::get('PS_HOME_CATEGORY');
        }

        $cache_id = 'Category::getNestedCategories_'.md5((int)$shop_id.(int)$root_category.(int)$id_lang.(int)$active
                .(int)$active.(int)$flat.(isset($groups) && Group::isFeatureActive() ? implode('', $groups) : ''));

        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS(
                'SELECT c.*, cl.*
                FROM `'._DB_PREFIX_.'category` c
                INNER JOIN `'._DB_PREFIX_.'category_shop` category_shop ON 
                (category_shop.`id_category` = c.`id_category` AND category_shop.`id_shop` = "'.(int)$shop_id.'")
                LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON 
                (c.`id_category` = cl.`id_category` AND cl.`id_shop` = "'.(int)$shop_id.'")
                WHERE 1 '.$sql_filter.' '.($id_lang ? 'AND cl.`id_lang` = '.(int)$id_lang : '').'
                '.($active ? ' AND (c.`active` = 1 OR c.`is_root_category` = 1)' : '').'
                '.(isset($groups) && Group::isFeatureActive() ? ' AND cg.`id_group` IN ('.
                implode(',', $groups).')' : '').'
                '.(!$id_lang || (isset($groups) && Group::isFeatureActive()) ? ' GROUP BY c.`id_category`' : '').'
                '.($sql_sort != '' ? $sql_sort : ' ORDER BY c.`level_depth` ASC').'
                '.($sql_sort == '' && $use_shop_restriction ? ', category_shop.`position` ASC' : '').'
                '.($sql_limit != '' ? $sql_limit : '')
            );

            $categories = array();
            $buff = array();

            if ($flat) {
                foreach ($result as $row) {
                    $categories[$row['id_category']] = $row;
                }
                return $categories;
            }


            foreach ($result as $row) {
                $current = &$buff[$row['id_category']];
                $current = $row;

                if ($row['id_category'] == $root_category) {
                    $categories[$row['id_category']] = &$current;
                } else {
                    $buff[$row['id_parent']]['children'][$row['id_category']] = &$current;
                }
            }

            Cache::store($cache_id, $categories);
        }

        return Cache::retrieve($cache_id);
    }
}
