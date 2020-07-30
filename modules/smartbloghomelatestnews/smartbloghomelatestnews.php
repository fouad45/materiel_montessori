<?php
if (!defined('_PS_VERSION_'))
    exit;
 
require_once (_PS_MODULE_DIR_.'smartblog/classes/SmartBlogPost.php');
require_once (_PS_MODULE_DIR_.'smartblog/smartblog.php');
class smartbloghomelatestnews extends Module {
    
     public function __construct(){
        $this->name = 'smartbloghomelatestnews';
        $this->tab = 'front_office_features';
        $this->version = '2.0.1';
        $this->bootstrap = true;
        $this->author = 'SmartDataSoft';
        $this->secure_key = Tools::encrypt($this->name);

        parent::__construct();

        $this->displayName = $this->l('SmartBlog Home Latest');
        $this->description = $this->l('The Most Powerfull Presta shop Blog  Module\'s tag - by smartdatasoft');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
        }
        
     public function install(){
                $langs = Language::getLanguages();
                $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
                 if (!parent::install() 
				 || !$this->registerHook('smartBlogHomePost')
				 || !$this->registerHook('displayHeader')
				 || !$this->registerHook('actionsbdeletepost')
				 || !$this->registerHook('actionsbnewpost')
				 || !$this->registerHook('actionsbupdatepost')
				 || !$this->registerHook('actionsbtogglepost')
				 )
            return false;
                 Configuration::updateGlobalValue('smartshowhomepost',6);
                 return true;
            }
            
     public function uninstall() {
	 $this->DeleteCache();
            if (!parent::uninstall() || !Configuration::deleteByName('smartshowhomepost'))
                 return false;
            return true;
                }
		
    public function hookDisplayHeader($params)
	{
	    global $smarty;
	    $this->_hookSmartBlogHomePost = $this->hookSmartBlogHomePost($params);
	    $smarty->assign('HOOK_SMARTBLOGHOMEPOST', $this->_hookSmartBlogHomePost);
        $this->context->controller->addCSS($this->_path . 'css/style.css');
	}
     public function hookSmartBlogHomePost($params) {
		$this->context->controller->addJS($this->_path.'js/jquery.smartbloghomeowlcarousel.js');
                if(Module::isInstalled('smartblog') != 1){
                        $this->smarty->assign( array(
                                  'smartmodname' => $this->name
                         ));
                        return $this->display(__FILE__, 'views/templates/front/install_required.tpl');
                }
                else
                {
                            if ($this->getCacheId())
                                {
                                    $view_data['posts'] = SmartBlogPost::GetPostLatestHome(Configuration::get('smartshowhomepost')); 
                                    $this->smarty->assign( array(
                                              'view_data'     	 => $view_data['posts']
                                    ));
                                }
                            return $this->display(__FILE__, 'views/templates/front/smartblog_latest_news.tpl', $this->getCacheId());
                }  
            }
            
     public function getContent(){
                $html = '';
                if(Tools::isSubmit('save'.$this->name))
                {
                    Configuration::updateValue('smartshowhomepost', Tools::getvalue('smartshowhomepost'));
                    $html = $this->displayConfirmation($this->l('The settings have been updated successfully.'));
                    $helper = $this->SettingForm();
                    $html .= $helper->generateForm($this->fields_form); 
                    return $html;
                }
                else
                {
                   $helper = $this->SettingForm();
                   $html .= $helper->generateForm($this->fields_form);
                   return $html;
                }
            }
            
     public function SettingForm() {
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->fields_form[0]['form'] = array(
          'legend' => array(
          'title' => $this->l('General Setting'),
            ),
            'input' => array(
                
                array(
                    'type' => 'text',
                    'label' => $this->l('Number of posts to dispay in Lastest News'),
                    'name' => 'smartshowhomepost',
                    'size' => 15,
                    'required' => true
                )                
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        foreach (Language::getLanguages(false) as $lang)
                            $helper->languages[] = array(
                                    'id_lang' => $lang['id_lang'],
                                    'iso_code' => $lang['iso_code'],
                                    'name' => $lang['name'],
                                    'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
                            );
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save'.$this->name.'token=' . Tools::getAdminTokenLite('AdminModules'),
            )
        );
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;       
        $helper->toolbar_scroll = true;    
        $helper->submit_action = 'save'.$this->name;
        
        $helper->fields_value['smartshowhomepost'] = Configuration::get('smartshowhomepost');
        return $helper;
      }
	public function DeleteCache()
            {
				return $this->_clearCache('smartblog_latest_news.tpl', $this->getCacheId());
			}
	public function hookactionsbdeletepost($params)
            {
                 return $this->DeleteCache();
            }
	public function hookactionsbnewpost($params)
            {
                 return $this->DeleteCache();
            }
	public function hookactionsbupdatepost($params)
            {
                return $this->DeleteCache();
            }
	public function hookactionsbtogglepost($params)
            {
                return $this->DeleteCache();
            }
	private function _installHookCustomer(){
	   $hooksfield = array(
			   'smartBlogHomePost'
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