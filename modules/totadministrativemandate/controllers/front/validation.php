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

class TotAdministrativeMandateValidationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cart = $this->context->cart;

        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0 || !$this->module->active) {
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

        $currency = new Currency($this->context->cookie->id_currency);
        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);

        $mail_Vars = array(
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{total_paid}' => Tools::displayPrice($total),
            '{payment_name}' => $this->module->conf[Tools::strtoupper($this->module->name).'_OWNER'],
            '{payment_address}' => Tools::nl2br($this->module->conf[Tools::strtoupper($this->module->name).'_ADDRESS']),
            '{payment_details}' => Tools::nl2br($this->module->conf[Tools::strtoupper($this->module->name).'_DETAILS']),
        );

        $this->module->validateOrder(
            (int) $cart->id,
            $this->module->conf[Tools::strtoupper($this->module->name).'_WAIT'],
            $total,
            $this->module->displayName,
            null,
            $mail_Vars,
            (int) $currency->id,
            false,
            $customer->secure_key
        );

        $id_order = Order::getOrderByCartId($cart->id);

        $order = new Order($id_order);

        if (isset($_SERVER['HTTPS'])) {
            $url_shop = Tools::getShopDomainSsl(true);
        } else {
            $url_shop = Tools::getShopDomain(true);
        }

        $l = $this->module->getPathUri().'pdftot.php?id_order='.urlencode($order->getUniqReference());

        $temp = array(
            '{order_name}' => $order->getUniqReference(),
            '{pdf_link}' => $l,
            '{shop_mandate}' => $url_shop,
        );

        $mailVars = array_merge($mail_Vars, $temp);

        $id_lang_mail = TotAdministrativeMandate::getMailLanguageId($cart->id_lang);

        $process = Mail::Send(
            (int) $id_lang_mail,
            'wait_mandate',
            $this->module->l('Mandat administratif'),
            $mailVars,
            $customer->email,
            $customer->firstname.' '.$customer->lastname,
            null,
            null,
            null,
            null,
            $this->module->getLocalPath().'/mails/',
            false
        );

        if (!$process) {
            die($this->module->l("Can't be send a mail"));
        }

        $datas = array(
            'id_cart' => (int) $cart->id,
            'id_module' => (int) $this->module->id,
            'id_order' => (int) $this->module->currentOrder,
            'key' => $customer->secure_key,
        );

        $id_lang = (int) $this->context->language->id;

        $link = $this->context->link->getPageLink('order-confirmation', null, $id_lang, $datas);

        Tools::redirectLink($link);
    }
}
