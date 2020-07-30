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
class FieldOneCateProductSlider extends Module
{

	public function __construct()
	{
		$this->name = 'fieldonecateproductslider';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'Fieldthemes';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Field one categories');
		$this->description = $this->l('Displays product on one categories in the your homepage.');
	}

	public function install()
	{
                $this->_createTab();
                $this->_createConfigs();

		if (!parent::install()
			|| !$this->registerHook('displayHeader')
			|| !$this->registerHook('onecateproductslider')
		)
			return false;

		return true;
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
            $languages = $this->context->language->getLanguages();

            foreach ($languages as $language){
                $title[$language['id_lang']] = 'today deals';
            }
            $response = Configuration::updateValue('FIELD_ONECATEPSL_NBR', 2);
            $response &= Configuration::updateValue('FIELD_ONECATEPSL_CAT', (int)Context::getContext()->shop->getCategory());
            $response &= Configuration::updateValue('FIELD_ONECATEPSL_TITLE', $title);
            $response &= Configuration::updateValue('FIELD_ONECATEPSL_VERTICAL', 0);
            $response &= Configuration::updateValue('FIELD_ONECATEPSL_COLUMNITEM', 1);
            $response &= Configuration::updateValue('FIELD_ONECATEPSL_MAXITEM', 1);
            $response &= Configuration::updateValue('FIELD_ONECATEPSL_MEDIUMITEM', 1);
            $response &= Configuration::updateValue('FIELD_ONECATEPSL_MINITEM', 1);
            $response &= Configuration::updateValue('FIELD_ONECATEPSL_AUTOSCROLL', 0);
            $response &= Configuration::updateValue('FIELD_ONECATEPSL_AUTOSCROLLDELAY', 5000);
            $response &= Configuration::updateValue('FIELD_ONECATEPSL_PAUSEONHOVER', 0);
            $response &= Configuration::updateValue('FIELD_ONECATEPSL_PAGINATION', 0);
            $response &= Configuration::updateValue('FIELD_ONECATEPSL_NAVIGATION', 1);

            return $response;
        }
        
