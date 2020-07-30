<?php

class AdminFieldstaticfooterController extends ModuleAdminController {
    protected $id_banner;
    public function __construct() {
        $this->table = 'field_staticfooter';
        $this->className = 'Staticfooter';
        $this->identifier = 'id_fieldstaticblock';
	$this->bootstrap = true;
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
		 parent::__construct();
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
        Shop::addTableAssociation($this->table, array('type' => 'shop'));
        $this->context = Context::getContext();

        parent::__construct();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
		if($this->display == 'add' || $this->display == 'edit')
        $this->addJS(__PS_BASE_URI__.'modules/fieldstaticfooter/js/staticblock.js');
    }

    public function renderList() {
         
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            )
        );

        $this->fields_list = array(
            'id_fieldstaticblock' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25,
                'lang' => false
            ),
            'title' => array(
                'title' => $this->l('Title'),
                'width' => 90,
                'lang' => false
            ),
            'identify' => array(
                'title' => $this->l('Identify'),
                'width' => '100',
                'lang' => false
            ),
            'hook_position' => array(
                'title' => $this->l('Hook Position'),
                'width' => '300',
                'lang' => false
            ),
            'active' => array(
                'title' => $this->l('Displayed'),
                'width' => 20,
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => FALSE
            ),
            'fieldorder' => array(
                'title' => $this->l('Order'),
                'width' => '30',
                'lang' => false
            )
        );

//        $this->fields_list['image'] = array(
//            'title' => $this->l('Image'),
//            'width' => 70,
//            "image" => $this->fieldImageSettings["dir"]
//        );
//            
//        $listSlideshows = Staticblock::getSlideshowLists($this->context->language->id);
//        echo "<pre>"; print_r($listSlideshows); die;
        $lists = parent::renderList();
        parent::initToolbar();

        return $lists;
    }
    
  

    public function renderForm() {
        
        $mod = new fieldstaticfooter();
        $listModules = $mod->getListModuleInstalled();
        
        
        $listHookModules = array(
            array('hook_position'=>'displayFooter'),
            array('hook_position'=>'blockFooter1'),
            array('hook_position'=>'blockFooter2'),
            array('hook_position'=>'blockFooter3'),
            array('hook_position'=>'blockFooter4')
        );
        
	      
        
        $listHookFooterModules = array(
            array('hook_position'=>'displayFooter'),
        );
        
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Staticfooter'),
                'image' => '../img/admin/cog.gif'
            ),
            
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title:'),
                    'name' => 'title',
                    'size' => 40,
                    'lang' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Identify:'),
                    'name' => 'identify',
                    'size' => 40,
                    'require' => false
                ),
                array(
                  'type'      => 'switch',                              
                  'label'     => $this->l('Display:'),       
                  'name'      => 'active',                             
                  'required'  => true,                                 
                  'class'     => 't',                                  
                  'is_bool'   => true,                                 
                  'values'    => array(                                
                        array(
                          'id'    => 'active_on',                          
                          'value' => 1,                                    
                          'label' => $this->l('Yes')                   
                        ),
                        array(
                          'id'    => 'active_off',
                          'value' => 0,
                          'label' => $this->l('No')
                        )
                  ),
                ),
               array(
                'type' => 'select',
                'label' => $this->l('Hook Position:'),
                'name' => 'hook_position',
                'required' => true,
                'options' => array(
                    'query' => $listHookModules,
                    'id' => 'hook_position',
                    'name' => 'hook_position'
                ),
             
                'desc' => $this->l('Choose the type of the Hooks <br> With new hooks as blockFooter1, block Footer2 , blockFooter3, blockFooter4 must been  insert at tpl file. For example {hook h = "newHook"}')
            ),
            
           array(
				  'type'      => 'switch',                              
				  'label'     => $this->l('Show/hide Hook'),       
				  'desc'      => $this->l('Show/hide Hook?'),  
				  'name'      => 'showhook',                             
				  'required'  => false,                                 
				  'is_bool'   => true,                                 
				  'values'    => array(                                
						array(
						  'id'    => 'showhook_on',                          
						  'value' => 1,                                    
						  'label' => $this->l('Enabled')                   
						),
						array(
						  'id'    => 'showhook_off',
						  'value' => 0,
						  'label' => $this->l('Disabled')
						)
				  ),
				),
			    array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'autoload_rte' => TRUE,
                    'lang' => true,
                    'required' => TRUE,
                    'rows' => 5,
                    'cols' => 40,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}'
                ),
                
                array(
                    'type' => 'text',
                    'label' => $this->l('Order:'),
                    'name' => 'fieldorder',
                    'size' => 40,
                    'require' => false
                ),
                 array(
                    'type' => 'switch',
                    'label' => $this->l('Insert Module?'),
                    'desc' => $this->l('Insert module?'),
                    'name' => 'insert_module',
                    'required' => true,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'insert_module_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'insert_module_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),
                array(
                'type' => 'select',
                'label' => $this->l('Modules:'),
                'name' => 'name_module',
                'required' => true,
                'options' => array(
                    'query' => $listModules,
                    'id' => 'name',
                    'name' => 'name'
                ),
                    'desc' => $this->l('Choose the type of the Module')
               ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Hook-Modules:'),
                    'name' => 'hook_module',
                    'required' => true,
                    'options' => array(
                        'query' => $listHookFooterModules,
                        'id' => 'hook_position',
                        'name' => 'hook_position'
                    ),
                    'desc' => $this->l('Choose the type of the Hooks')
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association:'),
                'name' => 'checkBoxShopAsso',
            );
        }

        if (!($obj = $this->loadObject(true)))
            return;


        return parent::renderForm();
    }
    
}
