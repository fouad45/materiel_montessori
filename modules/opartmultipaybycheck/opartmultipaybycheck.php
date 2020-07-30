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


if (!defined('_PS_VERSION_')) {
    exit;
}

class Opartmultipaybycheck extends PaymentModule
{
    protected $config_form = false;
    protected $post_error = array();
    protected $post_conf = array();

    public function __construct()
    {
        $this->name = 'opartmultipaybycheck';
        $this->tab = 'payments_gateways';
        $this->version = '1.1.0';
        $this->author = 'Olivier CLEMENCE';
        $this->need_instance = 0;
        $this->module_key = 'ad89f8524291a27e61cfe7c60d76fe0a';
        $this->module_dir = _MODULE_DIR_.$this->name.'/';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Multiple payment by check');
        $this->description = $this->l('Add multiple payment by check method on your site');

        $this->confirmUninstall = $this->l('Are you sure you want to delete these module ?');
    }

    public function install()
    {
        Configuration::updateValue('OMPBC_MINIMUM', 200);
        Configuration::updateValue('OMPBC_NUMBER', 3);

        /** create new orderstate * */
        $new_state = new OrderState();
        $new_state->send_email = 1;
        $new_state->color = '#00efee';
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $new_state->template[$language['id_lang']] = 'ompbc_multiple_cheque';
            $new_state->name[$language['id_lang']] = 'En attente de paiement par chèque en X Fois';
        }
        $new_state->save();

        Configuration::updateValue('OMPBC_ORDER_STATUT', $new_state->id);
        $hookName = (version_compare(_PS_VERSION_, '1.7.0', '>='))?'paymentOptions':'Payment';