        /* ------------------------------------------------------------- */
        /*  DELETE CONFIGS
        /* ------------------------------------------------------------- */
        private function _deleteConfigs()
        {
            $response = Configuration::deleteByName('FIELD_ONECATEPSL_TITLE');
            $response &= Configuration::deleteByName('FIELD_ONECATEPSL_VERTICAL');
            $response &= Configuration::deleteByName('FIELD_ONECATEPSL_COLUMNITEM');
            $response &= Configuration::deleteByName('FIELD_ONECATEPSL_MAXITEM');
            $response &= Configuration::deleteByName('FIELD_ONECATEPSL_MEDIUMITEM');
            $response &= Configuration::deleteByName('FIELD_ONECATEPSL_MINITEM');
            $response &= Configuration::deleteByName('FIELD_ONECATEPSL_AUTOSCROLL');
            $response &= Configuration::deleteByName('FIELD_ONECATEPSL_PAGINATION');
            $response &= Configuration::deleteByName('FIELD_ONECATEPSL_NAVIGATION');
            $response &= Configuration::deleteByName('FIELD_ONECATEPSL_AUTOSCROLLDELAY');
            $response &= Configuration::deleteByName('FIELD_ONECATEPSL_PAUSEONHOVER');
            $response &= Configuration::deleteByName('FIELD_ONECATEPSL_NBR');
            $response &= Configuration::deleteByName('FIELD_ONECATEPSL_CAT');

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
            $tab->class_name = "AdminFieldOneCateProductSlider";
            $tab->name = array();
            foreach (Language::getLanguages() as $lang){
                $tab->name[$lang['id_lang']] = "Configure one categories";
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
            $id_tab = Tab::getIdFromClassName('AdminFieldOneCateProductSlider');
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
		$errors = array();
		if (Tools::isSubmit('submitFieldOneCateProductSlider'))
		{
                        $title = array();

                        foreach ($languages as $language){
                            if (Tools::isSubmit('FIELD_ONECATEPSL_TITLE_'.$language['id_lang'])){
                                $title[$language['id_lang']] = Tools::getValue('FIELD_ONECATEPSL_TITLE_'.$language['id_lang']);
                            }
                        }
                        if (isset($title) && $title){
                            Configuration::updateValue('FIELD_ONECATEPSL_TITLE', $title);
                        }
                        if (Tools::isSubmit('FIELD_ONECATEPSL_VERTICAL')){
                            Configuration::updateValue('FIELD_ONECATEPSL_VERTICAL', Tools::getValue('FIELD_ONECATEPSL_VERTICAL'));
                        }
                        if (Tools::isSubmit('FIELD_ONECATEPSL_AUTOSCROLL')){
                            Configuration::updateValue('FIELD_ONECATEPSL_AUTOSCROLL', Tools::getValue('FIELD_ONECATEPSL_AUTOSCROLL'));
                        }
                        if (Tools::isSubmit('FIELD_ONECATEPSL_PAUSEONHOVER')){
                            Configuration::updateValue('FIELD_ONECATEPSL_PAUSEONHOVER', (int)Tools::getValue('FIELD_ONECATEPSL_PAUSEONHOVER'));
                        }
                        if (Tools::isSubmit('FIELD_ONECATEPSL_PAGINATION')){
                            Configuration::updateValue('FIELD_ONECATEPSL_PAGINATION', (int)Tools::getValue('FIELD_ONECATEPSL_PAGINATION'));
                        }
                        if (Tools::isSubmit('FIELD_ONECATEPSL_NAVIGATION')){
                            Configuration::updateValue('FIELD_ONECATEPSL_NAVIGATION', (int)Tools::getValue('FIELD_ONECATEPSL_NAVIGATION'));
                        }
                        
			if (Tools::isSubmit('FIELD_ONECATEPSL_CAT') || Tools::isSubmit('FIELD_ONECATEPSL_AUTOSCROLLDELAY') || Tools::isSubmit('FIELD_ONECATEPSL_COLUMNITEM') || Tools::isSubmit('FIELD_ONECATEPSL_MAXITEM') || Tools::isSubmit('FIELD_ONECATEPSL_MEDIUMITEM') || Tools::isSubmit('FIELD_ONECATEPSL_MINITEM') || Tools::isSubmit('FIELD_ONECATEPSL_NBR')){
                            if (Validate::isInt(Tools::getValue('FIELD_ONECATEPSL_CAT')) && Validate::isInt(Tools::getValue('FIELD_ONECATEPSL_AUTOSCROLLDELAY')) && Validate::isInt(Tools::getValue('FIELD_ONECATEPSL_COLUMNITEM')) && Validate::isInt(Tools::getValue('FIELD_ONECATEPSL_MAXITEM')) && Validate::isInt(Tools::getValue('FIELD_ONECATEPSL_MEDIUMITEM')) && Validate::isInt(Tools::getValue('FIELD_ONECATEPSL_MINITEM')) && Validate::isInt(Tools::getValue('FIELD_ONECATEPSL_NBR'))){
                                Configuration::updateValue('FIELD_ONECATEPSL_AUTOSCROLLDELAY', Tools::getValue('FIELD_ONECATEPSL_AUTOSCROLLDELAY'));
                                Configuration::updateValue('FIELD_ONECATEPSL_COLUMNITEM', Tools::getValue('FIELD_ONECATEPSL_COLUMNITEM'));
                                Configuration::updateValue('FIELD_ONECATEPSL_MAXITEM', Tools::getValue('FIELD_ONECATEPSL_MAXITEM'));
                                Configuration::updateValue('FIELD_ONECATEPSL_MEDIUMITEM', Tools::getValue('FIELD_ONECATEPSL_MEDIUMITEM'));
                                Configuration::updateValue('FIELD_ONECATEPSL_MINITEM', Tools::getValue('FIELD_ONECATEPSL_MINITEM'));
                                Configuration::updateValue('FIELD_ONECATEPSL_NBR', Tools::getValue('FIELD_ONECATEPSL_NBR'));
                                Configuration::updateValue('FIELD_ONECATEPSL_CAT', Tools::getValue('FIELD_ONECATEPSL_CAT'));
                            } else {
                                $errors[] = $this->l('value must be a numeric value!');
                            }
                        }
			if (isset($errors) && count($errors))
				$output = $this->displayError(implode('<br />', $errors));
			else
				$output = $this->displayConfirmation($this->l('Your settings have been updated.'));
		}

		return $output.$this->renderForm();
	}
	/* ------------------------------------------------------------- */
	/*  getProducts
	/* ------------------------------------------------------------- */
 	protected function getProducts()
    {
		$category = new Category((int)Configuration::get('FIELD_ONECATEPSL_CAT'), (int)Context::getContext()->language->id);
		$nb = (int)Configuration::get('FIELD_ONECATEPSL_NBR');
		$oneCate_Products = $category->getProducts((int)Context::getContext()->language->id, 1, ($nb ? $nb : 8), 'position');
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
		if(is_array($oneCate_Products)){
        foreach ($oneCate_Products as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }
		 return $products_for_template;
		}
        return $oneCate_Products;
    }  	
	/* ------------------------------------------------------------- */
	/*  hookOnecateproductslider
	/* ------------------------------------------------------------- */
	public function hookOnecateproductslider($params)
	{   
				$this->_prepHook($params);
                $oneCate_Products=$this->getProducts();
                $this->smarty->assign(
                        array(
                                'products' => $oneCate_Products,
                                'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY')
                        )
                );
                if(Configuration::get('FIELD_ONECATEPSL_VERTICAL'))
                    return $this->display(__FILE__, 'fieldonecateproductslider_vertical.tpl');
                else
                    return $this->display(__FILE__, 'fieldonecateproductslider.tpl');
	}
	/* ------------------------------------------------------------- */
	/*  hookDisplayHeader
	/* ------------------------------------------------------------- */
	public function hookDisplayHeader($params)
	{

	}
	/* ------------------------------------------------------------- */
	/*  hookDisplayHome
	/* ------------------------------------------------------------- */
	public function hookDisplayHome($params){
		return $this->hookOnecateproductslider($params);
	}
	/* ------------------------------------------------------------- */
	/*  hookRightColumn
	/* ------------------------------------------------------------- */
	public function hookRightColumn($params){
		return $this->hookOnecateproductslider($params);
	}
	/* ------------------------------------------------------------- */
	/*  hookLeftColumn
	/* ------------------------------------------------------------- */
	public function hookLeftColumn($params){
		return $this->hookOnecateproductslider($params);
	}

