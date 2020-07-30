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

use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;

class Version17CartAdapter extends BaseAdapter
{

    public function getResponseData()
    {
        $cart = $this->context->cart;
        $cart_data = (new CartPresenter)->present($cart);
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
            'FValue' => $cart_data['totals']['total']['value'],
            'Value' => $this->context->cart->getOrderTotal($useTax)
        );
        $cart_data['freeShippingFloat'] = 0;
        $cart_data['shippingCostFloat'] = 0;

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
        $touchize_cart['ItemsQty'] = $cart_data['products_count'];

        foreach ($cart_data['products'] as $product) {
            $vid = $pid = $product['id_product'];

            if (0 != $product['id_product_attribute']) {
                $pid .= '-'.$product['id_product_attribute'];
                $vid = $product['id_product_attribute'];
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

        $itemPrice = $product['price_amount']? $product['price_amount'] : null;

        //product.cover.bySize.cart_default.url as in cart-detailed-product-line.tpl
        $cartDef =  ImageType::getFormattedName('cart');
        if (isset($product['cover']) &&
            isset($product['cover']['bySize']) &&
            isset($product['cover']['bySize'][$cartDef]) &&
            isset($product['cover']['bySize'][$cartDef]['url'])) {
            $image = $product['cover']['bySize'][$cartDef]['url'];
        } else {
            $image = $this->helper->getDefaultPlaceholder();
        }

        return array(
            'Id' => $pid,
            'Title' => $product['name'],
            'Qty' => $product['quantity'],
            'FSubTotal' => $itemPrice,
            'ProductVariant' => array(
                'Id' => $vid, # variant id
                'ProductId' => $product['id_product'], # product id
                'Images' => array(
                    array(
                        'Name' => $image
                    ),
                ),
                'Attributes' => array_key_exists('attributes', $product)
                    ? $product['attributes']
                    : null,
                'Product' => array(
                    'Id' => $product['id_product'],
                    'Title' => $product['name'],
                ),
            ),
        );
    }
}
