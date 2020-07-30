<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class SubmitNativeOrderService extends BaseService
{
    public function doExecute()
    {
        //Create new order
        if ($this->isPostMethod()) {
            $requestData = json_decode($this->getRequestBody());
            $orderStatus = 0;
            $this->response = new OrderResponse();

            switch($requestData->order_status) {
                case 'pending':
                case 'settling':
                default:
                    $orderStatus = Configuration::get('JM_NATIVE_PAYMENT_PENDING');
                    break;
                case 'complete':
                case 'paid':
                    $orderStatus = Configuration::get('PS_OS_PAYMENT');
                    break;
            }
            $this->context->cart = new Cart((int)$requestData->id_cart);
            if (!Validate::isLoadedObject($this->context->cart) || $this->context->cart->OrderExists() == true){
                $this->response->errors[] = new JmError(500, 'Cart cannot be loaded or an order has already been placed using this cart');
                return;
            }
            $nativePaymentModule = new JMNativePaymentMethod();
            $nativePaymentModule->active = true;

            if (!(int)$requestData->id_customer) {
                $id_customer = $this->context->cart->id_customer;
            } else {
                $id_customer = $requestData->id_customer;
            }
            $customer = new Customer($id_customer);
            $key = $customer->secure_key;
            $message = 'Cart Id: ' . (int)$requestData->id_cart;
            $message .= '|Transaction ID: ' . $requestData->transaction_id;
            $nativePaymentModule->validateOrder($requestData->id_cart, $orderStatus, $requestData->amount, $requestData->payment_display_text, $message, array(), null, false, $key, $this->context->shop);
            $this->response->order = new Order((int)Order::getOrderByCartId($requestData->id_cart));
            $customer = new Customer($this->response->order->id_customer);
            $order_state = new OrderState($this->response->order->current_state);
            $this->response->order->customer_first_name = $customer->firstname;
            $this->response->order->customer_last_name = $customer->lastname;
            $this->response->order->customer_email = $customer->email;
            $this->response->order->status_string = $order_state->name[$this->context->language->id];
        }
    }
}