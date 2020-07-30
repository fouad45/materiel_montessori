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
* Menu builder controller.
*/

class AdminMenuBuilderController extends BaseTouchizeController
{

    const INFO_TEMPLATE = 'info/main-menu.tpl';

    const TABLE_NAME = 'touchize_main_menu';

    const TITLE_TABLE_NAME = 'touchize_main_menu_lang';

    /**
     * @var string
     */
    protected $position_identifier = 'position';

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->helperMenu = new TouchizeTopMenuHelper();
        $this->table = self::TABLE_NAME;
        $this->identifier = 'id_menu_item';
        $this->className = 'TouchizeMenuBuilder';
        $this->lang = true;
        $this->_defaultOrderBy = 'position';

        $this->context = Context::getContext();
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->translator = Context::getContext()->getTranslator();
        }

        $this->fields_list = array(
            'title' => array(
                'title' => $this->l('Title'),
                'width' => 'auto',
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'position' => 'position',
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
            ),
        );

        parent::__construct();
    }

    /**
     * @return array
     */
    public function getTemplateListVars()
    {
        return array(
            'title' => $this->l('Link menu')
        );
    }

    /**
     * AdminController::setMedia() override
     *
     * @see AdminController::setMedia()
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

        $this->context
            ->controller
            ->addCSS(
                _MODULE_DIR_.'touchize/views/css/top-menu.css'
            );

        $this->context
            ->controller
            ->addJS(
                array(
                    _MODULE_DIR_.'touchize/views/js/topmenu-loader.js',
                    _MODULE_DIR_.'touchize/views/js/jquery.nestable.js',
                    _MODULE_DIR_.'touchize/views/js/menu-builder.js'
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

    /**
     * AdminController::renderList() override
     *
     * @see AdminController::renderList()
     */
    public function renderList()
    {
        # Adds an Edit button for each result
        $this->addRowAction('edit');
        # Adds a Delete button for each result
        $this->addRowAction('delete');

        $topMenuBlock = $this->getTopMenuBlock();
        $infoTopMenu = $this->getInfoTopMenu();

        if (!$this->isSingleShop()) {
            return $this->createTemplate('builder/warning.tpl')->fetch();
        }

        return parent::renderList() . $infoTopMenu . $topMenuBlock;
    }

    /**
     * Admincontroller::getList() override
     *
     * @see  AdminController::renderList()
     */
    public function getList(
        $idLang,
        $orderBy = null,
        $orderWay = null,
        $start = 0,
        $limit = null,
        $idLangShop = false
    ) {
        parent::getList(
            $idLang,
            $orderBy,
            $orderWay,
            $start,
            $limit,
            $idLangShop
        );

        if ($this->_list) {
            foreach ($this->_list as $key => $item) {
                $sql = "SELECT *
                        FROM "._DB_PREFIX_.self::TITLE_TABLE_NAME."
                        WHERE id_lang = '".pSQL($idLang)."'
                        AND id_menu_item = '".pSQL($item['id_menu_item'])."'";

                $titles = Db::getInstance()->executeS($sql);

                switch ($item['action']) {
                    case 'page':
                        $data = TouchizeMenuBuilder::getSeoAndUrlsPages(
                            $idLang,
                            $item['page']
                        );

                        $title = !empty($titles) && is_array($titles)
                            ? $titles[0]['title']
                            : null;

                        if ($title) {
                            $this->_list[$key][
                                'title'
                            ] = $this->l($title);
                        } else {
                            $this->_list[$key][
                                'title'
                            ] = $this->l($data[0]['title']);
                        }
                        break;
                    case 'cms_page':
                        $data = TouchizeMenuBuilder::getCmsPages(
                            $idLang,
                            $item['cms_page']
                        );

                        $title = !empty($titles) && is_array($titles)
                            ? $titles[0]['title']
                            : null;

                        if ($title) {
                            $this->_list[$key][
                                'title'
                            ] = $this->l($title);
                        } else {
                            $this->_list[$key]['title'] = $this->l(
                                $data[0]['meta_title']
                            );
                        }
                        break;
                    default:
                        $sql = 'SELECT *
                                FROM '._DB_PREFIX_.self::TITLE_TABLE_NAME.'
                                WHERE id_menu_item = \'
                                '.pSQL($item['id_menu_item']).'\''
                                .' AND id_lang = \''.pSQL($idLang).'\'';

                        $titles = DB::getInstance()->executeS($sql);
                        if ($titles) {
                            $this->_list[$key]['title'] = $this->l(
                                $titles[0]['title']
                            );
                        }
                        break;
                }
            }
        }
    }

    /**
     * Admincontroller::initPageHeaderToolbar() override
     *
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_menu'] = array(
                'href' => self::$currentIndex
                        .'&addtouchize_main_menu&token='.$this->token,
                'desc' => $this->l('Add new link menu', null, null, false),
                'icon' => 'process-icon-new',
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * AdminController::renderForm() override
     *
     * @see AdminController::renderForm()
     */
    public function renderForm()
    {
        # Language id
        $langId = $this->context->language->id;

        # Set options for page dropdown list.
        $pageSelectOptions = array();
        $seoAndUrl = TouchizeMenuBuilder::getSeoAndUrlsPages($langId);
        if ($seoAndUrl) {
            foreach ($seoAndUrl as $val) {
                $pageSelectOptions[] = array(
                    'id_option' => $val['id_meta'],
                    'name' => $this->l($val['page']),
                );
            }
        }

        # Set options for CMS page dropdown list.
        $cmsPageSelectOptions = array();
        $cmsPages = TouchizeMenuBuilder::getCmsPages($langId);
        if ($cmsPages) {
            foreach ($cmsPages as $value) {
                $cmsPageSelectOptions[] = array(
                    'id_option' => $value['id_cms'],
                    'name' => $this->l($value['meta_title']),
                );
            }
        }

        # Set form fields.
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Link menu element'),
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'lang_id',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Menu item type:'),
                    'desc' => $this->l('Select menu item type'),
                    'name' => 'type',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 'menu-item',
                                'name' => $this->l('Link'),
                                'disabled' => false,
                            ),
                            array(
                                'id_option' => 'menu-header',
                                'name' => $this->l('Menu title'),
                                'disabled' => false,
                            ),
                            array(
                                'id_option' => 'menu-divider',
                                'name' => $this->l('Divider'),
                                'disabled' => false,
                            ),
                        ),
                        'id' => 'id_option',
                        'name' => 'name',
                        'disabled' => 'disabled',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Link type:'),
                    'desc' => $this->l('Select action type'),
                    'name' => 'action',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 'cms_page',
                                'name' => $this->l('CMS Page'),
                            ),
                            array(
                                'id_option' => 'page',
                                'name' => $this->l('Page'),
                            ),
                            array(
                                'id_option' => 'url',
                                'name' => $this->l('Url'),
                            ),
                        ),
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title:'),
                    'name' => 'title',
                    'desc' => $this->l('Current title name. Leave blank to use default system name.'),
                    'required' => true,
                    'lang' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Page'),
                    'desc' => $this->l('Select page'),
                    'name' => 'page',
                    'required' => true,
                    'options' => array(
                        'query' => $pageSelectOptions,
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('CMS Page'),
                    'desc' => $this->l('Select page'),
                    'name' => 'cms_page',
                    'required' => true,
                    'options' => array(
                        'query' => $cmsPageSelectOptions,
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Url'),
                    'required' => true,
                    'name' => 'url',
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('External'),
                    'desc' => $this->l('Select option'),
                    'name' => 'external',
                    'required' => true,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => '0',
                            'label' => $this->l('No'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => '1',
                            'label' => $this->l('Yes'),
                        ),
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Event'),
                    'name' => 'event',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Event input'),
                    'name' => 'event_input',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Page Url'),
                    'name' => 'page_url',
                    'required' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ),
        );

        # Language id as field value
        $this->fields_value = array(
            'lang_id' => $langId,
        );

        if (!$this->isSingleShop()) {
            return $this->createTemplate('builder/warning.tpl')->fetch();
        }

        return parent::renderForm();
    }

    /**
     * Return the list of fields value
     *
     * @param ObjectModel $obj Object
     *
     * @return array
     */
    public function getFieldsValue($obj)
    {
        $values = parent::getFieldsValue($obj);
        $translate = array();

        if ($obj->id) {
            $sql = "SELECT *
                    FROM "._DB_PREFIX_.self::TITLE_TABLE_NAME."
                    WHERE `id_menu_item` = '".pSQL($obj->id)."'";

            $titles = DB::getInstance()->executeS($sql);

            if ($titles) {
                foreach ($titles as $title) {
                    $translate[$title['id_lang']] = $this->l($title['title']);
                }
            }
        }

        $languages = Language::getLanguages(true);
        foreach ($languages as $lang) {
            if (!isset($translate[$lang['id_lang']])) {
                $translate[$lang['id_lang']] = "";
            }
        }
        $values['title'] = $translate;
        return $values;
    }

    /**
     * The processing of menu element position.
     *
     * @return string
     */
    public function ajaxProcessUpdatePositions()
    {
        $positions = Tools::getValue('menu_item');
        $menuBuilder = new TouchizeMenuBuilder();

        if (is_array($positions)) {
            if ($menuBuilder->updatePositions($positions)) {
                $response = Tools::jsonEncode(array('error' => false));
            } else {
                $response = Tools::jsonEncode(array('error' => true));
            }
        } else {
            $response = Tools::jsonEncode(array('error' => true));
        }

        header('Content-type: application/json');

        $this->ajaxDie($response);
    }

    /**
     * @return string
     */
    public function getInfoTemplate()
    {
        return static::INFO_TEMPLATE;
    }

    /**
     * @return string
     */
    public function getTopMenuBlock()
    {
        $smarty = $this->context->smarty;
        $smarty->assign(
            'previewPath',
            $this->context->link->getModuleLink(
                'touchize',
                'touchize',
                array(
                    'preview' => true,
                )
            )
        );
        $smarty->assign('img_dir', AdminSetupWizardController::IMAGE_CDN_PATH);

        Media::addJsDef(array('top_menu_options' => $this->getConfigDataArray()));

        $helper = new TouchizeAdminHelper();
        $helper->assignMenuVars();

        return $this->createTemplate('builder/topmenu.tpl')->fetch();
    }

    public function getInfoTopMenu()
    {
        return $this->createTemplate(AdminTopMenuBuilderController::INFO_TEMPLATE)->fetch();
    }

    /**
     * @return string
     */
    public function getConfigData()
    {
        return Tools::jsonEncode(array(
            'url' => $this->context->link->getAdminLink(
                'AdminTopMenuBuilder',
                true
            ),
            'allowed_items' => $this->getJsAllowedItems(),
            'selected_items' => $this->getSelectedItems(),
        ));
    }

    /**
     * @return array
     */
    public function getConfigDataArray()
    {
        return array(
            'url' => $this->context->link->getAdminLink(
                'AdminTopMenuBuilder',
                true
            ),
            'allowed_items' => $this->getJsAllowedItems(),
            'selected_items' => $this->getSelectedItems(),
        );
    }

    /**
     * @return array
     */
    public function getSelectedItems()
    {
        return $this->helperMenu->getSelectedItems();
    }

    /**
     * @param bool $flat
     *
     * @return array
     */
    public function getAllowedItems($flat = false)
    {
        return $this->helperMenu->getAllowedItems($flat);
    }

    /**
     * @param bool $flat
     *
     * @return array
     */
    public function getJsAllowedItems()
    {
        return $this->helperMenu->getJsAllowedItems();
    }
}
