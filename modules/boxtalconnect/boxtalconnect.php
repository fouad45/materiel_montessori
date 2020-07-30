<?php
/**
 * 2007-2019 PrestaShop
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
 * @author    Boxtal <api@boxtal.com>
 * @copyright 2007-2019 PrestaShop SA / 2018-2019 Boxtal
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Main plugin file.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/autoloader.php';

/**
 * Class BoxtalConnect
 *
 *  Main module class.
 */
class BoxtalConnect extends Module
{
    /**
     * Instance.
     *
     * @var BoxtalConnect
     */
    private static $instance;

    /**
     * Construct function.
     *
     * @void
     */
    public function __construct()
    {
        $this->name = 'boxtalconnect';
        $this->tab = 'shipping_logistics';
        $this->version = '1.2.1';
        $this->author = 'Boxtal';
        //phpcs:ignore
        $this->need_instance = 0;
        //phpcs:ignore
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->file = __FILE__;
        $this->module_key = '5ec0f6a902604743acef4cae367a615a';
        $this::$instance = $this;
        parent::__construct();

        $this->displayName = $this->l('Boxtal Connect');
        $this->description = $this->l('Managing your shipments becomes easier with our free plugin Boxtal! Save time and enjoy negotiated rates with 15 carriers: Colissimo, Mondial Relay...');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->minPhpVersion = '5.3.0';
        $this->onboardingUrl = 'https://www.boxtal.com/onboarding';

        Boxtal\BoxtalConnectPrestashop\Util\ShopUtil::getShopContext();

        if ($this->active) {
            $this->initEnvironmentCheck($this);

            if (false === Boxtal\BoxtalConnectPrestashop\Util\EnvironmentUtil::checkErrors($this)) {
                $this->initSetupWizard($this);
                $this->initShopController($this);
                $this->initAdminAjaxController($this);

                if (Boxtal\BoxtalConnectPrestashop\Util\AuthUtil::canUsePlugin()) {
                    $this->initFrontAjaxController($this);
                    $this->initOrderController($this);
                }
            }
        }
    }

    /**
     * Remove all plugin's tabs from a parent tab
     *
     * @param number $parentId id of parent's tab
     */
    private function removeModuleTabs($parentId)
    {
        $tabsRow = Tab::getTabs(false, (int) $parentId);
        foreach ($tabsRow as $tabRow) {
            if (isset($tabRow['id_tab'])) {
                $tab = new Tab((int)$tabRow['id_tab']);
                if ($this->name === $tab->module) {
                    $tab->delete();
                }
            }
        }
    }

