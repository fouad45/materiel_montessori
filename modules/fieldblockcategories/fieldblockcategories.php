<?php

/* Field Categories Block - 2015 - Fieldthemes - fieldthemes@gmail.com */

if (!defined('_PS_VERSION_'))
    exit;

class FieldBlockCategories extends Module
{
    function __construct()
    {
        $this->name = 'fieldblockcategories';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'fieldthemes';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Fieldblockcategories');
        $this->description = $this->l('Displays a block for showing list categories with thumbnails image.');
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
               && $this->registerHook('blockcategories')
               && $this->_createConfigs()
               && $this->_createTab();
    }

    /* ------------------------------------------------------------- */
    /*  UNINSTALL THE MODULE
    /* ------------------------------------------------------------- */
    public function uninstall()
    {
        return parent::uninstall()
               && $this->_deleteConfigs()
               && $this->_deleteTab();
    }

    /* ------------------------------------------------------------- */
    /*  CREATE CONFIGS
    /* ------------------------------------------------------------- */
     private function _createConfigs()
    {
	    $languages = $this->context->language->getLanguages();

            foreach ($languages as $language){
                $title[$language['id_lang']] = 'hot categories';
                $sub_title[$language['id_lang']] = '';
            }
            $arrayDefault = array(2,3,4,5,7,8,9,10);
            $cateDefault = implode(',',$arrayDefault);
	    $response = Configuration::updateGlobalValue('FIELD_CATEGORIES_CAT', $cateDefault);
            
            $response &= Configuration::updateValue('FIELD_CATEGORIES_TITLE', $title);
            $response &= Configuration::updateValue('FIELD_CATEGORIES_SUB_TITLE', $sub_title);
            $response &= Configuration::updateValue('FIELD_CATEGORIES_COLUMN', 2);

        return $response;
    }

    /* ------------------------------------------------------------- */
    /*  DELETE CONFIGS
    /* ------------------------------------------------------------- */
    private function _deleteConfigs()
    {
	    $response = Configuration::deleteByName('FIELD_CATEGORIES_CAT');
            $response &= Configuration::deleteByName('FIELD_CATEGORIES_TITLE');
            $response &= Configuration::deleteByName('FIELD_CATEGORIES_SUB_TITLE');
            $response &= Configuration::deleteByName('FIELD_CATEGORIES_COLUMN');

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
            $parentTab->module = $this->name;
            $response &= $parentTab->add();
        }
// Check for parent tab2
			$parentTab_2ID = Tab::getIdFromClassName('AdminFieldMenu2');
			if ($parentTab_2ID) {
				$parentTab_2 = new Tab($parentTab_2ID);
			}
			else {
				$parentTab_2 = new Tab();
				$parentTab_2->active = 1;
				$parentTab_2->name = array();
				$parentTab_2->class_name = "AdminFieldMenu2";
				foreach (Language::getLanguages() as $lang) {
					$parentTab_2->name[$lang['id_lang']] = "FieldThemes Configure";
				}
				$parentTab_2->id_parent = $parentTab->id;
				$parentTab_2->module = $this->name;
				$response &= $parentTab_2->add();
			}
			// Created tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "AdminFieldBlockCategories";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang){
            $tab->name[$lang['id_lang']] = "Configure block categories";
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
        $id_tab = Tab::getIdFromClassName('AdminFieldBlockCategories');
        $parentTabID = Tab::getIdFromClassName('AdminFieldMenu');

        $tab = new Tab($id_tab);
        $tab->delete();
// Get the number of tabs inside our parent tab
        // If there is no tabs, remove the parent
		$parentTab_2ID = Tab::getIdFromClassName('AdminFieldMenu2');
		$tabCount_2 = Tab::getNbTabs($parentTab_2ID);
        if ($tabCount_2 == 0) {
            $parentTab_2 = new Tab($parentTab_2ID);
            $parentTab_2->delete();
        }
        // Get the number of tabs inside our parent tab
        // If there is no tabs, remove the parent
        $tabCount = Tab::getNbTabs($parentTabID);
        if ($tabCount == 0){
            $parentTab = new Tab($parentTabID);
            $parentTab->delete();
        }

