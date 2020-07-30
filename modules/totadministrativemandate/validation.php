<?php
/**
 * 2007-2017 PrestaShop.
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

include dirname(__FILE__).'/../../config/config.inc.php';
if (version_compare(_PS_VERSION_, '1.5', '>')) {
    $context = Context::getContext();
    // Before 1.5.1 Context link doesn't exists
    if (version_compare(_PS_VERSION_, '1.5.1', '<')) {
        $context->link = new Link();
    }

    if (!defined('_PS_BASE_URL_')) {
        define('_PS_BASE_URL_', '');
    }
    if (!defined('_PS_BASE_URL_SSL_')) {
        define('_PS_BASE_URL_SSL_', '');
    }

    Tools::redirect($context->link->getModuleLink('totadministrativemandate', 'validation'));
}
$cookie = new Cookie('ps');
$cart = new Cart($cookie->id_cart);
include dirname(__FILE__).'/../../header.php';
include_once dirname(__FILE__).'/totadministrativemandate.php';
$module = new totAdministrativeMandate();

if ($cart->id_customer == 0 || $cart->id_address_delivery == 0
    || $cart->id_address_invoice == 0 || !$module->active) {
    Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');
}

$authorized = false;
foreach (Module::getPaymentModules() as $module) {
    if ($module['name'] == 'totadministrativemandate') {
        $authorized = true;
        break;
    }
}

if (!$authorized) {
    die(Tools::displayError('This payment method is not available.'));
}

$customer = new Customer((int) $cart->id_customer);

if (!Validate::isLoadedObject($customer)) {
    Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');
}

$currency = new Currency($cookie->id_currency);
$total = (float) $cart->getOrderTotal(true, Cart::BOTH);

$mailVars = array(
    '{firstname}' => $customer->firstname,
    '{lastname}' => $customer->lastname,
    '{total_paid}' => Tools::displayPrice($total),
    '{payment_name}' => $module->conf[Tools::strtoupper($module->name).'_OWNER'],
    '{payment_address}' => nl2br($module->conf[Tools::strtoupper($module->name).'_ADDRESS']),
    '{payment_details}' => nl2br($module->conf[Tools::strtoupper($module->name).'_DETAILS']),
);

$module->validateOrder(
    (int) $cart->id,
    $module->conf[Tools::strtoupper($module->name).'_WAIT'],
    $total,
    $module->displayName,
    null,
    $mailVars,
    (int) $currency->id,
    false,
    $customer->secure_key
);

$id_order = $module->currentOrder;

$l = Tools::getShopDomain(true).$module->getPathUri().'pdftot.php?id_order='.urlencode($id_order);

$mailVars['{pdf_link}'] = $l;
$mailVars['{order_name}'] = $id_order;

$id_lang_mail = TotAdministrativeMandate::getMailLanguageId($cart->id_lang);

Mail::Send(
    (int) $id_lang_mail,
    'wait_mandate',
    $module->displayName,
    $mailVars,
    $customer->email,
    $customer->firstname.' '.$customer->lastname,
    null,
    null,
    null,
    null,
    dirname(__FILE__).'/mails/',
    false
);

$datas = array(
    'id_cart' => (int) $cart->id,
    'id_module' => (int) $module->id,
    'id_order' => (int) $module->currentOrder,
    'key' => $customer->secure_key,
);

Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?'.http_build_query($datas, '', '&'));
