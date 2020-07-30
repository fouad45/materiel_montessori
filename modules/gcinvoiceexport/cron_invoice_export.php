<?php
/**
 * GcInvoiceExport
 *
 * @author    Grégory Chartier <hello@gregorychartier.fr>
 * @copyright 2019 Grégory Chartier (https://www.gregorychartier.fr)
 * @license   Commercial license see license.txt
 * @category  Prestashop
 * @category  Module
 */

require_once(dirname(__FILE__) . '/../../config/config.inc.php');

if (Tools::getValue('secure_key')) {
    $id_shop   = Tools::getValue('id_shop');
    $secureKey = md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));
    if (!empty($secureKey) && $secureKey === Tools::getValue('secure_key')) {
        $module = Module::getInstanceByName('gcinvoiceexport');
        $module->processMailExport($id_shop);
        die('Ok');
    }
}