        return true;
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

    /* ------------------------------------------------------------- */
    /*  GET CONTENT
    /* ------------------------------------------------------------- */
    public function getContent()
    {
	$languages = $this->context->language->getLanguages();
	$output = '';
	$errors = array();
	
        if (Tools::isSubmit('submit'.$this->name)){
	    $title = array();
	    $sub_title = array();

	    foreach ($languages as $language){
		if (Tools::isSubmit('field_title_'.$language['id_lang'])){
		    $title[$language['id_lang']] = Tools::getValue('field_title_'.$language['id_lang']);
		}
		if (Tools::isSubmit('field_sub_title_'.$language['id_lang'])){
		    $sub_title[$language['id_lang']] = Tools::getValue('field_sub_title_'.$language['id_lang']);
		}
	    }
	    if (isset($title) && $title){
		Configuration::updateValue('FIELD_CATEGORIES_TITLE', $title);
	    }
	    if (isset($sub_title) && $sub_title){
		Configuration::updateValue('FIELD_CATEGORIES_SUB_TITLE', $sub_title);
	    }
            if (Tools::isSubmit('field_column')){
		    if (Validate::isInt(Tools::getValue('field_column'))){
			Configuration::updateValue('FIELD_CATEGORIES_COLUMN', Tools::getValue('field_column'));
		    } else {
			$errors[] = $this->l('The number column must be a numeric value!');
		    }
	    }
	    if (Tools::isSubmit('field_block_cat')){
		    Configuration::updateValue('FIELD_CATEGORIES_CAT', implode(',',Tools::getValue('field_block_cat')));
	    }
	    if (isset($errors) && count($errors))
		    $output = $this->displayError(implode('<br />', $errors));
	    else
		    $output = $this->displayConfirmation($this->l('Your settings have been updated.'));
        }

        return $output.$this->_displayForm();
    }

