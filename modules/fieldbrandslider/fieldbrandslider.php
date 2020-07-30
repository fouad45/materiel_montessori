<?php
if (!defined('_PS_VERSION_'))
    exit;

class Fieldbrandslider extends Module
{
    private $_output = '';

    function __construct()
    {
        $this->name = 'fieldbrandslider';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'fieldthemes';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Field brands slider');
        $this->description = $this->l('Display manufacturer slider');
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
                && $this->_installHookCustomer()
                && $this->registerHook('fieldBrandSlider')
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
               &&  $this->unregisterHook('fieldBrandSlider')
                && $this->unregisterHook('displayHeader')
               &&  $this->_deleteConfigs()
               &&  $this->_deleteTab();
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

        foreach ($languages as $language){
            $title[$language['id_lang']] = 'top brands';
        }
        $response = Configuration::updateValue($this->name . '_TITLE', $title);
        $response &= Configuration::updateValue($this->name . '_MAXITEM', 6);
        $response &= Configuration::updateValue($this->name . '_MINITEM', 4);
        $response &= Configuration::updateValue($this->name . '_AUTOSCROLL', 1);
        $response &= Configuration::updateValue($this->name . '_AUTOSCROLLDELAY', 5000);
        $response &= Configuration::updateValue($this->name . '_PAUSEONHOVER', 0);
        $response &= Configuration::updateValue($this->name . '_PAGINATION', 0);
        $response &= Configuration::updateValue($this->name . '_NAVIGATION', 1);
        $response &= Configuration::updateValue($this->name . '_MANTITLE', 0);

        return $response;
    }

    /* ------------------------------------------------------------- */
    /*  DELETE CONFIGS
    /* ------------------------------------------------------------- */
    private function _deleteConfigs()
    {
        $response = Configuration::deleteByName($this->name . '_TITLE');
        $response &= Configuration::deleteByName($this->name . '_MAXITEM');
        $response &= Configuration::deleteByName($this->name . '_MINITEM');
        $response &= Configuration::deleteByName($this->name . '_AUTOSCROLL');
        $response &= Configuration::deleteByName($this->name . '_PAGINATION');
        $response &= Configuration::deleteByName($this->name . '_NAVIGATION');
        $response &= Configuration::deleteByName($this->name . '_AUTOSCROLLDELAY');
        $response &= Configuration::deleteByName($this->name . '_PAUSEONHOVER');
        $response &= Configuration::deleteByName($this->name . '_MANTITLE');

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
				$parentTab_2->id_parent = $parentTab_2->id;
				$parentTab_2->module = '';
				$response &= $parentTab_2->add();
			}
			// Created tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "AdminFieldBrandSlider";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang){
            $tab->name[$lang['id_lang']] = "Configuge brands";
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
        $id_tab = Tab::getIdFromClassName('AdminFieldBrandSlider');
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
        $languages = $this->context->language->getLanguages();
        $errors = array();

