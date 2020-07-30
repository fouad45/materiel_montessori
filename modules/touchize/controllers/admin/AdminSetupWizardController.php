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
* New Wizzard controller.
*/

class AdminSetupWizardController extends BaseTouchizeController
{

    const TEMPLATE_FILE = 'setupwizard/setupwizard.tpl';

    const BASIC_TEMPLATE = 'modern/latest';

    const STYLING_TABLE = 'touchize_variables';

    const STYLING_TABLE_PREVIEW = 'touchize_variables_preview';

    const USER_CREATE_URL = 'https://themecreator.touchize.com/?rest_route=/tzcb/v1/create_user';

    const CLIENT_CREATE_URL = 'https://themecreator.touchize.com/?rest_route=/tzcb/v1/create_client';

    const CLIENT_BUILDER_URL = 'https://themecreator.touchize.com/';

    const TOUCHIZE_MAIN_COLOR = '#009CDE';

    const IMAGE_CDN_PATH = 'https://d2kt9xhiosnf0k.cloudfront.net/tz-images/';

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->helperMenu = new TouchizeTopMenuHelper();
        parent::__construct();
    }

    /**
     * @see AdminController->init();
     */
    public function init()
    {
        parent::init();
        # Just redirect to the module configuration page if already finished the wizard
        if (Configuration::get('TOUCHIZE_WIZARD_FINISHED') == '1' && (int)Tools::getValue('step') !== 8) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminGetStarted'));
        }
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
                _MODULE_DIR_.'touchize/views/css/bootstrap.min.css',
                _MODULE_DIR_.'touchize/views/css/bootstrap-colorpicker.css',
                _MODULE_DIR_.'touchize/views/css/octicons.min.css',
                _MODULE_DIR_.'touchize/views/css/cpicker.min.css',
                _MODULE_DIR_.'touchize/views/css/wizard.css'
            )
        );

        $this->context->controller->addJS(
            array(
                _MODULE_DIR_.'touchize/views/js/cpicker.min.js',
                _MODULE_DIR_.'touchize/views/js/touchize-setupwizard.js',
            )
        );

        if (((int)Tools::getValue('step')) === 7) {
            $wizardLogo = Configuration::get('TOUCHIZE_PREVIEW_LOGO');
            $prestashopLogo = Configuration::get('PS_LOGO');

            $link = $this->context->link;
            $logo = !empty($wizardLogo)
                ? $link->getMediaLink(_PS_IMG_ . $wizardLogo)
                : $link->getMediaLink(_PS_IMG_ . $prestashopLogo);

            $maincolor = Configuration::get('TOUCHIZE_MAIN_COLOR') != ''
                ? Configuration::get('TOUCHIZE_MAIN_COLOR')
                : self::TOUCHIZE_MAIN_COLOR;

            $single_col = Configuration::get('TOUCHIZE_COLS_SELECTION') == '1'
                ? 'TRUE'
                : 'FALSE';

            $preview_url = $link->getPageLink('index', true);

            $client_settings = Tools::jsonEncode(array(
                'tzcb_logo_url' => $logo,
                'tzcb_primary_color' => $maincolor,
                'tzcb_is_single_column' => $single_col,
                'tzcb_user_preview_url' => $preview_url
            ));

            $start_trial_url = $link->getAdminLink('AdminLicense');

            Media::addJsDef(array(
                'tz_client_settings' => $client_settings,
                'tz_start_trial_url' => $start_trial_url
            ));
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
        $smarty = $this->context->smarty;

        $smarty->assign('is_multishop_mode', false);
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            if (Shop::getContext() != Shop::CONTEXT_SHOP) {
                $smarty->assign('is_multishop_mode', true);
            }
        }
        $smarty->assign('wizardsteps', $this->getWizardStepsURLs());
        $smarty->assign('template_dir', $this->getTemplatePath());
        $smarty->assign('img_dir', self::IMAGE_CDN_PATH);

        $current_step = 0;
        if (Tools::getValue('step')) {
            $current_step = (int)Tools::getValue('step');
        }
        $smarty->assign(
            'current_step',
            $current_step
        );
        $smarty->assign(
            'first_step',
            $this->context->link->getAdminLink('AdminSetupWizard')
        );
        if (Tools::getValue('skip') == 'true') {
            $this->setPossibleDefaultValuesForStep($current_step-1);
        }

        # LOGO.
        # Set logo parameters.
        # If `TOUCHIZE_PREVIEW_LOGO` is missing, wizard will use `PS_LOGO`.
        $wizardLogo = Configuration::get('TOUCHIZE_PREVIEW_LOGO');
        $defaultLogo = Configuration::get('PS_LOGO');
        $logo = _PS_IMG_.($wizardLogo ? $wizardLogo : $defaultLogo);

        $adminEmail = $this->context->employee->email;
        if (empty($adminEmail)) {
            $adminEmail = Configuration::get('PS_SHOP_EMAIL');
        }

        $removeBtnStyle = !empty($wizardLogo)
            ? 'display: inline-block;'
            : 'display: none;';

        $content = '';
        $logoAlertStyle = !is_int(strpos($content, $defaultLogo))
            ? 'display: block;'
            : 'display: none;';

        $smarty->assign(
            'logo',
            $logo
        );
        $smarty->assign(
            'logoAlertStyle',
            $logoAlertStyle
        );
        $smarty->assign(
            'removeBtnStyle',
            $removeBtnStyle
        );
        $smarty->assign(
            'default_color',
            self::TOUCHIZE_MAIN_COLOR
        );
        $smarty->assign(
            'cols_selection',
            Configuration::get('TOUCHIZE_COLS_SELECTION')
        );
        $smarty->assign(
            'main_color',
            Configuration::get('TOUCHIZE_MAIN_COLOR')
        );
        $smarty->assign(
            'menu_items',
            $this->getPossibleLinkMenuItems()
        );
        $smarty->assign(
            'shop_name',
            Configuration::get('PS_SHOP_NAME')
        );
        $smarty->assign(
            'domain_name',
            Tools::getShopDomain()
        );
        $smarty->assign(
            'preview_url',
            $this->context->link->getPageLink('index', true)
        );
        $smarty->assign(
            'shop_email',
            $adminEmail
        );
        if (Configuration::get('TOUCHIZE_LINK_MENU_PRESELECTION') != '' &&
            Configuration::get('TOUCHIZE_LINK_MENU_PRESELECTION') != 'false') {
            $smarty->assign(
                'preselect_all_link_menu_items',
                false
            );
            $smarty->assign(
                'link_menu_preselection',
                Tools::jsonDecode(Configuration::get('TOUCHIZE_LINK_MENU_PRESELECTION'), true)
            );
        } else {
            $smarty->assign(
                'preselect_all_link_menu_items',
                true
            );
            $smarty->assign(
                'link_menu_preselection',
                array()
            );
        }

        $helperTreeCategories = new HelperTreeCategories('categories-treeview');
        $helperTreeCategories->setUseCheckBox(true);
        $smarty->assign(
            'category_tree',
            $helperTreeCategories->render()
        );

        $possibleLandingPageItems = $this->getPossibleLandingPageItems();
        $smarty->assign(
            'landingpage_menu_items',
            $possibleLandingPageItems
        );
        $smarty->assign(
            'landingpage_menu_preselection',
            Configuration::get('TOUCHIZE_LANDING_PAGE_PRESELECTION') != ''
                ? Configuration::get('TOUCHIZE_LANDING_PAGE_PRESELECTION')
                : $this->getLandingPagePreselection(
                    $possibleLandingPageItems
                )
        );

        $banners = $this->getBanners();
        $smarty->assign(
            'banners_items',
            $banners
        );

        $defaultBanner = 0;
        if (sizeof($banners) > 0) {
            if (isset($banners[0]['id_touchize_touchmap'])) {
                $defaultBanner = (int)$banners[0]['id_touchize_touchmap'];
            }
        }

        $smarty->assign(
            'banner_preselection',
            Configuration::get('TOUCHIZE_BANNERS_PRESELECTION') != ''
            ? Configuration::get('TOUCHIZE_BANNERS_PRESELECTION')
            : $defaultBanner
        );
        switch ($current_step) {
            case 4:
                $top_menu_helper = new TouchizeTopMenuHelper();
                Media::addJsDef(array(
                    'top_menu_options' => array(
                        'url' => $this->context->link->getAdminLink(
                            'AdminTopMenuBuilder',
                            true
                        ),
                        'allowed_items' => $top_menu_helper->getJsAllowedItems(),
                        'selected_items' => $top_menu_helper->getSelectedItems(),
                    )
                ));

                $smarty->assign(array(
                    'template_dir' => $this->getTemplatePath()
                ));
                $this->context
                    ->controller
                    ->addCSS(
                        _MODULE_DIR_.'touchize/views/css/top-menu.css'
                    );
                $this->context
                    ->controller
                    ->addJS(
                        array(
                            _MODULE_DIR_.'touchize/views/js/topmenu-loader.js',
                            _MODULE_DIR_.'touchize/views/js/jquery.nestable.js',
                        )
                    );
                break;
            case 8:
                $errorResponse = Configuration::get('TOUCHIZE_SETUPWIZARD_ERRORMESSAGE');
                if ($errorResponse) {
                    $errorResponse = Tools::jsonDecode($errorResponse, true);
                    $errorMsg = $errorResponse['errormsg'];
                } else {
                    $errorMsg = $this->l('Failed creating theme.');
                }
                $smarty->assign(
                    'setupwizard_errormessage',
                    $errorMsg
                );
                $smarty->assign(
                    'adminGetStartedUrl',
                    $this->context->link->getAdminLink('AdminGetStarted')
                );
                $smarty->assign(
                    'clientBuilderUrl',
                    self::CLIENT_BUILDER_URL.
                    "?shop=".Configuration::get('TOUCHIZE_PS_SHOP_NAME').
                    "&email=".$adminEmail.
                    "&domain=".Configuration::get('TOUCHIZE_DOMAIN_NAME')
                );
                Configuration::deleteByName('TOUCHIZE_SETUPWIZARD_ERRORMESSAGE');
                break;
        }
        $smarty->assign(
            'wizardstepinclude',
            $this->createTemplate('setupwizard/_step' . $current_step . '.tpl')->fetch()
        );

        $smarty->assign(array(
            'content' => $this->content
                .$this->createTemplate(self::TEMPLATE_FILE)->fetch(),
        ));
    }

    /**
     * To upload logo-file to the server.
     * To return json-string with a result of operation completion.
     *
     * @return string
     */
    public function ajaxProcessUpdateLogo()
    {
        # Get the logo.
        $file = (isset($_FILES['logo']) && !empty($_FILES['logo']))
            ? $_FILES['logo']
            : null;

        # If logo was received.
        if ($file) {
            # Get validation error.
            $error = ImageManager::validateUpload(
                $file,
                Tools::getMaxUploadSize()
            );

            # If no error.
            if (!$error) {
                # Get file info by file path.
                $fileinfo = pathinfo($file['name']);
                # Set name of file for upload.
                $fileName = 'logo_touchize.'.$fileinfo['extension'];
                # Set name of file for converting.
                $convertedFileName = 'logo_'.time().'.jpg';

                # If file was uploaded successfully.
                if (move_uploaded_file(
                    $file['tmp_name'],
                    _PS_IMG_DIR_.$fileName
                )) {
                    # Convert uploaded file to .jpg.
                    $convertedFile = ImageManager::resize(
                        _PS_IMG_DIR_.$fileName,
                        _PS_IMG_DIR_.$convertedFileName
                    );

                    # Remove old logo file
                    $oldFile = Configuration::get('TOUCHIZE_PREVIEW_LOGO');
                    if (!empty($oldFile) &&
                        $oldFile != Configuration::get('TOUCHIZE_LOGO')
                    ) {
                        @unlink(_PS_IMG_DIR_.$oldFile);
                    }

                    # If converting and updating
                    # of the config variable were finished successfully.
                    if ($convertedFile && Configuration::updateValue(
                        'TOUCHIZE_PREVIEW_LOGO',
                        $convertedFileName
                    )) {
                        # Delete original file.
                        @unlink(_PS_IMG_DIR_.$fileName);
                        # Set response(success).
                        $response = Tools::jsonEncode(array(
                            'error' => false,
                            'message' => Tools::displayError(
                                $this->l('File was uploaded successfully.')
                            ),
                        ));
                    } else {
                        # Set response(fail).
                        $response = Tools::jsonEncode(array(
                            'error' => true,
                            'message' => Tools::displayError(
                                $this->l('An error occurred while attempting to copy your logo.')
                            ),
                        ));
                    }
                } else {
                    # Set response(fail).
                    $response = Tools::jsonEncode(array(
                        'error' => true,
                        'message' => Tools::displayError(
                            $this->l('An error occurred while file uploading.')
                        ),
                    ));
                }
            } else {
                # Set response(fail).
                $response = Tools::jsonEncode(array(
                    'error' => true,
                    'message' => Tools::displayError($this->l($error)),
                ));
            }
        } else {
            # Set response(fail).
            $response = Tools::jsonEncode(array(
                'error' => true,
                'message' => Tools::displayError($this->l('File is missing.')),
            ));
        }

        header('Content-type: application/json');

        $this->ajaxDie($response);
    }

    /**
     * Process the different steps and set config values
     *
     * @return string
     */
    public function ajaxProcessProcessStep()
    {
        switch (Tools::getValue('step')) {
            case '2':
                Configuration::updateValue('TOUCHIZE_COLS_SELECTION', pSQL(Tools::getValue('touchize_cols')));
                Configuration::updateValue('TOUCHIZE_MAIN_COLOR', pSQL(Tools::getValue('main_color')));
                $response = Tools::jsonEncode(array(
                    'error' => false,
                    'url' => $this->getNextWizardStepURL(Tools::getValue('step')),
                ));
                break;
            case '3':
                Configuration::updateValue(
                    'TOUCHIZE_LINK_MENU_PRESELECTION',
                    Tools::jsonEncode(Tools::getValue('menu_preselection'))
                );
                $response = Tools::jsonEncode(array(
                    'error' => false,
                    'url' => $this->getNextWizardStepURL(Tools::getValue('step')),
                ));
                break;
            case '4':
                $response = Tools::jsonEncode(array(
                    'error' => false,
                    'url' => $this->getNextWizardStepURL(Tools::getValue('step')),
                ));
                break;
            case '5':
                Configuration::updateValue(
                    'TOUCHIZE_LANDING_PAGE_PRESELECTION',
                    Tools::getValue('landingpage_preselection')
                );
                $response = Tools::jsonEncode(array(
                    'error' => false,
                    'url' => $this->getNextWizardStepURL(Tools::getValue('step')),
                ));
                break;
            case '6':
                Configuration::updateValue(
                    'TOUCHIZE_BANNERS_PRESELECTION',
                    Tools::getValue('banner_preselection')
                );
                $response = Tools::jsonEncode(array(
                    'error' => false,
                    'url' => $this->getNextWizardStepURL(Tools::getValue('step')),
                ));
                break;
            case '7':
                Configuration::updateValue(
                    'TOUCHIZE_PS_SHOP_EMAIL',
                    Tools::getValue('touchize_ps_shop_email')
                );
                Configuration::updateValue(
                    'TOUCHIZE_PS_SHOP_NAME',
                    Tools::getValue('touchize_ps_shop_name')
                );
                Configuration::updateValue(
                    'TOUCHIZE_DOMAIN_NAME',
                    Tools::getValue('touchize_domain_name')
                );
                $touchizeCdnCode = (string)Tools::getValue('touchize_cdn_code');
                if ($touchizeCdnCode &&
                    !empty($touchizeCdnCode) &&
                    Validate::isUrl($touchizeCdnCode)
                ) {
                    Configuration::updateValue(
                        'TOUCHIZE_CDN_CODE',
                        $touchizeCdnCode
                    );
                }
                $redirectURL = $this->processFinalize(false);


                Configuration::updateValue(
                    'TOUCHIZE_PWA_NAME',
                    Tools::getValue('touchize_ps_shop_name')
                );
                Configuration::updateValue(
                    'TOUCHIZE_PWA_SHORTNAME',
                    Tools::getValue('touchize_ps_shop_name')
                );
                Configuration::updateValue(
                    'TOUCHIZE_PWA_START_URL',
                    $this->context->link->getPageLink('index', true)
                );
                $touchizeMainColor = Configuration::get('TOUCHIZE_MAIN_COLOR') != ''
                        ? Configuration::get('TOUCHIZE_MAIN_COLOR')
                        : self::TOUCHIZE_MAIN_COLOR;
                Configuration::updateValue(
                    'TOUCHIZE_PWA_THEME_COLOR',
                    $touchizeMainColor
                );
                Configuration::updateValue('TOUCHIZE_PWA_BACKGROUND_COLOR', '#ffffff');

                $response = Tools::jsonEncode(array(
                    'error' => false,
                    'url' => $redirectURL,
                ));

                break;
            default:
                $error = Tools::getValue('error') == 'true' ? true:false;
                if ($error) {
                    $response = Tools::jsonEncode(array(
                        'error' => true,
                        'errormsg' => Tools::getValue('response')
                    ));

                    Configuration::updateValue(
                        'TOUCHIZE_SETUPWIZARD_ERRORMESSAGE',
                        $response
                    );

                    $url = $this->context->link->getAdminLink('AdminSetupWizard') . '&step=8';
                } else {
                    $url = $this->getNextWizardStepURL(Tools::getValue('step'));
                }

                $response = Tools::jsonEncode(array(
                    'error' => $error,
                    'url' => $url,
                ));
                break;
        }

        header('Content-type: application/json');
        $this->ajaxDie($response);
    }

    /**
     * To return all styling variables for the live/preview mode.
     *
     * @param  string $mode
     *
     * @return array
     */
    public static function getAllVariables($mode = 'live')
    {
        $table = 'live' == $mode
            ? self::STYLING_TABLE
            : self::STYLING_TABLE_PREVIEW;

        $template = Configuration::get('TOUCHIZE_PREVIEW_CDN_CODE');

        $variables = array();
        $sql = self::buildGetAllVariablesSql($table, $template, null, null);
        foreach (Db::getInstance()->executeS($sql) as $variable) {
            $variables[] = $variable;
        }

        $id_shop = Shop::getContextShopID(true);
        $id_shop_group = Shop::getContextShopGroupID(true);

        $sql = self::buildGetAllVariablesSql($table, $template, $id_shop, $id_shop_group);

        if ($subshop_variables = Db::getInstance()->executeS($sql)) {
            foreach ($subshop_variables as $variable) {
                foreach ($variables as &$main_variable) {
                    if ($main_variable['name'] == $variable['name']) {
                        $main_variable = $variable;
                    }
                }
            }
        }

        return $variables;
    }

    /**
     * Sets all default values if user skips one step
     *
     * @param int $step
     */
    public function setPossibleDefaultValuesForStep($step)
    {
        switch ((int)$step) {
            case 2:
                Configuration::deleteByName('TOUCHIZE_PREVIEW_LOGO');
                if (Configuration::get('TOUCHIZE_COLS_SELECTION') == '') {
                    Configuration::updateValue('TOUCHIZE_COLS_SELECTION', '2');
                }
                if (Configuration::get('TOUCHIZE_MAIN_COLOR') == '') {
                    Configuration::updateValue('TOUCHIZE_MAIN_COLOR', pSQL(self::TOUCHIZE_MAIN_COLOR));
                }
                break;
            case 3:
                if (Configuration::get('TOUCHIZE_LINK_MENU_PRESELECTION') == '') {
                    $defaultItems = array();
                    foreach ($this->getPossibleLinkMenuItems() as $item => $itemName) {
                        if (Tools::substr($item, 0, 3) == 'cms') {
                            $defaultItems[] = $item;
                        }
                    }
                    Configuration::updateValue(
                        'TOUCHIZE_LINK_MENU_PRESELECTION',
                        Tools::jsonEncode($defaultItems)
                    );
                }
                break;
            case 4:
                if (Configuration::get('TOUCHIZE_LINK_CATEGORIES_PRESELECTION') == '') {
                    $helperTreeCategories = new HelperTreeCategories('categories-treeview');
                    $categoryData = $helperTreeCategories->getData();
                    $preselecteCategories = $this->getPreselectedCategories($categoryData);
                    Configuration::updateValue(
                        'TOUCHIZE_LINK_CATEGORIES_PRESELECTION',
                        Tools::jsonEncode($preselecteCategories)
                    );
                }
                break;
            case 5:
                if (Configuration::get('TOUCHIZE_LANDING_PAGE_PRESELECTION') == '') {
                    $possibleLandingPageItems = $this->getPossibleLandingPageItems();
                    Configuration::updateValue(
                        'TOUCHIZE_LANDING_PAGE_PRESELECTION',
                        $this->getLandingPagePreselection($possibleLandingPageItems)
                    );
                }
                break;
        }
    }

    /**
     * Finalize and throw user to Admin Controller
     * If redirect === false -> ajax response!
     *
     * @param bool $redirect
     *
     * @return string
     */
    protected function processFinalize($redirect)
    {
        # Set to completed in case of something fails
        Configuration::updateValue('TOUCHIZE_WIZARD_FINISHED', '1');

        $error = Tools::getValue('error') == 'true' ? true:false;
        $response = '';
        if ($error) {
            $response = Tools::jsonEncode(array(
                'error' => true,
                'errormsg' => Tools::getValue('response')
            ));
        }

        # Link Menu
        $this->prepareAndSaveTouchizeLinkMenuItems(
            Configuration::get('TOUCHIZE_LINK_MENU_PRESELECTION')
        );

        # Saving Landingpage
        Configuration::updateValue(
            'TOUCHIZE_START_CATEGORY_ID',
            Configuration::get('TOUCHIZE_LANDING_PAGE_PRESELECTION')
        );

        # Activating Touchmap
        if ((int)Configuration::get('TOUCHIZE_BANNERS_PRESELECTION') > 0) {
            $touchmap = new TouchizeTouchmap((int)Configuration::get('TOUCHIZE_BANNERS_PRESELECTION'));
            $touchmap->active = 1;
            $touchmap->simpleUpdate();
        }

        # Compile LESS
        //$this->compileLESS();

        # Delete temporary DB configdata
        Configuration::deleteByName('TOUCHIZE_LINK_MENU_PRESELECTION');
        Configuration::deleteByName('TOUCHIZE_LINK_CATEGORIES_PRESELECTION');
        Configuration::deleteByName('TOUCHIZE_LANDING_PAGE_PRESELECTION');
        Configuration::deleteByName('TOUCHIZE_BANNERS_PRESELECTION');

        $adminURL = $this->context->link->getAdminLink('AdminGetStarted');
        if ($redirect) {
            Tools::redirectAdmin($adminURL);
        } elseif ($error) {
            Configuration::updateValue(
                'TOUCHIZE_SETUPWIZARD_ERRORMESSAGE',
                $response
            );
            $adminURL = $this->context->link->getAdminLink('AdminSetupWizard') . '&step=8';
        }
        return $adminURL;
    }

    /**
     *
     * Build SQL to get all Vars
     *
     * @param $table
     * @param $template
     * @param $id_shop
     * @param $id_shop_group
     * @return string
     */
    protected static function buildGetAllVariablesSql($table, $template, $id_shop, $id_shop_group)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.pSQL($table).'`';
        if ($template) {
            $sql .= ' WHERE `template` ="'.pSQL($template).'"';
        } else {
            $sql .= ' WHERE `template` ="'.self::BASIC_TEMPLATE.'"';
        }
        $sql.= TouchizeBaseHelper::sqlRestriction($id_shop_group, $id_shop);
        return $sql;
    }

    /**
     * To generate .css file on the base of the change of the preview mode
     * that overrides existent styles for the website.
     *
     * @param  string|null $fileName
     *
     * @return bool
     */
    // protected function compileLESS($fileName = null)
    // {
    //     $link = new Link();

    //     $wizardLogo = Configuration::get('TOUCHIZE_PREVIEW_LOGO');
    //     $prestashopLogo = Configuration::get('PS_LOGO');

    //     $logo = !empty($wizardLogo)
    //         ? $link->getMediaLink(_PS_IMG_.$wizardLogo)
    //         : $link->getMediaLink(_PS_IMG_.$prestashopLogo);

    //     $less = new lessc();
    //     $variables  = self::getAllVariables('preview');
    //     $input_vars = array(
    //         'brand-logo-background' => '"//'.$logo.'"',
    //     );

    //     if ($variables) {
    //         foreach ($variables as $variable) {
    //             if (!empty($variable['value'])) {
    //                 $input_vars[$variable['name']] = $variable['value'];
    //             }
    //         }
    //     }

    //     $destination = $fileName
    //         ? _PS_MODULE_DIR_.'touchize/views/css/'.$fileName
    //         : _PS_MODULE_DIR_.'touchize/views/css/override_preview.css';

    //     $less->setVariables($input_vars);
    //     $file = _PS_MODULE_DIR_.'touchize/less/index.less';
    //     $less->compileFile(
    //         $file,
    //         $destination
    //     );

    //     return true;
    // }

    /**
     * Query all possible items for link menu
     *
     * @return array
     */
    protected function getPossibleLinkMenuItems()
    {
        $items = array();
        $cms_items = CMS::getCMSPages((int)$this->context->language->id);
        foreach ($cms_items as $item) {
            $items['cms_' . $item['id_cms']] = $item['meta_title'];
        }

        $seoAndUrl = TouchizeMenuBuilder::getSeoAndUrlsPages((int)$this->context->language->id);
        if ($seoAndUrl) {
            foreach ($seoAndUrl as $val) {
                if (trim($val['title']) != '') {
                    $items['site_' . $val['id_meta']] = $val['title'];
                }
            }
        }

        return $items;
    }

    /**
     * Fetch all possible items for landing page
     *
     * @return array
     */
    protected function getPossibleLandingPageItems()
    {
        $possible_items = array(
            'prices-drop' => $this->getNameForItem('prices-drop'),
            'best-sales' => $this->getNameForItem('best-sales'),
            'new-products' => $this->getNameForItem('new-products')
        );

        $helperTreeCategories = new HelperTreeCategories('categories-treeview');
        $helperTreeCategories->setChildrenOnly(true);
        $categoryData = $helperTreeCategories->getData();
        foreach ($categoryData as $c) {
            $possible_items[$c['id_category']] = $c['name'];
        }
        return $possible_items;
    }

    /**
     * @param array $possibleItems
     *
     * @return id
     */
    protected function getLandingPagePreselection($possibleItems)
    {
        # Set Cart & Customer if not in context, otherwise the query for price-drops will crash
        if (!Context::getContext()->cart) {
            Context::getContext()->cart = new Cart();
        }
        if (!Context::getContext()->customer) {
            Context::getContext()->customer = new Customer();
        }
        foreach ($possibleItems as $id => $title) {
            if (sizeof($this->getProductList($id, 6)) >= 6) {
                return $id;
            }
        }
    }

    /**
     * get Preselected Categories
     *
     * @param array $tree
     * @return array
     */
    protected function getPreselectedCategories($tree)
    {
        if (isset($tree[Configuration::get('PS_HOME_CATEGORY')])) {
            $homeCategory = $tree[Configuration::get('PS_HOME_CATEGORY')];
            if (isset($homeCategory['children']) && is_array($homeCategory['children'])) {
                $return = array();
                foreach ($homeCategory['children'] as $c) {
                    $return[] = $c['id_category'];
                }
            }
            if (sizeof($return) > 0) {
                return $return;
            }
        }
        return $tree;
    }

    /**
     * Prepares preselected items for final storage
     *
     * @param $items
     * @return string
     */
    protected function prepareTouchizeTopMenuItems($items)
    {
        $return = array();
        $items = Tools::jsonDecode($items, true);
        foreach ($items as $item_id) {
            if (is_numeric($item_id)) {
                $category = new Category((int)$item_id);
                if ($category->level_depth == 2) {
                    $category_array = array(
                        'name' => $category->getName(),
                        'id' => $item_id
                    );
                    $children = $this->getPossibleCategoryChildren($category, $items);
                    if ($children) {
                        $category_array['is_open'] = true;
                        $category_array['children'] = $children;
                    }
                    $return[] = $category_array;
                }
            } else {
                $return[] = array(
                    'name' => $this->getNameForItem($item_id),
                    'id' => $item_id
                );
            }
        }
        return Tools::jsonEncode($return);
    }

    /**
     * @param $current_category
     * @param $items
     * @return array|bool
     */
    protected function getPossibleCategoryChildren($current_category, $items)
    {
        $return = array();
        foreach ($items as $item_id) {
            if (is_numeric($item_id)) {
                $category = new Category((int)$item_id);
                if ($current_category->id == $category->id_parent) {
                    $category_array = array(
                        'name' => $category->getName(),
                        'id' => $item_id
                    );
                    $children = $this->getPossibleCategoryChildren($category, $items);
                    if ($children) {
                        $category_array['is_open'] = true;
                        $category_array['children'] = $children;
                    }
                    $return[] = $category_array;
                }
            }
        }
        if (sizeof($return) > 0) {
            return $return;
        }
        return false;
    }

    /**
     * Prepares preselected items for final storage
     *
     * @param $items
     * @return bool
     */
    protected function prepareAndSaveTouchizeLinkMenuItems($items)
    {
        $items = Tools::jsonDecode($items, true);
        foreach ($items as $item) {
            $menuBuilder = new TouchizeMenuBuilder();
            $menuBuilder->type = 'menu-item';
            if (Tools::substr($item, 0, 4) == 'cms_') {
                $menuBuilder->action = 'cms_page';
                $menuBuilder->cms_page = (int)Tools::str_replace_once('cms_', '', $item);
            } elseif (Tools::substr($item, 0, 5) == 'site_') {
                $menuBuilder->action = 'page';
                $menuBuilder->page = (int)Tools::str_replace_once('site_', '', $item);
            }
            $menuBuilder->add();
        }
        return true;
    }

    /**
     * @param int|string $item_id
     * @return string
     */
    protected function getNameForItem($item_id)
    {
        switch ($item_id) {
            case 'prices-drop':
                return $this->l('Specials');
            case 'best-sales':
                return $this->l('Best sellers');
            case 'new-products':
                return $this->l('New arrivals');
        }
    }

    /**
     * Query the available touchmaps for selection
     *
     * @return array
     */
    protected function getBanners()
    {
        $touchmaps =  TouchizeTouchmap::getTouchmaps(-1, false, null, null, true);
        foreach ($touchmaps as &$tm) {
            $tm['imageurl'] = $this->context->link->getMediaLink(
                _PS_IMG_.'touchmaps/'.$tm['id_touchize_touchmap'].'.jpg'
            );
        };
        return $touchmaps;
    }

    /**
     * Returns next step URL
     *
     * @param $current_step
     * @return string
     * @throws PrestaShopException
     */
    private function getNextWizardStepURL($current_step)
    {
        return $this->context->link->getAdminLink('AdminSetupWizard') . '&step=' . ((int)$current_step+1);
    }

    /**
     * Build an Array with all wizard steps for use in tpl files
     *
     * @return array
     * @throws PrestaShopException
     */
    private function getWizardStepsURLs()
    {
        $wizardsteps = array();
        $baselink = $this->context->link->getAdminLink('AdminSetupWizard');
        for ($x=2; $x<=6; $x++) {
            $wizardsteps[] = $baselink . '&step=' . $x;
        }
        return $wizardsteps;
    }

    /**
     * Walk through an array and get all values by specific key
     *
     * @param array $haystack
     * @param $needle
     * @return array
     */
    protected function arrayColumnRecursive(array $haystack, $needle)
    {
        $found = array();
        array_walk_recursive($haystack, function ($value, $key) use (&$found, $needle) {
            if ($key == $needle) {
                $found[] = $value;
            }
        });
        return $found;
    }

    /**
     * Get list of products from category
     *
     * @param  int      $categoryId         Id of category
     * @param  int      $maxProducts
     *
     * @return array                        SLQ Productlist
     */
    public function getProductList(
        $categoryId,
        $maxProducts = 6
    ) {
        $page = 1;
        if (Validate::isInt($categoryId)) {
            $category = new Category($categoryId);

            $list = $category->getProducts(
                $this->context->language->id,
                $page,
                $maxProducts,
                null,
                null,
                false,
                true,
                false,
                $maxProducts
            );
        } else {
            # Since startpage is 0 for these -categories-
            $temppage = ($page - 1 < 0)
                ? 0
                : $page - 1;

            switch ($categoryId) {
                case 'new-products':
                    $list = Product::getNewProducts(
                        $this->context->language->id,
                        $temppage,
                        $maxProducts
                    );
                    break;
                case 'best-sales':
                    $list = ProductSale::getBestSales(
                        $this->context->language->id,
                        $temppage,
                        $maxProducts
                    );
                    break;
                case 'prices-drop':
                    $list = Product::getPricesDrop(
                        $this->context->language->id,
                        $temppage,
                        $maxProducts
                    );
                    break;
                default:
                    break;
            }
        }

        if (empty($list)) {
            return array();
        }
        return $list;
    }
}
