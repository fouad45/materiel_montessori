<?php

if (!defined('_PS_VERSION_'))
    exit;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use Symfony\Component\HttpKernel\HttpKernelInterface;
include_once(_PS_MODULE_DIR_ . 'fieldtabproductsisotope/model/fieldTabProductsIsotopeModel.php');

class FieldTabProductsIsotope extends Module
{
    private $_output = '';

    function __construct()
    {
        $this->name = 'fieldtabproductsisotope';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'fieldthemes';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Field Tabs Product Isotope');
        $this->description = $this->l('Frontpage product tabs (Isotope)');
    }

    /* ------------------------------------------------------------- */
    /*  INSTALL THE MODULE
    /* ------------------------------------------------------------- */
    public function install()
    {
        if (Shop::isFeatureActive()){
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install()
		
		 	   && $this->registerHook('displayHeader')
               && $this->registerHook('tabproductsisotope')
               && $this->registerHook('actionShopDataDuplication')
               && $this->_createTables()
               && $this->_createConfigs()
               && $this->_installDemoData()
               && $this->_createTab();
    }

    /* ------------------------------------------------------------- */
    /*  UNINSTALL THE MODULE
    /* ------------------------------------------------------------- */
    public function uninstall()
    {
        return parent::uninstall()
               && $this->unregisterHook('actionShopDataDuplication')
               && $this->_deleteTables()
               && $this->_deleteConfigs()
               && $this->_deleteTab();
    }

    /* ------------------------------------------------------------- */
    /*  CREATE THE TABLES
    /* ------------------------------------------------------------- */
    private function _createTables()
    {
        $response = (bool) Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldtabproductsisotope` (
                `id_fieldtabproductsisotope` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `active` tinyint(1) unsigned NOT NULL,
                `position` int(5) unsigned NOT NULL,
                `tab_type` varchar(255) NOT NULL,
                `tab_content` text NOT NULL,
                `banner_image` text,
                `banner_link` text,
                `title_image` text,
                `countdown_from` datetime,
                `countdown_to` datetime,
                PRIMARY KEY (`id_fieldtabproductsisotope`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');

        $response &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldtabproductsisotope_lang` (
                `id_fieldtabproductsisotope` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `title` varchar(255) NOT NULL,
                PRIMARY KEY (`id_fieldtabproductsisotope`,`id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');

        $response &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldtabproductsisotope_shop` (
                `id_fieldtabproductsisotope` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_fieldtabproductsisotope`,`id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');

        return $response;
    }

    /* ------------------------------------------------------------- */
    /*  DELETE THE TABLES
    /* ------------------------------------------------------------- */
    private function _deleteTables()
    {
        return Db::getInstance()->execute('
                DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'fieldtabproductsisotope`, `' . _DB_PREFIX_ . 'fieldtabproductsisotope_lang`, `' . _DB_PREFIX_ . 'fieldtabproductsisotope_shop`;
        ');
    }

    /* ------------------------------------------------------------- */
    /*  CREATE CONFIGS
    /* ------------------------------------------------------------- */
    private function _createConfigs()
    {
        $response = Configuration::updateValue($this->name . '_PRICES', 1);
        $response &= Configuration::updateValue($this->name . '_CARTBUTTONS', 1);
        $response &= Configuration::updateValue($this->name . '_MAXPRDCTS', 9);
        $response &= Configuration::updateValue($this->name . '_DISABLESHOWALL', 0);
        $response &= Configuration::updateValue($this->name . '_RANDOMORDER', 0);

        return $response;
    }

    /* ------------------------------------------------------------- */
    /*  DELETE CONFIGS
    /* ------------------------------------------------------------- */
    private function _deleteConfigs()
    {
        $response = Configuration::deleteByName($this->name . '_PRICES');
        $response &= Configuration::deleteByName($this->name . '_CARTBUTTONS');
        $response &= Configuration::deleteByName($this->name . '_MAXPRDCTS');
        $response &= Configuration::deleteByName($this->name . '_DISABLESHOWALL');
        $response &= Configuration::deleteByName($this->name . '_RANDOMORDER');
	
        return $response;
    }

    /* ------------------------------------------------------------- */
    /*  INSTALL DEMO DATA
    /* ------------------------------------------------------------- */
    private function _installDemoData()
    {
        $languages = $this->context->language->getLanguages(true);

        // New Products tab
        $fieldTabProductsIsotope = new FieldTabProductsIsotopeModel();
        foreach ($languages as $language){
            $fieldTabProductsIsotope->title[$language['id_lang']] = 'New Arrivals';
        }
        $fieldTabProductsIsotope->tab_type = 'new';
        $fieldTabProductsIsotope->add();

        // Special Products tab
        $fieldTabProductsIsotope = new FieldTabProductsIsotopeModel();
        foreach ($languages as $language){
            $fieldTabProductsIsotope->title[$language['id_lang']] = 'Onsale';
        }
        $fieldTabProductsIsotope->tab_type = 'special';
        $fieldTabProductsIsotope->add();

        // Featured Products tab
        $fieldTabProductsIsotope = new FieldTabProductsIsotopeModel();
        foreach ($languages as $language){
            $fieldTabProductsIsotope->title[$language['id_lang']] = 'Bestseller';
        }
        $fieldTabProductsIsotope->tab_type = 'bestseller';
        $fieldTabProductsIsotope->add();
        
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
        } else {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = "AdminFieldMenu";
            foreach (Language::getLanguages() as $lang){
                $parentTab->name[$lang['id_lang']] = "FIELDTHEMES";
            }
            $parentTab->id_parent = 0;
            $parentTab->module = '';
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
				$parentTab_2->module = '';
				$response &= $parentTab_2->add();
			}
			// Created tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "AdminFieldTabProductsIsotope";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang){
            $tab->name[$lang['id_lang']] = "Tabs Products Isotope";
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
        $id_tab = Tab::getIdFromClassName('AdminFieldTabProductsIsotope');
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
    /*  GET CONTENT
    /* ------------------------------------------------------------- */
    public function getContent()
    {   
        $errors = array();

        if (Tools::isSubmit('submit'.$this->name)){

            if (Tools::isSubmit('fieldtabproductsisotope_prices')){
                Configuration::updateValue($this->name . '_PRICES', Tools::getValue('fieldtabproductsisotope_prices'));
            }

            if (Tools::isSubmit('fieldtabproductsisotope_cartbuttons')){
                Configuration::updateValue($this->name . '_CARTBUTTONS', Tools::getValue('fieldtabproductsisotope_cartbuttons'));
            }

            // Validate numeric values
            if (Tools::isSubmit('fieldtabproductsisotope_maxprdcts')){
                if (Validate::isInt(Tools::getValue('fieldtabproductsisotope_maxprdcts'))){
                    Configuration::updateValue($this->name . '_MAXPRDCTS', Tools::getValue('fieldtabproductsisotope_maxprdcts'));
                } else {
                    $errors[] = $this->l('Max product count per tab must be a numeric value!');
                }
            }

            if (Tools::isSubmit('fieldtabproductsisotope_disableshowall')){
                Configuration::updateValue($this->name . '_DISABLESHOWALL', Tools::getValue('fieldtabproductsisotope_disableshowall'));
            }

            if (Tools::isSubmit('fieldtabproductsisotope_randomorder')){
                Configuration::updateValue($this->name . '_RANDOMORDER', Tools::getValue('fieldtabproductsisotope_randomorder'));
            }

            // Prepare the output
            if (count($errors)){
                $this->_output .= $this->displayError(implode('<br />', $errors));
            } else {
                $this->_output .= $this->displayConfirmation($this->l('Configuration updated'));
            }

        }

        return $this->_output.$this->displayForm();
    }

    /* ------------------------------------------------------------- */
    /*  DISPLAY CONFIGURATION FORM
    /* ------------------------------------------------------------- */
    public function displayForm()
    {
        // Get default Language
        $id_default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $fields_form = array(
            'fieldtabproductsisotope-general' => array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Isotope Tabs Options'),
                        'icon' => 'icon-cogs'
                    ),
                    'input' => array(
                        array(
                            'type' => 'hidden',
                            'label' => $this->l('Show prices'),
                            'name' => 'fieldtabproductsisotope_prices',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'prices_on',
                                    'value' => 1,
                                    'label' => $this->l('Show')
                                ),
                                array(
                                    'id' => 'prices_off',
                                    'value' => 0,
                                    'label' => $this->l('Hide')
                                )
                            )
                        ),
                        array(
                            'type' => 'hidden',
                            'label' => $this->l('Show "Add to Cart" buttons'),
                            'name' => 'fieldtabproductsisotope_cartbuttons',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'cartbutton_on',
                                    'value' => 1,
                                    'label' => $this->l('Show')
                                ),
                                array(
                                    'id' => 'cartbutton_off',
                                    'value' => 0,
                                    'label' => $this->l('Hide')
                                )
                            )
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Show "Show All" tab'),
                            'name' => 'fieldtabproductsisotope_disableshowall',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'showall_on',
                                    'value' => 1,
                                    'label' => $this->l('Show')
                                ),
                                array(
                                    'id' => 'showall_off',
                                    'value' => 0,
                                    'label' => $this->l('Hide')
                                )
                            )
                        ),
                        array(
                            'type' => 'text',
                            'name' => 'fieldtabproductsisotope_maxprdcts',
                            'label' => $this->l('Max. product count per tab'),
                            'required' => true,
                            'lang' => false,
                            'suffix' => $this->l('products per tab')
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Random order item'),
                            'name' => 'fieldtabproductsisotope_randomorder',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'randomorder_on',
                                    'value' => 1,
                                    'label' => $this->l('Show')
                                ),
                                array(
                                    'id' => 'randomorder_off',
                                    'value' => 0,
                                    'label' => $this->l('Hide')
                                )
                            )
                        )
                    ),
                    // Submit Button
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'name' => 'saveIsotopeTabsOptions'
                    )
                )
            )
        );

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $helper->default_form_language = $id_default_lang;
        $helper->allow_employee_form_lang = $id_default_lang;

        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'editTabs' => array(
                'desc' => $this->l('Edit Tabs'),
                'href' => $this->context->link->getAdminLink('AdminFieldTabProductsIsotope'),
                'imgclass' => 'edit'
            )
        );

        // Load current values
        $helper->fields_value['fieldtabproductsisotope_prices'] = Configuration::get($this->name . '_PRICES');
        $helper->fields_value['fieldtabproductsisotope_cartbuttons'] = Configuration::get($this->name . '_CARTBUTTONS');
        $helper->fields_value['fieldtabproductsisotope_maxprdcts'] = Configuration::get($this->name . '_MAXPRDCTS');
        $helper->fields_value['fieldtabproductsisotope_disableshowall'] = Configuration::get($this->name . '_DISABLESHOWALL');
        $helper->fields_value['fieldtabproductsisotope_randomorder'] = Configuration::get($this->name . '_RANDOMORDER');

        return $helper->generateForm($fields_form);

    }

    /* ------------------------------------------------------------- */
    /*  HOOK THE MODULE INTO SHOP DATA DUPLICATION ACTION
    /* ------------------------------------------------------------- */
    public function hookActionShopDataDuplication($params)
    {
        Db::getInstance()->execute('
            INSERT IGNORE INTO '._DB_PREFIX_.'fieldtabproductsisotope_shop (id_fieldtabproductsisotope, id_shop)
            SELECT id_fieldtabproductsisotope, '.(int)$params['new_id_shop'].'
            FROM '._DB_PREFIX_.'fieldtabproductsisotope_shop
            WHERE id_shop = '.(int)$params['old_id_shop']
        );
    }


    /* ------------------------------------------------------------- */
    /*
    /*  FRONT OFFICE RELATED STUFF
    /*
    /* ------------------------------------------------------------- */

    /* ------------------------------------------------------------- */
    /*  GET TABS LIST
    /* ------------------------------------------------------------- */

    private function _getTabsList($id_shop, $id_lang)
    {
        $fieldTabProductsIsotope = new FieldTabProductsIsotopeModel();
        $tabIds = $fieldTabProductsIsotope->getTabIds($id_shop);

        $response = array();

        $patterns = array(
            '/(%)/',
            '/(\s+)/'
        );
        $replacements = array(
            '',
            '-'
        );

        if (Configuration::get($this->name . '_DISABLESHOWALL')){
            $response[] = array(
                'title'  => $this->l('Show All'),
				'title_image'  => '',
                'filter' => '*'
            );
        }

        foreach ($tabIds as $key => $tabId)
        {
            $fieldTabProductsIsotope = new FieldTabProductsIsotopeModel($tabId['id_fieldtabproductsisotope'], $id_lang);
            
            $response[] = array(
                'title'  => $fieldTabProductsIsotope->title,
                'title_image'  => $fieldTabProductsIsotope->title_image,
                'filter' => strtolower(preg_replace($patterns, $replacements, $fieldTabProductsIsotope->title))
            );
        }

        if ($response){
            return $response;
        } else {
            return NULL;
        }
    }

    /* ------------------------------------------------------------- */
    /*  GET TAB CONTENT
    /* ------------------------------------------------------------- */
    public static function getBestSales($id_lang, $page_number = 0, $nb_products = 3, $order_by = null, $order_way = null) {
        if ($page_number < 0)
            $page_number = 0;
        if ($nb_products < 1)
            $nb_products = 3;
        $final_order_by = $order_by;
        $order_table = '';
        if (is_null($order_by) || $order_by == 'position' || $order_by == 'price')
            $order_by = 'sales';
        if ($order_by == 'date_add' || $order_by == 'date_upd')
            $order_table = 'product_shop';
        if (is_null($order_way) || $order_by == 'sales')
            $order_way = 'DESC';
        $groups = FrontController::getCurrentCustomerGroups();
        $sql_groups = (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= 1');
        $interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;

        $prefix = '';
        if ($order_by == 'date_add')
            $prefix = 'p.';

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
					pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
					pl.`meta_keywords`, pl.`meta_title`, pl.`name`,
					m.`name` AS manufacturer_name, p.`id_manufacturer` as id_manufacturer,
					MAX(image_shop.`id_image`) id_image, il.`legend`,
					ps.`quantity` AS sales, t.`rate`, pl.`meta_keywords`, pl.`meta_title`, pl.`meta_description`,
					DATEDIFF(p.`date_add`, DATE_SUB(NOW(),
					INTERVAL ' . $interval . ' DAY)) > 0 AS new
				FROM `' . _DB_PREFIX_ . 'product_sale` ps
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON ps.`id_product` = p.`id_product`
				' . Shop::addSqlAssociation('product', 'p', false) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`)' .
            Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `' . _DB_PREFIX_ . 'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`)
					AND tr.`id_country` = ' . (int) Context::getContext()->country->id . '
					AND tr.`id_state` = 0
				LEFT JOIN `' . _DB_PREFIX_ . 'tax` t ON (t.`id_tax` = tr.`id_tax`)
				' . Product::sqlStock('p') . '
				WHERE product_shop.`active` = 1
					AND product_shop.`visibility` != \'none\'
					AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `' . _DB_PREFIX_ . 'category_group` cg
						LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` ' . $sql_groups . '
					)
				GROUP BY product_shop.id_product
				ORDER BY ' . (!empty($order_table) ? '`' . pSQL($order_table) . '`.' : '') . '`' . pSQL($order_by) . '` ' . pSQL($order_way) . '
				LIMIT ' . (int) ($page_number * $nb_products) . ', ' . (int) $nb_products;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($final_order_by == 'price')
            Tools::orderbyPrice($result, $order_way);
        if (!$result)
            return false;
        return Product::getProductsProperties($id_lang, $result);
    }
    
    private function _getTabContent($id_fieldtabproductsisotope)
    {
        $id_default_lang = $this->context->language->id;
        $fieldTabProductsIsotope = new FieldTabProductsIsotopeModel($id_fieldtabproductsisotope, $id_default_lang);

        $patterns = array(
            '/(%)/',
            '/(\s+)/'
        );
        $replacements = array(
            '',
            '-'
        );
        
        if (Validate::isLoadedObject($fieldTabProductsIsotope))
        {
            $type = strtolower(preg_replace($patterns, $replacements, $fieldTabProductsIsotope->title));
            $maxProductCount = Configuration::get($this->name . '_MAXPRDCTS');
            $products = array();
            
            $now = date('Y-m-d H:i:00');
            $start_date = $fieldTabProductsIsotope->countdown_from;
            if (strtotime($start_date) > strtotime($now)){
                $end_date = "0000-00-00 00:00:00";
            } else {
                $end_date = $fieldTabProductsIsotope->countdown_to;
            }
            if ($fieldTabProductsIsotope->banner_image || $end_date != "0000-00-00 00:00:00"){
                $banner = array(
                    'banner_image' => $fieldTabProductsIsotope->banner_image,
                    'banner_link' => $fieldTabProductsIsotope->banner_link,
                    'banner_type' => $type,
                    'countdown_to' => $end_date
                );
            }else {
                $banner = array();
            }
            switch ($fieldTabProductsIsotope->tab_type)
            {
                case 'featured':
                    $category = new Category($this->context->shop->getCategory(), $id_default_lang);
		    $products = $category->getProducts($id_default_lang, 1, $maxProductCount);
                    break;

                case 'new':
		    $products = Product::getNewProducts($id_default_lang, 0, $maxProductCount);
                    break;

                case 'special':
                    $products = Product::getPricesDrop($id_default_lang, 0, $maxProductCount);
                    break;
                
                case 'bestseller':
                    $products = $this->getBestSales((int) Context::getContext()->language->id, 0, ($maxProductCount ? $maxProductCount : 8), null, null);
                    break;
                
                case 'category':
                    $category = new Category($fieldTabProductsIsotope->tab_content, $id_default_lang);
		    $products = $category->getProducts($id_default_lang, 1, $maxProductCount);
                    break;
                
                case 'manufacturers':
                    $manufacturers = new Manufacturer();
                    $products = $manufacturers -> getProducts($fieldTabProductsIsotope->tab_content,$id_default_lang, 1, $maxProductCount, 'position');
                    break;

                case 'custom':
                    $productIDs = explode(",", $fieldTabProductsIsotope->tab_content);
                    $i = 0;
                    foreach ($productIDs as $id_product){
                        if ($i < $maxProductCount) {
                            $customProduct = get_object_vars(new Product($id_product, true, $id_default_lang));
                            $customProduct['id_product'] = $customProduct['id'];

                            $coverImage = Product::getCover($customProduct['id_product']);
                            $customProduct['id_image'] = $coverImage['id_image'];

                            $products[] = Product::getProductProperties($id_default_lang, $customProduct);

                            $i++;
                        }
                    }
                    break;
            }

            if ($products){
		if (Configuration::get($this->name . '_RANDOMORDER')){
		    shuffle($products);
		}
                return array($this->_prepareProducts($products, $type), $banner);
            } else {
                return false;
            }
        }

        return false;
    }

    /* ------------------------------------------------------------- */
    /*  PREPARE PRODUCT
    /* ------------------------------------------------------------- */

    private function _prepareProducts($products, $type)
    {
        $tabProducts = array();

        foreach ($products as $product){
            if (isset($product['id_product'])) {
                $id_product = $product['id_product'];
            } elseif (isset($product['id'])) {
                $id_product = $product['id'];
            } else {
                continue;
            }

            if ($product['active']) {
                $tabProducts[$id_product] = $product;
                $tabProducts[$id_product]['type'] = $type;
            }
        }

        if (isset($tabProducts)) {
            return $tabProducts;
        } else {
            return false;
        }
    }



    /* ------------------------------------------------------------- */
    /*  PREPARE FOR HOOK
    /* ------------------------------------------------------------- */
    private function _prepHook($params)
    {
        $id_shop = $this->context->shop->id;
        $id_default_lang = $this->context->language->id;

        $fieldTabProductsIsotope = new FieldTabProductsIsotopeModel();
        $tabIds = $fieldTabProductsIsotope->getTabIds($id_shop);

        $tab_contents = array();
        $products = array();

        foreach ($tabIds as $key => $tabId){
            $tab_contents[$tabId['id_fieldtabproductsisotope']] = $this->_getTabContent($tabId['id_fieldtabproductsisotope']);
			$tab_contents[$tabId['id_fieldtabproductsisotope']]=$tab_contents[$tabId['id_fieldtabproductsisotope']][0];
            $banner_contents[$tabId['id_fieldtabproductsisotope']] = $this->_getTabContent($tabId['id_fieldtabproductsisotope']);
			$banner_contents[$tabId['id_fieldtabproductsisotope']]=$banner_contents[$tabId['id_fieldtabproductsisotope']][1];
        }

        foreach ($tab_contents as $key => $productsArray){
            if ($productsArray){
                foreach ($productsArray as $productID => $product){
                    if (array_key_exists($productID, $products)){
                        $products[$productID]['type'] .= ' ' . $product['type'];
                    } else {
                        $products[$productID] = $product;
                    }
                }
            }
        }

		$assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );
        $products_for_template = [];
		if(is_array($products)){
        foreach ($products as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        	}
		}


        $fieldtabproductsisotope = array(
            'filters' => $this->_getTabsList($id_shop, $id_default_lang),
            'products' => $products_for_template,
            'banners' => $banner_contents,
            'showPrice' => Configuration::get($this->name . '_PRICES'),
            'showCartBtn' => Configuration::get($this->name . '_CARTBUTTONS')
        );

        $this->smarty->assign('fieldtabproductsisotope', $fieldtabproductsisotope);
    }
    /* ------------------------------------------------------------- */
    /*  hookTabproductsisotope
    /* ------------------------------------------------------------- */
    public function hookTabproductsisotope($params)
    {
        $this->_prepHook($params);
        return $this->display(__FILE__, 'fieldtabproductsisotope.tpl');
    }
    /* ------------------------------------------------------------- */
    /*  hookDisplayHeader
    /* ------------------------------------------------------------- */
	public function hookDisplayHeader($params) {
		$this->context->controller->addCSS($this->_path . 'views/css/hook/fieldtabproductsisotope.css');
        $this->context->controller->addCSS($this->_path . 'views/css/hook/isotope.css');
		$this->context->controller->addJS($this->_path.'views/js/hook/jquery.fieldtabproductsisotope.js');
		$this->context->controller->addJS($this->_path.'views/js/hook/jquery.isotope.pkgd.min.js');
	}
	/* ------------------------------------------------------------- */
	/*  hookDisplayHome
	/* ------------------------------------------------------------- */
	public function hookDisplayHome($params){
		return $this->hookTabproductsisotope($params);
	}

}