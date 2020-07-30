<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2016 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once _PS_MODULE_DIR_ . 'fieldsizechart/models/SizeGuideField.php';

class FieldSizeChart extends Module
{
    protected $config_form = false;
    private $_html = '';

    public function __construct()
    {
        $this->name = 'fieldsizechart';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Fieldthemes';
        $this->need_instance = 0;
        $this->module_key = '';

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Size guide chart');
        $this->description = $this->l('Show popup with size guide');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->config_name = 'FIELDSIZECHART';
        $this->defaults = array(
            'width' => 1200,
            'height' => 650,
            'hook' => 2,
            'sh_measure' => 0,
            'sh_global' => 1,
            'content' => 'Content of newsletter popup',
            'global' => 'Content of newsletter popup',
        );
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
		$this->_createTab();
        if (parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayProductButtons') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('actionProductUpdate') &&
            $this->registerHook('actionAdminProductsControllerSaveAfter') &&
            $this->registerHook('actionProductDelete') &&
            $this->registerHook('actionUpdateQuantity') &&
            $this->registerHook('displayProductSizeGuide') &&
            $this->registerHook('actionValidateOrder') &&
            $this->registerHook('extraLeft') &&
            $this->registerHook('extraRight') &&
            $this->createTables()) {

            $this->setDefaults();
            $this->generateCss();
            return true;
        } else {
            return false;
        }

    }

