<?php
/**
* 2007-2013 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Address15 extends AddressController
{
    public $auth = true;
    public $guestAllowed = true;
    public $php_self = '';
    public $authRedirection = 'addresses';
    public $ssl = true;

    /**
     * @var Address Current address
     */
    protected $constructAddress;

    /**
     * Set default medias for this controller
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_THEME_JS_DIR_.'tools/statesManagement.js');
    }

    /**
     * Initialize address controller
     * @see FrontController::init()
     */
    public function init()
    {
        // Currenly mobile's seding param 'id' when editting address (It's Magento behaviour).
        // In Prestashop, in order to display existed address data, the param 'id_address' should be provided.
        if (Tools::getValue('id')) {
            $_GET['id_address'] = Tools::getValue('id');
        }

        parent::init();
    }

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitAddress')) {
            $this->processSubmitAddress();
        } elseif (!Validate::isLoadedObject($this->constructAddress) && Validate::isLoadedObject($this->context->customer)) {
            $_POST['firstname'] = $this->context->customer->firstname;
            $_POST['lastname'] = $this->context->customer->lastname;
            $_POST['company'] = $this->context->customer->company;
        }
    }

    /**
     * Process changes on an address
     */
    protected function processSubmitAddress()
    {
        $address = new Address();
        $this->errors = $address->validateController();
        $address->id_customer = (int)$this->context->customer->id;

        // Check page token
        if ($this->context->customer->isLogged() && !$this->isTokenValid()) {
            $this->errors[] = Tools::displayError('Invalid token.');
        }

        // Check phone
        if (Configuration::get('PS_ONE_PHONE_AT_LEAST')
            && !Tools::getValue('phone')
            && !Tools::getValue('phone_mobile')) {
            $this->errors[] = Tools::displayError('You must register at least one phone number.');
        }
        if ($address->id_country) {
            // Check country
            if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country)) {
                throw new PrestaShopException('Country cannot be loaded with address->id_country');
            }

            if ((int)$country->contains_states && !(int)$address->id_state) {
                $this->errors[] = Tools::displayError('This country requires you to chose a State.');
            }

            // US customer: normalize the address
            if ($address->id_country == Country::getByIso('US') && Configuration::get('PS_TAASC')) {
                include_once(_PS_TAASC_PATH_.'AddressStandardizationSolution.php');
                $normalize = new AddressStandardizationSolution;
                $address->address1 = $normalize->AddressLineStandardization($address->address1);
                $address->address2 = $normalize->AddressLineStandardization($address->address2);
            }

            $postcode = Tools::getValue('postcode');
            /* Check zip code format */
            if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                $this->errors[] = sprintf(
                    Tools::displayError('The Zip/Postal code you\'ve entered is invalid.
                    It must follow this format: %s'),
                    str_replace(
                        'C',
                        $country->iso_code,
                        str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))
                    )
                );
            } elseif (empty($postcode) && $country->need_zip_code) {
                $this->errors[] = Tools::displayError('A Zip / Postal code is required.');
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $this->errors[] = Tools::displayError('The Zip / Postal code is invalid.');
            }

            // Check country DNI
            if ($country->isNeedDni()
                && (!Tools::getValue('dni')
                    || !Validate::isDniLite(Tools::getValue('dni')))) {
                $this->errors[] = Tools::displayError(
                    'The identification number is incorrect or has already been used.'
                );
            } elseif (!$country->isNeedDni()) {
                $address->dni = null;
            }
        }
        // Check if the alias exists
        if (!$this->context->customer->is_guest
            && !empty(Tools::getValue('alias'))
            && (int)$this->context->customer->id > 0) {
            $id_address = Tools::getValue('id_address');
            if (Configuration::get('PS_ORDER_PROCESS_TYPE')
                && (int)Tools::getValue('opc_id_address_'.Tools::getValue('type')) > 0) {
                $id_address = Tools::getValue('opc_id_address_'.Tools::getValue('type'));
            }

            if (Db::getInstance()->getValue('
				SELECT count(*)
				FROM '._DB_PREFIX_.'address
				WHERE `alias` = \''.pSql(Tools::getValue('alias')).'\'
				AND id_address != '.(int)$id_address.'
				AND id_customer = '.(int)$this->context->customer->id.'
				AND deleted = 0') > 0) {
                $this->errors[] =
                    sprintf(
                        Tools::displayError('The alias "%s" has already been used. Please select another one.'),
                        Tools::safeOutput(Tools::getValue('alias'))
                    );
            }
        }

        // Check the requires fields which are settings in the BO
        $this->errors = array_merge($this->errors, $address->validateFieldsRequiredDatabase());

        // Don't continue this process if we have errors !
        if ($this->errors && !$this->ajax) {
            return;
        }

        // If we edit this address, delete old address and create a new one
        if (Validate::isLoadedObject($this->constructAddress)) {
            if (Validate::isLoadedObject($country) && !$country->contains_states) {
                $address->id_state = 0;
            }
            $address_old = $this->constructAddress;
            if (Customer::customerHasAddress($this->context->customer->id, (int)$address_old->id)) {
                if ($address_old->isUsed()) {
                    $address_old->delete();
                } else {
                    $address->id = (int)($address_old->id);
                    $address->date_add = $address_old->date_add;
                }
            }
        }

        if ($this->ajax && Tools::getValue('type') == 'invoice' && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
            $this->errors = array_unique(array_merge($this->errors, $address->validateController()));
            if (count($this->errors)) {
                $return = array(
                    'hasError' => (bool)$this->errors,
                    'errors' => $this->errors
                );
                die(Tools::jsonEncode($return));
            }
        }

        /* Save address
         * fix for validation_plugin $result not use.
         * Before :  if ($result = $address->save()) {
         */
        if ($address->save()) {
            // Update id address of the current cart if necessary
            if (isset($address_old) && $address_old->isUsed()) {
                $this->context->cart->updateAddressId($address_old->id, $address->id);
            } else {
                $this->context->cart->autosetProductAddress();
            }// Update cart address

            if ((bool)(Tools::getValue('select_address', false)) == true
                || (Tools::getValue('type') == 'invoice'
                    && Configuration::get('PS_ORDER_PROCESS_TYPE'))) {
                $this->context->cart->id_address_invoice = (int)$address->id;
            } elseif (Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                $this->context->cart->id_address_invoice = (int)$this->context->cart->id_address_delivery;
            }
            $this->context->cart->update();

            if (empty($this->errors)) {
                if ($this->context->customer) {
                    $return = "<meta name=\"JM-Account-Id\" content=\"" . $this->context->customer->id . "\">" .
                      "<meta name=\"JM-Account-Email\" content=\"" . $this->context->customer->email . "\">";
                    die($return);
                }
            }
        }
        $this->errors[] = Tools::displayError('An error occurred while updating your address.');
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $this->assignCountries();
        $this->assignVatNumber();
        $this->assignAddressFormat();

        // Assign common vars
        $this->context->smarty->assign(array(
            'one_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'),
            'onr_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'), //retro compat
            'ajaxurl' => _MODULE_DIR_,
            'errors' => $this->errors,
            'token' => Tools::getToken(false),
            'select_address' => (int)Tools::getValue('select_address'),
            'address' => $this->constructAddress,
            'id_address' => (Validate::isLoadedObject($this->constructAddress)) ? $this->constructAddress->id : 0,
        ));

        if ($back = Tools::getValue('back')) {
            $this->context->smarty->assign('back', Tools::safeOutput($back));
        }

        if ($mod = Tools::getValue('mod')) {
            $this->context->smarty->assign('mod', Tools::safeOutput($mod));
        }

        if (isset($this->context->cookie->account_created)) {
            $this->context->smarty->assign('account_created', 1);
            unset($this->context->cookie->account_created);
        }

        if ($this->context->getMobileDevice()) {
            $this->setMobileTemplate(_PS_MODULE_DIR_ . $this->module_name .'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/'.$this->module_name.'/address15-mobile.tpl');
        } else {
            $this->setTemplate(_PS_MODULE_DIR_ . $this->module_name .'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/'.$this->module_name.'/address15.tpl');
        }
    }

    public function display()
    {
        Tools::safePostVars();

        // assign css_files and js_files at the very last time
        if ((Configuration::get('PS_CSS_THEME_CACHE') || Configuration::get('PS_JS_THEME_CACHE'))
            && is_writable(_PS_THEME_DIR_.'cache')) {
            // CSS compressor management
            if (Configuration::get('PS_CSS_THEME_CACHE')) {
                $this->css_files = Media::cccCSS($this->css_files);
            }
            //JS compressor management
            if (Configuration::get('PS_JS_THEME_CACHE')
                && !$this->context->getMobileDevice()) {
                $this->js_files = Media::cccJs($this->js_files);
            }
        }

        $this->context->smarty->assign('css_files', $this->css_files);
        $this->context->smarty->assign('js_files', array_unique($this->js_files));
        $this->context->smarty->assign(array(
            'errors' => $this->errors,
            'display_header' => $this->display_header,
            'display_footer' => $this->display_footer,
        ));

        if ($this->template) {
            $this->smartyOutputContent($this->template);
        } else {// For retrocompatibility with 1.4 controller
            $this->displayContent();
        }

        return true;
    }

    /**
     * Assign template vars related to countries display
     */
    protected function assignCountries()
    {
        // Get selected country
        if (Tools::isSubmit('id_country')
            && !is_null(Tools::getValue('id_country'))
            && is_numeric(Tools::getValue('id_country'))) {
            $selected_country = (int)Tools::getValue('id_country');
        } elseif (isset($this->constructAddress)
            && isset($this->constructAddress->id_country)
            && !empty($this->constructAddress->id_country)
            && is_numeric($this->constructAddress->id_country)) {
            $selected_country = (int)$this->constructAddress->id_country;
        } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // get all countries as language (xy) or language-country (wz-XY)
            $array = array();
            preg_match("#(?<=-)\w\w|\w\w(?!-)#", $_SERVER['HTTP_ACCEPT_LANGUAGE'], $array);

            if (!Validate::isLanguageIsoCode($array[0]) || !($selected_country = Country::getByIso($array[0]))) {
                $selected_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
            }
        } else {
            $selected_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
        }

        // Generate countries list
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->context->language->id, true);
        }

        // @todo use helper
        $list = '';
        foreach ($countries as $country) {
            $selected = ($country['id_country'] == $selected_country) ? 'selected="selected"' : '';
            $list .=
                '<option value="'.
                (int)$country['id_country'].'" '.
                $selected.'>'.htmlentities($country['name'], ENT_COMPAT, 'UTF-8').'</option>';
        }

        // Assign vars
        $this->context->smarty->assign(array(
            'countries_list' => $list,
            'countries' => $countries,
        ));
    }

    /**
     * Assign template vars related to address format
     */
    protected function assignAddressFormat()
    {
        $id_country = is_null($this->constructAddress)? 0 : (int)$this->constructAddress->id_country;
        $dlv_adr_fields = AddressFormat::getOrderedAddressFields($id_country, true, true);
        $this->context->smarty->assign('ordered_adr_fields', $dlv_adr_fields);
    }

    /**
     * Assign template vars related to vat number
     * @todo move this in vatnumber module !
     */
    protected function assignVatNumber()
    {
        $vat_number_exists = file_exists(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');
        $vat_number_management = Configuration::get('VATNUMBER_MANAGEMENT');
        if ($vat_number_management && $vat_number_exists) {
            include_once(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');
        }

        if ($vat_number_management
            && $vat_number_exists
            && VatNumber::isApplicable(Configuration::get('PS_COUNTRY_DEFAULT'))) {
            $vat_display = 2;
        } elseif ($vat_number_management) {
            $vat_display = 1;
        } else {
            $vat_display = 0;
        }

        $this->context->smarty->assign(array(
            'vatnumber_ajax_call' => file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php'),
            'vat_display' => $vat_display,
        ));
    }

    public function displayAjax()
    {
        if (count($this->errors)) {
            $return = array(
                'hasError' => !empty($this->errors),
                'errors' => $this->errors
            );
            die(Tools::jsonEncode($return));
        }
    }
}
