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
 * Class ColissimoOrder
 */
class ColissimoOrder extends ObjectModel
{
    /** @var int $id_colissimo_order */
    public $id_colissimo_order;

    /** @var int $id_order */
    public $id_order;

    /** @var int $id_colissimo_service */
    public $id_colissimo_service;

    /** @var int $id_colissimo_pickup_point */
    public $id_colissimo_pickup_point;

    /** @var bool $migration Flag to indicate if the label has been migrated from other module */
    public $migration;

    /** @var array $definition */
    public static $definition = array(
        'table'   => 'colissimo_order',
        'primary' => 'id_colissimo_order',
        'fields'  => array(
            'id_order'                  => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => true,
            ),
            'id_colissimo_service'      => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => false,
                'default'  => 0,
            ),
            'id_colissimo_pickup_point' => array(
                'type'     => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => false,
                'default'  => 0,
            ),
            'migration'                 => array(
                'type'     => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
                'default'  => 0,
            ),
        ),
    );

    /**
     * @param int $idLang
     * @return array|bool
     * @throws PrestaShopDatabaseException
     */
    public function getShipments($idLang)
    {
        return self::getShipmentsByColissimoOrderId($this->id, $idLang);
    }

    /**
     * @param int $idColissimoOrder
     * @param int $idLang
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getShipmentsByColissimoOrderId($idColissimoOrder, $idLang)
    {
        $shipments = array();

        $colissimoOrder = new self((int) $idColissimoOrder);
        $labelIds = $colissimoOrder->getLabelIds(true);
        if (!$labelIds) {
            return array();
        }
        foreach ($labelIds as $labelId) {
            $label = new ColissimoLabel((int) $labelId);
            $tracking = $label->getLastTrackingDetailsKnown();
            if (!$label->return_label) {
                $shipments[$label->id]['id_label'] = $label->id;
                $shipments[$label->id]['shipping_number'] = $label->shipping_number;
                $shipments[$label->id]['cn23'] = $label->cn23;
                $shipments[$label->id]['id_deposit_slip'] = $label->id_colissimo_deposit_slip;
                $shipments[$label->id]['status_text'] = $tracking['status_text'];
                $shipments[$label->id]['status_upd'] = $tracking['date_upd'];
                $shipments[$label->id]['coliship'] = $label->coliship;
                $shipments[$label->id]['insurance'] = $label->insurance;
                $shipments[$label->id]['migration'] = $label->migration;
                $shipments[$label->id]['file_deleted'] = $label->file_deleted;
                try {
                    $base64 = base64_encode(Tools::file_get_contents($label->getFilePath()));
                    $shipments[$label->id]['base64'] = $base64;
                } catch (Exception $e) {
                    $shipments[$label->id]['base64'] = '';
                }
                try {
                    $base64 = $label->cn23 ? base64_encode(Tools::file_get_contents($label->getCN23Path())) : '';
                    $shipments[$label->id]['cn23_base64'] = $base64;
                } catch (Exception $e) {
                    $shipments[$label->id]['cn23_base64'] = '';
                }
                $colissimoService = new ColissimoService((int) $colissimoOrder->id_colissimo_service);
                $order = new Order((int) $colissimoOrder->id_order);
                if ($colissimoService->is_pickup) {
                    $deliveryAddr = new Address((int) $order->id_address_invoice);
                } else {
                    $deliveryAddr = new Address((int) $order->id_address_delivery);
                }
                $isoCountry = Country::getIsoById($deliveryAddr->id_country);
                $shipments[$label->id]['return_available'] = (bool) ColissimoTools::getReturnDestinationTypeByIsoCountry(
                    $isoCountry
                );
                $shipments[$label->id]['is_printable'] = $label->label_format == 'pdf' ? true : false;
                $shipments[$label->id]['is_deletable'] = $label->isDeletable();
                $shipments[$label->id]['is_downloadable'] = $label->isDownloadable();
            } else {
                $shipments[$label->return_label]['id_return_label'] = $label->id;
                $shipments[$label->return_label]['return_shipping_number'] = $label->shipping_number;
                $shipments[$label->return_label]['return_cn23'] = $label->cn23;
                $shipments[$label->return_label]['return_coliship'] = $label->coliship;
                $shipments[$label->return_label]['return_insurance'] = $label->insurance;
                $shipments[$label->return_label]['return_migration'] = $label->migration;
                $shipments[$label->return_label]['return_file_deleted'] = $label->file_deleted;
                $shipments[$label->return_label]['return_is_deletable'] = $label->isDeletable();
                $shipments[$label->return_label]['return_is_downloadable'] = $label->isDownloadable();
                try {
                    $base64 = base64_encode(Tools::file_get_contents($label->getFilePath()));
                    $shipments[$label->return_label]['return_base64'] = $base64;
                } catch (Exception $e) {
                    $shipments[$label->return_label]['return_base64'] = '';
                }
                try {
                    $base64 = $label->cn23 ? base64_encode(Tools::file_get_contents($label->getCN23Path())) : '';
                    $shipments[$label->return_label]['return_cn23_base64'] = $base64;
                } catch (Exception $e) {
                    $shipments[$label->return_label]['return_cn23_base64'] = '';
                }
            }
        }

        return $shipments;
    }

    /**
     * @param int $idOrder
     * @return int
     */
    public static function exists($idOrder)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_order')
                ->from('colissimo_order')
                ->where('id_order = '.(int) $idOrder);

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)
                       ->getValue($dbQuery);
    }

    /**
     * @param bool $includeReturns
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public function getLabelIds($includeReturns = false)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_label')
                ->from('colissimo_label')
                ->where('id_colissimo_order = '.(int) $this->id);
        if (!$includeReturns) {
            $dbQuery->where('return_label = 0');
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)
                    ->executeS($dbQuery);
        if ($result && is_array($result)) {
            return array_map(
                function ($element) {
                    return $element['id_colissimo_label'];
                },
                $result
            );
        }

        return $result;
    }

    /**
     * @param int $idOrder
     * @return int
     */
    public static function getIdByOrderId($idOrder)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_order')
                ->from('colissimo_order')
                ->where('id_order = '.(int) $idOrder);

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)
                       ->getValue($dbQuery);
    }
}
