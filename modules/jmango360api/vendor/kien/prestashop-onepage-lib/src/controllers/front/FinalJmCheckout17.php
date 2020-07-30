<?php
/**
 * Created by PhpStorm.
 * User: tien
 * Date: 12/21/17
 * Time: 18:03
 * @author tien
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class FinalJmCheckout17 extends ModuleFrontController
{
    const METHOD_CHANGE_COUNTRY = 'changeCountry';
    const METHOD_CHANGE_DELIVERY_COUNTRY = 'changeDeliveryCountry';
    const METHOD_GET_CARRIER = 'getCarrierList';
    const METHOD_UPDATE_CARRIER = 'updateCarrier';
    const METHOD_LOAD_CART = 'loadCart';
    const METHOD_LOAD_PAYMENT = 'loadPayment';
    const METHOD_UPDATE_PAYMENT = 'updatePayment';

    const SECTION_BILLING_ADDRESS = 'billing';
    const SECTION_SHIPPING_ADDRESS = 'shipping';
    const SECTION_SHIPPING_METHOD = 'shipping_method';
    const SECTION_PAYMENT_METHOD = 'payment';
    const SECTION_REVIEW = 'review';

    const ALLOWED_JS_MODULES = array(
        'jmango360pwa',
        'jmango360api',
        'sequrapayment',
        'chronopost',
        'soflexibilite',
        'maps.google.com',
        'maps.googleapis.com',
        'paypal'
    );

    protected $template;
    protected $template_dir;
    private $module_dir = "";
    private $registerForm;
    private $addressForm;
    protected $checkoutProcess;
    protected $personalInformationStep;
    protected $billingAddressStep;
    protected $shippingAddressStep;
    protected $paymentStep;
    protected $reviewStep;
    protected $errorMessages;
    protected $nextSection;
    protected $updatedSection;
    protected $extraData;
    protected $jmCheckoutSession;
    protected $context;
    public $module_name;

    public function init()
    {
        parent::init();

        $this->template = 'module:' . $this->module_name . '/vendor/kien/prestashop-onepage-lib/src/views/templates/front/' . $this->module_name . '/onepage17_new/jmcheckout.tpl';

        $this->template_dir = _PS_MODULE_DIR_ . $this->module_name . "/vendor/kien/prestashop-onepage-lib/src/views/templates/front/" . $this->module_name;

        $this->context = Context::getContext();

        /**
         * PS-918: Fix PS JS minify
         */
        if (strpos($_SERVER['HTTP_HOST'], 'hair-france.fr') !== false || Tools::getIsset('XDEBUG_SESSION_START')) {
            Configuration::set('PS_JS_THEME_CACHE', 0);
        }

        $this->context->smarty->escape_html = false;
    }

    public function initContent()
    {
        parent::initContent();

        /**
         * Remove some unused JS files
         */
        $jsFileList = $this->context->controller->getJavascript();
        $jsAllowedList = array();

        foreach ($jsFileList as $position => $jsFileTypes) {
            foreach ($jsFileTypes as $jsFileType => $jsFiles) {
                foreach ($jsFiles as $jsFileId => $jsFile) {
                    if (is_array($jsFile) && !empty($jsFile['path'])) {
                        $isAllowed = false;

                        foreach (self::ALLOWED_JS_MODULES as $jsModule) {
                            if (strpos($jsFile['path'], $jsModule) !== false) {
                                $isAllowed = true;
                            }
                        }

                        if (!$isAllowed) {
                            $this->context->controller->unregisterJavascript($jsFileId);
                        } else {
                            $jsAllowedList[$jsFileId] = str_replace(_PS_ROOT_DIR_, '', $jsFile['path']);
                        }
                    }
                }
            }
        }

        /**
         * PS-1522: Fix function 'unregisterJavascript' in PS 1.7.0.x
         */
        if (version_compare(_PS_VERSION_, '1.7.1', '<') && count($jsAllowedList)) {
            foreach ($jsAllowedList as $jsFileId => $jsFilePath) {
                $this->context->controller->registerJavascript($jsFileId, $jsFilePath, array(
                    'position' => 'head', 'attributes' => 'none'
                ));
            }
        }

        /**
         * Clean some unwanted element in header
         */
        $hookHeader = $this->context->smarty->getTemplateVars('HOOK_HEADER');
        if ($hookHeader) {
            $processedHookHeader = $this->_processHeader($hookHeader);
            $this->context->smarty->assign(array(
                'HOOK_HEADER' => $processedHookHeader
            ));
        }

        $this->context->smarty->assign('module_name', $this->module_name);
        if (!$this->context->cart->nbProducts()) {
            $this->setTemplate('module:' . $this->module_name . '/vendor/kien/prestashop-onepage-lib/src/views/templates/front/' . $this->module_name . '/empty.tpl');
            return;
        }

        $this->_assignCountries();

        $module_url = $this->context->link->getModuleLink($this->module_name, 'jmcheckout', array());

        $addresses = array();
        if ($this->context->customer->id != 0) {
            $addresses = $this->context->customer->getAddresses($this->context->language->id);
        }

        $selectedDeliveryAddressId = $this->context->cart->id_address_delivery;
        $selectedInvoiceAddressId = $this->context->cart->id_address_invoice;
        $this->context->smarty->assign(
            'selected_delivery_address_id',
            count($addresses) == 0 ? 0 : $selectedDeliveryAddressId
        );
        $this->context->smarty->assign(
            'selected_invoice_address_id',
            count($addresses) == 0 ? 0 : $selectedInvoiceAddressId
        );
        $this->context->smarty->assign('addresses', $addresses);
        $this->context->smarty->assign('lang_code', $this->context->language->iso_code);
        $this->context->smarty->assign('is_logged', (int)$this->context->customer->isLogged());
        $this->context->smarty->assign('customer', $this->context->customer);
        $this->context->smarty->assign('myopc_checkout_url', $module_url);
        $this->context->smarty->assign('template_dir', $this->template_dir);
        $this->context->smarty->assign('enable_coupon_onepage', Configuration::get(EcommService::JM_COUPON_FOR_ONEPAGE));

        $this->context->smarty->assign(array(
            'type' => 'invoice',
            'register_form' => $this->registerForm->getProxy(),
            'address_form' => $this->addressForm->getProxy(),
            'guest_allowed' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
            'default_country_id' => $this->getDefaultCountry17()
        ));

        /**
         * Support custom CSS and JS in checkout
         */
        $customCss = CheckoutSettingsService::getCheckoutCustomCss();
        $this->context->smarty->assign('custom_css', $customCss);
        $customJS = CheckoutSettingsService::getCheckoutCustomJs();
        $this->context->smarty->assign('custom_js', $customJS);

        $this->setTemplate($this->template);
    }

    /**
     * Strip some unwantted modules
     *
     * @param string $html
     * @return string
     */
    protected function _processHeader($html)
    {
        $doc = new DOMDocument();

        // Set error level to ignore some warnings
        $internalErrors = libxml_use_internal_errors(true);

        if (function_exists('mb_convert_encoding')) {
            $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        } elseif (function_exists('iconv')) {
            $doc->loadHTML(iconv('utf-8//TRANSLIT//IGNORE', 'HTML-ENTITIES', $html));
        } else {
            $doc->loadHTML($html);
        }

        // Restore error level
        libxml_use_internal_errors($internalErrors);

        $xpath = new DOMXPath($doc);
        $whatsappElms = $xpath->query('//a[contains(@class,"whatsappchat-anchor")]');
        foreach ($whatsappElms as $whatsappElm) {
            $whatsappElm->parentNode->removeChild($whatsappElm);
        }

        $headHtml = '';

        $headNode = $doc->getElementsByTagName('head')->item(0);
        if ($headNode) {
            $headHtml .= str_replace(array('<head>', '</head>'), '', $doc->saveHTML($headNode));
        }

        $bodyNode = $doc->getElementsByTagName('body')->item(0);
        if ($bodyNode) {
            $headHtml .= str_replace(array('<body>', '</body>'), '', $doc->saveHTML($bodyNode));
        }

        return $headHtml;
    }

    public function setMedia()
    {
        parent::setMedia();

        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->module_dir = __PS_BASE_URI__ . 'modules/' . $this->name . '/';
        } else {
            $this->module_dir = _PS_MODULE_DIR_ . $this->module_name . '/';
        }

        $lang_iso_code = $this->context->language->iso_code;

        $this->addCSS($this->module_dir . 'views/css/bootstrap.min.css');
        if ($lang_iso_code == 'ar') {
            $this->addCSS($this->module_dir . 'views/css/bootstrap-rtl.min.css');
        }
        $this->addCSS($this->module_dir . 'views/css/font-awesome-4.7.0/css/font-awesome.css');
        $this->addCSS($this->module_dir . 'views/css/responsive.css');
        $this->addCSS($this->module_dir . 'views/css/ladda.min.css');
        $this->addCSS($this->module_dir . '/vendor/kien/prestashop-onepage-lib/src/views/css/17/style_new.css');
        if ($lang_iso_code == 'ar') {
            $this->addCSS($this->module_dir . 'views/css/17/rtl.css');
        }

        $this->registerJavascript(
            'module-jmango360api-jquery.min.js',
            'modules/' . $this->module_name . '/views/js/libs/jquery.min.js',
            array('position' => 'head', 'priority' => 120, 'attributes' => 'none')
        );
        $this->registerJavascript(
            'module-jmango360api-bootstrap.min.js',
            'modules/' . $this->module_name . '/views/js/libs/bootstrap.min.js',
            array('position' => 'head', 'priority' => 150, 'attributes' => 'none')
        );
        $this->registerJavascript(
            'module-jmango360api-bootstrap-select.js',
            'modules/' . $this->module_name . '/views/js/libs/bootstrap-select.js',
            array('position' => 'head', 'priority' => 150, 'attributes' => 'none')
        );
        $this->registerJavascript(
            'module-jmango360api-collapse.js',
            'modules/' . $this->module_name . '/views/js/libs/collapse.js',
            array('position' => 'head', 'priority' => 150, 'attributes' => 'none')
        );
        $this->registerJavascript(
            'module-jmango360api-jquery.form-validator.js',
            'modules/' . $this->module_name . '/views/js/libs/jquery.form-validator.js',
            array('position' => 'head', 'priority' => 160, 'attributes' => 'defer')
        );

        $this->registerJavascript(
            'module-jmango360api-spin.min.js',
            'modules/' . $this->module_name . '/views/js/libs/spin.min.js',
            array('position' => 'head', 'priority' => 150, 'attributes' => 'none')
        );
        $this->registerJavascript(
            'module-jmango360api-ladda.min.js',
            'modules/' . $this->module_name . '/views/js/libs/ladda.min.js',
            array('position' => 'head', 'priority' => 150, 'attributes' => 'none')
        );

        if ($lang_iso_code == 'nl') {
            $this->registerJavascript(
                'module-jmango360api-libs-lang-nl.js',
                'modules/' . $this->module_name . '/views/js/libs/lang/nl.js',
                array('position' => 'head', 'priority' => 160, 'attributes' => 'defer')
            );
        } elseif ($lang_iso_code == 'es') {
            $this->registerJavascript(
                'module-jmango360api-libs-lang-es.js',
                'modules/' . $this->module_name . '/views/js/libs/lang/es.js',
                array('position' => 'head', 'priority' => 160, 'attributes' => 'defer')
            );
        } elseif ($lang_iso_code == 'it') {
            $this->registerJavascript(
                'module-jmango360api-libs-lang-it.js',
                'modules/' . $this->module_name . '/views/js/libs/lang/it.js',
                array('position' => 'head', 'priority' => 160, 'attributes' => 'defer')
            );
        } elseif ($lang_iso_code == 'ar') {
            $this->registerJavascript(
                'module-jmango360api-libs-lang-ar.js',
                'modules/' . $this->module_name . '/views/js/libs/lang/ar.js',
                array('position' => 'head', 'priority' => 160, 'attributes' => 'defer')
            );
        } elseif ($lang_iso_code == 'fr') {
            $this->registerJavascript(
                'module-jmango360api-libs-lang-fr.js',
                'modules/' . $this->module_name . '/views/js/libs/lang/fr.js',
                array('position' => 'head', 'priority' => 160, 'attributes' => 'defer')
            );
        } elseif ($lang_iso_code == 'de') {
            $this->registerJavascript(
                'module-jmango360api-libs-lang-de.js',
                'modules/' . $this->module_name . '/views/js/libs/lang/de.js',
                array('position' => 'head', 'priority' => 160, 'attributes' => 'defer')
            );
        }

        $this->registerJavascript(
            'module-jmango360api-template.js',
            'modules/' . $this->module_name . '/views/js/template.js',
            array('position' => 'head', 'priority' => 150, 'attributes' => 'none')
        );
        $this->registerJavascript(
            'module-jmango360api-jmcheckout.js',
            'modules/' . $this->module_name . '/vendor/kien/prestashop-onepage-lib/src/views/js/17_new/jmcheckout.js',
            array('position' => 'head', 'priority' => 170, 'attributes' => 'none')
        );
        $this->registerJavascript(
            'module-jmango360api-bodyScrollLock.js',
            'modules/' . $this->module_name . '/vendor/kien/prestashop-onepage-lib/src/views/js/bodyScrollLock.js',
            array('position' => 'head', 'priority' => 150, 'attributes' => 'none')
        );
        $this->registerJavascript(
            'noqa/themes/specialdev603/assets/js/custom.js',
            'themes/specialdev603/assets/js/custom.js',
            array('position' => 'head', 'priority' => 150, 'attributes' => 'none')
        );

        // Support soflexibilite module
        if (Module::isEnabled('soflexibilite')) {
            require_once _PS_MODULE_DIR_ . '/soflexibilite/soflexibilite.php';
            $soflexModule = new SoFlexibilite();
            if(version_compare($soflexModule->version, '4.0.02', '<')){
                $this->registerStylesheet(
                    'soflexibilite-soflexibilite.css',
                    'modules/soflexibilite/views/css/soflexibilite.css',
                    array('position' => 'head', 'priority' => 100)
                );
                $this->registerStylesheet(
                    'soflexibilite-soflexibilite-theme.css',
                    'modules/soflexibilite/views/css/soflexibilite' . Configuration::get('SOFLEXIBILITE_THEME') . '.css',
                    array('position' => 'head', 'priority' => 100)
                );
                $this->registerJavascript(
                    'soflexibilite-front_flexibilite.js',
                    'modules/soflexibilite/views/js/front_flexibilite.js',
                    array('position' => 'head', 'priority' => 160)
                );
                $this->registerJavascript(
                    'soflexibilite-fcts.js',
                    'modules/soflexibilite/views/js/fcts.js',
                    array('position' => 'head', 'priority' => 160)
                );

                if (Configuration::get('SOFLEXIBILITE_GMAP')) {
                    $this->registerJavascript('soflexibilite-gmaps', 'https://maps.google.com/maps/api/js?libraries=places&key=' . Configuration::get('SOFLEXIBILITE_GMAP_KEY'), array('server' => 'remote', 'position' => 'head'));
                }
            } else {
                $this->registerStylesheet(
                    'soflexibilite-common-front.css',
                    'modules/soflexibilite/views/css/front.css',
                    array('position' => 'head', 'priority' => 100)
                );
                $this->registerStylesheet(
                    'soflexibilite-icon-front.css',
                    'modules/soflexibilite/views/css/ps-icon-pack.min.css',
                    array('position' => 'head', 'priority' => 100)
                );
                $this->registerJavascript(
                    'soflexibilite-common-front.js',
                    'modules/soflexibilite/views/js/front.js',
                    array('position' => 'head', 'priority' => 160)
                );

                // Try to get google map from configuration
                $configuration = Tools::unSerialize(Configuration::get('SOFLEXIBILITE_CONFIGURATION'));
                if($configuration && $configuration['gmaps'] && $configuration['gmaps_key']){
                    $this->registerJavascript('soflexibilite-gmaps', 'https://maps.google.com/maps/api/js?libraries=places&key=' . $configuration['gmaps_key'],
                                                array('server' => 'remote', 'position' => 'head'));
                }
            }
        }

        if (Module::isEnabled('mondialrelayadvanced')) {
            require_once _PS_MODULE_DIR_ . 'mondialrelayadvanced/classes/MondialRelayTools.php';

            $this->registerJavascript(
                'mondialrelayadvanced-front.js',
                'modules/mondialrelayadvanced/views/js/front.js',
                array('position' => 'head', 'priority' => 160)
            );
            $this->registerStylesheet(
                'mondialrelayadvanced-front.css',
                'modules/mondialrelayadvanced/views/css/front.css',
                array('position' => 'head', 'priority' => 100)
            );

            $module_conf = (array)MondialRelayTools::unSerialize(Configuration::get('MRA_CONF'));

            if (isset($module_conf['google_map']) && $module_conf['google_map']) {
                $this->registerJavascript('mondialrelayadvanced-gmaps', 'https://maps.google.com/maps/api/js?libraries=places&key=' . $module_conf['google_map_key'], array('server' => 'remote', 'position' => 'head'));
            }
        }

        if(Module::isEnabled('chronopost')){
            $this->registerStylesheet(
                'chronopost-lais.css',
                'modules/chronopost/views/css/chronorelais.css',
                array('position' => 'head', 'priority' => 100)
            );

            $this->registerStylesheet(
                'chronopost-dv.css',
                'modules/chronopost/views/css/chronordv.css',
                array('position' => 'head', 'priority' => 100)
            );

            if(Configuration::get('CHRONOPOST_MAP_APIKEY')) {
                $this->context->smarty->assign('custom_html',
                    sprintf('<script src="%s"></script>', 'https://maps.googleapis.com/maps/api/js?key='. Configuration::get('CHRONOPOST_MAP_APIKEY'))
                );
            }
        }

        if (Module::isEnabled('paypal')) {
            if (Configuration::get('PAYPAL_METHOD') == 'BT') {
                if (Configuration::get('PAYPAL_BRAINTREE_ENABLED')) {
                    $this->addJqueryPlugin('fancybox');
                    $this->registerJavascript('paypal-braintreegateway-client', 'https://js.braintreegateway.com/web/3.24.0/js/client.min.js', array('server' => 'remote', 'position' => 'head'));
                    $this->registerJavascript('paypal-braintreegateway-hosted', 'https://js.braintreegateway.com/web/3.24.0/js/hosted-fields.min.js', array('server' => 'remote', 'position' => 'head'));
                    $this->registerJavascript('paypal-braintreegateway-data', 'https://js.braintreegateway.com/web/3.24.0/js/data-collector.min.js', array('server' => 'remote', 'position' => 'head'));
                    $this->registerJavascript('paypal-braintreegateway-3ds', 'https://js.braintreegateway.com/web/3.24.0/js/three-d-secure.min.js', array('server' => 'remote', 'position' => 'head'));
                    $this->registerStylesheet('paypal-braintreecss', 'modules/paypal/views/css/braintree.css');
                    $this->registerJavascript('paypal-braintreejs', 'modules/paypal/views/js/payment_bt.js', array('position' => 'head'));
                }
                if (Configuration::get('PAYPAL_BY_BRAINTREE')) {
                    $this->registerJavascript('paypal-pp-braintree-checkout', 'https://www.paypalobjects.com/api/checkout.js', array('server' => 'remote', 'position' => 'head'));
                    $this->registerJavascript('paypal-pp-braintree-checkout-min', 'https://js.braintreegateway.com/web/3.24.0/js/paypal-checkout.min.js', array('server' => 'remote', 'position' => 'head'));
                    $this->registerJavascript('paypal-pp-braintreejs', 'modules/paypal/views/js/payment_pbt.js', array('position' => 'head'));
                }
            }
            if ((Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT') || Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_CART')) && (isset($this->context->cookie->paypal_ecs) || isset($this->context->cookie->paypal_pSc))) {
                $this->registerJavascript('paypal-paypal-ec-sc', 'modules/paypal/views/js/shortcut_payment.js', array('position' => 'head'));
            }
            if (Configuration::get('PAYPAL_METHOD') == 'EC' && Configuration::get('PAYPAL_EXPRESS_CHECKOUT_IN_CONTEXT')) {
                $environment = (Configuration::get('PAYPAL_SANDBOX') ? 'sandbox' : 'live');
                Media::addJsDef(array(
                    'environment' => $environment,
                    'merchant_id' => Configuration::get('PAYPAL_MERCHANT_ID_' . Tools::strtoupper($environment)),
                    'url_token' => $this->context->link->getModuleLink('paypal', 'ecInit', array('credit_card' => '0', 'getToken' => 1), true),
                ));
                $this->registerJavascript('paypal-paypal-checkout', 'https://www.paypalobjects.com/api/checkout.js', array('server' => 'remote', 'position' => 'head'));
                $this->registerJavascript('paypal-paypal-checkout-in-context', 'modules/paypal/views/js/ec_in_context.js', array('position' => 'head'));
            }
            if (Configuration::get('PAYPAL_METHOD') == 'PPP' && Configuration::get('PAYPAL_PLUS_ENABLED')) {
                $this->registerJavascript('paypal-plus-minjs', 'https://www.paypalobjects.com/webstatic/ppplus/ppplus.min.js', array('server' => 'remote', 'position' => 'head'));
                $this->registerJavascript('paypal-plus-payment-js', 'modules/paypal/views/js/payment_ppp.js', array('position' => 'head'));
                $this->addJqueryPlugin('fancybox');
            }
        }
    }

    public function initHeader()
    {
        parent::initHeader();

        $this->context->smarty->assign('content_only', 1);
    }

    public function _assignCountries()
    {
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $availableCountries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $availableCountries = Country::getCountries($this->context->language->id, true);
        }

        $this->context->smarty->assign('countries', $availableCountries);
        $this->context->smarty->assign('default_country', 21);
    }

    public function _assignDatetime()
    {
    }

    public function postProcess()
    {
        //Tools::clearAllCache();

        parent::postProcess();

        $this->initCheckoutSession();
        $this->context->smarty->assign('selected_address', $this->jmCheckoutSession->getIdAddressDelivery());
        $this->context->smarty->assign('selected_invoice_address', $this->jmCheckoutSession->getIdAddressInvoice());

        if (Tools::isSubmit('ajax')) {
            $requestParameters = Tools::getAllValues();

            //handle all ajax request
            if (Tools::isSubmit('submitGuestAccount')) {
                $this->submitGuestAccount($requestParameters);
            } elseif (Tools::isSubmit('saveAddress')) {
                $this->initFirstForm();
                if ($requestParameters['saveAddress'] == 'invoice') {
                    $this->saveAddressForInvoice($requestParameters);
                } else {
                    $this->saveAddress($requestParameters);
                }
            } elseif (Tools::isSubmit('updateCartAddress')) {
                //
            } elseif (Tools::isSubmit('createAddress')) {
                //
            } elseif (Tools::isSubmit('submitDiscount')) {
                $this->applyDiscount();
                $this->loadSummary();
            } elseif (Tools::isSubmit('deleteDiscount')) {
                $this->applyDiscount();
                $this->loadSummary();
            } elseif (Tools::isSubmit('method')) {
                $method = Tools::getValue('method');

                switch ($method) {
                    case self::METHOD_CHANGE_COUNTRY:
                        $this->changeCountry();
                        break;
                    case self::METHOD_CHANGE_DELIVERY_COUNTRY:
                        $this->changeDeliveryCountry();
                        break;
                    case self::METHOD_GET_CARRIER:
                        $this->getCarriers();
                        break;
                    case self::METHOD_UPDATE_CARRIER:
                        $this->updateCarrier();
                        $this->getPayment($requestParameters);
                        break;
                    case self::METHOD_LOAD_CART:
                        break;
                    case self::METHOD_UPDATE_PAYMENT:
                        $this->loadSummary();
                        break;
                }
            }
            $this->responseAjax($this->extraData);
        } else {
            //this is the first time open checkout page

            $this->initCheckoutProcess();
            $this->initFirstForm();
        }
    }

    protected function initCheckoutProcess()
    {
    }

    //init checkout session to use in steps
    protected function initCheckoutSession()
    {
        $deliveryOptionsFinder = new DeliveryOptionsFinder(
            $this->context,
            $this->getTranslator(),
            $this->objectPresenter,
            new PriceFormatter()
        );

        $this->jmCheckoutSession = new JmCheckoutSession(
            $this->context,
            $deliveryOptionsFinder
        );
    }

    protected function initFirstForm()
    {
        //TODO: init personal info
        $this->registerForm = $this->makeCustomerForm();
        Context::getContext()->controller->php_self = "order";
        $this->registerForm->fillFromCustomer(
            $this->context->customer
        );

        $this->addressForm = $this->makeAddressForm();
    }

    protected function responseAjax($extraData = array())
    {
        $jsonData = array(
            "hasError" => !empty($this->errorMessages),
            "errors" => $this->errorMessages,
            "goto_section" => $this->nextSection,
            "updated_section" => $this->updatedSection
        );

        if ($extraData) {
            $jsonData = array_merge($extraData, $jsonData);
        }

        //return json data for each step
        echo Tools::jsonEncode($jsonData);
        die;
    }

    public function createAddressForm()
    {
        return $this->makeAddressForm();
    }

    //checkout as guest
    private function submitGuestAccount($requestParameters)
    {
        $this->initFirstForm();
        $this->personalInformationStep = new JMCheckoutPersonalInformationStep(
            $this->context,
            $this->getTranslator(),
            $this->jmCheckoutSession,
            $this->registerForm,
            $this
        );
        $this->billingAddressStep = new JMCheckoutBillingAddressStep(
            $this->context,
            $this->getTranslator(),
            $this->jmCheckoutSession,
            $this->addressForm,
            $this
        );
        $result = $this->personalInformationStep->handleRequest($requestParameters);
        $html = $this->billingAddressStep->render();
        $addressForm = $this->createAddressForm();
        $addressForm->getTemplateVariables();
        $this->extraData[] = $result;
        $this->extraData[] = array('token'=>$addressForm->getField('token')->getValue());
        $this->errorMessages = $result["errors"];
        $this->nextSection = self::SECTION_BILLING_ADDRESS;
        $this->updatedSection = array(
            self::SECTION_BILLING_ADDRESS => $html
        );
    }

    //when another country selected
    private function changeCountry()
    {
        $this->initFirstForm();
        $this->billingAddressStep = new JMCheckoutBillingAddressStep(
            $this->context,
            $this->getTranslator(),
            $this->jmCheckoutSession,
            $this->addressForm,
            $this
        );
        $html = $this->billingAddressStep->render();
        $this->errorMessages = array();
        $this->updatedSection = array(
            self::SECTION_BILLING_ADDRESS => $html
        );
    }

    //reload state list when change delivery country
    private function changeDeliveryCountry()
    {
        $this->initFirstForm();
        $this->shippingAddressStep = new JmCheckoutShippingAddressStep(
            $this->context,
            $this->getTranslator(),
            $this->jmCheckoutSession,
            $this->addressForm,
            $this
        );
        $html = $this->shippingAddressStep->render();
        $this->errorMessages = array();
        $this->updatedSection = array(
            self::SECTION_SHIPPING_ADDRESS => $html
        );
    }

    //get carrier list
    private function getCarriers()
    {
        $shippingMethodStep = new JmCheckoutShippingMethodStep(
            $this->context,
            $this->getTranslator(),
            $this->jmCheckoutSession,
            $this
        );
        /*
        * fix for validation_plugin $html not use.
        * Before :   $html = $shippingMethodStep->handleRequest();
        */
        $shippingMethodStep->handleRequest();
    }

    //when a carrier selected
    private function updateCarrier()
    {
        $shippingMethodStep = new JmCheckoutShippingMethodStep(
            $this->context,
            $this->getTranslator(),
            $this->jmCheckoutSession,
            $this
        );
        $this->errorMessages = $shippingMethodStep->handleRequest();
        $this->nextSection = self::SECTION_PAYMENT_METHOD;
    }

    //save address for invoice
    private function saveAddressForInvoice($requestParameters)
    {
        $this->shippingAddressStep = new JmCheckoutShippingAddressStep(
            $this->context,
            $this->getTranslator(),
            $this->jmCheckoutSession,
            $this->addressForm,
            $this
        );

        $this->billingAddressStep = new JMCheckoutBillingAddressStep(
            $this->context,
            $this->getTranslator(),
            $this->jmCheckoutSession,
            $this->addressForm,
            $this
        );

        $result = $this->billingAddressStep->handleRequest($requestParameters);

        $allow_sections = array();
        $this->updatedSection = array();

        if ($requestParameters['use_for_shipping']) {
            $this->nextSection = self::SECTION_SHIPPING_METHOD;

            $shippingMethodStep = new JmCheckoutShippingMethodStep(
                $this->context,
                $this->getTranslator(),
                $this->jmCheckoutSession,
                $this
            );
            $shippingMethodStep->handleRequest();
            $shippingMethodHtml = $shippingMethodStep->render();
            if (Module::isInstalled('mondialrelayadvanced')) {
                $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
                $url = $protocol . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') .
                    __PS_BASE_URI__ . 'modules/mondialrelayadvanced/';
                $shippingMethodHtml = $this->addCustomJS($url . 'views/js/front.js') . $shippingMethodHtml;
                $shippingMethodHtml = $this->addCustomJS($url . 'views/js/jquery.qtip.min.js') . $shippingMethodHtml;
                $shippingMethodHtml = $this->addCustomJS($url . 'views/js/printme.js') . $shippingMethodHtml;
            }
            if (Module::isEnabled('chronopost')) {
                $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
                $url = $protocol . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') .
                    __PS_BASE_URI__ . 'modules/chronopost/';

                $shippingMethodHtml = $this->addCustomJS($url . '/views/js/chronorelais-17.js') . $shippingMethodHtml;
                $shippingMethodHtml = $this->addCustomJS($url . '/views/js/chronordv-17.js') . $shippingMethodHtml;
            }
            $allow_sections[] = self::SECTION_SHIPPING_METHOD;
            $this->updatedSection[self::SECTION_SHIPPING_METHOD] = $shippingMethodHtml;
        } else {
            $this->nextSection = self::SECTION_SHIPPING_ADDRESS;
        }
        if (Tools::getIsset($_POST['id_address'])) {
            $shippingAddressHtml = $this->shippingAddressStep->render(array(
                'id_address' => $requestParameters['id_address']));
        } else {
            $shippingAddressHtml = $this->shippingAddressStep->render();
        }
        $allow_sections[] = self::SECTION_SHIPPING_ADDRESS;
        $this->updatedSection[self::SECTION_SHIPPING_ADDRESS] = $shippingAddressHtml;

        $this->extraData = $result;
        $this->extraData['allow_sections'] = $allow_sections;
        $this->errorMessages = $result["errors"];
    }

    //save address for delivery
    private function saveAddress($requestParameters)
    {

        $this->shippingAddressStep = new JmCheckoutShippingAddressStep(
            $this->context,
            $this->getTranslator(),
            $this->jmCheckoutSession,
            $this->addressForm,
            $this
        );
        $shippingMethodStep = new JmCheckoutShippingMethodStep(
            $this->context,
            $this->getTranslator(),
            $this->jmCheckoutSession,
            $this
        );

        $result = $this->shippingAddressStep->handleRequest($requestParameters);

        $this->extraData[] = $result;
        $this->errorMessages = $result["errors"];
        $shippingMethodStep->handleRequest();
        $html = $shippingMethodStep->render();
        $out = ob_get_clean();
        if (Module::isInstalled('mondialrelayadvanced')) {
            $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
            $url = $protocol . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') .
                __PS_BASE_URI__ . 'modules/mondialrelayadvanced/';
            $html = $this->addCustomJS($url . 'views/js/front.js') . $html;
            $html = $this->addCustomJS($url . 'views/js/jquery.qtip.min.js') . $html;
            $html = $this->addCustomJS($url . 'views/js/printme.js') . $html;
        }
        if (Module::isEnabled('chronopost')) {
            $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
            $url = $protocol . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') .
                __PS_BASE_URI__ . 'modules/chronopost/';

            $html = $this->addCustomJS($url . '/views/js/chronorelais-17.js'). $html;
            $html = $this->addCustomJS($url . '/views/js/chronordv-17.js') . $html;
        }
        $this->extraData = $result;
        $this->nextSection = self::SECTION_SHIPPING_METHOD;
        $this->updatedSection = array(
            self::SECTION_SHIPPING_METHOD => $html
        );
    }

    public function addCustomJS($url)
    {
        return '<script type="text/javascript" src="' . $url . '"></script>';
    }

    //get payment
    private function getPayment($requestParameters)
    {
        $translator = $this->getTranslator();
        $this->paymentStep = new JmCheckoutPaymentStep(
            $this->context,
            $translator,
            $this->jmCheckoutSession,
            new JmPaymentOptionsFinder(),
            new ConditionsToApproveFinder(
                $this->context,
                $translator
            ),
            $this->module_name
        );

        $this->paymentStep->handleRequest($requestParameters);
        $html = $this->paymentStep->render($requestParameters);
        $this->updatedSection = array(self::SECTION_PAYMENT_METHOD => $html);
    }

    private function loadSummary()
    {
        $translator = $this->getTranslator();
        /*
        * fix for validation_plugin $presentedCart not use.
        * Before :   $presentCart = $presentedCart = $this->cart_presenter->present($this->context->cart);
        */
        $presentCart = $this->cart_presenter->present($this->context->cart);
        $conditionFinder = new  ConditionsToApproveFinder(
            $this->context,
            $translator
        );

        $this->reviewStep = new JmCheckoutReviewStep(
            $this->context,
            $this->getTranslator(),
            $this->jmCheckoutSession,
            $conditionFinder,
            $presentCart,
            $this->module_name,
            new JmPaymentOptionsFinder(),
            $this->makeAddressForm()
        );

        $this->nextSection = self::SECTION_REVIEW;
        $html = $this->reviewStep->render();
        $this->updatedSection = array(
            self::SECTION_REVIEW => $html
        );
    }

    private function applyDiscount()
    {
        if (Tools::getIsset('submitDiscount')) {
            if (!($code = trim(Tools::getValue('coupon_code')))) {
                $this->errorMessages[] = $this->getLocalizeMessage('You must enter a voucher code.', 'coupon-service');
            } elseif (!Validate::isCleanHtml($code)) {
                $this->errorMessages[] = $this->getLocalizeMessage('The voucher code is invalid', 'jmcheckout_16');
            } else {
                if (($cartRule = new CartRule(CartRule::getIdByCode($code))) && Validate::isLoadedObject($cartRule)) {
                    if ($error = $cartRule->checkValidity($this->context, false, true)) {
                        $this->errorMessages[] = $this->getLocalizeMessage($error, "coupon-service");
                    } else {
                        $this->context->cart->addCartRule($cartRule->id);
                    }
                } else {
                    $this->errorMessages[] = $this->getLocalizeMessage('The voucher code is invalid', 'jmcheckout_16');
                }
            }
        } elseif (($id_cart_rule = (int)Tools::getValue('deleteDiscount')) && Validate::isUnsignedId($id_cart_rule)) {
            $this->context->cart->removeCartRule($id_cart_rule);
            CartRule::autoAddToCart($this->context);
        }
    }

    public function getLocalizeMessage($string, $specific)
    {
        return $this->l($string, $specific);
    }

    public function getDefaultCountry17()
    {

        $firstAddress = (int)Address::getFirstCustomerAddressId($this->context->customer->id);
        // address book exist => use first address's country
        if ($firstAddress) {
            $country = Address::getCountryAndState($firstAddress);
            return $country['id_country'];
        } else {
            // address book not exist => use default country
            $country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
            if (!$country->active) {
                // default country is not actived
                return 0;
            }
            return Configuration::get('PS_COUNTRY_DEFAULT');
        }
    }
}
