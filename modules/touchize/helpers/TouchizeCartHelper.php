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
 * Cart helper.
 */

class TouchizeCartHelper extends TouchizeBaseHelper
{
    public $context;

    /**
     * [__construct description]
     */
    public function __construct()
    {
        parent::__construct();

        // if (!$this->context->cart->id) {
        //     $this->context->cart->add();
        //     if ($this->context->cart->id) {
        //         $this->context->cookie->id_cart = (int)$this->context->cart->id;
        //     }
        // }
    }

    /**
     * Add a product to the cart
     *
     * @param  string $productId            The product id
     * @param  string $idProductAttribute   The variation id
     * @param  string $qty                  The quantity to add
     * @param  string $sku                  The SKU
     *
     * @return array SLQ Cart | error
     */
    public function add(
        $productId,
        $idProductAttribute,
        $qty,
        $sku,
        $token
    ) {
        if ($productId === $idProductAttribute) {
            $idProductAttribute = null;
        }
        if ($sku != null && $idProductAttribute != null) {
            $idProductAttribute = $sku;
        }

        # Run using the default platform CartController
        $cartController = Controller::getController('CartController');

        # Write some POST stuff and states to be able to use the cart controller
        $cartController->ajax = true;
        $_POST['ajax'] = true;
        $_POST['add'] = true;
        $_POST['id_product'] = $productId;
        $_POST['id_product_attribute'] = $idProductAttribute;
        $_POST['qty'] = $qty;
        $_POST['token'] = $token;

        # Run the Controller
        $cartController->init();

        //We need a special check since PS after 1.7.3 allows out of stock being added anyway, then asking to correct the amount
        if (version_compare(_PS_VERSION_, '1.7.3.0', '>=') &&
            $this->hasQuantity17($productId, $idProductAttribute, $qty)) {
                $errors = array(
                    0 => $this->l('There isn\'t enough product in stock.')
                );
        } else {
            $cartController->postProcess();
            $errors = $cartController->errors;
        }

        if ($errors) {
            //We have error, use message from error
            $result = array(
                'Status' => '400',
                'Message' => $this->l($errors[0]),
                'Result' => ''
            );

            $oosView = $this->getProductOutOfStockNotification(
                $productId,
                $idProductAttribute
            );
            if ($oosView != null) {
                $result['View'] = $oosView;
            }

            return $result;
        }

        return $this->getCartAdapterData();
    }

    private function hasQuantity17($pid, $vid, $qtyToCheck)
    {
        $product = new Product($pid, true, $this->context->language->id);
        if ($vid) {
            return !Product::isAvailableWhenOutOfStock($product->out_of_stock)
                && !Attribute::checkAttributeQty($vid, $qtyToCheck);
        } elseif (Product::isAvailableWhenOutOfStock($product->out_of_stock)) {
            return false;
        }

        // product quantity is the available quantity after decreasing products in cart
        $productQuantity = Product::getQuantity(
            $pid,
            $vid,
            null,
            $this->context->cart,
            null
        );

        return $productQuantity < $qtyToCheck;
    }

    /**
     * Remove a cart item based on cart_item id.
     * Since prestashop does not use cartitems id they are created as:
     * $productId .'-'. $idProductAttribute
     *
     * @param  string $cartitemId   Cartitem id
     * @param  string $qty          The quantity to remove
     * @param  string $qty          The total quantity of cart item
     *
     * @return array                SLQ Cart
     */
    public function remove($cartitemId, $qty, $qtyincart, $token)
    {
        $ids = explode('-', $cartitemId);

        if (!array_key_exists(1, $ids)) {
            $ids[1] = null;
        }

        # Run using the default platform CartController
        $cartController = Controller::getController('CartController');

        # Write some POST stuff and states to be able to
        # use the cart controller
        $cartController->ajax = true;
        $_POST['ajax'] = true;
        $_POST['id_product'] = $ids[0];
        $_POST['id_product_attribute'] = $ids[1];
        $_POST['qty'] = $qty;
        $_POST['token'] = $token;


        if ($qty != $qtyincart) { # Decrease using add
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                $_POST['update'] = true;
            } else {
                $_POST['add'] = true;
            }
            $_POST['op'] = 'down';
        } else { # Final item, remove using delete
            $_POST['delete'] = true;
        }

        # Run the Controller
        $cartController->init();
        $cartController->postProcess();

        if ($cartController->errors) {
            $result = array(
                'Status' => '400',
                'Message' => $this->l($cartController->errors[0]),
                'Result' => '',
            );

            return $result;
        }

