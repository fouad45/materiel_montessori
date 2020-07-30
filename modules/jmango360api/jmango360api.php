<?php
/**
 * @author JMango360 Operations BV
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class jmango360api
 */
class Jmango360api extends Module
{
    private $html = '';
    private $postErrors = array();
    private $smartAppBannerScript = 'SMART_APP_BANNER_SCRIPT_';
    private $smartAppBannerSetting = 'SMART_APP_BANNER_SETTING_';
    private $smartAppBannerHeader = 'SMART_APP_BANNER_HEADER_';
    private $smartAppBannerCss = 'SMART_APP_BANNER_CSS_';
    public $name;

    public function __construct()
    {
        $this->module_key = '3a5390116602dffd8707cc6674a08b06';
        $this->name = 'jmango360api';
        $this->tab = 'mobile';
        $this->version = '1.21.5';
        $this->author = 'Prestashop Partners';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('JMango360 Mobile App Builder');
        $this->description = $this->l('Create a native iOS and Android mobile app for your Prestashop webstore using the JMango360 plugin.');
    }

    public function parentInstall16()
    {
        Hook::exec('actionModuleInstallBefore', array('object' => $this));

        // Check module name validation
        if (!Validate::isModuleName($this->name)) {
            $this->_errors[] = Tools::displayError('Unable to install the module (Module name is not valid).');
            return false;
        }

        // Check PS version compliancy
        if (!$this->checkCompliancy()) {
            $this->_errors[] = Tools::displayError('The version of your module is not compliant with your PrestaShop version.');
            return false;
        }

        // Check module dependencies
        if (count($this->dependencies) > 0) {
            foreach ($this->dependencies as $dependency) {
                if (!Db::getInstance()->getRow('SELECT `id_module` FROM `' . _DB_PREFIX_ . 'module` WHERE LOWER(`name`) = \'' . pSQL(Tools::strtolower($dependency)) . '\'')) {
                    $error = Tools::displayError('Before installing this module, you have to install this/these module(s) first:') . '<br />';
                    foreach ($this->dependencies as $d) {
                        $error .= '- ' . $d . '<br />';
                    }
                    $this->_errors[] = $error;
                    return false;
                }
            }
        }

        // Check if module is installed
        $result = Module::isInstalled($this->name);
        if ($result) {
            $this->_errors[] = Tools::displayError('This module has already been installed.');
            return false;
        }

        // Install overrides
        try {
            $this->installOverrides();
        } catch (Exception $e) {
        }

        if (!$this->installControllers()) {
            return false;
        }

        // Install module and retrieve the installation id
        $result = Db::getInstance()->insert($this->table, array('name' => $this->name, 'active' => 1, 'version' => $this->version));
        if (!$result) {
            $this->_errors[] = Tools::displayError('Technical error: PrestaShop could not install this module.');
            return false;
        }
        $this->id = Db::getInstance()->Insert_ID();

        Cache::clean('Module::isInstalled' . $this->name);

        // Enable the module for current shops in context
        $this->enable();

        // Permissions management
        Db::getInstance()->execute('
			INSERT INTO `' . _DB_PREFIX_ . 'module_access` (`id_profile`, `id_module`, `view`, `configure`, `uninstall`) (
				SELECT id_profile, ' . (int)$this->id . ', 1, 1, 1
				FROM ' . _DB_PREFIX_ . 'access a
				WHERE id_tab = (
					SELECT `id_tab` FROM ' . _DB_PREFIX_ . 'tab
					WHERE class_name = \'AdminModules\' LIMIT 1)
				AND a.`view` = 1)');

        Db::getInstance()->execute('
			INSERT INTO `' . _DB_PREFIX_ . 'module_access` (`id_profile`, `id_module`, `view`, `configure`, `uninstall`) (
				SELECT id_profile, ' . (int)$this->id . ', 1, 0, 0
				FROM ' . _DB_PREFIX_ . 'access a
				WHERE id_tab = (
					SELECT `id_tab` FROM ' . _DB_PREFIX_ . 'tab
					WHERE class_name = \'AdminModules\' LIMIT 1)
				AND a.`view` = 0)');

        // Adding Restrictions for client groups
        Group::addRestrictionsForModule($this->id, Shop::getShops(true, null, true));
        Hook::exec('actionModuleInstallAfter', array('object' => $this));

        if (Module::$update_translations_after_install) {
            $this->updateModuleTranslations();
        }

        if (!$this->installTab('AdminParentPreferences', 'Jmango360Settings', 'JMango360 Settings')) {
            return false;
        }

        if (!$this->installTab('AdminParentCustomer', 'AdminJmango360Push', 'JMango360 Push Message', 0)) {
            return false;
        }

        return true;
    }

    public function parentInstall17()
    {
        Hook::exec('actionModuleInstallBefore', array('object' => $this));

        // Check module name validation
        if (!Validate::isModuleName($this->name)) {
            $this->_errors[] = Tools::displayError('Unable to install the module (Module name is not valid).');
            return false;
        }

        // Check PS version compliancy
        if (!$this->checkCompliancy()) {
            $this->_errors[] = Tools::displayError('The version of your module is not compliant with your PrestaShop version.');
            return false;
        }

        // Check module dependencies
        if (count($this->dependencies) > 0) {
            foreach ($this->dependencies as $dependency) {
                if (!Db::getInstance()->getRow('SELECT `id_module` FROM `' . _DB_PREFIX_ . 'module` WHERE LOWER(`name`) = \'' . pSQL(Tools::strtolower($dependency)) . '\'')) {
                    $error = Tools::displayError('Before installing this module, you have to install this/these module(s) first:') . '<br />';
                    foreach ($this->dependencies as $d) {
                        $error .= '- ' . $d . '<br />';
                    }
                    $this->_errors[] = $error;
                    return false;
                }
            }
        }

        // Check if module is installed
        $result = (new PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider(new PrestaShop\PrestaShop\Adapter\LegacyLogger(), $this->getTranslator()))->isInstalled($this->name);
        if ($result) {
            $this->_errors[] = Tools::displayError('This module has already been installed.');
            return false;
        }

        if (!$this->installControllers()) {
            $this->_errors[] = Tools::displayError('Could not install module controllers.');
            return false;
        }

        // Install module and retrieve the installation id
        $result = Db::getInstance()->insert($this->table, array('name' => $this->name, 'active' => 1, 'version' => $this->version));
        if (!$result) {
            $this->_errors[] = Tools::displayError('Technical error: PrestaShop could not install this module.');
            return false;
        }
        $this->id = Db::getInstance()->Insert_ID();

        Cache::clean('Module::isInstalled' . $this->name);

        // Enable the module for current shops in context
        $this->enable17();

        // Permissions management
        foreach (array('CREATE', 'READ', 'UPDATE', 'DELETE') as $action) {
            $slug = 'ROLE_MOD_MODULE_' . Tools::strtoupper($this->name) . '_' . $action;

            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'authorization_role` (`slug`) VALUES ("' . $slug . '")'
            );

            Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . 'module_access` (`id_profile`, `id_authorization_role`) (
                    SELECT id_profile, "' . Db::getInstance()->Insert_ID() . '"
                    FROM ' . _DB_PREFIX_ . 'access a
                    LEFT JOIN `' . _DB_PREFIX_ . 'authorization_role` r
                    ON r.id_authorization_role = a.id_authorization_role
                    WHERE r.slug = "ROLE_MOD_TAB_ADMINMODULES_' . $action . '"
            )');
        }

        // Adding Restrictions for client groups
        Group::addRestrictionsForModule($this->id, Shop::getShops(true, null, true));
        Hook::exec('actionModuleInstallAfter', array('object' => $this));

        if (Module::$update_translations_after_install) {
            $this->updateModuleTranslations();
        }

        if (!$this->installTab('ShopParameters', 'Jmango360Settings', 'JMango360 Settings')) {
            return false;
        }

        if (!$this->installTab('AdminParentCustomer', 'AdminJmango360Push', 'JMango360 Push Message', 0)) {
            return false;
        }

        return true;
    }

    public function enable17($force_all = false)
    {
        // Retrieve all shops where the module is enabled
        $list = Shop::getContextListShopID();
        if (!$this->id || !is_array($list)) {
            return false;
        }

        $sql = 'SELECT `id_shop` FROM `' . _DB_PREFIX_ . 'module_shop`
                WHERE `id_module` = ' . (int)$this->id .
            ((!$force_all) ? ' AND `id_shop` IN(' . implode(', ', $list) . ')' : '');

        // Store the results in an array
        $items = array();
        if ($results = Db::getInstance($sql)->executeS($sql)) {
            foreach ($results as $row) {
                $items[] = $row['id_shop'];
            }
        }

        if ($this->getOverrides() != null) {
            // Install overrides
            try {
                $this->installOverrides();
            } catch (Exception $e) {
            }
        }

        // Enable module in the shop where it is not enabled yet
        foreach ($list as $id) {
            if (!in_array($id, $items)) {
                Db::getInstance()->insert('module_shop', array(
                    'id_module' => $this->id,
                    'id_shop' => $id,
                ));
            }
        }

        return true;
    }

    public function install()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $result = $this->parentInstall17();
        } else {
            $result = $this->parentInstall16();
        }

        if (!$result) {
            parent::uninstall();
            return false;
        }

        $result = include(dirname(__FILE__) . '/sql/install.php');

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $result = $result && (bool)$this->registerHook('addWebserviceResources');
            $result = $result && (bool)$this->registerHook('actionOutputHTMLBefore');
        }

        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $result = $result && (bool)$this->registerHook('actionProductSave');
        }

        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $result = $result && (bool)$this->registerHook('actionProductDelete');
        }

        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $result = $result && (bool)$this->registerHook('displayOrderConfirmation');
        }

        $result = $result &&
            (bool)$this->registerHook('displayHeader') &&
            (bool)$this->registerHook('displayFooter') &&
            (bool)$this->registerHook('displayAdminProductsExtra') &&
            (bool)$this->registerHook('actionProductUpdate') &&
            (bool)$this->registerHook('displayFooterProduct') &&
            (bool)$this->registerHook('displayBackOfficeHeader') &&
            (bool)$this->registerHook('displayAfterBodyOpeningTag');

        if (strpos($_SERVER['HTTP_HOST'], 'tricotcafe.com') === false) {
            $result = $result && (bool)$this->createDatabase();
        }

        $result = $result && (bool)$this->createBraintreeOrderStatuses();

        if (Db::getInstance()->getMsgError() && Db::getInstance()->getMsgError() != '') {
            $this->context->controller->errors[] = Db::getInstance()->getMsgError();
        }

        if ($this->context->controller->errors) {
            parent::uninstall();
            return false;
        }

        /**
         * https://jmango360.atlassian.net/wiki/spaces/JMAN/pages/493551652
         */
        $result = $result &&
            (bool)$this->registerHook('actionAdminOrdersListingFieldsModifier') &&
            //(bool)$this->registerHook('actionAdminOrderPreferencesOptionsModifier') &&
            //(bool)$this->registerHook('actionAdminOrderPreferencesControllerUpdate_optionsBefore') &&
            (bool)$this->registerHook('actionValidateOrder');

        if (Configuration::get('JM360_SHOW_ORDERED_FROM') === false) {
            Configuration::updateValue('JM360_SHOW_ORDERED_FROM', 1);
        }

        return (bool)$result;
    }


    public function parentUninstall16()
    {
        // Check module installation id validation
        if (!Validate::isUnsignedId($this->id)) {
            $this->_errors[] = Tools::displayError('The module is not installed.');
            return false;
        }

        // Uninstall overrides
        if (!file_exists(_PS_MODULE_DIR_ . 'jmango360api/classes/webservice/WebserviceSpecificManagementJapi.php')) {
            if (!$this->uninstallOverrides()) {
                return false;
            }
        }

        // Retrieve hooks used by the module
        $sql = 'SELECT `id_hook` FROM `' . _DB_PREFIX_ . 'hook_module` WHERE `id_module` = ' . (int)$this->id;
        $result = Db::getInstance()->executeS($sql);
        foreach ($result as $row) {
            $this->unregisterHook((int)$row['id_hook']);
            $this->unregisterExceptions((int)$row['id_hook']);
        }

        foreach ($this->controllers as $controller) {
            $page_name = 'module-' . $this->name . '-' . $controller;
            $meta = Db::getInstance()->getValue('SELECT id_meta FROM `' . _DB_PREFIX_ . 'meta` WHERE page="' . pSQL($page_name) . '"');
            if ((int)$meta > 0) {
                Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'theme_meta` WHERE id_meta=' . (int)$meta);
                Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'meta_lang` WHERE id_meta=' . (int)$meta);
                Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'meta` WHERE id_meta=' . (int)$meta);
            }
        }

        // Disable the module for all shops
        $this->disable(true);

        // Delete permissions module access
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'module_access` WHERE `id_module` = ' . (int)$this->id);

        // Remove restrictions for client groups
        Group::truncateRestrictionsByModule($this->id);

        // Uninstall the module
        if (Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'module` WHERE `id_module` = ' . (int)$this->id)) {
            Cache::clean('Module::isInstalled' . $this->name);
            Cache::clean('Module::getModuleIdByName_' . pSQL($this->name));
            return true;
        }

        return false;
    }

    public function parentUninstall17()
    {
        // Check module installation id validation
        if (!Validate::isUnsignedId($this->id)) {
            $this->_errors[] = Tools::displayError('The module is not installed.');
            return false;
        }

        // Uninstall overrides
        if (!$this->uninstallOverrides()) {
            return false;
        }

        // Retrieve hooks used by the module
        $sql = 'SELECT `id_hook` FROM `' . _DB_PREFIX_ . 'hook_module` WHERE `id_module` = ' . (int)$this->id;
        $result = Db::getInstance()->executeS($sql);
        foreach ($result as $row) {
            $this->unregisterHook((int)$row['id_hook']);
            $this->unregisterExceptions((int)$row['id_hook']);
        }

        foreach ($this->controllers as $controller) {
            $page_name = 'module-' . $this->name . '-' . $controller;
            $meta = Db::getInstance()->getValue('SELECT id_meta FROM `' . _DB_PREFIX_ . 'meta` WHERE page="' . pSQL($page_name) . '"');
            if ((int)$meta > 0) {
                Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'meta_lang` WHERE id_meta=' . (int)$meta);
                Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'meta` WHERE id_meta=' . (int)$meta);
            }
        }

        // Disable the module for all shops
        $this->disable(true);

        // Delete permissions module access
        $roles = Db::getInstance()->executeS('SELECT `id_authorization_role` FROM `' . _DB_PREFIX_ . 'authorization_role` WHERE `slug` LIKE "ROLE_MOD_MODULE_' . Tools::strtoupper($this->name) . '_%"');

        if (!empty($roles)) {
            foreach ($roles as $role) {
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'module_access` WHERE `id_authorization_role` = ' . $role['id_authorization_role']
                );
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'authorization_role` WHERE `id_authorization_role` = ' . $role['id_authorization_role']
                );
            }
        }

        // Remove restrictions for client groups
        Group::truncateRestrictionsByModule($this->id);

        // Uninstall the module
        if (Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'module` WHERE `id_module` = ' . (int)$this->id)) {
            Cache::clean('Module::isInstalled' . $this->name);
            Cache::clean('Module::getModuleIdByName_' . pSQL($this->name));
            return true;
        }

        return false;
    }

    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');

        // Uninstall admin tab
        $this->uninstallTab('AdminJmango360Push');

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            return $this->parentUninstall17();
        } else {
            return $this->parentUninstall16();
        }
    }

    public function hookActionOutputHTMLBefore($args = array())
    {
        $setting = json_decode(Configuration::get($this->smartAppBannerSetting));
        if ($setting != null) {
            $metadata = sprintf("<meta name=\"apple-itunes-app\" content=\"app-id=%s\">", $setting->ios_app_id);
            $metadata .= sprintf("<link rel=\"apple-touch-icon\" href=\"%s\">", $setting->apple_touch_icon);

            if (strcmp(Tools::getValue('controller'), 'category') == 0 ||
                strcmp(Tools::getValue('controller'), 'index') == 0 ||
                strcmp(Tools::getValue('controller'), 'cms') == 0 ||
                strcmp(Tools::getValue('controller'), 'product') == 0) {
                $pos = strpos($args['html'], '<head>') + 6;
                $args['html'] = substr_replace($args['html'], $metadata, $pos, 0);
            }
        }
    }

    /**
     * Add setting "Show Ordered From" to "Order Settings"
     *
     * @param array $args
     */
    public function hookActionAdminOrderPreferencesOptionsModifier($args = array())
    {
        if (isset($args['options']) && !isset($args['options']['general']['fields']['JM360_SHOW_ORDERED_FROM'])) {
            $args['options']['general']['fields']['JM360_SHOW_ORDERED_FROM'] = $this->_getShowOrderedFromSetting();
        }
    }

    /**
     * Get Show Ordered from in order list setting data
     *
     * @return array
     */
    protected function _getShowOrderedFromSetting()
    {
        return array(
            'title' => $this->l('Show Ordered from in order list'),
            'hint' => $this->l('Show Ordered from in order list'),
            'validation' => 'isBool',
            'cast' => 'intval',
            'type' => 'bool'
        );
    }

    /**
     * Prepare setting "Show Ordered From" before save settings
     *
     * @param array $args
     * @throws
     */
    public function hookactionAdminOrderPreferencesControllerUpdateOptionsBefore($args = array())
    {
        if (isset($args['controller'])) {
            $refObj = new ReflectionObject($args['controller']);
            $refProperty = $refObj->getProperty('fields_options');
            $refProperty->setAccessible(true);
            $fields_options = $refProperty->getValue($args['controller']);

            if (is_array($fields_options) && !isset($fields_options['general']['fields']['JM360_SHOW_ORDERED_FROM'])) {
                $fields_options['general']['fields']['JM360_SHOW_ORDERED_FROM'] = $this->_getShowOrderedFromSetting();

                $refProperty->setValue($args['controller'], $fields_options);
            }
        }
    }

    /**
     * Add "Ordered from" column to admin Orders table
     *
     * @param array $args
     */
    public function hookActionAdminOrdersListingFieldsModifier($args = array())
    {
        if (!Configuration::get('JM360_SHOW_ORDERED_FROM')) {
            return;
        }

        if (isset($args['fields']) && !isset($args['fields']['is_jm360'])) {
            $column = array(
                'is_jm360' => array(
                    'title' => $this->l('Ordered from'),
                    'orderby' => false,
                    'search' => true,
                    'type' => 'select',
                    'list' => array('1' => 'JMango360'),
                    'filter_key' => 'jm360!mobile',
                    'filter_type' => 'int'
                )
            );

            /**
             * Find "Reference" column index
             */
            $index = 0;
            foreach ($args['fields'] as $field => $fieldCfg) {
                $index++;
                if ($field == 'reference') {
                    break;
                }
            }

            /**
             * Insert our column after "Reference" column
             */
            $args['fields'] = array_slice($args['fields'], 0, $index, true)
                + $column
                + array_slice($args['fields'], $index, count($args['fields']) - 1, true);
        }

        /**
         * Join our table
         */
        if (isset($args['join']) && strpos($args['join'], 'jmango360_order') === false) {
            $join = trim($args['join']);
            $args['join'] = $join . "\n\t\tLEFT JOIN `" . _DB_PREFIX_ . "jmango360_order` AS jm360 ON jm360.id_order = a.id_order";
        }

        /**
         * Select from our table
         */
        if (isset($args['select']) && strpos($args['select'], 'JMango360') === false) {
            $select = trim($args['select'], ',');
            $args['select'] = "IF(jm360.`mobile`=1, 'JMango360', '') AS `is_jm360`,\n\t\t" . $select;
        }
    }

    /**
     * Mark order as mobile
     *
     * @param array $args
     * @throws
     */
    public function hookactionValidateOrder($args = array())
    {
        if (isset($args['cart']) && isset($args['order'])) {
            if ($args['cart']->id && $args['order']->id) {
                try {
                    DB::getInstance()->update(
                        'jmango360_order',
                        array(
                            'id_order' => (int)$args['order']->id,
                            'mobile' => 1
                        ),
                        'id_cart=' . (int)$args['cart']->id
                    );
                } catch (Exception $e) {
                }
            }
        }
    }

    public function hookActionProductSave($args = array())
    {
    }

    public function hookActionProductDelete($args = array())
    {
    }

    public function hookAddWebserviceResources()
    {
        return array(
            'japi' => array(
                'description' => 'JMango360 Extended APIs',
                'specific_management' => true
            )
        );
    }

    public function hookDisplayOrderConfirmation()
    {
        header('jmango360: order-success');
        if (Tools::getIsset('id_order')) {
            $order = new Order(Tools::getValue('id_order'));
            return sprintf('<meta name="%s" content="%s">', 'prestashop-order-id', $order->reference);
        }
    }

    public function hookDisplayHeader($params)
    {
        $context = Context::getContext();
        $metadata = '';

        if (Tools::getValue('module') == $this->name && Tools::getValue('controller') == 'jmcheckout') {
            if (Module::isEnabled('sendcloud')) {
                $script = $this->getShopConfiguration($context->shop, 'SENDCLOUD_SPP_SCRIPT');
                $context->controller->addCSS(_PS_MODULE_DIR_ . 'sendcloud/views/css/front.css');
                $context->controller->addJS($script, false);
            }

            if (Module::isEnabled('soflexibilite')) {
                require_once _PS_MODULE_DIR_ . '/soflexibilite/soflexibilite.php';
                $soflexModule = new SoFlexibilite();
                if (version_compare($soflexModule->version, '4.0.02', '<')) {
                    $this->addCSS('soflexibilite/views/css/soflexibilite.css');
                    $this->addCSS('soflexibilite/views/css/soflexibilite' . Configuration::get('SOFLEXIBILITE_THEME') . '.css');
                    $this->addCSS('soflexibilite/views/css/jquery.qtip.min.css');
                    $this->addJs('soflexibilite/views/js/jquery.qtip.min.js', array('attributes' => 'defer'));
                    $this->addJs('soflexibilite/views/js/front_flexibilite.js', array('attributes' => 'defer'));
                    $this->addJs('soflexibilite/views/js/fcts.js', array('attributes' => 'defer'));

                    if (Configuration::get('SOFLEXIBILITE_GMAP')) {
                        $this->addJs('https://maps.google.com/maps/api/js?libraries=places&key=' . Configuration::get('SOFLEXIBILITE_GMAP_KEY'), array(
                            'server' => 'remote'
                        ));
                    }
                }
            }

            if (Module::isEnabled('mondialrelayadvanced')) {
                $this->addJs('mondialrelayadvanced/views/js/configure.js', array('attributes' => 'defer'));
                $this->addJs('mondialrelayadvanced/views/js/admin_orders.js', array('attributes' => 'defer'));
                $this->addJs('mondialrelayadvanced/views/js/plug.min.js', array('attributes' => 'defer'));

                $this->addCSS('mondialrelayadvanced/views/css/configure.css');
                $this->addCSS('mondialrelayadvanced/views/css/jquery.qtip.min.css');
                $this->addCSS('mondialrelayadvanced/views/css/front.css');
                $this->addCSS('mondialrelayadvanced/views/views/css/ps-icon-pack.min.css');
                $this->addCSS('mondialrelayadvanced/views/css/admin_orders.css');
            }

            if (Module::isEnabled('stripe_official')) {
                if (Configuration::get('STRIPE_ENABLE_IDEAL')
                    || Configuration::get('STRIPE_ENABLE_GIROPAY')
                    || Configuration::get('STRIPE_ENABLE_BANCONTACT')
                    || Configuration::get('STRIPE_ENABLE_SOFORT')
                ) {
                    $context->controller->addJS(_PS_MODULE_DIR_ . 'stripe_official/views/js/stripe-push-methods.js');
                }

                if (version_compare(_PS_VERSION_, '1.6', '<')) {
                    $context->controller->addCSS(_PS_MODULE_DIR_ . 'stripe_official/views/css/front_15.css');
                } else {
                    $context->controller->addCSS(_PS_MODULE_DIR_ . 'stripe_official/views/css/front.css');
                }

                //$context->controller->addJS(_PS_MODULE_DIR_ . 'stripe_official/views/js/jquery.the-modal.js');
                $context->controller->addCSS(_PS_MODULE_DIR_ . 'stripe_official/views/css/the-modal.css', 'all');
                $context->controller->addCSS(_PS_MODULE_DIR_ . 'stripe_official/views/css/front.css', 'all');
                $context->controller->addJS(_PS_MODULE_DIR_ . 'stripe_official/views/js/payment_validation.js');

                $metadata .= $context->smarty->fetch(_PS_MODULE_DIR_ . 'stripe_official/views/templates/hook/header.tpl');
            }

            if (Module::isEnabled('amzpayments')) {
                require_once _PS_MODULE_DIR_ . 'amzpayments/amzpayments.php';

                $amzPayments = new AmzPayments();

                Media::addJsDef(array('AMZ_USE_ACCOUNT_HEAD' => ''));
                Media::addJsDef(array('AMZ_USE_ACCOUNT_BODY' => ''));
                Media::addJsDef(array('AMZ_SHOW_REGISTRATION_PAGE' => ''));
                Media::addJsDef(array('AMZACTIVE' => ''));
                Media::addJsDef(array('AMZSELLERID' => ''));
                Media::addJsDef(array('AMZ_NO_TOKEN_AJAX' => ''));
                Media::addJsDef(array('AMZ_SHOW_IN_CART_POPUP' => ''));
                Media::addJsDef(array('AMZ_CREATE_ACCOUNT_EXP' => ''));
                Media::addJsDef(array('AMZ_BUTTON_TYPE_LOGIN' => ''));
                Media::addJsDef(array('AMZ_BUTTON_TYPE_PAY' => ''));
                Media::addJsDef(array('AMZ_BUTTON_SIZE_LPA' => ''));
                Media::addJsDef(array('AMZ_BUTTON_COLOR_LPA' => ''));
                Media::addJsDef(array('AMZ_BUTTON_COLOR_LPA_NAVI' => ''));
                Media::addJsDef(array('AMZ_WIDGET_LANGUAGE' => ''));
                Media::addJsDef(array('CLIENT_ID' => ''));
                Media::addJsDef(array('AMZ_SHOW_AS_PAYMENT_METHOD' => ''));
                Media::addJsDef(array('useRedirect' => ''));
                Media::addJsDef(array('LPA_MODE' => ''));
                Media::addJsDef(array('REDIRECTAMZ' => ''));
                Media::addJsDef(array('LOGINREDIRECTAMZ_CHECKOUT' => ''));
                Media::addJsDef(array('LOGINREDIRECTAMZ' => ''));
                Media::addJsDef(array('is_logged' => ''));
                Media::addJsDef(array('AMZACCTK' => ''));
                Media::addJsDef(array('SETUSERAJAX' => ''));

                $this->addJs('amzpayments/views/js/amzpayments.js');
                $this->addJs('https://static-eu.payments-amazon.com/OffAmazonPayments/' . $amzPayments->getRegionalCodeForURL() . '/sandbox/lpa/js/Widgets.js?sellerId=' . $amzPayments->merchant_id, array(
                    'server' => 'remote'
                ));
            }

            if (Module::isEnabled('chronopost')) {
                $module_uri = 'chronopost';
                $this->context->controller->addCSS($module_uri . '/views/css/chronorelais.css');
                $this->context->controller->addCSS($module_uri . '/views/css/chronordv.css');
                if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                    $this->addJS($module_uri . '/views/js/chronorelais.js');
                    $this->addJS($module_uri . '/views/js/chronordv.js');
                } else {
                    $this->addJS($module_uri . '/views/js/chronorelais-17.js', array('attributes' => 'defer'));
                    $this->addJS($module_uri . '/views/js/chronordv-17.js', array('attributes' => 'defer'));
                }

                $metadata .= '<script src="https://maps.googleapis.com/maps/api/js?key=' . Configuration::get('CHRONOPOST_MAP_APIKEY') . '"></script>';
            }
        }

        if (strcmp(Tools::getValue('controller'), 'category') == 0 ||
            strcmp(Tools::getValue('controller'), 'index') == 0 ||
            strcmp(Tools::getValue('controller'), 'cms') == 0 ||
            strcmp(Tools::getValue('controller'), 'product') == 0) {
            $this->smartAppBannerSetting .= $context->shop->getContextShopId() . '_' . $context->language->id;
            $this->smartAppBannerHeader .= $context->shop->getContextShopId() . '_' . $context->language->id;
            $setting = json_decode(Configuration::get($this->smartAppBannerSetting));
            if ($setting != null) {
                $metadata .= sprintf("<meta name=\"apple-itunes-app\" content=\"app-id=%s\">", $setting->ios_app_id);
                $metadata .= sprintf("<meta name=\"google-play-app\" content=\"app-id=%s\">", $setting->android_app_id);
                $context->controller->addCSS(_PS_MODULE_DIR_ . '/jmango360api/views/css/smartAppBanner/smart-app-banner.css');
                $metadata .= sprintf("<link rel=\"apple-touch-icon\" href=\"%s\">", $setting->apple_touch_icon);
                $metadata .= sprintf("<link rel=\"android-touch-icon\" href=\"%s\" />", $setting->android_touch_icon);
                if (Configuration::get($this->smartAppBannerHeader) == 1) {
                    $metadata .= $this->hookDisplayFooter(array());
                }
            }
        }

        if (strcmp(Tools::getValue('controller'), 'history') == 0) {
            $orders = Order::getCustomerOrders($context->customer->id);
            if ($orders) {
                $last_order = $orders[0];
                if ($last_order['module'] === 'free_order') {
                    $metadata .= sprintf('<meta name="%s" content="%s">', 'prestashop-order-id', $last_order['reference']);
                }
            }
        }

        if ($metadata !== '') {
            return $metadata;
        }
    }

    /**
     * Proxy for incluse JS file
     *
     * @param string $file
     * @param array $config
     */
    protected function addJs($file, $config = array())
    {
        $context = Context::getContext();

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $context->controller->registerJavascript($file, 'modules/' . $file, array_merge(array(
                'position' => 'head', 'priority' => 999
            ), $config));
        } else {
            $context->controller->addJS(_PS_MODULE_DIR_ . $file);
        }
    }

    /**
     * Proxy for incluse CSS file
     *
     * @param string $file
     * @param array $config
     */
    protected function addCss($file, $config = array())
    {
        $context = Context::getContext();

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $context->controller->registerStylesheet($file, 'modules/' . $file, array_merge(array(
                'position' => 'head', 'priority' => 999
            ), $config));
        } else {
            $context->controller->addJS(_PS_MODULE_DIR_ . $file);
        }
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        $context = Context::getContext();
        $context->controller->addJS('/modules/jmango360api/views/js/googleTag.js', false);
    }

    public function hookDisplayFooter($params)
    {
        if (strcmp(Tools::getValue('controller'), 'category') == 0 ||
            strcmp(Tools::getValue('controller'), 'index') == 0 ||
            strcmp(Tools::getValue('controller'), 'cms') == 0 ||
            strcmp(Tools::getValue('controller'), 'product') == 0) {
            $context = Context::getContext();
            $this->smartAppBannerScript .= $context->shop->getContextShopId() . '_' . $context->language->id;
            $this->smartAppBannerHeader .= $context->shop->getContextShopId() . '_' . $context->language->id;
            $this->smartAppBannerCss .= $context->shop->getContextShopId() . '_' . $context->language->id;
            $smartAppBannerScript = Configuration::get($this->smartAppBannerScript);
            $smartAppBannerSetting = json_decode(Configuration::get($this->smartAppBannerSetting));
            $smartAppBannerHeader = Configuration::get($this->smartAppBannerHeader);
            $smartAppBannerCss = Configuration::get($this->smartAppBannerCss);

            if ($smartAppBannerHeader != 1 && $smartAppBannerScript != null && $smartAppBannerSetting != null) {
                //global $smarty;
                $this->smarty->assign(array(
                    'baseUrl' => $context->shop->getBaseURL(true),
                    'customCss' => $smartAppBannerCss
                ));
                if ($smartAppBannerScript != null) {
                    $this->smarty->assign('smartAppBannerScript', $smartAppBannerScript);
                    $this->smarty->assign('smartAppBannerSetting', $smartAppBannerSetting);
                } else {
                    $this->smarty->assign('smartAppBannerScript', null);
                    $this->smarty->assign('smartAppBannerSetting', null);
                }
                return $this->display(__FILE__, 'smartAppBanner/smart-app-banner.tpl');
            }
        }

        if (Module::isEnabled('soflexibilite')) {
            $carrier_conf_keys = array(
                'SOFLEXIBILITE_DOM_ID',
                'SOFLEXIBILITE_DOS_ID',
                'SOFLEXIBILITE_A2P_ID',
                'SOFLEXIBILITE_BPR_ID'
            );
            $carrier_id_refs = array_filter(array_map('intval', Configuration::getMultiple(
                $carrier_conf_keys,
                null,
                null,
                null
            )));
            $this->context->smarty->assign(array(
                'sf_carriers_id' => SoFlexibiliteCarrier::getFlexibiliteCarriersId($carrier_id_refs),
                'sf_get_delivery_points_url' => Tools::getHttpHost(true) . __PS_BASE_URI__ .
                    'modules/soflexibilite' . '/functions/get_delivery_points.php',
                'sf_save_delivery_point_url' => Tools::getHttpHost(true) . __PS_BASE_URI__ .
                    'modules/soflexibilite' . '/functions/save_delivery_point.php',
                'sf_update_delivery_info_url' => Tools::getHttpHost(true) . __PS_BASE_URI__ .
                    'modules/soflexibilite' . '/functions/update_delivery_info.php',
                'sf_img_dir' => Tools::getHttpHost(true) . __PS_BASE_URI__ . 'modules/soflexibilite/views/img/'
            ));

            $html = '';
            if (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.6', '=')) {
                $html .= $this->fetch(_PS_MODULE_DIR_ . '/soflexibilite/views/templates/front/displayaftercarrier.tpl');
            }

            $html .= $this->fetch(_PS_MODULE_DIR_ . '/soflexibilite/views/templates/front/displayfooter.tpl');

            return $html;
        }
    }

    /**
     * Return plugin configuration page
     * Temporary disabled
     *
     * @return string
     */
    public function getContent()
    {
        $this->html = '';

        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->postErrors as $err) {
                    $this->html .= $this->displayError($err);
                }
            }
        }

        $this->html .= $this->displayForm();

        return $this->html;
    }

    private function _postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            //
        }
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            //
        }

        $this->html .= $this->displayConfirmation($this->l('Settings updated'));
    }

    protected function displayForm()
    {
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/libs/md5.min.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/libs/sha1.min.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/libs/base64.min.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/libs/jquery.validationEngine.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/libs/jquery.validationEngine-en.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/libs/jquery.validationEngine-nl.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/libs/bootstrap-select.js');

        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'jmango360api/views/css/configuration-page/app-config.css');
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'jmango360api/views/css/configuration-page/create-account.css');
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'jmango360api/views/css/configuration-page/pres_qs_business_question.css');
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'jmango360api/views/css/configuration-page/pretashop-qs-forgot-password.css');
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'jmango360api/views/css/configuration-page/pretashop-qs-import.css');
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'jmango360api/views/css/configuration-page/pretashop-qs-login.css');
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'jmango360api/views/css/configuration-page/pretashop-qs-preview.css');
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'jmango360api/views/css/font-awesome-4.7.0/css/font-awesome.css');

