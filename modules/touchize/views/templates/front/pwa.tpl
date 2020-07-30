{*
 * 2019 Touchize Sweden AB.
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
 *  @copyright 2019 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 *}

{if $tz_pwa_splashes}
    {foreach from=$tz_pwa_splashes item=splash}
        <!-- {$splash.device|escape:'html':'UTF-8'} ({$splash.width}px x {$splash.height}px) --> 
        <link rel="apple-touch-startup-image" media="(device-width: {$splash.width/$splash.ratio|escape:'html':'UTF-8'}px) and (device-height: {$splash.height/$splash.ratio|escape:'html':'UTF-8'}px) and (-webkit-device-pixel-ratio: {$splash.ratio|escape:'html':'UTF-8'}) and (orientation:portrait)" href="/img/tz-apple-launch-{$splash.width}x{$splash.height}.png">
        <link rel="apple-touch-startup-image" media="(device-width: {$splash.width/$splash.ratio|escape:'html':'UTF-8'}px) and (device-height: {$splash.height/$splash.ratio|escape:'html':'UTF-8'}px) and (-webkit-device-pixel-ratio: {$splash.ratio|escape:'html':'UTF-8'}) and (orientation:landscape)" href="/img/tz-apple-launch-{$splash.height}x{$splash.width}.png">
    {/foreach}
{/if}
