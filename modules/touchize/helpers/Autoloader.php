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

function touchizeAutoloader($class)
{
    $module_dir = _PS_MODULE_DIR_ . 'touchize/';

    if (file_exists($module_dir . 'classes/' . $class . '.php')) {
        require_once $module_dir . 'classes/' . $class . '.php';
        return;
    }

    if (file_exists($module_dir . 'helpers/' . $class . '.php')) {
        require_once $module_dir . 'helpers/' . $class . '.php';
        return;
    }

    if (file_exists($module_dir . 'helpers/adapter/' . $class . '.php')) {
        require_once $module_dir . 'helpers/adapter/' . $class . '.php';
        return;
    }

    if (file_exists($module_dir . 'helpers/resolver/' . $class . '.php')) {
        require_once $module_dir . 'helpers/resolver/' . $class . '.php';
        return;
    }

    if (file_exists($module_dir . 'helpers/resolver/listing/' . $class . '.php')) {
        require_once $module_dir . 'helpers/resolver/listing/' . $class . '.php';
        return;
    }

    if (file_exists($module_dir . 'controllers/admin/' . $class . '.php')) {
        require_once $module_dir . 'controllers/admin/' . $class . '.php';
        return;
    }

    if (file_exists($module_dir . 'controllers/front/' . $class . '.php')) {
        require_once $module_dir . 'controllers/front/' . $class . '.php';
        return;
    }

    if (file_exists($module_dir . 'settings/' . $class . '.php')) {
        require_once $module_dir . 'settings/' . $class . '.php';
        return;
    }
}

spl_autoload_register('touchizeAutoloader');
