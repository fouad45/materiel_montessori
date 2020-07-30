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

$sql = array();

$sql[_DB_PREFIX_ . 'touchize_touchmap'] = '
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'touchize_touchmap` (
            `id_touchize_touchmap` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_shop` int(10) UNSIGNED DEFAULT NULL,
            `imageurl` VARCHAR(255) DEFAULT NULL,
            `name` VARCHAR(255) DEFAULT NULL,
            `active` tinyint(1) NOT NULL DEFAULT \'1\',
            `mobile` tinyint(1) NOT NULL DEFAULT \'1\',
            `tablet` tinyint(1) NOT NULL DEFAULT \'1\',
            `runonce` tinyint(1) NOT NULL DEFAULT \'0\',
            `new_products` tinyint(1) NOT NULL DEFAULT \'0\',
            `best_sellers` tinyint(1) NOT NULL DEFAULT \'0\',
            `prices_drop` tinyint(1) NOT NULL DEFAULT \'0\',
            `home_page` tinyint(1) NOT NULL DEFAULT \'0\',
            `inslider` tinyint(1) NOT NULL DEFAULT \'1\',
            `position` int(10) UNSIGNED DEFAULT NULL,
            `width` int(10) UNSIGNED DEFAULT NULL,
            `height` int(10) UNSIGNED DEFAULT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_touchize_touchmap`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ';

$sql[_DB_PREFIX_ . 'touchize_actionarea'] = '
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'touchize_actionarea` (
            `id_touchize_actionarea` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `tx` VARCHAR(255) DEFAULT NULL,
            `ty` VARCHAR(255) DEFAULT NULL,
            `width` VARCHAR(255) DEFAULT NULL,
            `height` VARCHAR(255) DEFAULT NULL,
            `id_product` int(10) DEFAULT NULL,
            `id_product_attribute` VARCHAR(255) DEFAULT NULL,
            `id_category` int(10) DEFAULT NULL,
            `id_manufacturer` int(10) DEFAULT NULL,
            `search_term` VARCHAR(255) DEFAULT NULL,
            `id_touchize_touchmap` int(10) unsigned DEFAULT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_touchize_actionarea`),
            KEY `id_touchize_touchmap` (`id_touchize_touchmap`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;';

$sql[_DB_PREFIX_ . 'touchize_touchmapcategory'] = '
            CREATE TABLE IF NOT EXISTS `'
            ._DB_PREFIX_.'touchize_touchmapcategory` (
            `id_touchize_touchmap` int(10) unsigned NOT NULL,
            `id_category` int(10) unsigned NOT NULL,
            PRIMARY KEY (`id_touchize_touchmap`,`id_category`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;';

$sql[_DB_PREFIX_ . 'touchize_variables'] = '
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'touchize_variables` (
            `id_variable` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_shop` int(10) DEFAULT NULL,
            `id_shop_group` int(10) DEFAULT NULL,
            `name` VARCHAR(255) DEFAULT NULL,
            `description` VARCHAR(255) DEFAULT NULL,
            `value` VARCHAR(255) DEFAULT NULL,
            `is_color` tinyint(1) NOT NULL DEFAULT \'0\',
            `template` VARCHAR(255) DEFAULT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_variable`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;';

$sql[_DB_PREFIX_ . 'touchize_variables_preview'] = '
            CREATE TABLE IF NOT EXISTS `'
            ._DB_PREFIX_.'touchize_variables_preview` (
            `id_variable` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_shop` int(10) DEFAULT NULL,
            `id_shop_group` int(10) DEFAULT NULL,
            `name` VARCHAR(255) DEFAULT NULL,
            `description` VARCHAR(255) DEFAULT NULL,
            `value` VARCHAR(255) DEFAULT NULL,
            `is_color` tinyint(1) NOT NULL DEFAULT \'0\',
            `template` VARCHAR(255) DEFAULT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_variable`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ';

$sql[_DB_PREFIX_ . 'touchize_main_menu'] = '
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'touchize_main_menu` (
            `id_menu_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `type` enum(
                \'menu-item\',
                \'menu-header\',
                \'menu-divider\'
            ) DEFAULT NULL,
            `action` enum(
                \'page\',
                \'cms_page\',
                \'url\',
                \'event\'
            ) DEFAULT NULL,
            `page` INT(10) DEFAULT NULL,
            `cms_page` INT(10) DEFAULT NULL,
            `url` VARCHAR(255) DEFAULT NULL,
            `external` INT(1) DEFAULT NULL,
            `event` VARCHAR(255) DEFAULT NULL,
            `event_input` VARCHAR(255) DEFAULT NULL,
            `page_url` VARCHAR(255) DEFAULT NULL,
            `position` int(10) unsigned NOT NULL DEFAULT \'0\',
            PRIMARY KEY (`id_menu_item`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ';

$sql[_DB_PREFIX_ . 'touchize_main_menu_lang'] = '
            CREATE TABLE IF NOT EXISTS `'
            ._DB_PREFIX_.'touchize_main_menu_lang` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_lang` INT(10) DEFAULT NULL,
            `id_menu_item` INT(10) DEFAULT NULL,
            `title` VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ';

return $sql;
