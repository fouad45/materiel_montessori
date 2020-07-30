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
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use Symfony\Component\HttpKernel\HttpKernelInterface;
class FieldSpecialProduct extends Module
{
	public function __construct()
	{
		$this->name = 'fieldspecialproduct';
		$this->tab = 'pricing_promotion';
		$this->version = '1.0';
		$this->author = 'Fieldthemes';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Field specials products');
		$this->description = $this->l('Adds a block displaying your current discounted products.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		$this->_clearCache('*');
			$this->_createConfigs();
			$this->_createTab();
		$success = parent::install()
			&& $this->registerHook('displayHeader')
			&& $this->registerHook('specialproduct');
		return $success;
	}

	public function uninstall()
	{
			$this->_deleteConfigs();
			$this->_deleteTab();
		return parent::uninstall();
	}
        
        /* ------------------------------------------------------------- */
        /*  CREATE CONFIGS
        /* ------------------------------------------------------------- */
        private function _createConfigs()
        {
            $languages = $this->context->language->getLanguages();
			
            foreach ($languages as $language){
                $title[$language['id_lang']] = 'Specials products';
            }
            $response = Configuration::updateValue('FIELD_SPECIALPLS_NBR', 6);
            $response &= Configuration::updateValue('FIELD_SPECIALPLS_TITLE', $title);
            $response &= Configuration::updateValue('FIELD_SPECIALPLS_VERTICAL', 0);
            $response &= Configuration::updateValue('FIELD_SPECIALPLS_COLUMNITEM', 1);
            $response &= Configuration::updateValue('FIELD_SPECIALPLS_MAXITEM', 1);
            $response &= Configuration::updateValue('FIELD_SPECIALPLS_MEDIUMITEM', 1);
            $response &= Configuration::updateValue('FIELD_SPECIALPLS_MINITEM', 1);
            $response &= Configuration::updateValue('FIELD_SPECIALPLS_AUTOSCROLL', 0);
            $response &= Configuration::updateValue('FIELD_SPECIALPLS_AUTOSCROLLDELAY', 4000);
            $response &= Configuration::updateValue('FIELD_SPECIALPLS_PAUSEONHOVER', 0);
            $response &= Configuration::updateValue('FIELD_SPECIALPLS_PAGINATION', 0);
            $response &= Configuration::updateValue('FIELD_SPECIALPLS_NAVIGATION', 1);

            return $response;
        }
        
        /* ------------------------------------------------------------- */
        /*  DELETE CONFIGS
        /* ------------------------------------------------------------- */
        private function _deleteConfigs()
        {
            $response = Configuration::deleteByName('FIELD_SPECIALPLS_TITLE');
            $response &= Configuration::deleteByName('FIELD_SPECIALPLS_VERTICAL');
            $response &= Configuration::deleteByName('FIELD_SPECIALPLS_COLUMNITEM');
            $response &= Configuration::deleteByName('FIELD_SPECIALPLS_MAXITEM');
            $response &= Configuration::deleteByName('FIELD_SPECIALPLS_MEDIUMITEM');
            $response &= Configuration::deleteByName('FIELD_SPECIALPLS_MINITEM');
            $response &= Configuration::deleteByName('FIELD_SPECIALPLS_AUTOSCROLL');
            $response &= Configuration::deleteByName('FIELD_SPECIALPLS_PAGINATION');
            $response &= Configuration::deleteByName('FIELD_SPECIALPLS_NAVIGATION');
            $response &= Configuration::deleteByName('FIELD_SPECIALPLS_AUTOSCROLLDELAY');
            $response &= Configuration::deleteByName('FIELD_SPECIALPLS_PAUSEONHOVER');
            $response &= Configuration::deleteByName('FIELD_SPECIALPLS_NBR');

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
            $tab->class_name = "AdminFieldSpecialProduct";
            $tab->name = array();
            foreach (Language::getLanguages() as $lang){
                $tab->name[$lang['id_lang']] = "Configure specials products";
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
            $id_tab = Tab::getIdFromClassName('AdminFieldSpecialProduct');
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
                $languages = $this->context->language->getLanguages();
		$output = '';
                
		if (Tools::isSubmit('submitFieldSpecials'))
		{
			$title = array();

                        foreach ($languages as $language){
                            if (Tools::isSubmit('FIELD_SPECIALPLS_TITLE_'.$language['id_lang'])){
                                $title[$language['id_lang']] = Tools::getValue('FIELD_SPECIALPLS_TITLE_'.$language['id_lang']);
                            }
                        }
                        if (isset($title) && $title){
                            Configuration::updateValue('FIELD_SPECIALPLS_TITLE', $title);
                        }
                        if (Tools::isSubmit('FIELD_SPECIALPLS_VERTICAL')){
                            Configuration::updateValue('FIELD_SPECIALPLS_VERTICAL', Tools::getValue('FIELD_SPECIALPLS_VERTICAL'));
                        }
                        if (Tools::isSubmit('PS_BLOCK_SPECIALS_DISPLAY')){
                            Configuration::updateValue('PS_BLOCK_SPECIALS_DISPLAY', (int)Tools::getValue('PS_BLOCK_SPECIALS_DISPLAY'));
                        }
                        if (Tools::isSubmit('FIELD_SPECIALPLS_AUTOSCROLL')){
                            Configuration::updateValue('FIELD_SPECIALPLS_AUTOSCROLL', (int)Tools::getValue('FIELD_SPECIALPLS_AUTOSCROLL'));
                        }
                        if (Tools::isSubmit('FIELD_SPECIALPLS_PAUSEONHOVER')){
                            Configuration::updateValue('FIELD_SPECIALPLS_PAUSEONHOVER', (int)Tools::getValue('FIELD_SPECIALPLS_PAUSEONHOVER'));
                        }
                        if (Tools::isSubmit('FIELD_SPECIALPLS_PAGINATION')){
                            Configuration::updateValue('FIELD_SPECIALPLS_PAGINATION', (int)Tools::getValue('FIELD_SPECIALPLS_PAGINATION'));
                        }
                        if (Tools::isSubmit('FIELD_SPECIALPLS_NAVIGATION')){
                            Configuration::updateValue('FIELD_SPECIALPLS_NAVIGATION', (int)Tools::getValue('FIELD_SPECIALPLS_NAVIGATION'));
                        }
                        if (Tools::isSubmit('FIELD_SPECIALPLS_AUTOSCROLLDELAY') || Tools::isSubmit('FIELD_SPECIALPLS_MAXITEM') || Tools::isSubmit('FIELD_SPECIALPLS_MEDIUMITEM') || Tools::isSubmit('FIELD_SPECIALPLS_MINITEM') || Tools::isSubmit('FIELD_SPECIALPLS_NBR') || Tools::isSubmit('FIELD_SPECIALPLS_COLUMNITEM')){
                            if ( Validate::isInt(Tools::getValue('FIELD_SPECIALPLS_AUTOSCROLLDELAY')) && Validate::isInt(Tools::getValue('FIELD_SPECIALPLS_MAXITEM')) && Validate::isInt(Tools::getValue('FIELD_SPECIALPLS_MEDIUMITEM')) && Validate::isInt(Tools::getValue('FIELD_SPECIALPLS_MINITEM')) && Validate::isInt(Tools::getValue('FIELD_SPECIALPLS_NBR')) && Validate::isInt(Tools::getValue('FIELD_SPECIALPLS_COLUMNITEM'))){
                                Configuration::updateValue('FIELD_SPECIALPLS_COLUMNITEM', Tools::getValue('FIELD_SPECIALPLS_COLUMNITEM'));
                                Configuration::updateValue('FIELD_SPECIALPLS_AUTOSCROLLDELAY', Tools::getValue('FIELD_SPECIALPLS_AUTOSCROLLDELAY'));
                                Configuration::updateValue('FIELD_SPECIALPLS_MAXITEM', Tools::getValue('FIELD_SPECIALPLS_MAXITEM'));
                                Configuration::updateValue('FIELD_SPECIALPLS_MEDIUMITEM', Tools::getValue('FIELD_SPECIALPLS_MEDIUMITEM'));
                                Configuration::updateValue('FIELD_SPECIALPLS_MINITEM', Tools::getValue('FIELD_SPECIALPLS_MINITEM'));
                                Configuration::updateValue('FIELD_SPECIALPLS_NBR', Tools::getValue('FIELD_SPECIALPLS_NBR'));
                            } else {
                                $errors[] = $this->l('value must be a numeric value!');
                            }
                        }
                        if (isset($errors) && count($errors))
                            $output .= $this->displayError(implode('<br />', $errors));
                        else
                            $output .= $this->displayConfirmation($this->l('Settings updated'));
		}
		return $output.$this->renderForm();
	}

        
        public function getProducts($params)
	{
		
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
                
                $this->_prepHook($params);
                
                $specials_product = Product::getPricesDrop((int)$params['cookie']->id_lang, 0, Configuration::get('FIELD_SPECIALPLS_NBR'));
                    
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
		if(is_array($specials_product)){
        foreach ($specials_product as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }
		}	
		return $products_for_template;
					
	}
	/* ------------------------------------------------------------- */
	/*  hookSpecialproduct
	/* ------------------------------------------------------------- */
	public function hookSpecialproduct($params){
		$this->smarty->assign(array(
			'specials' =>$this->getProducts($params)
		));
		if(Configuration::get('FIELD_SPECIALPLS_VERTICAL'))
			return $this->display(__FILE__, 'fieldspecialproduct_vertical.tpl');
		else
			return $this->display(__FILE__, 'fieldspecialproduct.tpl');
	}
	/* ------------------------------------------------------------- */
	/*  hookDisplayHome
	/* ------------------------------------------------------------- */
	public function hookDisplayHome($params){
		return $this->hookSpecialproduct($params);
	}
	/* ------------------------------------------------------------- */
	/*  hookRightColumn
	/* ------------------------------------------------------------- */
	public function hookRightColumn($params){
		return $this->hookSpecialproduct($params);
	}
	/* ------------------------------------------------------------- */
	/*  hookLeftColumn
	/* ------------------------------------------------------------- */
	public function hookLeftColumn($params){
		return $this->hookSpecialproduct($params);
	}
	/* ------------------------------------------------------------- */
	/*  hookDisplayHeader
	/* ------------------------------------------------------------- */
	public function hookDisplayHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
	}

	public function renderForm()
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
                                                'name' => 'FIELD_SPECIALPLS_TITLE',
                                                'label' => $this->l('Title'),
                                                'desc' => $this->l('This title will appear just before the bestseller block, leave it empty to hide it completely'),
                                                'required' => false,
                                                'lang' => true,
                                        ),
					array(
						'type' => 'switch',
						'label' => $this->l('Always display this block'),
						'name' => 'PS_BLOCK_SPECIALS_DISPLAY',
						'desc' => $this->l('Show the block even if no products are available.'),
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->l('Enabled')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->l('Disabled')
									)
								),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Total products to display'),
						'name' => 'FIELD_SPECIALPLS_NBR',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Define the number of products to be displayed in this block on home page.')
					),
                                        array(
                                            'type' => 'switch',
                                            'label' => $this->l('Enable vertical mode'),
                                            'desc' => $this->l('Vertical slider'),
                                            'name' => 'FIELD_SPECIALPLS_VERTICAL',
                                            'required' => false,
                                            'is_bool' => true,
                                            'values' => array(
                                                array(
                                                    'id' => 'vertical_on',
                                                    'value' => 1,
                                                    'label' => $this->l('On')
                                                ),
                                                array(
                                                    'id' => 'vertical_off',
                                                    'value' => 0,
                                                    'label' => $this->l('Off')
                                                )
                                            )
                                        ),
                                        array(
                                                'type' => 'text',
                                                'name' => 'FIELD_SPECIALPLS_COLUMNITEM',
                                                'label' => $this->l('Item to be displayed'),
                                                'desc' => $this->l('The item number is showing in the column on vertical mode'),
                                                'suffix' => 'item',
                                                'class' => 'fixed-width-xs',
                                                'required' => false,
                                                'lang' => false,
                                        ),
                                        array(
                                                'type' => 'text',
                                                'name' => 'FIELD_SPECIALPLS_MAXITEM',
                                                'label' => $this->l('Max item'),
                                                'desc' => $this->l('The item number is showing on desstop screen'),
                                                'suffix' => 'item',
                                                'class' => 'fixed-width-xs',
                                                'required' => false,
                                                'lang' => false,
                                            ),
                                        array(
                                                'type' => 'text',
                                                'name' => 'FIELD_SPECIALPLS_MEDIUMITEM',
                                                'label' => $this->l('Medium item'),
                                                'desc' => $this->l('The item number is showing on tablet'),
                                                'suffix' => 'item',
                                                'class' => 'fixed-width-xs',
                                                'required' => false,
                                                'lang' => false,
                                            ),
                                            array(
                                                'type' => 'text',
                                                'name' => 'FIELD_SPECIALPLS_MINITEM',
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
                                                'name' => 'FIELD_SPECIALPLS_AUTOSCROLL',
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
                                                'name' => 'FIELD_SPECIALPLS_AUTOSCROLLDELAY',
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
                                                'name' => 'FIELD_SPECIALPLS_PAUSEONHOVER',
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
                                                'name' => 'FIELD_SPECIALPLS_PAGINATION',
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
                                                'name' => 'FIELD_SPECIALPLS_NAVIGATION',
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
                $helper->submit_action = 'submitFieldSpecials';

                foreach($languages as $language){
                    $helper->languages[] = array(
                        'id_lang' => $language['id_lang'],
                        'iso_code' => $language['iso_code'],
                        'name' => $language['name'],
                        'is_default' => ($id_default_lang == $language['id_lang'] ? 1 : 0)
                    );
                }

                // Load current values
                $helper->fields_value['FIELD_SPECIALPLS_VERTICAL'] = Configuration::get('FIELD_SPECIALPLS_VERTICAL');
                $helper->fields_value['FIELD_SPECIALPLS_COLUMNITEM'] = Configuration::get('FIELD_SPECIALPLS_COLUMNITEM');
                $helper->fields_value['FIELD_SPECIALPLS_MAXITEM'] = Configuration::get('FIELD_SPECIALPLS_MAXITEM');
                $helper->fields_value['FIELD_SPECIALPLS_MEDIUMITEM'] = Configuration::get('FIELD_SPECIALPLS_MEDIUMITEM');
                $helper->fields_value['FIELD_SPECIALPLS_MINITEM'] = Configuration::get('FIELD_SPECIALPLS_MINITEM');
                $helper->fields_value['FIELD_SPECIALPLS_AUTOSCROLL'] = Configuration::get('FIELD_SPECIALPLS_AUTOSCROLL');
                $helper->fields_value['FIELD_SPECIALPLS_AUTOSCROLLDELAY'] = Configuration::get('FIELD_SPECIALPLS_AUTOSCROLLDELAY');
                $helper->fields_value['FIELD_SPECIALPLS_PAUSEONHOVER'] = Configuration::get('FIELD_SPECIALPLS_PAUSEONHOVER');
                $helper->fields_value['FIELD_SPECIALPLS_PAGINATION'] = Configuration::get('FIELD_SPECIALPLS_PAGINATION');
                $helper->fields_value['FIELD_SPECIALPLS_NAVIGATION'] = Configuration::get('FIELD_SPECIALPLS_NAVIGATION');
                $helper->fields_value['FIELD_SPECIALPLS_NBR'] = Configuration::get('FIELD_SPECIALPLS_NBR');
                $helper->fields_value['PS_BLOCK_SPECIALS_DISPLAY'] = Configuration::get('PS_BLOCK_SPECIALS_DISPLAY');

                foreach($languages as $language){
                    $helper->fields_value['FIELD_SPECIALPLS_TITLE'][$language['id_lang']] = Configuration::get('FIELD_SPECIALPLS_TITLE', $language['id_lang']);
                }

		return $helper->generateForm(array($fields_form));
	}

	/* ------------------------------------------------------------- */
        /*  PREPARE FOR HOOK
        /* ------------------------------------------------------------- */

        private function _prepHook($params)
        {
			$this->context->controller->addJS($this->_path.'views/js/hook/jquery.specialpslowlcarousel.js');
            $id_default_lang = $this->context->language->id;

            $fieldspecialpsl = array(
                'FIELD_SPECIALPLS_TITLE' => Configuration::get('FIELD_SPECIALPLS_TITLE', $id_default_lang),
                'FIELD_SPECIALPLS_COLUMNITEM' => Configuration::get('FIELD_SPECIALPLS_COLUMNITEM'),
                'FIELD_SPECIALPLS_MAXITEM' => Configuration::get('FIELD_SPECIALPLS_MAXITEM'),
                'FIELD_SPECIALPLS_MEDIUMITEM' => Configuration::get('FIELD_SPECIALPLS_MEDIUMITEM'),
                'FIELD_SPECIALPLS_MINITEM' => Configuration::get('FIELD_SPECIALPLS_MINITEM'),
                'FIELD_SPECIALPLS_AUTOSCROLL' => Configuration::get('FIELD_SPECIALPLS_AUTOSCROLL'),
                'FIELD_SPECIALPLS_AUTOSCROLLDELAY' => Configuration::get('FIELD_SPECIALPLS_AUTOSCROLLDELAY'),
                'FIELD_SPECIALPLS_PAUSEONHOVER' => Configuration::get('FIELD_SPECIALPLS_PAUSEONHOVER'),
                'FIELD_SPECIALPLS_PAGINATION' => Configuration::get('FIELD_SPECIALPLS_PAGINATION'),
                'FIELD_SPECIALPLS_NAVIGATION' => Configuration::get('FIELD_SPECIALPLS_NAVIGATION'),
            );

            $this->smarty->assign('fieldspecialpsl', $fieldspecialpsl);
        }
}
