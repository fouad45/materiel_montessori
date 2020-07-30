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
    <div class="form-wrapper">
            {if $is_multishop_mode}
              {include file="{$template_dir|escape:'htmlall':'UTF-8'}info/multistorewarning.tpl"}
            {else}
            <h2 class="text-center">{l s='This setup wizard gives you a mobile ”Swipe-2-Buy” version of your webshop.' mod='touchize'}</h2>

            <br>
        <div class="row">
            <div class="col-xs-4 col-md-4">
            <h4 class="lead">{l s='This wizard takes you through 5 easy steps:' mod='touchize'}</h4>
            <br>
              <ol>
                <li><h4 class="lead">{l s='Customize Theme' mod='touchize'}</h4></li>
                <li><h4 class="lead">{l s='Setup Link Menu' mod='touchize'}</h4></li>
                <li><h4 class="lead">{l s='Setup Category Menu' mod='touchize'}</h4></li>
                <li><h4 class="lead">{l s='Select Landing Page' mod='touchize'}</h4></li>
                <li><h4 class="lead">{l s='Select Instruction Banner' mod='touchize'}</h4></li>
              </ol>
            </div>
            <div class="col-xs-8 col-md-8 text-center">
                <!-- 16:9 aspect ratio -->
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/l2b-NyNYSNE" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
            <div class="col-xs-12 col-md-12 text-center">
              <br>
              <p class="lead">
                {l s='After this setup wizard you can test ”Swipe-2-Buy” on your own mobile devices before you launch it to your customers.' mod='touchize'}
              </p>
            </div>
            </div>
            <div class="panel-footer text-center">
                <div class="row">
                    <div class="col-xs-12 col-md-4 col-md-push-4">
                        <p>
                            <a class="btn btn-primary btn-lg btn-block" href="{$wizardsteps.0|escape:'htmlall':'UTF-8'}">
                                {l s='Start Setup wizard' mod='touchize'}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            {/if}
    </div>
</div>
