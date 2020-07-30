<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class GetOrderDetailsService extends BaseService
{
    public function doExecute()
    {
        if ($this->isGetMethod()) {
            $this->response = new OrderResponse();
            $order_id = $this->getRequestResourceId();
            $language_id = $this->context->language->id;
            $order = new Order($order_id, $language_id);

            if (!$order || !$order->id) {
                $orders = Order::getByReference($order_id);
                $order = $orders[0];
            }

            if ($order && $order->id) {
                $this->response = new CustomerOrderDetailsResponse();
                $products = $order->getCartProducts();
                $jmProducts = array();

                if ($products && count($products) > 0) {
                    foreach ($products as $p) {
                        //get product id with key = product_id
                        $product_id = $p['product_id'];

                        //load product core
                        $product_core = new Product(
                            $product_id,
                            true,
                            $language_id,
                            $this->context->shop->id,
                            $this->context
                        );

                        if ($product_core && $product_core->id) {
                            //get product url by product core
                            $productUrl = $this->context->link->getProductLink($product_core);
                            $jm_product_detail = new JmProductDetail();
                            $jm_product_detail->id_product = $product_core->id;
                            //convert to JmProduct object
                            $jmProduct = ProductDataTransform::productDetails($product_core, $jm_product_detail);

                            //add  product url for sharing on mobile
                            $jmProduct->product_url = $productUrl;
                            $jmProduct->quantity = $p['cart_quantity'];
                            $jmProduct->unit_price_tax_incl = $p['unit_price_tax_incl'];
                            $jmProduct->unit_price_tax_excl = $p['unit_price_tax_excl'];
                            $jmProduct->selectedAttributes = $p['product_attribute_id'] != '0' ? $this->assignAttribute($product_core, $language_id, $p['product_attribute_id']) : null;
                            $jmProduct->id_product_attribute = $p['product_attribute_id'];
                            array_push($jmProducts, $jmProduct);
                        }
                    }
                }
                //dynamic add "products" to response
                $order->products = $jmProducts;
                $this->response->order = $order;
            }
        }
    }

    public function assignAttribute($product_core, $language_id, $product_attribute_id)
    {
        $attributes = array();
        $attrs = $product_core->getAttributeCombinationsById($product_attribute_id, $language_id);
        foreach ($attrs as $attr) {
            $attributes[] = array('key' => $attr['group_name'], 'value' => $attr['attribute_name']);
        }
        return $attributes;
    }
}