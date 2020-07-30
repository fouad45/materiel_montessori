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

class AdminGetStartedController extends BaseTouchizeController
{
    const INFO_TEMPLATE = 'info/get-started.tpl';

    /**
     * AdminGetStartedController constructor.
     */
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        if (Tools::getValue('is_ajax') == "true") {
            if (Tools::getValue('state') == '0') {
                Configuration::updateValue(
                    'TOUCHIZE_ENABLED',
                    0
                );
            } else {
                Configuration::updateValue(
                    'TOUCHIZE_ENABLED',
                    3
                );
            }
        }
    }

    /**
     * @see AdminController->init();
     */
    public function init()
    {
        parent::init();
        # Just redirect to the module configuration page if already finished the wizard
        if (Configuration::get('TOUCHIZE_WIZARD_FINISHED') != '1') {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminSetupWizard'));
        }
    }

    /**
     * AdminController::initContent() override
     *
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $licenseHelper = new TouchizeLicenseHelper();
        $result = $licenseHelper->validateLicense(true);
        $hasValidLicense = $result['ok'];

        $smarty = $this->context->smarty;
        $smarty->assign(
            'img_dir',
            AdminSetupWizardController::IMAGE_CDN_PATH
        );
        $smarty->assign(
            'manage_subscription',
            $this->context->link->getAdminLink('AdminLicense')
        );
        $smarty->assign(
            'pricing',
            $this->context->language->iso_code
        );
        $smarty->assign(
            'valid_license',
            $hasValidLicense
        );
        $smarty->assign(
            'videos',
            $this->getYoutubePlaylist()
        );
    }
    /**
     * AdminController::setMedia() override
     *
     * @see AdminController::setMedia()
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
        $this->context->controller->addCSS(
            array(
                _MODULE_DIR_.'touchize/views/css/embedbootstrap.css',
                _MODULE_DIR_.'touchize/views/css/octicons.min.css',
                _MODULE_DIR_.'touchize/views/css/cpicker.min.css',
                _MODULE_DIR_.'touchize/views/css/wizard.css',
                _MODULE_DIR_.'touchize/views/css/touchize-admin.css'
            )
        );
        $this->context->controller->addJS(
            array(
                _MODULE_DIR_.'touchize/views/js/botab.js',
                _MODULE_DIR_.'touchize/views/js/qrcode.js'
            )
        );
    }
    /**
     * Get youtube videos from "tutorial" playlist
     *
     * @return video items
     */
    public function getYoutubePlaylist()
    {
        $api_key = 'AIzaSyCaZUihJCOAsDtTaVmAzJ3WXAFNfgcspS8';
        $playlist_id =  'PLAectF90bVpr6fhqnjJmhtx2i2iXVYgeR';
        $api_url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=25&playlistId='. $playlist_id . '&key=' . $api_key;
        try {
            $get_contents = Tools::file_get_contents($api_url);
            $playlist = Tools::jsonDecode($get_contents, true);
            if ($playlist && array_key_exists('items', $playlist)) {
                return $playlist['items'];
            }
        } catch (Exception $e) {
            return array();
        }
    }
}
