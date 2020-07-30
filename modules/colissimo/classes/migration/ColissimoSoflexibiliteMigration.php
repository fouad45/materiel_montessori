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
 * Class ColissimoSoflexibiliteMigration
 */
class ColissimoSoflexibiliteMigration implements ColissimoOtherModuleInterface
{
    const MODULE_NAME = 'soflexibilite';

    /** @var ColissimoLogger $logger */
    private $logger;

    /**
     * ColissimoSoflexibiliteMigration constructor.
     * @param ColissimoLogger $logger
     */
    public function __construct(ColissimoLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return bool
     */
    public function migrateCredentials()
    {
        $login = Configuration::getMultiShopValues('SOFLEXIBILITE_LOGIN');
        $passwd = Configuration::getMultiShopValues('SOFLEXIBILITE_PASSWORD');
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            if ($login[$shop['id_shop']] && $passwd[$shop['id_shop']]) {
                Configuration::updateValue(
                    'COLISSIMO_ACCOUNT_LOGIN',
                    $login[$shop['id_shop']],
                    false,
                    null,
                    $shop['id_shop']
                );
                Configuration::updateValue(
                    'COLISSIMO_ACCOUNT_PASSWORD',
                    $passwd[$shop['id_shop']],
                    false,
                    null,
                    $shop['id_shop']
                );

                // If Colissimo Simplicite has at least one login/pass configured, we considered that these credentials
                // are the correct one. We won't try to migrate credentials from other modules.
                Configuration::updateGlobalValue('COLISSIMO_MIGRATION_CREDENTIALS', 1);
            }
        }

        return true;
    }

    /**
     * @return bool
     * @throws PrestaShopException
     */
    public function migrateCarriers()
    {
        $correspondance = array(
            'SOFLEXIBILITE_DOM_ID' => 'COLISSIMO_CARRIER_SANS_SIGNATURE',
            'SOFLEXIBILITE_DOS_ID' => 'COLISSIMO_CARRIER_AVEC_SIGNATURE',
            'SOFLEXIBILITE_A2P_ID' => 'COLISSIMO_CARRIER_RELAIS',
        );
        $flexibiliteCarrierIds = Configuration::getMultiple(
            array(
                'SOFLEXIBILITE_DOM_ID',
                'SOFLEXIBILITE_DOS_ID',
                'SOFLEXIBILITE_A2P_ID',
            )
        );
        foreach ($flexibiliteCarrierIds as $key => $flexibiliteCarrierId) {
            ColissimoTools::migrateCarrierData($flexibiliteCarrierId, $correspondance, $key);
        }
        Db::getInstance()
          ->update('carrier', array('active' => 0), 'external_module_name = "'.pSQL(self::MODULE_NAME).'"');

        return true;
    }

    /**
     * @return bool
     */
    public function migrateConfiguration()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function migrateDocuments()
    {
        return true;
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function migrateData()
    {
        $tableExists = Db::getInstance()
                         ->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'so_delivery"');
        if (!empty($tableExists)) {
            $oldThirdPatyOrdersQuery = new DbQuery();
            $oldThirdPatyOrdersQuery->select('*')
                                    ->from('so_delivery');
            $oldThirdPartyOrders = Db::getInstance(_PS_USE_SQL_SLAVE_)
                                     ->executeS($oldThirdPatyOrdersQuery);

            foreach ($oldThirdPartyOrders as $oldThirdPartyOrder) {
                $pickupPointReference = $oldThirdPartyOrder['point_id'];
                $order = ColissimoTools::getOrderByCartId((int) $oldThirdPartyOrder['cart_id']);
                if (!Validate::isLoadedObject($order)) {
                    continue;
                }
                $deliveryAddr = new Address((int) $order->id_address_delivery);
                $deliveryMode = $oldThirdPartyOrder['type'];
                $destinationType = ColissimoTools::getDestinationTypeByIsoCountry(
                    Country::getIsoById($deliveryAddr->id_country)
                );
                $idService = ColissimoService::getServiceIdByProductCodeDestinationType(
                    $deliveryMode,
                    $destinationType
                );
                $idColissimoOrder = ColissimoOrder::getIdByOrderId((int) $order->id);
                $colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);
                $colissimoOrder->id_colissimo_pickup_point = 0;
                if ($pickupPointReference) {
                    $colissimoPickupPoint = ColissimoPickupPoint::getPickupPointByIdColissimo($pickupPointReference);
                    if (!Validate::isLoadedObject($colissimoPickupPoint)) {
                        $colissimoPickupPoint->colissimo_id = pSQL($pickupPointReference);
                        $colissimoPickupPoint->company_name = pSQL($deliveryAddr->company);
                        $colissimoPickupPoint->address1 = pSQL($deliveryAddr->address1);
                        $colissimoPickupPoint->address2 = pSQL($deliveryAddr->address2);
                        $colissimoPickupPoint->address3 = '';
                        $colissimoPickupPoint->city = pSQL($deliveryAddr->city);
                        $colissimoPickupPoint->zipcode = pSQL($deliveryAddr->postcode);
                        $colissimoPickupPoint->country = pSQL(
                            Country::getNameById(Configuration::get('PS_LANG_DEFAULT'), $deliveryAddr->id_country)
                        );
                        $colissimoPickupPoint->iso_country = pSQL(Country::getIsoById($deliveryAddr->id_country));
                        $colissimoPickupPoint->product_code = pSQL($oldThirdPartyOrder['type']);
                        $colissimoPickupPoint->network = pSQL($oldThirdPartyOrder['codereseau']);
                        try {
                            $colissimoPickupPoint->save();
                        } catch (Exception $e) {
                            $this->logger->error($e->getMessage());
                            continue;
                        }
                    }
                    $colissimoOrder->id_colissimo_pickup_point = (int) $colissimoPickupPoint->id;
                }
                $colissimoOrder->id_order = (int) $order->id;
                $colissimoOrder->id_colissimo_service = (int) $idService;
                $colissimoOrder->migration = 1;
                try {
                    $colissimoOrder->save();
                } catch (Exception $e) {
                    $this->logger->error($e->getMessage());
                    continue;
                }
            }
        }

