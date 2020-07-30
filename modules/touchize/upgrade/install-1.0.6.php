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

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_6($object)
{
    $result = Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.
        'touchize_variables` ADD `id_shop` int(10) DEFAULT NULL AFTER `id_variable`');
    $result = Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.
        'touchize_variables_preview` ADD `id_shop` int(10) DEFAULT NULL AFTER `id_variable`');
    $result = Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.
        'touchize_variables` ADD `id_shop_group` int(10) DEFAULT NULL AFTER `id_shop`');
    $result = Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.
        'touchize_variables_preview` ADD `id_shop_group` int(10) DEFAULT NULL AFTER `id_shop`');
        
    $langs = Language::getLanguages();
    $contactusTab = new Tab();
    foreach ($langs as $lang) {
        $contactusTab->name[$lang['id_lang']] = $object->l('Contact Us');
    }
    $contactusTab->class_name = 'AdminContactUs';
    $contactusTab->id_parent = -1;
    $contactusTab->module = $object->name;
    $result &= $contactusTab->add();
    
    return $result;
}
