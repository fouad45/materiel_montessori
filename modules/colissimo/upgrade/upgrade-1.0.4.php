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
 * Upgrade to 1.0.4
 *
 * @param $module Colissimo
 * @return bool
 */
function upgrade_module_1_0_4($module)
{
    $logsEnabled = Configuration::get('COLISSIMO_LOGS');
    Configuration::updateValue('COLISSIMO_LOGS', 1);
    $module->initLogger();
    $module->logger->setChannel('ModuleUpgrade');
    $module->logger->info('Module upgrade. Version 1.0.4');
    if (version_compare(_PS_VERSION_, '1.7', '>=')) {
        $module->unregisterHook('actionCarrierProcess');
        $module->logger->info('Unregister actionCarrierProcess');
    } else {
        $module->unregisterHook('actionValidateStepComplete');
        $module->logger->info('Unregister actionValidateStepComplete');
    }
    $OMWithoutSignatureId = ColissimoService::getServiceIdByIdCarrierDestinationType(
        Configuration::get('COLISSIMO_CARRIER_SANS_SIGNATURE'),
        'OM'
    );
    if (!$OMWithoutSignatureId) {
        $colissimoService = new ColissimoService();
        $colissimoService->id_carrier = (int) Configuration::get('COLISSIMO_CARRIER_SANS_SIGNATURE');
        $colissimoService->product_code = 'COM';
        $colissimoService->commercial_name = 'OM - DOMICILE sans signature';
        $colissimoService->destination_type = 'OM';
        $colissimoService->is_signature = 0;
        $colissimoService->is_pickup = 0;
        $colissimoService->is_return = 0;
        $colissimoService->type = 'SANS_SIGNATURE';
        try {
            $colissimoService->save();
            $module->logger->info('COM service added.');
        } catch (Exception $e) {
            $module->logger->error('Cannot add OM without signature service.', array('message' => $e->getMessage()));
            Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

            return false;
        }
    } else {
        $module->logger->info('COM service already exists.');
    }
    $module->logger->info('Module upgraded.');
    Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

    return true;
}
