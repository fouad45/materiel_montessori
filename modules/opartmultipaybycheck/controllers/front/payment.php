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

class OpartmultipaybycheckPaymentModuleFrontController extends ModuleFrontController {

	public $ssl = true;
	public $display_column_left = false;

	public function initContent()
	{
		parent::initContent();

		$cart = $this->context->cart;

		$this->context->smarty->assign(array(
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => $this->module->getCurrency((int)$cart->id_currency),
			'total' => $cart->getOrderTotal(true, Cart::BOTH),
			'number_payment' => Configuration::get('OMPBC_NUMBER')
		));

		if (version_compare(_PS_VERSION_, '1.6.0', '<'))
			$this->setTemplate('payment_execution_15.tpl');
		else
			$this->setTemplate('payment_execution.tpl');
	}

}
