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
    <div class="panel">
        {include file="{$template_dir}setupwizard/_heading.tpl"}
        {if $is_multishop_mode}
          {include file="{$template_dir|escape:'htmlall':'UTF-8'}info/multistorewarning.tpl"}
        {else}
        <div class="alert alert-danger" role="alert">
            {$setupwizard_errormessage|escape:'htmlall':'UTF-8'}
        </div>
        <div class="form-wrapper">
          <h2>{$setupwizard_errormessage|escape:'htmlall':'UTF-8'}</h2>
          <div class="row text-center">
              <div class="col-xs-12">
                  <a class="btn btn-primary btn-lg" href="{$adminGetStartedUrl|escape:'htmlall':'UTF-8'}">
                      {l s='Ok, I got it!' mod='touchize'}
                  </a>
              </div>
          </div>
        </div>
        {/if}
    </div>