    /**
     * Install function.
     *
     * @return bool
     */
    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('header')
            || !$this->registerHook('displayCarrierList')
            || !$this->registerHook('displayAfterCarrier')
            || !$this->registerHook('newOrder')
            || !$this->registerHook('updateCarrier')
            || !$this->registerHook('adminOrder')
            || !$this->registerHook('displayOrderDetail')
            || !$this->registerHook('displayAdminAfterHeader')) {
            return false;
        }

        \Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bx_notices` (
            `id_notice` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_shop_group` int(11) unsigned NULL,
            `id_shop` int(11) unsigned NULL,
            `key` varchar(255) NOT NULL,
            `value` text,
            PRIMARY KEY (`id_notice`),
            CONSTRAINT UC_bx_notices UNIQUE (`key`, `id_shop_group`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'
        );

        \Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bx_carrier` (
            `id_bx_carrier` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_carrier` int(10) unsigned NOT NULL,
            `id_shop_group` int(11) unsigned NULL,
            `id_shop` int(11) unsigned NULL,
            `parcel_point_networks` text,
            PRIMARY KEY (`id_bx_carrier`),
            CONSTRAINT UC_bx_carrier UNIQUE (`id_carrier`, `id_shop_group`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'
        );

        \Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bx_cart_storage` (
            `id_cart_storage` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_cart` int(10) unsigned NOT NULL,
            `id_shop_group` int(11) unsigned NULL,
            `id_shop` int(11) unsigned NULL,
            `key` varchar(255) NOT NULL,
            `value` mediumtext,
            PRIMARY KEY (`id_cart_storage`),
            CONSTRAINT UC_bx_cart_storage UNIQUE (`id_cart`, `id_shop_group`, `id_shop`, `key`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'
        );

        \Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bx_order_storage` (
            `id_order_storage` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_order` int(10) unsigned NOT NULL,
            `id_shop_group` int(11) unsigned NULL,
            `id_shop` int(11) unsigned NULL,
            `key` varchar(255) NOT NULL,
            `value` mediumtext,
            PRIMARY KEY (`id_order_storage`),
            CONSTRAINT UC_bx_order_storage UNIQUE (`id_order`, `id_shop_group`, `id_shop`, `key`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'
        );

        // remove previous tab
        $this->removeModuleTabs(-1);
        // add invisible tab for admin ajax controller
        $invisibleTab = new \Tab();
        $invisibleTab->active = 1;
        //phpcs:ignore
        $invisibleTab->class_name = 'AdminAjax';
        $invisibleTab->name = array();
        foreach (\Language::getLanguages(true) as $lang) {
            $invisibleTab->name[$lang['id_lang']] = 'Ajax route';
        }
        //phpcs:ignore
        $invisibleTab->id_parent = -1;
        $invisibleTab->module = $this->name;
        if (false === $invisibleTab->add()) {
            return false;
        }

        // remove previous tab
        $adminParentShippingTabId = (int) Tab::getIdFromClassName('AdminParentShipping');
        $this->removeModuleTabs($adminParentShippingTabId);
        // add the new tab
        $tab = new Tab();
        //phpcs:ignore
        $tab->class_name = 'AdminShippingMethod';
        //phpcs:ignore
        $tab->id_parent = $adminParentShippingTabId;
        $tab->module = $this->name;
        $tab->name = array();
        foreach (\Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Boxtal Connect';
        }
        if (false === $tab->add()) {
            return false;
        }

        return true;
    }

    /**
     * Uninstall function.
     *
     * @return bool
     */
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        Boxtal\BoxtalConnectPrestashop\Util\ConfigurationUtil::deleteConfiguration();
        \DB::getInstance()->execute(
            'SET FOREIGN_KEY_CHECKS = 0;
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'bx_notices`;
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'bx_carrier`;
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'bx_cart_storage`;
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'bx_order_storage`;
            DELETE FROM `' . _DB_PREFIX_ . 'configuration` WHERE name like "BX_%";
            SET FOREIGN_KEY_CHECKS = 1;'
        );

        $this->removeModuleTabs((int) Tab::getIdFromClassName('AdminParentShipping'));
        $this->removeModuleTabs(-1);

        return true;
    }

    /**
     * Adds configure link to module page.
     */
    public function getContent()
    {
        $link = new Link();
        $shippingMethodConfiguration = $link->getAdminLink('AdminShippingMethod');
        Tools::redirectAdmin($shippingMethodConfiguration);
    }

    /**
     * Get module instance.
     *
     * @return BoxtalConnect
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * DisplayBackOfficeHeader hook. Used to display relevant css & js.
     *
     * @void
     */
    public function hookDisplayBackOfficeHeader()
    {
        $controller = $this->getContext()->controller;

        $hasRegisterSteelshitMethod = method_exists($controller, 'registerStylesheet');
        $hasRegisterJavascriptMethod = method_exists($controller, 'registerJavascript');

        if (Boxtal\BoxtalConnectPrestashop\Controllers\Misc\NoticeController::hasNotices()) {
            if ($hasRegisterJavascriptMethod) {
                $controller->registerJavascript(
                    'bx-polyfills',
                    'modules/' . $this->name . '/views/js/polyfills.min.js',
                    array('priority' => 99, 'server' => 'local')
                );
                $controller->registerJavascript(
                    'bx-notices',
                    'modules/' . $this->name . '/views/js/notices.min.js',
                    array('priority' => 100, 'server' => 'local')
                );
            } else {
                $controller->addJs('modules/' . $this->name . '/views/js/polyfills.min.js');
                $controller->addJs('modules/' . $this->name . '/views/js/notices.min.js');
            }
            if ($hasRegisterSteelshitMethod) {
                $controller->registerStylesheet(
                    'bx-notices',
                    'modules/' . $this->name . '/views/css/notices.css',
                    array('priority' => 100, 'server' => 'local')
                );
            } else {
                $controller->addCSS('modules/' . $this->name . '/views/css/notices.css', 'all');
            }
        }

        if ('AdminOrdersController' === get_class($controller) && false !== Tools::getValue('id_order')) {
            if ($hasRegisterSteelshitMethod) {
                $controller->registerStylesheet(
                    'bx-tracking',
                    'modules/' . $this->name . '/views/css/tracking.css',
                    array('priority' => 100, 'server' => 'local')
                );
            } else {
                $controller->addCSS('modules/' . $this->name . '/views/css/tracking.css', 'all');
            }
        }

        if ('AdminShippingMethodController' === get_class($controller)) {
            if ($hasRegisterSteelshitMethod) {
                $controller->registerStylesheet(
                    'bx-tracking',
                    'modules/' . $this->name . '/views/css/settings.css',
                    array('priority' => 100, 'server' => 'local')
                );
            } else {
                $controller->addCSS('modules/' . $this->name . '/views/css/settings.css', 'all');
            }
        }
    }

    /**
     * Header hook. Display includes JavaScript for maps.
     *
     * @param mixed $params context values
     *
     * @return string html
     */
    public function hookHeader($params)
    {
        if (!Boxtal\BoxtalConnectPrestashop\Util\AuthUtil::canUsePlugin()) {
            return null;
        }

        return Boxtal\BoxtalConnectPrestashop\Controllers\Front\ParcelPointController::addScripts();
    }

    /**
     * Prestashop < 1.7. Used to display front-office relay point list.
     *
     * @param array $params Parameters array (cart object, address information)
     *
     * @return string html
     */
    public function hookDisplayCarrierList($params)
    {
        if (!Boxtal\BoxtalConnectPrestashop\Util\AuthUtil::canUsePlugin()) {
            return null;
        }

        return Boxtal\BoxtalConnectPrestashop\Controllers\Front\ParcelPointController::initPoints($params);
    }

    /**
     * Prestashop > 1.7. Used to display front-office relay point list.
     *
     * @param array $params Parameters array (cart object, address information)
     *
     * @return string html
     */
    public function hookDisplayAfterCarrier($params)
    {
        if (!Boxtal\BoxtalConnectPrestashop\Util\AuthUtil::canUsePlugin()) {
            return null;
        }

        return Boxtal\BoxtalConnectPrestashop\Controllers\Front\ParcelPointController::initPoints($params);
    }

    /**
     * Order creation hook.
     *
     * @param array $params list of order params
     *
     * @void
     */
    public function hooknewOrder($params)
    {
        if (!Boxtal\BoxtalConnectPrestashop\Util\AuthUtil::canUsePlugin()) {
            return;
        }

        Boxtal\BoxtalConnectPrestashop\Controllers\Front\ParcelPointController::orderCreated($params);
    }

    /**
     * Update carrier hook. Used to update carrier id.
     *
     * @param array $params list of params used in the operation
     *
     * @void
     */
    public function hookUpdateCarrier($params)
    {
        $idCarrierOld = (int) $params['id_carrier'];
        $idCarrierNew = (int) $params['carrier']->id;

        $data = array('id_carrier' => $idCarrierNew);
        \Db::getInstance()->update(
            'bx_carrier',
            $data,
            'id_carrier = ' . $idCarrierOld,
            0,
            true
        );
    }

    /**
     * DisplayAdminAfterHeader hook. Used to display notices.
     *
     * @void
     */
    public function hookDisplayAdminAfterHeader()
    {
        $notices = Boxtal\BoxtalConnectPrestashop\Controllers\Misc\NoticeController::getNoticeInstances();
        foreach ($notices as $notice) {
            $notice->render();
        }
    }

    /**
     * adminOrder hook. Used to display tracking and parcelpoint in admin orders.
     *
     * @param array $params list of params used in the operation
     *
     * @return string html
     */
    public function hookAdminOrder($params)
    {
        return Boxtal\BoxtalConnectPrestashop\Controllers\Hook\AdminOrderController::trigger($params);
    }

    /**
     * displayOrderDetail hook. Used to display parcelpoint in user orders.
     *
     * @param array $params list of params used in the operation
     *
     * @return string html
     */
    public function hookDisplayOrderDetail($params)
    {
        return Boxtal\BoxtalConnectPrestashop\Controllers\Hook\DisplayOrderDetailController::trigger($params);
    }

    /**
     * Get context.
     *
     * @return \Context context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Check PHP version.
     *
     * @param BoxtalConnect $plugin plugin array
     *
     * @return EnvironmentCheck $object static environment check instance
     */
    public function initEnvironmentCheck($plugin)
    {
        static $object;

        if (null !== $object) {
            return $object;
        }

        $object = new Boxtal\BoxtalConnectPrestashop\Init\EnvironmentCheck($plugin);

        return $object;
    }

    /**
     * Init setup wizard.
     *
     * @param BoxtalConnect $plugin plugin array
     *
     * @return SetupWizard $object static setup wizard instance
     */
    public function initSetupWizard($plugin)
    {
        static $object;

        if (null !== $object) {
            return $object;
        }

        $object = new Boxtal\BoxtalConnectPrestashop\Init\SetupWizard($plugin);

        return $object;
    }

    /**
     * Init shop controller.
     *
     * @param BoxtalConnect $plugin plugin array
     *
     * @void
     */
    public function initShopController($plugin)
    {
        require_once dirname(__FILE__) . '/controllers/front/shop.php';
    }

    /**
     * Init admin ajax controller.
     *
     * @param BoxtalConnect $plugin plugin array
     *
     * @void
     */
    public function initAdminAjaxController($plugin)
    {
        require_once dirname(__FILE__) . '/controllers/admin/AdminAjaxController.php';
    }

    /**
     * Init front ajax controller.
     *
     * @param BoxtalConnect $plugin plugin array
     *
     * @void
     */
    public function initFrontAjaxController($plugin)
    {
        if (!Boxtal\BoxtalConnectPrestashop\Util\AuthUtil::canUsePlugin()) {
            return;
        }

        require_once dirname(__FILE__) . '/controllers/front/ajax.php';
    }

    /**
     * Init order controller.
     *
     * @param BoxtalConnect $plugin plugin array
     *
     * @void
     */
    public function initOrderController($plugin)
    {
        if (!Boxtal\BoxtalConnectPrestashop\Util\AuthUtil::canUsePlugin()) {
            return;
        }

        require_once dirname(__FILE__) . '/controllers/front/order.php';
    }

    /**
     * Get smarty.
     *
     * @return object
     */
    public function getSmarty()
    {
        return $this->getContext()->smarty;
    }

    /**
     * Get current controller.
     *
     * @return object
     */
    public function getCurrentController()
    {
        return $this->getContext()->controller;
    }

    /**
     * Display template.
     *
     * @param string $templatePath path to template from module folder
     *
     * @return string html
     */
    public function displayTemplate($templatePath)
    {
        return $this->display(__FILE__, '/views/templates/' . $templatePath);
    }
}
