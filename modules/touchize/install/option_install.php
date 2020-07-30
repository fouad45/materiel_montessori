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

$options = array();
$options[] = array(
    'name' => 'TOUCHIZE_ENABLED',
    'value' => 0
);
$options[] = array(
    'name' => 'TOUCHIZE_LICENSE_KEY',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_LICENSE_KEY_VALIDATED',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_GENERATE_STARTUP_MODULES',
    'value' => 1
);
$options[] = array(
    'name' => 'TOUCHIZE_START_CATEGORY_ID',
    'value' => 'best-sales'
);
$options[] = array(
    'name' => 'TOUCHIZE_HEAD_HTML',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_BODY_HTML',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_MAIN_MENU',
    'value' => '{"items":['.
        '{'.
        '"type": "menu-item",'.
        '"page": 0,'.
        '"pageurl": "page",'.
        '"title": "Page title"'.
        '}'.
        ']}'
);
$options[] = array(
    'name' => 'TOUCHIZE_TOUCHMAP_SLIDER_INTERVAL',
    'value' => 7000
);
$options[] = array(
    'name' => 'TOUCHIZE_DEBUG',
    'value' => 0
);
$options[] = array(
    'name' => 'TOUCHIZE_LOGO',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_PREVIEW_LOGO',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_CDN_PATH',
    'value' => Touchize::CDN_PATH
);
$options[] = array(
    'name' => 'TOUCHIZE_CDN_CODE',
    'value' => Touchize::CDN_CODE
);
$options[] = array(
    'name' => 'TOUCHIZE_PREVIEW_CDN_CODE',
    'value' => Touchize::CDN_CODE
);
$options[] = array(
    'name' => 'TOUCHIZE_SEO_SAME_AS',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_SEO_GA_ID',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_PWA_NAME',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_PWA_SHORTNAME',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_PWA_START_URL',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_PWA_THEME_COLOR',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_PWA_BACKGROUND_COLOR',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_PWA_ENABLED',
    'value' => 0
);
$options[] = array(
    'name' => 'TOUCHIZE_PWA_LOGO',
    'value' => 0
);
$menu_helper = new TouchizeTopMenuHelper();
$options[] = array(
    'name' => 'TOUCHIZE_TOP_MENU_ITEMS',
    'value' => json_encode($menu_helper->getJsAllowedItems())
);
$options[] = array(
    'name' => 'TOUCHIZE_TRIAL_ACTIVE',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_TRIAL_HAS_BEEN_ACTIVATED',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_WHEN_TRIAL_WAS_ACTIVATED',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_WIZARD_FINISHED',
    'value' => '0'
);
$options[] = array(
    'name' => 'TOUCHIZE_DOMAIN_NAME',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_PS_SHOP_NAME',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_PS_SHOP_EMAIL',
    'value' => ''
);
$options[] = array(
    'name' => 'TOUCHIZE_MAIN_COLOR',
    'value' => '#009cde'
);
$options[] = array(
    'name' => 'TOUCHIZE_COLS_SELECTION',
    'value' => '2'
);
return $options;
