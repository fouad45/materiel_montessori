<?php

/* Withinpixels - Megamenu - 2014 - Sercan YEMEN - twitter.com/sercan */

if (!defined('_PS_VERSION_'))
    exit;
use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\ObjectPresenter;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Adapter\Configuration as ConfigurationAdapter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Debug\Debug;
include_once(_PS_MODULE_DIR_ . 'fieldmegamenu/model/fieldMegamenuModel.php');
include_once(_PS_MODULE_DIR_ . 'fieldmegamenu/model/fieldMegamenuItemsModel.php');

class FieldMegamenu extends Module
{
    private $_output = '';

    function __construct()
    {
        $this->name = 'fieldmegamenu';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'fieldthemes';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Field Megamenu');
        $this->description = $this->l('Megamenu');
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
               && $this->registerHook('displayHeaderMenu')
               && $this->registerHook('actionShopDataDuplication')
               && $this->_createTables()
               && $this->_installDemoData()
               && $this->_createTab();
    }

    /* ------------------------------------------------------------- */
    /*  UNINSTALL THE MODULE
    /* ------------------------------------------------------------- */
    public function uninstall()
    {
        return parent::uninstall()
               && $this->unregisterHook('displayHeader')
               && $this->unregisterHook('displayHeaderMenu')
               && $this->unregisterHook('actionShopDataDuplication')
               && $this->_deleteTables()
               && $this->_deleteTab();
    }

