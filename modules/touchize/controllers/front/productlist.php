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
 * Product list controller.
 */

class TouchizeProductlistModuleFrontController extends ModuleFrontController
{
    /**
     * ModuleFrontController::init() override
     *
     * @see ModuleFrontController::init()
     */
    public function initContent()
    {
        $categoryId = TouchizeControllerHelper::getParam('taxonId');
        $index = (int)TouchizeControllerHelper::getParam('index');
        $helper = new TouchizeProductHelper();
        $response = $helper->getIndexProductList($categoryId, $index);
        header('Content-type: application/json');
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        $this->ajaxDie(Tools::jsonEncode($response));
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

    /**
     * Assigns product list page sorting variables
     * added for compatibility with prestashop 1.7
     */
    public function productSort()
    {
        // no display quantity order if stock management disabled
        $stock_management = Configuration::get('PS_STOCK_MANAGEMENT') ? true : false;
        $order_by_values  = array(
            0 => 'name',
            1 => 'price',
            2 => 'date_add',
            3 => 'date_upd',
            4 => 'position',
            5 => 'manufacturer_name',
            6 => 'quantity',
            7 => 'reference'
        );
        $order_way_values = array(0 => 'asc', 1 => 'desc');

        $this->orderBy  = Tools::strtolower(
            Tools::getValue('orderby', $order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')])
        );
        $this->orderWay = Tools::strtolower(
            Tools::getValue('orderway', $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')])
        );

        if (!in_array($this->orderBy, $order_by_values)) {
            $this->orderBy = $order_by_values[0];
        }

        if (!in_array($this->orderWay, $order_way_values)) {
            $this->orderWay = $order_way_values[0];
        }

        $this->context->smarty->assign(array(
            'orderby'          => $this->orderBy,
            'orderway'         => $this->orderWay,
            'orderbydefault'   => $order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')],
            'orderwayposition' => $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')], // Deprecated: orderwayposition
            'orderwaydefault'  => $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')],
            'stock_management' => (int)$stock_management
        ));
    }
}
