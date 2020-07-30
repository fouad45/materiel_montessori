<?php
/**
 * 2007-2017 Splashythemes
 *
 * NOTICE OF LICENSE
 *
 * St feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
 *
 * DISCLAIMER
 *
 *  @Module Name: ST Feature
 *  @author    splashythemes <splashythemes@gmail.com>
 *  @copyright 2007-2017 splashythemes
 *  @license   http://splashythemes.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_.'stfeature/classes/ProductReviewCriterion.php');
require_once(_PS_MODULE_DIR_.'stfeature/classes/ProductReview.php');
require_once(_PS_MODULE_DIR_.'stfeature/classes/CompareProduct.php');
require_once(_PS_MODULE_DIR_.'stfeature/classes/WishList.php');

class stfeature extends Module
{
    protected $config_form = false;
	private $link;
	public $static_token;
	public $link_cart;
	public $module_path;
	// public $array_wishlist_product;
	// public $array_wishlists;
	// public $id_wishlist;
	// public $id_lang;
	
    public function __construct()
    {
        $this->name = 'stfeature';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'SplashyThemes';
		$this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->need_instance = 0;
		$this->controllers = array('productscompare', 'mywishlist', 'viewwishlist');
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;
		//$this->static_token = Tools::getToken(false);
        parent::__construct();

		$this->secure_key = Tools::encrypt($this->name);
        $this->displayName = $this->l('ST Feature');
        $this->description = $this->l('ST feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list');
		$this->link = $this->context->link;
		
		$this->link_cart = $this->context->link->getPageLink('cart', true);
		$this->module_path = $this->local_path;
		// $this->array_wishlists = array();
		// $this->array_wishlist_product = array();
		
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        // Configuration::updateValue('STFEATURE_LIVE_MODE', false);

        // return parent::install() &&
            // $this->registerHook('header') &&
			// $this->registerHook('displayStCartButton') &&
            // $this->registerHook('backOfficeHeader');
		if (parent::install() && $this->registerStHook())
        {
            $res = true;
            /* Creates tables */
            $res &= $this->createTables();
           
            $id_parent = Tab::getIdFromClassName('IMPROVE');
            
            $class = 'Admin'.Tools::ucfirst($this->name).'Management';
            $tab1 = new Tab();
            $tab1->class_name = $class;
            $tab1->module = $this->name;
            $tab1->id_parent = $id_parent;
            $langs = Language::getLanguages(false);
            foreach ($langs as $l) {
                # validate module
                $tab1->name[$l['id_lang']] = $this->l('Splashy Feature Management');
            }
