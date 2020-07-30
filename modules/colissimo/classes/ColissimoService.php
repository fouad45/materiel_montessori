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
 * Class ColissimoService
 */
class ColissimoService extends ObjectModel
{
    const TYPE_RELAIS = 'RELAIS';
    const TYPE_RETOUR = 'RETOUR';
    const TYPE_SIGN = 'AVEC_SIGNATURE';
    const TYPE_NOSIGN = 'SANS_SIGNATURE';

    /** @var int $id_carrier */
    public $id_carrier;

    /** @var string $product_code */
    public $product_code;

    /** @var string $commercial_name */
    public $commercial_name;

    /** @var string $destination_type Destination of the service (FRANCE, EUROPE, OM, WORLDWIDE) */
    public $destination_type;

    /** @var bool $is_signature Flag to indicate if the service requires signature at the delivery */
    public $is_signature;

    /** @var bool $is_pickup Flag to indicate if the delivery is in Pickup-point */
    public $is_pickup;

    /** @var bool $return Flag to indicate if the service is related to return shipments */
    public $is_return;

    /** @var string $type */
    public $type;

    /** @var array $definition */
    public static $definition = array(
        'table'   => 'colissimo_service',
        'primary' => 'id_colissimo_service',
        'fields'  => array(
            'id_carrier'       => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'),
            'product_code'     => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 5),
            'commercial_name'  => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 50),
            'destination_type' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 10),
            'is_signature'     => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'is_pickup'        => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'is_return'        => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'type'             => array('type' => self::TYPE_STRING, 'required' => true),
        ),
    );

    /** @var array $insurableProducts */
    public static $insurableProducts = array('COL', 'BPR', 'A2P', 'CDS', 'CORE', 'CORI', 'COLI');

    /** @var array $unavailableMachinableProductCodes */
    public static $unavailableMachinableProductCodes = array('BPR', 'A2P', 'CMT');

    /**
     * @param string $isoCode
     * @return bool
     */
    public function isInsurable($isoCode)
    {
        //@formatter:off
        if (in_array($this->product_code, self::$insurableProducts) || ($this->product_code == 'DOS' && $isoCode == 'FR')) {
            return true;
        }
        //@formatter:off

        return false;
    }

    /**
     * @return bool
     */
    public function isMachinableOptionAvailable()
    {
        return !in_array($this->product_code, self::$unavailableMachinableProductCodes);
    }

    /**
     * @param bool $excludeReturn
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getAll($excludeReturn = true)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('*')
                ->from('colissimo_service');
        if ($excludeReturn) {
            $dbQuery->where('is_return = 0');
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)
                 ->executeS($dbQuery);
    }

    /**
     * @param int    $idCarrierReference
     * @param string $destinationType
     * @return int
     */
    public static function getServiceIdByIdCarrierDestinationType($idCarrierReference, $destinationType)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_service')
                ->from('colissimo_service')
                ->where(
                    'id_carrier = '.(int) $idCarrierReference.' AND destination_type = "'.pSQL($destinationType).'"'
                );

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)
                       ->getValue($dbQuery);
    }

    /**
     * @param string $productCode
     * @param string $destinationType
     * @return int
     */
    public static function getServiceIdByProductCodeDestinationType($productCode, $destinationType)
    {
        //@formatter:off
        if (in_array($productCode, ColissimoPickupPoint::$BPRAliases)) {
            $productCode = 'BPR';
        }
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_service')
                ->from('colissimo_service')
                ->where('product_code = "'.pSQL($productCode).'" AND destination_type = "'.pSQL($destinationType).'"');
        //@formatter:on

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)
                       ->getValue($dbQuery);
    }

    /**
     * @param int $idCarrier
     * @return false|null|string
     */
    public static function getServiceTypeByIdCarrier($idCarrier)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('type')
                ->from('colissimo_service')
                ->where('id_carrier = '.(int) $idCarrier);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)
                 ->getValue($dbQuery);
    }

    /**
     * @param string $destinationType
     * @param bool   $includeReturn
     * @return array|false|string|null
     * @throws PrestaShopDatabaseException
     */
    public static function getServiceIdsByDestinationType($destinationType, $includeReturn = false)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_service')
                ->from('colissimo_service')
                ->where('destination_type = "'.pSQL($destinationType).'"');
        if (!$includeReturn) {
            $dbQuery->where('is_return = 0');
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)
                    ->executeS($dbQuery);
        if ($result && is_array($result)) {
            return array_map(
                function ($element) {
                    return $element['id_colissimo_service'];
                },
                $result
            );
        }

        return $result;
    }

    /**
     * @param string $isoCountryCustomer
     * @param array  $accountType
     * @return bool
     */
    public function isEligibleToAccount($isoCountryCustomer, $accountType)
    {
        $availableIso = ColissimoPickupPoint::$availableIso;
        if (Configuration::get('COLISSIMO_ENABLE_BREXIT')) {
            $availableIso = array_diff($availableIso, array('GB'));
        }
        //@formatter:off
        $destinationWorldWideEU = array(ColissimoTools::DEST_EU, ColissimoTools::DEST_WORLD);
        if (in_array($this->destination_type, $destinationWorldWideEU) && !isset($accountType[$this->destination_type])) {
            return false;
        }
        //@formatter:on
        if ($this->type == ColissimoService::TYPE_RELAIS) {
            if (!in_array($isoCountryCustomer, $availableIso)) {
                return false;
            }
        }
        if ($this->destination_type == ColissimoTools::DEST_EU &&
            $this->is_signature == 0 &&
            $this->type != ColissimoService::TYPE_RELAIS
        ) {
            if (!in_array($isoCountryCustomer, ColissimoTools::$isoEUCountriesZone1Zone3)) {
                return false;
            }
        }
        if ($this->destination_type == ColissimoTools::DEST_OM &&
            !isset($accountType[$this->destination_type])
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param int $id
     * @return false|string|null
     */
    public static function getServiceTypeById($id)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('type')
                ->from('colissimo_service')
                ->where('id_colissimo_service = '.(int) $id);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)
                 ->getValue($dbQuery);
    }
}
