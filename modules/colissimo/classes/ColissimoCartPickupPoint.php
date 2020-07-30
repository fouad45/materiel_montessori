<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class ColissimoCartPickupPoint
 */
class ColissimoCartPickupPoint
{
    /**
     * @param int $idCart
     * @return int
     */
    public static function getByCartId($idCart)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_pickup_point')
                ->from('colissimo_cart_pickup_point')
                ->where('id_cart = '.(int) $idCart);

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)
                       ->getValue($dbQuery);
    }

    /**
     * @param int    $idCart
     * @param int    $idPickupPoint
     * @param string $mobilePhone
     * @throws PrestaShopDatabaseException
     */
    public static function updateCartPickupPoint($idCart, $idPickupPoint, $mobilePhone)
    {
        Db::getInstance()
          ->insert(
              'colissimo_cart_pickup_point',
              array(
                  'id_cart'                   => (int) $idCart,
                  'id_colissimo_pickup_point' => (int) $idPickupPoint,
                  'mobile_phone'              => pSQL($mobilePhone),
              ),
              false,
              false,
              Db::REPLACE
          );
    }

    /**
     * @param int    $idCart
     * @param string $mobilePhone
     */
    public static function updateMobilePhoneByCartId($idCart, $mobilePhone)
    {
        Db::getInstance()
          ->update(
              'colissimo_cart_pickup_point',
              array('mobile_phone' => pSQL($mobilePhone)),
              'id_cart = '.(int) $idCart
          );
    }

    /**
     * @param int $idCart
     * @return false|null|string
     */
    public static function getMobilePhoneByCartId($idCart)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('mobile_phone')
                ->from('colissimo_cart_pickup_point')
                ->where('id_cart = '.(int) $idCart);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)
                 ->getValue($dbQuery);
    }
}
