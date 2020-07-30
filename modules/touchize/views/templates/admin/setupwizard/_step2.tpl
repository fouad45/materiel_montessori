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
        <h2>{$current_step-1|escape:'htmlall':'UTF-8'}: {l s='Customize Theme' mod='touchize'}</h2>
        {include file="{$template_dir|escape:'htmlall':'UTF-8'}setupwizard/_progressbar.tpl" step=$current_step}
        <p class="ingress">
          {l s='This first step gives you the basic graphic styling for your mobile Swipe-2-Buy shop.' mod='touchize'}
          <br>
          {l s='In the module settings you can customise even more.' mod='touchize'}
        </p>
      </div>
      <div class="col-xs-12 col-md-12">
        <div class="panel">
          <div class="form-wrapper">
            <div class="row">
              <h3>{l s='Change logotype' mod='touchize'}
                <!-- {include file="{$template_dir|escape:'htmlall':'UTF-8'}setupwizard/_helptooltip.tpl" helptext='Coming soon!'} -->
              </h3>
              <div class="row">
                <div class="col-xs-3"></div>
                <div class="col-xs-6">
                  <center>
                    <img class="pointed logo-img" id="preview-img-logo" src="{$logo|escape:'htmlall':'UTF-8'}">
                  </center>
                </div>
                <div class="col-xs-3"></div>
              </div>
              <div class="row">
                <div class="col-xs-3"></div>
                <div class="col-lg-6 col-sm-6 col-xs-12">
                    <h2 class="lead">{l s='Upload logotype:' mod='touchize'}</h2>
                    <div class="input-group">
                      <label class="input-group-btn">
                        <span class="btn btn-primary">
                          <i class="icon-search"></i> {l s='Browse' mod='touchize'}
                          <input class="hidden" id="logo-file-input" type="file" multiple>
                        </span>
                      </label>
                      <input class="form-control" id="logo-name-input" type="text" readonly>
                    </div>
                    <span class="help-block">
                      {l s='Recommended height: 52px' mod='touchize'}
                    </span>
                </div>
                <div class="col-xs-3"></div>
                <div class="clearfix"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-md-12">
        <div class="panel">
          <div class="form-wrapper">
            <div class="row">
              <h3>{l s='Select product list presentation' mod='touchize'}
                <!-- {include file="{$template_dir|escape:'htmlall':'UTF-8'}setupwizard/_helptooltip.tpl" helptext='Coming soon!'} -->
              </h3>
              <div class="row text-center flex-display">
                <div class="col-xs-12 col-md-7 vertical-margin-auto">
                  <div class="col-xs-12 col-md-6">

                    <div class="radio">
                      <label for="touchize_cols_2" class="column-labels">
                        <input type="radio" id="touchize_cols_2" name="touchize_cols" value="2" {if $cols_selection == '2' || $cols_selection == ''}checked="checked"{/if}>
                        <img src="{$img_dir|escape:'html':'UTF-8'}onboarding_content_images/styling/double_row.png" class="img-responsive">
                        <h2 class="lead">{l s='2 Product Columns' mod='touchize'}</h2>                        
                      </label>
                    </div>
                  </div>
                  <div class="col-xs-12 col-md-6">
                    <div class="radio">
                      <label for="touchize_cols_1" class="column-labels">
                        <input type="radio" id="touchize_cols_1" name="touchize_cols" value="1" {if $cols_selection == '1'}checked="checked"{/if}>
                        <img src="{$img_dir|escape:'html':'UTF-8'}onboarding_content_images/styling/single_row.png" class="img-responsive">
                        <h2 class="lead">{l s='1 Product Column' mod='touchize'}</h2>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-md-12">
        <div class="panel">
          <div class="form-wrapper">
            <div class="row">
              <h3>{l s='Select primary color' mod='touchize'}
                <!-- {include file="{$template_dir|escape:'htmlall':'UTF-8'}setupwizard/_helptooltip.tpl" helptext='Coming soon!'} -->
              </h3>
              <div class="row text-center flex-display">
                <div class="col-xs-12 col-md-7 vertical-margin-auto">
                  <div class="row flex-display align-items-center">
                    <div class="col-xs-12 col-md-8">
                      <img src="{$img_dir|escape:'html':'UTF-8'}onboarding_content_images/styling/primary_color.png" class="img-responsive">
                    </div>
                    <div class="col-xs-12 col-md-4">
                      <h2 class="text-left lead">
                          {l s='Select primary color:' mod='touchize'}
                      </h2>
                      <input class="form-control colorpicker-element" name="main_color" id="picker_1" data-picker="true" data-is-color="true" type="text" value="{if $main_color != ''}{$main_color|escape:'htmlall':'UTF-8'}{else}{$default_color|escape:'htmlall':'UTF-8'}{/if}">
                      <div class="color-preview color-preview-setupwizard" id="picker_preview_1" style="background-color: {if $main_color != ''}{$main_color|escape:'htmlall':'UTF-8'}{else}{$default_color|escape:'htmlall':'UTF-8'}{/if}"></div>
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
  <div class="panel-footer text-center height-auto">
    <div class="row">
      <div class="col-xs-3 text-left">
        {* <a class="btn btn-link btn-lg" href="{$wizardsteps.{$current_step-7}|escape:'htmlall':'UTF-8'}"> {l s='Previous' mod='touchize'}</a> *}
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
