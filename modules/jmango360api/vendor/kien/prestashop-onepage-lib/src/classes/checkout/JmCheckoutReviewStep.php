<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use Symfony\Component\Translation\TranslatorInterface;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class JmCheckoutReviewStep extends JmCheckoutStep
{
    protected $template;

    private $priceFormatter;

    protected $template_dir;

    protected $conditionsToApproveFinder;

    protected $presentCart;

    public $module_name;

    protected $paymentOptionsFinder;

    protected $addressForm;

    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        JmCheckoutSession $checkoutSession,
        ConditionsToApproveFinder $conditionsToApproveFinder,
        $presentCart = null,
        $module_name,
        PaymentOptionsFinder $paymentOptionsFinder,
        CustomerAddressForm $addressForm
    ) {
        $this->template = 'module:'.$module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/'.$module_name.'/onepage17_new/steps/step5-order-review.tpl';
        $this->template_dir = _PS_MODULE_DIR_ .$module_name. "/vendor/kien/prestashop-onepage-lib/src/views/templates/front/".$module_name;

        parent::__construct($context, $translator, $checkoutSession, $module_name);
        $this->conditionsToApproveFinder = $conditionsToApproveFinder;
        $this->presentCart = $presentCart;
        $this->paymentOptionsFinder = $paymentOptionsFinder;
        $this->addressForm = $addressForm;
        $this->context->smarty->assign(
            'cart_template_path',
            'module:'.$module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/'.$module_name.'/onepage17/steps/_partials/order-final-summary-table.tpl'
        );
        $this->context->smarty->assign('module_template_dir',$this->template_dir);
        $this->module_name = $module_name;
    }

    public function handleRequest(array $requestParams = array())
    {
    }

    public function render(array $extraParams = array())
    {
        $conditionsToApprove = $this->conditionsToApproveFinder->getConditionsToApproveForTemplate();
        $isFree = 0 == (float)$this->getCheckoutSession()->getCart()->getOrderTotal(true, Cart::BOTH);
        $paymentOptions = $this->paymentOptionsFinder->present($isFree);
        $address_delivery_form = $this->addressForm->loadAddressById($this->presentCart['id_address_delivery']);
        $address_delivery = $this->convertAddressForm($address_delivery_form);
        $address_invoice_form = $this->addressForm->loadAddressById($this->presentCart['id_address_invoice']);
        $this->convertAddressForm($address_delivery_form);
        $address_invoice = $this->convertAddressForm($address_invoice_form);
        if (array_key_exists("id_state",$address_delivery) && $address_delivery['id_state']){
            $address_delivery['state']=State::getNameById($address_delivery['id_state']);
        } else {
            $address_delivery['state']="";
        }
        if (array_key_exists("id_state",$address_invoice) && $address_invoice['id_state']){
            $address_invoice['state']=State::getNameById($address_invoice['id_state']);
        } else {
            $address_invoice['state']="";
        }
        $this->assignCartRules();
        $assignedVars = array(
            'discounts' => $this->context->cart->getCartRules(),
            'voucher_allowed' => CartRule::isFeatureActive(),
            'conditions_to_approve' => $conditionsToApprove,
            'cart' => $this->presentCart,
            'enable_coupon_onepage' => Configuration::get(EcommService::JM_COUPON_FOR_ONEPAGE),
            'module_name' => $this->module_name,
            'delivery_option' => $this->getCheckoutSession()->getDeliveryOptions() ? $this->getCheckoutSession()->getDeliveryOptions()[$this->getCheckoutSession()->getSelectedDeliveryOption()] : null,
            'payment_options' => $paymentOptions,
            'billing_address' => $address_invoice,
            'shipping_address' => $address_delivery,
            'is_logged' => (int)!$this->context->customer->is_guest
        );
        return $this->renderTemplate($this->getTemplate(), array($extraParams), $assignedVars);
    }

    public function convertAddressForm($addressForm){
        $r = new ReflectionObject($addressForm);
        $p = $r->getProperty('formFields');
        $p->setAccessible(true);
        $address = array();
        foreach( $p->getValue($addressForm) as $key => $value ){
            $address[$key] = $value->getValue();
        };
        $address['country'] = Country::getNameById($this->context->language->id, $address['id_country']);
        return $address;
    }

    public function assignCartRules() {
        $highlight_cart_rules = CartRule::getCustomerCartRules(
            $this->context->language->id,
            $this->context->cart->id_customer,
            $active = true,
            $includeGeneric = true,
            $inStock = true,
            $this->context->cart,
            $freeShippingOnly = false,
            $highlightOnly = true
        );

        $this->priceFormatter = new PriceFormatter();

        $added_cart_rules = $this->context->cart->getCartRules();
        $added_rules = array();
        foreach($added_cart_rules as &$added_rule) {
            $added_rules[] = (int)$added_rule['id_cart_rule'];
            if ((int)$added_rule['reduction_amount']){
                $added_rule['reduction_amount'] = $this->priceFormatter->format($added_rule['reduction_amount']);
            } else {
                $added_rule['reduction_amount'] = 0;
            }
        }
        $this->context->smarty->assign('added_cart_rules', $added_cart_rules);
        $cart_rule_suggestions = array();
        foreach($highlight_cart_rules as $highlight_cart_rule) {
            if ($highlight_cart_rule['code'] && !in_array((int)$highlight_cart_rule['id_cart_rule'], $added_rules)) {
                $cart_rule_suggestions[] = $highlight_cart_rule;
            }
        }
        $this->context->smarty->assign('suggested_cart_rules', $cart_rule_suggestions);
    }
}
