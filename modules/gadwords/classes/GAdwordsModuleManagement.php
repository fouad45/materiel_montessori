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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2019 PrestaShop SA
 *  @version   Release: $Revision: 17142 $
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class GAdwordsModuleManagement
{
    /**
     * Manage the module and return the module configuration link if it is installed
     *
     * @param  string $moduleName
     * @param  int $moduleId
     *
     * @return bool
     */
    public function moduleManagement($moduleName, $moduleId)
    {
        // Is the module is installed ?
        if (Module::isInstalled($moduleName)) {
            return true;
        }

        if (!$this->isModuleOnDisk($moduleName, $moduleId)) {
            return false;
        }

        $modulePrestashopAds = Module::getInstanceByName($moduleName);

        if (!$modulePrestashopAds) {
            return false;
        }

        return $modulePrestashopAds->install();
    }

    /**
     * Check if a module is on the disk
     *
     * @param  string $moduleName
     * @param  int $moduleId
     *
     * @return bool
     */
    private function isModuleOnDisk($moduleName, $moduleId)
    {
        $modulesOnDisk = Module::getModulesDirOnDisk();

        if (in_array($moduleName, $modulesOnDisk)) {
            return true;
        }

        return $this->downloadModule($moduleName, $moduleId);
    }

    /**
     * Download module from Addons
     *
     * @param  string $moduleName
     * @param  int $moduleId
     *
     * @return bool
     */
    public function downloadModule($moduleName, $moduleId)
    {
        $length = file_put_contents(
            _PS_MODULE_DIR_.basename($moduleName).'.zip',
            Tools::addonsRequest('module', array('id_module' => $moduleId))
        );

        if (!empty($length) && Tools::ZipExtract(_PS_MODULE_DIR_.basename($moduleName).'.zip', _PS_MODULE_DIR_)) {
            @unlink(_PS_MODULE_DIR_.basename($moduleName).'.zip');
            return true;
        }

        return false;
    }
}
