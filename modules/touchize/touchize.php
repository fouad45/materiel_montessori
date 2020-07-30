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
 * Touchize TouchFront
 *
 * Redirects to TouchFront cloud based on what controller that is active.
 *
 * TODO: Better to have a Redirect to a Touchize
 * controller and that handles the redirect as a post?
 *
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once 'helpers/Autoloader.php';
require_once 'lib/lessphp/lessc.inc.php';

class Touchize extends Module
{
    const CDN_PATH = 'https://d2kt9xhiosnf0k.cloudfront.net/';
    const CDN_CODE = 'modern/latest';

    /**
     * @var number
     **/
    protected static $accessRights = 0775;

    /**
     * @var array
     */
    public $tabs = array(
        array(
            'name' => array(
                'en' => 'Touchize Commerce',
            ),
            'class_name' => 'AdminGetStarted',
            'icon' => 'phone_iphone',
            'parent_class_name' => 'CONFIGURE'
        ));

    /**
     * @var array
     **/
    private $templates = array(
        'classic/latest',
        'lines/latest',
        'clean/latest',
        'modern/latest'
    );

    private $adminControllers = array(
        'AdminGetStarted',
        'AdminTouchmaps',
        'AdminWizard',
        'AdminMenuBuilder',
        'AdminTopMenuBuilder',
        'AdminSettings',
        'AdminLicense',
        'AdminContactUs',
        'AdminSetupWizard'
    );
    private $adminControllersHaveInfo = array(
        'AdminGetStarted',
        'AdminTouchmaps',
        'AdminWizard',
        'AdminMenuBuilder',
        'AdminTopMenuBuilder',
        'AdminSettings',
        'AdminLicense',
        'AdminContactUs'
    );

