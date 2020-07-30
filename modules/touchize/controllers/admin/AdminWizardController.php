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
* Wizzard controller.
*/

class AdminWizardController extends BaseTouchizeController
{
    const TEMPLATE_FILE = 'tabs/wizard.tpl';

    const INFO_TEMPLATE = 'info/wizard.tpl';

    const STYLING_TABLE = 'touchize_variables';

    const STYLING_TABLE_PREVIEW = 'touchize_variables_preview';

    const BASIC_TEMPLATE = 'modern/latest';

    const CLIENT_BUILDER_URL = 'https://themecreator.touchize.com/';

    const USER_CREATE_URL = 'https://themecreator.touchize.com/?rest_route=/tzcb/v1/create_user';

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
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
                _MODULE_DIR_.'touchize/views/css/bootstrap-colorpicker.css',
                _MODULE_DIR_.'touchize/views/css/octicons.min.css',
                _MODULE_DIR_.'touchize/views/css/cpicker.min.css',
                _MODULE_DIR_.'touchize/views/css/wizard.css'
            )
        );

        $this->context->controller->addJS(
            array(
                _MODULE_DIR_.'touchize/views/js/cpicker.min.js',
                _MODULE_DIR_.'touchize/views/js/botab.js',
                _MODULE_DIR_.'touchize/views/js/touchize-wizard.js',
            )
        );
    }

    /**
     * AdminController::initContent() override
     *
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        # TEMPLATE.
        # Each template element has a list of parameters:
        # 1. src - path to the preview image of the template;
        # 2. value - PREVIEW_CDN_CODE of the template;
        # 3. checked - parameter for determining of the active template.
        $template = Configuration::get('TOUCHIZE_PREVIEW_CDN_CODE');
        $templates = array(
            'template_1' => array(
                'src' => _MODULE_DIR_
                    .'touchize/views/img/templates/classic.jpg',
                'value' => 'classic/latest',
                'checked' => (!empty($template) &&
                    'classic/latest' == $template)
                    ? "checked"
                    : "",
            ),
            'template_2' => array(
                'src' => _MODULE_DIR_
                    .'touchize/views/img/templates/lines.jpg',
                'value' => 'lines/latest',
                'checked' => (!empty($template) &&
                    'lines/latest' == $template)
                    ? "checked"
                    : "",
            ),
            'template_3' => array(
                'src' => _MODULE_DIR_
                    .'touchize/views/img/templates/clean.jpg',
                'value' => 'clean/latest',
                'checked' => (!empty($template) &&
                    'clean/latest' == $template)
                    ? "checked"
                    : "",
            ),
            'template_4' => array(
                'src' => _MODULE_DIR_
                    .'touchize/views/img/templates/modern.jpg',
                'value' => 'modern/latest',
                'checked' => (!empty($template) &&
                    'modern/latest' == $template)
                    ? "checked"
                    : "",
            ),
        );

        # LOGO.
        # Set logo parameters.
        # If `TOUCHIZE_PREVIEW_LOGO` is missing, wizard will use `PS_LOGO`.
        $wizardLogo = Configuration::get('TOUCHIZE_PREVIEW_LOGO');
        $defaultLogo = Configuration::get('PS_LOGO');
        $logo = _PS_IMG_.($wizardLogo ? $wizardLogo : $defaultLogo);


        $removeBtnStyle = !empty($wizardLogo)
            ? 'display: inline-block;'
            : 'display: none;';

        $content = Tools::file_get_contents(
            $_SERVER['DOCUMENT_ROOT']._MODULE_DIR_.'touchize/views/css/override' .
            TouchizeBaseHelper::getCSSFileAddition() . '.css'
        );

        $logoAlertStyle = !is_int(strpos($content, $defaultLogo))
            ? 'display: block;'
            : 'display: none;';


        # STYLING.
        # Get all styling variables from database.
        $stylingVariables = self::getAllVariables('preview');

        # SMARTY.
        # Get smarty and assign variables to it.
        $smarty = $this->context->smarty;

        $is_confirmed = Configuration::get('TOUCHIZE_PS_SHOP_EMAIL');
        $smarty->assign(
            'is_confirmed',
            $is_confirmed
        );
        if (!$is_confirmed) {
            $smarty->assign(
                'shop_name',
                Configuration::get('PS_SHOP_NAME')
            );
            $smarty->assign(
                'domain_name',
                Tools::getShopDomain()
            );
            $smarty->assign(
                'shop_email',
                Configuration::get('PS_SHOP_EMAIL')
            );
        } else {
            $smarty->assign(
                'clientBuilderUrl',
                self::CLIENT_BUILDER_URL.
                "?shop=".Configuration::get('TOUCHIZE_PS_SHOP_NAME').
                "&email=".Configuration::get('TOUCHIZE_PS_SHOP_EMAIL').
                "&domain=".Configuration::get('TOUCHIZE_DOMAIN_NAME')
            );
        }

        $smarty->assign('img_dir', AdminSetupWizardController::IMAGE_CDN_PATH);

        $smarty->assign(
            'templates',
            $templates
        );
        $smarty->assign(
            'logo',
            $logo
        );
        $smarty->assign(
            'logoAlertStyle',
            $logoAlertStyle
        );
        $smarty->assign(
            'stylingVariables',
            $stylingVariables
        );
        $smarty->assign(
            'removeBtnStyle',
            $removeBtnStyle
        );
        $helper = new TouchizeAdminHelper();
        $helper->assignMenuVars();

        if (!$this->isSingleShop()) {
            $smarty->assign(array(
                'content' => $this->createTemplate('builder/warning.tpl')->fetch()
            ));
            return ;
        }
        $smarty->assign(array(
            'menu' => $helper->getTemplate('partials/menu.tpl')
        ));
        $smarty->assign(array(
            'content' => $this->content
                .$this->createTemplate(self::TEMPLATE_FILE)->fetch(),
        ));
    }

    /**
     * Process the different steps and set config values
     *
     * @return string
     */
    public function ajaxProcessUpdateConfirmation()
    {
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
        if (!class_exists('\Httpful') && !class_exists('\Httpful\Bootstrap')) {
            require_once(_PS_MODULE_DIR_ . 'touchize/lib/httpful/httpful.phar');
        }

        try {
            $response = \Httpful\Request::post(self::USER_CREATE_URL)
                ->body(array(
                    'email' => Configuration::get('TOUCHIZE_PS_SHOP_EMAIL'),
                    'username' => Configuration::get('TOUCHIZE_PS_SHOP_NAME'),
                    'domain' => Configuration::get('TOUCHIZE_DOMAIN_NAME'),
                    'password' => Tools::passwdGen(9)
                ), Httpful\Mime::FORM)
                ->expectsJson()
                ->send()
            ;
        } catch (Exception $e) {
            $response = Tools::jsonEncode(array(
                'error' => true,
                'errormsg' => $e->getMessage()
            ));
        }

        $redirectURL = self::CLIENT_BUILDER_URL.
            "?shop=".Configuration::get('TOUCHIZE_PS_SHOP_NAME').
            "&email=".Configuration::get('TOUCHIZE_PS_SHOP_EMAIL').
            "&domain=".Configuration::get('TOUCHIZE_DOMAIN_NAME');

        if (isset($response->body->errors) &&
            is_array($response->body->errors) &&
            sizeof($response->body->errors) > 0) {
            $response = Tools::jsonEncode(array(
                'error' => true,
                'errormsg' => join(', ', $response->body->errors),
                'url' => $redirectURL
            ));
        } else {
            $response = Tools::jsonEncode(array(
                'success' => true,
                'url' => $redirectURL
            ));
        }
        header('Content-type: application/json');
        $this->ajaxDie($response);
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

        $this->compileLESS();

        header('Content-type: application/json');

        $this->ajaxDie($response);
    }

    /**
     * To save the logo for the preview mode.
     * To return json-string with a result of operation completion.
     *
     * @return string
     */
    public function ajaxProcessSaveLogo()
    {
        $oldLogo = Configuration::get('TOUCHIZE_LOGO');
        $previewLogo = Configuration::get('TOUCHIZE_PREVIEW_LOGO');
        if (Configuration::updateValue(
            'TOUCHIZE_LOGO',
            $previewLogo
        ) && (!empty($previewLogo) && !empty($oldLogo)) &&
              $previewLogo != $oldLogo) {
            @unlink(_PS_IMG_DIR_.$oldLogo);
            self::compileLESS('override' . TouchizeBaseHelper::getCSSFileAddition() . '.css');
        }

        $this->ajaxDie(Tools::jsonEncode(array(
            'error' => false,
            'message' => Tools::displayError($this->l('Logo was successfully saved.')),
        )));
    }

    /**
     * To remove the logo for the preview mode.
     * To return json-string with a result of operation completion.
     *
     * @return string
     */
    public function ajaxProcessRemoveLogo()
    {
        $touchPreviewLogo = Configuration::get('TOUCHIZE_PREVIEW_LOGO');
        $touchLogo = Configuration::get('TOUCHIZE_LOGO');
        $defaultLogo = Configuration::get('PS_LOGO');

        # If preview logo was removed.
        if (empty($touchPreviewLogo)) {
            die(Tools::jsonEncode(array(
                'error' => true,
                'message' => Tools::displayError(
                    $this->l('Logo is missing.')
                ),
            )));
        }

        # Clean up `preview` variable.
        if (Configuration::updateValue(
            'TOUCHIZE_PREVIEW_LOGO',
            null
        )) {
            # Rewrite logo path for `preview` mode.
            self::compileLESS('override_preview.css');
        } else {
            die(Tools::jsonEncode(array(
                'error' => true,
                'message' => Tools::displayError(
                    $this->l('An error has occurred when logo was removed.')
                ),
            )));
        }

        # If `preview` logo is not equal to `live` logo.
        if ($touchPreviewLogo != $touchLogo) {
            @unlink(_PS_IMG_DIR_.$touchPreviewLogo);
        }

        header('Content-type: application/json');

        $this->ajaxDie(Tools::jsonEncode(array(
            'error' => false,
            'message' => Tools::displayError(
                $this->l('Logo was successfully removed.')
            ),
            'defaultLogo' => $defaultLogo,
        )));
    }

    /**
     * To rewrite .css files by ajax request.
     *
     * @return string
     */
    public function ajaxProcessSyncLogo()
    {
        if (self::compileLESS('override' . TouchizeBaseHelper::getCSSFileAddition() . '.css') &&
            self::compileLESS('override_preview.css')
        ) {
            $response = array(
                'error' => false,
                'message' => Tools::displayError(
                    $this->l('Files were successfully rewritten.')
                ),
            );
        } else {
            $response = array(
                'error' => true,
                'message' => Tools::displayError(
                    $this->l('An error has occurred.')
                ),
            );
        }

        header('Content-type: application/json');

        $this->ajaxDie(Tools::jsonEncode($response));
    }

    /**
     * To update a template.
     * To return json-string with a result of operation completion.
     *
     * @return string
     */
    public function ajaxProcessUpdateTemplate()
    {
        $name = Tools::getValue('name');

        if ($name &&
            !empty($name) ||
            Validate::isUrl($name)
        ) {
            if (Configuration::updateValue(
                'TOUCHIZE_PREVIEW_CDN_CODE',
                $name
            )) {
                $response = Tools::jsonEncode(array(
                    'error' => false,
                    'template' => $name,
                    'styling' => self::getAllVariables('preview'),
                ));
            } else {
                $response = Tools::jsonEncode(array(
                    'error' => true,
                    'message' => Tools::displayError(
                        $this->l('Cannot update template value. Try again later.')
                    ),
                ));
            }
        } else {
            $response = Tools::jsonEncode(array(
                'error' => true,
                'message' => Tools::displayError(
                    $this->l('Missing required values.')
                ),
            ));
        }

        self::compileLESS('override_preview.css');

        header('Content-type: application/json');

        $this->ajaxDie($response);
    }

    /**
     * To save a template.
     * To return json-string with a result of operation completion.
     *
     * @return string
     */
    public function ajaxProcessSaveTemplate()
    {
        header('Content-type: application/json');

        $this->ajaxDie(Tools::jsonEncode(array(
            'error' => false,
            'template' => Tools::getValue('name'),
            'styling' => self::getAllVariables('preview'),
        )));
    }

    /**
     * To update styling variable.
     * To return json-string with a result of operation completion.
     *
     * @return string
     */
    public function ajaxProcessUpdateStylingVariable()
    {
        $id = (int)Tools::getValue('id');
        $value = Tools::getValue('value');

        $currentDate = new DateTime();

        if ($id &&
            Validate::isInt($id) &&
            $value &&
            Validate::isColor($value)
        ) {
            if (TouchizeVariable::insertOrUpdateOnMultishop(
                self::STYLING_TABLE_PREVIEW,
                array(
                    'value' => $value,
                    'date_upd' => $currentDate->format('Y-m-d H:i:s'),
                    ),
                'id_variable = '.$id
            )) {
                $response = Tools::jsonEncode(array(
                    'error' => false,
                    'message' => Tools::displayError(
                        $this->l('Variable value was updated successfully.')
                    ),
                ));
            } else {
                $response = Tools::jsonEncode(array(
                    'error' => true,
                    'message' => Tools::displayError(
                        $this->l('Database error.')
                    ),
                ));
            }
        } else {
            $response = Tools::jsonEncode(array(
                'error' => true,
                'message' => Tools::displayError(
                    $this->l('Missing required values.')
                ),
            ));
        }

        $this->compileLESS();

        header('Content-type: application/json');

        $this->ajaxDie($response);
    }

    /**
     * To restore styling variable.
     * To return json-string with a result of operation completion.
     *
     * @return string
     */
    public function ajaxProcessSaveStylingVariable()
    {
        $previewStyling = self::getAllVariables('preview');
        $styling = self::getAllVariables();
        $currentDate = new DateTime();

        if ($previewStyling && $styling) {
            foreach ($previewStyling as $previewVar) {
                $map = array_map(function ($el) {
                    return $el['id_variable'];
                }, $styling);

                $key = array_search(
                    $previewVar['id_variable'],
                    $map
                );

                if (!$key) {
                    if ($previewVar['id_shop_group'] == '0' || $previewVar['id_shop_group'] == '') {
                        unset($previewVar['id_shop_group']);
                    }
                    if ($previewVar['id_shop'] == '0' || $previewVar['id_shop'] == '') {
                        unset($previewVar['id_shop']);
                    }
                    Db::getInstance()->insert(
                        self::STYLING_TABLE,
                        $previewVar
                    );
                } elseif (is_int($key)) {
                    $styling[$key]['value'] = $previewStyling[$key]['value'];

                    $styling[$key]['date_upd'] = $currentDate->format(
                        'Y-m-d H:i:s'
                    );
                    $styling[$key] = array_map(
                        function ($arr) {
                            return pSQL($arr);
                        },
                        $styling[$key]
                    );

                    Db::getInstance()->update(
                        self::STYLING_TABLE,
                        $styling[$key],
                        'id_variable = '.(int)$styling[$key]['id_variable']
                    );
                }
            }
        }

        self::compileLESS('override' . TouchizeBaseHelper::getCSSFileAddition() . '.css');

        $this->ajaxDie(Tools::jsonEncode(array(
            'error' => false,
            'message' => Tools::displayError(
                $this->l('Style settings saved.')
            ),
        )));
    }

    /**
     * To restore styling variable.
     * To return json-string with a result of operation completion.
     *
     * @return string
     */
    public function ajaxProcessResetStylingVariable()
    {
        $template = Tools::getValue('template');

        if ($template) {
            $json = @Tools::file_get_contents(
                Touchize::CDN_PATH . $template . '/css/simplestyle/defs.json'
            );

            if ($json) {
                $data = json_decode($json);
                $currentDate = new DateTime();

                foreach ($data as $el) {
                    if ($el->Color) {
                        Db::getInstance()->update(
                            self::STYLING_TABLE_PREVIEW,
                            array(
                                'value' => pSQL($el->Value),
                                'date_upd' => pSQL($currentDate->format('Y-m-d H:i:s')),
                            ),
                            'name LIKE "' . pSQL($el->Variable) . '" AND template LIKE "' . pSQL($template) . '"'
                        );

                        Db::getInstance()->update(
                            self::STYLING_TABLE,
                            array(
                                'value' => pSQL($el->Value),
                                'date_upd' => pSQL($currentDate->format('Y-m-d H:i:s')),
                            ),
                            'name LIKE "' . pSQL($el->Variable) . '" AND template LIKE "' . pSQL($template) . '"'
                        );
                    }
                }

                $settings = Db::getInstance()->executeS('
                    SELECT `id_variable`, `value`
                    FROM `' . _DB_PREFIX_ . self::STYLING_TABLE_PREVIEW . '`
                    WHERE `is_color` = 1 AND `template` LIKE "' . pSQL($template) . '"
                ');

                //Delete override_preview file
                Tools::deleteFile(_PS_MODULE_DIR_.'touchize/views/css/override_preview.css');

                $response = Tools::jsonEncode(array(
                    'error' => false,
                    'message' => Tools::displayError(
                        $this->l('Settings successfully restored.')
                    ),
                    'settings' => $settings,
                ));
            } else {
                $response = Tools::jsonEncode(array(
                    'error' => true,
                    'message' => Tools::displayError(
                        $this->l('Default settings for this template is not found.')
                    ),
                ));
            }
        } else {
            $response = Tools::jsonEncode(array(
                'error' => true,
                'message' => Tools::displayError(
                    $this->l('Template is not set.')
                ),
            ));
        }

        header('Content-type: application/json');

        $this->ajaxDie($response);
    }

    /**
     * To apply all changes which were done
     * in the preview mode for the website.
     * To return json-string with a result of operation completion.
     *
     * @return string
     */
    public function ajaxProcessApplyChanges()
    {
        Configuration::updateValue(
            'TOUCHIZE_CDN_CODE',
            Configuration::get('TOUCHIZE_PREVIEW_CDN_CODE')
        );

        $oldLogo = Configuration::get('TOUCHIZE_LOGO');
        $previewLogo = Configuration::get('TOUCHIZE_PREVIEW_LOGO');
        if (Configuration::updateValue(
            'TOUCHIZE_LOGO',
            $previewLogo
        ) &&
        (!empty($previewLogo) && !empty($oldLogo)) && $previewLogo != $oldLogo) {
            @unlink(_PS_IMG_DIR_.$oldLogo);
        }

        $previewStyling = self::getAllVariables('preview');
        $styling = self::getAllVariables();
        $currentDate = new DateTime();

        if ($previewStyling && $styling) {
            foreach ($previewStyling as $previewVar) {
                $map = array_map(function ($el) {
                    return $el['id_variable'];
                }, $styling);

                $key = array_search(
                    $previewVar['id_variable'],
                    $map
                );

                if (is_int($key)) {
                    $styling[$key]['value'] = $previewStyling[$key]['value'];
                    $styling[$key]['date_upd'] = $currentDate->format(
                        'Y-m-d H:i:s'
                    );
                    $styling[$key] = array_map(
                        function ($arr) {
                            return pSQL($arr);
                        },
                        $styling[$key]
                    );

                    Db::getInstance()->update(
                        self::STYLING_TABLE,
                        $styling[$key],
                        'id_variable = '.(int)$styling[$key]['id_variable']
                    );
                }
            }
        }

        self::compileLESS('override' . TouchizeBaseHelper::getCSSFileAddition() . '.css');

        header('Content-type: application/json');

        $this->ajaxDie(Tools::jsonEncode(array(
            'error' => false,
            'message' => Tools::displayError(
                $this->l('All changes were successfully applied.')
            ),
        )));
    }

    /**
     * To generate .css file on the base of the change of the preview mode
     * that overrides existent styles for the website.
     *
     * @param  string|null $fileName
     *
     * @return bool
     */
    protected function compileLESS($fileName = null)
    {
        $link = new Link();

        $wizardLogo = Configuration::get('TOUCHIZE_PREVIEW_LOGO');
        $prestashopLogo = Configuration::get('PS_LOGO');

        $logo = !empty($wizardLogo)
            ? $link->getMediaLink(_PS_IMG_.$wizardLogo)
            : $link->getMediaLink(_PS_IMG_.$prestashopLogo);

        $less = new lessc();
        $variables  = self::getAllVariables('preview');
        $input_vars = array(
            'brand-logo-background' => '"//'.$logo.'"',
        );

        if ($variables) {
            foreach ($variables as $variable) {
                if (!empty($variable['value'])) {
                    $input_vars[$variable['name']] = $variable['value'];
                }
            }
        }

        $destination = $fileName
            ? _PS_MODULE_DIR_.'touchize/views/css/'.$fileName
            : _PS_MODULE_DIR_.'touchize/views/css/override_preview.css';

        $less->setVariables($input_vars);
        $file = _PS_MODULE_DIR_.'touchize/less/index.less';
        $less->compileFile(
            $file,
            $destination
        );

        return true;
    }
}
