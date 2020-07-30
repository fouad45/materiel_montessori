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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2019 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Upgrade to 1.0.1
 *
 * - Sender address: replace serialized value stored in Configuration table with json encoded value
 *
 * @param $module Colissimo
 * @return bool
 */
function upgrade_module_1_0_1($module)
{
    $logsEnabled = Configuration::get('COLISSIMO_LOGS');
    Configuration::updateValue('COLISSIMO_LOGS', 1);
    $module->initLogger();
    $module->logger->setChannel('ModuleUpgrade');
    $module->logger->info('Module upgrade. Version 1.0.1');
    $senderAddresses = Configuration::getMultiShopValues('COLISSIMO_SENDER_ADDRESS');
    foreach ($senderAddresses as $idShop => $senderAddress) {
        if (!$senderAddress) {
            continue;
        }
        $address = (array) unserialize($senderAddress);
        if (!is_array($address) || empty($address)) {
            continue;
        }
        Configuration::updateValue('COLISSIMO_SENDER_ADDRESS', json_encode($address), false, null, (int) $idShop);
    }
    $module->logger->info('Module upgraded.');
    Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

    return true;
}
