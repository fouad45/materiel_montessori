<?php

/* Fieldthemes - Theme Customizer - 2015 - fieldthemes@gmail.com */

if (!defined('_PS_VERSION_'))
    exit;
use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\ObjectPresenter;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Adapter\Configuration as ConfigurationAdapter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Debug\Debug;
include_once(_PS_MODULE_DIR_ . 'fieldthemecustomizer/model/fieldThemeCustomizerModel.php');

class FieldThemeCustomizer extends Module
{
    private $_output = '';

    private $_standardConfig = '';
    private $_styleConfig = '';
    private $_multiLangConfig = '';

    private $_bgImageConfig = '';
    private $_fontConfig = '';

    private $_cssRules = array();
    private $_configDefaults = array();
    private $_websafeFonts = array();
    private $_googleFonts = array();

    function __construct()
    {
        $this->name = 'fieldthemecustomizer';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'fieldthemes';
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Field Theme Customizer');
        $this->description = $this->l('Required by author: Fieldthemes.');

        $this->_defineArrays();
    }

    /* ------------------------------------------------------------- */
    /*  INSTALL THE MODULE
    /* ------------------------------------------------------------- */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install()
        && $this->registerHook('displayHeader')
        && $this->_createConfigs()
        && $this->_createTab();
    }

    /* ------------------------------------------------------------- */
    /*  UNINSTALL THE MODULE
    /* ------------------------------------------------------------- */
    public function uninstall()
    {
        return parent::uninstall()
        && $this->unregisterHook('displayHeader')
        && $this->_deleteConfigs()
        && $this->_deleteTab();
    }

    /* ------------------------------------------------------------- */
    /*  CREATE THE TABLES
    /* ------------------------------------------------------------- */
    private function _createTables()
    {
        return true;
    }

    /* ------------------------------------------------------------- */
    /*  DELETE THE TABLES
    /* ------------------------------------------------------------- */
    private function _deleteTables()
    {
        return true;
    }

    /* ------------------------------------------------------------- */
    /*  CREATE CONFIGS
    /* ------------------------------------------------------------- */
    private function _createConfigs()
    {
        $languages = $this->context->language->getLanguages();

        // General Options
        
        $response = Configuration::updateValue('FIELD_showPanelTool', 1);
        $response &= Configuration::updateValue('FIELD_mainLayout', 'fullwidth');
        $response &= Configuration::updateValue('FIELD_enableCountdownTimer', 1);
        $response &= Configuration::updateValue('FIELD_quickView', 1);

        // Header Options
        $response &= Configuration::updateValue('FIELD_stickyMenu', 1);
        $response &= Configuration::updateValue('FIELD_stickySearch', 1);
        $response &= Configuration::updateValue('FIELD_stickyCart', 1);

        // Category Page Options
        $response &= Configuration::updateValue('FIELD_subcategories', 0);
        $response &= Configuration::updateValue('FIELD_categoryShowAvgRating', 1);
        $response &= Configuration::updateValue('FIELD_categoryShowColorOptions', 0);
        $response &= Configuration::updateValue('FIELD_categoryShowStockInfo', 0);

        // Product Page Options
        $response &= Configuration::updateValue('FIELD_productShowReference', 0);
        $response &= Configuration::updateValue('FIELD_productShowCondition', 0);
        $response &= Configuration::updateValue('FIELD_productShowManName', 0);
        $response &= Configuration::updateValue('FIELD_productVerticalThumb', 0);
        $response &= Configuration::updateValue('FIELD_productUpsell', 0);

        // Font Options
        $response &= Configuration::updateValue('FIELD_includeCyrillicSubset', 0);
        $response &= Configuration::updateValue('FIELD_includeGreekSubset', 0);
        $response &= Configuration::updateValue('FIELD_includeVietnameseSubset', 0);
        $response &= Configuration::updateValue('FIELD_mainFont', 'Open Sans');
        $response &= Configuration::updateValue('FIELD_titleFont', 'Roboto');

        // Color Options
        $response &= Configuration::updateValue('FIELD_mainColorScheme', '#1e1e21');
        $response &= Configuration::updateValue('FIELD_activeColorScheme', '#61a321');

        // Background Options
        $response &= Configuration::updateValue('FIELD_backgroundColor', '#f1f1f1');
        $response &= Configuration::updateValue('FIELD_backgroundImage', '');
        $response &= Configuration::updateValue('FIELD_backgroundRepeat', 'repeat');
        $response &= Configuration::updateValue('FIELD_backgroundAttachment', 'scroll');
        $response &= Configuration::updateValue('FIELD_backgroundSize', 'auto');

        $response &= Configuration::updateValue('FIELD_bodyBackgroundColor', '#f1f1f1');
        $response &= Configuration::updateValue('FIELD_bodyBackgroundImage', '');
        $response &= Configuration::updateValue('FIELD_bodyBackgroundRepeat', 'repeat');
        $response &= Configuration::updateValue('FIELD_bodyBackgroundAttachment', 'scroll');
        $response &= Configuration::updateValue('FIELD_bodyBackgroundSize', 'auto');

		$response &= Configuration::updateValue('FIELD_breadcrumbBackgroundImage', '');
        // Custom Codes
        $response &= Configuration::updateValue('FIELD_customCSS', '');
        $response &= Configuration::updateValue('FIELD_customJS', '');

        // Override Options
        $response &= Configuration::updateValue('PS_TC_ACTIVE', 0);
        $response &= Configuration::updateValue('PS_QUICK_VIEW', 1);
        $response &= Configuration::updateValue('PS_GRID_PRODUCT', 0);

        return $response;
    }

    /* ------------------------------------------------------------- */
    /*  DELETE CONFIGS
    /* ------------------------------------------------------------- */
    private function _deleteConfigs()
    {
        // General Options
        $response = Configuration::deleteByName('FIELD_showPanelTool');
        $response &= Configuration::deleteByName('FIELD_mainLayout');
        $response &= Configuration::deleteByName('FIELD_enableCountdownTimer');
        $response &= Configuration::deleteByName('FIELD_quickView');

        // Header Options
        $response &= Configuration::deleteByName('FIELD_stickyMenu');
        $response &= Configuration::deleteByName('FIELD_stickySearch');
        $response &= Configuration::deleteByName('FIELD_stickyCart');
        
        // Category Page Options
        $response &= Configuration::deleteByName('FIELD_subcategories');
        $response &= Configuration::deleteByName('FIELD_categoryShowAvgRating');
        $response &= Configuration::deleteByName('FIELD_categoryShowColorOptions');
        $response &= Configuration::deleteByName('FIELD_categoryShowStockInfo');

        // Product Page Options
        $response &= Configuration::deleteByName('FIELD_productShowReference');
        $response &= Configuration::deleteByName('FIELD_productShowCondition');
        $response &= Configuration::deleteByName('FIELD_productShowManName');
        $response &= Configuration::deleteByName('FIELD_productVerticalThumb');
        $response &= Configuration::deleteByName('FIELD_productUpsell');

        // Font Options
        $response &= Configuration::deleteByName('FIELD_includeCyrillicSubset');
        $response &= Configuration::deleteByName('FIELD_includeGreekSubset');
        $response &= Configuration::deleteByName('FIELD_includeVietnameseSubset');
        $response &= Configuration::deleteByName('FIELD_mainFont');
        $response &= Configuration::deleteByName('FIELD_titleFont');

        // Color Options
        $response &= Configuration::deleteByName('FIELD_mainColorScheme');
        $response &= Configuration::deleteByName('FIELD_activeColorScheme');

        // Background Options
        $response &= Configuration::deleteByName('FIELD_backgroundColor');
        $response &= Configuration::deleteByName('FIELD_backgroundImage');
        $response &= Configuration::deleteByName('FIELD_backgroundRepeat');
        $response &= Configuration::deleteByName('FIELD_backgroundAttachment');
        $response &= Configuration::deleteByName('FIELD_backgroundSize');

        $response &= Configuration::deleteByName('FIELD_bodyBackgroundColor');
        $response &= Configuration::deleteByName('FIELD_bodyBackgroundImage');
        $response &= Configuration::deleteByName('FIELD_bodyBackgroundRepeat');
        $response &= Configuration::deleteByName('FIELD_bodyBackgroundAttachment');
        $response &= Configuration::deleteByName('FIELD_bodyBackgroundSize');

		$response &= Configuration::deleteByName('FIELD_breadcrumbBackgroundImage');
        // Custom Codes
        $response &= Configuration::deleteByName('FIELD_customCSS');
        $response &= Configuration::deleteByName('FIELD_customJS');

        return $response;
    }

    /* ------------------------------------------------------------- */
    /*  INSTALL DEMO DATA
    /* ------------------------------------------------------------- */
    private function _installDemoData()
    {
        return true;
    }

    /* ------------------------------------------------------------- */
    /*  CREATE THE TAB MENU
    /* ------------------------------------------------------------- */
    private function _createTab()
    {
        $response = true;

        // First check for parent tab
        $parentTabID = Tab::getIdFromClassName('AdminFieldMenu');

        if ($parentTabID) {
            $parentTab = new Tab($parentTabID);
        }
        else {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = "AdminFieldMenu";
            foreach (Language::getLanguages() as $lang) {
                $parentTab->name[$lang['id_lang']] = "FIELDTHEMES";
            }
            $parentTab->id_parent = 0;
            $parentTab->module ='';
            $response &= $parentTab->add();
        }

			// Check for parent tab2
			$parentTab_2ID = Tab::getIdFromClassName('AdminFieldMenuSecond');
			if ($parentTab_2ID) {
				$parentTab_2 = new Tab($parentTab_2ID);
			}
			else {
				$parentTab_2 = new Tab();
				$parentTab_2->active = 1;
				$parentTab_2->name = array();
				$parentTab_2->class_name = "AdminFieldMenuSecond";
				foreach (Language::getLanguages() as $lang) {
					$parentTab_2->name[$lang['id_lang']] = "FieldThemes Configure";
				}
				$parentTab_2->id_parent = $parentTab->id;
				$parentTab_2->module ='';
				$response &= $parentTab_2->add();
			}
			// Created tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "AdminFieldThemeCustomizerConfig";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "Manage Theme Customizer";
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();
        return $response;
    }

    /* ------------------------------------------------------------- */
    /*  DELETE THE TAB MENU
    /* ------------------------------------------------------------- */
    private function _deleteTab()
    {
        $id_tab = Tab::getIdFromClassName('AdminFieldThemeCustomizerConfig');
		if($id_tab){
			$tab = new Tab($id_tab);
			$tab->delete();
		}
		// Get the number of tabs inside our parent tab
        // If there is no tabs, remove the parent
		$parentTab_2ID = Tab::getIdFromClassName('AdminFieldMenuSecond');
		if($parentTab_2ID){
			$tabCount_2 = Tab::getNbTabs($parentTab_2ID);
			if ($tabCount_2 == 0) {
				$parentTab_2 = new Tab($parentTab_2ID);
				$parentTab_2->delete();
			}
		}
        // Get the number of tabs inside our parent tab
        // If there is no tabs, remove the parent
		$parentTabID = Tab::getIdFromClassName('AdminFieldMenu');
		if($parentTabID){
			$tabCount = Tab::getNbTabs($parentTabID);
			if ($tabCount == 0){
				$parentTab = new Tab($parentTabID);
				$parentTab->delete();
			}
		}
        return true;
    }

    /* ------------------------------------------------------------- */
    /*  DEFINE ARRAYS
    /* ------------------------------------------------------------- */
    private function _defineArrays()
    {
        $bgImageDirs = $this->context->link->getMediaLink(_MODULE_DIR_ . $this->name . '/views/img/front/bg/');
        
        $force_ssl = (Configuration::get('PS_SSL_ENABLED'));
        if($force_ssl){
            $bgImageDir = str_replace("http:", "https:", $bgImageDirs);
        } else {
            $bgImageDir = $bgImageDirs;
        }

        // CONFIG ARRAYS
        $this->_standardConfig = array(
            // General Options
            'FIELD_showPanelTool',
            'FIELD_mainLayout',
            'FIELD_enableCountdownTimer',
            'FIELD_quickView',
            'FIELD_categoryShowAvgRating',
            
            // Header Options
            'FIELD_stickyMenu',
            'FIELD_stickySearch',
            'FIELD_stickyCart',

            // Category Page Options
	    'PS_GRID_PRODUCT',
            'FIELD_subcategories',
            'FIELD_categoryShowColorOptions',
            'FIELD_categoryShowStockInfo',

            // Product Page Options
            'FIELD_productShowReference',
            'FIELD_productShowCondition',
            'FIELD_productShowManName',
	    'FIELD_productVerticalThumb',
            'FIELD_productUpsell',

            // Font Options
            'FIELD_includeCyrillicSubset',
            'FIELD_includeGreekSubset',
            'FIELD_includeVietnameseSubset'
        );

        $this->_styleConfig = array(
            // Background Options
            'FIELD_backgroundColor',
            'FIELD_backgroundImage',
            'FIELD_backgroundRepeat',
            'FIELD_backgroundAttachment',
            'FIELD_backgroundSize',

            'FIELD_bodyBackgroundColor',
            'FIELD_bodyBackgroundImage',
            'FIELD_bodyBackgroundRepeat',
            'FIELD_bodyBackgroundAttachment',
            'FIELD_bodyBackgroundSize',
			
			'FIELD_breadcrumbBackgroundImage',

            // Font Options
            'FIELD_mainFont',
            'FIELD_titleFont',

            // Color Options
            'FIELD_mainColorScheme',
            'FIELD_activeColorScheme',

            // Custom Codes
            'FIELD_customCSS',
            'FIELD_customJS'
        );

        // SPECIAL ARRAYS
        // These arrays are only for defining certain config values that needs to be handled differently.
        $this->_bgImageConfig = array(
            'FIELD_backgroundImage',
            'FIELD_bodyBackgroundImage',
			'FIELD_breadcrumbBackgroundImage'
        );

        $this->_fontConfig = array(
            'FIELD_mainFont',
            'FIELD_titleFont'
        );
        // End - SPECIAL ARRAYS

        // CSS AND CONFIG RELATIONS
        $this->_cssRules = array(
            // main Background
            'FIELD_backgroundColor' => array(
                array(
                    'selector' => 'main',
                    'rule' => 'background-color'
                )
            ),
            'FIELD_backgroundImage' => array(
                array(
                    'selector' => 'main',
                    'rule' => 'background-image',
                    'prefix' => 'url("' . $bgImageDir,
                    'suffix' => '")'
                )
            ),
            'FIELD_backgroundRepeat' => array(
                array(
                    'selector' => 'main',
                    'rule' => 'background-repeat'
                )
            ),
            'FIELD_backgroundAttachment' => array(
                array(
                    'selector' => 'main',
                    'rule' => 'background-attachment'
                )
            ),
            'FIELD_backgroundSize' => array(
                array(
                    'selector' => 'main',
                    'rule' => 'background-size'
                )
            ),

            // Body Background
            'FIELD_bodyBackgroundColor' => array(
                array(
                    'selector' => 'body',
                    'rule' => 'background-color'
                )
            ),
            'FIELD_bodyBackgroundImage' => array(
                array(
                    'selector' => 'body',
                    'rule' => 'background-image',
                    'prefix' => 'url("' . $bgImageDir,
                    'suffix' => '")'
                )
            ),
            'FIELD_bodyBackgroundRepeat' => array(
                array(
                    'selector' => 'body',
                    'rule' => 'background-repeat'
                )
            ),
            'FIELD_bodyBackgroundAttachment' => array(
                array(
                    'selector' => 'body',
                    'rule' => 'background-attachment'
                )
            ),
            'FIELD_bodyBackgroundSize' => array(
                array(
                    'selector' => 'body',
                    'rule' => 'background-size'
                )
            ),

            // Font
            'FIELD_mainFont' => array(
                array(
                    'selector' => 'body',
                    'rule' => 'font-family'
                )
            ),
            'FIELD_titleFont' => array(
                array(
                    'selector' => '',
                    'rule' => 'font-family'
                )
            ),

            // Main Color Scheme
            'FIELD_mainColorScheme' => array(
                array(
                    'selector' => '',
                    'rule' => 'background-color'
                ),
                array(
                    'selector' => '',
                    'rule' => 'color'
                ),
                array(
                    'selector' => '',
                    'rule' => 'border-color'
                )
            ),
            
            // Active Color Scheme
            'FIELD_activeColorScheme' => array(
                array(
                    'selector' => '.has-discount .discount,#header .dropdown-menu li.current, #header .dropdown-menu li:hover, #header .mobile_links-wrapper .dropdown-menu li:first-child,.btn-primary.focus, .btn-primary:focus, .btn-primary:hover, .btn:hover,.cart-grid .cart-grid-body > a.label:hover, .btn-primary:active,#cms #cms-about-us .page-subheading,#cms #cms-about-us .cms-line .cms-line-comp,#products .item-product-list .right-product .discount-percentage-product,.products.horizontal_mode #box-product-list .quick-view:hover, .products.horizontal_mode #box-product-list .add-to-cart:hover,.pagination .current a, .pagination a:hover,#field_productcates .title_block .title_text:before,#field_productcates .title_block .title_text:after,#blockcart-modal .cart-content .btn:hover,.modal-header .close:hover,.product-actions .add-to-cart,.js-qv-mask.mask .owl-buttons [class^="carousel-"] span:hover,#back-top a,.field-demo-wrap .control.active, .cl-row-reset .cl-reset:hover,.contact_ft ul li .icon .fa:hover,.sdsblog-box-content .sds_blog_post .right_blog_home .r_more:hover,.products-sort-order .select-list:hover,.tab_cates li:before,#fieldtabproductsisotope li a:before,.sale_product,.horizontal_mode .quick-view:hover,.horizontal_mode .add-to-cart:hover,#fieldblockcategories.horizontal_mode .item-inner .right-block-cate .name_item a:before,#fieldblockcategories.horizontal_mode .item-inner .right-block-cate a.more-cate:hover,#onecate_products_block.horizontal_mode .quick-view:hover, #onecate_products_block.horizontal_mode .add-to-cart:hover,#cart_block_top .cart_top_ajax a.view-cart:hover,a.slide-button,.outer-slide [data-u="arrowright"]:hover, .outer-slide [data-u="arrowleft"]:hover,.owl-buttons [class^="carousel-"] span:hover,#search_block_top .current:hover, #search_block_top .current[aria-expanded=true],#search_block_top .btn.button-search:hover,.sticky-fixed-top #cart_block_top:hover,.button_unique,.popup_text .social_footer a:hover,.v-megamenu-title,#search_block_top .btn.button-search:hover',
                    'rule' => 'background-color'
                ),
                array(
                    'selector' => '.block-categories .category-sub-menu .category-sub-link:hover,.price-ajax,#header .header-nav #mobile_links .expand-more[aria-expanded=true],.product-price,.has-discount.product-price, .has-discount p,.footer-container .contact_ft ul li a:hover,.product-line-grid-right .cart-line-product-actions,.cart-summary-line .value, .product-line-grid-right .product-price,.info-category span, .info-category span a,#recent_article_smart_blog_block_left .block_content ul li .info,a:hover,.block-categories a:hover,.block-categories .collapse-icons .add:hover, .block-categories .collapse-icons .remove:hover, .block-categories .arrows .arrow-down:hover, .block-categories .arrows .arrow-right:hover,#search_filters .facet .facet-title:hover,#search_filters .facet .facet-label a:hover,.click-product-list-grid > div:hover,.active_grid .click-product-list-grid > div.click-product-grid,.active_list .click-product-list-grid > div.click-product-list,#wrapper .breadcrumb li a:hover,#fieldsizechart-show:hover,.product-cover .layer:hover .zoom-in,.tabs .nav-tabs .nav-link.active, .tabs .nav-tabs .nav-link:hover,#blockcart-modal .divide-right p.price,#blockcart-modal .cart-content p,.footer-container .bullet ul li a:hover,.box-static_content .fa,.content_text h3 a:hover,.footer-top  h3:hover,.right_blog_home .content h3:hover a,.right_blog_home .content .smart-auth,.tab-category-slider .tab_cates li.active, .tab-category-slider .tab_cates li:hover,#fieldtabproductsisotope .fieldtabproductsisotope-filter a:hover, #fieldtabproductsisotope .fieldtabproductsisotope-filter a.active,.horizontal_mode .item-inner .right-product .product_name a:hover,.item-countdown .countdown_time,.item-inner .item-countdown .section_cout .Days,.item-inner .item-countdown .section_cout .Hours,.item-inner .item-countdown .section_cout .Minutes,.item-inner .item-countdown .section_cout .Seconds,#fieldblockcategories.horizontal_mode .item-inner .right-block-cate a:hover,.title_block .title_text:hover,.cart_top_ajax::before,#cart_block_top .product-name-ajax a:hover,.price,#cart_block_top .cart_top_ajax a.remove-from-cart:hover,.v-main-section-links > li > a,.v-main-section-sublinks li a:hover,.v-megamenu .more-vmegamenu a,.contact-link:hover, .contact-link-ft:hover, #header .contact-link-ft a:hover,#header .header-nav #mobile_links:hover .expand-more, .click-nav2:hover,#header_menu .fieldmegamenu .root:hover .root-item > a > .title, #header_menu .fieldmegamenu .root:hover .root-item > .title, #header_menu .fieldmegamenu .root.active .root-item > a > .title, #header_menu .fieldmegamenu .root.active .root-item > .title, #header_menu .fieldmegamenu .root .root-item > a.active > .title,#cart_block_top span.fa:hover,#cart_block_top span.cart-products-count,#cart_block_top span.cart_item_top,.v-megamenu > ul > li:hover > a',
                    'rule' => 'color'
                ),
                array(
                    'selector' => '#header_menu.fieldmegamenu-sticky,.right_blog_home .content .smart-auth:before',
                    'rule' => 'border-bottom-color'
                ),
				array(
                    'selector' => '.v-megamenu > ul > li div.submenu',
                    'rule' => 'border-left-color'
                ),
				array(
                    'selector' => '.content-nav2,.cart_top_ajax,#fieldblockcategories.horizontal_mode .item-inner,.title_block',
                    'rule' => 'border-top-color'
                ),
				array(
                    'selector' => '#fieldblockcategories.horizontal_mode .item-inner .right-block-cate a.more-cate:hover,.horizontal_mode .add-to-cart:hover,.sdsblog-box-content .sds_blog_post .right_blog_home .r_more:hover,.pagination .current a, .pagination a:hover',
                    'rule' => 'border-color'
                )
			)
        );

        // Config defaults
        $this->_configDefaults = array(
            'FIELD_mainColorScheme' => '#1E1E21',
            'FIELD_activeColorScheme' => '#61a321',

            /* Background Options */
            'FIELD_backgroundColor' => '#ffffff',
            'FIELD_backgroundRepeat' => 'repeat',
            'FIELD_backgroundAttachment' => 'scroll',
            'FIELD_backgroundSize' => 'auto',

            'FIELD_bodyBackgroundColor' => '#ffffff',
            'FIELD_bodyBackgroundRepeat' => 'repeat',
            'FIELD_bodyBackgroundAttachment' => 'scroll',
            'FIELD_bodyBackgroundSize' => 'auto'
        );

        // Web-safe Fonts
        $this->_websafeFonts = array('Arial', 'Agency', 'Helveticaneue', 'sans-serif');

        // Google Fonts
        $this->_googleFonts = array(
            'ABeeZee' => array('subsets' => array('latin'), 'variants' => array('400', 'italic')),
            'Abel' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Abril Fatface' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Aclonica' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Acme' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Actor' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Adamina' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Advent Pro' => array('subsets' => array('latin', 'latin-ext', 'greek'), 'variants' => array('100', '200', '300', '400', '500', '600', '700')),
            'Aguafina Script' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Akronim' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Aladin' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Aldrich' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Alef' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Alegreya' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic', '900', '900italic')),
            'Alegreya SC' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic', '900', '900italic')),
            'Alegreya Sans' => array('subsets' => array('latin', 'latin-ext', 'vietnamese'), 'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic')),
            'Alegreya Sans SC' => array('subsets' => array('latin', 'latin-ext', 'vietnamese'), 'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic')),
            'Alex Brush' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Alfa Slab One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Alice' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Alike' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Alike Angular' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Allan' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Allerta' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Allerta Stencil' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Allura' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Almendra' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Almendra Display' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Almendra SC' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Amarante' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Amaranth' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Amatic SC' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Amethysta' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Anaheim' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Andada' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Andika' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Angkor' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Annie Use Your Telescope' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Anonymous Pro' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'greek'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Antic' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Antic Didone' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Antic Slab' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Anton' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Arapey' => array('subsets' => array('latin'), 'variants' => array('400', 'italic')),
            'Arbutus' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Arbutus Slab' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Architects Daughter' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Archivo Black' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Archivo Narrow' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Arimo' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese', 'greek'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Arizonia' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Armata' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Artifika' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Arvo' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Asap' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Asset' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Astloch' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Asul' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Atomic Age' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Aubrey' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Audiowide' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Autour One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Average' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Average Sans' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Averia Gruesa Libre' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Averia Libre' => array('subsets' => array('latin'), 'variants' => array('300', '300italic', '400', 'italic', '700', '700italic')),
            'Averia Sans Libre' => array('subsets' => array('latin'), 'variants' => array('300', '300italic', '400', 'italic', '700', '700italic')),
            'Averia Serif Libre' => array('subsets' => array('latin'), 'variants' => array('300', '300italic', '400', 'italic', '700', '700italic')),
            'Bad Script' => array('subsets' => array('cyrillic', 'latin'), 'variants' => array('400')),
            'Balthazar' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Bangers' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Basic' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Battambang' => array('subsets' => array('khmer'), 'variants' => array('400', '700')),
            'Baumans' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Bayon' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Belgrano' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Belleza' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'BenchNine' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('300', '400', '700')),
            'Bentham' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Berkshire Swash' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Bevan' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Bigelow Rules' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Bigshot One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Bilbo' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Bilbo Swash Caps' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Bitter' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700')),
            'Black Ops One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Bokor' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Bonbon' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Boogaloo' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Bowlby One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Bowlby One SC' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Brawler' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Bree Serif' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Bubblegum Sans' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Bubbler One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Buda' => array('subsets' => array('latin'), 'variants' => array('300')),
            'Buenard' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Butcherman' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Butterfly Kids' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Cabin' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic')),
            'Cabin Condensed' => array('subsets' => array('latin'), 'variants' => array('400', '500', '600', '700')),
            'Cabin Sketch' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Caesar Dressing' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Cagliostro' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Calligraffitti' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Cambo' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Candal' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Cantarell' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Cantata One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Cantora One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Capriola' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Cardo' => array('subsets' => array('greek-ext', 'latin', 'latin-ext', 'greek'), 'variants' => array('400', 'italic', '700')),
            'Carme' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Carrois Gothic' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Carrois Gothic SC' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Carter One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Caudex' => array('subsets' => array('greek-ext', 'latin', 'latin-ext', 'greek'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Cedarville Cursive' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Ceviche One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Changa One' => array('subsets' => array('latin'), 'variants' => array('400', 'italic')),
            'Chango' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Chau Philomene One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic')),
            'Chela One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Chelsea Market' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Chenla' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Cherry Cream Soda' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Cherry Swash' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Chewy' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Chicle' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Chivo' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '900', '900italic')),
            'Cinzel' => array('subsets' => array('latin'), 'variants' => array('400', '700', '900')),
            'Cinzel Decorative' => array('subsets' => array('latin'), 'variants' => array('400', '700', '900')),
            'Clicker Script' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Coda' => array('subsets' => array('latin'), 'variants' => array('400', '800')),
            'Coda Caption' => array('subsets' => array('latin'), 'variants' => array('800')),
            'Codystar' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('300', '400')),
            'Combo' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Comfortaa' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'greek'), 'variants' => array('300', '400', '700')),
            'Coming Soon' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Concert One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Condiment' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Content' => array('subsets' => array('khmer'), 'variants' => array('400', '700')),
            'Contrail One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Convergence' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Cookie' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Copse' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Corben' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Courgette' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Cousine' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese', 'greek'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Coustard' => array('subsets' => array('latin'), 'variants' => array('400', '900')),
            'Covered By Your Grace' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Crafty Girls' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Creepster' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Crete Round' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic')),
            'Crimson Text' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '600', '600italic', '700', '700italic')),
            'Croissant One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Crushed' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Cuprum' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Cutive' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Cutive Mono' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Damion' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Dancing Script' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Dangrek' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Dawning of a New Day' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Days One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Delius' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Delius Swash Caps' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Delius Unicase' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Della Respira' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Denk One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Devonshire' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Didact Gothic' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'greek'), 'variants' => array('400')),
            'Diplomata' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Diplomata SC' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Domine' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Donegal One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Doppio One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Dorsa' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Dosis' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('200', '300', '400', '500', '600', '700', '800')),
            'Dr Sugiyama' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Droid Sans' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Droid Sans Mono' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Droid Serif' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Duru Sans' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Dynalight' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'EB Garamond' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese'), 'variants' => array('400')),
            'Eagle Lake' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Eater' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Economica' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Electrolize' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Elsie' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '900')),
            'Elsie Swash Caps' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '900')),
            'Emblema One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Emilys Candy' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Engagement' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Englebert' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Enriqueta' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Erica One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Esteban' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Euphoria Script' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Ewert' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Exo' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic')),
            'Exo 2' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic')),
            'Expletus Sans' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic')),
            'Fanwood Text' => array('subsets' => array('latin'), 'variants' => array('400', 'italic')),
            'Fascinate' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Fascinate Inline' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Faster One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Fasthand' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Fauna One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Federant' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Federo' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Felipa' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Fenix' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Finger Paint' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Fjalla One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Fjord One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Flamenco' => array('subsets' => array('latin'), 'variants' => array('300', '400')),
            'Flavors' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Fondamento' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic')),
            'Fontdiner Swanky' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Forum' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Francois One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Freckle Face' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Fredericka the Great' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Fredoka One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Freehand' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Fresca' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Frijole' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Fruktur' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Fugaz One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'GFS Didot' => array('subsets' => array('greek'), 'variants' => array('400')),
            'GFS Neohellenic' => array('subsets' => array('greek'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Gabriela' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Gafata' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Galdeano' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Galindo' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Gentium Basic' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Gentium Book Basic' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Geo' => array('subsets' => array('latin'), 'variants' => array('400', 'italic')),
            'Geostar' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Geostar Fill' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Germania One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Gilda Display' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Give You Glory' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Glass Antiqua' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Glegoo' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Gloria Hallelujah' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Goblin One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Gochi Hand' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Gorditas' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Goudy Bookletter 1911' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Graduate' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Grand Hotel' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Gravitas One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Great Vibes' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Griffy' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Gruppo' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Gudea' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700')),
            'Habibi' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Hammersmith One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Hanalei' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Hanalei Fill' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Handlee' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Hanuman' => array('subsets' => array('khmer'), 'variants' => array('400', '700')),
            'Happy Monkey' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Headland One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Henny Penny' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Herr Von Muellerhoff' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Holtwood One SC' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Homemade Apple' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Homenaje' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'IM Fell DW Pica' => array('subsets' => array('latin'), 'variants' => array('400', 'italic')),
            'IM Fell DW Pica SC' => array('subsets' => array('latin'), 'variants' => array('400')),
            'IM Fell Double Pica' => array('subsets' => array('latin'), 'variants' => array('400', 'italic')),
            'IM Fell Double Pica SC' => array('subsets' => array('latin'), 'variants' => array('400')),
            'IM Fell English' => array('subsets' => array('latin'), 'variants' => array('400', 'italic')),
            'IM Fell English SC' => array('subsets' => array('latin'), 'variants' => array('400')),
            'IM Fell French Canon' => array('subsets' => array('latin'), 'variants' => array('400', 'italic')),
            'IM Fell French Canon SC' => array('subsets' => array('latin'), 'variants' => array('400')),
            'IM Fell Great Primer' => array('subsets' => array('latin'), 'variants' => array('400', 'italic')),
            'IM Fell Great Primer SC' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Iceberg' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Iceland' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Imprima' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Inconsolata' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Inder' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Indie Flower' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Inika' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Irish Grover' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Istok Web' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Italiana' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Italianno' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Jacques Francois' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Jacques Francois Shadow' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Jim Nightshade' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Jockey One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Jolly Lodger' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Josefin Sans' => array('subsets' => array('latin'), 'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic')),
            'Josefin Slab' => array('subsets' => array('latin'), 'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic')),
            'Joti One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Judson' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700')),
            'Julee' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Julius Sans One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Junge' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Jura' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'greek'), 'variants' => array('300', '400', '500', '600')),
            'Just Another Hand' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Just Me Again Down Here' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Kameron' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Kantumruy' => array('subsets' => array('khmer'), 'variants' => array('300', '400', '700')),
            'Karla' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Kaushan Script' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Kavoon' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Kdam Thmor' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Keania One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Kelly Slab' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Kenia' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Khmer' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Kite One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Knewave' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Kotta One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Koulen' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Kranky' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Kreon' => array('subsets' => array('latin'), 'variants' => array('300', '400', '700')),
            'Kristi' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Krona One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'La Belle Aurore' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Lancelot' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Lato' => array('subsets' => array('latin'), 'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic')),
            'League Script' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Leckerli One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Ledger' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Lekton' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700')),
            'Lemon' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Libre Baskerville' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700')),
            'Life Savers' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Lilita One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Lily Script One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Limelight' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Linden Hill' => array('subsets' => array('latin'), 'variants' => array('400', 'italic')),
            'Lobster' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Lobster Two' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Londrina Outline' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Londrina Shadow' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Londrina Sketch' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Londrina Solid' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Lora' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Love Ya Like A Sister' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Loved by the King' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Lovers Quarrel' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Luckiest Guy' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Lusitana' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Lustria' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Macondo' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Macondo Swash Caps' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Magra' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Maiden Orange' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Mako' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Marcellus' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Marcellus SC' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Marck Script' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Margarine' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Marko One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Marmelad' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Marvel' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Mate' => array('subsets' => array('latin'), 'variants' => array('400', 'italic')),
            'Mate SC' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Maven Pro' => array('subsets' => array('latin'), 'variants' => array('400', '500', '700', '900')),
            'McLaren' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Meddon' => array('subsets' => array('latin'), 'variants' => array('400')),
            'MedievalSharp' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Medula One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Megrim' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Meie Script' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Merienda' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Merienda One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Merriweather' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic')),
            'Merriweather Sans' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('300', '300italic', '400', 'italic', '700', '700italic', '800', '800italic')),
            'Metal' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Metal Mania' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Metamorphous' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Metrophobic' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Michroma' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Milonga' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Miltonian' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Miltonian Tattoo' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Miniver' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Miss Fajardose' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Modern Antiqua' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Molengo' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Molle' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('italic')),
            'Monda' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Monofett' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Monoton' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Monsieur La Doulaise' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Montaga' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Montez' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Montserrat' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Montserrat Alternates' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Montserrat Subrayada' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Moul' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Moulpali' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Mountains of Christmas' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Mouse Memoirs' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Mr Bedfort' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Mr Dafoe' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Mr De Haviland' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Mrs Saint Delafield' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Mrs Sheppards' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Muli' => array('subsets' => array('latin'), 'variants' => array('300', '300italic', '400', 'italic')),
            'Mystery Quest' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Neucha' => array('subsets' => array('cyrillic', 'latin'), 'variants' => array('400')),
            'Neuton' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('200', '300', '400', 'italic', '700', '800')),
            'New Rocker' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'News Cycle' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Niconne' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Nixie One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Nobile' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Nokora' => array('subsets' => array('khmer'), 'variants' => array('400', '700')),
            'Norican' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Nosifer' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Nothing You Could Do' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Noticia Text' => array('subsets' => array('latin', 'latin-ext', 'vietnamese'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Noto Sans' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese', 'greek'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Noto Serif' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese', 'greek'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Nova Cut' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Nova Flat' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Nova Mono' => array('subsets' => array('latin', 'greek'), 'variants' => array('400')),
            'Nova Oval' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Nova Round' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Nova Script' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Nova Slim' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Nova Square' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Numans' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Nunito' => array('subsets' => array('latin'), 'variants' => array('300', '400', '700')),
            'Odor Mean Chey' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Offside' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Old Standard TT' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700')),
            'Oldenburg' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Oleo Script' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Oleo Script Swash Caps' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Open Sans' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese', 'greek'), 'variants' => array('300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic')),
            'Open Sans Condensed' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese', 'greek'), 'variants' => array('300', '300italic', '700')),
            'Oranienbaum' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Orbitron' => array('subsets' => array('latin'), 'variants' => array('400', '500', '700', '900')),
            'Oregano' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic')),
            'Orienta' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Original Surfer' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Oswald' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('300', '400', '700')),
            'Over the Rainbow' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Overlock' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic', '900', '900italic')),
            'Overlock SC' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Ovo' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Oxygen' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('300', '400', '700')),
            'Oxygen Mono' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'PT Mono' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'), 'variants' => array('400')),
            'PT Sans' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'PT Sans Caption' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'), 'variants' => array('400', '700')),
            'PT Sans Narrow' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'), 'variants' => array('400', '700')),
            'PT Serif' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'PT Serif Caption' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'), 'variants' => array('400', 'italic')),
            'Pacifico' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Paprika' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Parisienne' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Passero One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Passion One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700', '900')),
            'Pathway Gothic One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Patrick Hand' => array('subsets' => array('latin', 'latin-ext', 'vietnamese'), 'variants' => array('400')),
            'Patrick Hand SC' => array('subsets' => array('latin', 'latin-ext', 'vietnamese'), 'variants' => array('400')),
            'Patua One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Paytone One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Peralta' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Permanent Marker' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Petit Formal Script' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Petrona' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Philosopher' => array('subsets' => array('cyrillic', 'latin'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Piedra' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Pinyon Script' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Pirata One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Plaster' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Play' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'greek'), 'variants' => array('400', '700')),
            'Playball' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Playfair Display' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic', '900', '900italic')),
            'Playfair Display SC' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic', '900', '900italic')),
            'Podkova' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Poiret One' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Poller One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Poly' => array('subsets' => array('latin'), 'variants' => array('400', 'italic')),
            'Pompiere' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Pontano Sans' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Port Lligat Sans' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Port Lligat Slab' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Prata' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Preahvihear' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Press Start 2P' => array('subsets' => array('cyrillic', 'latin', 'latin-ext', 'greek'), 'variants' => array('400')),
            'Princess Sofia' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Prociono' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Prosto One' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Puritan' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Purple Purse' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Quando' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Quantico' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Quattrocento' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Quattrocento Sans' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Questrial' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Quicksand' => array('subsets' => array('latin'), 'variants' => array('300', '400', '700')),
            'Quintessential' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Qwigley' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Racing Sans One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Radley' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic')),
            'Raleway' => array('subsets' => array('latin'), 'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900')),
            'Raleway Dots' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Rambla' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Rammetto One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Ranchers' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Rancho' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Rationale' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Redressed' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Reenie Beanie' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Revalia' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Ribeye' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Ribeye Marrow' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Righteous' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Risque' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Roboto' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese', 'greek'), 'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '900', '900italic')),
            'Roboto Condensed' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese', 'greek'), 'variants' => array('300', '300italic', '400', 'italic', '700', '700italic')),
            'Roboto Slab' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese', 'greek'), 'variants' => array('100', '300', '400', '700')),
            'Rochester' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Rock Salt' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Rokkitt' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Romanesco' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Ropa Sans' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic')),
            'Rosario' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Rosarivo' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic')),
            'Rouge Script' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Ruda' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700', '900')),
            'Rufina' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Ruge Boogie' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Ruluko' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Rum Raisin' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Ruslan Display' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Russo One' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Ruthie' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Rye' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Sacramento' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Sail' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Salsa' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Sanchez' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic')),
            'Sancreek' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Sansita One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Sarina' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Satisfy' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Scada' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Schoolbell' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Seaweed Script' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Sevillana' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Seymour One' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Shadows Into Light' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Shadows Into Light Two' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Shanti' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Share' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Share Tech' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Share Tech Mono' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Shojumaru' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Short Stack' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Siemreap' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Sigmar One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Signika' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('300', '400', '600', '700')),
            'Signika Negative' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('300', '400', '600', '700')),
            'Simonetta' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic', '900', '900italic')),
            'Sintony' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Sirin Stencil' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Six Caps' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Skranji' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '700')),
            'Slackey' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Smokum' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Smythe' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Sniglet' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', '800')),
            'Snippet' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Snowburst One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Sofadi One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Sofia' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Sonsie One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Sorts Mill Goudy' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400', 'italic')),
            'Source Code Pro' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('200', '300', '400', '500', '600', '700', '900')),
            'Source Sans Pro' => array('subsets' => array('latin', 'latin-ext', 'vietnamese'), 'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900', '900italic')),
            'Special Elite' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Spicy Rice' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Spinnaker' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Spirax' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Squada One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Stalemate' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Stalinist One' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Stardos Stencil' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Stint Ultra Condensed' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Stint Ultra Expanded' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Stoke' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('300', '400')),
            'Strait' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Sue Ellen Francisco' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Sunshiney' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Supermercado One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Suwannaphum' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Swanky and Moo Moo' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Syncopate' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Tangerine' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Taprom' => array('subsets' => array('khmer'), 'variants' => array('400')),
            'Tauri' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Telex' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Tenor Sans' => array('subsets' => array('cyrillic', 'cyrillic-ext', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Text Me One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'The Girl Next Door' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Tienne' => array('subsets' => array('latin'), 'variants' => array('400', '700', '900')),
            'Tinos' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'vietnamese', 'greek'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Titan One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Titillium Web' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900')),
            'Trade Winds' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Trocchi' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Trochut' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700')),
            'Trykker' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Tulpen One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Ubuntu' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'greek'), 'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic')),
            'Ubuntu Condensed' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'greek'), 'variants' => array('400')),
            'Ubuntu Mono' => array('subsets' => array('cyrillic', 'greek-ext', 'cyrillic-ext', 'latin', 'latin-ext', 'greek'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Ultra' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Uncial Antiqua' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Underdog' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Unica One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'UnifrakturCook' => array('subsets' => array('latin'), 'variants' => array('700')),
            'UnifrakturMaguntia' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Unkempt' => array('subsets' => array('latin'), 'variants' => array('400', '700')),
            'Unlock' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Unna' => array('subsets' => array('latin'), 'variants' => array('400')),
            'VT323' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Vampiro One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Varela' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Varela Round' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Vast Shadow' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Vibur' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Vidaloka' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Viga' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Voces' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Volkhov' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Vollkorn' => array('subsets' => array('latin'), 'variants' => array('400', 'italic', '700', '700italic')),
            'Voltaire' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Waiting for the Sunrise' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Wallpoet' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Walter Turncoat' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Warnes' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Wellfleet' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Wendy One' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('400')),
            'Wire One' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Yanone Kaffeesatz' => array('subsets' => array('latin', 'latin-ext'), 'variants' => array('200', '300', '400', '700')),
            'Yellowtail' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Yeseva One' => array('subsets' => array('cyrillic', 'latin', 'latin-ext'), 'variants' => array('400')),
            'Yesteryear' => array('subsets' => array('latin'), 'variants' => array('400')),
            'Zeyada' => array('subsets' => array('latin'), 'variants' => array('400'))
        );

    }

    /* ------------------------------------------------------------- */
    /*  GET CONTENT
    /* ------------------------------------------------------------- */
    public function getContent()
    {
        $id_shop = $this->context->shop->id;
        $languages = $this->context->language->getLanguages();
        $errors = array();
		  $reset_tables='<form class="import_demo"  method="post" action="'.$this->context->link->getAdminLink('AdminModules', false).'&reset_tables&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'">	
		<button type="submit" class="btn btn-default btn-lg" style="margin-bottom:20px;"><span class="icon icon-refresh"></span>&nbsp;&nbsp;'.$this->l('Refresh Tables').'</button></form>';
        // Load css file for option panel
        $this->context->controller->addCSS(_MODULE_DIR_ . $this->name . '/views/css/admin/field-admin.css');

        // Load js file for option panel
        $this->context->controller->addJqueryPlugin('field', _MODULE_DIR_ . $this->name . '/views/js/admin/');

        if (Tools::isSubmit('reset_tables')) {
			$parentTabID = Tab::getIdFromClassName('AdminFieldMenu');
			$tables = Db::getInstance()->executeS('
			SELECT id_tab
			FROM `'._DB_PREFIX_.'tab`  
			WHERE `id_parent` = 0 AND active =0 AND id_tab > '.$parentTabID);
			foreach($tables as $table){
		 Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'tab` WHERE `id_tab` = '.$table['id_tab'].'');
		 Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'tab_lang` WHERE `id_tab` = '.$table['id_tab'].'');
			}
		}
        elseif (Tools::isSubmit('submit' . $this->name)) {

            // Standard config
            foreach ($this->_standardConfig as $config) {
                if (Tools::isSubmit($config)) {
                    Configuration::updateValue($config, Tools::getValue($config));
                }
            }

            // Style config
            foreach ($this->_styleConfig as $config) {

                // Check if the config is a background image
                if (in_array($config, $this->_bgImageConfig)) {
                    if (isset($_FILES[$config]) && isset($_FILES[$config]['tmp_name']) && !empty($_FILES[$config]['tmp_name'])) {
                        if ($error = ImageManager::validateUpload($_FILES[$config], Tools::convertBytes(ini_get('upload_max_filesize')))) {
                            $errors[] = $error;
                        }
                        else {
                            $imageName = explode('.', $_FILES[$config]['name']);
                            $imageExt = $imageName[1];
                            $imageName = $imageName[0];
                            $backgroundImageName = $imageName . '-' . $id_shop . '.' . $imageExt;

                            if (!move_uploaded_file($_FILES[$config]['tmp_name'], _PS_MODULE_DIR_ . $this->name . '/views/img/front/bg/' . $backgroundImageName)) {
                                $errors[] = $this->l('File upload error.');
                            }
                            else {
                                Configuration::updateValue($config, $backgroundImageName);
                            }
                        }
                    }

                    continue;
                }

                if (Tools::isSubmit($config)) {
                    Configuration::updateValue($config, Tools::getValue($config));
                }

            }

            // Custom Codes
            if (Tools::isSubmit('FIELD_customCSS')) {
                Configuration::updateValue('FIELD_customCSS', Tools::getValue('FIELD_customCSS'));
            }

            if (Tools::isSubmit('FIELD_customJS')) {
                Configuration::updateValue('FIELD_customJS', Tools::getValue('FIELD_customJS'));
            }

            // Write the configurations to a CSS file
            $response = $this->_writeCss();
            if (!$response) {
                $errors[] = $this->l('An error occured while writing the css file!');
            }

            // Prepare the output
            if (count($errors)) {
                $this->_output .= $this->displayError(implode('<br />', $errors));
            }
            else {
                $this->_output .= $this->displayConfirmation($this->l('Configuration updated'));
            }

        }
        elseif (Tools::isSubmit('deleteConfig')) {
            $config = Tools::getValue('deleteConfig');
            $configValue = Configuration::get($config);

            if (file_exists(_PS_MODULE_DIR_ . $this->name . '/views/img/front/bg/' . $configValue)) {
                unlink(_PS_MODULE_DIR_ . $this->name . '/views/img/front/bg/' . $configValue);
            }

            Configuration::updateValue($config, null);

        }

        return $reset_tables.$this->_output . $this->_displayForm();
    }

    /* ------------------------------------------------------------- */
    /*  DISPLAY CONFIGURATION FORM
    /* ------------------------------------------------------------- */
    private function _displayForm()
    {
        $id_default_lang = $this->context->language->id;
        $languages = $this->context->language->getLanguages();
        $id_shop = $this->context->shop->id;

        // General Options
        $layoutTypes = array(
            array(
                'value' => 'fullwidth',
                'name' => 'FullWidth'
            ),
            array(
                'value' => 'boxed',
                'name' => 'Boxed'
            )
        );

        // Background Options
        $backgroundRepeatOptions = array(
            array(
                'value' => 'repeat-x',
                'name' => 'Repeat-X'
            ),
            array(
                'value' => 'repeat-y',
                'name' => 'Repeat-Y'
            ),
            array(
                'value' => 'repeat',
                'name' => 'Repeat Both'
            ),
            array(
                'value' => 'no-repeat',
                'name' => 'No Repeat'
            )
        );

        $backgroundAttachmentOptions = array(
            array(
                'value' => 'scroll',
                'name' => 'Scroll'
            ),
            array(
                'value' => 'fixed',
                'name' => 'Fixed'
            )
        );

        $backgroundSizeOptions = array(
            array(
                'value' => 'auto',
                'name' => 'Auto'
            ),
            array(
                'value' => 'cover',
                'name' => 'Cover'
            )
        );

        // Font Options
        $fontOptions = array();

        foreach ($this->_websafeFonts as $fontName){
            $fontOptions[] = array(
                'value' => $fontName,
                'name' => $fontName
            );
        }

        foreach ($this->_googleFonts as $fontName => $fontInfo){
            $fontOptions[] = array(
                'value' => $fontName,
                'name' => $fontName
            );
        }


        $fields_form = array(
            'field-general' => array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('General'),
                        'icon' => 'icon-cog'
                    ),
                    'input' => array(
                        array(
                            'type' => 'switch',
                            'name' => 'FIELD_showPanelTool',
                            'label' => $this->l('Show paneltool'),
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'showPanelTool_on',
                                    'value' => 1,
                                    'label' => $this->l('On')
                                ),
                                array(
                                    'id' => 'showPanelTool_off',
                                    'value' => 0,
                                    'label' => $this->l('Off')
                                )
                            )
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'FIELD_mainLayout',
                            'label' => $this->l('Layout type'),
                            'required' => false,
                            'lang' => false,
                            'options' => array(
                                'query' => $layoutTypes,
                                'id' => 'value',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'name' => 'FIELD_enableCountdownTimer',
                            'label' => $this->l('Enable Countdown Timers'),
                            'desc' => $this->l('This option enables/disables countdown timers for timed specific prices.'),
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'timer_on',
                                    'value' => 1,
                                    'label' => $this->l('On')
                                ),
                                array(
                                    'id' => 'timer_off',
                                    'value' => 0,
                                    'label' => $this->l('Off')
                                )
                            )
                        ),
                        array(
                            'type' => 'hidden',
                            'label' => $this->l('Enable quick view'),
                            'name' => 'FIELD_quickView',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'quickview_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'quickview_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            )
                        ),
                        array(
                            'type' => 'hidden',
                            'label' => $this->l('Show average rating stars'),
                            'name' => 'FIELD_categoryShowAvgRating',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'avgratings_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'avgratings_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            )
                        )
                    ),
                    // Submit Button
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'name' => 'savefieldThemeConfig'
                    )
                )
            ),
            'field-header' => array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Header'),
                        'icon' => 'icon-cog'
                    ),
                    'input' => array(
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Sticky menu'),
                            'name' => 'FIELD_stickyMenu',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'stickymenu_on',
                                    'value' => 1,
                                    'label' => $this->l('On')
                                ),
                                array(
                                    'id' => 'stickymenu_off',
                                    'value' => 0,
                                    'label' => $this->l('Off')
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Sticky Search'),
                            'name' => 'FIELD_stickySearch',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'stickysearch_on',
                                    'value' => 1,
                                    'label' => $this->l('On')
                                ),
                                array(
                                    'id' => 'stickysearch_off',
                                    'value' => 0,
                                    'label' => $this->l('Off')
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Sticky Cart'),
                            'name' => 'FIELD_stickyCart',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'stickymenucart_on',
                                    'value' => 1,
                                    'label' => $this->l('On')
                                ),
                                array(
                                    'id' => 'stickycart_off',
                                    'value' => 0,
                                    'label' => $this->l('Off')
                                )
                            )
                        )
		    ),
                    // Submit Button
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'name' => 'savefieldThemeConfig'
                    )
                )
            ),
            'field-categorypages' => array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Category Pages'),
                        'icon' => 'icon-cog'
                    ),
                    'input' => array(
			array(
                            'type' => 'switch',
                            'label' => $this->l('Display categories as a list of products instead of the default grid-based display'),
                            'name' => 'PS_GRID_PRODUCT',
                            'required' => false,
                            'is_bool' => true,
			    'desc' => $this->l('Works only for first-time users. This setting is overridden by the user\'s choice as soon as the user cookie is set.'),
                            'values' => array(
                                array(
                                    'id' => 'grid_list_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'grid_list_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Show subcategories'),
                            'name' => 'FIELD_subcategories',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'subcategories_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'subcategories_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Show color options'),
                            'name' => 'FIELD_categoryShowColorOptions',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'coloroptions_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'coloroptions_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Show stock information'),
                            'name' => 'FIELD_categoryShowStockInfo',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'stockinfo_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'stockinfo_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            )
                        )
                    ),
                    // Submit Button
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'name' => 'savefieldThemeConfig'
                    )
                )
            ),
            'field-productpages' => array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Product Pages'),
                        'icon' => 'icon-cog'
                    ),
                    'input' => array(
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Show product reference code'),
                            'name' => 'FIELD_productShowReference',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'productreference_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'productreference_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Show product condition'),
                            'name' => 'FIELD_productShowCondition',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'productcondition_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'productcondition_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Show product manufacturer name'),
                            'name' => 'FIELD_productShowManName',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'productmanname_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'productmanname_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable Vertical thumbnail list'),
                            'name' => 'FIELD_productVerticalThumb',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'productVerticalThumb_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'productVerticalThumb_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable Upsell Product Slider'),
                            'name' => 'FIELD_productUpsell',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'productUpsell_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'productUpsell_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            )
                        )
                    ),
                    // Submit Button
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'name' => 'savefieldThemeConfig'
                    )
                )
            ),
            'field-fonts' => array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Fonts'),
                        'icon' => 'icon-cog'
                    ),
                    'input' => array(
                        array(
                            'type' => 'switch',
                            'name' => 'FIELD_includeCyrillicSubset',
                            'label' => $this->l('Include Cyrillic subsets'),
                            'desc' => $this->l('If the selected font has support for Cyrillic subset, Fieldthemes will automatically include it if selected Yes. To see which fonts have Cyrillic subsets support: https://www.google.com/fonts'),
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'cyrillic_on',
                                    'value' => 1,
                                    'label' => $this->l('Include Cyrillic')
                                ),
                                array(
                                    'id' => 'cyrillic_off',
                                    'value' => 0,
                                    'label' => $this->l('Exclude Cyrillic')
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'name' => 'FIELD_includeGreekSubset',
                            'label' => $this->l('Include Greek subsets'),
                            'desc' => $this->l('If the selected font has support for Greek subset, Fieldthemes will automatically include it if selected Yes. To see which fonts have Greek subsets support: https://www.google.com/fonts'),
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'greek_on',
                                    'value' => 1,
                                    'label' => $this->l('Include Greek')
                                ),
                                array(
                                    'id' => 'greek_off',
                                    'value' => 0,
                                    'label' => $this->l('Exclude Greek')
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'name' => 'FIELD_includeVietnameseSubset',
                            'label' => $this->l('Include Vietnamese subset'),
                            'desc' => $this->l('If the selected font has support for Vietnamese subset, Fieldthemes will automatically include it if selected Yes. To see which fonts have Vietnamese subset support: https://www.google.com/fonts'),
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'vietnamese_on',
                                    'value' => 1,
                                    'label' => $this->l('Include Vietnamese')
                                ),
                                array(
                                    'id' => 'vietnamese_off',
                                    'value' => 0,
                                    'label' => $this->l('Exclude Vietnamese')
                                )
                            )
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'FIELD_mainFont',
                            'label' => $this->l('Main Font Family'),
                            'required' => false,
                            'lang' => false,
                            'options' => array(
                                'query' => $fontOptions,
                                'id' => 'value',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'FIELD_titleFont',
                            'label' => $this->l('Title Font Family'),
                            'required' => false,
                            'lang' => false,
                            'options' => array(
                                'query' => $fontOptions,
                                'id' => 'value',
                                'name' => 'name'
                            )
                        )
                    ),
                    // Submit Button
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'name' => 'savefieldThemeConfig'
                    )
                )
            ),
            'field-colors' => array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Colors'),
                        'icon' => 'icon-cog'
                    ),
                    'input' => array(
                        array(
                            'type' => 'color',
                            'name' => 'FIELD_mainColorScheme',
                            'label' => $this->l('Main color scheme'),
                            'size' => 20,
                            'required' => false,
                            'lang' => false
                        ),
                        array(
                            'type' => 'color',
                            'name' => 'FIELD_activeColorScheme',
                            'label' => $this->l('Active color scheme'),
                            'size' => 20,
                            'required' => false,
                            'lang' => false
                        )
                    ),
                    // Submit Button
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'name' => 'savefieldThemeConfig'
                    )
                )
            ),
            'field-background' => array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Backgrounds'),
                        'icon' => 'icon-cog'
                    ),
                    'input' => array(
                        array(
                            'type' => 'color',
                            'name' => 'FIELD_backgroundColor',
                            'label' => $this->l('Background color'),
                            'size' => 20,
                            'required' => false,
                            'lang' => false
                        ),
                        array(
                            'type' => 'file',
                            'name' => 'FIELD_backgroundImage',
                            'label' => $this->l('Background image'),
                            'size' => 20,
                            'required' => false,
                            'lang' => false
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'FIELD_backgroundRepeat',
                            'label' => $this->l('Background repeat'),
                            'required' => false,
                            'lang' => false,
                            'options' => array(
                                'query' => $backgroundRepeatOptions,
                                'id' => 'value',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'FIELD_backgroundAttachment',
                            'label' => $this->l('Background attachment'),
                            'required' => false,
                            'lang' => false,
                            'options' => array(
                                'query' => $backgroundAttachmentOptions,
                                'id' => 'value',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'FIELD_backgroundSize',
                            'label' => $this->l('Background size'),
                            'required' => false,
                            'lang' => false,
                            'options' => array(
                                'query' => $backgroundSizeOptions,
                                'id' => 'value',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'color',
                            'name' => 'FIELD_bodyBackgroundColor',
                            'label' => $this->l('Body background color'),
                            'desc' => $this->l('Body background color only visible in "Boxed" mode.'),
                            'size' => 20,
                            'required' => false,
                            'lang' => false
                        ),
                        array(
                            'type' => 'file',
                            'name' => 'FIELD_bodyBackgroundImage',
                            'label' => $this->l('Body background image'),
                            'desc' => $this->l('Body background image only visible in "Boxed" mode.'),
                            'size' => 20,
                            'required' => false,
                            'lang' => false
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'FIELD_bodyBackgroundRepeat',
                            'label' => $this->l('Body background repeat'),
                            'required' => false,
                            'lang' => false,
                            'options' => array(
                                'query' => $backgroundRepeatOptions,
                                'id' => 'value',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'FIELD_bodyBackgroundAttachment',
                            'label' => $this->l('Body background attachment'),
                            'required' => false,
                            'lang' => false,
                            'options' => array(
                                'query' => $backgroundAttachmentOptions,
                                'id' => 'value',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'FIELD_bodyBackgroundSize',
                            'label' => $this->l('Body background size'),
                            'required' => false,
                            'lang' => false,
                            'options' => array(
                                'query' => $backgroundSizeOptions,
                                'id' => 'value',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'file',
                            'name' => 'FIELD_breadcrumbBackgroundImage',
                            'label' => $this->l('Breadcrumb background image'),
                            'size' => 20,
                            'required' => false,
                            'lang' => false
                        )
                    ),
                    // Submit Button
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'name' => 'savefieldThemeConfig'
                    )
                )
            ),
            'field-codes' => array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Custom Codes'),
                        'icon' => 'icon-cog'
                    ),
                    'input' => array(
                        array(
                            'type' => 'textarea',
                            'name' => 'FIELD_customCSS',
                            'desc' => $this->l('Important Note: Use this area if only there are rules you cannot override with using normal css files. This will add css rules as inline code and it is not the best practice. Try using "custom.css" file located under "themes/[theme_name]/css/" folder to add your custom css rules.'),
                            'rows' => 10,
                            'label' => $this->l('Custom CSS Code'),
                            'required' => false,
                            'lang' => false
                        ),
                        array(
                            'type' => 'textarea',
                            'name' => 'FIELD_customJS',
                            'rows' => 10,
                            'label' => $this->l('Custom JS Code'),
                            'required' => false,
                            'lang' => false
                        )
                    ),
                    // Submit Button
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'name' => 'savefieldThemeConfig'
                    )
                )
            )
        );

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        $helper->default_form_language = $id_default_lang;
        $helper->allow_employee_form_lang = $id_default_lang;

        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            )
        );

        foreach ($languages as $language) {
            $helper->languages[] = array(
                'id_lang' => $language['id_lang'],
                'iso_code' => $language['iso_code'],
                'name' => $language['name'],
                'is_default' => ($id_default_lang == $language['id_lang'] ? 1 : 0)
            );
        }

        // Load standard field values
        foreach ($this->_standardConfig as $key => $standardField) {
            $helper->fields_value[$standardField] = Configuration::get($standardField);
        }

        // Load css field values
        foreach ($this->_styleConfig as $key => $cssField) {
            $helper->fields_value[$cssField] = Configuration::get($cssField);
        }

        // Custom variables
        $helper->tpl_vars = array(
            'fieldtabs' => $this->_getTabs(),
            'imagePath' => _MODULE_DIR_ . $this->name . '/views/img/front/bg/',
            'shopId' => $id_shop
        );

        return $helper->generateForm($fields_form);
    }


    /* ------------------------------------------------------------- */
    /*  GET TABS
    /* ------------------------------------------------------------- */
    private function _getTabs()
    {
        $tabArray = array(
            'General' => 'fieldset_field-general',
            'Header' => 'fieldset_field-header_1',
            'Category Pages' => 'fieldset_field-categorypages_2',
            'Product Pages' => 'fieldset_field-productpages_3',
            'Fonts' => 'fieldset_field-fonts_4',
            'Colors' => 'fieldset_field-colors_5',
            'Background' => 'fieldset_field-background_6',
            'Custom Codes' => 'fieldset_field-codes_7',
        );

        return $tabArray;
    }

    /* ------------------------------------------------------------- */
    /*  WRITE CSS
    /* ------------------------------------------------------------- */
    private function _writeCss()
    {
        $id_shop = $this->context->shop->id;

        $cssFile = _PS_MODULE_DIR_ . $this->name . '/views/css/front/configCss-' . $id_shop . '.css';
        $handle = fopen($cssFile, 'w');

        $config = $this->_getThemeConfig();

        // Starting of the cssCode
        $cssCode = '';

        // Read _cssRules and create css rules
        foreach ($this->_cssRules as $configName => $css) {

            // Check if the config is set, and it's not the default value
            if ($config[$configName] == '') {
                continue;
            }
            if (isset($this->_configDefaults[$configName]) && $config[$configName] == $this->_configDefaults[$configName]) {
                continue;
            }

            // If the config is a font config then do this and write the css rule for it
            if ( in_array($configName, $this->_fontConfig) ){

                // Check if the font is one of the web-safe fonts,
                // if it's then just write the basic font-family rule
                if ( in_array($config[$configName], $this->_websafeFonts) ){
                    foreach ($css as $line){
                        $cssCode .= $line['selector'] . '{' . $line['rule'] . ':' . (isset($line['prefix']) ? $line['prefix'] : '') . (isset($line['value']) ? $line['value'] : '"' . $config[$configName] . '", "sans-serif"') . (isset($line['suffix']) ? $line['suffix'] : '') . ';}';
                    }
                    continue;
                }

                // If not then do some preparations for google fonts
                // then write the proper css rule
                $googleFontName = str_replace(' ', '+', $config[$configName]);
                $googleFontSubsets = $this->_googleFonts[$config[$configName]]['subsets'];
                $googleFontVariants = $this->_googleFonts[$config[$configName]]['variants'];

                $isIncludeCyrillic = Configuration::get('FIELD_includeCyrillicSubset');
                $isIncludeGreek = Configuration::get('FIELD_includeCyrillicSubset');
                $isIncludeVietnamese = Configuration::get('FIELD_includeCyrillicSubset');

                $importCode = '@import "//fonts.googleapis.com/css?family='.$googleFontName;

                /* VARIANTS */
                // Include normal (400)
		if ( in_array('300', $googleFontVariants) ){
                    $importCode .= ':300';
		    $importCode .= ',400';
                }
		else
		    $importCode .= ':400';
		    
                // Include bold if available
                if ( in_array('700', $googleFontVariants) ){
                    $importCode .= ',700';
                }

                /* SUBSETS */
                // Include Latin and Latin-ext
                $importCode .= '&subset=latin,latin-ext';

                // Include Cyrillic subsets if they are selected and available for the font
                if ($isIncludeCyrillic){
                    if ( in_array('cyrillic', $googleFontSubsets) ){
                        $importCode .=',cyrillic';
                    }
                    if ( in_array('cyrillic-ext', $googleFontSubsets) ){
                        $importCode .=',cyrillic-ext';
                    }
                }

                // Include Greek subsets if they are selected and available for the font
                if ($isIncludeGreek){
                    if ( in_array('greek', $googleFontSubsets) ){
                        $importCode .=',greek';
                    }
                    if ( in_array('cyrillic-ext', $googleFontSubsets) ){
                        $importCode .=',greek-ext';
                    }
                }

                // Include Vietnamese subset if it is selected and available for the font
                if ($isIncludeVietnamese && in_array('vietnamese', $googleFontSubsets)){
                    $importCode .=',greek';
                }

                $importCode .= '";';

                $cssCode = $importCode . $cssCode;

                foreach ($css as $line){
                    $cssCode .= $line['selector'] . '{' . $line['rule'] . ':' . (isset($line['prefix']) ? $line['prefix'] : '') . (isset($line['value']) ? $line['value'] : '"' . $config[$configName] . '", "Helvetica", "Arial", "sans-serif"') . (isset($line['suffix']) ? $line['suffix'] : '') . ';}';
                }

                continue;
            }

            // Otherwise create the general css rule for it
            foreach ($css as $line) {
                $cssCode .= (isset($line['media']) ? $line['media'].'{' : '') . $line['selector'] . '{' . $line['rule'] . ':' . (isset($line['prefix']) ? $line['prefix'] : '') . (isset($line['value']) ? $line['value'] : $config[$configName]) . (isset($line['suffix']) ? $line['suffix'] : '') . ';}' . (isset($line['media']) ? '}' : '');
            }


        }

        $response = fwrite($handle, $cssCode);

        return $response;

    }

    /* ------------------------------------------------------------- */
    /*  GET THEME CONFIG
    /* ------------------------------------------------------------- */
    private function _getThemeConfig($standard = true, $style = true, $multiLang = true)
    {
        $id_default_lang = $this->context->language->id;

        $config = array();

        if ($standard) {
            foreach ($this->_standardConfig as $configItem) {
                $config[$configItem] = Configuration::get($configItem);
            }
        }

        if ($style) {
            foreach ($this->_styleConfig as $configItem) {
                $config[$configItem] = Configuration::get($configItem);
            }
        }

        return $config;
    }
    /* ------------------------------------------------------------- */
    /*  PREPARE FOR HOOK
    /* ------------------------------------------------------------- */
    private function _prepHook($params)
    {
        $config = $this->_getThemeConfig();
        $controller_name = Dispatcher::getInstance()->getController();

        if ($config) {
            foreach ($config as $key => $value) {
                $this->smarty->assignGlobal($key, $value);
            }
        }


	$image_types =(ImageType::getImagesTypes());
		foreach($image_types as $image_type){
			 $this->smarty->assignGlobal('size_'.$image_type['name'],Image::getSize($image_type['name']));	
		}
		$this->smarty->assignGlobal('FIELD_IMG_BREADCRUMB',$this->context->link->getMediaLink((_MODULE_DIR_ . $this->name . '/views/img/front/bg/').Configuration::get('FIELD_breadcrumbBackgroundImage')));
        /* Show paneltool */
        $FIELD_showPanelTool = Configuration::get('FIELD_showPanelTool');
        if($FIELD_showPanelTool){
			$this->context->controller->addJS($this->_path.'views/js/front/jquery.colorpicker.js');
			$this->context->controller->addJS($this->_path.'views/js/front/jquery.fieldcolortool.js');
            $this->context->controller->addCSS(_MODULE_DIR_ . $this->name . '/views/css/front/field.cltool.css');
            $this->smarty->assignGlobal('FIELD_PANELTOOL_TPL', _PS_MODULE_DIR_.$this->name.'/views/templates/front/colortool.tpl');
        }
		
		 /* LOAD CSS */
				$this->context->controller->registerStylesheet('font_awesome_css', '/assets/field-css/font-awesome/font-awesome.css',['media' => 'all', 'priority' => -1]);
		$this->context->controller->registerStylesheet('field_style_css', '/assets/field-css/field_style.css',['media' => 'all', 'priority' => 1001]);
        /* LOAD JS */
			$this->context->controller->registerJavascript('jquery-1.11.0.min.js', '/assets/field-js/jquery-1.11.0.min.js', ['position' => 'head', 'priority' => 0]);	
        // Load custom JS files
	$this->context->controller->registerJavascript('field-jquery.plugins', '/assets/field-js/jquery.plugins.js', ['position' => 'bottom', 'priority' => 10000]);
	$productVerticalThumb = Configuration::get('FIELD_productVerticalThumb');
//	var_dump($productVerticalThumb);
	if (isset($productVerticalThumb) && $productVerticalThumb){
		$this->context->controller->registerJavascript('field-themes-jquery.carouFredSel-6.2.1.min', '/assets/js/field-js/jquery.carouFredSel-6.2.1.min.js', ['position' => 'bottom', 'priority' => 10000]);
	}
		
		$this->context->controller->registerJavascript('field-jquery-field', '/assets/field-js/jquery.field.js', ['position' => 'bottom', 'priority' => 10000]);
		
	$this->context->controller->registerJavascript('field-jquery.field_title', '/assets/field-js/jquery.field_title.js', ['position' => 'bottom', 'priority' => 10000]);
	$this->context->controller->registerJavascript('field-fancybox', '/assets/field-js/fancybox/jquery.fancybox.js');
	$this->context->controller->registerStylesheet('field-fancyboxcss', '/assets/field-js/fancybox/jquery.fancybox.css');
 /* ------------------------------------------------------------- */
 	$this->context->controller->addJqueryUI('ui.autocomplete');
        return true;
    }


    /* ------------------------------------------------------------- */
    /*  HOOK (displayHeader)
    /* ------------------------------------------------------------- */
    public function hookDisplayHeader($params)
    {		
        $this->_prepHook($params);
		$id_shop = $this->context->shop->id;
        /* We are loading css files in this hook, because
         * this is the only way to make sure these css files
         * will override any other css files.. Otherwise
         * module positioning will cause a lot of issues.
         */
	/* LOAD JS */
        /* LOAD CSS */
        // Load auto-created css files
		
        $cssFile = 'configCss-' . $id_shop . '.css';
        if (file_exists(_PS_MODULE_DIR_ . $this->name . '/views/css/front/' . $cssFile)) {
			$this->context->controller->registerStylesheet('configCss',$this->context->controller->getAssetUriFromLegacyDeprecatedMethod($this->_path.'views/css/front/' . $cssFile),['media' => 'all', 'priority' => 1002]);
        }
        else {
			$this->context->controller->registerStylesheet('configCss',$this->context->controller->getAssetUriFromLegacyDeprecatedMethod($this->_path.'views/css/front/configCSS-default.css'),['media' => 'all', 'priority' => 1002]);

        }
        // Load custom.css file
        $this->context->controller->addCSS(_THEME_CSS_DIR_ . 'custom.css');	
    }
}
