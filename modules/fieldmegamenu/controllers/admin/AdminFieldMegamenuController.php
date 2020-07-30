<?php

class AdminFieldMegamenuController extends ModuleAdminController {


    public function __construct()
    {
        $this->className = 'fieldMegamenuModel';
        $this->table = 'fieldmegamenu';
		 parent::__construct();
        $this->meta_title = $this->l('Field Megamenu');
        $this->deleted = false;
        $this->explicitSelect = true;
        $this->context = Context::getContext();
        $this->lang = true;
        $this->bootstrap = true;

        $this->_defaultOrderBy = 'position';

        if (Shop::isFeatureActive()){
            Shop::addTableAssociation($this->table, array('type' => 'shop'));
        }

        $this->position_identifier = 'id_fieldmegamenu';

        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('delete');


        $this->fields_list = array(
            'id_fieldmegamenu' => array(
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
            )
        );

        parent::__construct();

        $this->fieldmmCategories = array();
        $this->fieldmmCMSPages = '';

    }

    /* ------------------------------------------------------------- */
    /*  INIT PAGE HEADER TOOLBAR
    /* ------------------------------------------------------------- */
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)){
            $this->page_header_toolbar_btn = array(
                'new' => array(
                    'href' => self::$currentIndex.'&addfieldmegamenu&token='.$this->token,
                    'desc' => $this->l('Add New Menu', null, null, false),
                    'icon' => 'process-icon-new'
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

        $this->addCSS(__PS_BASE_URI__.'modules/fieldmegamenu/views/css/admin/fieldmegamenu.css');

        // Only load these if we are in the view display (menu editor)
        if($this->display == 'view')
        {
            $this->addJqueryUI('ui.sortable');

            $this->addJqueryPlugin('mjs.nestedSortable', __PS_BASE_URI__.'modules/fieldmegamenu/views/js/admin/');
            $this->addJqueryPlugin('fieldmegamenu', __PS_BASE_URI__.'modules/fieldmegamenu/views/js/admin/');
            $this->addJqueryPlugin('autocomplete');
            $this->addJS(_PS_JS_DIR_.'tiny_mce/tinymce.min.js');
            $this->addJS( __PS_BASE_URI__.'modules/fieldmegamenu/views/js/admin/tinymce.inc.js');
            $this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
            $this->addJS(_PS_JS_DIR_.'jquery/plugins/jquery.autosize.min.js');

        }
    }

    /* ------------------------------------------------------------- */
    /*  AJAX PROCESS FOR UPDATING POSITIONS
    /* ------------------------------------------------------------- */
    public function ajaxProcessUpdatePositions()
    {
        $way = (int)(Tools::getValue('way'));
        $id_fieldmegamenu = (int)(Tools::getValue('id'));
        $positions = Tools::getValue($this->table);

        foreach ($positions as $position => $value){
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int)$pos[2] === $id_fieldmegamenu){
                if ($fieldMegamenu = new FieldMegamenuModel((int)$pos[2])){
                    if (isset($position) && $fieldMegamenu->updatePosition($way, $position)){
                        echo 'ok position '.(int)$position.' for carousel '.(int)$pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update carousel '.(int)$id_fieldmegamenu.' to position '.(int)$position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This carousel ('.(int)$id_fieldmegamenu.') can t be loaded"}';
                }

                break;
            }
        }
    }

    /* ------------------------------------------------------------- */
    /*  RENDER ADD/EDIT FORM
    /* ------------------------------------------------------------- */
    public function renderForm() {

        // Init Fields form array
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Menu'),
                'icon' => 'icon-cogs'
            ),
            // Inputs
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'desc' => $this->l('Must be less than 250 characters.'),
                    'required' => true,
                    'lang' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'desc' => $this->l('Must be less than 125 characters.'),
                    'required' => false,
                    'lang' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link'),
                    'name' => 'link',
                    'desc' => $this->l('Must be less than 250 characters.'),
                    'required' => false,
                    'lang' => true
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Open in new tab'),
                    'name' => 'open_in_new',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'in_new_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'in_new_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Custom class'),
                    'name' => 'menu_class',
                    'desc' => $this->l('Must be less than 250 characters.'),
                    'required' => false,
                    'lang' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Width Popup class'),
                    'name' => 'width_popup_class',
                    'desc' => $this->l('You can customize the width of the popup. Example: col-md-1, col-md-2, ..., col-md-12. If this box blank, width popup of this item is 100%.'),
                    'required' => false,
                    'lang' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Icon class'),
                    'name' => 'icon_class',
                    'desc' => $this->l('You can use this area to add your iconfont class. Must be less than 250 characters. Ex: "icon-home". You can see http://fortawesome.github.io/Font-Awesome/3.2.1/cheatsheet/ for complete list of available icons. '),
                    'required' => false,
                    'lang' => false
                ),
            ),
            // Submit Button
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'saveMegamenuRoot'
            )
        );

        if (Shop::isFeatureActive()){
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        return parent::renderForm();
    }

    /* ------------------------------------------------------------- */
    /*  RENDER VIEW
    /* ------------------------------------------------------------- */
    public function renderView()
    {
        $languages = $this->context->language->getLanguages(false);
        $id_lang = $this->context->language->id;
        $iso = $this->context->language->iso_code;

        $id_fieldmegamenu = Tools::getValue('id_fieldmegamenu');
        $ajax_url = $this->context->link->getAdminLink('AdminFieldMegamenu');

        $menuItems = $this->getMenuItems($id_fieldmegamenu);
        $this->getCategories();
        $this->getCMSPages();

        $this->tpl_view_vars = array(
            'languages' => $languages,
            'id_default_lang' => $id_lang,
            'fieldmm_id_fieldmegamenu' => $id_fieldmegamenu,
            'fieldmm_ajax_url' => $ajax_url,
            'fieldmmMenuItems' => $menuItems,
            'fieldmmCategories' => $this->fieldmmCategories,
            'fieldmmManufacturers' => $this->getManufacturers(),
            'fieldmmSuppliers' => $this->getSuppliers(),
            'fieldmmCMSPages' => $this->fieldmmCMSPages,
            // Rich Text Editor
            'iso' => file_exists(_PS_CORE_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en',
            'path_css' => _THEME_CSS_DIR_,
            'ad' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_)
            // End - Rich Text Editor
        );

        return parent::renderView();
    }

    /* ------------------------------------------------------------- */
    /*  GET MENU ITEMS
    /* ------------------------------------------------------------- */
    private function getMenuItems($id_fieldmegamenu)
    {
        // Check if there is any menu items
        if (FieldMegamenuItemsModel::getMenuItemsCount($id_fieldmegamenu) == 0){
            return false;
        }

        $id_lang = $this->context->language->id;

        $menuTypes = array(
            'Custom Link / Text' => 1,
            'Category' => 2,
            'Product' => 3,
            'Manufacturer' => 4,
            'Supplier' => 5,
            'Cms Page' => 6,
            'Custom Content' => 7,
            'Divider' => 8,
        );

        $items = FieldMegamenuItemsModel::getMenuItems($id_fieldmegamenu);

        foreach ($items as $key => $item)
        {
            $fieldMegamenu = new FieldMegamenuItemsModel($item['id_fieldmegamenuitems']);
            $menuItems['items'][$key] = $fieldMegamenu;
            $menuItems['items'][$key]->menu_type_name = array_search($fieldMegamenu->menu_type, $menuTypes);

            switch ($fieldMegamenu->menu_type)
            {
                case 2:
                    // Category Link
                    $catID = $fieldMegamenu->link[$id_lang];
                    $category = new Category($catID, $id_lang);
                    $menuItems['items'][$key]->item_info = true;
                    $menuItems['items'][$key]->item_info_label = $this->l('Category: ');
                    $menuItems['items'][$key]->item_info_name = $category->name;
                    $menuItems['items'][$key]->item_info_link = $this->context->link->getCategoryLink($category, null, $id_lang);
                    break;

                case 3:
                    // Product Link
                    $productID = $fieldMegamenu->link[$id_lang];
                    $product = new Product($productID, false, $id_lang);
                    $menuItems['items'][$key]->item_info = true;
                    $menuItems['items'][$key]->item_info_label = $this->l('Product: ');
                    $menuItems['items'][$key]->item_info_name = $product->name;
                    $menuItems['items'][$key]->item_info_link = $this->context->link->getProductLink($product, null, $id_lang);
                    break;

                case 4:
                    // Manufacturer Link
                    $manID = $fieldMegamenu->link[$id_lang];
                    $manufacturer = new Manufacturer($manID, $id_lang);
                    $menuItems['items'][$key]->item_info = true;
                    $menuItems['items'][$key]->item_info_label = $this->l('Manufacturer: ');
                    $menuItems['items'][$key]->item_info_name = $manufacturer->name;
                    $menuItems['items'][$key]->item_info_link = $this->context->link->getManufacturerLink($manufacturer, null, $id_lang);
                    break;

                case 5:
                    // Supplier Link
                    $supID = $fieldMegamenu->link[$id_lang];
                    $supplier = new Supplier($supID, $id_lang);
                    $menuItems['items'][$key]->item_info = true;
                    $menuItems['items'][$key]->item_info_label = $this->l('Supplier: ');
                    $menuItems['items'][$key]->item_info_name = $supplier->name;
                    $menuItems['items'][$key]->item_info_link = $this->context->link->getSupplierLink($supplier, null, $id_lang);
                    break;

                case 6:
                    // CMS Page Link
                    $cmsID = $fieldMegamenu->link[$id_lang];
                    $cmsPage = new CMS($cmsID, $id_lang);
                    $menuItems['items'][$key]->item_info = true;
                    $menuItems['items'][$key]->item_info_label = $this->l('CMS Page: ');
                    $menuItems['items'][$key]->item_info_name = $cmsPage->meta_title;
                    $menuItems['items'][$key]->item_info_link = $this->context->link->getCMSLink($cmsPage, null, null, $id_lang);
                    break;
            }

        }

        $menuItems['count'] = FieldMegamenuItemsModel::getMenuItemsCount($id_fieldmegamenu);

        return $menuItems;
    }

    /* ------------------------------------------------------------- */
    /*  GET CATEGORIES
    /* ------------------------------------------------------------- */
    private function getCategories($id_category = 1, $id_shop = false, $recursive = true)
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

            $spacer = str_repeat('&nbsp;', 1 * $depth);
        }

        $this->fieldmmCategories[] = array(
            'value' =>  (int) $category->id,
            'name' => (isset($spacer) ? $spacer : '') . $category->name
        );

        if (isset($children) && count($children)){
            foreach ($children as $child){
                $this->getCategories((int) $child['id_category'], (int) $child['id_shop'], true);
            }
        }
    }

    /* ------------------------------------------------------------- */
    /*  GET MANUFACTURERS
    /* ------------------------------------------------------------- */
    private function getManufacturers()
    {
        $id_lang = $this->context->language->id;
        $manArray = array();

        $manufacturers = Manufacturer::getManufacturers(false, $id_lang);

        foreach ($manufacturers as $manufacturer){
            $manArray[] = array(
                'value' => $manufacturer['id_manufacturer'],
                'name' => $manufacturer['name']
            );
        }

        return $manArray;
    }

    /* ------------------------------------------------------------- */
    /*  GET SUPPLIERS
    /* ------------------------------------------------------------- */
    private function getSuppliers()
    {
        $id_lang = $this->context->language->id;
        $supArray = array();

        $suppliers = Supplier::getSuppliers(false, $id_lang);

        foreach ($suppliers as $supplier){
            $supArray[] = array(
                'value' => $supplier['id_supplier'],
                'name' => $supplier['name']
            );
        }

        return $supArray;
    }

    /* ------------------------------------------------------------- */
    /*  GET CMS PAGES
    /* ------------------------------------------------------------- */
    private function getCMSPages()
    {
        $id_lang = $this->context->language->id;

        $cmsCategories = CMSCategory::getCategories($id_lang);

        foreach ($cmsCategories as $key => $value){
            foreach ($value as $catId => $info){
                $cmsPages = CMS::getCMSPages($id_lang, $info['infos']['id_cms_category']);
                $this->fieldmmCMSPages .= '<optgroup label="' . $info['infos']['name'] . '">';
                foreach ($cmsPages as $cmsPage){
                    $this->fieldmmCMSPages .= '<option value="' . $cmsPage['id_cms'] . '">' . $cmsPage['meta_title'] . '</option>';
                }
                $this->fieldmmCMSPages .= '</optgroup>';
            }
        }
    }

    /* ------------------------------------------------------------- */
    /*  VIEW - AJAX POST PROCESSES
    /* ------------------------------------------------------------- */

    /*
     * MENU TYPES
     *
     * id : description
     * --   -----------
     *  1 : Custom link
     *  2 : Category link
     *  3 : Product link
     *  4 : Manufacturer link
     *  5 : Supplier link
     *  6 : CMS page link
     *  7 : Custom content
     *  8 : Divider
     *
     */

    /* ------------------------------------------------------------- */
    /*  ADD MENU ITEM
    /* ------------------------------------------------------------- */
    public function ajaxProcessAddMenuItem()
    {
        // Parse the serialized form data
        parse_str(Tools::getValue('formData'), $formData);

        $languages = $this->context->language->getLanguages(false);
        $id_lang = $this->context->language->id;
        $lang_name = $this->context->language->name;

        $id_fieldmegamenu = Tools::getValue('id_fieldmegamenu');
        $menu_type = $formData['menu_type'];

        switch ($menu_type){
            case 1:
                $fieldname = 'customlink';
                break;
            case 2:
                $fieldname = 'categorylink';
                break;
            case 3:
                $fieldname = 'productlink';
                break;
            case 4:
                $fieldname = 'manufacturerlink';
                break;
            case 5:
                $fieldname = 'supplierlink';
                break;
            case 6:
                $fieldname = 'cmspagelink';
                break;
            case 7:
                $fieldname = 'customcontent';
                break;
            case 8:
                $fieldname = 'divider';
                break;
            default:
                break;
        }

        // First, check the default language fields, if they are empty, throw an error
        if ( ($formData['fieldmm_'.$fieldname.'_title_' . $id_lang] == '') ){

            $ajaxResponse['error'] = true;
            $ajaxResponse['success'] = false;
            $ajaxResponse['errorMessage'] = 'Please fill all the required fields at least in ' . $lang_name . '.';

            die(Tools::jsonEncode($ajaxResponse));
        }

        // If at least default language fields are filled, then do the stuff
        $fieldMegamenuItem = new FieldMegamenuItemsModel();
        $fieldMegamenuItem->id_fieldmegamenu = $id_fieldmegamenu;

        // Set nleft & nright
        $nright = FieldMegamenuItemsModel::getMaxRight($id_fieldmegamenu);
        $fieldMegamenuItem->nleft = $nright + 1;
        $fieldMegamenuItem->nright = $nright + 2;

        // Menu type
        $fieldMegamenuItem->menu_type = $menu_type;

        foreach ($languages as $language){

            // Title
            if ($formData['fieldmm_'.$fieldname.'_title_' . $language['id_lang']] == ''){
                $title = $formData['fieldmm_'.$fieldname.'_title_' . $id_lang];
            } else {
                $title = $formData['fieldmm_'.$fieldname.'_title_' . $language['id_lang']];
            }

            // Link
            $link = $formData['fieldmm_'.$fieldname.'_link_' . $language['id_lang']];

            $fieldMegamenuItem->title[$language['id_lang']] = $title;
            $fieldMegamenuItem->link[$language['id_lang']] = $link;
        }

        $response = $fieldMegamenuItem->save();

        $ajaxResponse['error'] = false;
        $ajaxResponse['success'] = true;
        $ajaxResponse['successMessage'] = $response;

        die(Tools::jsonEncode($ajaxResponse));

    }

    /* ------------------------------------------------------------- */
    /*  UPDATE MENU ITEM
    /* ------------------------------------------------------------- */
    public function ajaxProcessUpdateMenuItem()
    {
        // Parse the serialized form data
        parse_str(Tools::getValue('formData'), $formData);

        $languages = $this->context->language->getLanguages(false);
        $id_lang = $this->context->language->id;
        $lang_name = $this->context->language->name;

        $id_fieldmegamenuitem = $formData['id_fieldmegamenuitem'];
        $menu_type = $formData['menu_type'];

        // First, check the default language fields, if they are empty, throw an error
        if ( ($menu_type == 1 && $formData['fieldmm_editmenu_title_' . $id_fieldmegamenuitem . '_' . $id_lang] == '') ){

            $ajaxResponse['error'] = true;
            $ajaxResponse['success'] = false;
            $ajaxResponse['errorMessage'] = 'Please fill all the required fields at least in ' . $lang_name . '.';

            die(Tools::jsonEncode($ajaxResponse));
        }

        // If at least default language fields are filled, then do the stuff
        $fieldMegamenuItem = new FieldMegamenuItemsModel($id_fieldmegamenuitem);

        foreach ($languages as $language){

            // Title - only for Custom Link
            if ($menu_type == 1){
                if ($formData['fieldmm_editmenu_title_' . $id_fieldmegamenuitem . '_' . $language['id_lang']] == ''){
                    $title = $formData['fieldmm_editmenu_title_' . $id_fieldmegamenuitem . '_' . $id_lang];
                } else {
                    $title = $formData['fieldmm_editmenu_title_' . $id_fieldmegamenuitem . '_' . $language['id_lang']];
                }
            }

            // Link - only for Custom Link
            if ($menu_type == 1){
                $link = $formData['fieldmm_editmenu_link_' . $id_fieldmegamenuitem . '_' . $language['id_lang']];
            }
			// Description
			if(isset($formData['fieldmm_editmenu_description_' . $id_fieldmegamenuitem . '_' . $language['id_lang']]))
            $description = $formData['fieldmm_editmenu_description_' . $id_fieldmegamenuitem . '_' . $language['id_lang']];

            // Custom Content
            if (isset($formData['fieldmm_editmenu_customcontent_' . $id_fieldmegamenuitem . '_' . $language['id_lang']])){
                $customContent = $formData['fieldmm_editmenu_customcontent_' . $id_fieldmegamenuitem . '_' . $language['id_lang']];
            }

            // Do the assignments
            if (isset($title)){
                $fieldMegamenuItem->title[$language['id_lang']] = $title;
            }

            if (isset($link)){
                $fieldMegamenuItem->link[$language['id_lang']] = $link;
            }
			if(isset($formData['fieldmm_editmenu_description_' . $id_fieldmegamenuitem . '_' . $language['id_lang']]))
            $fieldMegamenuItem->description[$language['id_lang']] = $description;

            if (isset($customContent)){
                $fieldMegamenuItem->content[$language['id_lang']] = $customContent;
            }
        }

        // Non-multilingual stuff
        // Menu Class
        $fieldMegamenuItem->menu_class = $formData['fieldmm_editmenu_class_' . $id_fieldmegamenuitem];

        // Icon Class
		if(isset($formData['fieldmm_editicon_class_' . $id_fieldmegamenuitem]))
        $fieldMegamenuItem->icon_class = $formData['fieldmm_editicon_class_' . $id_fieldmegamenuitem];

        // Menu Layout
        if ($menu_type == 8){
            $fieldMegamenuItem->menu_layout = 'menucol-1-1';
        } else {
            if (isset($formData['fieldmm_editmenu_layout_' . $id_fieldmegamenuitem])){
                if ($formData['fieldmm_editmenu_layout_' . $id_fieldmegamenuitem] == 'auto'){
                    $fieldMegamenuItem->menu_layout = '';
                } else {
                    $fieldMegamenuItem->menu_layout = $formData['fieldmm_editmenu_layout_' . $id_fieldmegamenuitem];
                }
            }
        }

        // Menu Link Target
        if (isset($formData['fieldmm_editmenu_target_' . $id_fieldmegamenuitem])){
            $fieldMegamenuItem->open_in_new = 1;
        } else {
            $fieldMegamenuItem->open_in_new = 0;
        }

        // Show Image
        if (isset($formData['fieldmm_editmenu_showimage_' . $id_fieldmegamenuitem])){
            $fieldMegamenuItem->show_image = 1;
        } else {
            $fieldMegamenuItem->show_image = 0;
        }

        // SAVE
        $response = $fieldMegamenuItem->save();

        $ajaxResponse['error'] = false;
        $ajaxResponse['success'] = true;
        $ajaxResponse['successMessage'] = $response;

        die(Tools::jsonEncode($ajaxResponse));
    }

    /* ------------------------------------------------------------- */
    /*  DELETE MENU ITEM (RECURSIVE)
    /* ------------------------------------------------------------- */
    public function ajaxProcessDeleteMenuItem()
    {
        $itemIds = Tools::getValue('itemIds');

        foreach ($itemIds as $itemId){
            $fieldMegamenuItem = new FieldMegamenuItemsModel($itemId);
            $fieldMegamenuItem->delete();
        }
    }

    /* ------------------------------------------------------------- */
    /*  SAVE THE NESTED STRUCTURE
    /* ------------------------------------------------------------- */
    public function ajaxProcessSaveSortable()
    {
        $menuArray = Tools::getValue('menuArray');

        foreach ($menuArray as $menu){
            if ($menu['item_id']){
                $fieldMegamenu = New FieldMegamenuItemsModel($menu['item_id']);
                $fieldMegamenu->nleft = $menu['left'];
                $fieldMegamenu->nright = $menu['right'];
                $fieldMegamenu->depth = $menu['depth'];
                $fieldMegamenu->update();
            }
        }
    }

    /* ------------------------------------------------------------- */
    /*  RELOAD NESTED STRUCTURE
    /* ------------------------------------------------------------- */
    public function ajaxProcessReloadSortable()
    {
        $languages = $this->context->language->getLanguages(false);
        $id_lang = $this->context->language->id;

        $id_fieldmegamenu = Tools::getValue('id_fieldmegamenu');
        $ajax_url = $this->context->link->getAdminLink('AdminFieldMegamenu');

        $menuItems = $this->getMenuItems($id_fieldmegamenu);

        $this->context->smarty->assign(
            array(
                'languages' => $languages,
                'id_default_lang' => $id_lang,
                'fieldmm_id_fieldmegamenu' => $id_fieldmegamenu,
                'fieldmm_ajax_url' => $ajax_url,
                'fieldmmMenuItems' => $menuItems
            )
        );

        $renderedSortable = $this->context->smarty->fetch(_PS_MODULE_DIR_.'fieldmegamenu/views/templates/admin/field_megamenu/helpers/view/menu_builder.tpl');

        die(Tools::jsonEncode($renderedSortable));

    }

}
