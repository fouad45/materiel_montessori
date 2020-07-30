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
* @author Touchize Sweden AB <prestashop@touchize.com>
    * @copyright 2018 Touchize Sweden AB
    * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
    * International Registered Trademark & Property of Touchize Sweden AB
    *}
    <div class="panel">
        {include file="{$template_dir}setupwizard/_heading.tpl"}
        {if $is_multishop_mode}
        {include file="{$template_dir|escape:'htmlall':'UTF-8'}info/multistorewarning.tpl"}
        {else}
        <div class="form-wrapper">
            <div class="row text-center">
                <h2>{l s='Great, the Setup Wizard is done!' mod='touchize'}</h2>
                <div class="col-md-12">
                    <div class="text-left" style="margin: 1rem auto; display: inline-block;">
                        <ul>
                            <li>
                                <p class="lead">
                                    {l s='Go to Touchize Main Page to experience your webshop as ”Swipe-2-Buy”'
                                    mod='touchize'}
                                </p>
                            </li>
                            <li>
                                <p class="lead">
                                    {l s='Launch it to your mobile customers whenever you are ready during the free trial'
                                    mod='touchize'}
                                </p>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
            <div class="form-group hide">
                <label class="col-sm-2 control-label" for="touchize_ps_shop_name">
                    {l s='Shopname' mod='touchize'}
                </label>
                <div class="col-sm-10">
                    <input class="form-control" id="touchize_ps_shop_name" name="touchize_ps_shop_name"
                        placeholder="{l s='Shopname' mod='touchize'}" type="text"
                        value="{$shop_name|escape:'htmlall':'UTF-8'}">
                </div>
            </div>
            <div class="form-group hide">
                <label class="col-sm-2 control-label" for="touchize_domain_name">
                    {l s='Domain' mod='touchize'}
                </label>
                <div class="col-sm-10">
                    <input class="form-control" id="touchize_domain_name" name="touchize_domain_name"
                        placeholder="{l s='Domain' mod='touchize'}" type="text"
                        value="{$domain_name|escape:'htmlall':'UTF-8'}">
                </div>
            </div>
            <div class="panel-footer text-center">
                <div class="row">
                    <div class="col-xs-12 col-md-4 col-md-push-4">
                        <p>
                            <button class="btn btn-primary btn-lg btn-block next-step"
                                data-step="{$current_step|escape:'htmlall':'UTF-8'}" id="next-step">
                                {l s='LET´S GO TO TOUCHIZE MAIN PAGE!' mod='touchize'}
                            </button>
                        </p>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
    {/if}
    </div>