    public function __construct()
    {
        $this->name = 'touchize';
        $this->tab = 'mobile';
        $this->version = '1.3.1';
        $this->author = $this->l('Touchize Sweden AB');
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array(
          'min' => '1.6',
          'max' => _PS_VERSION_
        );
        $this->bootstrap = true;

        parent::__construct();
        $this->module_key = 'f19cf81a3cdcf7aa959b1fa69c6fbc6f';

        $this->displayName = $this->l('Touchize Commerce');
        $this->description = $this->l('Swipe-2-Buy makes mobile shopping faster, easier and more enjoyable giving increasing loyalty and boosting mobile sales. Easy one-click installation and 3 months free trial.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->licenseHelper = new TouchizeLicenseHelper();
        $this->revalidate = false;
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $res = true;
        $options = require dirname(__FILE__).'/install/option_install.php';
        foreach ($options as $_option) {
            $res &= Configuration::updateValue(
                $_option['name'],
                $_option['value']
            );
        }

        if (!parent::install() ||
            !$this->registerHook('header') ||
            !$this->registerHook('ModuleRoutes') ||
            !$this->registerHook('displayAdminAfterHeader') ||
            !$this->registerHook('displayBackOfficeHeader') ||
            !$res
        ) {
            return false;
        }

        $this->setModuleTabs();
        $this->createImageDirectory();

        $this->createTables();
        $this->setTableDefaultData();
        $this->insertDefaultBanners();
        $this->setStylingVariables();

        $this->clearCaches();

        return true;
    }

    public function uninstall()
    {
        $res = true;
        $options = require dirname(__FILE__).'/install/option_install.php';
        foreach ($options as $_option) {
            $res &= Configuration::deleteByName($_option['name']);
        }

        if (!parent::uninstall() ||
            !$this->unregisterHook('header') ||
            !$this->unregisterHook('ModuleRoutes') ||
            !$this->unregisterHook('displayBackOfficeHeader') ||
            !$this->unregisterHook('displayAdminAfterHeader') ||
            !$res
        ) {
            return false;
        }

        $this->deleteModuleTabs();
        $this->deleteImageDirectory();
        $this->deleteServiceWorker();

        $this->clearCaches();

        return $this->deleteTables();
    }

    private function clearCaches()
    {
        //Clear the compiled cache
        $this->context->smarty->clearCompiledTemplate(
            $this->getTemplatePath('touchize.tpl')
        );
        //Clear the DB cache if any
        $this->_clearCache('touchize.tpl');
    }

    public function getReroute()
    {
        return $this->context->link->getModuleLink($this->name, 'touchize');
    }

    public function hookHeader($params)
    {
        //$this->revalidate will be set within this call, pass by reference!
        $enableValue = $this->licenseHelper->isTouchizeEnabled($this->revalidate);
        if ($enableValue > 0) {
            $overriddenControllers = array(
                'index',
                'product',
                'category',
                'new-products',
                'best-sales',
                'prices-drop',
                'manufacturer',
                'cms',
                'search'
            );
            if (in_array($this->context->controller->php_self, $overriddenControllers)) {
                header('Vary: User-Agent');
            }
        }
        $isMobile = $this->context->mobile_detect->isMobile();
        $isTablet = $this->context->mobile_detect->isTablet();

        switch ($enableValue) {
            case 0:
                # Disabled
                $enable = false;
                break;
            case 1:
                # Disable if not mobile
                $enable = ($isMobile && !$isTablet) ? true : false;
                break;
            case 2:
                # Disable if not table
                $enable = $isTablet ? true : false;
                break;
            case 3:
                $enable = ($isMobile || $isTablet) ? true : false;
                break;
            default:
                # Disabled by default
                $enable = false;
                break;
        }
        $botab = TouchizeControllerHelper::getParam('botab', false);
        if (!$botab) {
            if (isset($_SERVER['HTTP_REFERER'])) {
                if (strpos($_SERVER['HTTP_REFERER'], 'botab=') !== false) {
                    $botab = true;
                }
            }
        }
        $preview = TouchizeControllerHelper::getParam('preview', false);
        $testTouchize = Tools::getValue('touchize');
        if ($testTouchize == 'yes') {
            $this->context->cookie->__set('touchizetest', 'yes');
            $this->context->cookie->write();
        } elseif ($testTouchize == 'no') {
            $this->context->cookie->__unset('touchizetest');
            $this->context->cookie->write();
        }
        if (isset($this->context->cookie->touchizetest)) {
            $enable = ($isMobile || $isTablet) ? true : false;
        }

        if ($enable || $preview || $botab) {
            if (Module::isInstalled('blocknewsletter') &&
                Module::isEnabled('blocknewsletter')
            ) {
                if (Tools::isSubmit('submitNewsletter')) {
                    # If we get a POST with submitNewsletter, run hook
                    require_once _PS_MODULE_DIR_.
                        'blocknewsletter/blocknewsletter.php';

                    $newsletter = new Blocknewsletter();
                    $newsletter->hookDisplayLeftColumn();
                }
            }

            $pid = $tid = $query = $page = null;
            $url = $this->getReroute();
            if (!empty($url)) {
                $controller = $this->context->controller->php_self;
                switch ($controller) {
                    case 'index':
                        $tid = Configuration::get(
                            'TOUCHIZE_START_CATEGORY_ID'
                        );
                        break;
                    case 'product':
                        $pid = $this->context->controller->getProduct()->id;
                        $tid = $this->context
                                    ->controller
                                    ->getProduct()
                                    ->id_category_default;
                        break;
                    case 'category':
                        $tid = $this->context->controller->getCategory()->id;
                        break;
                    case 'new-products':
                    case 'best-sales':
                    case 'prices-drop':
                        $tid = $controller;
                        break;
                    case 'manufacturer':
                        $mid = Tools::getValue('id_manufacturer');
                        $tid = $controller.$mid;
                        break;
                    case 'cms':
                        $tid  = Configuration::get(
                            'TOUCHIZE_START_CATEGORY_ID'
                        );
                        $page = $this->context->controller->cms->id;
                        break;
                    case 'search':
                        $query = Tools::getValue('search_query');
                        break;
                    default:
                        return false;
                }
                if ($preview) {
                    $this->context->cookie->__set(
                        'router',
                        json_encode(
                            array(
                                'SiteUrl' => $this->context->link->getPageLink('index', true),
                                'tid' => $tid,
                                'pid' => $pid,
                                'page' => $page,
                                'qs' => $_SERVER['REQUEST_URI'],
                                'search' => $query,
                                'queryString' => $_SERVER['QUERY_STRING'],
                                'requestURI' => $_SERVER['REQUEST_URI'],
                                'PreviewUrl' => $this->context->link->getPageLink('index', true)."?preview=1"
                            )
                        )
                    );
                } elseif ($botab) {
                    $this->context->cookie->__set(
                        'router',
                        json_encode(
                            array(
                                'SiteUrl' => $this->context->link->getPageLink('index', true),
                                'tid' => $tid,
                                'pid' => $pid,
                                'page' => $page,
                                'qs' => $_SERVER['REQUEST_URI'],
                                'search' => $query,
                                'queryString' => $_SERVER['QUERY_STRING'],
                                'requestURI' => $_SERVER['REQUEST_URI'],
                                'PreviewUrl' => $this->context->link->getPageLink('index', true)."?botab=1"
                            )
                        )
                    );
                } else {
                    $this->context->cookie->__set(
                        'router',
                        json_encode(
                            array(
                                'SiteUrl' => $this->context->link->getPageLink('index', true),
                                'tid' => $tid,
                                'pid' => $pid,
                                'page' => $page,
                                'qs' => $_SERVER['REQUEST_URI'],
                                'search' => $query,
                                'queryString' => $_SERVER['QUERY_STRING'],
                                'requestURI' => $_SERVER['REQUEST_URI'],
                            )
                        )
                    );
                }

                include(_PS_MODULE_DIR_.'touchize'.DIRECTORY_SEPARATOR.'controllers'.
                DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'touchize.php');
                $_POST['module'] = $this->name;
                $frontController = new TouchizeTouchizeModuleFrontController();
                $frontController->setPreview($preview);
                $frontController->init();

                # $frontController->getLayout() returns different path
                # to template file while changing _PS_MODE_DEV_ value.
                $layout = is_file($frontController->getLayout())
                    ? $frontController->getLayout()
                    : _PS_MODULE_DIR_.$this->name.'/views/templates/front/touchize.tpl';

                $this->context->smarty->assign(array(
                    'display_header' => true,
                    'display_footer' => true
                ));

                $this->context->smarty->display($layout);
                ob_flush();
                flush();
                if ($this->revalidate) {
                    $this->licenseHelper->revalidate();
                }
                die();
            }
        }

        return false;
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCss($this->_path.'views/css/tab.css');
        if (in_array($this->context->controller->controller_name, $this->adminControllers) ||
            Tools::getValue('configure') == 'touchize' && Tools::getValue('config_tab', true)) {
            $this->context->smarty->assign(
                array(
                  'matomo' => $this->createMatomo(),
                  'controller' => $this->context->controller->controller_name,
                )
            );
            return $this->display(__FILE__, 'views/templates/admin/partials/matomo.tpl');
        } else {
            return '';
        }
    }
    protected function createMatomo()
    {
        $urlFull = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        $key = 'token';
        $url = preg_replace('~(\?|&)'.$key.'=[^&]*~', '', $urlFull); //remove token
        $infoStruct = array(
            'idsite' => 3,
            'rec' => 1,
            'url' => $url,
            'rand' => mt_rand(0, 1000000),
            'apiv' => 1,
            'dimension1' => PHP_VERSION, //PHPVersion
            'dimension2' => Tools::getShopDomain(), //Domain
            'dimension3' => Configuration::get('PS_SHOP_ACTIVITY'), //BusinessType
            'dimension4' => $this->context->country->iso_code, //Country
            'dimension5' => $this->context->currency->iso_code, //Currency
            'dimension6' => $this->context->language->iso_code, //Language
            'dimension7' => _PS_VERSION_, //PrestashopVersion
            'dimension8' => $this->version, //TZModuleVersion
            'dimension9' => (int)Configuration::get('TOUCHIZE_ENABLED'), //TZEnabled
            'dimension10' => Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') //TZMultistore
        );
        return http_build_query($infoStruct);
    }

    public function hookDisplayAdminAfterHeader()
    {
        $result = '';

        if (in_array($this->context->controller->controller_name, $this->adminControllersHaveInfo)) {
            $helper = new TouchizeAdminHelper();
            $helper->assignMenuVars();
            $result .= $helper->getTemplate('partials/menu.tpl');
            if (method_exists($this->context->controller, 'getInfoTemplate')) {
                $info_template = $this->context->controller->getInfoTemplate();
                $result .= $helper->getTemplate($info_template);
            }
        }
        return $result;
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $touchizeGenerateStartupModules = (int)Tools::getValue(
                'TOUCHIZE_GENERATE_STARTUP_MODULES_on'
            );

            $pwa_logo     = '../img/tz-pwa-logo.png';
            $pwa_logo_512 = '../img/tz-pwa-logo-512.png';
            $pwa_logo_192 = '../img/tz-pwa-logo-192.png';

            if (!Validate::isBool($touchizeGenerateStartupModules)) {
                $output .= $this->displayError(
                    $this->l('Generate startup modules failed')
                );
                $success = false;
            } else {
                Configuration::updateValue(
                    'TOUCHIZE_GENERATE_STARTUP_MODULES',
                    $touchizeGenerateStartupModules
                );
            }

            $touchizeStartCategoryId = (string)Tools::getValue(
                'TOUCHIZE_START_CATEGORY_ID'
            );
            # Check if multiple and check each category id
            $allowedControllers = array('prices-drop', 'best-sales', 'new-products');
            $multiCategory = $touchizeStartCategoryId && strpos($touchizeStartCategoryId, ',') !== false;
            $validMultiCategory = true;
            if ($multiCategory) {
                $categoryIds = array_map(
                    'trim',
                    explode(',', $touchizeStartCategoryId)
                );
                foreach ($categoryIds as $catId) {
                    $validMultiCategory = $validMultiCategory &&
                                          (Category::categoryExists($catId) ||
                                          in_array($catId, $allowedControllers));
                }
            }
            $isManufacturersCategory = $touchizeStartCategoryId &&
                false !== Tools::strpos($touchizeStartCategoryId, 'manufacturer');

            //Since categoryExists uses cast to int, we need a complex check...
            // to avoid true for multicategories as "2, some_nonexisting_cat"
            $valid = $touchizeStartCategoryId &&
                     ($multiCategory && $validMultiCategory ||
                     $isManufacturersCategory ||
                     !$multiCategory && Category::categoryExists($touchizeStartCategoryId) ||
                     in_array($touchizeStartCategoryId, $allowedControllers));

            if (!$valid) {
                Configuration::updateValue(
                    'TOUCHIZE_START_CATEGORY_ID',
                    'best-sales'//Category::getRootCategory()->id
                );
                $output .= $this->displayError(
                    $this->l('One or more invalid start category, reset to best sellers.')
                );
                $success = false;
            } else {
                Configuration::updateValue(
                    'TOUCHIZE_START_CATEGORY_ID',
                    $touchizeStartCategoryId
                );
            }

            $touchizeCdnPath = (string)Tools::getValue('TOUCHIZE_CDN_PATH');
            if (!$touchizeCdnPath ||
                empty($touchizeCdnPath) ||
                !Validate::isUrl($touchizeCdnPath)
            ) {
                Configuration::updateValue(
                    'TOUCHIZE_CDN_PATH',
                    self::CDN_PATH
                );
                $output .= $this->displayError(
                    $this->l('CDN path failed, reset to default')
                );
                $success = false;
            } else {
                Configuration::updateValue(
                    'TOUCHIZE_CDN_PATH',
                    $touchizeCdnPath
                );
            }

            $touchizeCdnCode = (string)Tools::getValue('TOUCHIZE_CDN_CODE');
            if (!$touchizeCdnCode ||
                empty($touchizeCdnCode) ||
                !Validate::isUrl($touchizeCdnCode)
            ) {
                Configuration::updateValue(
                    'TOUCHIZE_CDN_CODE',
                    self::CDN_CODE
                );
                $output .= $this->displayError(
                    $this->l('CDN code failed, reset to default')
                );
                $success = false;
            } else {
                Configuration::updateValue(
                    'TOUCHIZE_CDN_CODE',
                    $touchizeCdnCode
                );
            }

            $touchizeHeadHtml = (string)Tools::getValue(
                'TOUCHIZE_HEAD_HTML'
            );
            if (!Validate::isString($touchizeHeadHtml)) {
                Configuration::updateValue(
                    'TOUCHIZE_HEAD_HTML',
                    ''
                );
                $output .= $this->displayError(
                    $this->l('Entered HTML was not valid, modified.')
                );
            } else {
                Configuration::updateValue(
                    'TOUCHIZE_HEAD_HTML',
                    htmlentities($touchizeHeadHtml)
                );
            }

            $touchizeBodyHtml = (string)Tools::getValue(
                'TOUCHIZE_BODY_HTML'
            );
            if (!Validate::isString($touchizeBodyHtml)) {
                Configuration::updateValue(
                    'TOUCHIZE_BODY_HTML',
                    ''
                );
                $output .= $this->displayError(
                    $this->l('Entered HTML was not valid, modified.')
                );
            } else {
                Configuration::updateValue(
                    'TOUCHIZE_BODY_HTML',
                    htmlentities($touchizeBodyHtml)
                );
            }

            $touchizeGaID = (string)Tools::getValue('TOUCHIZE_SEO_GA_ID');
            if (!Validate::isString($touchizeGaID)) {
                Configuration::updateValue(
                    'TOUCHIZE_SEO_GA_ID',
                    ''
                );
                $output .= $this->displayError(
                    $this->l('Setting Google Analytics ID failed')
                );
                $success = false;
            } else {
                Configuration::updateValue(
                    'TOUCHIZE_SEO_GA_ID',
                    $touchizeGaID
                );
            }

            $touchizeSeoSameAs = (string)Tools::getValue(
                'TOUCHIZE_SEO_SAME_AS'
            );
            if (!Validate::isString($touchizeSeoSameAs)) {
                Configuration::updateValue(
                    'TOUCHIZE_SEO_SAME_AS',
                    ''
                );
                $output .= $this->displayError(
                    $this->l('SEO ´same as´ must be string.')
                );
            } else {
                Configuration::updateValue(
                    'TOUCHIZE_SEO_SAME_AS',
                    $touchizeSeoSameAs
                );
            }

            $touchizeTouchmapSliderInterval = (int)Tools::getValue(
                'TOUCHIZE_TOUCHMAP_SLIDER_INTERVAL'
            );
            if (!$touchizeTouchmapSliderInterval ||
                !Validate::isInt($touchizeTouchmapSliderInterval) ||
                empty($touchizeTouchmapSliderInterval)
            ) {
                Configuration::updateValue(
                    'TOUCHIZE_TOUCHMAP_SLIDER_INTERVAL',
                    7000
                );
                /*
                Commented out since in first version slider will not be used
                $output .= $this->displayError(
                    $this->l(
                        'TouchMap slider interval failed, reset to default'
                    )
                );
                $success = false;
                */
                $success = true;
            } else {
                Configuration::updateValue(
                    'TOUCHIZE_TOUCHMAP_SLIDER_INTERVAL',
                    $touchizeTouchmapSliderInterval
                );
            }

            $touchizeDebug = (int) Tools::getValue('TOUCHIZE_DEBUG_on');
            if (!Validate::isBool($touchizeDebug)) {
                $output .= $this->displayError($this->l('Error...'));
                $success = false;
            } else {
                Configuration::updateValue('TOUCHIZE_DEBUG', $touchizeDebug);
            }

            $tz_pwa_name = Tools::getValue('TOUCHIZE_PWA_NAME');
            $tz_pwa_success = true;
            if (empty($tz_pwa_name) || !Validate::isString($tz_pwa_name)) {
                $output  .= $this->displayError($this->l('Invalid app name.'));
                $success = false;
                $tz_pwa_success = false;
            } else {
                Configuration::updateValue('TOUCHIZE_PWA_NAME', $tz_pwa_name);
            }

            $tz_pwa_shortname = Tools::getValue('TOUCHIZE_PWA_SHORTNAME');
            if (empty($tz_pwa_shortname) || !Validate::isString($tz_pwa_shortname)) {
                $output  .= $this->displayError($this->l('Invalid app shortname.'));
                $success = false;
                $tz_pwa_success = false;
            } else {
                Configuration::updateValue('TOUCHIZE_PWA_SHORTNAME', $tz_pwa_shortname);
            }

            if (isset($_FILES['TOUCHIZE_PWA_LOGO'])) {
                $logo_src = $_FILES['TOUCHIZE_PWA_LOGO']['tmp_name'];
                $img_size = $_FILES['TOUCHIZE_PWA_LOGO']['size'];

                if ($img_size !== 0 && @getimagesize($logo_src)) {
                    if (file_exists($pwa_logo)) {
                        unlink($pwa_logo);
                    }
                    if (file_exists($pwa_logo_512)) {
                        unlink($pwa_logo_512);
                    }
                    if (file_exists($pwa_logo_192)) {
                        unlink($pwa_logo_192);
                    }

                    copy($logo_src, $pwa_logo);
                    ImageManager::resize(
                        $logo_src,
                        $pwa_logo_512,
                        512,
                        512,
                        'png',
                        true
                    );
                    ImageManager::resize(
                        $logo_src,
                        $pwa_logo_192,
                        192,
                        192,
                        'png',
                        true
                    );
                    Configuration::updateValue('TOUCHIZE_PWA_LOGO', '/img/tz-pwa-logo.png');
                }
            }

            $tz_pwa_start_url = Tools::getValue('TOUCHIZE_PWA_START_URL');
            if (empty($tz_pwa_start_url) || !Validate::isString($tz_pwa_start_url)) {
                $output  .= $this->displayError($this->l('Invalid app start URL.'));
                $success = false;
                $tz_pwa_success = false;
            } else {
                Configuration::updateValue('TOUCHIZE_PWA_START_URL', $tz_pwa_start_url);
            }

            $tz_pwa_theme_color = Tools::getValue('TOUCHIZE_PWA_THEME_COLOR');
            if (empty($tz_pwa_theme_color) || !Validate::isString($tz_pwa_theme_color)) {
                $output  .= $this->displayError($this->l('Theme color error.'));
                $success = false;
                $tz_pwa_success = false;
            } else {
                Configuration::updateValue('TOUCHIZE_PWA_THEME_COLOR', $tz_pwa_theme_color);
            }

            $tz_pwa_background_color = Tools::getValue('TOUCHIZE_PWA_BACKGROUND_COLOR');
            if (empty($tz_pwa_background_color) || !Validate::isString($tz_pwa_background_color)) {
                $output  .= $this->displayError($this->l('Background color error.'));
                $success = false;
                $tz_pwa_success = false;
            } else {
                Configuration::updateValue('TOUCHIZE_PWA_BACKGROUND_COLOR', $tz_pwa_background_color);
            }

            $tz_pwa_enabled = Tools::getValue('TOUCHIZE_PWA_ENABLED');
            if (!Validate::isBool($tz_pwa_enabled)) {
                $output  .= $this->displayError($this->l('Could not enable app functionality.'));
                $success = false;
            } else {
                $tz_pwa_enabled = $tz_pwa_enabled && $tz_pwa_success;
                $touchizePWAHelper = new TouchizePWAHelper();

                if ($tz_pwa_enabled) {
                    if (Configuration::get('TOUCHIZE_PWA_LOGO') != 0 ||
                        !file_exists($pwa_logo_192) || !file_exists($pwa_logo_512)) {
                        $output .= $this->displayError(
                            $this->l('Logo image missing. A logo is needed for app functionality.')
                        );
                        $success        = false;
                        $tz_pwa_enabled = false;
                    } else {
                        try {
                            $this->setupServiceWorker();
                            $touchizePWAHelper->createSplashImages();
                        } catch (Exception $e) {
                            $output  .= $this->displayError($this->l(
                                'Could not enable PWA. Error: ' . $e->getMessage()
                            ));
                            $success = false;
                        }
                    }
                }

                if (!$tz_pwa_enabled) {
                    $this->deleteServiceWorker();
                    $touchizePWAHelper->deleteSplashImages();
                }

                Configuration::updateValue('TOUCHIZE_PWA_ENABLED', $tz_pwa_enabled);
            }

            if ($success) {
                $output .= $this->displayConfirmation(
                    $this->l('Settings updated')
                );
            }
        } elseif (Tools::getValue('configure') == 'touchize' && !Tools::getValue('config_tab', false)) {
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminGetStarted').'&configure='.
                Tools::safeOutput($this->module->name)
            );
        }
        return $output . $this->displayForm();
    }

    public function setupServiceWorker()
    {
        $root = $_SERVER['DOCUMENT_ROOT'];
        if (file_exists($root . "/touchize-sw.js")) {
            @unlink($root . '/touchize-sw.js');
        }
        $cache_version  = 3;
        $online_url     = Configuration::get('TOUCHIZE_PWA_START_URL');
        $offline_url    = $online_url . '?offline';
        $service_worker = "const CACHE_NAME = 'touchize-commerce-v{$cache_version}';
const CACHE_WHITELIST = [CACHE_NAME];
const ONLINE_URL = '{$online_url}';
const OFFLINE_URL = '{$offline_url}';
";
        $service_worker .= Tools::file_get_contents(_PS_MODULE_DIR_ . $this->name . '/views/js/pwa/touchize-sw.js');
        file_put_contents($root . '/touchize-sw.js', $service_worker);

        $pwa_manifest = [
            'short_name'       => Configuration::get('TOUCHIZE_PWA_SHORTNAME'),
            'name'             => Configuration::get('TOUCHIZE_PWA_NAME'),
            'start_url'        => Configuration::get('TOUCHIZE_PWA_START_URL'),
            'display'          => 'standalone',
            'scope'            => '/',
            'theme_color'      => Configuration::get('TOUCHIZE_PWA_THEME_COLOR'),
            'background_color' => Configuration::get('TOUCHIZE_PWA_BACKGROUND_COLOR'),
            'icons'            => [
                [
                    'src'   => 'img/tz-pwa-logo-192.png',
                    'type'  => 'image/png',
                    'sizes' => '192x192'
                ],
                [
                    'src'   => 'img/tz-pwa-logo-512.png',
                    'type'  => 'image/png',
                    'sizes' => '512x512'
                ]
            ]
        ];
        file_put_contents($root . '/manifest.json', json_encode($pwa_manifest));
    }

    public function deleteServiceWorker()
    {
        $root = $_SERVER['DOCUMENT_ROOT'];
        if (file_exists($root . "/touchize-sw.js")) {
            @unlink($root . '/touchize-sw.js');
        }
        if (file_exists($root . "/manifest.json")) {
            @unlink($root . '/manifest.json');
        }
    }

    public function isSingleShop()
    {
        $shops = Shop::getContextListShopID();
        if (count($shops) > 1) {
            return false;
        }
        return true;
    }
    public function showMultistoreWarning()
    {
        $helper = new TouchizeAdminHelper();
        $this->context->smarty->assign(array(
            'is_multishop_mode' => true
        ));

        return $helper->getTemplate('info/multistorewarning.tpl');
    }
    public function displayForm()
    {
        if (!$this->isSingleShop()) {
            return $this->showMultistoreWarning();
        }
        $config_form = new ConfigForm();
        return $config_form->generateHtml();
    }

    protected function createTables()
    {
        $res = true;
        $sql = require dirname(__FILE__).'/install/sql_install.php';
        foreach ($sql as $table_query) {
            $res &= Db::getInstance()->execute($table_query);
        }

        return $res;
    }

    protected function setTableDefaultData()
    {
        $res = (bool) Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'touchize_main_menu`
            (`id_menu_item`, `type`, `action`, `page`, `cms_page`, `url`,
            `external`, `event`, `event_input`, `page_url`, `position`)
            VALUES
            (null, \'menu-item\', \'page\', \'3\', null, null, null, null, null, null, 0);
        ');
        $languages = Language::getLanguages(true);
        foreach ($languages as $lang) {
            $res &= (bool) Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'touchize_main_menu_lang`
                (`id`, `id_lang`, `id_menu_item`, `title`)
                VALUES
                (null, \''.$lang['id_lang'].'\', \''.Db::getInstance()->Insert_ID().'\', null);
            ');
        }
        $menuBuilder = new TouchizeMenuBuilder();
        $menuBuilder->generateJson();

        return $res;
    }

    protected function deleteTables()
    {
        $res = true;
        $sql = require dirname(__FILE__).'/install/sql_install.php';
        foreach ($sql as $table_name => $query) {
            $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS ' . $table_name);
        }

        return $res;
    }

    private function setModuleTabs()
    {
        $langs = Language::getLanguages();

        $tabs_data = require dirname(__FILE__).'/install/tabs_install.php';
        foreach ($tabs_data as $tab) {
            $install_tab = new Tab();
            foreach ($langs as $lang) {
                $tab_label = isset($tab['label'][$lang['iso_code']])?
                    $tab['label'][$lang['iso_code']]:$tab['label']['en'] ;
                $install_tab->name[$lang['id_lang']] = $tab_label;
            }
            $install_tab->class_name = $tab['class_name'];
            $install_tab->id_parent = $tab['id_parent'];
            $install_tab->module = $this->name;
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && $tab['id_parent'] == 0) {
                $install_tab->active = 1;
                $install_tab->id_parent = (int) Tab::getIdFromClassName('AdminTools');
            }
            $install_tab->add();
            if (isset($tab['config_name'])) {
                Configuration::updateValue(
                    $tab['config_name'],
                    $install_tab->id
                );
            }
        }


        return true;
    }

    private function deleteModuleTabs()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }

        $tabs_data = require dirname(__FILE__).'/install/tabs_install.php';
        foreach ($tabs_data as $tab) {
            if (isset($tab['config_name'])) {
                Configuration::deleteByName($tab['config_name']);
            }
        }

        return true;
    }

    public function getImgFolder()
    {
        return 'touchmaps/';
    }

    public function createImageDirectory()
    {
        if (!file_exists(_PS_IMG_DIR_.$this->getImgFolder())) {
            # Apparently sometimes mkdir cannot set the rights,
            # and sometimes chmod can't. Trying both.
            $success = @mkdir(
                _PS_IMG_DIR_.$this->getImgFolder(),
                self::$accessRights,
                true
            );
            $chmod = @chmod(
                _PS_IMG_DIR_.$this->getImgFolder(),
                self::$accessRights
            );

            # Create an index.php file in the new folder
            if (($success || $chmod) &&
                !file_exists(_PS_IMG_DIR_.$this->getImgFolder().'index.php') &&
                file_exists(_PS_IMG_DIR_.'index.php')
            ) {
                return @copy(
                    _PS_IMG_DIR_.'index.php',
                    _PS_IMG_DIR_.$this->getImgFolder().'index.php'
                );
            }
        }

        return true;
    }

    /**
     * @return $this
     */
    protected function insertDefaultBanners()
    {
        $banners = require dirname(__FILE__).'/install/banner_install.php';
        $pos = 0;
        foreach ($banners as $banner) {
            $this->insertBanner($banner['name'], $banner['value'], $banner['width'], $banner['height'], $pos++);
        }
    }
    protected function insertBanner($name, $image, $width, $height, $pos)
    {
        $currentDate = new DateTime();
        $result = Db::getInstance()->insert(
            'touchize_touchmap',
            array(
                'id_shop' => 0,
                'name' => pSQL($name),
                'active' => 0,
                'mobile' => 1,
                'tablet' => 1,
                'runonce' => 1,
                'new_products' => 0,
                'best_sellers' => 0,
                'prices_drop' => 0,
                'home_page' => 1,
                'inslider' => 0,
                'position' => pSQL($pos),
                'width' => (int)pSQL($width),
                'height' => (int)pSQL($height),
                'date_add' => pSQL($currentDate->format('Y-m-d H:i:s')),
                'date_upd' => pSQL($currentDate->format('Y-m-d H:i:s')),
            )
        );

        if ($result) {
            $bannerId = Db::getInstance()->Insert_ID();
            $imageFolder = $this->getImgFolder();
            $imagePath = _PS_IMG_DIR_ . $imageFolder . $bannerId . '.jpg';
            $defBanner = _PS_MODULE_DIR_ . $this->name . '/views/img/defbanners/' . $image;
            copy($defBanner, $imagePath);
        }
        return $this;
    }

    public function deleteImageDirectory()
    {
        @rmdir(_PS_IMG_DIR_.$this->getImgFolder());

        return true;
    }

    public function setStylingVariables($data = null, $template = null)
    {
        if (!$data) {
            foreach ($this->templates as $cdnCode) {
                $json = @Tools::file_get_contents(
                    self::CDN_PATH.$cdnCode.'/css/simplestyle/defs.json'
                );

                if ($json) {
                    self::setStylingVariables(
                        json_decode($json),
                        $cdnCode
                    );
                }
            }

            $link = new Link();

            # Create .css files with logo class inside for further using.
            file_put_contents(
                _PS_MODULE_DIR_.'touchize/views/css/override' . TouchizeBaseHelper::getCSSFileAddition() . '.css',
                '.tz-brand__logo {'
                    .'background: url('
                        .'"//'.$link->getMediaLink(
                            _PS_IMG_.Configuration::get('PS_LOGO')
                        ).'"'
                    .') no-repeat center center;'
                    .'background-size: contain;'.
                '}'
            );

            file_put_contents(
                _PS_MODULE_DIR_.'touchize/views/css/override_preview.css',
                '.tz-brand__logo {'
                    .'background: url('
                        .'"//'.$link->getMediaLink(
                            _PS_IMG_.Configuration::get('PS_LOGO')
                        ).'"'
                    .') no-repeat center center;'
                    .'background-size: contain;'.
                '}'
            );
        } else {
            $currentDate = new DateTime();
            foreach ($data as $el) {
                $isColor = (int)$el->Color ? 1 : 0;

                Db::getInstance()->insert(
                    'touchize_variables_preview',
                    array(
                        'name' => pSQL($el->Variable),
                        'description' => '',
                        'value' => pSQL($el->Value),
                        'is_color' => pSQL($isColor),
                        'template' => pSQL($template),
                        'date_add' => pSQL($currentDate->format('Y-m-d H:i:s')),
                        'date_upd' => pSQL($currentDate->format('Y-m-d H:i:s')),
                    )
                );

                Db::getInstance()->insert(
                    'touchize_variables',
                    array(
                        'name' => pSQL($el->Variable),
                        'description' => '',
                        'value' => pSQL($el->Value),
                        'is_color' => pSQL($isColor),
                        'template' => pSQL($template),
                        'date_add' => pSQL($currentDate->format('Y-m-d H:i:s')),
                        'date_upd' => pSQL($currentDate->format('Y-m-d H:i:s')),
                    )
                );
            }
        }
    }
}
