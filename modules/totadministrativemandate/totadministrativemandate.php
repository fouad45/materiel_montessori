<?php
/**
 * 2007-2017 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

//Can't declare namespace, throws a parse error in Module::getModulesOnDisk when calling eval() (PS1.6)
//use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class TotAdministrativeMandate extends PaymentModule
{
    /**
     * Warning message.
     *
     * @var string
     */
    public $warning;

    /**
     * Configuration.
     *
     * @var array
     */
    public $conf;

    /**
     * Constructor of module.
     */

    private $recommended = array();




    public function __construct()
    {
        $this->name = 'totadministrativemandate'; // Name module
        $this->tab = 'payments_gateways'; // Tab module
        $this->version = '1.7.0'; // Version of module 1.7.0
        $this->author = '202-ecommerce'; // Author module
        $this->module_key = '463e276472f1cbbc0301fffcd8f8b663';
        $this->bootstrap = 'true';

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        parent::__construct(); // Parent constructor

        $this->displayName = $this->l('Administrative mandate'); // Translation display name
        $this->description = $this->l('Payment by Administrative mandate'); // Translation description


        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            include _PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php';
        }


        $this->recommended = array(
            array(
                'short_name' => 'totshowmailalerts',
                'installed' => false,
                'name' => $this->l('Product out of stock'),
                'descr' => $this->l('Discover the #1 solution that allows you to learn which out-of-stock products are your clients most interested in.'),
                'link' => 'https://addons.prestashop.com/en/6320-product-out-of-stock-emails-and-number-of-requests.html'
            ),
            array(
                'short_name' => 'totredirectionmanagerpro',
                'installed' => false,
                'name' => $this->l('Redirection 404 Manager pro'),
                'descr' => $this->l('The best solution for enhancing your SEO and website traffic. Manage your 404 pages and provide personnalized and relevant redirections.'),
                'link' => 'https://addons.prestashop.com/en/url-redirects/20901-redirect-404-manager-pro-show-and-redirect-301-302.html'
            ),
            array(
                'short_name' => 'totpaymentreminder',
                'installed' => false,
                'name' => $this->l('Payment reminder'),
                'descr' => $this->l('This module is the best solution for offline payment logistics. Payment Reminder will allow you to finalize more payments from your customers and improve the finalization rate of offline payments without accumulating open orders.'),
                'link' => 'https://addons.prestashop.com/en/6614-payment-reminder.html'
            ),
            array(
                'short_name' => 'totshippingpreview',
                'installed' => false,
                'name' => $this->l('Shipping preview'),
                'descr' => $this->l('This is the best solution for offering your customers a preview of their shipping costs! This Addon gives you the ability to add a button on your product page that displays the estimated shipping.'),
                'link' => 'https://addons.prestashop.com/en/8222-shipping-preview.html'
            )
        );

        // Get conf
        $keys = array(
            Tools::strtoupper($this->name).'_MESSAGE',
            Tools::strtoupper($this->name).'_OWNER',
            Tools::strtoupper($this->name).'_ADDRESS',
            Tools::strtoupper($this->name).'_DETAILS',
            Tools::strtoupper($this->name).'_WAIT',
            Tools::strtoupper($this->name).'_DONE',
        );
        $this->conf = Configuration::getMultiple($keys);

        $this->warning = array();
        if (!sizeof(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning[] = $this->l('No currency set for this module');
        }

        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $link = 'controller='.Tools::getValue('controller');
        } else {
            $link = 'tab='.Tools::getValue('tab');
        }

        $this->link = 'index.php?'.$link.'&token='.Tools::getValue('token');
        $this->link .= '&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;

        if (Module::isInstalled($this->name)) {
            $this->upgrade();

            if (!$this->conf[Tools::strtoupper($this->name).'_WAIT']
                || !$this->conf[Tools::strtoupper($this->name).'_DONE']) {
                $this->assign();
            }
        }
    }

    /**
     * Installing the module.
     *
     * @return bool
     */
    public function install()
    {
        // Set settings configuration
        Configuration::updateValue(Tools::strtoupper($this->name.'_owner'), '');
        Configuration::updateValue(Tools::strtoupper($this->name.'_details'), '');
        Configuration::updateValue(Tools::strtoupper($this->name.'_address'), '');
        Configuration::updateValue(Tools::strtoupper($this->name.'_co_name'), Configuration::get('PS_SHOP_NAME'));

        Configuration::updateValue(
            Tools::strtoupper($this->name.'_co_addr'),
            Configuration::get('PS_SHOP_ADDR1')."\n".
            Configuration::get('PS_SHOP_ADDR2')."\n".
            Configuration::get('PS_SHOP_CODE').' - '.Configuration::get('PS_SHOP_CITY')
        );

        Configuration::updateValue(Tools::strtoupper($this->name.'_phone'), Configuration::get('PS_SHOP_PHONE'));
        Configuration::updateValue(Tools::strtoupper($this->name.'_fax'), Configuration::get('PS_SHOP_FAX'));
        Configuration::updateValue(Tools::strtoupper($this->name.'_mail'), Configuration::get('PS_SHOP_EMAIL'));

        if (parent::install() == false
            || !$this->registerHook('adminOrder')
            || !$this->registerHook('payment')
            || !$this->registerHook('paymentReturn')
        ) {
            return false;
        }

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            if (!$this->registerHook('paymentOptions')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create order state.
     *
     * @return bool
     */
    private function assign()
    {

        // FIRST OS
        //Check if order state already a
        $os_wait_id = false;
        $os_wait = false;
        //Get id
        $os_wait_key = Tools::strtoupper($this->name.'_wait');
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $os_wait_id = Configuration::getGlobalValue($os_wait_key);
        } else {
            $os_wait_id = Configuration::get($os_wait_key);
        }
        //Get order state object
        if (!empty($os_wait_id)) {
            $os_wait = new OrderState($os_wait_id);
        }

        //If no id or no object, create new
        if (empty($os_wait_id) || !Validate::isLoadedObject($os_wait)) {
            $os_wait = new OrderState();
            $os_wait->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $os_wait->name[$language['id_lang']] = 'En attente de reception du mandat';
                } else {
                    $os_wait->name[$language['id_lang']] = 'Pending receipt of mandate';
                }
            }
        }

        $os_wait->send_mail = false;
        $os_wait->color = '#868F98';
        $os_wait->hidden = false;
        $os_wait->delivery = false;
        $os_wait->logable = false;
        $os_wait->invoice = false;
        $os_wait->module_name = $this->name;
        if (!$os_wait->save()) {
            return false;
        }

        Configuration::updatevalue($os_wait_key, $os_wait->id);

        $this->deleteOrderStateImage($os_wait->id);

        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $file = 'os/'.$os_wait->id.'.gif';
        } else {
            $file = 'tmp/order_state_mini_'.$os_wait->id.'.gif';
        }

        copy(dirname(__FILE__).'/views/img/wait.gif', _PS_IMG_DIR_.$file);

        // Second OS
        //Check if order state already exists
        $os_done_id = false;
        $os_done = false;
        //Get id
        $os_done_key = Tools::strtoupper($this->name.'_DONE');
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $os_done_id = Configuration::getGlobalValue($os_done_key);
        } else {
            $os_done_id = Configuration::get($os_done_key);
        }
        //Get order state object
        if (!empty($os_done_id)) {
            $os_done = new OrderState($os_done_id);
        }

        //If no id or no object, create new
        if (empty($os_done_id) || !Validate::isLoadedObject($os_done)) {
            $os_done = new OrderState();
            $os_done->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $os_done->name[$language['id_lang']] = 'Mandat recu';
                } else {
                    $os_done->name[$language['id_lang']] = 'Mandate received';
                }
            }
        }

        $os_done->send_mail = false;
        $os_done->color = '#669900';
        $os_done->hidden = false;
        $os_done->delivery = false;
        $os_done->logable = true;
        $os_done->invoice = true;
        $os_done->module_name = $this->name;
        if (!$os_done->save()) {
            return false;
        }

        Configuration::updateValue($os_done_key, $os_done->id);

        $this->deleteOrderStateImage($os_done->id);

        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $file = 'os/'.$os_done->id.'.gif';
        } else {
            $file = 'tmp/order_state_mini_'.$os_done->id.'.gif';
        }

        copy(dirname(__FILE__).'/views/img/done.gif', _PS_IMG_DIR_.$file);

        return true;
    }

    private function deleteOrderStateImage($id_order_state)
    {
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            if (version_compare(_PS_VERSION_, '1.5.6.3', '<') && !defined('Shop::getContextListShopID')) {
                if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                    $id_shops = array(Shop::getContextShopID());
                } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
                    $id_shops = Shop::getShops(true, Shop::getContextShopGroupID(), true);
                } else {
                    $id_shops = Shop::getShops(true, null, true);
                }
            } else {
                $id_shops = Shop::getContextListShopID();
            }

            foreach ($id_shops as $id_shop) {
                $suffixe = '_'.$id_shop;

                if (file_exists(_PS_IMG_DIR_.'tmp/order_state_mini_'.$id_order_state.$suffixe.'.gif')) {
                    unlink(_PS_IMG_DIR_.'tmp/order_state_mini_'.$id_order_state.$suffixe.'.gif');
                }
            }
        } elseif (file_exists(_PS_IMG_DIR_.'tmp/order_state_mini_'.$id_order_state.'.gif')) {
            unlink(_PS_IMG_DIR_.'tmp/order_state_mini_'.$id_order_state.'.gif');
        }

        if (file_exists(_PS_IMG_DIR_.'os/'.$id_order_state.'.gif')) {
            unlink(_PS_IMG_DIR_.'os/'.$id_order_state.'.gif');
        }
    }

    /**
     * Upgrade.
     *
     * @return bool
     */
    private function upgrade()
    {
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $version = Configuration::getGlobalValue(Tools::strtoupper($this->name.'_version'));
        } else {
            $version = Configuration::get(Tools::strtoupper($this->name.'_version'));
        }

        if ($version == false) {
            //######################################################################
            //# FIRST OS
            //######################################################################
            //Get id
            $os_wait_id = false;
            if (version_compare(_PS_VERSION_, '1.5', '>')) {
                $os_wait_id = Configuration::getGlobalValue(Tools::strtoupper($this->name.'_wait'));
            } else {
                $os_wait_id = Configuration::get(Tools::strtoupper($this->name.'_wait'));
            }
            //If order state exists
            if ($os_wait_id) {
                //Update values
                $os_wait = new OrderState($os_wait_id);
                $os_wait->send_email = false;
                $os_wait->color = '#868F98';
                $os_wait->hidden = false;
                $os_wait->delivery = false;
                $os_wait->logable = true;
                $os_wait->invoice = true;
                $os_wait->module_name = $this->name;

                $os_wait->name = array();
                foreach (Language::getLanguages() as $language) {
                    if (Tools::strtolower($language['iso_code']) == 'fr') {
                        $os_wait->name[$language['id_lang']] = 'En attente de reception du mandat';
                    } else {
                        $os_wait->name[$language['id_lang']] = 'Pending receipt of mandate';
                    }
                }

                if (!$os_wait->save()) {
                    $this->warning[] = $this->l('Could not update order state : "Pending receipt of mandate"');

                    return false;
                }
            } else {
                $this->warning[] = $this->l('Could not find order state : "Pending receipt of mandate"');

                return false;
            }

            //######################################################################
            //# SECOND OS
            //######################################################################
            //Get id
            if (version_compare(_PS_VERSION_, '1.5', '>')) {
                $os_done_id = Configuration::getGlobalValue(Tools::strtoupper($this->name.'_DONE'));
            } else {
                $os_done_id = Configuration::get(Tools::strtoupper($this->name.'_DONE'));
            }
            //If order state exists
            if ($os_done_id) {
                //Update values
                $os_done = new OrderState($os_done_id);
                $os_done->send_email = false;
                $os_done->color = '#868F98';
                $os_done->hidden = false;
                $os_done->delivery = false;
                $os_done->logable = true;
                $os_done->invoice = true;
                $os_done->module_name = $this->name;

                $os_done->name = array();
                foreach (Language::getLanguages() as $language) {
                    if (Tools::strtolower($language['iso_code']) == 'fr') {
                        $os_done->name[$language['id_lang']] = 'Mandat recu';
                    } else {
                        $os_done->name[$language['id_lang']] = 'Mandate received';
                    }
                }

                if (!$os_done->save()) {
                    $this->warning[] = $this->l('Could not update order state : "Mandate received"');

                    return false;
                }
            } else {
                $this->warning[] = $this->l('Could not find order state : "Mandate received"');

                return false;
            }
        }

        if ($version == false || version_compare($version, '1.3', '<')) {
            Configuration::updateValue(Tools::strtoupper($this->name.'_co_name'), Configuration::get('PS_SHOP_NAME'));

            Configuration::updateValue(
                Tools::strtoupper($this->name.'_co_addr'),
                Configuration::get('PS_SHOP_ADDR1')."\n".
                Configuration::get('PS_SHOP_ADDR2')."\n".
                Configuration::get('PS_SHOP_CODE').' - '.Configuration::get('PS_SHOP_CITY')
            );

            Configuration::updateValue(Tools::strtoupper($this->name.'_phone'), Configuration::get('PS_SHOP_PHONE'));
            Configuration::updateValue(Tools::strtoupper($this->name.'_fax'), Configuration::get('PS_SHOP_FAX'));
            Configuration::updateValue(Tools::strtoupper($this->name.'_mail'), Configuration::get('PS_SHOP_EMAIL'));
            // Reload configuration after update value
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                Configuration::loadConfiguration();
            }
        }

        if ($version == false || version_compare($version, '1.4.3', '<')) {
            $OsId = configuration::get(Tools::strtoupper($this->name).'_WAIT');

            if (version_compare(_PS_VERSION_, '1.5', '>')) {
                $file = 'os/'.$OsId.'.gif';
            } else {
                $file = 'tmp/order_state_mini_'.$OsId.'.gif';
            }

            @copy(dirname(__FILE__).'/wait.gif', _PS_IMG_DIR_.$file);

            $OsId = configuration::get(Tools::strtoupper($this->name).'_DONE');

            if (version_compare(_PS_VERSION_, '1.5', '>')) {
                $file = 'os/'.$OsId.'.gif';
            } else {
                $file = 'tmp/order_state_mini_'.$OsId.'.gif';
            }

            @copy(dirname(__FILE__).'/done.gif', _PS_IMG_DIR_.$file);
        }

        if ($version == false || version_compare($version, '1.4.5', '<')) {
            $this->registerHook('header');
        }

        if (($version == false || version_compare($version, '1.6.1', '<')) && version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->registerHook('paymentOptions');
        }

        // Update version in confguration
        if ($version != $this->version) {
            if (version_compare(_PS_VERSION_, '1.5', '>')) {
                Configuration::updateGlobalValue(Tools::strtoupper($this->name.'_version'), $this->version);
            } else {
                Configuration::updateValue(Tools::strtoupper($this->name.'_version'), $this->version);
            }
        }
    }

    /**
     * Removing the module.
     *
     * @return bool
     */
    public function uninstall()
    {
        $id = (int) Tab::getIdFromClassName('pdftot');
        if ($id != 0) {
            $tab = new Tab($id); // Get class infos
            if (!$tab->delete()) {
                // Delete tab from old version if it exists
                return false;
            }
        }

        // Delete configuration
        Configuration::deleteByName(Tools::strtoupper($this->name.'_owner'));
        Configuration::deleteByName(Tools::strtoupper($this->name.'_details'));
        Configuration::deleteByName(Tools::strtoupper($this->name.'_address'));
        Configuration::deleteByName(Tools::strtoupper($this->name.'_co_name'));
        Configuration::deleteByName(Tools::strtoupper($this->name.'_co_addr'));
        Configuration::deleteByName(Tools::strtoupper($this->name.'_phone'));
        Configuration::deleteByName(Tools::strtoupper($this->name.'_fax'));
        Configuration::deleteByName(Tools::strtoupper($this->name.'_mail'));

        Configuration::deleteByName(Tools::strtoupper($this->name.'_txtCol'));
        Configuration::deleteByName(Tools::strtoupper($this->name.'_bgCol'));
        Configuration::deleteByName(Tools::strtoupper($this->name.'_txtColHov'));
        Configuration::deleteByName(Tools::strtoupper($this->name.'_bgColHov'));
        Configuration::deleteByName(Tools::strtoupper($this->name.'_btnPic'));
        Configuration::deleteByName(Tools::strtoupper($this->name.'_useCustomStyle'));

        if (parent::uninstall() == false || !Configuration::deleteByName(Tools::strtoupper($this->name).'_MESSAGE')) {
            return false;
        }

        return true;
    }

    private function displayBanner()
    {
        $module = array(
            'description' => $this->description,
            'displayName' => $this->displayName,
        );

        $smarty = $this->context->smarty;

        $datas = array(
            'module' => $module,
        );

        $smarty->assign($datas);

        return $this->display($this->_path, 'views/templates/admin/banner.tpl');
    }

    /**
     * Admin panel.
     *
     * @return string
     */
    public function getContent()
    {

        $_html = '';

        $js = array(
            _MODULE_DIR_ . $this->name . '/views/js/riot-compiler.min.js'
            );

        $this->context->controller->addJS($js);
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css', 'all');



        if (!$this->logoInvoiceIsOk()) {
            return $this->addErrors(
                $this->l('To generate PDF, our module requires you to have an image in "JPG" format. Please first make sure you use a picture in "JPG" format. To do this, go to the theme page and change the image "Invoice logo".')
            );
        }

        $this->context->controller->addCSS($this->_path.'views/css/admin.css', 'all');


        // Process
        $process = $this->postProcess();
        if (!sizeof(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning[] = $this->l('No currency set for this module');
        }

        $_html .= $this->assignSeeMoreTemplateVars();

        $_html .= $this->displayBanner();

        $this->assignButtonConfig();
        $this->assignVarsForm();
        $this->assignVarsRIBZone();
        $this->context->controller->addJqueryPlugin('colorpicker');
        $this->context->smarty->assign('PSVersion', version_compare(_PS_VERSION_, '1.7', '>=') ? '17' : '16');

        // PrestUI
        $_html .= $this->display(__FILE__, 'views/templates/admin/backoffice.tpl');
        $this->smarty->assign('ps_version', Tools::substr(_PS_VERSION_, 0, 3));

        $_html .= $this->display(__FILE__, 'views/templates/admin/prestui/ps-alert.tpl');
        $_html .= $this->display(__FILE__, 'views/templates/admin/prestui/ps-form.tpl');
        $_html .= $this->display(__FILE__, 'views/templates/admin/prestui/ps-panel.tpl');
        $_html .= $this->display(__FILE__, 'views/templates/admin/prestui/ps-table.tpl');
        $_html .= $this->display(__FILE__, 'views/templates/admin/prestui/ps-tags.tpl');

        return $_html;
    }

    private function assignSeeMoreTemplateVars()
    {
        foreach ($this->recommended as $key => $module) {
            if (Module::isInstalled($module['short_name'])) {
                $link = 'index.php?controller=';
                $link .= Tools::getValue('controller') . '&configure=' . $module['short_name'] . '&token=';
                $link .= Tools::getValue('token') . '&tab_module=' . $this->tab;
                $link .= '&module_name=' . $module['short_name'];

                $this->recommended[$key]['link'] = $link;
                $this->recommended[$key]['installed'] = true;
            }
        }

        $assigns = array(
            'path' => $this->_path,
            'recommended' => $this->recommended
        );

        $this->context->smarty->assign('seemore', $assigns);
    }

    private function assignButtonConfig()
    {
        $configs = array('txtCol', 'bgCol', 'txtColHov', 'bgColHov', 'btnPic', 'useCustomStyle');
        foreach ($configs as $config) {
            $value = Configuration::get(Tools::strtoupper($this->name.'_'.$config));
            $this->context->smarty->assign(array($config => $value));
            Media::addJsDef(array($config => $value));
        }
        if (Tools::getValue('controller') == 'AdminModules' && version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/admin-custom.js');
        }
    }

    public function logoInvoiceIsOk()
    {
        //Récupération du logo facture
        $file = Configuration::get('PS_LOGO_INVOICE');

        if (!$file) {
            $file = Configuration::get('PS_LOGO');
        }
        if (!$file) {
            if (file_exists(_PS_IMG_DIR_.'logo_invoice.jpg')) {
                $file = _PS_IMG_DIR_.'logo_invoice.jpg';
            } else if (file_exists(_PS_IMG_DIR_.'logo.jpg')) {
                $file = _PS_IMG_DIR_.'logo.jpg';
            } else {
                return false;
            }
        }

        $path_parts = pathinfo($file);
        $a = getimagesize(_PS_IMG_DIR_.$path_parts['basename']);

        if ($a[2] != 2 && $path_parts['extension'] == 'jpg') {
            return false;
        }

        return true;
    }

    /**
     * Form process.
     */
    private function postProcess()
    {
        // If form send
        if (Tools::isSubmit('btnSubmit')) {
            $log = null;
            $errors = false;
            // For temporary cachePrestashop GIT
            $tmp = array();

            $tmp = Configuration::getMultiple(
                array(
                    Tools::strtoupper($this->name.'_co_name'),
                    Tools::strtoupper($this->name.'_co_addr'),
                    Tools::strtoupper($this->name.'_phone'),
                    Tools::strtoupper($this->name.'_fax'),
                    Tools::strtoupper($this->name.'_mail'),
                )
            );

            // Company name
            if (Tools::getValue('company') || Tools::getValue('company') === '') {
                // Update value
                $cfg_key = Tools::strtoupper($this->name.'_co_name');
                if (!Configuration::updateValue($cfg_key, Tools::getValue('company', null))) {
                    $errors = true;
                }
            }
            // Address
            if ((Tools::getValue('address') || Tools::getValue('address') === '') && $errors == false) {
                // Update value
                $cfg_key = Tools::strtoupper($this->name.'_co_addr');
                if (!Configuration::updateValue($cfg_key, Tools::getValue('address', null))) {
                    $errors = true;
                }
            }
            // Phone
            if ((Tools::getValue('phone') || Tools::getValue('phone') === '') && $errors == false) {
                // Update value
                $cfg_key = Tools::strtoupper($this->name.'_phone');
                if ($errors == false && !Configuration::updateValue($cfg_key, Tools::getValue('phone', null))) {
                    $errors = true;
                }
            }
            // Fax
            if ((Tools::getValue('fax') || Tools::getValue('fax') === '') && $errors == false) {
                // Update value
                $cfg_key = Tools::strtoupper($this->name.'_fax');
                if (!Configuration::updateValue($cfg_key, Tools::getValue('fax', null))) {
                    $errors = true;
                }
            }
            // Mail
            if ((Tools::getValue('mail') || Tools::getValue('mail') === '') && $errors == false) {
                // if not a valid email
                if (!Validate::isEmail(Tools::getValue('mail'))) {
                    $errors = true;
                    $log .= ($log != null ? ', ' : '').$this->l('mail');
                }
                // Update value
                $cfg_key = Tools::strtoupper($this->name.'_mail');
                if ($errors == false && !Configuration::updateValue($cfg_key, Tools::getValue('mail', null))) {
                    $errors = true;
                }
            }
            // if error
            if ($errors === true) {
                foreach ($tmp as $key => $val) {
                    Configuration::updateValue($key, $val);
                }

                return $this->addErrors($this->l('Verify your formulaire : ').$log);
            } else {
                Tools::redirectAdmin($this->link.'&conf=4');
            }
        } elseif (Tools::getValue('btnRIB')) {
            //if the form is completed
            if (Tools::getValue('owner') || Tools::getValue('details') || Tools::getValue('rib_address')) {
                $cfg_key = Tools::strtoupper($this->name.'_owner');
                $cfg_key_details = Tools::strtoupper($this->name.'_details');
                $cfg_key_address = Tools::strtoupper($this->name.'_address');

                if (Configuration::UpdateValue($cfg_key, Tools::getValue('owner'))
                    && Configuration::UpdateValue($cfg_key_details, Tools::getValue('details'))
                    && Configuration::UpdateValue($cfg_key_address, Tools::getValue('rib_address'))
                ) {
                    Tools::redirectAdmin($this->link.'&conf=4');
                } else {
                    return $this->addErrors($this->l('Your configuration has not been saved'));
                }
            }
        }

        if (Tools::isSubmit('totadm_custom_submit')) {
            Configuration::updateValue(Tools::strtoupper($this->name.'_txtCol'), Tools::getValue('txtCol'));
            Configuration::updateValue(Tools::strtoupper($this->name.'_bgCol'), Tools::getValue('bgCol'));
            Configuration::updateValue(Tools::strtoupper($this->name.'_txtColHov'), Tools::getValue('txtColHov'));
            Configuration::updateValue(Tools::strtoupper($this->name.'_bgColHov'), Tools::getValue('bgColHov'));
            Configuration::updateValue(Tools::strtoupper($this->name . '_useCustomStyle'), Tools::getValue('useCustomStyle'));
            if ($_FILES['btnPic']['tmp_name']) {
                if (!ImageManagerCore::validateUpload($_FILES['btnPic'])) {
                    $imagePath = _PS_MODULE_DIR_ . $this->name . "/views/img/customize-image.jpg";
                    if (move_uploaded_file($_FILES['btnPic']['tmp_name'], $imagePath)) {
                        Configuration::updateValue(Tools::strtoupper($this->name.'_btnPic'), _MODULE_DIR_ . $this->name . '/views/img/customize-image.jpg?'.time());
                    }
                }
            }
        }
    }

    /**
     * Form to save message.
     */
    private function formMessage()
    {
        $this->assignVarsForm();
        return $this->display($this->_path, 'views/templates/admin/form.tpl');
    }

    private function assignVarsForm()
    {
        $cfg_keys = array(
            Tools::strtoupper($this->name.'_co_name'),
            Tools::strtoupper($this->name.'_co_addr'),
            Tools::strtoupper($this->name.'_phone'),
            Tools::strtoupper($this->name.'_fax'),
            Tools::strtoupper($this->name.'_mail'),
        );

        $config = Configuration::getMultiple($cfg_keys);

        foreach ($config as $key => &$value) {
            $value = Tools::getValue($key, $value);
        }

        $this->context->smarty->assign(
            array(
                'configMessage' => $config,
                'module_link' => $this->link,
                'module_uri' => $this->_path,
            )
        );
    }

    /**
     * form RIBZone.
     */
    private function RIBZone()
    {
        $this->assignVarsRIBZone();
        return $this->display($this->_path, 'views/templates/admin/rib_zone.tpl');
    }

    private function assignVarsRIBZone()
    {
        $cfg_keys = array(
            Tools::strtoupper($this->name.'_owner'),
            Tools::strtoupper($this->name.'_details'),
            Tools::strtoupper($this->name.'_address'),
        );

        $config = Configuration::getMultiple($cfg_keys);

        foreach ($config as $key => &$value) {
            $value = Tools::getValue($key, $value);
        }

        $this->context->smarty->assign(
            array(
                'config' => $config,
                'module_link' => $this->link,
                'module_uri' => $this->_path,
            )
        );
    }

    /**
     * Display in admin order.
     *
     * @param array Params
     *
     * @return string
     */
    public function hookadminOrder($params)
    {
        $order = new Order($params['id_order']);
        // If buy by this module
        if ($order->module == $this->name) {
            $token = Tools::getAdminTokenLite('PDFTot');
            $url = 'index.php?tab=PDFTot&token='.$token.'';
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                return '
                    <br />
                    <fieldset>
                        <legend><img src="'.$this->_path.'logo.gif" alt="" />'.$this->displayName.'</legend>
                             <a target="_blank" href="'.$url.'&id_order='.(version_compare(_PS_VERSION_, '1.5', '>') ? $order->reference : $order->id).'">'.$this->l('Mandat in PDF').'</a>
                    </fieldset>';
            } else {
                return '
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel">
                                <div class="panel-heading">
                                    <span class="badge">'.$this->displayName.'</span>
                                </div>
                                <div class="well">
                                    <a class="btn btn-default" target="_blank" href="'.$url.'&id_order='.(version_compare(_PS_VERSION_, '1.5', '>') ? $order->reference : $order->id).'">
                                        <i class="icon-file"></i>
                                        '.$this->l('Download the mandat in PDF').'
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>';
            }
        }
    }

    /**
     * Display when select type of payment.
     *
     * @param array Params
     *
     * @return string
     */
    public function hookPayment($params)
    {
        if (!$this->active) {
            return;
        }

        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $smarty = $this->context->smarty;

        $smarty->assign(
            array(
                'this_path' => $this->_path,
                'this_path_ssl' => $this->context->link->getModuleLink($this->name, 'payment', array(), true, $this->context->language->id)
            )
        );
        $this->assignButtonConfig();
        return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
    }

    /**
     * Display when select type of payment (PS1.7).
     *
     * @param array Params
     *
     * @return array PaymentOption
     */
    public function hookPaymentOptions($params)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            return;
        }

        if (!$this->active) {
            return;
        }

        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $newOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $newOption->setCallToActionText($this->l('Pay by administrative mandate'))
                ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
                ->setAdditionalInformation(
                    $this->context->smarty->fetch('module:totadministrativemandate/views/templates/hook/payment_option.tpl')
                )
        ;
        $payment_options = array(
            $newOption,
        );

        return $payment_options;
    }

    /**
     * Display CSS.
     */
    public function hookHeader()
    {
        if (version_compare(_PS_VERSION_, '1.6', '>')) {
            if ($this->context->smarty->getTemplateVars('page_name') == 'order' || $this->context->smarty->getTemplateVars('page_name') == 'order-opc'
            ) {
                $this->context->controller->addCSS($this->getLocalPath().'views/css/totadministrativemandate.css');
            }
        }
    }

    /**
     * Display message if payment accepted.
     *
     * @param array Params
     *
     * @return string
     */
    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        $smarty = $this->context->smarty;

        $order = isset($params['objOrder']) ? $params['objOrder'] : $params['order'];

        // $state = $order->getCurrentState();

        // $key_wait = Tools::strtoupper($this->name.'_wait');
        // $key_done = Tools::strtoupper($this->name.'_done');
        $key_owner = Tools::strtoupper($this->name.'_owner');
        $key_address = Tools::strtoupper($this->name.'_address');
        $key_co_name = Tools::strtoupper($this->name.'_co_name');
        $key_co_addr = Tools::strtoupper($this->name.'_co_addr');

        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $paymentAddress = Tools::nl2br($this->conf[$key_address]);
        } else {
            $paymentAddress = nl2br2($this->conf[$key_address]);
        }

        $paymentMessage = $this->l('This file have to send back to:').' ';
        $paymentMessage .= Configuration::get($key_co_name)."\n";
        $paymentMessage .= Configuration::get($key_co_addr);

        $reference = version_compare(_PS_VERSION_, '1.5', '>') ? $order->reference : $order->id;

        $smarty->assign(
            array(
                'shop_name' => Configuration::get(Tools::strtoupper($this->name.'_co_name')),
                'paymentName' => $this->conf[$key_owner],
                'paymentAddress' => $paymentAddress,
                'status' => 'ok',
                'id_order' => $order->id,
                'paymentMessage' => $paymentMessage,
                'linkPDF' => $this->_path.'pdftot.php?id_order='.urlencode($reference),
            )
        );

        return $this->display(__FILE__, 'views/templates/hook/payment_return.tpl');
    }

    /**
     * @param objet Cart
     *
     * @return string
     */
    public function execPayment($cart)
    {
        if (!$this->active) {
            return;
        }

        if (!$this->checkCurrency($cart)) {
            Tools::redirectLink(__PS_BASE_URI__.'order.php');
        }

        $smarty = $this->context->smarty;
        $cookie = $this->context->cookie;

        $key_address = Tools::strtoupper($this->name.'_address');

        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $chequeAddress = Tools::nl2br($this->conf[$key_address]);
        } else {
            $chequeAddress = Tools::nl2br($this->conf[$key_address]);
        }

        $smarty->assign(
            array(
                'nbProducts' => $cart->nbProducts(),
                'cust_currency' => $cart->id_currency,
                'currencies' => $this->getCurrency((int) $cart->id_currency),
                'total' => $cart->getOrderTotal(true, Cart::BOTH),
                'isoCode' => Language::getIsoById((int) ($cookie->id_lang)),
                'chequeName' => $this->conf[Tools::strtoupper($this->name).'_OWNER'],
                'chequeAddress' => $chequeAddress,
                'this_path' => $this->_path,
                'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/',
            )
        );

        return $this->display(__FILE__, 'views/templates/front/payment_execution.tpl');
    }

    /**
     * For (at least) PS 1.5.0.1
     * @return string
     */
    public function actionpayment()
    {
        $this->execPayment($this->context->cart);
        return dirname(__FILE__).'/views/templates/front/payment_execution.tpl';
    }

    /**
     * For (at least) PS 1.5.0.1
     * @return string
     */
    public function actionvalidation()
    {

        $cart = $this->context->cart;

        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0 || !$this->active) {
            Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');
        }

        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'totadministrativemandate') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die(Tools::displayError('This payment method is not available.'));
        }

        $customer = new Customer((int) $cart->id_customer);

        if (!Validate::isLoadedObject($customer)) {
            Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');
        }

        $currency = new Currency($this->context->cookie->id_currency);
        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);

        $mail_Vars = array(
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{total_paid}' => Tools::displayPrice($total),
            '{payment_name}' => $this->conf[Tools::strtoupper($this->name).'_OWNER'],
            '{payment_address}' => Tools::nl2br($this->conf[Tools::strtoupper($this->name).'_ADDRESS']),
            '{payment_details}' => Tools::nl2br($this->conf[Tools::strtoupper($this->name).'_DETAILS']),
        );

        $this->validateOrder(
            (int) $cart->id,
            $this->conf[Tools::strtoupper($this->name).'_WAIT'],
            $total,
            $this->displayName,
            null,
            $mail_Vars,
            (int) $currency->id,
            false,
            $customer->secure_key
        );

        $id_order = Order::getOrderByCartId($cart->id);

        $order = new Order($id_order);

        if (isset($_SERVER['HTTPS'])) {
            $url_shop = Tools::getShopDomainSsl(true);
        } else {
            $url_shop = Tools::getShopDomain(true);
        }

        $l = $this->_path.'pdftot.php?id_order='. urlencode($order->reference);

        $temp = array(
            '{order_name}' => $order->reference,
            '{pdf_link}' => $l,
            '{shop_mandate}' => $url_shop,
        );

        $mailVars = array_merge($mail_Vars, $temp);

        $id_lang_mail = TotAdministrativeMandate::getMailLanguageId($cart->id_lang);

        $process = Mail::Send(
            (int) $id_lang_mail,
            'wait_mandate',
            $this->l('Mandat administratif'),
            $mailVars,
            $customer->email,
            $customer->firstname.' '.$customer->lastname,
            null,
            null,
            null,
            null,
            dirname(__FILE__).'/mails/',
            false
        );

        if (!$process) {
            die($this->l("Can't be send a mail"));
        }

        $datas = array(
            'id_cart' => (int) $cart->id,
            'id_module' => (int) $this->id,
            'id_order' => (int) $this->currentOrder,
            'key' => $customer->secure_key,
        );

        $id_lang = (int) $this->context->language->id;

        $link = $this->context->link->getPageLink('order-confirmation', null, $id_lang, $datas);

        Tools::redirectLink($link);
    }

    /**
     * Check if currency exists.
     *
     * @param object Cart
     *
     * @return bool
     */
    public function checkCurrency($cart)
    {
        $currency_order = new Currency((int) ($cart->id_currency));
        $currencies_module = $this->getCurrency((int) $cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }

        return false;
    }

    private function addErrors($msg)
    {
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            return $this->displayError($msg);
        } else {
            $this->context->controller->errors[] = $msg;

            return true;
        }
    }

    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Get an existing language ID to send mail.
     * @param int $id_lang
     * @return false|int|null|string
     */
    public static function getMailLanguageId($id_lang)
    {
        $lang = new Language($id_lang);

        $available_mail_languages = array();
        $handle                   = opendir(dirname(_PS_MODULE_DIR_.DIRECTORY_SEPARATOR.'totadministrativemandate'.DIRECTORY_SEPARATOR.'mails'.DIRECTORY_SEPARATOR.'.'));
        $blacklist                = array('.', '..', 'index.php');
        while (false !== ($file = readdir($handle))) {
            if (!in_array($file, $blacklist)) {
                $available_mail_languages[] = $file;
            }
        }
        closedir($handle);

        if (!in_array($lang->iso_code, $available_mail_languages)) {
            $context = Context::getContext();
            if (in_array($context->language->iso_code, $available_mail_languages)) {
                $id_lang = $context->language->id;
            } else {
                if (Language::getIdByIso('en')) {
                    $id_lang = Language::getIdByIso('en');
                }
            }
        }

        return $id_lang;
    }
}
