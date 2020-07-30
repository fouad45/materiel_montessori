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
  {include file="{$template_dir|escape:'htmlall':'UTF-8'}setupwizard/_heading.tpl"}
  {if $is_multishop_mode}
    {include file="{$template_dir|escape:'htmlall':'UTF-8'}info/multistorewarning.tpl"}
  {else}
  <div class="form-wrapper">
    <div class="row">
      <div class="col-xs-12 col-md-10 col-md-push-1">
        <h2>{$current_step-1|escape:'htmlall':'UTF-8'}: {l s='Setup Link Menu' mod='touchize'}</h2>
        {include file="{$template_dir|escape:'htmlall':'UTF-8'}setupwizard/_progressbar.tpl" step=$current_step}
        <p class="ingress">
          {l s='The link menu is where your customers find the links that usually are found in the bottom ”footer”. ' mod='touchize'}
          {l s='This gives more screen space for your products.' mod='touchize'}
        </p>
      </div>
      <div class="col-xs-12 col-md-12">
        <div class="panel">
          <div class="form-wrapper">
            <div class="row">
              <h3>{l s='Select pages to be displayed in the link menu' mod='touchize'}
                <!-- {include file="{$template_dir|escape:'htmlall':'UTF-8'}setupwizard/_helptooltip.tpl" helptext='Help text for Link Menu setup'} -->
              </h3>
              <div class="row flex-display align-items-center">
                <div class="col-xs-12 col-md-3">
                  <img src="{$img_dir|escape:'html':'UTF-8'}onboarding_content_images/link_menu/link_menu.png" class="img-responsive">
                </div>
                <div class="col-xs-12 col-md-6 col-md-push-1">
                  <div class="panel">
                    <div id="link_menu" class="select_menu row">
                      {foreach from=$menu_items key=menu_id item=menu_name}
                        <div class="col-xs-12">
                          <input type="checkbox" value="{$menu_id|escape:'htmlall':'UTF-8'}" {if $menu_id|in_array:$link_menu_preselection || ($preselect_all_link_menu_items && $menu_id|substr:0:3 == 'cms')}checked="checked"{/if} name="menu_item" id="menu_item_{$menu_id|escape:'htmlall':'UTF-8'}">
                          <label for="menu_item_{$menu_id|escape:'htmlall':'UTF-8'}">
                            {$menu_name|escape:'htmlall':'UTF-8'}
                          </label>
                        </div>
                      {/foreach}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer">
    <div class="row">
      <div class="col-xs-3 text-left">

          <a class="btn btn-link btn-lg" href="{$wizardsteps.{$current_step-3}|escape:'htmlall':'UTF-8'}"> {l s='Previous' mod='touchize'}</a>

      </div>
      <div class="col-xs-6 text-center">
        <p>
          <button class="btn btn-primary btn-lg btn-block next-step" id="next-step" data-step="{$current_step|escape:'htmlall':'UTF-8'}">{l s='Next' mod='touchize'}</button>
        </p>
        <p>
          <a class="btn btn-link btn-lg" href="{$wizardsteps.{$current_step-1}|escape:'htmlall':'UTF-8'}&skip=true">{l s='Skip this step' mod='touchize'}</a>
        </p>
      </div>
    </div>
  </div>
  {/if}
</div>
