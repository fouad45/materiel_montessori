<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author     PrestaShop SA <contact@prestashop.com>
 * @copyright  2007-2019 PrestaShop SA
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Upgrade to 1.2.0
 *
 * @param $module Colissimo
 * @return bool
 * @throws PrestaShopDatabaseException
 * @throws PrestaShopException
 */
function upgrade_module_1_2_0($module)
{
    $logsEnabled = Configuration::get('COLISSIMO_LOGS');
    Configuration::updateValue('COLISSIMO_LOGS', 1);
    $module->initLogger();
    $module->logger->setChannel('ModuleUpgrade');
    $module->logger->info('Module upgrade. Version 1.2.0');

    // Create new tables
    $colissimoCustomCategoryQuery = "CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'colissimo_custom_category` (
        `id_colissimo_custom_category` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `id_category` INT(10) NULL DEFAULT 0,
        `short_desc` VARCHAR(64) NULL DEFAULT NULL,
        `id_country_origin` INT(10) NULL DEFAULT 0,
        `hs_code` VARCHAR(50) NULL DEFAULT NULL,
        PRIMARY KEY (`id_colissimo_custom_category`)
    )";
    $colissimoCustomCategory = Db::getInstance()
                                 ->execute($colissimoCustomCategoryQuery);
    if (!$colissimoCustomCategory) {
        $module->logger->error('Cannot create table colissimo_custom_category.');
        Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

        return false;
    }
    $colissimoCustomProductQuery = "CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'colissimo_custom_product` (
        `id_colissimo_custom_product` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `id_product` INT(10) NULL DEFAULT 0,
        `short_desc` VARCHAR(64) NULL DEFAULT NULL,
        `id_country_origin` INT(10) NULL DEFAULT 0,
        `hs_code` VARCHAR(50) NULL DEFAULT NULL,
        PRIMARY KEY (`id_colissimo_custom_product`)
    )";
    $colissimoCustomProduct = Db::getInstance()
                                ->execute($colissimoCustomProductQuery);
    if (!$colissimoCustomProduct) {
        $module->logger->error('Cannot create table colissimo_custom_product.');
        Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

        return false;
    }

    // Add new hooks
    if (!$module->isRegisteredInHook('displayAdminProductsExtra')) {
        $module->registerHook('displayAdminProductsExtra');
        $module->logger->info('Register module on displayAdminProductsExtra');
    } else {
        $module->logger->info('Module already registered on displayAdminProductsExtra');
    }
    if (!$module->isRegisteredInHook('actionProductUpdate')) {
        $module->registerHook('actionProductUpdate');
        $module->logger->info('Register module on actionProductUpdate');
    } else {
        $module->logger->info('Module already registered on actionProductUpdate');
    }
    if (!$module->isRegisteredInHook('actionAdminCategoriesControllerSaveAfter')) {
        $module->registerHook('actionAdminCategoriesControllerSaveAfter');
        $module->logger->info('Register module on actionAdminCategoriesControllerSaveAfter');
    } else {
        $module->logger->info('Module already registered on actionAdminCategoriesControllerSaveAfter');
    }
    if (!$module->isRegisteredInHook('actionAdminCategoriesFormModifier')) {
        $module->registerHook('actionAdminCategoriesFormModifier');
        $module->logger->info('Register module on actionAdminCategoriesFormModifier');
    } else {
        $module->logger->info('Module already registered on actionAdminCategoriesFormModifier');
    }
    if (!$module->isRegisteredInHook('actionCategoryFormBuilderModifier')) {
        $module->registerHook('actionCategoryFormBuilderModifier');
        $module->logger->info('Register module on actionCategoryFormBuilderModifier');
    } else {
        $module->logger->info('Module already registered on actionCategoryFormBuilderModifier');
    }
    if (!$module->isRegisteredInHook('actionAfterCreateCategoryFormHandler')) {
        $module->registerHook('actionAfterCreateCategoryFormHandler');
        $module->logger->info('Register module on actionAfterCreateCategoryFormHandler');
    } else {
        $module->logger->info('Module already registered on actionAfterCreateCategoryFormHandler');
    }
    if (!$module->isRegisteredInHook('actionAfterUpdateCategoryFormHandler')) {
        $module->registerHook('actionAfterUpdateCategoryFormHandler');
        $module->logger->info('Register module on actionAfterUpdateCategoryFormHandler');
    } else {
        $module->logger->info('Module already registered on actionAfterUpdateCategoryFormHandler');
    }
    // Add INTRA_DOM in destination_type value
    $result = Db::getInstance()
                ->execute(
                    "ALTER TABLE `"._DB_PREFIX_."colissimo_service` MODIFY COLUMN `destination_type` ENUM('FRANCE','OM','EUROPE','WORLDWIDE','INTRA_DOM')"
                );
    if (!$result) {
        $module->logger->error('Cannot update column destination_type in colissimo_service table.');
        Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

        return false;
    }
    $module->logger->info('Column destination_type in colissimo_service updated.');
    // add services COLD  et COL
    $COLDService = ColissimoService::getServiceIdByIdCarrierDestinationType(
        Configuration::get('COLISSIMO_CARRIER_SANS_SIGNATURE'),
        'INTRA_DOM'
    );
    if (!$COLDService) {
        $colissimoService = new ColissimoService();
        $colissimoService->id_carrier = (int) Configuration::get('COLISSIMO_CARRIER_SANS_SIGNATURE');
        $colissimoService->product_code = 'COLD';
        $colissimoService->commercial_name = 'FR - DOMICILE sans signature';
        $colissimoService->destination_type = 'INTRA_DOM';
        $colissimoService->is_signature = 0;
        $colissimoService->is_pickup = 0;
        $colissimoService->is_return = 0;
        $colissimoService->type = 'SANS_SIGNATURE';
        try {
            $colissimoService->save();
            $module->logger->info('COLD service added.');
        } catch (Exception $e) {
            $module->logger->error('Cannot add COLD service.', array('message' => $e->getMessage()));
            Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

            return false;
        }
    } else {
        $module->logger->info('COLD service already exists.');
    }
    $COLService = ColissimoService::getServiceIdByIdCarrierDestinationType(
        Configuration::get('COLISSIMO_CARRIER_AVEC_SIGNATURE'),
        'INTRA_DOM'
    );
    if (!$COLService) {
        $colissimoService = new ColissimoService();
        $colissimoService->id_carrier = (int) Configuration::get('COLISSIMO_CARRIER_AVEC_SIGNATURE');
        $colissimoService->product_code = 'COL';
        $colissimoService->commercial_name = 'FR - DOMICILE avec signature';
        $colissimoService->destination_type = 'INTRA_DOM';
        $colissimoService->is_signature = 1;
        $colissimoService->is_pickup = 0;
        $colissimoService->is_return = 0;
        $colissimoService->type = 'AVEC_SIGNATURE';
        try {
            $colissimoService->save();
            $module->logger->info('COL service added.');
        } catch (Exception $e) {
            $module->logger->error('Cannot add COL service.', array('message' => $e->getMessage()));
            Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

            return false;
        }
    } else {
        $module->logger->info('COLD service already exists.');
    }
    //Add iso FR in sender address
    $senderAddresses = Configuration::getMultiShopValues('COLISSIMO_SENDER_ADDRESS');
    foreach ($senderAddresses as $idShop => $senderAddress) {
        if (!$senderAddress) {
            continue;
        }
        $address = (array) json_decode($senderAddress);
        $address['sender_country'] = 'FR';
        Configuration::updateValue('COLISSIMO_SENDER_ADDRESS', json_encode($address), false, null, (int) $idShop);
    }
    // Default values for new configurations
    Configuration::updateValue('COLISSIMO_ENABLE_BREXIT', 0);
    Configuration::updateValue('COLISSIMO_USE_RETURN_ADDRESS', 0);
    Configuration::updateValue('COLISSIMO_RETURN_ADDRESS', '');

    $module->logger->info('Clearing cache.');
    Tools::clearCache();
    $module->logger->info('Module upgraded.');
    Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);
    Configuration::updateGlobalValue('COLISSIMO_SHOW_WHATS_NEW', 1);

    return true;
}
