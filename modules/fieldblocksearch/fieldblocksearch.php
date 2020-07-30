<?php


if (!defined('_PS_VERSION_'))
	exit;

class FieldBlockSearch extends Module
{
	private $_prefix;
    private $_fields_form = array();
	public function __construct()
	{
		$this->name = 'fieldblocksearch';
		$this->tab = 'search_filter';
		$this->version = "2.0";
		$this->author = 'fieldthemes';
		$this->need_instance = 0;
		$this->_prefix = 'fieldsearch';
		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Field Quick search block');
		$this->description = $this->l('Adds a quick search field to your website.');
	}

	public function install()
	{
		$this->checkOwnerHooks();
                $this->context->controller->getLanguages();
                $tags = array();
                foreach ($this->context->controller->_languages as $language){
                    $tags[(int)$language['id_lang']] = 'Dress';
                }
		if (!parent::install() || !$this->_createTab() || !$this->registerHook('top') || !$this->registerHook('header') || !$this->registerHook('displayMobileTopSiteMap') || !Configuration::updateValue('FIELDSEARCH_SHOW_CAT', 1) || !Configuration::updateValue('FIELDSEARCH_SHOW_TAGS', 0) || !Configuration::updateValue('FIELDSEARCH_TAGS', $tags))
			return false;
		return true;
	}
        
