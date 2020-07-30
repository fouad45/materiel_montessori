<?php
/*
*  2015 Fieldthemes
*
*  @author Fieldthemes <fieldthemes@gmail.com>
*/

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
class FieldProductCates extends Module
{

	public function __construct()
	{
		$this->name = 'fieldproductcates';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'Fieldthemes';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Field product cate');
		$this->description = $this->l('Displays other products on the same categories.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		$success = (parent::install()
			&& $this->registerHook('header')
			&& $this->registerHook('displayFooterProduct')
		);

                $this->_createTab();
                $this->_createConfigs();

		return $success;
	}

	public function uninstall()
	{
                $this->_deleteTab();
                $this->_deleteConfigs();

		return parent::uninstall();
	}
        
        /* ------------------------------------------------------------- */
        /*  CREATE CONFIGS
        /* ------------------------------------------------------------- */
        private function _createConfigs()
        {
            $response = Configuration::updateValue('FIELD_PRODUCTCATES_NBR', 30);
            $response &= Configuration::updateValue('FIELD_PRODUCTCATES_MAXITEM', 5);
            $response &= Configuration::updateValue('FIELD_PRODUCTCATES_MEDIUMITEM', 3);
            $response &= Configuration::updateValue('FIELD_PRODUCTCATES_MINITEM', 1);
            $response &= Configuration::updateValue('FIELD_PRODUCTCATES_AUTOSCROLL', 1);
            $response &= Configuration::updateValue('FIELD_PRODUCTCATES_AUTOSCROLLDELAY', 5000);
            $response &= Configuration::updateValue('FIELD_PRODUCTCATES_PAUSEONHOVER', 0);
            $response &= Configuration::updateValue('FIELD_PRODUCTCATES_PAGINATION', 0);
            $response &= Configuration::updateValue('FIELD_PRODUCTCATES_NAVIGATION', 1);

            return $response;
        }
        