        if (Tools::isSubmit('submit'.$this->name)){

            $title = array();

            foreach ($languages as $language){
                if (Tools::isSubmit('fieldbrand_title_'.$language['id_lang'])){
                    $title[$language['id_lang']] = Tools::getValue('fieldbrand_title_'.$language['id_lang']);
                }
            }
            if (isset($title) && $title){
                Configuration::updateValue($this->name . '_TITLE', $title);
            }

            if (Tools::isSubmit('fieldbrand_autoscroll')){
                Configuration::updateValue($this->name . '_AUTOSCROLL', Tools::getValue('fieldbrand_autoscroll'));
            }

            if (Tools::isSubmit('fieldbrand_autoscrolldelay') || Tools::isSubmit('fieldbrand_maxitem') || Tools::isSubmit('fieldbrand_minitem')){
                if (Validate::isInt(Tools::getValue('fieldbrand_autoscrolldelay')) && Validate::isInt(Tools::getValue('fieldbrand_maxitem')) && Validate::isInt(Tools::getValue('fieldbrand_minitem'))){
                    Configuration::updateValue($this->name . '_AUTOSCROLLDELAY', Tools::getValue('fieldbrand_autoscrolldelay'));
                    Configuration::updateValue($this->name . '_MAXITEM', Tools::getValue('fieldbrand_maxitem'));
                    Configuration::updateValue($this->name . '_MINITEM', Tools::getValue('fieldbrand_minitem'));
                } else {
                    $errors[] = $this->l('value must be a numeric value!');
                }
            }

            if (Tools::isSubmit('fieldbrand_pauseonhover')){
                Configuration::updateValue($this->name . '_PAUSEONHOVER', Tools::getValue('fieldbrand_pauseonhover'));
            }
            if (Tools::isSubmit('fieldbrand_pagination')){
                Configuration::updateValue($this->name . '_PAGINATION', Tools::getValue('fieldbrand_pagination'));
            }
            if (Tools::isSubmit('fieldbrand_navigation')){
                Configuration::updateValue($this->name . '_NAVIGATION', Tools::getValue('fieldbrand_navigation'));
            }
            if (Tools::isSubmit('fieldbrand_mantitle')){
                Configuration::updateValue($this->name . '_MANTITLE', Tools::getValue('fieldbrand_mantitle'));
            }

            // Prepare the output
            if (isset($errors) && count($errors)){
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
        $id_default_lang = $this->context->language->id;
        $languages = $this->context->language->getLanguages();

        $fields_form = array(
            'fieldbrandslider-general' => array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Manufacturer Carousel Options'),
                        'icon' => 'icon-cogs'
                    ),
                    'input' => array(
                        array(
                            'type' => 'text',
                            'name' => 'fieldbrand_title',
                            'label' => $this->l('Title'),
                            'desc' => $this->l('This title will appear just before the manufacturer carousel, leave it empty to hide it completely'),
                            'required' => false,
                            'lang' => true,
                        ),
                        array(
                            'type' => 'text',
                            'name' => 'fieldbrand_maxitem',
                            'label' => $this->l('Max item'),
                            'desc' => $this->l('The item number is showing on desstop screen'),
                            'suffix' => 'item',
                            'class' => 'fixed-width-xs',
                            'required' => false,
                            'lang' => false,
                        ),
                        array(
                            'type' => 'text',
                            'name' => 'fieldbrand_minitem',
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
                            'desc' => $this->l('Scroll the manufacturers automatically'),
                            'name' => 'fieldbrand_autoscroll',
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
                            'name' => 'fieldbrand_autoscrolldelay',
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
                            'name' => 'fieldbrand_pauseonhover',
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
                            'name' => 'fieldbrand_pagination',
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
                            'name' => 'fieldbrand_navigation',
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
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Show manufacturers title'),
                            'name' => 'fieldbrand_mantitle',
                            'required' => false,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'mantitle_on',
                                    'value' => 1,
                                    'label' => $this->l('On')
                                ),
                                array(
                                    'id' => 'mantitle_off',
                                    'value' => 0,
                                    'label' => $this->l('Off')
                                )
                            )
                        )
                    ),
                    // Submit Button
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'name' => 'saveFrontpageBlocksOptions'
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

        foreach($languages as $language){
            $helper->languages[] = array(
                'id_lang' => $language['id_lang'],
                'iso_code' => $language['iso_code'],
                'name' => $language['name'],
                'is_default' => ($id_default_lang == $language['id_lang'] ? 1 : 0)
            );
        }

        // Load current values
        $helper->fields_value['fieldbrand_maxitem'] = Configuration::get($this->name . '_MAXITEM');
        $helper->fields_value['fieldbrand_minitem'] = Configuration::get($this->name . '_MINITEM');
        $helper->fields_value['fieldbrand_autoscroll'] = Configuration::get($this->name . '_AUTOSCROLL');
        $helper->fields_value['fieldbrand_autoscrolldelay'] = Configuration::get($this->name . '_AUTOSCROLLDELAY');
        $helper->fields_value['fieldbrand_pauseonhover'] = Configuration::get($this->name . '_PAUSEONHOVER');
        $helper->fields_value['fieldbrand_pagination'] = Configuration::get($this->name . '_PAGINATION');
        $helper->fields_value['fieldbrand_navigation'] = Configuration::get($this->name . '_NAVIGATION');
        $helper->fields_value['fieldbrand_mantitle'] = Configuration::get($this->name . '_MANTITLE');

        foreach($languages as $language){
            $helper->fields_value['fieldbrand_title'][$language['id_lang']] = Configuration::get($this->name . '_TITLE', $language['id_lang']);
        }

