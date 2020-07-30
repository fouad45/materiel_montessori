<?php
/**
 * 2018 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 */

/**
 * Selector controller.
 */

class TouchizeSelectorModuleFrontController extends ModuleFrontController
{
    /**
     * ModuleFrontController::initContent() override
     *
     * @see ModuleFrontController::initContent()
     */
    public function initContent()
    {
        //Code from ChangeCurrencyControllerCore, since need to reload
        $currency = new Currency((int)Tools::getValue('id_currency'));
        if (Validate::isLoadedObject($currency) && !$currency->deleted) {
            $this->context->cookie->id_currency = (int)$currency->id;
            Tools::redirect('index');
        } else {
            $helper = new TouchizeSelectorHelper();
            header('Content-type: application/json');
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");
            $this->ajaxDie(Tools::jsonEncode($helper->getSelectors()));
        }
    }

    /**
     * Safety to work with versions less than 1.6.0.12
     */
    protected function ajaxDie($value = null, $controller = null, $method = null)
    {
        if (is_callable('parent::ajaxDie')) {
            parent::ajaxDie($value, $controller, $method);
        } else {
            die($value);
        }
    }
}
