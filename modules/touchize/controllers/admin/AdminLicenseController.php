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
 * License controller.
 */

class AdminLicenseController extends BaseTouchizeController
{
    const INFO_TEMPLATE = 'info/license.tpl';

    /**
     * ~ constructor.
     */
    public function __construct()
    {
        if (Tools::getValue('is_ajax') == "true") {
            if (Tools::getValue('id') == 'start-trial') {
                if ($this->validateTrial()) {
                    Configuration::updateValue(
                        'TOUCHIZE_TRIAL_HAS_BEEN_ACTIVATED',
                        '1'
                    );
                    Configuration::updateValue(
                        'TOUCHIZE_TRIAL_ACTIVE',
                        '1'
                    );
                    //Using unix timestamp
                    Configuration::updateValue(
                        'TOUCHIZE_WHEN_TRIAL_WAS_ACTIVATED',
                        time()
                    );
                    //Start Touchize
                    Configuration::updateValue(
                        'TOUCHIZE_ENABLED',
                        Tools::getValue('is_setup_wizard') == "true" ? 0 : 3
                    );
                }
            }
            return;
        }
        $this->bootstrap = true;
        parent::__construct();
        $this->context->smarty->assign(array(
            'iso_code' => $this->context->language->iso_code
        ));
        $helper = new TouchizeAdminHelper();
        $this->licenseHelper = new TouchizeLicenseHelper();
        $helper->assignMenuVars();
        $this->fields_options = array(
            'enable' => array(
                'title' => $this->l('Enable Swipe-2-Buy'),
                'icon' => 'icon-power-off',
                'fields' => array(
                    'TOUCHIZE_ENABLED' => array(
                        'hint' => $this->l('Choose which devices Swipe-2-Buy should be enabled on.'),
                        'title' => $this->l('Enable on'),
                        'validation' => 'isGenericName',
                        'type' => 'radio',
                        'choices' => array(
                            3 => $this->l('Both mobile and tablet.'),
                            2 => $this->l('Only on tablet.'),
                            1 => $this->l('Only on mobile.'),
                            0 => $this->l('None (Disable and go to sandbox mode).'),
                        ),
                        'no_multishop_checkbox' => true
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'SubmitEnabling'
                )
            ),
            'account' => array(
                'title' => $this->l('Manage your account'),
                'info' => '',
                'icon' => 'icon-user',
            ),
            'key' => array(
                'title' => $this->l('Enter Subscription Id'),
                'info' => '',
                'icon' => 'icon-key',
                'fields' => array(
                    'TOUCHIZE_LICENSE_KEY' => array(
                        'hint' => $this->l('Enter the Subscription Id you received in your email'),
                        'title' => $this->l('Subscription Id'),
                        'desc' => $this->l('Enter the Subscription Id you received in your email'),
                        'validation' => 'isGenericName',
                        'type' => 'text',
                        'no_multishop_checkbox' => true
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Verify'),
                    'name' => 'ValidateKey'
                )
            ),
        );

        $keyValidated = Configuration::get('TOUCHIZE_LICENSE_KEY_VALIDATED');
        $trialActivated = Configuration::get("TOUCHIZE_TRIAL_HAS_BEEN_ACTIVATED");
    
        if (empty($keyValidated) && empty($trialActivated)) {
            unset($this->fields_options['enable']);
            $this->fields_options['account']['title'] = $this->l('Manage your account');
            $this->fields_options['key']['title'] = $this->l('Enter Subscription Id');
        }
        $this->setAccountButton();
    }

    /**
     * AdminController::setMedia() override
     *
     * @see AdminController::setMedia()
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

        $this->context
            ->controller
            ->addCSS(
                _MODULE_DIR_.'touchize/views/css/touchize-admin.css'
            );
    }


    private function setAccountButton()
    {
        $accountButtonHTML = $this->createTemplate('partials/accountbutton.tpl')->fetch();
        $this->fields_options['account']['info'] .= $accountButtonHTML;
    }

    /**
     * @return bool|ObjectModel
     */
    public function postProcess()
    {
        if (Tools::isSubmit('ValidateKey')) {
            $key = Tools::getValue('TOUCHIZE_LICENSE_KEY');
            Configuration::updateValue(
                'TOUCHIZE_LICENSE_KEY',
                $key
            );

            if (Validate::isString($key) && !empty($key)) {
                $serverResponse = $this->licenseHelper->getKeyFromServer(trim($key));
                if (!$serverResponse['success']) {
                    $this->errors = array_merge($this->errors, $serverResponse['errors']);
                }
            } else {
                Configuration::updateValue(
                    'TOUCHIZE_LICENSE_KEY_VALIDATED',
                    ''
                );
            }
            $ok = $this->validateKey();
        }
        if (Tools::isSubmit('SubmitEnabling')) {
            $option = (int)Tools::getValue('TOUCHIZE_ENABLED');
            $silent = false;
        } else {
            $option = Configuration::get('TOUCHIZE_ENABLED');
            $silent = true;
        }

        if ($option == 0 || //Disabling always allowed
            Configuration::get('TOUCHIZE_TRIAL_ACTIVE') == '1') { //If in trial always allowed
            $ok = true;
        } else {
            $ok = $this->validateKey($silent);
        }
        if (Tools::getIsset('TOUCHIZE_ENABLED')) {
            $_POST['TOUCHIZE_ENABLED'] =  $ok ? $option : 0;
        }
        Configuration::updateValue(
            'TOUCHIZE_ENABLED',
            $ok ? $option : 0
        );
        return parent::postProcess();
    }

    /**
     * Validates the license from database
     */
    private function validateKey($silent = false)
    {
        $result = $this->licenseHelper->validateLicense($silent);

        if (isset($result['info_description'])) {
            $this->fields_options['info']['description'] = $result['info_description'];
        }
        if (isset($result['key_description'])) {
            $this->fields_options['key']['description'] = $result['key_description'];
        }
        if (isset($result['enable_description'])) {
            $this->fields_options['enable']['description'] = $result['enable_description'];
        }
        if (isset($result['confirmation'])) {
            $this->confirmations[] = $result['confirmation'];
        }
        if (isset($result['errors'])) {
            $this->errors[] = $result['errors'];
        }
        return $result['ok'];
    }

    /**
    * Validates if trial has already been activated
    */
    private function validateTrial()
    {
        //if trial previously has been activated return false else true
        if (Configuration::get('TOUCHIZE_TRIAL_HAS_BEEN_ACTIVATED', '')) {
            return false;
        }
        return true;
    }
}