    public function uninstall()
    {
		$this->_deleteTab();
        foreach ($this->defaults as $default => $value) {
            Configuration::deleteByName($this->config_name . '_' . $default);
        }

        return parent::uninstall() && $this->deleteTables();
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
        $tab->class_name = "AdminFieldSizechart";
        $tab->name = array();
        foreach (Language::getLanguages() as $lang){
            $tab->name[$lang['id_lang']] = "Config Sizechart";
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
        $id_tab = Tab::getIdFromClassName('AdminFieldSizechart');
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
	





    /**
     * Creates tables
     */
    protected function createTables()
    {
        /* guides */
        $res = (bool) Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldsizechart` (
				`id_fieldsizechart_guides` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id_fieldsizechart_guides`, `id_shop`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
		');

        /* guides configuration */
        $res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldsizechart_guides` (
			  `id_fieldsizechart_guides` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`id_fieldsizechart_guides`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
		');

        /* guides lang configuration */
        $res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldsizechart_guides_lang` (
			  `id_fieldsizechart_guides` int(10) unsigned NOT NULL,
			  `id_lang` int(10) unsigned NOT NULL,
			  `title` varchar(255) NOT NULL,
			  `description` text NOT NULL,
			  PRIMARY KEY (`id_fieldsizechart_guides`,`id_lang`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
		');

        /* guides product association */
        $res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fieldsizechart_product` (
				`id_product` int(10) unsigned NOT NULL,
				`id_guide` int(10) unsigned NOT NULL,
				 PRIMARY KEY (`id_product`)
				) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
		');

        return $res;
    }

    /**
     * deletes tables
     */
    protected function deleteTables()
    {
        $guides = $this->getGuides();
        foreach ($guides as $guide) {
            $to_del = new SizeGuideField($guide['id_guide']);
            $to_del->delete();
        }

        return Db::getInstance()->execute('
			DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'fieldsizechart`, `' . _DB_PREFIX_ . 'fieldsizechart_guides`, `' . _DB_PREFIX_ . 'fieldsizechart_product`, `' . _DB_PREFIX_ . 'fieldsizechart_guides_lang`;
		');
    }

    public function setDefaults()
    {
        foreach ($this->defaults as $default => $value) {
            if ($default == 'content') {
                $message_trads = array();
                foreach (Language::getLanguages(false) as $lang) {
                    $message_trads[(int) $lang['id_lang']] = '';
                }

                Configuration::updateValue($this->config_name . '_' . $default, $message_trads, true);
            } elseif ($default == 'global') {
                $message_trads = array();
                $curren_path =_MODULE_DIR_ . $this->name . '/images/images.jpg';
                foreach (Language::getLanguages(false) as $lang) {
                    $message_trads[(int) $lang['id_lang']] = '<div class="row">
<div class="left-content-size">
<div class="hover_banner_img"><a href="#"><img src="'.$curren_path.'" alt="" /></a></div>
</div>
<div class="right-content-size">
<h2 class="title-measure">Measuring Tips to Assure The Best Fit</h2>
<ul class="text-measure">
<li><span>Measure your back size â€“ wrap the measuring tape snugly around your ribcage, just under your bust line <em>1</em>, keeping it horizontal. This will give you your band size.</span></li>
<li><span>Then measure around the fullest part of your bust <em>2</em> , again keeping the tape horizontal.</span></li>
<li><span>To determine the size of pants, measure the girth of the waist <em>3</em> and hips <em>4</em></span></li>
<li><span>The following charts will help you match the designerâ€™s size scheme for this product (indicated in the dropdown) to your usual size</span></li>
</ul>
<h2 class="title-measure a2">size guide visible to all products</h2>
<table class="table table-sizegudie">
<thead>
<tr><th></th><th>UK</th><th>USA</th><th class="border-right">EU</th><th>BUST</th><th>Waist</th><th>Hips</th></tr>
</thead>
<tbody>
<tr class="backgroundc">
<td>XS</td>
<td>8</td>
<td>4</td>
<td class="border-right">34</td>
<td>31/32</td>
<td>23/24</td>
<td>33/34</td>
</tr>
<tr>
<td>S</td>
<td>10</td>
<td>6</td>
<td class="border-right">36</td>
<td>33/34</td>
<td>25/26</td>
<td>35/36</td>
</tr>
<tr class="backgroundc">
<td>M</td>
<td>12</td>
<td>8</td>
<td class="border-right">38</td>
<td>35/36</td>
<td>27/28</td>
<td>37/38</td>
</tr>
<tr>
<td>L</td>
<td>12</td>
<td>8</td>
<td class="border-right">38</td>
<td>35/36</td>
<td>27/28</td>
<td>37/38</td>
</tr>
<tr class="backgroundc">
<td>XL</td>
<td>10</td>
<td>6</td>
<td class="border-right">36</td>
<td>33/34</td>
<td>25/26</td>
<td>35/36</td>
</tr>
</tbody>
</table>
</div>
</div>';
                }

                Configuration::updateValue($this->config_name . '_' . $default, $message_trads, true);
            } else {
                Configuration::updateValue($this->config_name . '_' . $default, $value);
            }

        }
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->context->controller->addJS($this->_path . 'js/back.js');
        $this->context->controller->addCSS($this->_path . 'css/back.css');

        if (Tools::isSubmit('submitGuide') || Tools::isSubmit('delete_id_guide')) {
            if ($this->_postValidation()) {
                $this->_postProcess();
                $this->_html .= $this->renderForm();
                $this->_html .= $this->renderList();
            } else {
                $this->_html .= $this->renderAddForm();
            }

        } elseif (Tools::isSubmit('addGuide') || (Tools::isSubmit('id_guide') && $this->guideExists((int) Tools::getValue('id_guide')))) {
            return $this->renderAddForm();
        } elseif (Tools::isSubmit('submitfieldsizechartModule')) {
            $this->_postProcess2();

            $this->context->smarty->assign('module_dir', $this->_path);

            $this->_html .= $this->renderForm() . $this->renderList();
        } else {

            $this->context->smarty->assign('module_dir', $this->_path);

            $this->_html .= $this->renderForm() . $this->renderList();
        }
        return $this->_html;
    }

    private function _postValidation()
    {
        $errors = array();

        /* Validation for guide */
        if (Tools::isSubmit('submitGuide')) {
            /* If edit : checks id_guide */
            if (Tools::isSubmit('id_guide')) {
                if (!Validate::isInt(Tools::getValue('id_guide')) && !$this->guideExists(Tools::getValue('id_guide'))) {
                    $errors[] = $this->l('Invalid id_guide');
                }

            }
            /* Checks title/description/*/
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                if (Tools::strlen(Tools::getValue('title_' . $language['id_lang'])) > 255) {
                    $errors[] = $this->l('The title is too long.');
                }

            }

            /* Checks title/description for default lang */
            $id_lang_default = (int) Configuration::get('PS_LANG_DEFAULT');
            if (Tools::strlen(Tools::getValue('title_' . $id_lang_default)) == 0) {
                $errors[] = $this->l('The title is not set.');
            }

        }
        /* Validation for deletion */
        elseif (Tools::isSubmit('delete_id_guide') && (!Validate::isInt(Tools::getValue('delete_id_guide')) || !$this->guideExists((int) Tools::getValue('delete_id_guide')))) {
            $errors[] = $this->l('Invalid id_guide');
        }

        /* Display errors if needed */
        if (count($errors)) {
            $this->_html .= $this->displayError(implode('<br />', $errors));

            return false;
        }

        /* Returns if validation is ok */

        return true;
    }

    private function _postProcess()
    {
        $errors = array();

        /* Processes guide */
        if (Tools::isSubmit('submitGuide')) {
            /* Sets ID if needed */
            if (Tools::getValue('id_guide')) {
                $guide = new SizeGuideField((int) Tools::getValue('id_guide'));
                if (!Validate::isLoadedObject($guide)) {
                    $this->_html .= $this->displayError($this->l('Invalid id_guide'));

                    return false;
                }
            } else {
                $guide = new SizeGuideField();
            }

            $guide->active = 1;

            /* Sets each langue fields */
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $guide->title[$language['id_lang']] = Tools::getValue('title_' . $language['id_lang']);
                $guide->description[$language['id_lang']] = Tools::getValue('description_' . $language['id_lang']);

            }

            /* Processes if no errors  */
            if (!$errors) {
                /* Adds */
                if (!Tools::getValue('id_guide')) {
                    if (!$guide->add()) {
                        $errors[] = $this->displayError($this->l('The guide could not be added.'));
                    }

                }
                /* Update */
                elseif (!$guide->update()) {
                    $errors[] = $this->displayError($this->l('The guide could not be updated.'));
                }

                $this->clearCache();
            }
        } /* Deletes */
        elseif (Tools::isSubmit('delete_id_guide')) {
            $guide = new SizeGuideField((int) Tools::getValue('delete_id_guide'));
            $res = $guide->delete();
            $this->clearCache();
            if (!$res) {
                $this->_html .= $this->displayError('Could not delete.');
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=1&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
            }

        }

        /* Display errors if needed */
        if (count($errors)) {
            $this->_html .= $this->displayError(implode('<br />', $errors));
        } elseif (Tools::isSubmit('submitGuide') && Tools::getValue('id_guide')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=4&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
        } elseif (Tools::isSubmit('submitGuide')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=3&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
        }

    }

    public function renderList()
    {
        $guides = $this->getGuides();

        $this->context->smarty->assign(
            array(
                'link' => $this->context->link,
                'guides' => $guides,
            )
        );

        return $this->display(__FILE__, 'list.tpl');
    }

    public function renderAddForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Guide informations'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title'),
                        'name' => 'title',
                        'lang' => true,
                    ),

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Description'),
                        'name' => 'description',
                        'autoload_rte' => true,
                        'lang' => true,
                    ),
                    array(
                        'type' => 'table_creator',
                        'label' => $this->l('Generate table and add to editor'),
                        'name' => 'table_creator',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        if (Tools::isSubmit('id_guide') && $this->guideExists((int) Tools::getValue('id_guide'))) {
            $guide = new SizeGuideField((int) Tools::getValue('id_guide'));
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_guide');
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGuide';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code,
            ),
            'fields_value' => $this->getAddFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'module_path' => $this->_path,
            'image_baseurl' => $this->_path . 'images/',
        );

        $helper->override_folder = '/';

        return $helper->generateForm(array($fields_form));
    }

    public function getGuides($active = null)
    {
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hs.`id_fieldsizechart_guides` as id_guide, hssl.`title`, hssl.`description`
			FROM ' . _DB_PREFIX_ . 'fieldsizechart hs
			LEFT JOIN ' . _DB_PREFIX_ . 'fieldsizechart_guides hss ON (hs.id_fieldsizechart_guides = hss.id_fieldsizechart_guides)
			LEFT JOIN ' . _DB_PREFIX_ . 'fieldsizechart_guides_lang hssl ON (hss.id_fieldsizechart_guides = hssl.id_fieldsizechart_guides)
			WHERE id_shop = ' . (int) $id_shop . '
			AND hssl.id_lang = ' . (int) $id_lang
        );
    }

    public function getAddFieldsValues()
    {
        $fields = array();

        if (Tools::isSubmit('id_guide') && $this->guideExists((int) Tools::getValue('id_guide'))) {
            $guide = new SizeGuideField((int) Tools::getValue('id_guide'));
            $fields['id_guide'] = (int) Tools::getValue('id_guide', $guide->id);
        } else {
            $guide = new SizeGuideField();
        }

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $fields['title'][$lang['id_lang']] = Tools::getValue('title_' . (int) $lang['id_lang'], $guide->title[$lang['id_lang']]);
            $fields['description'][$lang['id_lang']] = Tools::getValue('description_' . (int) $lang['id_lang'], $guide->description[$lang['id_lang']]);
        }

        return $fields;
    }

    public function guideExists($id_guide)
    {
        $req = 'SELECT hs.`id_fieldsizechart_guides` as id_guide
				FROM `' . _DB_PREFIX_ . 'fieldsizechart` hs
				WHERE hs.`id_fieldsizechart_guides` = ' . (int) $id_guide;
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);

        return ($row);
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitfieldsizechartModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'module_path' => $this->_path,
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }
    public function getAttributes()
    {
        $attributes = AttributeGroup::getAttributesGroups($this->context->language->id);

        $selectAttributes = array();

        foreach ($attributes as $attribute) {
            $selectAttributes[$attribute['id_attribute_group']]['id_option'] = $attribute['id_attribute_group'];
            $selectAttributes[$attribute['id_attribute_group']]['name'] = $attribute['name'];
        }

        return $selectAttributes;
    }
    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {

        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(

                    array(
                        'type' => 'select',
                        'label' => $this->l('Hook'),
                        'name' => 'hook',
                        'desc' => $this->l('Note: If you want to use custom hook you have to add in product.tpl in place which you want to show it'),
                        'options' => array(
                            'query' => array(array(
                                'id_option' => 1,
                                'name' => $this->l('Custom Hook'),
                            ),
                                array(
                                    'id_option' => 0,
                                    'name' => $this->l('displayProductButtons'),
                                ),
                                array(
                                    'id_option' => 2,
                                    'name' => $this->l('extraLeft'),
                                ),
                                array(
                                    'id_option' => 3,
                                    'name' => $this->l('extraRight'),
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Width'),
                        'name' => 'width',
                        'suffix' => 'px',
                        'desc' => $this->l('Popup window width.'),
                        'size' => 20,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Height of main content'),
                        'name' => 'height',
                        'suffix' => 'px',
                        'desc' => $this->l('Popup window height.'),
                        'size' => 20,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show how to measure tab'),
                        'name' => 'sh_measure',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('How to measure tab'),
                        'name' => 'content',
                        'autoload_rte' => true,
                        'lang' => true,
                        'cols' => 60,
                        'rows' => 30,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show global size guide'),
                        'name' => 'sh_global',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Global size guide'),
                        'name' => 'global',
                        'autoload_rte' => true,
                        'lang' => true,
                        'cols' => 60,
                        'rows' => 30,
                    ),
                    array(
                        'type' => 'table_creator',
                        'label' => $this->l('Generate table and add to editor'),
                        'name' => 'table_creator',
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $var = array();

        foreach ($this->defaults as $default => $value) {

            if ($default == 'content' || $default == 'global') {
                foreach (Language::getLanguages(false) as $lang) {
                    $var[$default][(int) $lang['id_lang']] = Configuration::get($this->config_name . '_' . $default, (int) $lang['id_lang']);
                }

            } elseif ($default == 'newval') {
                $var[$default] = 1;
            } else {
                $var[$default] = Configuration::get($this->config_name . '_' . $default);
            }

        }
        return $var;

    }

    /**
     * Save form data.
     */
    protected function _postProcess2()
    {
        foreach ($this->defaults as $default => $value) {
            if ($default == 'content') {
                $message_trads = array();
                foreach ($_POST as $key => $value) {
                    if (preg_match('/content_/i', $key)) {
                        $id_lang = preg_split('/content_/i', $key);
                        $message_trads[(int) $id_lang[1]] = $value;
                    }
                }

                Configuration::updateValue($this->config_name . '_' . $default, $message_trads, true);
            } elseif ($default == 'global') {
                $message_trads = array();
                foreach ($_POST as $key => $value) {
                    if (preg_match('/global_/i', $key)) {
                        $id_lang = preg_split('/global_/i', $key);
                        $message_trads[(int) $id_lang[1]] = $value;
                    }
                }

                Configuration::updateValue($this->config_name . '_' . $default, $message_trads, true);
            } elseif ($default == 'newval') {
                if (Tools::getValue($default)) {
                    Configuration::updateValue($this->config_name . '_' . $default, mt_rand(1, 40000));
                }
            } else {
                Configuration::updateValue($this->config_name . '_' . $default, (Tools::getValue($default)));
            }

        }
        $this->_clearCache('fieldsizechart.tpl');
        $this->generateCss();
    }

    public function clearCache()
    {
        $this->_clearCache('fieldsizechart.tpl');
    }

    public function generateCss()
    {
        $css = '';

        $css .= '
		#fieldsizechart{ width: ' . (int) Configuration::get($this->config_name . '_width') . 'px; height: ' . ((int) Configuration::get($this->config_name . '_height')) . 'px; }
		';
        if (Shop::getContext() == Shop::CONTEXT_GROUP) {
            $my_file = $this->local_path . 'css/fieldsizechart_g_' . (int) $this->context->shop->getContextShopGroupID() . '.css';
        } elseif (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $my_file = $this->local_path . 'css/fieldsizechart_s_' . (int) $this->context->shop->getContextShopID() . '.css';
        }

        $fh = fopen($my_file, 'w') or die("can't open file");
        fwrite($fh, $css);
        fclose($fh);
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        if ($this->context->controller->php_self == 'product') {
            $this->context->controller->addJS($this->_path . '/js/front.js');
            $this->context->controller->addCSS($this->_path . '/css/front.css');
            if (Shop::getContext() == Shop::CONTEXT_GROUP) {
                $this->context->controller->addCSS(($this->_path) . 'css/fieldsizechart_g_' . (int) $this->context->shop->getContextShopGroupID() . '.css', 'all');
            } elseif (Shop::getContext() == Shop::CONTEXT_SHOP) {
                $this->context->controller->addCSS(($this->_path) . 'css/fieldsizechart_s_' . (int) $this->context->shop->getContextShopID() . '.css', 'all');
            }

        }
        $this->context->controller->addCSS($this->_path . '/css/platributes.css');

    }

    public function hookDisplayAdminProductsExtra($params)
    {

        if (Validate::isLoadedObject($product = new Product((int) Tools::getValue('id_product')))) {
            $guides = $this->getGuides();

            $this->context->smarty->assign(array(
                'guides' => $this->getGuides(),
                'selectedGuide' => (int) SizeGuideField::getProductGuide((int) Tools::getValue('id_product')),
            ));

            return $this->display(__FILE__, 'views/templates/admin/addtab.tpl');
        } else {
            return $this->displayError($this->l('You must save this product before adding tabs'));
        }
    }

    public function hookdisplayProductListReviews($params)
    {
        $prodtid = (int) $params['product']['id_product'];

        return $this->hookdisplayProductAttributesPL($prodtid);

    }

    public function hookActionUpdateQuantity()
    {
        $this->_clearcache('combinations.tpl');
    }

    public function hookActionValidateOrder()
    {
        $this->_clearcache('combinations.tpl');
    }

    public function hookActionProductUpdate($params)
    {
        $this->_clearcache('combinations.tpl');

    }

    public function hookActionAdminProductsControllerSaveAfter($params)
    {
        $this->_clearcache('combinations.tpl');

        $id_product = (int) Tools::getValue('id_product');
        $id_guide = (int) Tools::getValue('id_fieldsizechart');

        if ($id_guide) {
            SizeGuideField::assignProduct($id_product, $id_guide);
        } else {
            SizeGuideField::unassignProduct($id_product);
        }

    }

    public function hookActionProductDelete($params)
    {
        $this->_clearcache('combinations.tpl');

        $id_product = (int) Tools::getValue('id_product');
        SizeGuideField::unassignProduct($id_product);
    }

    public function hookDisplayProductSizeGuide($params)
    {
        if ($this->context->controller->php_self != 'product' || Configuration::get($this->config_name . '_hook') != 1) {
            return;
        }

        return $this->_prepareHook($params);
    }

    public function _prepareHook($params)
    {

        $product = (int) Tools::getValue('id_product');

        $id_guide = SizeGuideField::getProductGuide((int) Tools::getValue('id_product'));
        $sh_global = Configuration::get($this->config_name . '_sh_global');

        if ($id_guide || $sh_global) {
            if ($id_guide) {
                $guide = new SizeGuideField((int) $id_guide, $this->context->language->id);
                $cache_id = 'fieldsizechart|' . (int) $id_guide;
            } else {
                $cache_id = 'fieldsizechart';
            }

            if ($this->getCacheId($cache_id)) {
                if ($id_guide) {
                    $this->smarty->assign(
                        array(
                            'guide' => $guide,
                        )
                    );
                }

                $this->smarty->assign(
                    array(
                        'howto' => Configuration::get($this->config_name . '_content', $this->context->language->id),
                        'sh_measure' => Configuration::get($this->config_name . '_sh_measure'),
                        'sh_global' => $sh_global,  
//                        'sh_measure' => $sh_measure,   
                        'global' => Configuration::get($this->config_name . '_global', $this->context->language->id),
                    )

                );

            }
            return $this->display(__FILE__, 'fieldsizechart.tpl', $this->getCacheId($cache_id));
        }
    }

    public function hookDisplayProductButtons($params)
    {
        if ($this->context->controller->php_self != 'product' || Configuration::get($this->config_name . '_hook') != 0) {
            return;
        }

        return $this->_prepareHook($params);
    }

    public function hookExtraLeft($params)
    {
        if ($this->context->controller->php_self != 'product' || Configuration::get($this->config_name . '_hook') != 2) {
            return;
        }

        return $this->_prepareHook($params);
    }

    public function hookExtraRight($params)
    {
        if ($this->context->controller->php_self != 'product' || Configuration::get($this->config_name . '_hook') != 3) {
            return;
        }

        return $this->_prepareHook($params);
    }

    public function getAttributeCombinations($id_product, $id_lang, $groupByIdAttributeGroup = true)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }
        $sql = 'SELECT pa.*, product_attribute_shop.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name,
					a.`id_attribute`
				FROM `' . _DB_PREFIX_ . 'product_attribute` pa
				' . Shop::addSqlAssociation('product_attribute', 'pa') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
				WHERE pa.`id_product` = ' . (int) $id_product . '
				GROUP BY pa.`id_product_attribute`' . ($groupByIdAttributeGroup ? ',ag.`id_attribute_group`' : '') . '
				ORDER BY pa.`id_product_attribute`';
        $res = Db::getInstance()->executeS($sql);
        //Get quantity of each variations
        foreach ($res as $key => $row) {
            $cache_key = $row['id_product'] . '_' . $row['id_product_attribute'] . '_quantity';
            if (!Cache::isStored($cache_key)) {
                Cache::store(
                    $cache_key,
                    StockAvailable::getQuantityAvailableByProduct($row['id_product'], $row['id_product_attribute'])
                );
            }
            $res[$key]['quantity'] = Cache::retrieve($cache_key);
        }
        return $res;
    }

}
