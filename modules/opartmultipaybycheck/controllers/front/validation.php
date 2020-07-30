<?php
/**
 * Module opartmultipaybycheck
 *
 * @category Prestashop
 * @category Module
 * @author    Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright Op'art
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

class OpartmultipaybycheckValidationModuleFrontController extends ModuleFrontController {

	public function postProcess()
	{
		$cart = $this->context->cart;

		if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
			Tools::redirect('index.php?controller=order&step=1');

		// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
			if ($module['name'] == 'opartmultipaybycheck')
			{
				$authorized = true;
				break;
			}

		if (!$authorized)
			die($this->module->l('This payment method is not available.', 'validation'));

		$customer = new Customer($cart->id_customer);

		if (!Validate::isLoadedObject($customer))
			Tools::redirect('index.php?controller=order&step=1');

		$currency = $this->context->currency;
		$total = (float)$cart->getOrderTotal(true, Cart::BOTH);

		$number_check = Configuration::get('OMPBC_NUMBER');
		$order_total = $total;
		$check_amount = floor($order_total / $number_check);
		$number_check_minus_one = $number_check - 1;
		$pre_total = $check_amount * $number_check_minus_one;
		$last_check_amount = $order_total - $pre_total;

		$id_lang = $cart->id_lang;
		$sql = 'SELECT text_confirmation FROM '._DB_PREFIX_.'opartmultipaybycheck_lang WHERE id_lang='.(int)$id_lang;
		$text_confirmation = db::getInstance()->getValue($sql);

		$mail_vars = array(
			'{number_check}' => $number_check,
			'{order_total}' => Tools::displayPrice($order_total, $currency, false),
			'{check_amount}' => Tools::displayPrice($check_amount, $currency, false),
			'{number_check_minus_one}' => $number_check_minus_one,
			'{pre_total}' => $pre_total,
			'{last_check_amount}' => Tools::displayPrice($last_check_amount, $currency, false),
			'{text_confirmation}' => $text_confirmation
		);

		$this->module->validateOrder((int)$cart->id, Configuration::get('OMPBC_ORDER_STATUT'), $total, $this->module->displayName, null, $mail_vars,
			(int)$currency->id, false, $customer->secure_key);

		Tools::redirect('index.php?controller=order-confirmation&id_cart='.
			(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
	}

}
