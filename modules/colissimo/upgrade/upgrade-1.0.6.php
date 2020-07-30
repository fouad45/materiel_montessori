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
 * Upgrade to 1.0.6
 *
 * @param $module Colissimo
 * @return bool
 */
function upgrade_module_1_0_6($module)
{
    $logsEnabled = Configuration::get('COLISSIMO_LOGS');
    Configuration::updateValue('COLISSIMO_LOGS', 1);
    $module->initLogger();
    $module->logger->setChannel('ModuleUpgrade');
    $module->logger->info('Module upgrade. Version 1.0.6');
    if (version_compare(_PS_VERSION_, '1.7', '<')) {
        if (!$module->isRegisteredInHook('displayPaymentTop')) {
            $module->registerHook('displayPaymentTop');
            $module->logger->info('Register module on displayPaymentTop');
        } else {
            $module->logger->info('Module already registered on displayPaymentTop');
        }
    }
    Configuration::updateGlobalValue('COLISSIMO_SHOW_WHATS_NEW', 0);
    $module->logger->info('Clearing cache.');
    Tools::clearCache();
    $module->logger->info('Module upgraded.');
    Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

    return true;
}
