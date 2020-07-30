<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class UpdateNativeOrderService extends BaseService
{
    public function doExecute()
    {
        if ($this->isPutMethod()) {
            $requestData = json_decode($this->getRequestBody());
            $orderStatus = 0;
            switch($requestData->order_status) {
                case 'pending':
                case 'settling':
                    $orderStatus = Configuration::get('JM_NATIVE_PAYMENT_PENDING');
                    break;
                case 'complete':
                case 'paid':
                    $orderStatus = Configuration::get('PS_OS_PAYMENT');
                    break;
                case 'canceled':
                    $orderStatus = Configuration::get('PS_OS_CANCELED');
                    break;
                default:
                    $orderStatus = 0;
                    break;
            }
            $order = new Order((int)$requestData->order_id);

            if ($orderStatus) {
                $new_history = new OrderHistory();
                $new_history->id_order = (int)$order->id;
                $new_history->changeIdOrderState((int)$orderStatus, (int)$order->id, true);
                $new_history->addWithemail(true);
            }

            $this->response = new OrderResponse();
            $this->response->order = new Order((int)$requestData->order_id);
            $customer = new Customer($this->response->order->id_customer);
            $order_state = new OrderState($this->response->order->current_state);

            $payments = $this->response->order->getOrderPayments();
            if ($payments) {
                $payment_count = count($payments)-1;
                if ($requestData->payment_display_text) {
                    $this->updatePaymentLabel($requestData->order_id, $requestData->payment_display_text, $payments[$payment_count]->id);
                }

                if ($requestData->transaction_id){
                    $this->updateTransactionId($requestData->transaction_id, $payments[$payment_count]->id);
                }
            }

            $this->response->order->customer_first_name = $customer->firstname;
            $this->response->order->customer_last_name = $customer->lastname;
            $this->response->order->customer_email = $customer->email;
            $this->response->order->status_string = $order_state->name[$this->context->language->id];
        }
    }

    public function updatePaymentLabel($id_order, $payment_label, $id_order_payment){
        $sql = 'UPDATE '._DB_PREFIX_.'orders 
                SET payment = \''.$payment_label.'\'
                WHERE id_order = ' . $id_order;
        Db::getInstance()->execute($sql);

        $sql = 'UPDATE '._DB_PREFIX_.'order_payment 
                SET payment_method = \''.$payment_label.'\'
                WHERE id_order_payment = ' . $id_order_payment;
        Db::getInstance()->execute($sql);
    }

    public function updateTransactionId($transaction_id, $id_order_payment){
        $sql = 'UPDATE '._DB_PREFIX_.'order_payment 
                SET transaction_id = \''.$transaction_id.'\'
                WHERE id_order_payment = ' . $id_order_payment;
        Db::getInstance()->execute($sql);
    }
}