<?php
include_once(_PS_MODULE_DIR_.'fieldslideshow/models/ModelFieldSlideShow.php');
class AdminFieldSlideshowController extends ModuleAdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->table = 'fieldslideshow';
		$this->className = 'ModelFieldSlideShow';
                $this->name ='slideshow';
		$this->lang = true;
		$this->deleted = false;
		$this->colorOnBackground = false;
		parent::__construct();
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
        Shop::addTableAssociation($this->table, array('type' => 'shop'));
		$this->context = Context::getContext();
                
		parent::__construct();
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
                'id_fieldslideshow' => array(
                    'title' => $this->l('ID'),
                    'align' => 'center',
                    'width' => 25
                ),
                  'title1' => array(
                    'title' => $this->l('Title1'),
                    'width' => 90,
                ),
                  'title2' => array(
                    'title' => $this->l('Title2'),
                    'width' => 90,
                ),
                  'link' => array(
                    'title' => $this->l('Link'),
                    'width' => 90,
                ),
                'description' => array(
                    'title' => $this->l('Desscription'),
                    'width' => '300',
                 ),
                'active' => array(
                    'title' => $this->l('Displayed'),
                    'width' => 20,
                    'align' => 'center',
                    'active' => 'status',
                    'type' => 'bool',
                    'orderby' => FALSE
                ),
                'porder' => array(
                    'title' => $this->l('Order'),
                    'width' => 25,
                ),
            );
            
//            $this->fields_list['images'] = array(
//                'title' => $this->l('Image'),
//                'width' => 70,
//                "image" => $this->fieldImageSettings["dir"]
//            );

            $lists = parent::renderList();
            parent::initToolbar();

            return $lists;
        }
    
	/* ------------------------------------------------------------- */
	/*  INIT PAGE HEADER TOOLBAR
	/* ------------------------------------------------------------- */
	public function initPageHeaderToolbar()
	{
	    if (empty($this->display)){
		$this->page_header_toolbar_btn = array(
		    'options' => array(
			'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=fieldslideshow',
			'desc' => $this->l('Options'),
			'icon' => 'process-icon-cogs'
		    )
		);
	    }

	    parent::initPageHeaderToolbar();
	}
	
        public function postProcess()
        {
		$obj = $this->loadObject(true);
                $errors = "";
					$_POST['images']=$obj->images;
		if (isset($_FILES['images']) && isset($_FILES['images']['tmp_name']) && !empty($_FILES['images']['tmp_name'])) {
                        if ($error = ImageManager::validateUpload($_FILES['images'], Tools::convertBytes(ini_get('upload_max_filesize')))) {
                            $errors[] = $error;
                        }
                        else {
                            $imageName = explode('.', $_FILES['images']['name']);
                            $imageExt = $imageName[1];
                            $imageName = $imageName[0];
                            $sliderImageName = strtolower($imageName).'-'.rand(0,1000).'.'.$imageExt;

                            if (!move_uploaded_file($_FILES['images']['tmp_name'], _PS_MODULE_DIR_ . 'fieldslideshow/images/' . $sliderImageName)) {
                                $errors[] = $this->l('File upload error.');
                            }
                            else{
                                $img_old = _PS_MODULE_DIR_ . 'fieldslideshow/images/' . $obj->images;
                                if (file_exists($img_old && $obj->images!=null))
                                    unlink($img_old);
                                move_uploaded_file($_FILES['images']['tmp_name'], _PS_MODULE_DIR_ . 'fieldslideshow/images/' . $sliderImageName);
                                $_POST['images'] = $sliderImageName;
                            }
                        }
                }
        $return = parent::postProcess();
		return $return;
	}
        
        public function renderForm()
        {
            $obj = $this->loadObject(true);
            if (Validate::isLoadedObject($this->object))
                $media_desc = '<br/>
                            <img id="image_desc" style="clear:both;width: 400px; height:auto;" alt="" src="'. __PS_BASE_URI__.'modules/fieldslideshow/images/' . $obj->images .'" />
                            <br/>';
            else {
                $media_desc = '';
            }
            $this->context->smarty->assign('media_desc',$media_desc);
            $this->fields_form = array(
                'tinymce' => true,
                'legend' => array(
                    'title' => $this->l('Slideshow'),
                    'image' => '../img/admin/cog.gif'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title 1:'),
                        'name' => 'title1',
                        'size' => 40,
                                            'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title 2:'),
                        'name' => 'title2',
                        'size' => 40,
                                            'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Text for button:'),
                        'name' => 'btntext',
                        'size' => 40,
                                            'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link for button:'),
                        'name' => 'link',
                        'size' => 40,
                                             'lang' => true,
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Image:'),
                        'id' => 'images',
                        'name' => 'images',
                        'display_image' => true,
                        'image' => $media_desc ? $media_desc: false,
                        'desc' => $this->l('Upload  a banner from your computer.')
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
                        'type' => 'switch',
                        'label' => $this->l('Displayed:'),
                        'name' => 'active',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                                array(
                                    'id' => 'require_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'require_off',
                                    'value' => 0,
                                    'label' => $this->l('no')
                                )
                            )
                        ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Order:'),
                        'name' => 'porder',
                        'size' => 40,
                        'require' => false
                    ),
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right'
                )
            );
            if (Shop::isFeatureActive())
                $this->fields_form['input'][] = array(
                    'type' => 'shop',
                    'label' => $this->l('Shop association:'),
                    'name' => 'checkBoxShopAsso',
            );
            if (!($obj = $this->loadObject(true)))
                return;
            return parent::renderForm();
        }
}
