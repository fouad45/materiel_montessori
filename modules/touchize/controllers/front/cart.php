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
 * Cart controller.
 */

class TouchizeCartModuleFrontController extends ModuleFrontController
{
    /**
     * @var object
     **/
    protected $cartHelper;

    /**
     * Routes the controller actions and initializes the cart (thru parent)
     */
    public function init()
    {
        # Need to initialize parent to get cart in context
        parent::init();

        $this->cartHelper = new TouchizeCartHelper();
        if (!$this->isTokenValid()) {
            header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request');
            header('Content-type: application/json');
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");
            $response = array(
                'Title' => $this->module->l('Invalid token...', 'cart'),
                'Msg' => $this->module->l('Please reload!', 'cart'),
                'Level' => '1',
            );

            $this->ajaxDie(Tools::jsonEncode($response));
        }

        $this->getCart();
    }

    /**
     * FrontController::isTokenValid() override
     *
     * @see FrontController::isTokenValid()
     */
    public function isTokenValid()
    {
        if (!Configuration::get('PS_TOKEN_ENABLE')) {
            return true;
        }

        return (0 == strcasecmp(
            Tools::getToken(false),
            TouchizeControllerHelper::getParam('token')
        ));
    }

    /**
     * Gets the cart
     *
     * @return JSON object SLQ Cart
     */
    protected function getCart()
    {
        $result = $this->cartHelper->getCartAdapterData();

        header('Content-type: application/json');

        $this->ajaxDie(Tools::jsonEncode($result));
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
