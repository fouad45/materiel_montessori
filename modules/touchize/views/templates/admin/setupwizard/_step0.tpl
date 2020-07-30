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
<!-- Start of touchize Zendesk Widget script -->
<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=1a0ec4a5-1fcd-4941-88bc-cfe8fd0305a3"> </script>
<script type="text/javascript">
    window.zESettings = {
        webWidget: {
        offset: {
            horizontal: '100px',
            vertical: '15px'
        }
        }
    };
</script>
<!-- End of touchize Zendesk Widget script -->
<div class="panel">
    <div class="panel-heading">
        <i class="icon-user"></i>
        {l s='Set Up Account' mod='touchize'}
    </div>
    {if $is_multishop_mode}
      {include file="{$template_dir|escape:'htmlall':'UTF-8'}info/multistorewarning.tpl"}
    {else}
    <div class="form-wrapper text-center">
        <h1>
            {l s='Thanks for installing Touchize Commerce "Swipe-2-Buy" module' mod='touchize'}
        </h1>
        <h4 class="text-muted">
            {l s='Please take a minute to create your account to start your 3 months free trial.' mod='touchize'}
        </h4>
    </div>
    <div class="form-wrapper">
        <form class="form-horizontal"role="form" id="confirmation-form">
            <div class="form-group row">
                <label for="touchize_ps_first_name" class="col-sm-3 control-label">{l s='Your first name:' mod='touchize'}</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="touchize_ps_first_name" id="touchize_ps_first_name" aria-describedby="firstNameHelp" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="touchize_ps_last_name" class="col-sm-3 control-label">{l s='Your last name:' mod='touchize'}</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="touchize_ps_last_name" id="touchize_ps_last_name" aria-describedby="lastNameHelp" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="touchize_ps_shop_email" class="col-sm-3 control-label">{l s='Your e-mail:' mod='touchize'}</label>
                <div class="col-sm-6">
                    <input type="email" class="form-control" name="touchize_ps_shop_email" id="touchize_ps_shop_email" aria-describedby="emailHelp" required>
                    <small class="form-text text-muted text-left">{l s='This e-mail will be your username' mod='touchize'}</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="touchize_ps_password" class="col-sm-3 control-label">{l s='Create password:' mod='touchize'}</label>
                <div class="col-sm-6">
                    <input type="password" class="form-control" name="touchize_ps_password" id="touchize_ps_password" required>
                    <input type="hidden" id="domain" name="domain" value="{$domain_name|escape:'htmlall':'UTF-8'}">
                    <input type="hidden" id="tzcb_user_preview_url" name="tzcb_user_preview_url" value="{$preview_url|escape:'htmlall':'UTF-8'}">
                </div>
            </div>
            <div class="form-group row tz_sign_up_notification alert alert-danger">
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <div class="checkbox">
                        <input type="checkbox" class="confirm-box" id="termsPrivacyCheck">
                        <label for="termsPrivacyCheck">
                            {l s='I accept the' mod='touchize'}
                            <a href="https://touchize.com/terms-of-use/" target="_blank">{l s='Terms of Use' mod='touchize'}</a>
                            {l s='and' mod='touchize'}
                            <a href="https://touchize.com/privacy-policy/" target="_blank">{l s='Privacy Policy' mod='touchize'}</a>
                        </label>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg" data-step="{$current_step|escape:'htmlall':'UTF-8'}" disabled="disabled" id="create-account">{l s='Create Account to start trial' mod='touchize'}</button>
                <br>
                <span class="form-text text-muted small-signin-text">{l s='Already have an account?' mod='touchize'}
                    <!-- Button trigger modal -->
                    <a href="" data-toggle="modal" data-target="#signInModal">{l s='Sign in here!' mod='touchize'}</a>
                </span>
            </div>
        </form>
        <!-- Modal -->
        <div class="modal fade" id="signInModal" tabindex="-1" role="dialog" aria-labelledby="signIn" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='touchize'}</span></button>
                        <h3 class="modal-title text-center" id="myModalLabel">{l s='Sign in to an existing account' mod='touchize'}</h3>
                    </div>
                    <div class="modal-body">
                        <div class="form-wrapper">
                            <form class="form-horizontal"role="form" id="signin-form">
                                <div class="form-group row">
                                    <label for="touchize_ps_signin_shop_email" class="col-sm-3 control-label">{l s='E-mail:' mod='touchize'}</label>
                                    <div class="col-sm-6">
                                        <input type="email" class="form-control" name="touchize_ps_signin_shop_email" id="touchize_ps_signin_shop_email" aria-describedby="emailHelp" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="touchize_ps_sigin_password" class="col-sm-3 control-label">{l s='Password:' mod='touchize'}</label>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control" name="touchize_ps_sigin_password" id="touchize_ps_sigin_password" required>
                                    </div>
                                </div>
                            </form>
                            <div class="tz_sign_in_notification alert alert-danger"></div>
                        </div>
                    </div>
                    <div class="modal-footer text-center">
                        <button id="touchize-sign-in" type="button" data-step="{$current_step|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn-lg">{l s='Sign in' mod='touchize'}</button>
                        <span class="form-text text-muted small-signin-text">
                            <a href="https://themecreator.touchize.com/wp-login.php?action=lostpassword" target="_blank">{l s='Lost your password?' mod='touchize'}</a>
                        </span>
                      </div>
                    </div>
                </div>
            </div>
    </div>
    {/if}
</div>
