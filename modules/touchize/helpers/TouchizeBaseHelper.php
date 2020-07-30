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
 * Admin helper.
 */

class TouchizeBaseHelper extends Module
{
    /**
     * @var string
     **/
    public $context;

    /**
     * @var VersionResolver
     */
    public $versionResolver;

    /**
     * TouchizeBaseHelper constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->name = 'touchize';
        $this->versionResolver = new VersionResolver();
    }

    /**
     * @param      $string
     * @param bool $specific
     * @param null $locale used to compatibility with PS1.7
     *
     * @return mixed
     */
    public function l($string, $specific = false, $locale = null)
    {
        if (!$specific) {
            $specific = Tools::strtolower(get_class($this));
        }

        return parent::l($string, $specific);
    }
    
    public static function getCSSFileAddition()
    {
        $id_shop = Shop::getContextShopID(true);
        $id_shop_group = Shop::getContextShopGroupID(true);
        if (!$id_shop && !$id_shop_group) {
            return;
        } else {
            return (int)$id_shop . '_' . $id_shop_group;
        }
    }
    
    /**
     * Add SQL restriction on shops for configuration table
     *
     * @param int $id_shop_group
     * @param int $id_shop
     * @return string
     */
    public static function sqlRestriction($id_shop_group, $id_shop)
    {
        if ($id_shop) {
            return ' AND id_shop = '.(int)$id_shop;
        } elseif ($id_shop_group) {
            return ' AND id_shop_group = '.(int)$id_shop_group.' AND (id_shop IS NULL OR id_shop = 0)';
        } else {
            return ' AND (id_shop_group IS NULL OR id_shop_group = 0) AND (id_shop IS NULL OR id_shop = 0)';
        }
    }
}