        public function uninstall() {
            // Uninstall Module
            if (!parent::uninstall()
                || !$this->_deleteTab()
		|| !Configuration::deleteByName('FIELDSEARCH_SHOW_CAT')
		|| !Configuration::deleteByName('FIELDSEARCH_SHOW_TAGS')
		|| !Configuration::deleteByName('FIELDSEARCH_TAGS')
		)
                return false;
            return true;
        }
        
		

		
	private function checkOwnerHooks()
    {   
        $hookspos = array(
            'displayTop',
            'displayHeaderRight',
            'displaySlideshow',
            'topNavigation',
			'displayMainmenu',
            'displayPromoteTop',
            'displayRightColumn',
            'displayLeftColumn',
            'displayHome',
            'displayFooter',
            'displayBottom',
            'displayContentBottom',
            'displayFootNav',
            'displayFooterTop',
            'displayFooterBottom'

        );
        
        foreach( $hookspos as $hook ){
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
        $tab->class_name = "AdminFieldBlockSearch";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "Quick search block";
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
        $id_tab = Tab::getIdFromClassName('AdminFieldBlockSearch');
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

    public function getContent() {
    	$output = '<h2>' . $this->displayName . '</h2>';
    	if ( Tools::isSubmit('submitFieldBlockSearch') ) {

            $this->makeFormConfig();
            $this->batchUpdateConfigs();
            $output .= $this->displayConfirmation($this->l('Settings updated successfully.'));

        }
        $this->context->controller->addJqueryPlugin('tagify');
        return $output . $this->renderForm();
    }    
	
	public function makeFormConfig() {
    	if( $this->_fields_form ){
            return ;
        }
    	$fields_form = array(
            'form' => array(
	            'legend' => array(
	                'title' => $this->l('Settings'),
	                'icon' => 'icon-cogs'
	            ),

	            'input' => array(
	              	array(
		                'type' => 'switch',
		                'label' => $this->l('Display categories'),
		                'name' => $this->renderName( 'show_cat' ),
		                'desc' => $this->l('Show list categories to filter.'),
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
		                'default' => '0'
	              	),

	              	array(
		                'type' => 'switch',
		                'label' => $this->l('Show tags'),
		                'name' => $this->renderName( 'show_tags' ),
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
		                'default' => '0'
	              	),
                    
	              	array(
						'type' => 'tags',
						'label' => $this->l('Tags'),
						'name' => $this->renderName( 'tags' ),
						'lang' => true,
						'hint' => array(
							$this->l('Invalid characters:').' &lt;&gt;;=#{}',
							$this->l('To add "tags" click in the field, write something, and then press "Enter."')
						),
						'default' => ''
					),

	            ),

	            'submit' => array(
	              'title' => $this->l('Save'),
	              'class' => 'btn btn-default')
	        ),
		);

		$this->_fields_form[] = $fields_form;
		
    }

    public function renderForm() {
    	$this->makeFormConfig();

    	$helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFieldBlockSearch';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm( ($this->_fields_form) );

    }

    public function getConfigFieldsValues( $data = null ) {
        $fields_values = array();
        foreach ( $this->_fields_form as $k => $f ) {
            foreach ( $f['form']['input'] as $i => $input ) {
                if( isset($input['lang']) ) {
                    foreach ( $this->languages() as $lang ) {
                        $values = Tools::getValue( $input['name'].'_'.$lang['id_lang'], ( Configuration::hasKey($input['name'], $lang['id_lang']) ? Configuration::get($input['name'], $lang['id_lang']) : $input['default'] ) );
                        $fields_values[$input['name']][$lang['id_lang']] = $values;
                    }
                } else {
                    $values = Tools::getValue( $input['name'], ( Configuration::hasKey($input['name']) ? Configuration::get($input['name']) : $input['default'] ) );
                    $fields_values[$input['name']] = $values;
                }
            }
        }
        return $fields_values;
    }

    public function batchUpdateConfigs() {
        foreach ( $this->_fields_form as $k => $f ) {
            foreach ( $f['form']['input'] as $i => $input ) {
                if( isset($input['lang']) ) {
                    $data = array();
                    foreach ( $this->languages() as $lang ) {
                        $val = Tools::getValue( $input['name'].'_'.$lang['id_lang'], $input['default'] );
                        $data[$lang['id_lang']] = $val;
                    }
                    Configuration::updateValue( trim($input['name']), $data );
                }else { 
                    $val = Tools::getValue( $input['name'], $input['default'] );
                    Configuration::updateValue( $input['name'], $val );
                }
            }
        }
    }

    public function deleteConfigs() {

        foreach ( $this->_fields_form as $k => $f ) {
            foreach ( $f['form']['input'] as $i => $input ) {
                if( isset($input['lang']) ) {
                    foreach ( $this->languages() as $lang ) {
                        Configuration::deleteByName( $input['name'].'_'.$lang['id_lang'] );
                    }
                }else {
                    Configuration::deleteByName( $input['name'] );
                }
            }
        }

    }

    public function getConfigValue( $key, $id_lang = null , $value=null ){
      return( Configuration::hasKey( $this->renderName($key), $id_lang )?Configuration::get($this->renderName($key), $id_lang) : $value );
    }

    public function renderName($name){
        return strtoupper($this->_prefix.'_'.$name);
    }

    public function languages(){
        return Language::getLanguages(false);
    }

	public function hookdisplayMobileTopSiteMap($params)
	{
		$this->smarty->assign(array('hook_mobile' => true, 'instantsearch' => false));
		$params['hook_mobile'] = true;
		return $this->hookTop($params);
	}
	
	public function hookHeader($params)
	{
		$this->context->controller->addJS($this->_path.'js/fieldblocksearch.js');
		$this->context->controller->addCSS(($this->_path).'fieldblocksearch.css', 'all');
        Media::addJsDef(array('search_url' => $this->context->link->getPageLink('search', Tools::usingSecureMode())));
		
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookRightColumn($params)
	{
		if (Tools::getValue('search_query') || !$this->isCached('fieldblocksearch.tpl', $this->getCacheId()))
		{
			$this->calculHookCommon($params);
			$this->smarty->assign(array(
				'fieldblocksearch_type' => 'block',
				'search_query' => (string)Tools::getValue('search_query')
				)
			);
		}
		Media::addJsDef(array('fieldblocksearch_type' => 'block'));
		return $this->display(__FILE__, 'fieldblocksearch.tpl', Tools::getValue('search_query') ? null : $this->getCacheId());
	}

	public function hookTop($params) {

            $this->calculHookCommon($params);
            $this->smarty->assign(array(
                'fieldblocksearch_type' => 'top',
                'search_query' => (string) Tools::getValue('search_query'),
                'FIELD_SHOWCAT' => Configuration::get('FIELDSEARCH_SHOW_CAT')
                    )
            );

        Media::addJsDef(array('fieldblocksearch_type' => 'top'));
        return $this->display(__FILE__, 'fieldblocksearch-top.tpl');
    }
	
	public function hookDisplayNav($params)
	{
		return $this->hookTop($params);
	}
	public function hookDisplayMainmenu($params)
	{
		return $this->hookTop($params);
	}

	private function calculHookCommon($params)
	{
		if (file_exists(_PS_THEME_DIR_.'modules/fieldblocksearch/fieldblocksearch-instantsearch.tpl'))
			$this->smarty->assign('instance_tpl_path', _PS_THEME_DIR_.'modules/fieldblocksearch/fieldblocksearch-instantsearch.tpl');
		else
			$this->smarty->assign('instance_tpl_path', _PS_MODULE_DIR_.'fieldblocksearch/fieldblocksearch-instantsearch.tpl');
		$tags = '';
                $arrtags = array();
		if($this->getConfigValue('show_tags', null, 1)){
			$tags = $this->getConfigValue('tags', $this->context->language->id, '');
			if($tags){
				$tags = explode(',', $tags);
				foreach ($tags as $key => $value) {
					$request = array(
						'orderby' => 'position',
						'orderway' => 'desc',
						'search_query' => urlencode($value)
						);
					$arrtags[] = array('tag_name' => $value, 'link' => $this->context->link->getPageLink('search', null, null, $request));
				}
			}
		}
		$this->smarty->assign(array(
			'ENT_QUOTES' =>		ENT_QUOTES,
			'search_ssl' =>		Tools::usingSecureMode(),
			'instantsearch' =>	Configuration::get('PS_INSTANT_SEARCH'),
            'form_link' => $this->context->link->getModuleLink('fieldblocksearch', 'search'),
			'self' =>			dirname(__FILE__),
			'category_html' => $this->getHtmlCategories(),
			'tags' => $arrtags

		));

		return true;
	}
    
    public function getHtmlCategories()
	{
        if(!Configuration::get('FIELDSEARCH_SHOW_CAT'))
        	return '';
        $maxdepth = Configuration::get('BLOCK_CATEG_MAX_DEPTH');
        // Get all groups for this customer and concatenate them as a string: "1,2,3..."
        $groups = implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id));
        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT DISTINCT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
            FROM `'._DB_PREFIX_.'category` c
            '.Shop::addSqlAssociation('category', 'c').'
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
            LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)
            WHERE (c.`active` = 1 OR c.`id_category` = '.(int)Configuration::get('PS_HOME_CATEGORY').')
			AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
            '.((int)($maxdepth) != 0 ? ' AND `level_depth` <= '.(int)($maxdepth) : '').'
            AND cg.`id_group` IN ('.pSQL($groups).')
            ORDER BY `level_depth` ASC, '.(Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'category_shop.`position`').' '.(Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC')))
            return;
        $resultParents = array();
        $resultIds = array();

        foreach ($result as &$row)
        {
            $resultParents[$row['id_parent']][] = &$row;
            $resultIds[$row['id_category']] = &$row;
        }
        //$nbrColumns = Configuration::get('BLOCK_CATEG_NBR_COLUMNS_FOOTER');
        $nbrColumns = (int)Configuration::get('BLOCK_CATEG_NBR_COLUMN_FOOTER');
        if (!$nbrColumns or empty($nbrColumns))
            $nbrColumns = 3;
        $numberColumn = abs(count($result) / $nbrColumns);
        $widthColumn = floor(100 / $nbrColumns);
        $this->smarty->assign('numberColumn', $numberColumn);
        $this->smarty->assign('widthColumn', $widthColumn);

        $blockCategTree = $this->getTree($resultParents, $resultIds, Configuration::get('BLOCK_CATEG_MAX_DEPTH'));
        
        unset($resultParents, $resultIds);

        $this->smarty->assign('fieldblocksearchCategTree', $blockCategTree);
        $this->smarty->assign('current_category', Tools::getValue('id_category'));
        $this->smarty->assign('home_category', new Category(Context::getContext()->shop->getCategory(), (int)Context::getContext()->language->id));

        if (file_exists(_PS_THEME_DIR_.'modules/fieldblocksearch/categories.tpl'))
            $this->smarty->assign('fieldbranche_tpl_path', _PS_THEME_DIR_.'modules/fieldblocksearch/category-tree-branch.tpl');
        else
            $this->smarty->assign('fieldbranche_tpl_path', _PS_MODULE_DIR_.'fieldblocksearch/category-tree-branch.tpl');
		
        $this->smarty->assign('category_filter',(int)Tools::getValue('category_filter'));
		$display = $this->display(__FILE__, 'categories.tpl');
//echo "<pre>".print_r($display,1); die;
		return $display;
	}
    
    public function getTree($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0, $prefix = '')
	{
		if (is_null($id_category))
			$id_category = $this->context->shop->getCategory();
        $prefix .= '--';
		$children = array();
		if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth))
			foreach ($resultParents[$id_category] as $subcat)
				$children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1, $prefix);

		if (!isset($resultIds[$id_category]))
			return false;
		
		$return = array(
			'id' => $id_category,
			'link' => $this->context->link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']),
			'name' =>  $resultIds[$id_category]['name'],
			'desc'=>  $resultIds[$id_category]['description'],
			'children' => $children,
			'prefix' => $prefix
		);

		return $return;
	}
}

