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
 * Class ColissimoSocolissimoMigration
 */
class ColissimoSocolissimoMigration implements ColissimoOtherModuleInterface
{
    /** @var ColissimoLogger $logger */
    protected $logger;

    /**
     * ColissimoColissimoSimpliciteMigration constructor.
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
        if (Configuration::getGlobalValue('COLISSIMO_MIGRATION_CREDENTIALS') == 1) {
            return true;
        }
        $login = Configuration::getMultiShopValues('SOCOLISSIMO_LOGIN');
        $passwd = Configuration::getMultiShopValues('SOCOLISSIMO_PASSWORD');
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
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function migrateConfiguration()
    {
        $preparationTime = Configuration::getMultiShopValues('SOCOLISSIMO_PREPARATION_TIME');
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            Configuration::updateValue(
                'COLISSIMO_ORDER_PREPARATION_TIME',
                $preparationTime[$shop['id_shop']],
                false,
                null,
                $shop['id_shop']
            );
        }

        return true;
    }

    /**
     * @return bool
     */
    public function migrateCarriers()
    {
        $idCarrier = Configuration::getGlobalValue('SOCOLISSIMO_CARRIER_ID');
        Db::getInstance()
          ->update('carrier', array('active' => 0), 'id_carrier = '.(int) $idCarrier);

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
                         ->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'socolissimo_delivery_point"');
        if (!empty($tableExists)) {
            $oldThirdPatyOrdersQuery = new DbQuery();
            $oldThirdPatyOrdersQuery->select('*')
                                    ->from('socolissimo_delivery_point');
            $oldThirdPartyOrders = Db::getInstance(_PS_USE_SQL_SLAVE_)
                                     ->executeS($oldThirdPatyOrdersQuery);

            foreach ($oldThirdPartyOrders as $oldThirdPartyOrder) {
                $pickupPointReference = $oldThirdPartyOrder['identifiant'];
                $order = ColissimoTools::getOrderByCartId((int) $oldThirdPartyOrder['id_cart']);
                if (!Validate::isLoadedObject($order)) {
                    continue;
                }
                $deliveryAddr = new Address((int) $order->id_address_delivery);
                $deliveryMode = $oldThirdPartyOrder['typeDePoint'];
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
                        $colissimoPickupPoint->company_name =
                            pSQL($oldThirdPartyOrder['nom']) ? pSQL($oldThirdPartyOrder['nom']) : ' ';
                        $colissimoPickupPoint->address1 = pSQL($deliveryAddr->address1);
                        $colissimoPickupPoint->address2 = pSQL($deliveryAddr->address2);
                        $colissimoPickupPoint->address3 = '';
                        $colissimoPickupPoint->city = pSQL($deliveryAddr->city);
                        $colissimoPickupPoint->zipcode = pSQL($deliveryAddr->postcode);
                        $colissimoPickupPoint->country = pSQL(
                            Country::getNameById(Configuration::get('PS_LANG_DEFAULT'), $deliveryAddr->id_country)
                        );
                        $colissimoPickupPoint->iso_country = pSQL(Country::getIsoById($deliveryAddr->id_country));
                        $colissimoPickupPoint->product_code = pSQL($deliveryMode);
                        $colissimoPickupPoint->network = pSQL($oldThirdPartyOrder['reseau']);
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
                        $colissimoPickupPoint->company_name =
                            pSQL($oldOrder['prname']) ? pSQL($oldOrder['prname']) : ' ';
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

    public function migrateDocuments()
    {
        return true;
    }
}
