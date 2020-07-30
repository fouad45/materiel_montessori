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
    
function upgrade_module_1_0_1($object)
{
    Configuration::updateValue(
        'TOUCHIZE_TOP_MENU_ITEMS',
        ''
    ) ;
    $tabBuilder = new Tab();
    $langs = Language::getLanguages();
    foreach ($langs as $lang) {
        $tabBuilder->name[$lang['id_lang']] = $object->l('Top Menu');
    }
    $tabBuilder->class_name = 'AdminTopMenuBuilder';
    $tabBuilder->id_parent = -1;
    $tabBuilder->module = $object->name;
    return ($tabBuilder->add());
}
