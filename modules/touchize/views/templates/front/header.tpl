{*
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
 *}
<!DOCTYPE HTML>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<html{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}>
  <head>
    <meta charset="utf-8" />
      {if !empty($tz_pwa_enabled)}
          <meta name="theme-color" content="{$theme_color|escape:'html':'UTF-8'}" />
          <link rel="manifest" href="/manifest.json" crossorigin="use-credentials">
          <script src="/modules/touchize/views/js/pwa/registration.js" type="text/javascript"></script>
      {/if}
      {if ((float)$PS_VERSION) >= 1.7}
        {if isset($page)}
          <title>{block name='head_seo_title'}{$page.meta.title|escape:'html':'UTF-8'}{/block}</title>
          <meta name="description" content="{block name='head_seo_description'}{$page.meta.description|escape:'html':'UTF-8'}{/block}">
          <meta name="keywords" content="{block name='head_seo_keywords'}{$page.meta.keywords|escape:'html':'UTF-8'}{/block}">
        {/if}
      {else}
        <title>{$meta_title|escape:'html':'UTF-8'}</title>
        {if isset($meta_description) AND $meta_description}
          <meta name="description" content="{$meta_description|escape:'html':'UTF-8'}" />
        {/if}
        {if isset($meta_keywords) AND $meta_keywords}
          <meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}" />
        {/if}
      {/if}
      <link rel="dns-prefetch" href="//d2kt9xhiosnf0k.cloudfront.net"/>
      <link rel="dns-prefetch" href="//fonts.gstatic.com"/>
      <meta name="generator" content="PrestaShop" />
      {if isset($page)}
        {if $page.meta.robots !== 'index'}
          <meta name="robots" content="{$page.meta.robots|escape:'html':'UTF-8'}">
        {/if}
      {/if}
    <meta 
      name="viewport"
      content="width=device-width, user-scalable=0, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="img/tz-pwa-logo-512.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/tz-pwa-logo-512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="img/tz-pwa-logo-512.png">
    <link rel="apple-touch-icon" sizes="167x167" href="img/tz-pwa-logo-512.png">
    {if !empty($tz_pwa_enabled)}
        {include file="./pwa.tpl"}
    {/if}

    {if isset($shop)}
    <link 
      rel="icon"
      type="image/vnd.microsoft.icon"
      href="{$shop.favicon|escape:'html':'UTF-8'}?{$shop.favicon_update_time|escape:'html':'UTF-8'}" crossorigin="use-credentials"
    />
    <link
      rel="shortcut icon"
      type="image/x-icon"
      href="{$shop.favicon|escape:'html':'UTF-8'}?{$shop.favicon_update_time|escape:'html':'UTF-8'}"
    />
    {/if}
    <link rel="preload" href="{$scriptPath|escape:'htmlall':'UTF-8'}/css/slq.css" as="style">
    <link rel="preload" href="{$scriptPath|escape:'htmlall':'UTF-8'}/js/slq.js" as="script">
    <link rel="preload" href="{$scriptPath|escape:'htmlall':'UTF-8'}/css/images/shopping_cart.svg" as="image">
      {if $tc_preview !== false}
        <script src="/modules/touchize/views/js/theme_creator_helper.js" type="text/javascript"></script>
      {/if}

    <link rel="stylesheet" type="text/css" href="{$scriptPath|escape:'htmlall':'UTF-8'}/css/slq.css">
    {include file="./microdata.tpl"}
    {* No escaping since admin user is allowed to enter pure HTML here. *}
    {if isset($head_html) && $head_html}
      {$head_html nofilter}
    {/if}    
  </head>
  <body style='background-color:{$app_background_color|escape:'html':'UTF-8'}'>
  {if !isset($content_only) || !$content_only}
    {if isset($restricted_country_mode) && $restricted_country_mode}
      <div id="restricted-country">
        <p>
          {l s='You cannot place a new order from your country.' mod='touchize'}
          {if isset($geolocation_country) && $geolocation_country}
            <span class="bold">{$geolocation_country|escape:'html':'UTF-8'}</span>
          {/if}
        </p>
      </div>
    {/if}
    <div id="sq-base">
        <noscript style="position: fixed; width:100%;text-align: center;padding-top:200px">{$tz_noscript_content|escape:'html':'UTF-8'}</noscript>
    </div>
  {/if}

