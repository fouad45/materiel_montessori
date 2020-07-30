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
 * Touchize controller.
 */

class TouchizeTouchizeModuleFrontController extends ModuleFrontController
{

    private $preview = false;

    /**
     * FrontController::init() override
     *
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        $token = Tools::getToken(false);
       // $preview = TouchizeControllerHelper::getParam('preview', false);
        $router = json_decode($this->context->cookie->router);
    /*    if ($preview && !$router) {
            $router = (object) array(
                'SiteUrl' => $this->context->link->getPageLink('index', true),
                'tid' => Configuration::get('TOUCHIZE_START_CATEGORY_ID'),
                'pid' => null,
                'page' => null,
                'qs' => null,
                'search' => null,
            );
        }*/
        $config = array(
            'ApiServer' => '',
            'Router' => $router,
            'Endpoints' => $this->getEndpoints(),
            'Debug' => (bool) Configuration::get('TOUCHIZE_DEBUG'),
            'MainMenu' => $this->getMainMenu(),
            'Token' => $token,
            'Checkout' => $this->getCheckout(),
            'Analytics' => $this->getAnalytics(),
            'Search' => array(
                'Placeholder' => $this->module->l('Search ...', 'touchize'),
            ),
            'CookieNotification' => $this->getCookieNotification(),
            'ProductRelations' => array(
                'Title' => $this->module->l('Related products', 'touchize'),
            ),
            'ProductTechSpec' => array(
                'Title' => $this->module->l('Features', 'touchize'),
            ),
            'OptionSelect' => array(
                'VariantsSelectionText' => $this->module->l('Select variation', 'touchize'),
            ),
            'ProductVariants' => array(
                'VariantsText' => $this->module->l('Drag variation', 'touchize'),
            ),
            'ProductDescription' => array(
                'Title' => $this->module->l('Description', 'touchize'),
            ),
            'TaxonomyPopup' => array(
                'Prefix' => $this->module->l('All', 'touchize'),
            ),
            'ErrorMessages' => $this->getErrorMessages(),
        );

        if (Configuration::get('TOUCHIZE_GENERATE_STARTUP_MODULES')) {
            $config = array_merge($config, array(
                'StartupModules' => $this->getStartupModules($router),
            ));
        }
        $scriptPath = Configuration::get('TOUCHIZE_CDN_PATH').
            ($this->preview
            ? Configuration::get('TOUCHIZE_PREVIEW_CDN_CODE')
            : Configuration::get('TOUCHIZE_CDN_CODE'));

        # Slider
        $slider = Configuration::get('TOUCHIZE_TOUCHMAP_SLIDER_INTERVAL');
        if ($slider > 0) {
            $config = array_merge($config, array(
                'Slider' => array(
                    'Interval' => (int)$slider,
                ),
            ));
        }

        $headHtml = Configuration::get('TOUCHIZE_HEAD_HTML');
        if (Validate::isString($headHtml)) {
            $headHtml = html_entity_decode($headHtml);
        }
        $bodyHtml = Configuration::get('TOUCHIZE_BODY_HTML');
        if (Validate::isString($bodyHtml)) {
            $bodyHtml = html_entity_decode($bodyHtml);
        }

        $seoSameAs = Configuration::get('TOUCHIZE_SEO_SAME_AS');
        $seoSameAs = array_map('trim', explode(',', $seoSameAs));
        if (!$seoSameAs) {
            $seoSameAs = array();
        }
        $this->context->smarty->assign(array(
            'PS_VERSION' => _PS_VERSION_
        ));
        $styleOverrideFile = ($this->preview
                ? 'touchize/views/css/override_preview.css'
                : ('touchize/views/css/override' . TouchizeBaseHelper::getCSSFileAddition() .'.css'));

        $styleOverride = file_exists(_PS_MODULE_DIR_ . $styleOverrideFile) ?
            (_MODULE_DIR_ .$styleOverrideFile . '?rand='.mt_rand(0, 1000000)) : null;