        /* ------------------------------------------------------------- */
        /*  DELETE CONFIGS
        /* ------------------------------------------------------------- */
        private function _deleteConfigs()
        {
            $response = Configuration::deleteByName('FIELD_PRODUCTCATES_MAXITEM');
            $response &= Configuration::deleteByName('FIELD_PRODUCTCATES_MEDIUMITEM');
            $response &= Configuration::deleteByName('FIELD_PRODUCTCATES_MINITEM');
            $response &= Configuration::deleteByName('FIELD_PRODUCTCATES_AUTOSCROLL');
            $response &= Configuration::deleteByName('FIELD_PRODUCTCATES_PAGINATION');
            $response &= Configuration::deleteByName('FIELD_PRODUCTCATES_NAVIGATION');
            $response &= Configuration::deleteByName('FIELD_PRODUCTCATES_AUTOSCROLLDELAY');
            $response &= Configuration::deleteByName('FIELD_PRODUCTCATES_PAUSEONHOVER');
            $response &= Configuration::deleteByName('FIELD_PRODUCTCATES_NBR');

            return $response;
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
                    $parentTab->name[$lang['id_lang']] = "Fieldthemes";
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
            $tab->class_name = "AdminFieldProductCates";
            $tab->name = array();
            foreach (Language::getLanguages() as $lang){
                $tab->name[$lang['id_lang']] = "Configure product cates";
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
            $id_tab = Tab::getIdFromClassName('AdminFieldProductCates');
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

        
	public function getContent()
	{
		$output = '';
                
		if (Tools::isSubmit('submitFieldProductCates'))
		{
                        if (Tools::isSubmit('FIELD_PRODUCTCATES_AUTOSCROLL')){
                            Configuration::updateValue('FIELD_PRODUCTCATES_AUTOSCROLL', Tools::getValue('FIELD_PRODUCTCATES_AUTOSCROLL'));
                        }
                        if (Tools::isSubmit('FIELD_PRODUCTCATES_PAUSEONHOVER')){
                            Configuration::updateValue('FIELD_PRODUCTCATES_PAUSEONHOVER', (int)Tools::getValue('FIELD_PRODUCTCATES_PAUSEONHOVER'));
                        }
                        if (Tools::isSubmit('FIELD_PRODUCTCATES_PAGINATION')){
                            Configuration::updateValue('FIELD_PRODUCTCATES_PAGINATION', (int)Tools::getValue('FIELD_PRODUCTCATES_PAGINATION'));
                        }
                        if (Tools::isSubmit('FIELD_PRODUCTCATES_NAVIGATION')){
                            Configuration::updateValue('FIELD_PRODUCTCATES_NAVIGATION', (int)Tools::getValue('FIELD_PRODUCTCATES_NAVIGATION'));
                        }
                        if (Tools::isSubmit('FIELD_PRODUCTCATES_AUTOSCROLLDELAY') || Tools::isSubmit('FIELD_PRODUCTCATES_MAXITEM') || Tools::isSubmit('FIELD_PRODUCTCATES_MEDIUMITEM') || Tools::isSubmit('FIELD_PRODUCTCATES_MINITEM') || Tools::isSubmit('FIELD_PRODUCTCATES_NBR')){
                            if (Validate::isInt(Tools::getValue('FIELD_PRODUCTCATES_AUTOSCROLLDELAY')) && Validate::isInt(Tools::getValue('FIELD_PRODUCTCATES_MAXITEM')) && Validate::isInt(Tools::getValue('FIELD_PRODUCTCATES_MEDIUMITEM')) && Validate::isInt(Tools::getValue('FIELD_PRODUCTCATES_MINITEM')) && Validate::isInt(Tools::getValue('FIELD_PRODUCTCATES_NBR'))){
                                Configuration::updateValue('FIELD_PRODUCTCATES_AUTOSCROLLDELAY', Tools::getValue('FIELD_PRODUCTCATES_AUTOSCROLLDELAY'));
                                Configuration::updateValue('FIELD_PRODUCTCATES_MAXITEM', Tools::getValue('FIELD_PRODUCTCATES_MAXITEM'));
                                Configuration::updateValue('FIELD_PRODUCTCATES_MEDIUMITEM', Tools::getValue('FIELD_PRODUCTCATES_MEDIUMITEM'));
                                Configuration::updateValue('FIELD_PRODUCTCATES_MINITEM', Tools::getValue('FIELD_PRODUCTCATES_MINITEM'));
                                Configuration::updateValue('FIELD_PRODUCTCATES_NBR', Tools::getValue('FIELD_PRODUCTCATES_NBR'));
                            } else {
                                $errors[] = $this->l('value must be a numeric value!');
                            }
                        }
                        if (isset($errors) && count($errors))
                            $output .= $this->displayError(implode('<br />', $errors));
                        else
                            $output .= $this->displayConfirmation($this->l('Settings updated'));
		}
		return $output.$this->displayForm();
	}

	private function getCurrentProduct($products, $id_current)
	{
		if ($products)
			foreach ($products AS $key => $product)
				if ($product['id_product'] == $id_current)
					return $key;
		return false;
	}
	
	public function hookProductFooter($params)
	{   
				$id_product = (int)$params['product']['id_product'];
				
            	$product = $params['product'];
		
		$cache_id = 'fieldproductcates|'.$id_product.'|'.(isset($params['category']->id_category) ? (int)$params['category']->id_category : $product['id_category_default']);

			/* If the visitor has came to this product by a category, use this one */
			if (isset($params['category']->id_category))
				$category = $params['category'];
			/* Else, use the default product category */
			else
			{
				if (isset($product['id_category_default']) AND $product['id_category_default'] > 1)
					$category = new Category((int)$product['id_category_default']);
			}
			if (!Validate::isLoadedObject($category) OR !$category->active) 
				return;
                              
			// Get infos
			$categoryProducts = $category->getProducts($this->context->language->id, 1, 100); /* 100 products max. */
			
			
			
			
			$sizeOfCategoryProducts = (int)sizeof($categoryProducts);
			$middlePosition = 0;
			if (is_array($categoryProducts) AND sizeof($categoryProducts))
			{
				foreach ($categoryProducts AS $key => $categoryProduct)
					if ($categoryProduct['id_product'] == $id_product)
					{
						unset($categoryProducts[$key]);
						break;
					}

				$taxes = Product::getTaxCalculationMethod();
					foreach ($categoryProducts AS $key => $categoryProduct)
						if ($categoryProduct['id_product'] != $id_product)
						{
							if ($taxes == 0 OR $taxes == 2)
								$categoryProducts[$key]['displayed_price'] = Product::getPriceStatic((int)$categoryProduct['id_product'], true, NULL, 2);
							elseif ($taxes == 1)
								$categoryProducts[$key]['displayed_price'] = Product::getPriceStatic((int)$categoryProduct['id_product'], false, NULL, 2);
						}
			
				// Get positions
				$middlePosition = round($sizeOfCategoryProducts / 2, 0);
				$productPosition = $this->getCurrentProduct($categoryProducts, (int)$id_product);
			
				// Flip middle product with current product
				if ($productPosition)
				{
					$tmp = $categoryProducts[$middlePosition-1];
					$categoryProducts[$middlePosition-1] = $categoryProducts[$productPosition];
					$categoryProducts[$productPosition] = $tmp;
				}
			
				// If products tab higher than 30, slice it
				if (Configuration::get('FIELD_PRODUCTCATES_NBR'))
				    $nbp = Configuration::get('FIELD_PRODUCTCATES_NBR');
				else {
				    $nbp = 30; 
				}
				if ($sizeOfCategoryProducts > $nbp)
				{
					$categoryProducts = array_slice($categoryProducts, $middlePosition - ($nbp/2), $nbp, true);
					$middlePosition = $nbp/2;
				}
			}
                          
                        $slideOptions = array(
                            'FIELD_PRODUCTCATES_MAXITEM' => Configuration::get('FIELD_PRODUCTCATES_MAXITEM'),
                            'FIELD_PRODUCTCATES_MEDIUMITEM' => Configuration::get('FIELD_PRODUCTCATES_MEDIUMITEM'),
                            'FIELD_PRODUCTCATES_MINITEM' => Configuration::get('FIELD_PRODUCTCATES_MINITEM'),
                            'FIELD_PRODUCTCATES_AUTOSCROLL' => Configuration::get('FIELD_PRODUCTCATES_AUTOSCROLL'),
                            'FIELD_PRODUCTCATES_AUTOSCROLLDELAY' => Configuration::get('FIELD_PRODUCTCATES_AUTOSCROLLDELAY'),
                            'FIELD_PRODUCTCATES_PAUSEONHOVER' => Configuration::get('FIELD_PRODUCTCATES_PAUSEONHOVER'),
                            'FIELD_PRODUCTCATES_PAGINATION' => Configuration::get('FIELD_PRODUCTCATES_PAGINATION'),
                            'FIELD_PRODUCTCATES_NAVIGATION' => Configuration::get('FIELD_PRODUCTCATES_NAVIGATION'),
                            'FIELD_PRODUCTCATES_NBR' => Configuration::get('FIELD_PRODUCTCATES_NBR'),
                        );

			// Display tpl
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
		if(is_array($categoryProducts)){
        foreach ($categoryProducts as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        	}
		}
			
			
			$this->smarty->assign(array(
				'categoryProducts' => $products_for_template,
				'middlePosition' => (int)$middlePosition,
                'slideOptions' => $slideOptions,            
			));
		return $this->display(__FILE__, 'fieldproductcates.tpl');
	}

	public function hookHeader($params)
	{
            $this->context->controller->addCSS($this->_path . 'views/css/hook/fieldproductcates.css');
	}
        
	public function displayForm()
	{
                $id_default_lang = $this->context->language->id;
                $languages = $this->context->language->getLanguages();
                
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Products to display'),
						'name' => 'FIELD_PRODUCTCATES_NBR',
						'desc' => $this->l('Total number of product to display in this block'),
						'class' => 'fixed-width-xs',
					),
                                        array(
                                                'type' => 'text',
                                                'name' => 'FIELD_PRODUCTCATES_MAXITEM',
                                                'label' => $this->l('Max item'),
                                                'desc' => $this->l('The item number is showing on desstop screen'),
                                                'suffix' => 'item',
                                                'class' => 'fixed-width-xs',
                                                'required' => false,
                                                'lang' => false,
                                            ),
                                        array(
                                                'type' => 'text',
                                                'name' => 'FIELD_PRODUCTCATES_MEDIUMITEM',
                                                'label' => $this->l('Medium item'),
                                                'desc' => $this->l('The item number is showing on tablet'),
                                                'suffix' => 'item',
                                                'class' => 'fixed-width-xs',
                                                'required' => false,
                                                'lang' => false,
                                            ),
                                            array(
                                                'type' => 'text',
                                                'name' => 'FIELD_PRODUCTCATES_MINITEM',
                                                'label' => $this->l('Min item'),
                                                'desc' => $this->l('The item number is showing on mobile'),
                                                'suffix' => 'item',
                                                'class' => 'fixed-width-xs',
                                                'required' => false,
                                                'lang' => false,
                                            ),
                                            array(
                                                'type' => 'switch',
                                                'label' => $this->l('Auto scroll'),
                                                'desc' => $this->l('Scroll the item automatically'),
                                                'name' => 'FIELD_PRODUCTCATES_AUTOSCROLL',
                                                'required' => false,
                                                'is_bool' => true,
                                                'values' => array(
                                                    array(
                                                        'id' => 'autoscroll_on',
                                                        'value' => 1,
                                                        'label' => $this->l('On')
                                                    ),
                                                    array(
                                                        'id' => 'autoscroll_off',
                                                        'value' => 0,
                                                        'label' => $this->l('Off')
                                                    )
                                                )
                                            ),
                                            array(
                                                'type' => 'text',
                                                'name' => 'FIELD_PRODUCTCATES_AUTOSCROLLDELAY',
                                                'label' => $this->l('Auto scroll delay'),
                                                'desc' => $this->l('Delay between the auto scrolls'),
                                                'suffix' => 'milliseconds',
                                                'required' => false,
                                                'lang' => false,
                                            ),
                                            array(
                                                'type' => 'switch',
                                                'label' => $this->l('Pause on hover'),
                                                'desc' => $this->l('Pause the carousel on mouse hover'),
                                                'name' => 'FIELD_PRODUCTCATES_PAUSEONHOVER',
                                                'required' => false,
                                                'is_bool' => true,
                                                'values' => array(
                                                    array(
                                                        'id' => 'pauseonhover_on',
                                                        'value' => 1,
                                                        'label' => $this->l('On')
                                                    ),
                                                    array(
                                                        'id' => 'pauseonhover_off',
                                                        'value' => 0,
                                                        'label' => $this->l('Off')
                                                    )
                                                )
                                            ),
                                            array(
                                                'type' => 'switch',
                                                'label' => $this->l('Show pagination'),
                                                'name' => 'FIELD_PRODUCTCATES_PAGINATION',
                                                'required' => false,
                                                'is_bool' => true,
                                                'values' => array(
                                                    array(
                                                        'id' => 'pagination_on',
                                                        'value' => 1,
                                                        'label' => $this->l('On')
                                                    ),
                                                    array(
                                                        'id' => 'pagination_off',
                                                        'value' => 0,
                                                        'label' => $this->l('Off')
                                                    )
                                                )
                                            ),
                                            array(
                                                'type' => 'switch',
                                                'label' => $this->l('Show navigation'),
                                                'name' => 'FIELD_PRODUCTCATES_NAVIGATION',
                                                'required' => false,
                                                'is_bool' => true,
                                                'values' => array(
                                                    array(
                                                        'id' => 'navigation_on',
                                                        'value' => 1,
                                                        'label' => $this->l('On')
                                                    ),
                                                    array(
                                                        'id' => 'navigation_off',
                                                        'value' => 0,
                                                        'label' => $this->l('Off')
                                                    )
                                                )
                                            )
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
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
                $helper->submit_action = 'submitFieldProductCates';

                foreach($languages as $language){
                    $helper->languages[] = array(
                        'id_lang' => $language['id_lang'],
                        'iso_code' => $language['iso_code'],
                        'name' => $language['name'],
                        'is_default' => ($id_default_lang == $language['id_lang'] ? 1 : 0)
                    );
                }

                // Load current values
                $helper->fields_value['FIELD_PRODUCTCATES_MAXITEM'] = Configuration::get('FIELD_PRODUCTCATES_MAXITEM');
                $helper->fields_value['FIELD_PRODUCTCATES_MEDIUMITEM'] = Configuration::get('FIELD_PRODUCTCATES_MEDIUMITEM');
                $helper->fields_value['FIELD_PRODUCTCATES_MINITEM'] = Configuration::get('FIELD_PRODUCTCATES_MINITEM');
                $helper->fields_value['FIELD_PRODUCTCATES_AUTOSCROLL'] = Configuration::get('FIELD_PRODUCTCATES_AUTOSCROLL');
                $helper->fields_value['FIELD_PRODUCTCATES_AUTOSCROLLDELAY'] = Configuration::get('FIELD_PRODUCTCATES_AUTOSCROLLDELAY');
                $helper->fields_value['FIELD_PRODUCTCATES_PAUSEONHOVER'] = Configuration::get('FIELD_PRODUCTCATES_PAUSEONHOVER');
                $helper->fields_value['FIELD_PRODUCTCATES_PAGINATION'] = Configuration::get('FIELD_PRODUCTCATES_PAGINATION');
                $helper->fields_value['FIELD_PRODUCTCATES_NAVIGATION'] = Configuration::get('FIELD_PRODUCTCATES_NAVIGATION');
                $helper->fields_value['FIELD_PRODUCTCATES_NBR'] = Configuration::get('FIELD_PRODUCTCATES_NBR');

		return $helper->generateForm(array($fields_form));
	}
}