        $tableExists = Db::getInstance()
                         ->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'socolissimo_delivery_info"');
        if (!empty($tableExists)) {
            $oldColissimoOrdersQuery = new DbQuery();
            $oldColissimoOrdersQuery->select('*')
                                    ->from('socolissimo_delivery_info');
            $oldColissimoOrders = Db::getInstance()
                                    ->executeS($oldColissimoOrdersQuery);

            foreach ($oldColissimoOrders as $oldOrder) {
                $pickupPointReference = $oldOrder['prid'];
                $order = ColissimoTools::getOrderByCartId((int) $oldOrder['id_cart']);
                if (!Validate::isLoadedObject($order)) {
                    continue;
                }
                $deliveryAddr = new Address((int) $order->id_address_delivery);
                $deliveryMode = $oldOrder['delivery_mode'];
                $destinationType = ColissimoTools::getDestinationTypeByIsoCountry(
                    Country::getIsoById($deliveryAddr->id_country)
                );
                $idService = ColissimoService::getServiceIdByProductCodeDestinationType(
                    $deliveryMode,
                    $destinationType
                );
                $idColissimoOrder = ColissimoOrder::getIdByOrderId((int) $order->id);
                $colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);
                $colissimoOrder->id_colissimo_pickup_point = 0;
                if ($pickupPointReference) {
                    $colissimoPickupPoint = ColissimoPickupPoint::getPickupPointByIdColissimo($pickupPointReference);
                    if (!Validate::isLoadedObject($colissimoPickupPoint)) {
                        $colissimoPickupPoint->colissimo_id = pSQL($pickupPointReference);
                        $colissimoPickupPoint->company_name = pSQL($deliveryAddr->company);
                        $colissimoPickupPoint->address1 = pSQL($deliveryAddr->address1);
                        $colissimoPickupPoint->address2 = pSQL($deliveryAddr->address2);
                        $colissimoPickupPoint->address3 = '';
                        $colissimoPickupPoint->city = pSQL($deliveryAddr->city);
                        $colissimoPickupPoint->zipcode = pSQL($deliveryAddr->postcode);
                        $colissimoPickupPoint->country = pSQL(
                            Country::getNameById(Configuration::get('PS_LANG_DEFAULT'), $deliveryAddr->id_country)
                        );
                        $colissimoPickupPoint->iso_country = pSQL(Country::getIsoById($deliveryAddr->id_country));
                        $colissimoPickupPoint->product_code = pSQL($oldOrder['delivery_mode']);
                        $colissimoPickupPoint->network = pSQL($oldOrder['codereseau']);
                        try {
                            $colissimoPickupPoint->save();
                        } catch (Exception $e) {
                            $this->logger->error($e->getMessage());
                            continue;
                        }
                    }
                    $colissimoOrder->id_colissimo_pickup_point = (int) $colissimoPickupPoint->id;
                }
                $colissimoOrder->id_order = (int) $order->id;
                $colissimoOrder->id_colissimo_service = (int) $idService;
                $colissimoOrder->migration = 1;
                try {
                    $colissimoOrder->save();
                } catch (Exception $e) {
                    $this->logger->error($e->getMessage());
                    continue;
                }
            }
        }

        return true;
    }
}
