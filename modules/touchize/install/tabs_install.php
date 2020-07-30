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

$tabs = array();

$tabs[] = array(
    'label' => array(
        'en' => 'Touchize Commerce',
    ),
    'class_name' => 'AdminGetStarted',
    'id_parent' => 0,
);

$tabs[] = array(
    'label' => array(
        'en' => 'Touchize Commerce | Setup Wizard',
    ),
    'class_name' => 'AdminSetupWizard',
    'id_parent' => -1
);

$tabs[] = array(
    'label' => array(
        'en' => 'Touchize Commerce | Settings',
    ),
    'class_name' => 'AdminSettings',
    'id_parent' => -1,
    'config_name' => 'TOUCHIZE_SETTINGS_TAB_ID'
);

$tabs[] = array(
    'label' => array(
        'en' => 'Touchize Commerce | Customize Theme',
    ),
    'class_name' => 'AdminWizard',
    'id_parent' => -1,
    'config_name' => 'TOUCHIZE_WIZARD_TAB_ID'
);

$tabs[] = array(
    'label' => array(
        'en' => 'Touchize Commerce | Banners',
    ),
    'class_name' => 'AdminTouchmaps',
    'id_parent' => -1,
    'config_name' => 'TOUCHIZE_TAB_ID'
);

$tabs[] = array(
    'label' => array(
        'en' => 'Touchize Commerce | Setup Menus',
//        'de' => 'Touchize Commerce Menus DE'
    ),
    'class_name' => 'AdminMenuBuilder',
    'id_parent' => -1,
    'config_name' => 'TOUCHIZE_MENU_TAB_ID'
);

$tabs[] = array(
    'label' => array(
        'en' => 'Touchize Commerce | Manage Subscription',
    ),
    'class_name' => 'AdminLicense',
    'id_parent' => -1,
    'config_name' => 'TOUCHIZE_LICENSE_TAB_ID'
);

$tabs[] = array(
    'label' => array(
        'en' => 'Touchize Commerce |Setup Menus',
    ),
    'class_name' => 'AdminTopMenuBuilder',
    'id_parent' => -1,
);

$tabs[] = array(
    'label' => array(
        'en' => 'Touchize Commerce | Contact Us',
    ),
    'class_name' => 'AdminContactUs',
    'id_parent' => -1,
);

return $tabs;
