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
 * Admin helper.
 */

class TouchizeLicenseHelper extends TouchizeBaseHelper
{
    /**
     * Check if Touchize should be used
     * @return 0 if not, 1 - mobile, 2 - tablet, 3 - mobile and tablet
     */
    public function isTouchizeEnabled(&$revalidate)
    {
        $enabled = (int)Configuration::get('TOUCHIZE_ENABLED');
        $trial = Configuration::get('TOUCHIZE_TRIAL_ACTIVE');
        $trialWhen = (int)Configuration::get('TOUCHIZE_WHEN_TRIAL_WAS_ACTIVATED');
        $expired = ceil((strtotime('+3 month', $trialWhen)-time())/86400) < 0;
        if ($trial == '1' && !$expired) {
            return $enabled;
        } elseif (!empty($trial)) { // if trial has expired
            // disable trial
            Configuration::updateValue(
                'TOUCHIZE_TRIAL_ACTIVE',
                '0'
            );
            $key = Configuration::get('TOUCHIZE_LICENSE_KEY');
            if (!$this->getKeyFromServer($key)['success']) { // check if license key is valid
                // disable module
                Configuration::updateValue(
                    'TOUCHIZE_ENABLED',
                    0
                );
                Configuration::updateValue(
                    'TOUCHIZE_LICENSE_KEY_VALIDATED',
                    null
                );
                return 0;
            }
        }
        if ($enabled == 0) {
            return $enabled; //Be quick if we are not enabled
        }

        $revalidateLicense = false;
        $data = Configuration::get('TOUCHIZE_LICENSE_KEY_VALIDATED');
        if (!$data || empty($data)) {
            $revalidateLicense = true;
        } else {
            $validation = Tools::jsonDecode($data, true);
            $lastValidated = $validation['lastvalidated'];
            $currentTime = time();
            $revalidateLicense = $currentTime - $lastValidated > 86400; //(day)
        }
        $revalidate = $revalidateLicense;

        return $enabled;
    }

    public function revalidate()
    {
        $key = Configuration::get('TOUCHIZE_LICENSE_KEY');
        $this->getKeyFromServer($key);
    }

    /**
     * Fetches the license response from server and stores it in DB
     */
    public function getKeyFromServer($key)
    {
        $path = 'https://seagull.touchize.com/';
        $url = $path . '?subscription=' . $key . '&domain=' . Tools::getShopDomain() .
            '&lang=' . $this->context->language->iso_code;

        $errors = array();
        $result = array(
            'status' => ''
        );
        if (!$validation_link = Tools::file_get_contents($url)) {
            $errors[] = Tools::displayError('Validation server unreachable.');
            $result['status'] = 'unreachable';
        } elseif (!$validation = Tools::jsonDecode($validation_link, true)) {
            $errors[] = Tools::displayError('Validation server sent wrong response.');
            $result['status'] = 'bad_response';
        } else {
            $result = $validation;
            //$this->confirmations[] = 'Licensing server response.';
        }
        $result['lastvalidated'] = time();
        Configuration::updateValue(
            'TOUCHIZE_LICENSE_KEY_VALIDATED',
            Tools::jsonEncode($result)
        );
        return array (
            'success' => empty($errors),
            'errors' => $errors
        );
    }

    public function validateLicense($silent = false)
    {
        $result = array(
            'ok' => true
        );

        $data = Configuration::get('TOUCHIZE_LICENSE_KEY_VALIDATED');
        if (!$data || empty($data)) {
            $noLicenseText = $this->l('In order to enable you need to enter a valid Subscription Id first');
            $result['enable_description'] = $noLicenseText;
            if (!$silent) {
                $result['errors'] = Tools::displayError($this->l($noLicenseText));
            }
            $result['ok'] = false;
            return $result;
        }

        $validation = Tools::jsonDecode($data, true);
        switch ($validation['status']) {
            case 'license_not_found':
            case 'invalid_domain':
                $result['ok'] = false;
                if (!$silent) {
                    $result['errors'] = Tools::displayError($validation['message']);
                }
                break;
            case 'unreachable':
            case 'bad_response':
            case 'in_trial':
            case 'active':
                $result['ok'] = true;
                if (!$silent) {
                    $result['confirmation'] = $validation['message'];
                }
                break;
            default:
                break;
        }
        $result['key_description'] = $validation['message'];
        return $result;
    }
}
