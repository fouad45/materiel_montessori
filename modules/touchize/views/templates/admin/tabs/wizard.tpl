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
    <div class="panel-heading">
        <i class="icon-info">
        </i>
        {l s='Customize your theme' mod='touchize'}
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 text-center">
                <img alt="modern-template" class="img-responsive" src="{$img_dir|escape:'htmlall':'UTF-8'}modern_template.jpg">
                </img>
            </div>
            <div class="col-md-3 text-center">
                <img alt="classic-template" class="img-responsive" src="{$img_dir|escape:'htmlall':'UTF-8'}classic_template.jpg">
                </img>
            </div>
            <div class="col-md-3 text-center">
                <img alt="clean-template" class="img-responsive" src="{$img_dir|escape:'htmlall':'UTF-8'}clean_template.jpg">
                </img>
            </div>
            <div class="col-md-3 text-center">
                <img alt="lines-template" class="img-responsive" src="{$img_dir|escape:'htmlall':'UTF-8'}lines_template.jpg">
                </img>
            </div>
        </div>
    </div>
    {if $is_confirmed}
    <div class="panel-footer text-center">
        <a href="{$clientBuilderUrl|escape:'htmlall':'UTF-8'}" target="_blank" id="start-subscription" class="btn btn-primary btn-lg" style="padding-left: 60px; padding-right: 60px;">
            {l s='Go to Theme Creator' mod='touchize'}
        </a>
    </div>
    {else}
    <div class="panel">
        <div class="row hidden" id="create-user-error">
            <div class="alert alert-danger" id="alert-message" role="alert"></div>
            <div class="form-wrapper">
              <h2 id="heading-message"></h2>
              <div class="row">
                  <div class="col-xs-12">
                      <p class="lead">
                          {l s='Click the link to customize your theme' mod='touchize'}
                      </p>
                      <a class="btn btn-link btn-lg btn-block" href="{$clientBuilderUrl|escape:'htmlall':'UTF-8'}" id="start-subscription" target="_blank">
                          {l s='Customize your theme' mod='touchize'}
                      </a>
                  </div>
              </div>
            </div>
        </div>
        <form class="form-horizontal" id="confirmation-form" role="form">
            <div class="form-wrapper">
                <div class="row">
                    <h3>
                        {l s='Confirm your credentials' mod='touchize'}
                    </h3>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="touchize_ps_shop_name">
                            {l s='Shopname' mod='touchize'}
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" id="touchize_ps_shop_name" name="touchize_ps_shop_name" placeholder="{l s='Shopname' mod='touchize'}" type="text" value="{$shop_name|escape:'htmlall':'UTF-8'}">
                            </input>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="touchize_domain_name">
                            {l s='Domain' mod='touchize'}
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" id="touchize_domain_name" name="touchize_domain_name" placeholder="{l s='Domain' mod='touchize'}" type="text" value="{$domain_name|escape:'htmlall':'UTF-8'}">
                            </input>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="touchize_ps_shop_email">
                            {l s='E-mail' mod='touchize'}
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" id="touchize_ps_shop_email" name="touchize_ps_shop_email" placeholder="{l s='E-mail' mod='touchize'}" type="email" value="{$shop_email|escape:'htmlall':'UTF-8'}">
                            </input>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input class="confirm-box" type="checkbox">
                                        {l s='I´ve read and accept the' mod='touchize'}
                                        <a href="https://touchize.com/terms-of-use/" target="_blank">
                                            {l s='Terms & Conditions' mod='touchize'}
                                        </a>
                                    </input>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input class="confirm-box" type="checkbox">
                                        {l s='I´ve read and accept the' mod='touchize'}
                                        <a href="https://touchize.com/privacy-policy/" target="_blank">
                                            {l s='Privacy Policy' mod='touchize'}
                                        </a>
                                    </input>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer text-center">
                <div class="row">
                    <div class="col-xs-12 col-md-4 col-md-push-4">
                        <p>
                            <button class="btn btn-primary btn-lg btn-block" disabled="disabled" id="confirmation-theme">
                                {l s='Go to Theme Creator' mod='touchize'}
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
    {/if}
</div>
