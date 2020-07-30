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
  <div class="form-wrapper">
    <div class="row">
      <div class="col-xs-12 col-md-10 col-md-push-1">
        <h2>{$current_step-1|escape:'htmlall':'UTF-8'}: {l s='Select Instruction Banner' mod='touchize'}</h2>
        {include file="{$template_dir}setupwizard/_progressbar.tpl" step=$current_step}
        <p class="ingress">
          {l s='The Instruction Banner helps your customers to swipe the first product to the cart.' mod='touchize'}
          <br />
          {l s='You can always create and upload your own banner in backoffice after the Setup Wizard' mod='touchize'}
        </p>
      </div>
      <div class="col-xs-12 col-md-12">
        <div class="panel">
          <div class="form-wrapper">
            <div class="row">
              <h3>{l s='Select a Instruction Banner for the landing page' mod='touchize'}
                <!-- {include file="{$template_dir|escape:'htmlall':'UTF-8'}setupwizard/_helptooltip.tpl" helptext='Coming soon!'} -->
              </h3>
              <div class="row flex-display align-items-center">
                <div class="col-xs-12 col-md-3 text-center">
                  <img src="{$img_dir|escape:'html':'UTF-8'}onboarding_content_images/introduction_banner/introduction_banner.png" class="img-responsive">
                </div>
                <div class="col-xs-12 col-md-9">
                  <div id="banners_menu" class="select_menu">
                    {foreach from=$banners_items item=banner}
                      <div class="row animation_banners flex-display align-items-center">
                        <div class="col-md-10 col-md-push-1 banner-labels">
                          <label for="banner_item_{$banner.id_touchize_touchmap|escape:'htmlall':'UTF-8'}">
                            <input type="radio" value="{$banner.id_touchize_touchmap|escape:'htmlall':'UTF-8'}" name="banner_item" id="banner_item_{$banner.id_touchize_touchmap|escape:'htmlall':'UTF-8'}" {if $banner_preselection == $banner.id_touchize_touchmap}checked="checked"{/if}>
                            <img src="{$banner.imageurl|escape:'htmlall':'UTF-8'}" class="img-responsive">
                          </label>
                        </div>
                      </div>
                    {/foreach}
                    <div class="row animation_banners flex-display align-items-center">
                      <div class="col-md-10 col-md-push-1 banner-labels">
                        <label for="banner_item_0">
                          <input type="radio" value="0" name="banner_item" id="banner_item_0">
                          <img src="{$img_dir|escape:'html':'UTF-8'}onboarding_content_images/introduction_banner/no_banner.jpg" class="img-responsive">
                        </label>
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
  </div>
  <div class="panel-footer">
    <div class="row">
      <div class="col-xs-3 text-left">
        <p>
          <a class="btn btn-link btn-lg" href="{$wizardsteps.{$current_step-3}|escape:'htmlall':'UTF-8'}">{l s='Previous' mod='touchize'}</a>
        </p>
      </div>
      <div class="col-xs-6 text-center">
        <p>
          <button class="btn btn-primary btn-lg btn-block next-step" id="next-step" data-step="{$current_step|escape:'htmlall':'UTF-8'}">{l s='Finish' mod='touchize'}</button>
        </p>
      </div>
    </div>
  </div>
  {/if}
</div>
