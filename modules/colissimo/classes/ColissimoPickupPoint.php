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
 * Class ColissimoPickupPoint
 */
class ColissimoPickupPoint extends ObjectModel
{
    /** @var int $id_colissimo_pickup_point */
    public $id_colissimo_pickup_point;

    /** @var string $colissimo_id */
    public $colissimo_id;

    /** @var string $company_name */
    public $company_name;

    /** @var string $address1 */
    public $address1;

    /** @var string $address2 */
    public $address2;

    /** @var string $address3 */
    public $address3;

    /** @var string $city */
    public $city;

    /** @var string $zipcode */
    public $zipcode;

    /** @var string $country */
    public $country;

    /** @var string $iso_country */
    public $iso_country;

    /** @var string $product_code */
    public $product_code;

    /** @var string $network */
    public $network;

    /** @var string $date_add */
    public $date_add;

    /** @var string $date_upd */
    public $date_upd;

    //@formatter:off

    /** @var array $definition */
    public static $definition = array(
        'table'   => 'colissimo_pickup_point',
        'primary' => 'id_colissimo_pickup_point',
        'fields'  => array(
            'colissimo_id' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 8),
            'company_name' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 64),
            'address1'     => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 120),
            'address2'     => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 120),
            'address3'     => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 120),
            'city'         => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 80),
            'zipcode'      => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 10),
            'country'      => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 64),
            'iso_country'  => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 2),
            'product_code' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 3),
            'network'      => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 10),
            'date_add'     => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd'     => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /** @var array $availableIso Countries that allow pickup point (15 ISO) */
    public static $availableIso = array('AD', 'AT', 'BE', 'DE', 'ES', 'FR', 'EE', 'GB', 'LT', 'LU', 'LV', 'MC', 'NL', 'PL', 'PT', 'SE', 'DK', 'FI', 'CZ', 'SK', 'SI');

    /** @var array $availableLanguages Available languages in the popup (7 languages) */
    public static $availableLanguages = array('fr', 'en', 'es', 'it', 'pt', 'nl', 'de');

    /** @var array $BPRAliases Product codes NOT TO USE -- Instead use BPR */
    public static $BPRAliases = array('ACP', 'CDI');

    //formatter:on

    /**
     * @param string $colissimoId
     * @return ColissimoPickupPoint
     */
    public static function getPickupPointByIdColissimo($colissimoId)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_pickup_point')
                ->from('colissimo_pickup_point')
                ->where('colissimo_id = "'.pSQL($colissimoId).'"');
        $id = Db::getInstance(_PS_USE_SQL_SLAVE_)
                ->getValue($dbQuery);

        return new self((int) $id);
    }

    /**
     * @return string
     */
    public function getProductCodeForAffranchissement()
    {
        if (in_array($this->product_code, self::$BPRAliases)) {
            return 'BPR';
        }

        return $this->product_code;
    }
}
