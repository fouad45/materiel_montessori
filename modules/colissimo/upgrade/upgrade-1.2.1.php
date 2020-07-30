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
 * Upgrade to 1.2.1
 *
 * @param $module Colissimo
 * @return bool
 * @throws PrestaShopDatabaseException
 * @throws PrestaShopException
 */
function upgrade_module_1_2_1($module)
{
    $logsEnabled = Configuration::get('COLISSIMO_LOGS');
    Configuration::updateValue('COLISSIMO_LOGS', 1);
    $module->initLogger();
    $module->logger->setChannel('ModuleUpgrade');
    $module->logger->info('Module upgrade. Version 1.2.1');

    $senderAddressGlobal = Configuration::getGlobalValue('COLISSIMO_SENDER_ADDRESS');
    if ($senderAddressGlobal) {
        $senderAddress = (array) json_decode($senderAddressGlobal);
        $senderAddress['sender_country'] = 'FR';
        Configuration::updateGlobalValue('COLISSIMO_SENDER_ADDRESS', json_encode($senderAddress));
    }

    // Create new tables
    $colissimoCustomCategoryQuery = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_custom_category` (
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
    $colissimoCustomProductQuery = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_custom_product` (
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

    Db::getInstance()->execute("DROP TABLE IF EXISTS `'._DB_PREFIX_.'colissimo_custom_product`");
    Db::getInstance()->execute("DROP TABLE IF EXISTS `'._DB_PREFIX_.'colissimo_custom_category`");

    $module->logger->info('Module upgraded.');
    Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

    return true;
}