        return $helper->generateForm($fields_form);
    }


    /* ------------------------------------------------------------- */
    /*
    /*  FRONT OFFICE RELATED STUFF
    /*
    /* ------------------------------------------------------------- */

    /* ------------------------------------------------------------- */
    /*  PREPARE FOR HOOK
    /* ------------------------------------------------------------- */

    private function _prepHook($params)
    {
        $id_default_lang = $this->context->language->id;
        $manufacturers = Manufacturer::getManufacturers(false, $id_default_lang);

        $fieldbrandslider = array(
            'manufacturers' => $manufacturers,
            'mainTitle' => Configuration::get($this->name . '_TITLE', $id_default_lang),
            'maxitem' => Configuration::get($this->name . '_MAXITEM'),
            'minitem' => Configuration::get($this->name . '_MINITEM'),
            'autoScroll' => Configuration::get($this->name . '_AUTOSCROLL'),
            'autoScrollDelay' => Configuration::get($this->name . '_AUTOSCROLLDELAY'),
            'pauseOnHover' => Configuration::get($this->name . '_PAUSEONHOVER'),
            'pagination' => Configuration::get($this->name . '_PAGINATION'),
            'navigation' => Configuration::get($this->name . '_NAVIGATION'),
            'mantitle' => Configuration::get($this->name . '_MANTITLE')
        );

        $this->smarty->assign(array(
            'fieldbrandslider'=> $fieldbrandslider,
             'manu_dir'=> _THEME_MANU_DIR_
        ));
    }

    /* ------------------------------------------------------------- */
    /*  HOOK (displayHeader)
    /* ------------------------------------------------------------- */
    
    public function hookDisplayHeader($params)
    {
	global $smarty;
	/* ------------------------------------------------------------- */
	if(Configuration::get($this->name . '_MAXITEM'))
	{Media::addJsDef(array('fieldbs_maxitem' => Configuration::get($this->name . '_MAXITEM')));}
	else
	{Media::addJsDef(array('fieldbs_maxitem' => 6));}
	
	if(Configuration::get($this->name . '_MINITEM'))
	{Media::addJsDef(array('fieldbs_minitem' => Configuration::get($this->name . '_MINITEM')));}
	else
	{Media::addJsDef(array('fieldbs_minitem' => 2));}
	
	if(Configuration::get($this->name . '_AUTOSCROLL'))
	{Media::addJsDef(array('fieldbs_autoscroll' => true));}
	else
	{Media::addJsDef(array('fieldbs_autoscroll' => false));}
	
	if(Configuration::get($this->name . '_PAUSEONHOVER'))
	{Media::addJsDef(array('fieldbs_pauseonhover' => true));}
	else
	{Media::addJsDef(array('fieldbs_pauseonhover' => false));}
	
	if(Configuration::get($this->name . '_PAGINATION'))
	{Media::addJsDef(array('fieldbs_pagination' => true));}
	else
	{Media::addJsDef(array('fieldbs_pagination' => false));}
	
	if(Configuration::get($this->name . '_NAVIGATION'))
	{Media::addJsDef(array('fieldbs_navigation' => true));}
	else
	{Media::addJsDef(array('fieldbs_navigation' => false));}
	/* ------------------------------------------------------------- */
	$this->context->controller->addJS($this->_path . 'views/js/hook/jquery.brandsliderowlcarousel.js');
	$this->_hookFieldBrands = $this->hookFieldBrandSlider($params);
	$smarty->assign('HOOK_FIELDBRANDSLIDER', $this->_hookFieldBrands);
    }
    
    public function hookFieldBrandSlider($params)
    {
        $this->_prepHook($params);

        return $this->display(__FILE__, 'fieldbrandslider.tpl');
    }
    
    private function _installHookCustomer(){
		$hooksfield = array(
				'fieldBrandSlider'
			); 
		foreach( $hooksfield as $hook ){
			if( Hook::getIdByName($hook) ){
				
			} else {
				$new_hook = new Hook();
				$new_hook->name = pSQL($hook);
				$new_hook->title = pSQL($hook);
				$new_hook->add();
				$id_hook = $new_hook->id;
			}
		}
		return true;
	}

}