        return (parent::install() &&
            $this->registerHook($hookName) &&
            $this->registerHook('paymentReturn') &&
            $this->registerHook('displayProductPriceBlock')    &&
            $this->registerHook('actionGetExtraMailTemplateVars')    &&
            $this->installSql());
    }

    private function installSql()
    {
        $sql = array();

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opartmultipaybycheck_lang` (
            `id_opartmultipaybycheck` int(10) NOT NULL AUTO_INCREMENT,
            `id_lang` int(4),
            `text_confirmation` longtext,
            PRIMARY KEY (`id_opartmultipaybycheck`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opartmultipaybycheck_orders` (
            `id_order` int(11) NOT NULL,
            `number_payments` int(11) NOT NULL
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) == false) {
                return false;
            }
        }

        return true;
    }

    public function uninstall()
    {
        Configuration::deleteByName('OMPBC_MINIMUM');
        Configuration::deleteByName('OMPBC_NUMBER');
        Configuration::deleteByName('OMPBC_ORDER_STATUT');

        if ($this->uninstallSql()) {
            return parent::uninstall();
        } else {
            return false;
        }
    }

    private function uninstallSql()
    {
        $sql = array();
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'opartmultipaybycheck_lang`';
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'opartmultipaybycheck_orders`';

        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) == false) {
                return false;
            }
        }

        return true;
    }

    public function getContent()
    {
        $this->post_errors = array();
        $this->postProcess();
        $this->assignSmartyVar();

        $output = '';
        if (count($this->post_error) > 0) {
            foreach ($this->post_error as $err) {
                $output .= $this->displayError($err);
            }
        }

        if (count($this->post_conf) > 0) {
            foreach ($this->post_conf as $conf) {
                $output .= $this->displayConfirmation($conf);
            }
        }

        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    protected function postProcess()
    {
        if (tools::isSubmit('submitOpartmultipaybycheckModule')) {
            $languages = Language::getLanguages(false);
            $sql = 'DELETE FROM `'._DB_PREFIX_.'opartmultipaybycheck_lang`';

            if (!Db::getInstance()->execute($sql)) {
                die('erreur');
            }

            foreach ($languages as $language) {
                $text_confirmation = Tools::getValue('text_confirmation_'.$language['id_lang']);

                $sql = 'INSERT INTO `'._DB_PREFIX_.'opartmultipaybycheck_lang` (id_lang,text_confirmation)
                    VALUES ('.(int)$language['id_lang'].',"'.pSQL($text_confirmation, true).'")';

                if (!Db::getInstance()->execute($sql)) {
                    die('erreur '.$sql);
                }
            }

            $minimum_amount = tools::getValue('minimum_amount');
            if (!is_numeric($minimum_amount)) {
                $this->post_error[] = $this->l('Minimum order amount have to be a number');
            } else {
                Configuration::updateValue('OMPBC_MINIMUM', $minimum_amount);
            }

            $payment_number = tools::getValue('payment_number');
            if (!is_numeric($payment_number)) {
                $this->post_error[] = $this->l('Payment number have to be a number');
            } else {
                Configuration::updateValue('OMPBC_NUMBER', $payment_number);
            }

            $order_statut = tools::getValue('order_statut');

            Configuration::updateValue('OMPBC_ORDER_STATUT', $order_statut);
        }
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->multiple_fieldsets = true;
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitOpartmultipaybycheckModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $html = $this->context->smarty->fetch($this->local_path.'views/templates/admin/header.tpl');

        $html .= $helper->generateForm($this->getConfigForm());
        $html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/help.tpl');

        return $html;
    }

    protected function getFormValues()
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'opartmultipaybycheck_lang`';
        $rows = Db::getInstance()->executeS($sql);
        $result = array();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                foreach ($row as $key => $value) {
                    if ($key != 'id_opartmultipaybycheck' && $key != 'id_lang') {
                        $result[$key][$row['id_lang']] = $value;
                    }
                }
            }
        } else {
            foreach (Language::getLanguages(false) as $language) {
                $result['text_confirmation'][$language['id_lang']] = '';
            }
        }

        $result['minimum_amount'] = Configuration::get('OMPBC_MINIMUM');
        $result['payment_number'] = Configuration::get('OMPBC_NUMBER');
        $result['order_statut'] = Configuration::get('OMPBC_ORDER_STATUT');

        return $result;
    }

    protected function getConfigForm()
    {
        $array = array();
        $array[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'name' => 'payment_number',
                    'label' => $this->l('Payment number:'),
                ),
                array(
                    'type' => 'text',
                    'name' => 'minimum_amount',
                    'label' => $this->l('Minimum order amount'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Order statut'),
                    'name' => 'order_statut',
                    'col' => 8,
                    'options' => array(
                        'query' => $this->getSelectOrderStatut(),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Please read the documentation before change this setting'),
                ),
                array(
                    'type' => 'textarea',
                    'lang' => true,
                    'autoload_rte' => true,
                    'label' => $this->l('Confirmation page text:'),
                    'name' => 'text_confirmation',
                    'desc' => $this->l('Add here the informations that the customer will need to send you the payment. (Address, ...)'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        return $array;
    }

    private function getSelectOrderStatut()
    {
        $id_lang = Context::getContext()->language->id;

        $select_array = array();
        foreach (OrderState::getOrderStates($id_lang) as $value) {
            $select_array[] = array(
                'id' => $value['id_order_state'],
                'name' => $value['name']
            );
        }

        return $select_array;
    }

    private function assignSmartyVar()
    {
        $this->context->smarty->assign(array(
            'module_name' => $this->name,
            'module_dir' => $this->module_dir
        ));
    }

    private function prepareHook()
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'opartmultipaybycheck_lang` where id_lang='.(int)Context::getContext()->language->id;
        $row = Db::getInstance()->getRow($sql);
        $order_total = Context::getContext()->cart->getOrderTotal(true, Cart::BOTH);
        $this->smarty->assign(array(
            'order_total' => $order_total,
            'result' => $row,
            'minimum_amount' => Configuration::get('OMPBC_MINIMUM'),
            'number_payment' => Configuration::get('OMPBC_NUMBER'),
            'module_dir' => $this->module_dir
        ));

        $this->context->controller->addJS($this->_path.'views/js/front.js');
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        $order_total = Context::getContext()->cart->getOrderTotal(true, Cart::BOTH);
        if ($order_total<Configuration::get('OMPBC_MINIMUM')) {
            return;
        }

        $cart = $this->context->cart;
        $number_payment = Configuration::get('OMPBC_NUMBER');
        $this->smarty->assign(array(
            'total' => Tools::displayPrice($cart->getOrderTotal(true, Cart::BOTH)),
            'minimum_amount' => Configuration::get('OMPBC_MINIMUM'),
            'number_payment' => $number_payment,
            'use_taxes' => (int)Configuration::get('PS_TAX')
        ));

        $newOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption;
        $newOption->setModuleName($this->name)
            //->setCallToActionText($this->trans('Pay by Check in %d installment', array($number_payment), 'Opartmultipaybycheck'))
            ->setCallToActionText(sprintf($this->l('Pay by Check in %d installment'), $number_payment))
            ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
            ->setAdditionalInformation($this->fetch('module:opartmultipaybycheck/views/templates/hook/payment_informations.tpl'));

        return array($newOption);
    }

    public function hookPayment()
    {
        $this->prepareHook();
        if (version_compare(_PS_VERSION_, '1.6.0', '<')) {
            return $this->display(__FILE__, 'payment_15.tpl');
        } else {
            return $this->display(__FILE__, 'payment.tpl');
        }
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            $objOrder = $params['order'];
        } else {
            $objOrder = $params['objOrder'];
        }

        $currencyObj = new Currency($objOrder->id_currency);
        $total_to_pay = Tools::displayPrice($objOrder->getOrdersTotalPaid(), $currencyObj, false);
        $state = $objOrder->getCurrentState();
        $minimum_amount = Configuration::get('OMPBC_MINIMUM');

        if (
            in_array($state, array(
                Configuration::get('OMPBC_ORDER_STATUT'),
                Configuration::get('PS_OS_OUTOFSTOCK'),
                Configuration::get('PS_OS_OUTOFSTOCK_UNPAID')
            ))
            && $minimum_amount <= $total_to_pay
        ) {
            $id_lang = Context::getContext()->language->id;
            $sql = 'SELECT text_confirmation FROM '._DB_PREFIX_.'opartmultipaybycheck_lang WHERE id_lang='.(int)$id_lang;
            $text_confirmation = db::getInstance()->getValue($sql);

            /* calc check amount and number check */
            $number_check = Configuration::get('OMPBC_NUMBER');

            //Register Confiugration in database
            $this->registerConfiguration($objOrder->id, $number_check);

            $order_total = $objOrder->getOrdersTotalPaid();
            $check_amount = floor($order_total / $number_check);
            $number_check_minus_one = $number_check - 1;
            $pre_total = $check_amount * $number_check_minus_one;
            $last_check_amount = $order_total - $pre_total;

            $this->smarty->assign(array(
                'total_to_pay' => Tools::displayPrice($total_to_pay, $currencyObj, false),
                'status' => 'ok',
                'id_order' => $objOrder->id,
                'text_confirmation' => $text_confirmation,
                'number_check' => $number_check,
                'number_check_minus_one' => $number_check_minus_one,
                'check_amount' => Tools::displayPrice($check_amount, $currencyObj, false),
                'last_check_amount' => Tools::displayPrice($last_check_amount, $currencyObj, false),
            ));

            if (isset($objOrder->reference) && !empty($objOrder->reference)) {
                $this->smarty->assign('reference', $objOrder->reference);
            }
        } else {
            $this->smarty->assign('status', 'failed');
        }

        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            return $this->display(__FILE__, 'ps17/payment_return.tpl');
        } else {
            return $this->display(__FILE__, 'payment_return.tpl');
        }
    }

    public function registerConfiguration($id_order, $number)
    {
        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'opartmultipaybycheck_orders (`id_order` , `number_payments` )
            VALUES ( "' . $id_order . '","' . $number . '")'
        );
    }

    public function hookActionGetExtraMailTemplateVars($params)
    {
        $template_vars = $params['template_vars'];
        if (isset($template_vars['{id_order}']) && $template_vars['{id_order}'] > 0) {
            $order = new Order($template_vars['{id_order}']);
            $sql = 'SELECT * FROM '._DB_PREFIX_.'opartmultipaybycheck_orders WHERE id_order='.(int)$order->id;
            $result = db::getInstance()->getRow($sql);

            if ($result == false) {
                $number_check = Configuration::get('OMPBC_NUMBER');
            } else {
                $number_check = $result['number_payments'];
            }

            $id_lang = Context::getContext()->language->id;
            $sql = 'SELECT text_confirmation FROM '._DB_PREFIX_.'opartmultipaybycheck_lang WHERE id_lang='.(int)$id_lang;
            $text_confirmation = db::getInstance()->getValue($sql);

            $currency = $this->context->currency;
            $cart = new Cart($order->id_cart);
            $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
            $order_total = $total;
            $check_amount = floor($order_total / $number_check);
            $number_check_minus_one = $number_check - 1;
            $pre_total = $check_amount * $number_check_minus_one;
            $last_check_amount = $order_total - $pre_total;

            if ($last_check_amount == $check_amount) {
                $details = sprintf(
                    $this->l('%1$d checks for %2$s'),
                    $number_check,
                    Tools::displayPrice($check_amount, $currency, false)
                );
            } else {
                $details = sprintf(
                    $this->l('%1$d checks for %2$s and 1 check for %3$s'),
                    $number_check_minus_one,
                    Tools::displayPrice($check_amount, $currency, false),
                    Tools::displayPrice($last_check_amount, $currency, false)
                );
            }

            $params['extra_template_vars']['{number_check}'] = $number_check;
            $params['extra_template_vars']['{order_total}'] = Tools::displayPrice($order_total, $currency, false);
            $params['extra_template_vars']['{details}'] = $details;
            $params['extra_template_vars']['{pre_total}'] = $pre_total;
            $params['extra_template_vars']['{text_confirmation}'] = $text_confirmation;
        }
    }
}