        return $this->getCartAdapterData();
    }

    /**
     * Get the cart as a Touchize model
     * @param string $serviceName
     *
     * @return array SLQ Cart
     */
    public function getCartAdapterData()
    {
        $touchize_cart_data  = $this->getCartAdapter()->getResponseData();

        # Needed before PS 1.6.1.7
        # $cart_sorted_items = $this->sortCartByAdd($touchize_cart_data['items']);

        $cart_sorted_items = $this->getCartSortOrder($touchize_cart_data['items']);
        //Last added on top
        $touchize_cart_data['Items'] = array_reverse($cart_sorted_items);

        return $touchize_cart_data;
    }

    /**
     * Sorting of cart items before 1.6.1.7,
     * since classes/Cart had ORDER BY name before
     *
     * @param  array $cartItems
     *
     * @return array
     */
    private function sortCartByAdd($cartItems)
    {
        # Don't need if version equal or larger than 1.6.1.7
        # Don't need if nothing in cart
        if (true === Tools::version_compare(_PS_VERSION_, '1.6.1.7', '>=') ||
            empty($cartItems)
        ) {
            return $cartItems;
        }

        # Only need ids sorted as ORDER BY in newer classes/Cart
        $sql = 'SELECT `id_product`, `id_product_attribute`
                FROM `'._DB_PREFIX_.'cart_product`
                WHERE `id_cart` = \''.pSQL((int)$this->context->cart->id).'\'
                ORDER BY `date_add`, `id_product`,`id_product_attribute` ASC';

        $result = Db::getInstance()->executeS($sql);

        if ($result) {
            # Create an associative array first (faster)
            $cartAss = array();
            foreach ($cartItems as $item) {
                $cartAss[$item['Id']] = $item;
            }

            # Created sorted array based on order of the ids
            $sortedCartItems= array();
            foreach ($result as $row) {
                $prodAttr = 0 != $row['id_product_attribute']
                    ? '-'.$row['id_product_attribute']
                    : '';

                $cartId = ''.$row['id_product'].$prodAttr;
                $sortedCartItems[] = $cartAss[$cartId];
            }

            return $sortedCartItems;
        }

        return $cartItems;
    }

    /**
     * [getProductOutOfStockNotification description]
     *
     * @param  mixed $idProduct
     * @param  mixed $idProductAttribute
     *
     * @return array
     */
    private function getProductOutOfStockNotification(
        $idProduct,
        $idProductAttribute
    ) {
        if (!Module::isInstalled('mailalerts') ||
            !Module::isEnabled('mailalerts') ||
            !file_exists(_PS_MODULE_DIR_ .'mailalerts/MailAlert.php')
        ) {
            return null;
        }

        require_once _PS_MODULE_DIR_.'mailalerts/MailAlert.php';

        if (!(int)Configuration::get('MA_CUSTOMER_QTY') ||
            !Configuration::get('PS_STOCK_MANAGEMENT')
        ) {
            return null;
        }


        $context = Context::getContext();
        $idCustomer = (int)$context->customer->id;

        if (MailAlert::customerHasNotification(
            $idCustomer,
            $idProduct,
            $idProductAttribute,
            (int)$context->shop->id
        )) {
            return;
        }

        $notifyText =  $this->l('Notify me when available');

        $children = array();

        $children[] = array(
            'input' => array(
                'type' => 'email',
                'name' => 'customer_email',
                'placeholder' => $this->l('Email'),
                'class' => 'customer-email',
            ),
        );

        $children[] = array(
            'input' => array(
                'type' => 'hidden',
                'name' => 'id_product',
                'value' => $this->l(''.$idProduct),
            ),
        );

        $children[] = array(
            'input' => array(
                'type' => 'hidden',
                'name' => 'id_product_attribute',
                'value' => $this->l(''.$idProductAttribute),
            ),
        );

        $children[] = array(
            'input' => array(
                'type' => 'button',
                'name' => 'submit-button',
                'class' => 'submit-button',
                'value' => $this->l($notifyText),
            ),
        );

        $view = array(
            'form' => array(
                'id' => 'oos-notification-form',
                'children' => $children,
            ),
        );

        return $view;
    }

    /**
     * [getCartSortOrder description]
     *
     * @param  array $cartItems
     *
     * @return array
     */
    protected function getCartSortOrder($cartItems)
    {
        # Get old sort order from session
        $cartSortOrder = json_decode($this->context->cookie->slqcartorder);

        $sortedCartItems = null;
        if ($cartSortOrder &&
            $cartItems &&
            count($cartItems) > 1
        ) {
            # Create an associative array first (faster)
            $cartAss = array();
            foreach ($cartItems as $item) {
                $cartAss[$item['Id']] = $item;
            }

            # Created sorted array based on order of the ids
            $sortedCartItems = array();
            foreach ($cartSortOrder as $cartId) {
                if (array_key_exists($cartId, $cartAss)) {
                    $sortedCartItems[] = $cartAss[$cartId];
                    unset($cartAss[$cartId]);
                }
            }

            foreach ($cartAss as $item) {
                $sortedCartItems[] = $item;
            }
        } else {
            $sortedCartItems = $cartItems;
        }

        # Store the sorting
        $this->putCartSortOrder($sortedCartItems);

        return $sortedCartItems;
    }

    /**
     * [putCartSortOrder description]
     *
     * @param  array $cartItems
     */
    protected function putCartSortOrder($cartItems)
    {
        $cartSortOrder = array();
        if ($cartItems) {
            foreach ($cartItems as $item) {
                $cartSortOrder[] = $item['Id'];
            }
        }

        # Store the sort order in session
        $this->context->cookie->__set(
            'slqcartorder',
            json_encode($cartSortOrder)
        );
    }

    /**
     * @return DefaultCartAdapter|Version17CartAdapter
     */
    protected function getCartAdapter()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            return new Version17CartAdapter();
        }

        return new DefaultCartAdapter();
    }
}
