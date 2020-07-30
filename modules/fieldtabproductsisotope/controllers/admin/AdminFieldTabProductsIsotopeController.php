<?php

class AdminFieldTabProductsIsotopeController extends ModuleAdminController {

    private $_categorySelect = array();
    private $_manufacturersSelect = array();

    public function __construct()
    {
        $this->className = 'fieldTabProductsIsotopeModel';
        $this->table = 'fieldtabproductsisotope';
		  parent::__construct();
        $this->meta_title = $this->l('Field Tab Products Isotope');
        $this->deleted = false;
        $this->explicitSelect = true;
		
        $this->context = Context::getContext();
        $this->lang = true;
        $this->bootstrap = true;

        $this->_defaultOrderBy = 'position';

        if (Shop::isFeatureActive()){
            Shop::addTableAssociation($this->table, array('type' => 'shop'));
        }

        $this->position_identifier = 'id_fieldtabproductsisotope';

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_fieldtabproductsisotope' => array(
                'title' => $this->l('ID'),
                'type' => 'int',
                'width' => 'auto',
                'orderby' => false
            ),
            'title' => array(
                'title' => $this->l('Title'),
                'width' => 'auto',
                'orderby' => false
            ),
            'tab_type' => array(
                'title' => $this->l('Tab Content'),
                'width' => 'auto',
                'orderby' => false
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'width' => 'auto',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'width' => 'auto',
                'filter_key' => 'a!position',
                'position' => 'position'
            ),
            'banner_image' => array(
                'title' => $this->l('Tab banner'),
                'width' => 'auto',
                'orderby' => false
            ),
            'banner_link' => array(
                'title' => $this->l('Tab banner link'),
                'width' => 'auto',
                'orderby' => false
            ),
            'countdown_from' => array(
                'title' => $this->l('Countdown from'),
                'width' => 'auto',
                'orderby' => false
            ),
            'countdown_to' => array(
                'title' => $this->l('Countdown to'),
                'width' => 'auto',
                'orderby' => false
            )
        );

        parent::__construct();

    }

    /* ------------------------------------------------------------- */
    /*  INIT PAGE HEADER TOOLBAR
    /* ------------------------------------------------------------- */
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)){
            $this->page_header_toolbar_btn = array(
                'new' => array(
                    'href' => self::$currentIndex.'&addfieldtabproductsisotope&token='.$this->token,
                    'desc' => $this->l('Add New Tab', null, null, false),
                    'icon' => 'process-icon-new'
                ),
                'options' => array(
                    'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=fieldtabproductsisotope',
                    'desc' => $this->l('Options'),
                    'icon' => 'process-icon-cogs'
                )
            );
        }

        parent::initPageHeaderToolbar();
    }

    /* ------------------------------------------------------------- */
    /*  INCLUDE NECESSARY FILES
    /* ------------------------------------------------------------- */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
        $this->addJqueryPlugin('fieldtabproductsisotope.admin', __PS_BASE_URI__.'modules/fieldtabproductsisotope/views/js/admin/');
        $this->addJqueryPlugin('autocomplete');
        $this->addJqueryUI('ui.datepicker');
        $this->addCSS(__PS_BASE_URI__.'modules/fieldtabproductsisotope/views/css/admin/fieldtabproductsisotope.css');
    }

    /* ------------------------------------------------------------- */
    /*  AJAX PROCESS FOR UPDATING POSITIONS
    /* ------------------------------------------------------------- */
    public function ajaxProcessUpdatePositions()
    {
        $way = (int)(Tools::getValue('way'));
        $id_fieldtabproductsisotope = (int)(Tools::getValue('id'));
        $positions = Tools::getValue($this->table);

        foreach ($positions as $position => $value){
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int)$pos[2] === $id_fieldtabproductsisotope){
                if ($fieldTabProductsIsotope = new FieldTabProductsIsotopeModel((int)$pos[2])){
                    if (isset($position) && $fieldTabProductsIsotope->updatePosition($way, $position)){
                        echo 'ok position '.(int)$position.' for tab '.(int)$pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update tab '.(int)$id_fieldtabproductsisotope.' to position '.(int)$position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This tab ('.(int)$id_fieldtabproductsisotope.') can t be loaded"}';
                }

                break;
            }
        }
    }

    /* ------------------------------------------------------------- */
    /*  RENDER ADD/EDIT FORM
    /* ------------------------------------------------------------- */
    public function renderForm() {

        $obj = $this->loadObject(true);
        $id_default_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $languages = $this->context->language->getLanguages();

        /* Render Form */

        $tab_types = array(
            array(
                'value' => 'featured',
                'name' => $this->l('Featured products')
            ),
            array(
                'value' => 'new',
                'name' => $this->l('New products')
            ),
            array(
                'value' => 'special',
                'name' => $this->l('Special products')
            ),
            array(
                'value' => 'bestseller',
                'name' => $this->l('Bestseller products')
            ),
            array(
                'value' => 'category',
                'name' => $this->l('All products from certain category')
            ),
            array(
                'value' => 'manufacturers',
                'name' => $this->l('All products from certain manufacturers')
            ),
            array(
                'value' => 'custom',
                'name' => $this->l('Custom products')
            )
        );

        // Get Categories
        $root_category = Category::getRootCategory($id_default_lang);
        $this->_getCategories($root_category->id_category, $id_shop);
        
        // Get manufacture
        $this->_getManufacturers();

        // Init Fields form array
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Tab'),
                'icon' => 'icon-cogs'
            ),
            // Inputs
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Tab Title'),
                    'name' => 'title',
                    'desc' => $this->l('Must be less than 250 characters.'),
                    'size' => 50,
                    'required' => true,
                    'lang' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title image'),
                    'name' => 'title_image',
                    'size' => 30,
                ),
                array(
                    'type' => 'select',
                    'name' => 'tab_type',
                    'label' => $this->l('Tab Content'),
                    'required' => false,
                    'lang' => false,
                    'options' => array(
                        'query' => $tab_types,
                        'id' => 'value',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'name' => 'select_category',
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
                    'type' => 'select',
                    'name' => 'select_manufacturers',
                    'label' => $this->l('Select a manufacturers'),
                    'required' => false,
                    'lang' => false,
                    'options' => array(
                        'query' => $this->_manufacturersSelect,
                        'id' => 'value',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Add a product'),
                    'name' => 'product_autocomplete',
                    'size' => 50,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Tab Content'),
                    'name' => 'tab_content',
                    'size' => 50
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Content banner image'),
                    'name' => 'banner_image',
                    'size' => 30,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link banner image'),
                    'name' => 'banner_link',
                    'size' => 30,
                ),
                array(
                        'type' => 'datetime',
                        'label' => $this->l('Countdown from'),
                        'name' => 'countdown_from',
                        'size' => 10,
                ),
                array(
                        'type' => 'datetime',
                        'label' => $this->l('to'),
                        'name' => 'countdown_to',
                        'size' => 10,
                        'desc' => $this->l('Leave it empty, if you don\'t want to show countdown in this tab.'),
                )
            ),
            // Submit Button
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'saveIsotopeTab'
            )
        );

        if (Shop::isFeatureActive()){
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        if (!($obj = $this->loadObject(true)))
            return;

        if ($obj && $obj->tab_type == 'custom'){
            $tab_content_products = array();
            $tab_content = explode(',', $obj->tab_content);

            foreach ($tab_content as $pid) {
                $product = new Product($pid, false, $id_default_lang);
                $tab_content_products[] = array(
                    'id' => $pid,
                    'name' => $product->name,
                    'ref' => $product->reference
                );
            }

            $this->tpl_form_vars['tab_content_products'] = $tab_content_products;
        }

        return parent::renderForm();
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
    /*  GET MANUFACTURERS
    /* ------------------------------------------------------------- */
    private function _getManufacturers()
    {
        $id_lang = $this->context->language->id;

        $manufacturers = Manufacturer::getManufacturers(false, $id_lang);

        foreach ($manufacturers as $manufacturer){
            $this->_manufacturersSelect[] = array(
                'value' => $manufacturer['id_manufacturer'],
                'name' => $manufacturer['name']
            );
        }
    }

}
