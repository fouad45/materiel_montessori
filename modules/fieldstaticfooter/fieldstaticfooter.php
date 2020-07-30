<?php

// Security
if (!defined('_PS_VERSION_'))
    exit;

// Checking compatibility with older PrestaShop and fixing it
if (!defined('_MYSQL_ENGINE_'))
    define('_MYSQL_ENGINE_', 'MyISAM');

// Loading Models
require_once(_PS_MODULE_DIR_ . 'fieldstaticfooter/models/Staticfooter.php');

class fieldstaticfooter extends Module {
    public  $hookAssign   = array();
    public $_staticModel =  "";
    public function __construct() {
        $this->name = 'fieldstaticfooter';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'fieldthemes';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.7');
        $this->hookAssign = array('rightcolumn','leftcolumn','home','top','footer','extraLeft');
        $this->_staticModel = new Staticfooter();
        parent::__construct();

        $this->displayName = $this->l('Field Static Footer');
        $this->description = $this->l('Manager Static blocks');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->admin_tpl_path = _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/';
        
    }

    public function install() {

        // Install SQL
        include(dirname(__FILE__) . '/sql/install.php');
        foreach ($sql as $s)
            if (!Db::getInstance()->execute($s))
                return false;

        // Set some defaults
        return parent::install() &&
                $this->registerHook('footer') &&
		$this->_installHookCustomer()&&
		$this->registerHook('blockFooter1')&&
		$this->registerHook('blockFooter2')&&
		$this->registerHook('blockFooter3')&&
		$this->registerHook('blockFooter4')&&
		$this->registerHook('displayBackOfficeHeader')&&
		$this->_createTab();
    }

    public function uninstall() {

        Configuration::deleteByName('FieldSTATICFOOTER');

        $sql = array();
        include (dirname(__file__) . '/sql/uninstall_sql.php');
        foreach ($sql as $s) {
            if (!Db::getInstance()->Execute($s)) {
                return FALSE;
            }
        }
        // Uninstall Tabs
        $this->_deleteTab();
        // Uninstall Module
        if (!parent::uninstall())
            return false;
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
        $tab->class_name = "AdminFieldstaticfooter";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "Manage Static Footer";
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
        $id_tab = Tab::getIdFromClassName('AdminFieldstaticfooter');
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
    
    public function hookDisplayFooter($params){ 
        global $smarty;
        $this->_blockfooter1 = $this->hookBlockFooter1($params);
        $this->_blockfooter2 = $this->hookBlockFooter2($params);
        $this->_blockfooter3 = $this->hookBlockFooter3($params);
        $this->_blockfooter4 = $this->hookBlockFooter4($params);
        $smarty->assign(array(
            'HOOK_BLOCKFOOTER1' => $this->_blockfooter1,
            'HOOK_BLOCKFOOTER2' => $this->_blockfooter2,
            'HOOK_BLOCKFOOTER3' => $this->_blockfooter3,
            'HOOK_BLOCKFOOTER4' => $this->_blockfooter4
        ));
    }
      
    public function hookFooter($param) { 
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticfooterLists($id_shop,'footer');
        if(count($staticBlocks)<1) return null;
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'block_footer.tpl');
    }
    
    
//     public function hookDisplayBackOfficeHeader($params) {
//	if (method_exists($this->context->controller, 'addJquery'))
//	 {        
//	  $this->context->controller->addJquery();
//	  $this->context->controller->addJS(($this->_path).'js/staticblock.js');
//	 }
//    }	
    /* define some hook customer */
	public function hookBlockFooter1($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticfooterLists($id_shop,'blockFooter1');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'block_footer.tpl');
    }
    
	public function hookBlockFooter2($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticfooterLists($id_shop,'blockFooter2');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'block_footer.tpl');
    }
    
	public function hookBlockFooter3($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticfooterLists($id_shop,'blockFooter3');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'block_footer.tpl');
    }
    
    public function hookBlockFooter4($param) {
        $id_shop = (int)Context::getContext()->shop->id;
        $staticBlocks = $this->_staticModel->getStaticfooterLists($id_shop,'blockFooter4');
        if(count($staticBlocks)<1) return null;
        //if(is_array($staticBlocks))
        $this->smarty->assign(array(
            'staticblocks' => $staticBlocks,
        ));
       return $this->display(__FILE__, 'block_footer.tpl');
    }
    
    
    public function getModulById($id_module) {
        return Db::getInstance()->getRow('
            SELECT m.*
            FROM `' . _DB_PREFIX_ . 'module` m
            JOIN `' . _DB_PREFIX_ . 'module_shop` ms ON (m.`id_module` = ms.`id_module` AND ms.`id_shop` = ' . (int) ($this->context->shop->id) . ')
            WHERE m.`id_module` = ' . $id_module);
    }

    public function getHooksByModuleId($id_module) {
        $module = self::getModulById($id_module);
	$module_name = '';
        if (isset($module['name']) && $module['name'] != '') {
            $module_name = $module['name'];
        }
        $moduleInstance = Module::getInstanceByName($module_name);
        $hooks = array();
        if ($this->hookAssign)
            foreach ($this->hookAssign as $hook) {
                if (_PS_VERSION_ < "1.5") {
                    if (is_callable(array($moduleInstance, 'hook' . $hook))) {
                        $hooks[] = $hook;
                    }
                } else {
                    $retro_hook_name = Hook::getRetroHookName($hook);
                    if (is_callable(array($moduleInstance, 'hook' . $hook)) || is_callable(array($moduleInstance, 'hook' . $retro_hook_name))) {
                        $hooks[] = $retro_hook_name;
                    }
                }
            }
		if(!$hooks){
			$id_hook_17=$result = Db::getInstance()->ExecuteS('
				SELECT `id_hook`
				FROM `' . _DB_PREFIX_ . 'hook_module` 
				WHERE `id_module` = '.$id_module);
				foreach($id_hook_17 as $id_hook_17_){
					$id_hook_17_array[]=$id_hook_17_['id_hook'];
				}
			$results=Db::getInstance()->ExecuteS('
				SELECT `id_hook`, `name`
				FROM `' . _DB_PREFIX_ . 'hook` 
				WHERE `id_hook` IN (\'' . implode("','", $id_hook_17_array) . '\')');
			
		}else{
        	$results = self::getHookByArrName($hooks);
		}
        return $results;
    }

    public static function getHookByArrName($arrName) {
        $result = Db::getInstance()->ExecuteS('
		SELECT `id_hook`, `name`
		FROM `' . _DB_PREFIX_ . 'hook` 
		WHERE `name` IN (\'' . implode("','", $arrName) . '\')');
        return $result;
    }
  //$hooks = $this->getHooksByModuleId(10);
    public function getListModuleInstalled() {
        $mod = new fieldstaticfooter();
        $modules = $mod->getModulesInstalled(0);
        $arrayModule = array();
        foreach($modules as $key => $module) {
            if($module['active']==1) {
                $arrayModule[0] = array('id_module'=>0, 'name'=>'Chose Module');
                $arrayModule[$key] = $module;
            }
        }
        if ($arrayModule)
            return $arrayModule;
        return array();
    }
	
	private function _installHookCustomer(){
		$hooksfield = array(
				'blockFooter1',
				'blockFooter2',
				'blockFooter3',
                                'blockFooter4'
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