        /* ------------------------------------------------------------- */
        /*  GET CATEGORIES WITH NICE FORMATTING
        /* ------------------------------------------------------------- */
        private function _getCategories($id_category = 1, $id_shop = false, $recursive = true)
        {
            $id_lang = $this->context->language->id;

            $category = new Category((int) $id_category, (int) $id_lang, (int) $id_shop);

            if (is_null($category->id))
                return;

            if ($recursive){
                $children = Category::getChildren((int) $id_category, (int) $id_lang, true, (int) $id_shop);
                if ($category->level_depth == 0) {
                    $depth = $category->level_depth;
                } else {
                    $depth = $category->level_depth - 1;
                }

                $spacer = str_repeat('&mdash;', 1 * $depth);
            }

            $this->_categorySelect[] = array(
                'value' =>  (int) $category->id,
                'name' => (isset($spacer) ? $spacer : '') . $category->name
            );

            if (isset($children) && count($children)){
                foreach ($children as $child){
                    $this->_getCategories((int) $child['id_category'], (int) $child['id_shop'], true);
                }
            }
        }
        
	public function renderForm()
	{
                $id_default_lang = $this->context->language->id;
                $languages = $this->context->language->getLanguages();
                $id_shop = $this->context->shop->id;
                $root_category = Category::getRootCategory($id_default_lang);
                $this->_getCategories($root_category->id_category, $id_shop);
        
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'description' => $this->l('To add products to your homepage, simply add them to the corresponding product category (default: "Home").'),
				'input' => array(
                                        array(
                                                'type' => 'text',
                                                'name' => 'FIELD_ONECATEPSL_TITLE',
                                                'label' => $this->l('Title'),
                                                'desc' => $this->l('This title will appear just before the bestseller block, leave it empty to hide it completely'),
                                                'required' => false,
                                                'lang' => true,
                                            ),
					array(
						'type' => 'text',
						'label' => $this->l('Number of products to be displayed'),
						'name' => 'FIELD_ONECATEPSL_NBR',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Set the number of products that you would like to display on homepage (default: 8).'),
					),
                                        array(
                                            'type' => 'switch',
                                            'label' => $this->l('Enable vertical mode'),
                                            'desc' => $this->l('Vertical slider'),
                                            'name' => 'FIELD_ONECATEPSL_VERTICAL',
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
                                                'name' => 'FIELD_ONECATEPSL_COLUMNITEM',
                                                'label' => $this->l('Item to be displayed'),
                                                'desc' => $this->l('The item number is showing in the column on vertical mode'),
                                                'suffix' => 'item',
                                                'class' => 'fixed-width-xs',
                                                'required' => false,
                                                'lang' => false,
                                        ),
                                        array(
                                            'type' => 'select',
                                            'name' => 'FIELD_ONECATEPSL_CAT',
                                            'label' => $this->l('Select a category'),
                                            'required' => false,
                                            'lang' => false,
                                            'options' => array(
                                                'query' => $this->_categorySelect,
                                                'id' => 'value',
                                                'name' => 'name'
                                            )
                                        ),
                                        array(
                                                'type' => 'text',
                                                'name' => 'FIELD_ONECATEPSL_MAXITEM',
                                                'label' => $this->l('Max item'),
                                                'desc' => $this->l('The item number is showing on desstop screen'),
                                                'suffix' => 'item',
                                                'class' => 'fixed-width-xs',
                                                'required' => false,
                                                'lang' => false,
                                        ),
                                        array(
                                                'type' => 'text',
                                                'name' => 'FIELD_ONECATEPSL_MEDIUMITEM',
                                                'label' => $this->l('Medium item'),
                                                'desc' => $this->l('The item number is showing on tablet'),
                                                'suffix' => 'item',
                                                'class' => 'fixed-width-xs',
                                                'required' => false,
                                                'lang' => false,
                                        ),
                                        array(
                                            'type' => 'text',
                                            'name' => 'FIELD_ONECATEPSL_MINITEM',
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
                                            'name' => 'FIELD_ONECATEPSL_AUTOSCROLL',
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
                                            'name' => 'FIELD_ONECATEPSL_AUTOSCROLLDELAY',
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
                                            'name' => 'FIELD_ONECATEPSL_PAUSEONHOVER',
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
                                            'name' => 'FIELD_ONECATEPSL_PAGINATION',
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
                                            'name' => 'FIELD_ONECATEPSL_NAVIGATION',
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
                $helper->submit_action = 'submitFieldOneCateProductSlider';

                foreach($languages as $language){
                    $helper->languages[] = array(
                        'id_lang' => $language['id_lang'],
                        'iso_code' => $language['iso_code'],
                        'name' => $language['name'],
                        'is_default' => ($id_default_lang == $language['id_lang'] ? 1 : 0)
                    );
                }

                // Load current values
                $helper->fields_value['FIELD_ONECATEPSL_VERTICAL'] = Configuration::get('FIELD_ONECATEPSL_VERTICAL');
                $helper->fields_value['FIELD_ONECATEPSL_COLUMNITEM'] = Configuration::get('FIELD_ONECATEPSL_COLUMNITEM');
                $helper->fields_value['FIELD_ONECATEPSL_MAXITEM'] = Configuration::get('FIELD_ONECATEPSL_MAXITEM');
                $helper->fields_value['FIELD_ONECATEPSL_MEDIUMITEM'] = Configuration::get('FIELD_ONECATEPSL_MEDIUMITEM');
                $helper->fields_value['FIELD_ONECATEPSL_MINITEM'] = Configuration::get('FIELD_ONECATEPSL_MINITEM');
                $helper->fields_value['FIELD_ONECATEPSL_AUTOSCROLL'] = Configuration::get('FIELD_ONECATEPSL_AUTOSCROLL');
                $helper->fields_value['FIELD_ONECATEPSL_AUTOSCROLLDELAY'] = Configuration::get('FIELD_ONECATEPSL_AUTOSCROLLDELAY');
                $helper->fields_value['FIELD_ONECATEPSL_PAUSEONHOVER'] = Configuration::get('FIELD_ONECATEPSL_PAUSEONHOVER');
                $helper->fields_value['FIELD_ONECATEPSL_PAGINATION'] = Configuration::get('FIELD_ONECATEPSL_PAGINATION');
                $helper->fields_value['FIELD_ONECATEPSL_NAVIGATION'] = Configuration::get('FIELD_ONECATEPSL_NAVIGATION');
                $helper->fields_value['FIELD_ONECATEPSL_NBR'] = Configuration::get('FIELD_ONECATEPSL_NBR');
                $helper->fields_value['FIELD_ONECATEPSL_CAT'] = Configuration::get('FIELD_ONECATEPSL_CAT');

                foreach($languages as $language){
                    $helper->fields_value['FIELD_ONECATEPSL_TITLE'][$language['id_lang']] = Configuration::get('FIELD_ONECATEPSL_TITLE', $language['id_lang']);
                }

		return $helper->generateForm(array($fields_form));
	}
        
        /* ------------------------------------------------------------- */
        /*  PREPARE FOR HOOK
        /* ------------------------------------------------------------- */

        private function _prepHook($params)
        {
			$this->context->controller->addJS($this->_path.'views/js/hook/jquery.onecatepslowlcarousel.js');
            $id_default_lang = $this->context->language->id;

            $fieldonecatepsl = array(
                'FIELD_ONECATEPSL_TITLE' => Configuration::get('FIELD_ONECATEPSL_TITLE', $id_default_lang),
                'FIELD_ONECATEPSL_COLUMNITEM' => Configuration::get('FIELD_ONECATEPSL_COLUMNITEM'),
                'FIELD_ONECATEPSL_MAXITEM' => Configuration::get('FIELD_ONECATEPSL_MAXITEM'),
                'FIELD_ONECATEPSL_MEDIUMITEM' => Configuration::get('FIELD_ONECATEPSL_MEDIUMITEM'),
                'FIELD_ONECATEPSL_MINITEM' => Configuration::get('FIELD_ONECATEPSL_MINITEM'),
                'FIELD_ONECATEPSL_AUTOSCROLL' => Configuration::get('FIELD_ONECATEPSL_AUTOSCROLL'),
                'FIELD_ONECATEPSL_AUTOSCROLLDELAY' => Configuration::get('FIELD_ONECATEPSL_AUTOSCROLLDELAY'),
                'FIELD_ONECATEPSL_PAUSEONHOVER' => Configuration::get('FIELD_ONECATEPSL_PAUSEONHOVER'),
                'FIELD_ONECATEPSL_PAGINATION' => Configuration::get('FIELD_ONECATEPSL_PAGINATION'),
                'FIELD_ONECATEPSL_NAVIGATION' => Configuration::get('FIELD_ONECATEPSL_NAVIGATION'),
                'FIELD_ONECATEPSL_NBR' => Configuration::get('FIELD_ONECATEPSL_NBR'),
            );

            $this->smarty->assign('fieldonecatepsl', $fieldonecatepsl);
        }

}