    /* ------------------------------------------------------------- */
    /*  CREATE THE TABLES
    /* ------------------------------------------------------------- */
    private function _createTables()
    {

        $response = (bool) Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldmegamenu` (
                `id_fieldmegamenu` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `active` tinyint(1) unsigned NOT NULL,
                `position` tinyint(3) unsigned NOT NULL,
                `open_in_new` tinyint(1) NOT NULL,
                `icon_class` varchar(255) NOT NULL,
                `menu_class` varchar(255) NOT NULL,
                `width_popup_class` varchar(255) NOT NULL,
                PRIMARY KEY (`id_fieldmegamenu`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        
        $response &= Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'fieldmegamenu` VALUES (1, 1, 0, 0, "menu-home", "", "");
        ');
        $response &= Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'fieldmegamenu` VALUES (2, 1, 1, 0, "", "", "");
        ');
        
        $response &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldmegamenu_lang` (
                `id_fieldmegamenu` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `title` varchar(255) NOT NULL,
                `description` varchar(255) NOT NULL,
                `link` varchar(255) NOT NULL,
                PRIMARY KEY (`id_fieldmegamenu`,`id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        
        $response &= Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'fieldmegamenu_lang` VALUES (1, 1, "HOME", "", "#");
        ');
        $response &= Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'fieldmegamenu_lang` VALUES (2, 1, "CUSTOM", "", "#");
        ');
        
        $response &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldmegamenu_shop` (
                `id_fieldmegamenu` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_fieldmegamenu`,`id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        
        $response &= Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'fieldmegamenu_shop` VALUES (1, 1);
        ');
        $response &= Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'fieldmegamenu_shop` VALUES (2, 1);
        ');
        
        $response &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldmegamenuitems` (
                `id_fieldmegamenuitems` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_fieldmegamenu` int(10) unsigned NULL,
                `active` tinyint(1) unsigned NOT NULL,
                `nleft` int(10) unsigned NOT NULL,
                `nright` int(10) unsigned NOT NULL,
                `depth` int(10) unsigned NOT NULL,
                `icon_class` varchar(255) NOT NULL,
                `menu_type` tinyint(3) unsigned NOT NULL,
                `menu_class` varchar(255) NOT NULL,
                `menu_layout` varchar(255) NOT NULL,
                `menu_image` varchar(255) NOT NULL,
                `open_in_new` tinyint(1) NOT NULL,
                `show_image` tinyint(1) NOT NULL,
                PRIMARY KEY (`id_fieldmegamenuitems`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');

        $response &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldmegamenuitems_lang` (
                `id_fieldmegamenuitems` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `title` varchar(255) NOT NULL,
                `description` varchar(255) NOT NULL,
                `link` varchar(255) NOT NULL,
                `content` text NOT NULL,
                PRIMARY KEY (`id_fieldmegamenuitems`,`id_lang`)
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
                DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'fieldmegamenu`, `' . _DB_PREFIX_ . 'fieldmegamenu_lang`, `' . _DB_PREFIX_ . 'fieldmegamenu_shop`, `' . _DB_PREFIX_ . 'fieldmegamenuitems`, `' . _DB_PREFIX_ . 'fieldmegamenuitems_lang`;
        ');
    }

    /* ------------------------------------------------------------- */
    /*  CREATE CONFIGS
    /* ------------------------------------------------------------- */
    private function _createConfigs()
    {
        return true;
    }

    /* ------------------------------------------------------------- */
    /*  DELETE CONFIGS
    /* ------------------------------------------------------------- */
    private function _deleteConfigs()
    {
        return true;
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
        } else {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = "AdminFieldMenu";
            foreach (Language::getLanguages() as $lang){
                $parentTab->name[$lang['id_lang']] = "fieldthemes";
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
        $tab->class_name = "AdminFieldMegamenu";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang){
            $tab->name[$lang['id_lang']] = "Manage Megamenu";
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
        $id_tab = Tab::getIdFromClassName('AdminFieldMegamenu');
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
    /*  HOOK THE MODULE INTO SHOP DATA DUPLICATION ACTION
    /* ------------------------------------------------------------- */
    public function hookActionShopDataDuplication($params)
    {
        Db::getInstance()->execute('
            INSERT IGNORE INTO '._DB_PREFIX_.'fieldmegamenu_shop (id_fieldmegamenu, id_shop)
            SELECT id_fieldmegamenu, '.(int)$params['new_id_shop'].'
            FROM '._DB_PREFIX_.'fieldmegamenu_shop
            WHERE id_shop = '.(int)$params['old_id_shop']
        );
    }


    /* ------------------------------------------------------------- */
    /*
    /*  FRONT OFFICE RELATED STUFF
    /*
    /* ------------------------------------------------------------- */

    /*
     * MENU TYPES
     *
     * id : description
     * --   -----------
     *  1 : Custom link
     *  2 : Category link
     *  3 : Product link
     *  4 : Manufacturer link
     *  5 : Supplier link
     *  6 : CMS page link
     *  7 : Custom content
     *  8 : Divider
     *
     */

    /* ------------------------------------------------------------- */
    /*  RENDER MEGAMENU
    /* ------------------------------------------------------------- */
    private function renderMenu()
    {
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;

        $roots = FieldMegamenuModel::getMenus($id_shop);

        $menuTypes = array(
            'customlink' => 1,
            'category' => 2,
            'product' => 3,
            'manufacturer' => 4,
            'supplier' => 5,
            'cmspage' => 6,
            'customcontent' => 7,
            'divider' => 8,
        );

        $fieldmegamenu = array();

        // Get Root Items
        foreach ($roots as $root)
        {
            $fieldmegamenu['root'][$root['id_fieldmegamenu']] = new FieldMegamenuModel($root['id_fieldmegamenu'], $id_lang);
        }

        // Get Menu Items
        foreach ($roots as $root)
        {
            $items = FieldMegamenuItemsModel::getMenuItems($root['id_fieldmegamenu']);

            if (!$items){
                continue;
            }

            // Iterate through all items and prepare them
            foreach ($items as $item)
            {
                $fieldMegamenuItem = new FieldMegamenuItemsModel($item['id_fieldmegamenuitems'], $id_lang);

                $menuTitle = "";
                $menuLink = "";

                switch ($fieldMegamenuItem->menu_type)
                {
                    case 1:
                        // Custom Link
                        $menuLink = $fieldMegamenuItem->link;
                        $menuType = array_search($fieldMegamenuItem->menu_type, $menuTypes);
                        break;

                    case 2:
                        // Category Link
                        $category = new Category($fieldMegamenuItem->link, $id_lang);
                        $menuTitle = $category->name;
                        $menuLink = $this->context->link->getCategoryLink($category, null, $id_lang);
                        $menuType = array_search($fieldMegamenuItem->menu_type, $menuTypes);
                        break;

                    case 3:
                       $customProduct = get_object_vars(new Product($fieldMegamenuItem->link, true, $id_lang));
                            $customProduct['id_product'] = $customProduct['id'];

                            $coverImage = Product::getCover($customProduct['id_product']);
                            $customProduct['id_image'] = $coverImage['id_image'];

                            $products= Product::getProductProperties($id_lang, $customProduct);
                        // Product Link
                        $product = new Product($fieldMegamenuItem->link, true, $id_lang);
						$fieldMegamenuItem->product['id_product_attribute']=$products['id_product_attribute'];
//			if ($product->id == 5)
			$fieldMegamenuItem->product['id_product'] = $product->id;
			$fieldMegamenuItem->product['quantity']= $product->quantity;
//			    var_dump($fieldMegamenuItem->product);
                        $menuTitle = $product->name;
                        $menuLink = $products['link'];
                        $menuType = array_search($fieldMegamenuItem->menu_type, $menuTypes);

                        if ($fieldMegamenuItem->show_image){
                            $coverImage = $product->getCover($product->id);
                            $fieldMegamenuItem->image = $this->context->link->getImageLink($product->link_rewrite, $coverImage['id_image'], 'home_default');
			    $fieldMegamenuItem->second_image = $this->context->link->getImageLink($product->link_rewrite, $coverImage['id_image'], 'home_default', $product->id);
			    $reduction=0;

			    if(isset($product->specificPrice['reduction'])){
				$reduction=$product->specificPrice['reduction'];
			    }
			    $priceDisplay = Product::getTaxCalculationMethod();
			    $pscatmode = (bool)Configuration::get('PS_CATALOG_MODE') || !(bool)Group::getCurrent()->show_prices;
			    if ($product->show_price && !(bool)$pscatmode){
				if(!$priceDisplay){
				    $fieldMegamenuItem->price = Tools::displayPrice(Tools::ps_round($product->price*(0.01*$product->tax_rate)+$product->price, 2));
				}
				else{
				    $fieldMegamenuItem->price = Tools::displayPrice(Tools::ps_round($product->price, 2));
				}
				if($reduction){
				    $fieldMegamenuItem->old_price = Tools::displayPrice($product->getPriceWithoutReduct(false));
				}
				if(isset($product->specificPrice['reduction']) && $product->specificPrice['reduction_type'] == "percentage"){
				    $fieldMegamenuItem->reduction_price = $product->specificPrice['reduction']*100;
				}
			    }
                        }
                        break;

                    case 4:
                        // Manufacturer Link
                        $manufacturer = new Manufacturer($fieldMegamenuItem->link, $id_lang);
                        $menuTitle = $manufacturer->name;
                        $menuLink = $this->context->link->getManufacturerLink($manufacturer, null, $id_lang);
                        $menuType = array_search($fieldMegamenuItem->menu_type, $menuTypes);

                        if ($fieldMegamenuItem->show_image){
                            $fieldMegamenuItem->image = $this->context->link->getMediaLink(_THEME_MANU_DIR_ . $manufacturer->id_manufacturer . '-medium_default.jpg');
                        }
                        break;

                    case 5:
                        // Supplier Link
                        $supplier = new Supplier($fieldMegamenuItem->link, $id_lang);
                        $menuTitle = $supplier->name;
                        $menuLink = $this->context->link->getSupplierLink($supplier, null, $id_lang);
                        $menuType = array_search($fieldMegamenuItem->menu_type, $menuTypes);

                        if ($fieldMegamenuItem->show_image){
                            $fieldMegamenuItem->image = $this->context->link->getMediaLink(_THEME_SUP_DIR_ . $supplier->id_supplier . '-medium_default.jpg');
                        }
                        break;

                    case 6:
                        // CMS Page Link
                        $cmsPage = new CMS($fieldMegamenuItem->link, $id_lang);
                        $menuTitle = $cmsPage->meta_title;
                        $menuLink = $this->context->link->getCMSLink($cmsPage, null, null, $id_lang);
                        $menuType = array_search($fieldMegamenuItem->menu_type, $menuTypes);
                        break;

                    case 7:
                        // Custom Content
                        $menuType = array_search($fieldMegamenuItem->menu_type, $menuTypes);
                        break;

                    case 8:
                        // Divider
                        $menuType = array_search($fieldMegamenuItem->menu_type, $menuTypes);
                        break;

                    default:
                        break;
                }

                if ($menuTitle != ''){
                    $fieldMegamenuItem->title = $menuTitle;
                }

                if ($menuLink != ''){
                    $fieldMegamenuItem->link = $menuLink;
                }

                $fieldMegamenuItem->menu_type_name = $menuType;

                $fieldmegamenu['root'][$root['id_fieldmegamenu']]->items[] = $fieldMegamenuItem;
            }

        }

        return $fieldmegamenu;
    }

    /* ------------------------------------------------------------- */
    /*  PREPARE FOR HOOK
    /* ------------------------------------------------------------- */
    private function _prepHook($params)
    {
        $fieldmegamenu = $this->renderMenu();
        if ($fieldmegamenu){
            $this->smarty->assign('fieldmegamenu', $fieldmegamenu['root']);
        }

        if (isset($params['fieldmegamenumobile']) && $params['fieldmegamenumobile'] == true){
            $this->smarty->assign('fieldmegamenumobile', true);
        }
    }

    /* ------------------------------------------------------------- */
    /*  HOOK (displayHeader)
    /* ------------------------------------------------------------- */
    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path . 'views/css/hook/fieldmegamenu.css');
		$this->context->controller->addJS($this->_path.'views/js/hook/jquery.fieldmegamenu.js');
    }

    /* ------------------------------------------------------------- */
    /*  HOOK (displayTop)
    /* ------------------------------------------------------------- */
    public function hookDisplayHeaderMenu($params)
    {
        $this->_prepHook($params);
        return $this->display(__FILE__, 'views/templates/hook/fieldmegamenu.tpl');
    }

}