    /* ------------------------------------------------------------- */
    /*  DISPLAY CONFIGURATION FORM
    /* ------------------------------------------------------------- */
    private function _displayForm()
    {
        $id_default_lang = $this->context->language->id;
	$languages = $this->context->language->getLanguages();
	$id_shop = $this->context->shop->id;
	$root_category = Category::getRootCategory($id_default_lang);
	$this->_getCategories($root_category->id_category, $id_shop);

        $fields_form = array(
            'fieldblockcategories-general' => array(
                'form' => array(
                    'legend' => array(
			    'title' => $this->l('Field - Block Categories'),
			    'icon' => 'icon-cogs'
                    ),
                    'input' => array(
			    array(
				    'type' => 'text',
				    'name' => 'field_title',
				    'label' => $this->l('Title'),
				    'desc' => $this->l('This title will appear just before the categories block, leave it empty to hide it completely'),
				    'required' => false,
				    'lang' => true,
			    ),
			    array(
				    'type' => 'text',
				    'name' => 'field_sub_title',
				    'label' => $this->l('Sub title'),
				    'desc' => $this->l('This sub title will appear just before the title of categories block, leave it empty to hide it completely'),
				    'required' => false,
				    'lang' => true,
			    ),
			    array(
				    'type' => 'hidden',
				    'label' => $this->l('Number column'),
				    'name' => 'field_column',
				    'class' => 'fixed-width-xs',
				    'desc' => $this->l('Set the number column that you would like to display on homepage (default: 3).'),
			    ),
			    array(
				'type' => 'select',
				'name' => 'field_block_cat[]',
				'label' => $this->l('Select a category'),
				'required' => false,
				'multiple' => true,
				'size' => 10,
				'class' => 'fixed-width-xxl',
				'lang' => false,
				'options' => array(
				    'query' => $this->_categorySelect,
				    'id' => 'value',
				    'name' => 'name'
				)
			    ),
                    ),
                    // Submit Button
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'name' => 'saveCategoriesBlock'
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
	
        foreach($languages as $language){
                    $helper->fields_value['field_title'][$language['id_lang']] = Configuration::get('FIELD_CATEGORIES_TITLE', $language['id_lang']);
                    $helper->fields_value['field_sub_title'][$language['id_lang']] = Configuration::get('FIELD_CATEGORIES_SUB_TITLE', $language['id_lang']);
	}
	$helper->fields_value['field_column'] = Configuration::get('FIELD_CATEGORIES_COLUMN');
	$helper->fields_value['field_block_cat[]'] = explode(',' ,Configuration::get('FIELD_CATEGORIES_CAT'));


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

    private function _prepHook()
    {
	    $id_default_lang = $this->context->language->id;
	    $id_lang =(int) Context::getContext()->language->id;
	    $id_shop = (int) Context::getContext()->shop->id;
	    
	    $catSelected = Configuration::get('FIELD_CATEGORIES_CAT');
	    $cateArray = explode(',', $catSelected); 
	    $arrayCategory = array();
	    foreach($cateArray as $id_category) {
		    $category = new Category((int) $id_category, (int) $id_lang, (int) $id_shop);
		    $thumbnails = '';

			    $files = scandir(_PS_CAT_IMG_DIR_);
			    if (count(preg_grep('/^'.$category->id_category.'-([0-9])?_thumb.jpg/i', $files)) > 0) {
				foreach ($files as $file) {
				    if (preg_match('/^'.$category->id_category.'-([0-9])?_thumb.jpg/i', $file) === 1) {
					$thumbnails = $this->context->link->getMediaLink(_THEME_CAT_DIR_.$file);
				    }
				}
			    }else{
					$thumbnails = $this->context->link->getMediaLink($this->_path . 'images/no_image.jpg');
				}
				$link="";
				if(Tools::HtmlEntitiesUTF8($category->getLink()))
				$link=Tools::HtmlEntitiesUTF8($category->getLink());
        $categoryProducts = $category->getProducts((int)Context::getContext()->language->id,1,999999999);
		$sub_categories=$category->getSubCategories($this->context->language->id);
		$arr_subcates=array();
		$i=0;
		foreach($sub_categories as $sub_categorie) {
			
				$category1 = new Category((int) $sub_categorie['id_category'], (int) $id_lang, (int) $id_shop);
				$link1=$category1->getLink();
				$arr_subcates[$i]["name"]=$category1->name;
				$arr_subcates[$i]["link"]=$link1;		
				$i++;
				
		}
		    $arrayCategory[] = array(
			'it_products'=> count($categoryProducts),
			'id_category' => $category->id_category, 
			'name' => $category->name,
			'link' => $link,
			'description_short' =>  Tools::truncateString($category->description, 350),
			'sub_categories' => $arr_subcates,
			'thumbnails' => $thumbnails
		    );
	    }
	    if (!isset($arrayCategory) && !count($arrayCategory)){
		return false;
	    }
//	    var_dump($arrayCategory);
	    
	    $fieldblockcategories = array(
		    'arrayCategory' =>  $arrayCategory,
		    'field_title' => Configuration::get('FIELD_CATEGORIES_TITLE', $id_default_lang),
		    'field_sub_title' => Configuration::get('FIELD_CATEGORIES_SUB_TITLE', $id_default_lang),
		    'field_column' => Configuration::get('FIELD_CATEGORIES_COLUMN')
	    );

	    $this->smarty->assign('fieldblockcategories', $fieldblockcategories);

	    // Load CSS File
	    $this->context->controller->addCSS($this->_path . 'views/css/front/fieldblockcategories.css', 'all');

	    // Load JS File
//	    $this->context->controller->addJqueryPlugin('fieldblockcategories', $this->_path . 'views/js/front/');
    }


    /* ------------------------------------------------------------- */
    /*  hookBlockcategories
    /* ------------------------------------------------------------- */
    public function hookBlockcategories($params)
    {
        $this->_prepHook();

        return $this->display(__FILE__, 'fieldblockcategories.tpl');
    }
    /* ------------------------------------------------------------- */
    /*  HOOK (displayHome)
    /* ------------------------------------------------------------- */
    public function displayHome($params)
    {
        $this->_prepHook();

        return $this->display(__FILE__, 'fieldblockcategories.tpl');
    }
}