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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class AdminColissimoMigrationController
 *
 * Ajax processes:
 *  - startMigration
 *  - migrateStep
 *  - endMigration
 *
 */
class AdminColissimoMigrationController extends ModuleAdminController
{
    /** @var Colissimo $module */
    public $module;

    /** @var ColissimoMigration $migration */
    public $migration;

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function ajaxProcessEndMigration()
    {
        $modules = Tools::getValue('modules_to_migrate');
        foreach ($modules as $module) {
            $instance = Module::getInstanceByName($module);
            $instance->disable();
        }
        Configuration::updateGlobalValue('COLISSIMO_SHOW_MIGRATION', -1);
        $this->context->smarty->assign('link', $this->context->link);
        $html = $this->context->smarty->fetch(
            $this->module->getLocalPath().'views/templates/admin/migration/result.tpl'
        );
        $return = array(
            'html_result' => $html,
        );
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @throws Exception
     */
    public function ajaxProcessMigrateStep()
    {
        $this->migration = new ColissimoMigration();
        $modules = Tools::getValue('modules_to_migrate');
        $step = Tools::getValue('step');
        foreach ($modules as $module) {
            $moduleClass = Tools::toCamelCase('Colissimo_'.$module.'_Migration', true);
            /** @var ColissimoOtherModuleInterface $moduleInstance */
            $moduleInstance = new $moduleClass($this->module->logger);
            $this->migration->addModule($moduleInstance);
        }
        $this->module->logger->info('Migrate '.count($modules).' modules. Step: '.$step, array('modules' => $modules));
        $this->migration->migrate($step);
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function ajaxProcessStartMigration()
    {
        Configuration::updateGlobalValue('COLISSIMO_LOGS', 1);
        $migrate = Tools::getValue('migrate');
        if (!$migrate) {
            Configuration::updateGlobalValue('COLISSIMO_SHOW_MIGRATION', -1);
            $moduleCarrierIds = Configuration::getMultiple(
                array(
                    'COLISSIMO_CARRIER_AVEC_SIGNATURE',
                    'COLISSIMO_CARRIER_SANS_SIGNATURE',
                    'COLISSIMO_CARRIER_RELAIS',
                )
            );
            foreach ($moduleCarrierIds as $carrierId) {
                $carrier = ColissimoCarrier::getCarrierByReference((int) $carrierId);
                $carrier->setGroups(Group::getGroups($this->context->language->id));
            }
            $return = array('migrate' => 0);
        } else {
            $html = $this->context->smarty->fetch(
                $this->module->getLocalPath().'views/templates/admin/migration/process.tpl'
            );
            $return = array('migrate' => 1, 'html_result' => $html);
        }
        $this->ajaxDie(json_encode($return));
    }
}