//        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/bootstrap-select.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/configuration.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/tiny-tip.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/pres_qs_app_config.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/pres_qs_create_account.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/header_pretashop_backend_view.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/pres_qs_business_question.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/pres_qs_import_data.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/pres_qs_preview_app.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/pres_qs_hurray.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/pres_qs_login.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/pres_qs_login2.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/pretashop_qs_forgot_password.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/pres_qs_logout.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration-page/localization.js');

        $data = $this->prepareData();

        // dummy data
//        $data['shopUrl'] = "https://prestashop17.jmango360.com/";
//        $data['apiKey'] = "DISW7M8L2MUDCJV4ZQEHU67QAI7P7H8J";
        $default_lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $current_lang = $this->context->language->iso_code;

        $this->context->smarty->assign('data', json_encode($data));
        $this->context->smarty->assign('moduleLocation', _PS_MODULE_DIR_);
        $this->context->smarty->assign('dev_mode', Configuration::get('JM_DEV_MODE'));
        $this->context->smarty->assign('ticket', Configuration::get('JM_TICKET'));
        $this->context->smarty->assign('default_lang', json_encode($default_lang));
        $this->context->smarty->assign('current_lang', json_encode($current_lang));

        $content = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configuration.tpl');
        return $content;
    }

    public function prepareData()
    {
        $data = array();
        // set ws key and ticket
        $wskey = new WebserviceKey(Configuration::get('JM_WS_KEY_ID'));
        $ticket = Configuration::get('JM_TICKET');
        $appKey = Configuration::get('JM_APP_KEY');
        $key = $wskey->key;
        if (!WebserviceKey::keyExists($key)) {
            // create new key if not exist
            $key = $this->createNewWSKey();
        }
        $data['apiKey'] = $key;
        $data['ticket'] = $ticket ? $ticket : null;
//        $data['ticket'] = "ST-6217-HjCMBnI3kYSGrGoe1fRg-integration.jmango360.com";
        $data['appKey'] = $appKey ? $appKey : null;

        // ser user details
        $employee = new EmployeeCore($this->context->cookie->id_employee);

        $data['firstName'] = $employee->firstname;
        $data['lastName'] = $employee->lastname;
        $email = Configuration::get('JM_EMAIL');
        if ($email != null && !empty($email)) {
            $data['email'] = $email;
        } else {
            $data['email'] = $employee->email;
        }

        // set URL
        $url = $this->context->shop->getBaseURL(true);
        $data['shopUrl'] = $url;

        // set Languages
        $languages_available = Language::getLanguages(true);
//        $data['lang'] = $this->context->language->iso_code;
        if (Configuration::get("JM_ID_LANG") != null && (int)Configuration::get("JM_ID_LANG") != 0) {
            $default_lang = new Language((int)Configuration::get("JM_ID_LANG"));
        } else {
            $default_lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        }
        $data['current_language']['iso_code'] = $default_lang->iso_code;
        $data['current_language']['id_lang'] = $default_lang->id;

        $languages = array();
        foreach ($languages_available as $lang) {
            $l = array();
            $l['iso_code'] = $lang['iso_code'];
            $l['id_lang'] = (int)$lang['id_lang'];
            $l['name'] = $lang['name'];
            $languages[] = $l;
        }
        $data['languages'] = $languages;

        // set shops
        if (Configuration::get("JM_ID_SHOP") != null && (int)Configuration::get("JM_ID_SHOP") != 0) {
            $default_shop = new Shop(Configuration::get("JM_ID_SHOP"));
            $data['current_shop']['id_shop'] = $default_shop->id;
            $data['current_shop']['name'] = $default_shop->name;
        } else {
            $data['current_shop']['id_shop'] = $this->context->shop->id;
            $data['current_shop']['name'] = $this->context->shop->name;
        }
//        $data['storeId'] = $this->context->shop->id;
        $shops = ShopCore::getShops();
        $shop_info = array();
        foreach ($shops as $shop) {
            $s = array();
            $s['id_shop'] = $shop['id_shop'];
            $s['name'] = $shop['name'];
            $shop_info[] = $s;
        }
        $data['shops'] = $shop_info;

        return $data;
    }


    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_PS_MODULE_DIR_ . 'jmango360api/views/js/configuration.js');
    }

    private function generateRandomString($length = 32)
    {
        $characters = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';

        $charactersLength = Tools::strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function createNewWSKey()
    {
        $wskey = new WebserviceKeyCore(Configuration::get('JM_WS_KEY_ID'));
        $jmkey = $wskey->key;
        if (!WebserviceKeyCore::keyExists($jmkey)) {
            $ressources = WebserviceRequest::getResources();
            $key = $this->generateRandomString(32);
            $ws_key = new WebserviceKeyCore();
            $ws_key->key = $key;
            $ws_key->description = "JMango's key";
            $ws_key->add();
            $permissions = array();
            foreach ($ressources as $row => $values) {
                $permissions = array_merge($permissions, array($row => array(
                    'GET' => 'on',
                    'PUT' => 'on',
                    'POST' => 'on',
                    'DELETE' => 'on',
                    'HEAD' => 'on'
                )));
            }
            WebserviceKey::setPermissionForAccount($ws_key->id, $permissions);
            Configuration::updateValue('JM_WS_KEY_ID', $ws_key->id);

            return $ws_key->key;
        }
    }

    protected function getShopConfiguration(Shop $shop, $config_name)
    {
        $config = Configuration::get($config_name, 0, 0, $shop->id);
        if ($config) {
            return $config;
        }

        $retrieve_sql = sprintf(
            "SELECT `value` FROM `%s` WHERE name='%s' and id_shop='%d'",
            pSQL(_DB_PREFIX_ . 'configuration'),
            pSQL($config_name),
            (int)$shop->id
        );
        $value = Db::getInstance()->getValue($retrieve_sql);
        if ($value) {
            // Set the value temporarily to avoid multiple database lookups
            // for this configuration.
            Configuration::set($config_name, $value, 0, $shop->id);
        }

        return $value;
    }

    /*
     *  Descriptions :
     *  Date:
     */

    //Hook Display
    public function hookDisplayAdminProductsExtra($params)
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $id_product = $params['id_product'];
            $status_product_on_mobile = (int)$this->getDatabase($id_product);
            $this->context->smarty->assign(
                'stt_on_mobile',
                $status_product_on_mobile != 0 ? $status_product_on_mobile : 1
            );

            return $this->display(__FILE__, '17/template.tpl');
        } else {
            $id_product = Tools::getValue('id_product');
            $status_product_on_mobile = (int)$this->getDatabase($id_product);
            $this->context->smarty->assign(
                'stt_on_mobile',
                $status_product_on_mobile != 0 ? $status_product_on_mobile : 1
            );

            return $this->display(__FILE__, 'template.tpl');
        }
    }

    //Hook update
    public function hookActionProductUpdate($params)
    {
        if (!Tools::getIsset('status_mobile')) {
            return;
        }

        $status_product_on_mobile = 1;
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $id_product = (int)$params['id_product'];
            $status_product_on_mobile = (int)Tools::getValue('status_mobile');
        } else {
            $id_product = (int)Tools::getValue('id_product');
            $status_product_on_mobile = (int)Tools::getValue('status_mobile');
        }

        if ($status_product_on_mobile != 1 && $status_product_on_mobile != 2) {
            $status_product_on_mobile = 1;
        }

        $this->updateDatabase($id_product, $status_product_on_mobile);
    }

    public function createDatabase()
    {
        //Creating a table for data storage
        // only  when value `not_visible`= 2 product will hide on mobile-app
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'jm_product_visibility`(
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `id_product` int( 10 ) UNSIGNED NOT NULL,
                  `not_visible` tinyint(1) UNSIGNED NOT NULL,
                  PRIMARY KEY (`id`)
                ) ' . 'ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

        return (boolean)Db::getInstance()->execute($sql);
    }

    //Update DB
    public function updateDatabase($id_product, $status_product_on_mobile)
    {
        Db::getInstance()->update(
            'jm_product_visibility',
            array('not_visible' => (int)$status_product_on_mobile),
            'id_product=' . (int)$id_product
        );
        if ($this->getDatabase($id_product, $status_product_on_mobile) == 0
            && $this->getDatabase($id_product, $status_product_on_mobile) == null) {
            $this->insertDatabase($id_product, $status_product_on_mobile);
        }
    }

    //Get DB
    public function getDatabase($id_product)
    {
        $sql = 'SELECT `not_visible` FROM `' . _DB_PREFIX_ . 'jm_product_visibility` WHERE `id_product`= ' . (int)$id_product;
        $status_product_on_mobile = DB::getInstance()->getValue($sql);
        return $status_product_on_mobile;
    }

    //Insert DB
    public function insertDatabase($id_product, $status_product_on_mobile)
    {
        DB::getInstance()->insert(
            'jm_product_visibility',
            array(
                'id' => '',
                'id_product' => (int)$id_product,
                'not_visible' => (int)$status_product_on_mobile,
            )
        );
    }

    public function installTab($parent, $class_name, $name, $active = 1)
    {
        $tab_id = (bool)Tab::getIdFromClassName($class_name);
        if ($tab_id) {
            return true;
        }

        // Create new admin tab
        $tab = new Tab();
        $tab->id_parent = (int)Tab::getIdFromClassName($parent);
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $name;
        }
        $tab->class_name = $class_name;
        $tab->module = $this->name;
        $tab->active = $active;

        return (bool)$tab->add();
    }

    public function uninstallTab($class_name)
    {
        // remove app's stored info
        Configuration::deleteByName('JM_EMAIL');
        Configuration::deleteByName('JM_TICKET');
        Configuration::deleteByName('JM_APP_KEY');
        Configuration::deleteByName('JM_DEV_MODE');
        Configuration::deleteByName('JM_ID_LANG');
        Configuration::deleteByName('JM_ID_SHOP');

        if ((int)Configuration::get('JM_BRAINTREE_SETTLING')) {
            Db::getInstance()->delete(
                'order_state',
                '`id_order_state` = ' . (int)Configuration::get('JM_BRAINTREE_SETTLING')
            );
            Db::getInstance()->delete(
                'order_state_lang',
                '`id_order_state` = ' . (int)Configuration::get('JM_BRAINTREE_SETTLING')
            );
        }
        if ((int)Configuration::get('JM_BRAINTREE_VOIDED')) {
            Db::getInstance()->delete(
                'order_state',
                '`id_order_state` = ' . (int)Configuration::get('JM_BRAINTREE_VOIDED')
            );
            Db::getInstance()->delete(
                'order_state_lang',
                '`id_order_state` = ' . (int)Configuration::get('JM_BRAINTREE_VOIDED')
            );
        }

        // Retrieve Tab IDOP
        $id_tab = (int)Tab::getIdFromClassName($class_name);

        // Load tab
        $tab = new Tab((int)$id_tab);

        // Delete it
        return $tab->delete();
    }

    public function createBraintreeOrderStatuses()
    {
        $values_to_insert = array(
            'invoice' => 0,
            'send_email' => 0,
            'module_name' => pSQL($this->name),
            'color' => 'RoyalBlue',
            'unremovable' => 0,
            'hidden' => 0,
            'logable' => 1,
            'delivery' => 0,
            'shipped' => 0,
            'paid' => 0,
            'deleted' => 0
        );
        if (!Db::getInstance()->insert('order_state', $values_to_insert, true)) {
            return false;
        }
        $id_order_state = (int)Db::getInstance()->Insert_ID();
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            try {
                Db::getInstance()->insert(
                    'order_state_lang',
                    array(
                        'id_order_state' => $id_order_state,
                        'id_lang' => (int)$language['id_lang'],
                        'name' => pSQL('Settling'),
                        'template' => ' '
                    ),
                    true
                );
            } catch (PrestaShopDatabaseException $e) {
                return false;
            }
        }
        Configuration::updateValue('JM_BRAINTREE_SETTLING', $id_order_state);

        $values_to_insert = array(
            'invoice' => 0,
            'send_email' => 0,
            'module_name' => pSQL($this->name),
            'color' => 'RoyalBlue',
            'unremovable' => 0,
            'hidden' => 0,
            'logable' => 1,
            'delivery' => 0,
            'shipped' => 0,
            'paid' => 0,
            'deleted' => 0
        );
        if (!Db::getInstance()->insert('order_state', $values_to_insert, true)) {
            return false;
        }
        $id_order_state = (int)Db::getInstance()->Insert_ID();
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            try {
                Db::getInstance()->insert(
                    'order_state_lang',
                    array(
                        'id_order_state' => $id_order_state,
                        'id_lang' => (int)$language['id_lang'],
                        'name' => pSQL('Voided'),
                        'template' => ' '
                    ),
                    true
                );
            } catch (PrestaShopDatabaseException $e) {
                return false;
            }
        }
        Configuration::updateValue('JM_BRAINTREE_VOIDED', $id_order_state);

        if (!(int)Configuration::get('JM_NATIVE_PAYMENT_PENDING')) {
            $values_to_insert = array(
                'invoice' => 0,
                'send_email' => 0,
                'module_name' => pSQL($this->name),
                'color' => 'RoyalBlue',
                'unremovable' => 0,
                'hidden' => 0,
                'logable' => 0,
                'delivery' => 0,
                'shipped' => 0,
                'paid' => 0,
                'deleted' => 0
            );
            if (!Db::getInstance()->insert('order_state', $values_to_insert, true)) {
                return false;
            }
            $id_order_state = (int)Db::getInstance()->Insert_ID();
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                try {
                    Db::getInstance()->insert(
                        'order_state_lang',
                        array(
                            'id_order_state' => $id_order_state,
                            'id_lang' => (int)$language['id_lang'],
                            'name' => pSQL('Pending'),
                            'template' => ' '
                        ),
                        true
                    );
                } catch (PrestaShopDatabaseException $e) {
                    return false;
                }
            }
            Configuration::updateValue('JM_NATIVE_PAYMENT_PENDING', $id_order_state);
        }
        return true;
    }

    public function hookDisplayAfterBodyOpeningTag()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=') && Module::isEnabled('soflexibilite')) {
            return $this->hookDisplayFooter(array());
        }

        return '';
    }
}