//            $id_tab1 = $tab1->add(true, false);
            $tab1->add(true, false);
			
			# insert icon for tab
			Db::getInstance()->execute(' UPDATE `'._DB_PREFIX_.'tab` SET `icon` = "star" WHERE `id_tab` = "'.(int)$tab1->id.'"');
			                       
			$this->installModuleTab('Splashy Feature Configuration', 'module', 'AdminStfeatureManagement');
			$this->installModuleTab('Product Review Management', 'reviews', 'AdminStfeatureManagement');
			
			$this->createConfiguration();
			
            return (bool)$res;
        }
        return false;
    }

    public function uninstall()
    {
        // Configuration::deleteByName('STFEATURE_LIVE_MODE');

        // return parent::uninstall();
		if (parent::uninstall() && $this->unregisterStHook()) {
            $res = true;

            $this->uninstallModuleTab('management');
            // $this->uninstallModuleTab('dashboard');
            // $this->uninstallModuleTab('categories');
            // $this->uninstallModuleTab('blogs');
            $this->uninstallModuleTab('reviews');
            $this->uninstallModuleTab('module');
            
            $res &= $this->deleteTables();
            $this->deleteConfiguration();

            return (bool)$res;
        }
        return false;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
		$output = '';
		//DONGND:: correct module
		if(Tools::getValue('correctmodule'))
		{
			$this->correctModule();
		}
		
		if (Tools::getValue('success'))
		{			
			switch (Tools::getValue('success'))
			{				
				case 'correct':
					$output .= $this->displayConfirmation($this->l('Correct Module is successful'));					
					break;
			}
		}
		
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitStfeatureConfig')) == true) {
            $this->postProcess();
        }
		
        // $this->context->smarty->assign('module_dir', $this->_path);	
		
		$output .= $this->renderGroupConfig();
		
        return $output;
    }
	
	public function renderGroupConfig()
    {
        $fields_form = array();
        $fields_form[0]['form'] = array(
   
                'input' => array(
					array(
						'type' => 'hidden',
						'name' => 'STFEATURE_DEFAULT_TAB',                   
						'default' => '',
					),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Button Cart At Product List'),
                        'name' => 'STFEATURE_ENABLE_AJAXCART',
                        'is_bool' => true,
                        // 'desc' => $this->l('Show Button Cart At Product List'),
                        'values' => array(
                            array(
                                'id' => 'STFEATURE_ENABLE_AJAXCART_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'STFEATURE_ENABLE_AJAXCART_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
					array(
                        'type' => 'switch',
                        'label' => $this->l('Show Select Attribute'),
                        'name' => 'STFEATURE_ENABLE_SELECTATTRIBUTE',
                        'is_bool' => true,
                        // 'desc' => $this->l('Show Select Attribute'),
                        'values' => array(
                            array(
                                'id' => 'STFEATURE_ENABLE_SELECTATTRIBUTE_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'STFEATURE_ENABLE_SELECTATTRIBUTE_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),					
					array(
                        'type' => 'switch',
                        'label' => $this->l('Update product label after select attribute'),
                        'name' => 'STFEATURE_ENABLE_UPDATELABEL',
                        'is_bool' => true,
                        'desc' => $this->l('Only for enable select attribute'),
                        'values' => array(
                            array(
                                'id' => 'STFEATURE_ENABLE_UPDATELABEL_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'STFEATURE_ENABLE_UPDATELABEL_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),								
					array(
                        'type' => 'switch',
                        'label' => $this->l('Show Input Quantity'),
                        'name' => 'STFEATURE_ENABLE_INPUTQUANTITY',
                        'is_bool' => true,
                        // 'desc' => $this->l('Show Input Quantity'),
                        'values' => array(
                            array(
                                'id' => 'STFEATURE_ENABLE_INPUTQUANTITY_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'STFEATURE_ENABLE_INPUTQUANTITY_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),                                                                                     
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default'
				)     
        );
		//DONGND:: image setting
		$fields_form[1]['form'] = array(
			'input' => array(
				array(				
					'type' => 'switch',
					'label' => $this->l('Enable product reviews'),
					'name' => 'STFEATURE_ENABLE_PRODUCT_REVIEWS',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_ENABLE_PRODUCT_REVIEWS_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_ENABLE_PRODUCT_REVIEWS_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(				
					'type' => 'switch',
					'label' => $this->l('Show product reviews at list product'),
					'name' => 'STFEATURE_SHOW_PRODUCT_REVIEWS_LISTPRODUCT',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_SHOW_PRODUCT_REVIEWS_LISTPRODUCT_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_SHOW_PRODUCT_REVIEWS_LISTPRODUCT_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),
				),				
				array(				
					'type' => 'switch',
					'label' => $this->l('Show number product reviews at list product'),
					'name' => 'STFEATURE_SHOW_NUMBER_PRODUCT_REVIEWS_LISTPRODUCT',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_SHOW_NUMBER_PRODUCT_REVIEWS_LISTPRODUCT_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_SHOW_NUMBER_PRODUCT_REVIEWS_LISTPRODUCT_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(				
					'type' => 'switch',
					'label' => $this->l('Show zero product reviews at list product'),
					'name' => 'STFEATURE_SHOW_ZERO_PRODUCT_REVIEWS_LISTPRODUCT',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_SHOW_ZERO_PRODUCT_REVIEWS_LISTPRODUCT_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_SHOW_ZERO_PRODUCT_REVIEWS_LISTPRODUCT_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(			
					'type' => 'switch',
					'label' => $this->l('All reviews must be validated by an employee'),
					'name' => 'STFEATURE_PRODUCT_REVIEWS_MODERATE',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_PRODUCT_REVIEWS_MODERATE_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_PRODUCT_REVIEWS_MODERATE_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(			
					'type' => 'switch',
					'label' => $this->l('Allow usefull button'),
					'name' => 'STFEATURE_PRODUCT_REVIEWS_ALLOW_USEFULL_BUTTON',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_PRODUCT_REVIEWS_ALLOW_USEFULL_BUTTON_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_PRODUCT_REVIEWS_ALLOW_USEFULL_BUTTON_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(			
					'type' => 'switch',
					'label' => $this->l('Allow report button'),
					'name' => 'STFEATURE_PRODUCT_REVIEWS_ALLOW_REPORT_BUTTON',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_PRODUCT_REVIEWS_ALLOW_REPORT_BUTTON_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_PRODUCT_REVIEWS_ALLOW_REPORT_BUTTON_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Allow guest reviews'),
					'name' => 'STFEATURE_PRODUCT_REVIEWS_ALLOW_GUESTS',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_PRODUCT_REVIEWS_ALLOW_GUESTS_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_PRODUCT_REVIEWS_ALLOW_GUESTS_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(
					
					'type' => 'text',
					'label' => $this->l('Minimum time between 2 reviews from the same user'),
					'name' => 'STFEATURE_PRODUCT_REVIEWS_MINIMAL_TIME',
					'lang' => false,
					'suffix' => $this->l('seconds'),
				),
				
			),
			'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default') 
		);
		//DONGND:: css setting
		$fields_form[2]['form'] = array(
			'input' => array(
				array(
					'type' => 'switch',
					'label' => $this->l('Enable product compare'),
					'name' => 'STFEATURE_ENABLE_PRODUCTCOMPARE',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_ENABLE_PRODUCTCOMPARE_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_ENABLE_PRODUCTCOMPARE_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Show product compare at list product'),
					'name' => 'STFEATURE_SHOW_PRODUCTCOMPARE_LISTPRODUCT',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_SHOW_PRODUCTCOMPARE_LISTPRODUCT_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_SHOW_PRODUCTCOMPARE_LISTPRODUCT_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Show product compare at product page'),
					'name' => 'STFEATURE_SHOW_PRODUCTCOMPARE_PRODUCTPAGE',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_SHOW_PRODUCTCOMPARE_PRODUCTPAGE_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_SHOW_PRODUCTCOMPARE_PRODUCTPAGE_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),
				),					
				array(				
					'type' => 'text',
					'label' => $this->l('Number product comparison '),
					'name' => 'STFEATURE_COMPARATOR_MAX_ITEM',
					'lang' => false,				
				),
			),
			'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default') 
		);
		
		//DONGND:: navigatior and direction
		$fields_form[3]['form'] = array(
			'input' => array(
				array(
					'type' => 'switch',
					'label' => $this->l('Enable product wishlist'),
					'name' => 'STFEATURE_ENABLE_PRODUCTWISHLIST',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_ENABLE_PRODUCTWISHLIST_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_ENABLE_PRODUCTWISHLIST_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),			
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Show product wishlist at list product'),
					'name' => 'STFEATURE_SHOW_PRODUCTWISHLIST_LISTPRODUCT',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_SHOW_PRODUCTWISHLIST_LISTPRODUCT_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_SHOW_PRODUCTWISHLIST_LISTPRODUCT_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),			
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Show product wishlist at product page'),
					'name' => 'STFEATURE_SHOW_PRODUCTWISHLIST_PRODUCTPAGE',
					'is_bool' => true,
					//'desc' => $this->l('Show Input Quantity'),
					'values' => array(
						array(
							'id' => 'STFEATURE_SHOW_PRODUCTWISHLIST_PRODUCTPAGE_on',
							'value' => true,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'STFEATURE_SHOW_PRODUCTWISHLIST_PRODUCTPAGE_off',
							'value' => false,
							'label' => $this->l('Disabled')
						)
					),			
				),
				
			),
			'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default') 
		);
       
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->name_controller = 'stfeature';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitStfeatureConfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getGroupFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            
        );

        $globalform = $helper->generateForm($fields_form);
		
		//DONGND::
		$this->context->smarty->assign(array(
            'globalform' => $globalform,
            'url_admin' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name,
			'default_tab' => Configuration::get('STFEATURE_DEFAULT_TAB'),
           
        ));
		return $this->context->smarty->fetch($this->local_path.'views/templates/admin/panel.tpl');
    }

    /**
     * Set values for the inputs.
     */
    protected function getGroupFieldsValues()
    {	
        return array(
            'STFEATURE_DEFAULT_TAB' => Configuration::get('STFEATURE_DEFAULT_TAB'),
            'STFEATURE_ENABLE_AJAXCART' => Configuration::get('STFEATURE_ENABLE_AJAXCART'),
            'STFEATURE_ENABLE_SELECTATTRIBUTE' => Configuration::get('STFEATURE_ENABLE_SELECTATTRIBUTE'),
			'STFEATURE_ENABLE_UPDATELABEL' => Configuration::get('STFEATURE_ENABLE_UPDATELABEL'),		
			'STFEATURE_ENABLE_INPUTQUANTITY' => Configuration::get('STFEATURE_ENABLE_INPUTQUANTITY'),
			'STFEATURE_ENABLE_PRODUCT_REVIEWS' => Configuration::get('STFEATURE_ENABLE_PRODUCT_REVIEWS'),
			'STFEATURE_SHOW_PRODUCT_REVIEWS_LISTPRODUCT' => Configuration::get('STFEATURE_SHOW_PRODUCT_REVIEWS_LISTPRODUCT'),
			'STFEATURE_SHOW_NUMBER_PRODUCT_REVIEWS_LISTPRODUCT' => Configuration::get('STFEATURE_SHOW_NUMBER_PRODUCT_REVIEWS_LISTPRODUCT'),
			'STFEATURE_SHOW_ZERO_PRODUCT_REVIEWS_LISTPRODUCT' => Configuration::get('STFEATURE_SHOW_ZERO_PRODUCT_REVIEWS_LISTPRODUCT'),			
			'STFEATURE_PRODUCT_REVIEWS_MODERATE' => Configuration::get('STFEATURE_PRODUCT_REVIEWS_MODERATE'),
			'STFEATURE_PRODUCT_REVIEWS_ALLOW_GUESTS' => Configuration::get('STFEATURE_PRODUCT_REVIEWS_ALLOW_GUESTS'),
			'STFEATURE_PRODUCT_REVIEWS_ALLOW_REPORT_BUTTON' => Configuration::get('STFEATURE_PRODUCT_REVIEWS_ALLOW_REPORT_BUTTON'),
			'STFEATURE_PRODUCT_REVIEWS_ALLOW_USEFULL_BUTTON' => Configuration::get('STFEATURE_PRODUCT_REVIEWS_ALLOW_USEFULL_BUTTON'),
			'STFEATURE_PRODUCT_REVIEWS_MINIMAL_TIME' => Configuration::get('STFEATURE_PRODUCT_REVIEWS_MINIMAL_TIME'),
			'STFEATURE_ENABLE_PRODUCTCOMPARE' => Configuration::get('STFEATURE_ENABLE_PRODUCTCOMPARE'),
			'STFEATURE_SHOW_PRODUCTCOMPARE_LISTPRODUCT' => Configuration::get('STFEATURE_SHOW_PRODUCTCOMPARE_LISTPRODUCT'),
			'STFEATURE_SHOW_PRODUCTCOMPARE_PRODUCTPAGE' => Configuration::get('STFEATURE_SHOW_PRODUCTCOMPARE_PRODUCTPAGE'),
			'STFEATURE_COMPARATOR_MAX_ITEM' => Configuration::get('STFEATURE_COMPARATOR_MAX_ITEM'),
			'STFEATURE_ENABLE_PRODUCTWISHLIST' => Configuration::get('STFEATURE_ENABLE_PRODUCTWISHLIST'),
			'STFEATURE_SHOW_PRODUCTWISHLIST_PRODUCTPAGE' => Configuration::get('STFEATURE_SHOW_PRODUCTWISHLIST_PRODUCTPAGE'),
			'STFEATURE_SHOW_PRODUCTWISHLIST_LISTPRODUCT' => Configuration::get('STFEATURE_SHOW_PRODUCTWISHLIST_LISTPRODUCT'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getGroupFieldsValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
		
		Tools::redirectAdmin('index.php?controller=AdminModules&configure=stfeature&token='.Tools::getAdminTokenLite('AdminModules').'&conf=4');
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookActionAdminControllerSetMedia()
	{
		$this->context->controller->addJS($this->_path.'views/js/back.js');
		$this->context->controller->addCSS($this->_path.'views/css/back.css');
		Media::addJsDef(
			array(
				'leofeature_module_dir' => $this->_path,				
			)
		);
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
		if (Configuration::get('STFEATURE_ENABLE_AJAXCART'))
		{
			$this->context->controller->addJS($this->_path.'views/js/stfeature_cart.js');
			
			if (Configuration::get('STFEATURE_ENABLE_SELECTATTRIBUTE'))
			{
				Media::addJsDef(
					array(					
						'enable_product_label' => Configuration::get('STFEATURE_ENABLE_UPDATELABEL'),					
					)
				);
			}
			
		}
		
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
		$this->static_token = Tools::getToken(false);
		// $this->id_lang = $this->context->language->id;
		// if (isset($this->context->controller->php_self) && ($this->context->controller->php_self == 'product')) {
		if (Configuration::get('STFEATURE_ENABLE_PRODUCT_REVIEWS'))
		{
			$this->context->controller->addJS($this->_path.'views/js/jquery.rating.pack.js');
            $this->context->controller->addJS($this->_path.'views/js/stfeature_review.js');
		}
        // }
		
		
		if (Configuration::get('STFEATURE_ENABLE_PRODUCTCOMPARE') && Configuration::get('STFEATURE_COMPARATOR_MAX_ITEM') > 0)
		{
			$this->context->controller->addJS($this->_path.'views/js/stfeature_compare.js');
			
			//DONGND:: add javascript param for compare
			$compared_products = array();
			if (Configuration::get('STFEATURE_COMPARATOR_MAX_ITEM') && isset($this->context->cookie->id_compare)) {
				$compared_products = StCompareProduct::getCompareProducts($this->context->cookie->id_compare);
			}
			
			$comparator_max_item = (int)Configuration::get('STFEATURE_COMPARATOR_MAX_ITEM');
			
			$productcompare_max_item = sprintf($this->l('You cannot add more than %d product(s) to the product comparison'), $comparator_max_item);		
			Media::addJsDef(
				array(
					'productcompare_url' => $this->link->getModuleLink('stfeature', 'productscompare'),
					'productcompare_add' => $this->l('The product has been added to list compare'),
					'productcompare_viewlistcompare' => $this->l('View list compare'),
					'productcompare_remove' => $this->l('The product was successfully removed from list compare'),
					'comparator_max_item' => $comparator_max_item,
					'compared_products' => (count($compared_products)>0) ? $compared_products : array(),
					'productcompare_max_item' => $productcompare_max_item,
					'buttoncompare_title_add' => $this->l('Add to Compare'),
					'buttoncompare_title_remove' => $this->l('Remove from Compare'),
				)
			);
		}
		
		if (Configuration::get('STFEATURE_ENABLE_PRODUCTWISHLIST'))
		{
			$this->context->controller->addJS($this->_path.'views/js/stfeature_wishlist.js');
			//DONGND:: add javascript param for wishlist
			if ($this->context->customer->isLogged())
			{
				$isLogged = true;
			}
			else
			{
				$isLogged = false;
			}
			Media::addJsDef(
				array(
					'wishlist_url' => $this->link->getModuleLink('stfeature', 'mywishlist'),
					'wishlist_add' => $this->l('The product was successfully added to your wishlist'),
					'wishlist_viewwishlist' => $this->l('View your wishlist'),
					'wishlist_remove' => $this->l('The product was successfully removed from your wishlist'),			
					// 'wishlist_products' => (count($this->array_wishlist_product)>0) ? $this->array_wishlist_product : array(),			
					'buttonwishlist_title_add' => $this->l('Add to Wishlist'),
					'buttonwishlist_title_remove' => $this->l('Remove from WishList'),
					'wishlist_loggin_required' => $this->l('You must be logged in to manage your wishlist'),
					'isLogged' => $isLogged,
					'wishlist_cancel_txt' => $this->l('Cancel'),
					'wishlist_ok_txt' => $this->l('Ok'),
					'wishlist_send_txt' => $this->l('Send'),
					'wishlist_reset_txt' => $this->l('Reset'),
					'wishlist_send_wishlist_txt' => $this->l('Send wishlist'),
					'wishlist_email_txt' => $this->l('Email'),
					'wishlist_confirm_del_txt' => $this->l('Delete selected item?'),
					'wishlist_del_default_txt' => $this->l('Cannot delete default wishlist'),
					'wishlist_quantity_required' => $this->l('You must enter a quantity'),
				)
			);
		}
		if (Configuration::get('STFEATURE_ENABLE_PRODUCT_REVIEWS'))
		{		
			Media::addJsDef(
				array(					
					'disable_review_form_txt' => $this->l('Not exists a criterion to review for this product or this language'),					
				)
			);
		}
    }
	
	public function hookdisplayStCartButton($params)
    {
		if (Configuration::get('STFEATURE_ENABLE_AJAXCART'))
		{
			// $presenter = new ProductPresenter();
			$product = $params['product'];
			$id_product = $product['id_product'];
			if ($this->shouldEnableAddToCartButton($product)) {
				$product['add_to_cart_url'] = $this->getAddToCartURL($product);
			} else {
				$product['add_to_cart_url'] = null;
			}
			
			if ($product['customizable']) {
				$customization_datas = $this->context->cart->getProductCustomization($id_product, null, true);
			}
			
			$product['id_customization'] = empty($customization_datas) ? null : $customization_datas[0]['id_customization'];
						
			if (Configuration::get('STFEATURE_ENABLE_SELECTATTRIBUTE') && count($product['attributes'])>0)
			{				
				$attributes = $this->getAttributesResume($id_product, $this->context->language->id);
				
				if (!empty($attributes)) {			
					$whitelist = $this->getProductAttributeWhitelist();
					foreach ($attributes as $k_attributes =>$v_attributes)
					{
						if ($v_attributes['id_product_attribute'] == $product['id_product_attribute'])
						{
							$product['attribute_designation'] = $v_attributes['attribute_designation'];
						}
						foreach ($whitelist as $v_whitelist) {
							if (isset($product[$v_whitelist]))
							{
								$attributes[$k_attributes][$v_whitelist] = $product[$v_whitelist];
							}
						}
						
						if ($this->shouldEnableAddToCartButton($attributes[$k_attributes])) {
							$attributes[$k_attributes]['add_to_cart_url'] = $this->getAddToCartURL($attributes[$k_attributes]);
						} else {
							$attributes[$k_attributes]['add_to_cart_url'] = null;
						}
					}
					$product['combinations'] = $attributes;
					
				}
			}
			
			//DONGND:: fix for some case have not 
			if (!isset($product['product_attribute_minimal_quantity']))
			{
				// print_r('test');die();
				$product['product_attribute_minimal_quantity'] = Attribute::getAttributeMinimalQty($product['id_product_attribute']);
			}
			
			$templateVars = array(
				'static_token' => $this->static_token,
				'leo_cart_product' => $product,
				'link_cart' => $this->link_cart,
				'show_input_quantity' => Configuration::get('STFEATURE_ENABLE_INPUTQUANTITY'),
			);
			// echo '<pre>';
			// print_r($product);die();
			$this->context->smarty->assign($templateVars);
			return $this->display(__FILE__, 'st_cart_button.tpl');
		}
    }
	
	
	public function hookdisplayStProductReviewExtra($params)
    {
		if (Configuration::get('STFEATURE_ENABLE_PRODUCT_REVIEWS'))
		{	
			$product = $params['product'];
			// echo '<pre>';
			// print_r($product);die();
			$id_product = $product['id_product'];
			
			$id_guest = (!$id_customer = (int) $this->context->cookie->id_customer) ? (int) $this->context->cookie->id_guest : false;
			$customerReview = ProductReview::getByCustomer((int) $id_product, (int) $this->context->cookie->id_customer, true, (int) $id_guest);

			$average = ProductReview::getAverageGrade((int) $id_product);        
					
			$this->context->smarty->assign(array(                      
				// 'secure_key' => $this->secure_key,
				// 'logged' => $this->context->customer->isLogged(true),
					'allow_guests_extra' => (int) Configuration::get('STFEATURE_PRODUCT_REVIEWS_ALLOW_GUESTS'),                                  
					//'criterions' => ProductReviewCriterion::getByProduct((int) $id_product, $this->context->language->id),            
					'averageTotal_extra' => round($average['grade']),
					'ratings_extra' => ProductReview::getRatings((int) $id_product),
					'too_early_extra' => ($customerReview && (strtotime($customerReview['date_add']) + Configuration::get('STFEATURE_PRODUCT_REVIEWS_MINIMAL_TIME')) > time()),
					'nbReviews_product_extra' => (int) (ProductReview::getReviewNumber((int) $id_product)),
					'id_product_review_extra' => $id_product,
					'link_product_review_extra' => $product['link'],
			));
			// print_r((int) (ProductReview::getReviewNumber((int) $id_product)));die();
			return $this->display(__FILE__, 'st_product_review_extra.tpl');
		}
	}
	
	//DONGND:: create review tab at product page
	public function hookdisplayStProductTab($params)
    {
		if (Configuration::get('STFEATURE_ENABLE_PRODUCT_REVIEWS'))
		{
			// print_r($params['use_ptabs']);die();
			if (Module::isInstalled('appagebuilder') && Module::isEnabled('appagebuilder')) {
				include_once(_PS_MODULE_DIR_.'appagebuilder/appagebuilder.php');
				$module = new APPageBuilder();				
				$this->context->smarty->assign(array(                                  
					'USE_PTABS' => $module->getConfig('ENABLE_PTAB'),                                     			                                                       
				));
			}
			
			return $this->display(__FILE__, 'st_product_tab.tpl');
		}
	}
	
	//DONGND:: display reviews on review tab at product detail
	public function hookdisplayStProductTabContent($params)
    {
		if (Configuration::get('STFEATURE_ENABLE_PRODUCT_REVIEWS'))
		{
			$product = $params['product'];
			$id_product = $product['id_product'];
			
			$id_guest = (!$id_customer = (int) $this->context->cookie->id_customer) ? (int) $this->context->cookie->id_guest : false;
			$customerReview = StProductReview::getByCustomer((int) $id_product, (int) $this->context->cookie->id_customer, true, (int) $id_guest);

			$this->context->smarty->assign(array(                                  
				'reviews' => StProductReview::getByProduct((int) $id_product, 1, null, $this->context->cookie->id_customer),                                               
				'allow_guests' => (int) Configuration::get('STFEATURE_PRODUCT_REVIEWS_ALLOW_GUESTS'),
				'too_early' => ($customerReview && (strtotime($customerReview['date_add']) + Configuration::get('STFEATURE_PRODUCT_REVIEWS_MINIMAL_TIME')) > time()), 		
				'allow_report_button' => (int) Configuration::get('STFEATURE_PRODUCT_REVIEWS_ALLOW_REPORT_BUTTON'),
				'allow_usefull_button' => (int) Configuration::get('STFEATURE_PRODUCT_REVIEWS_ALLOW_USEFULL_BUTTON'),
				'id_product_tab_content' => $id_product,
				'link_product_tab_content' => $product['link'],
			));
			if (Module::isInstalled('appagebuilder') && Module::isEnabled('appagebuilder')) {
				include_once(_PS_MODULE_DIR_.'appagebuilder/appagebuilder.php');
				$module = new APPageBuilder();				
				$this->context->smarty->assign(array(                                  
					'USE_PTABS' => $module->getConfig('ENABLE_PTAB'),                                     			                                                       
				));
			}
			
			return $this->display(__FILE__, 'st_product_tab_content.tpl');
		}
	}
	
	//DONGND:: display review of product at product list
	public function hookdisplayStProductListReview($params)
    {
		if (Configuration::get('STFEATURE_ENABLE_PRODUCT_REVIEWS') && Configuration::get('STFEATURE_SHOW_PRODUCT_REVIEWS_LISTPRODUCT'))
		{
			$product = $params['product'];
			$id_product = $product['id_product'];
			if (!$this->isCached('st_list_product_review.tpl', $this->getCacheId($id_product))) {            
				$average = StProductReview::getAverageGrade($id_product);
				$this->smarty->assign(array(
					// 'product' => $product,
					'averageTotal' => round($average['grade']),
					'ratings' => StProductReview::getRatings($id_product),
					'nbReviews' => (int) StProductReview::getReviewNumber($id_product),
					'show_number_product_review' => Configuration::get('STFEATURE_SHOW_NUMBER_PRODUCT_REVIEWS_LISTPRODUCT'),
					'show_zero_product_review' => Configuration::get('STFEATURE_SHOW_ZERO_PRODUCT_REVIEWS_LISTPRODUCT'),
				));
			}
			return $this->display(__FILE__, 'st_list_product_review.tpl', $this->getCacheId($id_product));
		}
	}
	
	//DONGND:: display compare button
	public function hookdisplayStCompareButton($params)
    {
		if (Configuration::get('STFEATURE_ENABLE_PRODUCTCOMPARE') && Configuration::get('STFEATURE_COMPARATOR_MAX_ITEM') > 0)
		{
			$page_name = Dispatcher::getInstance()->getController();
			if ((Configuration::get('STFEATURE_SHOW_PRODUCTCOMPARE_LISTPRODUCT') && $page_name != 'product') 
				|| (Configuration::get('STFEATURE_SHOW_PRODUCTCOMPARE_PRODUCTPAGE') && $page_name == 'product')			
			)
			{
				$id_product = $params['product']['id_product'];
				$compared_products = array();
				if (Configuration::get('STFEATURE_COMPARATOR_MAX_ITEM') && isset($this->context->cookie->id_compare)) {
					$compared_products = StCompareProduct::getCompareProducts($this->context->cookie->id_compare);
				}
				$added = false;
				if (count($compared_products) > 0 && in_array($id_product, $compared_products))
				{
					$added = true;
				}
				$this->smarty->assign(array(
					'added' => $added,
					'leo_compare_id_product' => $id_product,
				));
				return $this->display(__FILE__, 'st_compare_button.tpl');
			}
		}
	}
	
	//DONGND:: display review at product compare page
	public function hookdisplayStProducReviewCompare($params)
    {	
		if (Configuration::get('STFEATURE_ENABLE_PRODUCT_REVIEWS'))
		{
			// echo '<pre>';
			// print_r($params['list_product']);die();
			$list_grades = array();
			$list_product_grades = array();
			$list_product_average = array();
			$list_product_review = array();

			foreach ($params['list_product'] as $id_product) {
				$id_product = (int) $id_product;
				$grades = StProductReview::getAveragesByProduct($id_product, $this->context->language->id);
				$criterions = StProductReviewCriterion::getByProduct($id_product, $this->context->language->id);
				$grade_total = 0;
				if (count($grades) > 0) {
					foreach ($criterions as $criterion) {
						if (isset($grades[$criterion['id_product_review_criterion']])) {
							$list_product_grades[$criterion['id_product_review_criterion']][$id_product] = $grades[$criterion['id_product_review_criterion']];
							$grade_total += (float) ($grades[$criterion['id_product_review_criterion']]);
						} else {
							$list_product_grades[$criterion['id_product_review_criterion']][$id_product] = 0;
						}

						if (!array_key_exists($criterion['id_product_review_criterion'], $list_grades)) {
							$list_grades[$criterion['id_product_review_criterion']] = $criterion['name'];
						}
					}

					$list_product_average[$id_product] = $grade_total / count($criterions);
					$list_product_review[$id_product] = StProductReview::getByProduct($id_product, 0, 3);
				}
			}

			if (count($list_grades) < 1) {
				return false;
			}

			$this->context->smarty->assign(array(
				'grades' => $list_grades,
				'product_grades' => $list_product_grades,
				'list_ids_product' => $params['list_product'],
				'list_product_average' => $list_product_average,
				'product_reviews' => $list_product_review,
			));

			return $this->display(__FILE__, '/st_product_review_compare.tpl');
		}
	}
	
	//DONGND:: display wishlist button
	public function hookdisplayStWishlistButton($params)
    {
		if (Configuration::get('STFEATURE_ENABLE_PRODUCTWISHLIST'))
		{
			$page_name = Dispatcher::getInstance()->getController();
			if ((Configuration::get('STFEATURE_SHOW_PRODUCTWISHLIST_LISTPRODUCT') && $page_name != 'product') 
				|| (Configuration::get('STFEATURE_SHOW_PRODUCTWISHLIST_PRODUCTPAGE') && $page_name == 'product') 
			)
			{	
				$wishlists = array();
				$wishlists_added = array();
				$id_wishlist = false;
				$added_wishlist = false;
				$id_product = $params['product']['id_product'];
				$id_product_attribute = $params['product']['id_product_attribute'];
				if ($this->context->customer->isLogged())
				{
					$wishlists = StWishlist::getByIdCustomer($this->context->customer->id);	
					if (empty($this->context->cookie->id_wishlist) === true ||
						StWishList::exists($this->context->cookie->id_wishlist, $this->context->customer->id) === false)
					{
						if (!count($wishlists))
							$id_wishlist = false;
						else
						{
							$id_wishlist = (int)$wishlists[0]['id_wishlist'];
							$this->context->cookie->id_wishlist = (int)$id_wishlist;
						}
					}
					else
						$id_wishlist = $this->context->cookie->id_wishlist;
				
					$wishlist_products = ($id_wishlist == false ? array() : StWishList::getSimpleProductByIdCustomer($this->context->customer->id, $this->context->shop->id));
									
					$check_product_added = array('id_product' => $id_product, 'id_product_attribute' => $id_product_attribute);
					
					foreach ($wishlist_products as $key => $wishlist_products_val)
					{
						if (in_array($check_product_added, $wishlist_products_val))
						{
							$added_wishlist = true;
							$wishlists_added[] = $key;
						}
						
					}
				}	
				
				$this->smarty->assign(
					array(				
						'wishlists_added' => $wishlists_added,
						'wishlists' => $wishlists,
						'added_wishlist' => $added_wishlist,
						'id_wishlist' => $id_wishlist,
						'leo_wishlist_id_product' => $id_product,
						'leo_wishlist_id_product_attribute' => $id_product_attribute,
					)
				);
				
				return $this->display(__FILE__, 'st_wishlist_button.tpl');
			}
		}
	}
	
	//DONGND:: add mywishlist link to page my account
	public function hookdisplayCustomerAccount($params)
    {
		if (Configuration::get('STFEATURE_ENABLE_PRODUCTWISHLIST'))
		{
			$this->context->smarty->assign(array(
				'wishlist_link' => $this->link->getModuleLink('stfeature', 'mywishlist'),            
			));
			return $this->display(__FILE__, 'st_wishlist_link.tpl');
		}
	}
	
	//DONGND:: copy function from base
	protected function shouldEnableAddToCartButton(array $product)
    {
        if (($product['customizable'] == 2 || !empty($product['customization_required']))) {
            $shouldShowButton = false;

            if (isset($product['customizations'])) {
                $shouldShowButton = true;
                foreach ($product['customizations']['fields'] as $field) {
                    if ($field['required'] && !$field['is_customized']) {
                        $shouldShowButton = false;
                    }
                }
            }
        } else {
            $shouldShowButton = true;
        }

        $shouldShowButton = $shouldShowButton && $this->shouldShowAddToCartButton($product);

        if ($product['quantity'] <= 0 && !$product['allow_oosp']) {
            $shouldShowButton = false;
        }

        return $shouldShowButton;
    }
	
	//DONGND:: copy function from base
	private function shouldShowAddToCartButton($product)
    {
        return (bool) $product['available_for_order'];
    }
	
	//DONGND:: copy function from base
	private function getAddToCartURL(array $product)
    {
		// echo '<pre>';
		// print_r($this->link);die();
        return $this->link->getAddToCartURL(
            $product['id_product'],
            $product['id_product_attribute']
        );
    }
	
	//DONGND:: render modal cart popup
	public function renderModal()
	{
		$output = $this->fetch('module:stfeature/views/templates/front/modal.tpl');

        return $output;
	}
	
	//DONGND:: render modal review popup
	public function renderModalReview($id_product, $is_logged)
	{
		$product = new Product((int)$id_product, false, $this->context->language->id, $this->context->shop->id);
		// echo '<pre>';
		// print_r($product);
        $image = Product::getCover((int) $id_product);
        $cover_image = $this->context->link->getImageLink($product->link_rewrite, $image['id_image'], ImageType::getFormattedName('home'));
		// print_r($cover_image);die();
		$this->context->smarty->assign(array(            
            'product_modal_review' => $product,            
            'criterions' => StProductReviewCriterion::getByProduct((int)$id_product, $this->context->language->id),              
            'secure_key' => $this->secure_key,            
            'productcomment_cover_image' => $cover_image,            
			'allow_guests' => (int) Configuration::get('STFEATURE_PRODUCT_REVIEWS_ALLOW_GUESTS'),
			'is_logged' => (int) $is_logged,
       ));
	    
		$output = $this->fetch('module:stfeature/views/templates/front/modal_review.tpl');

        return $output;
	}
	
	//DONGND:: render modal popup
	public function renderPriceAttribute($product)
	{
		$templateVars = array(            
            'product_price_attribute' => $product,            
        );
		$this->context->smarty->assign($templateVars);
		
		$output = $this->fetch('module:stfeature/views/templates/front/price_attribute.tpl');

        return $output;
	}
	
	//DONGND:: get list attribute of product
    public function getAttributesResume($id_product, $id_lang, $attribute_value_separator = ' - ', $attribute_separator = ', ')
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        $combinations = Db::getInstance()->executeS('SELECT pa.*, product_attribute_shop.*
				FROM `'._DB_PREFIX_.'product_attribute` pa
				'.Shop::addSqlAssociation('product_attribute', 'pa').'
				WHERE pa.`id_product` = '.(int)$id_product.'
				GROUP BY pa.`id_product_attribute`');

        if (!$combinations) {
            return false;
        }

        $product_attributes = array();
        foreach ($combinations as $combination) {
            $product_attributes[] = (int)$combination['id_product_attribute'];
        }

        $lang = Db::getInstance()->executeS('SELECT pac.id_product_attribute, GROUP_CONCAT(agl.`name`, \''.pSQL($attribute_value_separator).'\',al.`name` ORDER BY agl.`id_attribute_group` SEPARATOR \''.pSQL($attribute_separator).'\') as attribute_designation
				FROM `'._DB_PREFIX_.'product_attribute_combination` pac
				LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
				LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
				WHERE pac.id_product_attribute IN ('.implode(',', $product_attributes).')
				GROUP BY pac.id_product_attribute');

        foreach ($lang as $k => $row) {
            $combinations[$k]['attribute_designation'] = $row['attribute_designation'];
        }

        //Get quantity of each variations
        foreach ($combinations as $key => $row) {
            $cache_key = $row['id_product'].'_'.$row['id_product_attribute'].'_quantity';

            if (!Cache::isStored($cache_key)) {
                $result = StockAvailable::getQuantityAvailableByProduct($row['id_product'], $row['id_product_attribute']);
                Cache::store(
                    $cache_key,
                    $result
                );
                $combinations[$key]['quantity'] = $result;
            } else {
                $combinations[$key]['quantity'] = Cache::retrieve($cache_key);
            }
        }

        return $combinations;
    }
	
	//DONGND:: get list attribute of product inherit to attribute of its
	public function getProductAttributeWhitelist()
    {
        return array(
			"customizable",
			"available_for_order",
			"customization_required",
			"customizations",
			"allow_oosp",
        );
    }
	
	/**
     * Common method
     * Resgister all hook for module
     */
    public function registerStHook()
    {
        $res = true;        
        $res &= $this->registerHook('header');
        $res &= $this->registerHook('displayStCartButton');       
        $res &= $this->registerHook('displayBackOfficeHeader');  
		$res &= $this->registerHook('displayStProductReviewExtra');
		$res &= $this->registerHook('displayStProductTab');
		$res &= $this->registerHook('displayStProductTabContent');
		$res &= $this->registerHook('displayStProductListReview');
		$res &= $this->registerHook('displayStCompareButton');
		$res &= $this->registerHook('displayStWishlistButton');
		$res &= $this->registerHook('displayStProducReviewCompare');
		$res &= $this->registerHook('displayCustomerAccount');
		$res &= $this->registerHook('actionAdminControllerSetMedia');
        return $res;        
    }
	
	/**
     * Common method
     * Unresgister all hook for module
     */
    public function unregisterStHook()
    {
        $res = true;        
        $res &= $this->unregisterHook('header');       
        $res &= $this->unregisterHook('displayStCartButton');       
        $res &= $this->unregisterHook('displayBackOfficeHeader');
		$res &= $this->unregisterHook('displayStProductReviewExtra'); 
		$res &= $this->unregisterHook('displayStProductTab'); 
		$res &= $this->unregisterHook('displayStProductTabContent'); 
		$res &= $this->unregisterHook('displayStProductListReview');
		$res &= $this->unregisterHook('displayStCompareButton');
		$res &= $this->unregisterHook('displayStWishlistButton');
		$res &= $this->unregisterHook('displayStProducReviewCompare');
		$res &= $this->unregisterHook('displayCustomerAccount');
		$res &= $this->unregisterHook('actionAdminControllerSetMedia');
        return $res;        
    }
	
	/**
     * Install Module Tabs
     */
    private function installModuleTab($title, $class_sfx = '', $parent = '')
    {
        $class = 'Admin'.Tools::ucfirst($this->name).Tools::ucfirst($class_sfx);
        // @copy(_PS_MODULE_DIR_.$this->name.'/logo.gif', _PS_IMG_DIR_.'t/'.$class.'.gif');
        if ($parent == '') {
            # validate module
            $position = Tab::getCurrentTabId();
        } else {
            # validate module
            $position = Tab::getIdFromClassName($parent);
        }

        $tab1 = new Tab();
        $tab1->class_name = $class;
        $tab1->module = $this->name;
        $tab1->id_parent = (int)$position;
        $langs = Language::getLanguages(false);
        foreach ($langs as $l) {
            # validate module
            $tab1->name[$l['id_lang']] = $title;
        }
        $tab1->add(true, false);
    }
	
	/**
     * Uninstall tabs
     */
    private function uninstallModuleTab($class_sfx = '')
    {
        $tab_class = 'Admin'.Tools::ucfirst($this->name).Tools::ucfirst($class_sfx);

        $id_tab = Tab::getIdFromClassName($tab_class);
        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            $tab->delete();
            return true;
        }
        return false;
    }
	
	/**
     * Creates tables
     */
    protected function createTables()
    {
        if ($this->_installDataSample()) {
            return true;
        }
        $res = 1;
        include_once( dirname(__FILE__).'/install/install.php' );
        return $res;
    }
	
	public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'stfeature_product_review`,
			`'._DB_PREFIX_.'stfeature_product_review_criterion`,
			`'._DB_PREFIX_.'stfeature_product_review_criterion_product`,
			`'._DB_PREFIX_.'stfeature_product_review_criterion_lang`,
			`'._DB_PREFIX_.'stfeature_product_review_criterion_category`,
			`'._DB_PREFIX_.'stfeature_product_review_grade`,
			`'._DB_PREFIX_.'stfeature_product_review_usefulness`,
			`'._DB_PREFIX_.'stfeature_product_review_report`,
			`'._DB_PREFIX_.'stfeature_compare`,
			`'._DB_PREFIX_.'stfeature_compare_product`,
			`'._DB_PREFIX_.'stfeature_wishlist`,
			`'._DB_PREFIX_.'stfeature_wishlist_product`
		');
    }
	
	//DONGND:: create configs
	public function createConfiguration()
    {

		//DONGND:: create configs of ajax cart
		Configuration::updateValue('STFEATURE_ENABLE_AJAXCART', 1);
		Configuration::updateValue('STFEATURE_ENABLE_SELECTATTRIBUTE', 0);
		Configuration::updateValue('STFEATURE_ENABLE_UPDATELABEL', 0);
		Configuration::updateValue('STFEATURE_ENABLE_INPUTQUANTITY', 1);
		
		//DONGND:: create configs of product review
		Configuration::updateValue('STFEATURE_ENABLE_PRODUCT_REVIEWS', 1);
		Configuration::updateValue('STFEATURE_SHOW_PRODUCT_REVIEWS_LISTPRODUCT', 1);
		Configuration::updateValue('STFEATURE_SHOW_NUMBER_PRODUCT_REVIEWS_LISTPRODUCT', 1);
		Configuration::updateValue('STFEATURE_SHOW_ZERO_PRODUCT_REVIEWS_LISTPRODUCT', 1);
		Configuration::updateValue('STFEATURE_PRODUCT_REVIEWS_MINIMAL_TIME', 30);
		Configuration::updateValue('STFEATURE_PRODUCT_REVIEWS_ALLOW_GUESTS', 0);
		Configuration::updateValue('STFEATURE_PRODUCT_REVIEWS_ALLOW_USEFULL_BUTTON', 1);
		Configuration::updateValue('STFEATURE_PRODUCT_REVIEWS_ALLOW_REPORT_BUTTON', 1);
		Configuration::updateValue('STFEATURE_PRODUCT_REVIEWS_MODERATE', 1);
		//DONGND:: create configs of product compare
		Configuration::updateValue('STFEATURE_ENABLE_PRODUCTCOMPARE', 1);
		Configuration::updateValue('STFEATURE_SHOW_PRODUCTCOMPARE_PRODUCTPAGE', 1);	
		Configuration::updateValue('STFEATURE_SHOW_PRODUCTCOMPARE_LISTPRODUCT', 1);
		Configuration::updateValue('STFEATURE_COMPARATOR_MAX_ITEM', 3);
		//DONGND:: create configs of product wishlist
		Configuration::updateValue('STFEATURE_ENABLE_PRODUCTWISHLIST', 1);	
		Configuration::updateValue('STFEATURE_SHOW_PRODUCTWISHLIST_PRODUCTPAGE', 1);	
		Configuration::updateValue('STFEATURE_SHOW_PRODUCTWISHLIST_LISTPRODUCT', 1);
		//DONGND:: create config for default tab
		Configuration::updateValue('STFEATURE_DEFAULT_TAB', '#fieldset_0');
		
        return true;
    }
	
	//DONGND:: delete configs
	public function deleteConfiguration()
    {	
		//DONGND:: delete configs of ajax cart
		Configuration::deleteByName('STFEATURE_ENABLE_AJAXCART');
		Configuration::deleteByName('STFEATURE_ENABLE_SELECTATTRIBUTE');
		Configuration::deleteByName('STFEATURE_ENABLE_UPDATELABEL');
		Configuration::deleteByName('STFEATURE_ENABLE_INPUTQUANTITY');
		//DONGND:: delete configs of product review
		Configuration::deleteByName('STFEATURE_ENABLE_PRODUCT_REVIEWS');
		Configuration::deleteByName('STFEATURE_SHOW_PRODUCT_REVIEWS_LISTPRODUCT');
		Configuration::deleteByName('STFEATURE_SHOW_NUMBER_PRODUCT_REVIEWS_LISTPRODUCT');
		Configuration::deleteByName('STFEATURE_SHOW_ZERO_PRODUCT_REVIEWS_LISTPRODUCT');
        Configuration::deleteByName('STFEATURE_PRODUCT_REVIEWS_MINIMAL_TIME');
		Configuration::deleteByName('STFEATURE_PRODUCT_REVIEWS_ALLOW_GUESTS');	
		Configuration::deleteByName('STFEATURE_PRODUCT_REVIEWS_ALLOW_USEFULL_BUTTON');
		Configuration::deleteByName('STFEATURE_PRODUCT_REVIEWS_ALLOW_REPORT_BUTTON');		
        Configuration::deleteByName('STFEATURE_PRODUCT_REVIEWS_MODERATE');
		//DONGND:: delete configs of product compare
		Configuration::deleteByName('STFEATURE_ENABLE_PRODUCTCOMPARE');
		Configuration::deleteByName('STFEATURE_SHOW_PRODUCTCOMPARE_PRODUCTPAGE');	
		Configuration::deleteByName('STFEATURE_SHOW_PRODUCTCOMPARE_LISTPRODUCT');
		Configuration::deleteByName('STFEATURE_COMPARATOR_MAX_ITEM');
		//DONGND:: delete configs of product wishlist
		Configuration::deleteByName('STFEATURE_ENABLE_PRODUCTWISHLIST');
		Configuration::deleteByName('STFEATURE_SHOW_PRODUCTWISHLIST_PRODUCTPAGE');	
		Configuration::deleteByName('STFEATURE_SHOW_PRODUCTWISHLIST_LISTPRODUCT');
		//DONGND:: delete config for default tab
		Configuration::deleteByName('STFEATURE_DEFAULT_TAB');
        return true;
    }

    private function _installDataSample()
    {
        if (!file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/StDataSample.php')) {
            return false;
        }
        require_once( _PS_MODULE_DIR_.'appagebuilder/libs/StDataSample.php' );

        $sample = new Datasample(1);
        return $sample->processImport($this->name);
    }
	
	public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($template);
    }
	
	public function correctModule()
	{	
		//DONGND:: add config
		if (!Configuration::hasKey('STFEATURE_SHOW_ZERO_PRODUCT_REVIEWS_LISTPRODUCT'))
		{
			Configuration::updateValue('STFEATURE_SHOW_ZERO_PRODUCT_REVIEWS_LISTPRODUCT', 1);
		}
		
		//DONGND:: add config
		if (!Configuration::hasKey('STFEATURE_PRODUCT_REVIEWS_ALLOW_USEFULL_BUTTON'))
		{
			Configuration::updateValue('STFEATURE_PRODUCT_REVIEWS_ALLOW_USEFULL_BUTTON', 1);
		}
		
		//DONGND:: add config
		if (!Configuration::hasKey('STFEATURE_PRODUCT_REVIEWS_ALLOW_REPORT_BUTTON'))
		{
			Configuration::updateValue('STFEATURE_PRODUCT_REVIEWS_ALLOW_REPORT_BUTTON', 1);
		}
		
		//DONGND:: add config
		if (!Configuration::hasKey('STFEATURE_ENABLE_UPDATELABEL'))
		{
			Configuration::updateValue('STFEATURE_ENABLE_UPDATELABEL', 0);
		}
			
	}
	
    /**
     * Run only one when install/change Theme_of_St
     */
    public function hookActionAdminBefore()
    {
        $this->unregisterHook('actionAdminBefore');
		
        # JUST INSTALL MODULE NOT RESTORE DATABASE
        $before = (int)Configuration::get('_ALLOW_LOAD_DATASAMPLE_');
        $now = time();
        if($now - $before <= 10 ){
            if (file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/StDataSample.php')) {
                require_once( _PS_MODULE_DIR_.'appagebuilder/libs/StDataSample.php' );
                $sample = new Datasample();
                $sample->processHook($this->name);
            }
            return;
        }
        
        # INSERT HOOK FROM THEME_DATASAMPLE
        if (!file_exists(_PS_MODULE_DIR_.'appagebuilder/libs/StDataSample.php')) {
            return false;
        }
        require_once( _PS_MODULE_DIR_.'appagebuilder/libs/StDataSample.php' );

        $data_sample_class = 'Datasample';

        $sample = new $data_sample_class(1);
        if($sample->processImport($this->name))
        {
            return true;
        }
        
        # INSERT HOOK FROM MODULE_DATASAMPLE
        $this->registerStHook();
    }
}
 