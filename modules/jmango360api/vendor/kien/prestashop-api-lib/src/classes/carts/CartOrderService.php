<?php
/**
 * Created by PhpStorm.
 * User: steven
 * Date: 05/07/2019
 * Time: 17:20
 */

class CartOrderService extends BaseService
{

    /**
     * Implement business logic
     */
    public function doExecute()
    {
        if ($this->isGetMethod()) {

            $arrayId = explode(",", Tools::getValue('id_carts'));

            if (!$arrayId || empty($arrayId)) {
                $this->response = new CustomerOrderResponse();
                return;
            }

            // Convert elements to int
            $id_carts = array_map('intval', $arrayId);

            $orders = $this->getCartOrders($id_carts, tr);
            $this->response = new CustomerOrderResponse();
            $this->response->orders = $orders;

        } else {
            $this->throwUnsupportedMethodException();
        }
    }

    /**
     * Get customer orders.
     *
     * @param int $id_customer Customer id
     * @param bool $show_hidden_status Display or not hidden order statuses
     *
     * @return array Customer orders
     */
    public function getCartOrders($id_carts = array(), $show_hidden_status = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $orderStates = OrderState::getOrderStates((int)$context->language->id);
        $indexedOrderStates = array();
        foreach ($orderStates as $orderState) {
            $indexedOrderStates[$orderState['id_order_state']] = $orderState;
        }

        $sql = ' SELECT o.*, 
              (SELECT SUM(od.`product_quantity`) FROM `' . _DB_PREFIX_ . 'order_detail` od WHERE od.`id_order` = o.`id_order`) nb_products,
              (SELECT oh.`id_order_state` FROM `' . _DB_PREFIX_ . 'order_history` oh
               LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
               WHERE oh.`id_order` = o.`id_order` ' .
            (!$show_hidden_status ? ' AND os.`hidden` != 1' : '') .
            ' ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC LIMIT 1) id_order_state
            FROM `' . _DB_PREFIX_ . 'orders` o
            WHERE 1 AND o.`id_cart` IN (' . implode(',', $id_carts) . ')' . '
            GROUP BY o.`id_order`
            ORDER BY o.`date_add` DESC';
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$res) {
            return array();
        }

        foreach ($res as $key => $val) {
            $res[$key]['order_state'] = $indexedOrderStates[$val['id_order_state']]['name'];
            $res[$key]['invoice'] = $indexedOrderStates[$val['id_order_state']]['invoice'];
            $res[$key]['order_state_color'] = $indexedOrderStates[$val['id_order_state']]['color'];

            // Paid state
            $order_state = $indexedOrderStates[$val['current_state']];
            if ($order_state) {
                if (strcmp($order_state['paid'], '0') == 0) {
                    $res[$key]['paid'] = false;
                } else {
                    $res[$key]['paid'] = true;
                }
            }
            $id_customer = $val['id_customer'];
            $res[$key]['customer_group_without_tax'] = (strcmp(Group::getPriceDisplayMethod(Customer::getDefaultGroupId($id_customer)), "1") == 0 ? true : false);
            $res[$key]['currency_code'] = $this->assignCurrencyCode($val['id_currency']);

            // Get customer
            $customer = new Customer($id_customer);
            if ($customer) {
                $res[$key]['customer']['displayName'] = $customer->firstname . ' ' . $customer->lastname;
                $res[$key]['customer']['email'] = $customer->email;
                $res[$key]['customer']['firstName'] = $customer->firstname;
                $res[$key]['customer']['lastName'] = $customer->lastname;
                $res[$key]['customer']['id'] = $customer->id;
            }


        }

        return $res;
    }

    public function assignCurrencyCode($id_currency)
    {
        $currency = Currency::getCurrency($id_currency);
        if ($currency) {
            return $currency['iso_code'];
        }

        return "";
    }
}