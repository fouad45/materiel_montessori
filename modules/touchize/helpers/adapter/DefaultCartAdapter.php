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

class DefaultCartAdapter extends BaseAdapter
{

    /**
     * @return array
     */
    public function getResponseData()
    {
        $block_cart = Module::getInstanceByName('blockcart');
        $cart_data = json_decode(
            $block_cart->hookAjaxCall(array(
                'cookie' => $this->context->cookie,
                'cart' => $this->context->cart,
            )),
            true
        );

        $prepared_data = $this->getPreparedData($cart_data);
        return $prepared_data;
    }

    /**
     * @param $cart_data
     */
    public function getPreparedData($cart_data)
    {
        $touchize_cart = array();
        $tax_calculation_method = Group::getPriceDisplayMethod((int)Group::getCurrent()->id);
        $useTax = !($tax_calculation_method == PS_TAX_EXC);
        $touchize_cart['GrandTotal'] = array(
            'Title' => $this->l('Total'),
            'FValue' => $cart_data['total'],
            'Value' => $this->context->cart->getOrderTotal($useTax)
        );

        if (0 != $cart_data['shippingCostFloat']) {
            $touchize_cart['Shipping'] = array(
                'Title' => $this->l('Shipping'),
                'FValue' => $this->l($cart_data['shippingCost']),
            );
        } elseif (0 == $cart_data['freeShippingFloat']) {
            $touchize_cart['Shipping'] = array(
                'Title' => $this->l('Shipping'),
                'FValue' =>  $this->l('Free shipping'),
            );
        }

        $cart_items = array();
        $touchize_cart['ItemsCount'] = 0;
        $touchize_cart['ItemsQty'] = $cart_data['nbTotalProducts'];

        foreach ($cart_data['products'] as $product) {
            $pid = $product['id'];
            $vid = $product['id'];
            if (0 != $product['idCombination']) {
                $pid .= '-'.$product['idCombination'];
                $vid = $product['idCombination'];
            }

            $cart_items[] = $this->mapCartItem($product, $pid, $vid);
            $touchize_cart['ItemsCount']++;
        }

        $touchize_cart['items'] = $cart_items;

        return $touchize_cart;
    }

    /**
     * Map cart item to Touchize Model
     */
    protected function mapCartItem($product, $pid, $vid)
    {
        # Do not show price if 0
        $itemPrice = (0 != $product['price_float'])
            ? $product['priceByLine']
            : null;

        return array(
            'Id' => $pid,
            'Title' => $product['name'],
            'Qty' => $product['quantity'],
            'FSubTotal' => $itemPrice,
            'ProductVariant' => array(
                'Id' => $vid, # variant id
                'ProductId' => $product['id'], # product id
                'Images' => array(
                    array(
                        # 'Name' => $product['image_cart'] # to small
                        'Name' => $product['image'],
                        'Alt' => $product['name']
                    ),
                ),
                'Attributes' => array_key_exists('attributes', $product)
                    ? explode(',', $product['attributes'])
                    : null,
                'Product' => array(
                    'Id' => $product['id'],
                    'Title' => $product['name'],
                ),
            ),
        );
    }
}
