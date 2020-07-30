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
 * Class ColissimoSoniceEtiquetageMigration
 */
class ColissimoSoniceEtiquetageMigration implements ColissimoOtherModuleInterface
{
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
        if (Configuration::getGlobalValue('COLISSIMO_MIGRATION_CREDENTIALS') == 1) {
            return true;
        }
        $configs = Configuration::getMultiShopValues('SONICE_ETQ_CONF');
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $config = unserialize($configs[$shop['id_shop']]);
            if ($config['ContractNumber'] && $config['Password']) {
                Configuration::updateValue(
                    'COLISSIMO_ACCOUNT_LOGIN',
                    $config['ContractNumber'],
                    false,
                    null,
                    $shop['id_shop']
                );
                Configuration::updateValue(
                    'COLISSIMO_ACCOUNT_PASSWORD',
                    $config['Password'],
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
    public function migrateCarriers()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function migrateConfiguration()
    {
        $configs = Configuration::getMultiShopValues('SONICE_ETQ_CONF');
        $statuses = Configuration::getMultiShopValues('SONICE_ETQ_STATUS');
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $config = unserialize($configs[$shop['id_shop']]);
            $selectedStatuses = unserialize($statuses[$shop['id_shop']]);
            $address = array(
                'sender_company'   => $config['companyName'],
                'sender_lastname'  => $config['Surname'],
                'sender_firstname' => $config['Name'],
                'sender_address1'  => $config['Line2'],
                'sender_address2'  => $config['Line0'],
                'sender_address3'  => '',
                'sender_address4'  => '',
                'sender_city'      => $config['City'],
                'sender_zipcode'   => $config['PostalCode'],
                'sender_country'   => 'FR',
                'sender_phone'     => '+33'.Tools::substr($config['phoneNumber'], -9),
                'sender_email'     => $config['Mail'],
            );
            Configuration::updateValue(
                'COLISSIMO_SENDER_ADDRESS',
                json_encode($address),
                false,
                null,
                $shop['id_shop']
            );
            Configuration::updateValue(
                'COLISSIMO_LABEL_FORMAT',
                $config['output_print_type'],
                false,
                null,
                $shop['id_shop']
            );
            if (is_array($selectedStatuses)) {
                Configuration::updateValue(
                    'COLISSIMO_GENERATE_LABEL_STATUSES',
                    json_encode(array_fill_keys(array_values($selectedStatuses), 1)),
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
    public function migrateData()
    {
        return true;
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function migrateDocuments()
    {
        $oldLabelsQuery = new DbQuery();
        $oldLabelsQuery->select('*')
                       ->from('sonice_etq_label');
        $oldLabels = Db::getInstance(_PS_USE_SQL_SLAVE_)
                       ->executeS($oldLabelsQuery);

        foreach ($oldLabels as $oldLabel) {
            $idColissimoOrder = ColissimoOrder::getIdByOrderId((int) $oldLabel['id_order']);
            $colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);
            if (!Validate::isLoadedObject($colissimoOrder)) {
                $this->logger->error('Invalid ColissimoOrder object', array('id' => $idColissimoOrder));
                continue;
            }
            $colissimoLabel = new ColissimoLabel();
            $colissimoLabel->id_colissimo_order = (int) $colissimoOrder->id;
            $colissimoLabel->id_colissimo_deposit_slip = 0;
            $colissimoLabel->shipping_number = pSQL($oldLabel['parcel_number']);
            $colissimoLabel->label_format = 'pdf';
            $colissimoLabel->coliship = 0;
            $colissimoLabel->return_label = 0;
            $colissimoLabel->cn23 = 0;
            $colissimoLabel->date_add = pSQL($oldLabel['date_add']);
            $colissimoLabel->migration = 1;
            $colissimoLabel->insurance = null;
            $colissimoLabel->file_deleted = 0;
            try {
                $colissimoLabel->save(true);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
                continue;
            }
        }

        return true;
    }
}