        $tc_preview = TouchizeControllerHelper::getParam('tc_preview');
        $tc_template = TouchizeControllerHelper::getParam('tc_template');
        $tz_cdn_path = Configuration::get('TOUCHIZE_CDN_PATH');
        if ($tc_preview !== false && !empty($tc_template)) {
            $scriptPath = $tz_cdn_path . $tc_template . "/latest";
        }

        $this->context->smarty->assign(array(
            'touchfrontConfig' => $config,
            'scriptPath' => $scriptPath,
            'token' => $token,
            'head_html' => $headHtml,
            'body_html' => $bodyHtml,
            'seo_same_as' => $seoSameAs,
            'style_override_path' => $styleOverride
        ));
        $this->context->smarty->assign(array(
            'shop_name'   => $this->context->shop->name,
            'favicon_url' => _PS_IMG_.Configuration::get('PS_FAVICON'),
            'logo_url'    => $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO'))
        ));

            $offline_overlay         = Tools::getIsset('offline');
            $theme_color             = Configuration::get('TOUCHIZE_PWA_THEME_COLOR');
            $app_background_color    = Configuration::get('TOUCHIZE_PWA_BACKGROUND_COLOR');
            $tz_pwa_enabled          = Configuration::get('TOUCHIZE_PWA_ENABLED');
            $tz_noscript_content     = $this->module->l('This site needs javascript to operate properly.');
            $tz_offline_content      = $this->module->l('Content not available offline.');
            $tz_pwa_reload_link_text = $this->module->l('Reload');
            $tz_pwa_start_url        = Configuration::get('TOUCHIZE_PWA_START_URL');
            $PWAHelper               = new TouchizePWAHelper();
            $tz_pwa_splashes         = $PWAHelper->getSplashes();
            $this->context->smarty->assign(array(
                'offline_overlay'         => $offline_overlay,
                'theme_color'             => $theme_color,
                'app_background_color'    => $app_background_color,
                'tz_pwa_enabled'          => $tz_pwa_enabled,
                'tz_noscript_content'     => $tz_noscript_content,
                'tz_offline_content'      => $tz_offline_content,
                'tz_pwa_start_url'        => $tz_pwa_start_url,
                'tz_pwa_reload_link_text' => $tz_pwa_reload_link_text,
                'tc_preview'              => $tc_preview,
                'tz_pwa_splashes'         => $tz_pwa_splashes,
            ));
    }

    /**
     * FrontController::getLayout() override
     *
     * @see FrontController::getLayout()
     */
    public function getLayout()
    {
        return $this->getTemplatePath('touchize.tpl');
    }

    public function getCookieNotification()
    {
        return array(
            'Title' => $this->module->l('This site uses cookies.', 'touchize'),
            'Info' => $this->module->l('By continuing your visit to this site, you accept the use of cookies', 'touchize'),
        );
    }

    /**
     * Get checkout.
     *
     * @return array
     */
    public function getCheckout()
    {
        //Default
        $checkout = Configuration::get('PS_ORDER_PROCESS_TYPE')
            ? 'order-opc'
            : 'order';
        $url = $this->context->link->getPageLink($checkout);

        //Other modules for checkout
        if (Module::isInstalled('klarnaofficial') &&
            Module::isEnabled('klarnaofficial')) {
            if (Configuration::get('KCO_IS_ACTIVE')) {
                $url = $this->context->link->getModuleLink('klarnaofficial', 'checkoutklarna', array(), true);
            }
        }

        return array(
            'Title' => $this->module->l('Checkout', 'touchize'),
            'Url'   => $url,
        );
        /*
         * Can use code below instead as well, will then use text
        $meta = Meta::getMetaByPage($checkout, $this->context->language->id);
        if ($meta) {
            return array (
                'Title' => $meta['title'],
                'Url' => $meta['url_rewrite']
            );
        }
        */
    }

    /**
     * Get Analytics.
     *
     * @return array
     */
    public function getAnalytics()
    {
        $analytics = array(
            'TZUAID' => 'UA-117781565-3',
            'CustomerID' => $this->getCustomerGAId(),
            'TZCustomDimensions' => array(
                'Domain' => Tools::getShopDomain(),
                'SubscriptionID' => $this->getTZSubscriptionId(),
                'BusinessType' => Configuration::get('PS_SHOP_ACTIVITY'),
                'Country' => $this->context->country->iso_code,
                'Currency' => $this->context->currency->iso_code,
                'Language' => $this->context->language->iso_code,
                'PlatformVersion' => _PS_VERSION_,
                'PHPversion' => PHP_VERSION,
                'TZModuleVersion' => $this->module->version,
                'TZEnabled' => (int)Configuration::get('TOUCHIZE_ENABLED'),
                'TZMultistore' => Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')
            ),
        );
        return $analytics;
    }

    public function getTZSubscriptionId()
    {
        $key = Configuration::get('TOUCHIZE_LICENSE_KEY');
        if (Tools::strlen($key) > 0) {
            return hash('sha256', $key);
        }
        if (Configuration::get('TOUCHIZE_TRIAL_ACTIVE') == '1') {
            $trialStarted = Configuration::get('TOUCHIZE_WHEN_TRIAL_WAS_ACTIVATED');
            $now = time();
            $timePassed = $now-$trialStarted;
            $daysPassed = round($timePassed / 86400, 0); // 86400 = 1 day
            return 'T'.$daysPassed;
        }
        $subscriptionId = Configuration::get('TOUCHIZE_SUBSCRIPTION_ID', '');
        return $subscriptionId ? $subscriptionId : 'installed';
    }

    public function getCustomerGAId()
    {
        $customerGAId = '';
        $seoGAId = Configuration::get('TOUCHIZE_SEO_GA_ID');
        if (Validate::isString($seoGAId) && !empty($seoGAId)) {
            $customerGAId = $seoGAId;
        }
        return $customerGAId ? $customerGAId : '';
    }

    public function getErrorMessages()
    {
        return array(
            'CartEmpty'=> array(
                'Title'=> $this->module->l('The cart is empty!', 'touchize'),
                'Msg'=> $this->module->l('Drag a product to the cart and try again', 'touchize'),
                'Level'=> 3
            ),
            'CartRemove'=> array(
                'Title'=> $this->module->l('Can\'t remove item from cart...', 'touchize'),
                'Msg'=> $this->module->l('Something went wrong!', 'touchize'),
                'Level'=> 1
            ),
            'ProductListLoad'=> array(
                'Title'=> $this->module->l('Error', 'touchize'),
                'Msg'=> $this->module->l('Ooops, something went wrong loading products', 'touchize'),
                'Level'=> 4
            )
        );
    }

    /**
     * Returns TouchFront Main Menu object
     *
     * @return array
     */
    protected function getMainMenu()
    {
        # TODO: Store in cache
        $mainmenu = json_decode(
            Configuration::getGlobalValue('TOUCHIZE_MAIN_MENU'),
            true
        );
        $lang = $this->context->language->id;

        // Comment in this code in to have sign in/sign out in main menu
        // $loggedIn = (bool)$this->context->customer->isLogged();
        // $menuitem  = array(
        //     'type' => 'menu-item',
        // );
        // if ($loggedIn) {
        //     $menuitem['title'] = $this->module->l('Sign out', 'touchize');
        //     $menuitem['url'] = $this->context->link->getPageLink(
        //         'index',
        //         true,
        //         null,
        //         'mylogout'
        //     );
        // } else {
        //     $menuitem['title'] = $this->module->l('Sign in', 'touchize');
        //     $menuitem['url'] = $this->context->link->getPageLink(
        //         'my-account',
        //         true
        //     );
        // }
        // array_unshift($mainmenu['items'], $menuitem);

        # Go thru the main menu elements and convert if needed
        foreach ($mainmenu['items'] as &$menuitem) {
            # Page controller
            if (array_key_exists('pagecontroller', $menuitem)) {
                $data = TouchizeMenuBuilder::getSeoAndUrlsPages(
                    $lang,
                    $menuitem['pagecontroller']
                );
                $meta = Meta::getMetaByPage($data[0]['page'], $lang);

                if ($meta) {
                    $menuitem['title'] = isset($menuitem['title']) &&
                        is_array($menuitem['title']) &&
                        !empty($menuitem['title'][$lang])
                            ? $this->module->l($menuitem['title'][$lang], 'touchize')
                            : $this->module->l($meta['title'], 'touchize');

                    $menuitem['url'] = $this->context->link->getPageLink(
                        $data[0]['page'],
                        true
                    );
                }

            # CMS page id
            } elseif (array_key_exists('cmsid', $menuitem)) {
                $cms = new CMS($menuitem['cmsid'], $lang);
                $menuitem['title'] = isset($menuitem['title']) &&
                    is_array($menuitem['title']) &&
                    !empty($menuitem['title'][$lang])
                        ? $this->module->l($menuitem['title'][$lang], 'touchize')
                        : $this->module->l($cms->meta_title, 'touchize');

                $menuitem['pageurl'] = $this->context->link->getCMSLink(
                    $menuitem['cmsid']
                );
                $menuitem['page'] = $menuitem['cmsid'];
            } else {
                if (isset($menuitem['title']) &&
                    is_array($menuitem['title'])
                ) {
                    $menuitem['title'] = $this->module->l(
                        $menuitem['title'][$lang],
                        'touchize'
                    );
                }
            }
        }

        return $mainmenu;
    }

    /**
      * Return TouchFront Endpoints object
      *
      * @return array
      */
    private function getEndpoints()
    {
        return array(
            'Filter' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'filter'
                ),
                'Method' => 'POST',
            ),
            'Products' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'productlist'
                ),
                'Method' => 'POST',
            ),
            'ProductDetails' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'product'
                ),
                'Method' => 'POST',
            ),
            'Cart' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'cart'
                ),
                'Method' => 'POST',
            ),
            'FlushCart' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'cart',
                    array(
                        'module_action' => 'clear',
                    )
                ),
                'Method' => 'POST',
            ),
            'AddToCart' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'cartadd'
                ),
                'Method' => 'POST',
            ),
            'RemoveFromCart' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'cartremove'
                ),
                'Method' => 'POST',
            ),
            'Categories' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'taxonomy'
                ),
                'Method' => 'POST',
            ),
            'Taxonomies' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'taxonomy'
                ),
                'Method' => 'POST',
            ),
            'Search' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'search'
                ),
                'Method' => 'POST',
            ),
            'Selectors' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'selector'
                ),
                'Method' => 'POST',
            ),
            'AutoSearch' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'autosearch'
                ),
                'Method' => 'POST',
            ),
            'Banners' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'touchmap'
                ),
                'Method' => 'POST',
            ),
            'Campaigns' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'touchmap'
                ),
                'Method' => 'POST',
            ),
            'Content' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'touchize',
                    'page'
                ),
                'Method' => 'POST',
            ),
            'Blocks' => array(
                'Endpoint' => 'module/touchize/blocks',
            ),
            'Reviews' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'productcomments',
                    'default',
                    array(
                        'action' => 'add_comment',
                    )
                ),
                'Method' => 'POST',
                'Type' => 'form',
            ),
            'StockNotification' => array(
                'Endpoint' => $this->context->link->getModuleLink(
                    'mailalerts',
                    'actions',
                    array(
                        'process' => 'add',
                    )
                ),
                'Method' => 'POST',
                'Type' => 'form',
            ),
        );
    }

    /**
     * Get startup modules.
     *
     * @param  object $router
     *
     * @return array
     */
    private function getStartupModules($router)
    {
        $tid = $router->tid;
        $pid = $router->pid;
        $page = $router->page;
        $search = $router->search;

        $returnData = array();
        $contentBlocks = array();

        if ($page) {
            $pageModel = $this->getPage($page);
            $returnData['HtmlPage'] = array(
                'Params' => array(
                    'Model' => $pageModel,
                ),
            );
        }

        if ($pid) {
            $productModel = $this->getProductData($pid);
            $returnData['ProductDetailsPopup'] = array(
                'Params' => array(
                    'Model' => $productModel,
                ),
            );
        }

        //Cookie notification
        array_push(
            $contentBlocks,
            array(
                'Module' => 'CookieNotification',
                'Params' => array(),
            )
        );


        $touchmapModel = $this->getTouchmap($tid);
        if ($touchmapModel) {
            array_push(
                $contentBlocks,
                array(
                    'Module' => 'Campaign',
                    'Params' => array(
                        'Model' => $touchmapModel,
                    ),
                )
            );
        }
        if ($touchmapModel) {
            array_push(
                $contentBlocks,
                array(
                    'Module' => 'TouchMapSlider',
                    'Params' => array(
                        'Model' => $touchmapModel,
                    ),
                )
            );
        }

        $productListModel = $this->getProductlist($tid);
        if ($productListModel) {
            array_push($contentBlocks, array(
                'Module' => 'TaxonomyDescription',
                'Params' => array()
            ));
            array_push($contentBlocks, array(
                'Module' => 'ProductList',
                'Params' => array(
                    'Model' => $productListModel,
                ),
            ));
        }

        if ($search) {
            //Search has highest presidence
            $returnData['Search'] = array(
                'Params' => array(
                    'Model' => $this->getSearch($search),
                ),
            );
        }

        if (!empty($contentBlocks)) {
            $contentTemplate = array(
                'Blocks' => $contentBlocks,
            );
        }

        if (!empty($contentTemplate)) {
            $returnData['Content'] = array(
                'Params' => array(
                    'Template' => $contentTemplate,
                ),
            );
        }

        $returnData['TaxonomyMenu'] = array(
            'Params' => array(
                'Model' => $this->getTaxonomy(),
            ),
        );

        $returnData['TaxonomyPopup'] = array(
            'Params' => array(
                'Model' => $this->getTaxonomy(),
            ),
        );

        return $returnData;
    }

    /**
     * Get taxonomy.
     *
     * @return array
     */
    protected function getTaxonomy()
    {
        $helper = new TouchizeTaxonomyHelper();

        return $helper->getTree();
    }

    /**
     * Get product list.
     *
     * @param  int $tid
     *
     * @return array
     */
    protected function getProductlist($tid)
    {
        $helper = new TouchizeProductHelper();

        return $helper->getIndexProductList($tid, null);
    }

    /**
     * Get product data.
     *
     * @param  int $pid
     *
     * @return array
     */
    protected function getProductData($pid)
    {
        $helper = new TouchizeProductHelper();

        return $helper->getProduct($pid);
    }

    /**
     * Get touchmap.
     *
     * @param  int $tid
     *
     * @return array
     */
    protected function getTouchmap($tid)
    {
        $helper = new TouchizeTouchmapHelper();

        return $helper->getTouchmaps($tid, true);
    }

    /**
     * Get page.
     *
     * @param  int $page
     *
     * @return array
     */
    protected function getPage($page)
    {
        $helper = new TouchizePageHelper();

        return $helper->getPage($page);
    }

    /**
     * Get search.
     *
     * @param  string $term
     *
     * @return array
     */
    protected function getSearch($term)
    {
        $helper = new TouchizeSearchHelper();

        return $helper->getSearch($term);
    }

    public function setPreview($preview = false)
    {
        $this->preview = $preview;
    }
}
