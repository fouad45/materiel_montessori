<?php
/**
 * 2018 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 */

/**
 * Touchmaps controller.
 */

class AdminTouchmapsController extends BaseTouchizeController
{
    const INFO_TEMPLATE = 'info/touch-map.tpl';

    const CREATE_INFO_TEMPLATE = 'info/touch-map-create.tpl';

    /**
     * @var string
     */
    protected $position_identifier = 'id_touchize_touchmap';

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->bootstrap = true;

        $this->table = 'touchize_touchmap';
        $this->identifier = 'id_touchize_touchmap';
        $this->className = 'TouchizeTouchmap';

        $this->_defaultOrderBy = 'position';
        $this->lang = false;
        $this->context = Context::getContext();

        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->translator = Context::getContext()->getTranslator();
        }

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fieldImageSettings = array(
            array(
                'name' => 'image',
                'dir' => 'touchmaps',
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
            ),
        );

        $this->fields_list = array(
            'id_touchize_touchmap' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25,
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'width' => 20,
                'align' => 'center',
                'image' => 'touchmaps',
                'filter' => false,
                'search' => false,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => '300',
                'filter_key' => 'b!name',
            ),
            'active' => array(
                'title' => $this->l('Displayed'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'callback' => 'printActiveIcon',
                'type' => 'bool',
            ),
            'mobile' => array(
                'title' => $this->l('Show on Mobile'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'callback' => 'printMobileIcon',
                'type' => 'bool',
            ),
            'tablet' => array(
                'title' => $this->l('Show on Tablet'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'callback' => 'printTabletIcon',
                'type' => 'bool',
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'position' => 'position'
            ),
        );

        parent::__construct();

        $this->_where .= ' AND (a.id_shop = \'' . (int)Shop::getContextShopID(true) . '\' OR a.`id_shop` = 0)';
    }

    /**
     * AdminController::setMedia() override
     *
     * @see AdminController::setMedia()
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

        $this->context->controller->addCSS(
            array(
                _MODULE_DIR_.'touchize/views/css/embedbootstrap.css',
                _MODULE_DIR_.'touchize/views/css/octicons.min.css',
                _MODULE_DIR_.'touchize/views/css/cpicker.min.css',
                _MODULE_DIR_.'touchize/views/css/wizard.css'
            )
        );
        $this->addCSS(_MODULE_DIR_.'touchize/views/css/touchize-admin.css');
        $this->addJS(_MODULE_DIR_.'touchize/views/js/touchize-admin.js');
        $this->context->controller->addJS(
            array(
                _MODULE_DIR_.'touchize/views/js/botab.js',
                _MODULE_DIR_.'touchize/views/js/qrcode.js'
            )
        );
    }

    /**
     * AdminController::initContent() override
     *
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        $smarty = $this->context->smarty;

        $smarty->assign('img_dir', AdminSetupWizardController::IMAGE_CDN_PATH);
    }

    public function createImageDescription()
    {
        $smarty = $this->context->smarty;
        $smarty->assign(array(
            'maxUploadSize' => (Tools::getMaxUploadSize() / 1024),
            'hasObject' => $this->object->id ? true : false,
        ));
        if ($this->object->id) {
            $smarty->assign('imgURL', _PS_IMG_.'touchmaps/'
                        .$this->object->id.'.jpg?rand='.(int)mt_rand());
            $smarty->assign('objectId', $this->object->id);
            $smarty->assign('objectName', $this->object->name);
            $smarty->assign('token', Tools::getAdminTokenLite('AdminTouchmaps'));
        }
        return $this->createTemplate('partials/touchmapimagedescription.tpl')->fetch();
    }


    /**
     * Admincontroller::renderForm() override
     *
     * @see AdminController::renderForm()
     */
    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        if (Validate::isLoadedObject($this->object)) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }

        $this->initToolbar();

        $selectedCat = array();
        if (Tools::isSubmit('categories')) {
            foreach (Tools::getValue('categories') as $row) {
                $selectedCat[] = $row;
            }
        } elseif ($this->object->id) {
            $touchmap = TouchizeTouchmap::getCategories($this->object->id);
            foreach ($touchmap as $row) {
                $selectedCat[] = $row['id_category'];
            }
        }

        $imageDesc = $this->createImageDescription();
        $this->fields_form = array(
            'tinymce' => false,
            'legend' => array(
                'title' => $this->l('Banner'),
                'icon' => 'icon-image',
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_shop',
                    'id' => 'id_shop',
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Image:'),
                    'name' => 'image',
                    'display_image' => true,
                    'desc' => $imageDesc,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title:'),
                    'name' => 'name',
                    'id' => 'name',
                    'lang' => false,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:').' <>;=#{}',
                    'size' => 40,
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'width',
                    'id' => 'width',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'height',
                    'id' => 'height',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active:'),
                    'name' => 'active',
                    'required' => true,
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display on Mobile:'),
                    'name' => 'mobile',
                    'required' => true,
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'mobile_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'mobile_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display on Tablet:'),
                    'name' => 'tablet',
                    'required' => true,
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'tablet_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'tablet_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Run once:'),
                    'name' => 'runonce',
                    'required' => false,
                    'is_bool' => false,
                    'hint' => $this->l('Enable if the banner should only run once for new users.'),
                    'values' => array(
                        array(
                            'id' => 'runonce_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'runonce_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display on New arrivals:'),
                    'name' => 'new_products',
                    'required' => true,
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'new_products_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'new_products_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display on Best sellers:'),
                    'name' => 'best_sellers',
                    'required' => true,
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'best_sellers_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'best_sellers_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display on Specials:'),
                    'name' => 'prices_drop',
                    'required' => true,
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'best_sellers_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'best_sellers_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display on Homepage:'),
                    'name' => 'home_page',
                    'required' => true,
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'home_page_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'home_page_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'categories',
                    'name' => 'categories',
                    'tree' => array(
                        'id' => 'categories-tree',
                        'title' => $this->l('Categories'),
                        'selected_categories' => $selectedCat,
                        'use_search' => true,
                        'use_checkbox' => true,
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                'save_and_preview' => array(
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'title' => $this->l('Save and stay'),
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );

        return parent::renderForm();
    }

    /**
     * Admincontroller::initPageHeaderToolbar() override
     *
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_touchmap'] = array(
                'href' => self::$currentIndex
                        .'&addtouchize_touchmap&token='.$this->token,
                'desc' => $this->l('Add new banner', null, null, false),
                'icon' => 'process-icon-new',
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * Admincontroller::initProcess() override
     *
     * @see AdminController::initProcess()
     */
    public function initProcess()
    {
        parent::initProcess();

        if (Tools::isSubmit('changeActiveVal') && $this->id_object) {
            if ('1' === $this->tabAccess['edit']) {
                $this->action = 'change_active_val';
            } else {
                $this->errors[] = Tools::displayError(
                    $this->l('You do not have permission to edit this.')
                );
            }
        } elseif (Tools::isSubmit('changeTabletVal') && $this->id_object) {
            if ('1' === $this->tabAccess['edit']) {
                $this->action = 'change_tablet_val';
            } else {
                $this->errors[] = Tools::displayError(
                    $this->l('You do not have permission to edit this.')
                );
            }
        } elseif (Tools::isSubmit('changeMobileVal') && $this->id_object) {
            if ('1' === $this->tabAccess['edit']) {
                  $this->action = 'change_mobile_val';
            } else {
                $this->errors[] = Tools::displayError(
                    $this->l('You do not have permission to edit this.')
                );
            }
        }
    }

    /**
     * Admincontroller::uploadImage() override
     *
     * @see AdminController::uploadImage()
     */
    protected function uploadImage(
        $id,
        $name,
        $dir,
        $ext = false,
        $width = null,
        $height = null
    ) {
        # Since HelperList.php reates a thumbnail using the site id as well
        # in the name, we need to delete it if new image is uploaded.
        # It is not deleted thru the deleteImage as it should so doing it here
        if (Validate::isLoadedObject($object = $this->loadObject()) &&
            isset($_FILES[$name]['type'])
        ) {
            #.jpg hardcoded since always jpg
            $thumbname = _PS_TMP_IMG_DIR_.'touchize_touchmap'
                        .'_mini_'.$object->id.'_'
                        .$this->context->shop->id.'.jpg';

            if (file_exists($thumbname)) {
                unlink($thumbname);
            }
        }

        # If not GIF do the built in that will resize and change extensions
        if (isset($_FILES[$name]['type']) &&
            $_FILES[$name]['type'] != 'image/gif'
        ) {
            return parent::uploadImage($id, $name, $dir, $ext, $width, $height);
        }

        # Move the gif to the proper place without any resize
        # Keep part of parent code until resize and after (except unlink!)
        if (isset($_FILES[$name]['tmp_name']) &&
            !empty($_FILES[$name]['tmp_name'])
        ) {
            # Delete old image
            if (Validate::isLoadedObject($object = $this->loadObject())) {
                $object->deleteImage();
            } else {
                return false;
            }

            # Check image validity
            $maxSize = isset($this->max_image_size)
                ? $this->max_image_size
                : 0;

            if ($error = ImageManager::validateUpload(
                $_FILES[$name],
                Tools::getMaxUploadSize($maxSize)
            )) {
                $this->errors[] = $error;
            }

            $tmpName = _PS_IMG_DIR_.$dir.$id.'.jpg';

            if (!move_uploaded_file($_FILES[$name]['tmp_name'], $tmpName)) {
                return false;
            }

            if (count($this->errors)) {
                return false;
            }

            if ($this->afterImageUpload()) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Toggle mobile active flag
     */
    public function processChangeActiveVal()
    {
        $touchmap = new TouchizeTouchmap($this->id_object);
        if (!Validate::isLoadedObject($touchmap)) {
            $this->errors[] = Tools::displayError(
                $this->l('An error occurred while updating banner information.')
            );
        }

        $touchmap->active = $touchmap->active ? 0 : 1;
        if (!$touchmap->simpleUpdate()) {
            $this->errors[] = Tools::displayError(
                $this->l('An error occurred while updating banner information.')
            );
        }

        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    /**
     * Toggle mobile active flag
     */
    public function processChangeMobileVal()
    {
        $touchmap = new TouchizeTouchmap($this->id_object);
        if (!Validate::isLoadedObject($touchmap)) {
            $this->errors[] = Tools::displayError(
                $this->l('An error occurred while updating banner information.')
            );
        }

        $touchmap->mobile = $touchmap->mobile ? 0 : 1;
        if (!$touchmap->simpleUpdate()) {
            $this->errors[] = Tools::displayError(
                $this->l('An error occurred while updating banner information.')
            );
        }

        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    /**
     * Toggle tablet active flag
     */
    public function processChangeTabletVal()
    {
        $touchmap = new TouchizeTouchmap($this->id_object);
        if (!Validate::isLoadedObject($touchmap)) {
            $this->errors[] = Tools::displayError(
                $this->l('An error occurred while updating banner information.')
            );
        }

        $touchmap->tablet = $touchmap->tablet ? 0 : 1;
        if (!$touchmap->simpleUpdate()) {
            $this->errors[] = Tools::displayError(
                $this->l('An error occurred while updating banner information.')
            );
        }

        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    /**
     * Get products.
     */
    public function ajaxProcessGetProducts()
    {
        $query = Tools::getValue('q');
        $query = Tools::replaceAccentedChars(urldecode($query));
        $products = array();
        if (Validate::isValidSearch($query)) {
            $search   = Product::searchByName($this->context->language->id, $query);
        } else {
            $search = null;
        }
        if (is_array($search) || is_object($search)) {
            foreach ($search as $product) {
                $image = Image::getCover($product['id_product']);
                $tempProduct = new Product($product['id_product'], false, Context::getContext()->language->id);
                $imagePath = $this->context->link->getImageLink(
                    $tempProduct->link_rewrite,
                    $image['id_image'],
                    ImageType::getFormatedName('home')
                );

                array_push($products, array(
                    'Id'    => $product['id_product'],
                    'Title' => $product['name'],
                    'ShortDescription' => '<img src="'. $imagePath .'" alt="" class="imgm img-thumbnail">',
                    'Image' => $imagePath,
                ));
            }
        }
        $this->jsonResponse($products); # TODO: Fix, not pretty
    }

    /**
     * Get categories.
     */
    public function ajaxProcessGetCategoriesOrig()
    {
        $cats = array();
        $query = Tools::getValue('q');
        if (Validate::isValidSearch($query)) {
            $categories = Category::searchByName(
                $this->context->language->id,
                $query
            );
            foreach ($categories as $category) {
                array_push(
                    $cats,
                    array(
                        'Id' => $category['id_category'],
                        'Name' => $category['name'],
                    )
                );
            }
        }
        $this->jsonResponse($cats);
    }

    /**
     * Get categories.
     */
    public function ajaxProcessGetCategories()
    {
        $cats = array();
        $query = Tools::getValue('q');
        if (Validate::isValidSearch($query)) {
            $helperMenu = new TouchizeTopMenuHelper();
            $categories = $helperMenu->getAllowedItems(true);
            foreach ($categories as $category) {
                if (false !== Tools::strpos(Tools::strtolower($category['name']), Tools::strtolower($query))) {
                    array_push(
                        $cats,
                        array(
                            'Id' => $category['id_category'],
                            'Name' => $category['name'],
                        )
                    );
                }
            }
        }
        $this->jsonResponse($cats);
    }

    /**
     * Get action areas.
     */
    public function ajaxProcessGetActionAreas()
    {
        $this->getActionAreas();
    }

    /**
     * @param $float
     *
     * @return int
     */
    private function validateFloat($float)
    {
        return preg_match('/[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)/', $float);
    }

    /**
     * Add action area.
     */
    public function ajaxProcessAddActionArea()
    {
        $id = Tools::getValue('id_touchize_touchmap');
        $tx = Tools::getValue('tx');
        $ty = Tools::getValue('ty');
        $width = Tools::getValue('width');
        $height = Tools::getValue('height');

        if (Validate::isTableOrIdentifier($id) &&
            $this->validateFloat($tx) &&
            $this->validateFloat($ty) &&
            $this->validateFloat($width) &&
            $this->validateFloat($height)
        ) {
            $area = new TouchizeActionarea();
            $area->id_touchize_touchmap = $id;
            $area->tx = $tx * 100 . '%';
            $area->ty = $ty * 100 . '%';
            $area->width = $width * 100 . '%';
            $area->height = $height * 100 . '%';
            $area->save();
        }

        $this->getActionAreas();
    }

    /**
     * Update action area.
     */
    public function ajaxProcessUpdateActionArea()
    {
        $id = (int)Tools::getValue('id');
        $pid = Tools::getValue('product_id');
        $tid = Tools::getValue('taxon_id');
        $query = Tools::getValue('search_term');

        if (Validate::isTableOrIdentifier($id) &&
            (!$pid || Validate::isTableOrIdentifier($pid)) &&
            (!$tid || Validate::isString($tid)) &&
            (!$query || Validate::isString($query))
        ) {
            $area = new TouchizeActionarea($id);
            $area->id_product  = $pid;
            if (false !== Tools::strpos($tid, 'manufacturer')) {
                $area->id_manufacturer = str_replace('manufacturer', '', $tid);
            } else {
                $area->id_category = $tid;
            }
            $area->search_term = $query;
            $area->save();
        }

        $this->getActionAreas();
    }

    /**
     * Delete action area.
     */
    public function ajaxProcessDeleteActionArea()
    {
        $area = new TouchizeActionarea((int)Tools::getValue('id'));

        if (Validate::isLoadedObject($area)) {
            $area->delete();
        }

        $this->getActionAreas();
    }

    /**
     * Update position
     */
    public function ajaxProcessUpdatePositions()
    {
        $way = (int)(Tools::getValue('way'));
        $id_touchmap = (int)(Tools::getValue('id'));
        $positions = Tools::getValue($this->table);

        if (Validate::isInt($way) &&
            Validate::isInt($id_touchmap)
        ) {
            foreach ($positions as $position => $value) {
                $pos = explode('_', $value);
                if (isset($pos[2]) && (int)$pos[2] === $id_touchmap) {
                    if ($touchmap = new TouchizeTouchmap((int)$pos[2])) {
                        if (isset($position) && $touchmap->updatePosition($way, $position)) {
                            echo 'ok position '.(int)$position.' for Touchmap '.(int)$pos[1].'\r\n';
                        } else {
                            echo '{"hasError" : true, "errors" : "Can not update Touchmap '.
                                (int)$id_touchmap.' to position '.(int)$position.' "}';
                        }
                    } else {
                        echo '{"hasError" : true, "errors" : "This Touchmap ('.(int)$id_touchmap.') cant be loaded"}';
                    }
                    break;
                }
            }
        }
    }

    /**
     * Get action areas.
     */
    protected function getActionAreas()
    {
        $areas = TouchizeActionarea::getActionAreas(
            Tools::getValue('id_touchize_touchmap')
        );

        $this->jsonResponse($areas);
    }

    /**
     * Map product.
     *
     * @param  array $product
     *
     * @return array
     */
    protected function mapProduct($product)
    {
        return array(
            'Id' => $product['id_product'],
            'Title' => $this->l($product['name']),
            'ShortDescription' => $this->l($product['description_short']),
        );
    }

    /**
     * Returns json string as server response.
     *
     * @param  mixed $response
     *
     * @return string
     */
    protected function jsonResponse($response)
    {
        header('Content-type: application/json');

        $this->ajaxDie(Tools::jsonEncode($response));
    }

    /**
     * To print active icon.
     *
     * @param  string $value
     * @param  array $touchmap
     *
     * @return string
     */
    public function printActiveIcon($value, $touchmap)
    {
        $enabled = $value
            ? 'action-enabled'
            : 'action-disabled';

        $i = $value
            ? Html::tag('i', '', array('class' => 'icon-check'))
            : Html::tag('i', '', array('class' => 'icon-remove'));

        $html = Html::a(
            $i,
            'index.php?tab=AdminTouchmaps&id_touchize_touchmap='
                .(int)$touchmap['id_touchize_touchmap']
                .'&changeActiveVal&token='
                .Tools::getAdminTokenLite('AdminTouchmaps'),
            array(
                'class' => 'list-action-enable '.$enabled,
            )
        );

        return $html;
    }

    /**
     * To print mobile icon.
     *
     * @param  string $value
     * @param  array $touchmap
     *
     * @return string
     */
    public function printMobileIcon($value, $touchmap)
    {
        $enabled = $value
            ? 'action-enabled'
            : 'action-disabled';

        $i = $value
            ? Html::tag('i', '', array('class' => 'icon-check'))
            : Html::tag('i', '', array('class' => 'icon-remove'));

        $html = Html::a(
            $i,
            'index.php?tab=AdminTouchmaps&id_touchize_touchmap='
                .(int)$touchmap['id_touchize_touchmap']
                .'&changeMobileVal&token='
                .Tools::getAdminTokenLite('AdminTouchmaps'),
            array(
                'class' => 'list-action-enable '.$enabled,
            )
        );

        return $html;
    }

    /**
     * To print tablet icon.
     *
     * @param  string $value
     * @param  array $touchmap
     *
     * @return string
     */
    public function printTabletIcon($value, $touchmap)
    {
        $enabled = $value
            ? 'action-enabled'
            : 'action-disabled';

        $i = $value
            ? Html::tag('i', '', array('class' => 'icon-check'))
            : Html::tag('i', '', array('class' => 'icon-remove'));

        $html = Html::a(
            $i,
            'index.php?tab=AdminTouchmaps&id_touchize_touchmap='
                .(int)$touchmap['id_touchize_touchmap']
                .'&changeTabletVal&token='
                .Tools::getAdminTokenLite('AdminTouchmaps'),
            array(
                'class' => 'list-action-enable '.$enabled,
            )
        );

        return $html;
    }

    /**
     * @return string
     */
    public function getInfoTemplate()
    {
        $createAction = Tools::getValue('addtouchize_touchmap', false);
        $updateAction = Tools::getValue('updatetouchize_touchmap', false);

        if ($createAction !== false || $updateAction !== false) {
            return static::CREATE_INFO_TEMPLATE;
        }
        return static::INFO_TEMPLATE;
    }
}
