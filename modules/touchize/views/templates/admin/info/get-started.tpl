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
    {if $is_multishop_mode}
    {else}

    {if (Configuration::get('TOUCHIZE_TRIAL_ACTIVE'))}
    {if (Configuration::get('TOUCHIZE_TRIAL_HAS_BEEN_ACTIVATED'))}
    <div class="ribbon">
        <span>Trial</span>
    </div>
    {/if}
    {/if}

    {if (!{$valid_license|escape:'htmlall':'UTF-8'})}
    <div class="panel">
        {if (Configuration::get('TOUCHIZE_TRIAL_ACTIVE'))}
        <!--- TRIAL MODE---->
        <div class="panel-heading">
            {l s='Your trial is active!' mod='touchize'}
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                {if (ceil(((strtotime('+3 month', Configuration::get('TOUCHIZE_WHEN_TRIAL_WAS_ACTIVATED'))-time())/86400)) <= 10 ) }
                <h1 style="color: #f00">
                    {l s='Your free trial is now active and will expire in' mod='touchize'}
                    {ceil(((strtotime('+3 month', Configuration::get('TOUCHIZE_WHEN_TRIAL_WAS_ACTIVATED'))-time())/86400))|escape:'htmlall':'UTF-8'}
                    {l s='days!' mod='touchize'}
                </h1>
            </div>
            <div class="col-md-12 text-center">
                <p class="lead">{l s='To continue using Touchize Commerce ”Swipe-2-Buy” after the trial period please start a subscription.' mod='touchize'}</p>
                <a class="btn btn-success btn-lg" target="_blank" href="https://touchize.com/subscription/prestashop?lang={$lang_iso|escape:'htmlall':'UTF-8'}" style="padding-left: 60px; padding-right: 60px; text-transform: uppercase;">
                    {l s='Add payment details' mod='touchize'}
                </a>
            </div>
            <div class="col-md-12 text-center">
                {else}
                <h1>
                    {l s='Your free trial is now active and will expire in' mod='touchize'}
                    {ceil(((strtotime('+3 month',
                    Configuration::get('TOUCHIZE_WHEN_TRIAL_WAS_ACTIVATED'))-time())/86400))|escape:'htmlall':'UTF-8'}
                    {l s='days.' mod='touchize'}
                </h1>
                {/if}
                <div class="text-left" style="margin: 1rem auto; display: inline-block;">
                    <ul>
                        <li>
                            <p class="lead">{l s='Try your "Swipe-2-Buy" shop on your own mobile phone or in the simulator below.' mod='touchize'}</p>
                        </li>
                        <li>
                            <p class="lead">{l s='You can launch it to your customers by pressing "LAUNCH MODULE TO CUSTOMERS".' mod='touchize'}</p>
                        </li>
                        <li>
                            <p class="lead">{l s='If you want to customize it more before launch go to the SETTINGS tab.' mod='touchize'}</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row text-center">
            {if (Configuration::get('TOUCHIZE_ENABLED'))}
            <div class="col-lg-12">
                <button class="btn btn-danger btn-lg" data-toggle="modal" data-target="#deactivateModal" style="padding-left: 60px; padding-right: 60px; text-transform: uppercase;">
                    {l s='Pause module to customers' mod='touchize'}
                </button>
                <span class="help-block">
                    {l s='If you ”PAUSE MODULE TO CUSTOMERS” your mobile customers gets your old mobile
                    solution! Launch again whenever you are ready. This do NOT halt the trial period.' mod='touchize'}
                </span>
            </div>
            <!-- MODAL -->
            <div class="modal fade" id="deactivateModal" tabindex="-1" role="dialog" aria-labelledby="deactivateModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <p class="lead">
                            {l s='Are you sure that you want to pause the ”Swipe-2-Buy” module for your mobile customers?' mod='touchize'}
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default tz-btn-default" data-dismiss="modal">{l
                            s='Cancel' mod='touchize'}</button>
                        <button type="button" id="deactivate" class="btn btn-danger" style="text-transform: uppercase;">{l s='Pause module'
                            mod='touchize'}</button>
                    </div>
                </div>
            </div>
          </div>
          <!-- MODAL ENDS -->
            <script>
                var ajaxurl = '{$link->getAdminLink("AdminGetStarted")|escape:'html':'UTF-8'}';
                document.getElementById('deactivate').addEventListener('click', function () {
                    var data = {
                        is_ajax: true,
                        state: 0
                    };
                    $.ajax({
                        url: ajaxurl,
                        data: data,
                        type: 'POST',
                        dataType: 'json',
                        beforeSend: function () {},
                        error: function (xhr, status, error) {},
                        success: function (data) {},
                        complete: function () {
                            location.reload();
                        }
                    });
                });
            </script>
            {else}
            <div class="col-lg-12">
                <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#activateModal" style="padding-left: 60px; padding-right: 60px;">
                    {l s='Launch module to customers' mod='touchize'}
                </button>
                <span class="help-block">
                    {l s='When you activate ”LAUNCH MODULE TO CUSTOMERS” your mobile customers will get ”Swipe-2-Buy”.'
                    mod='touchize'}
                </span>
            </div>
              <!-- MODAL -->
              <div class="modal fade" id="activateModal" tabindex="-1" role="dialog" aria-labelledby="activateModalLabel"
              aria-hidden="true">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-body text-center">
                         <p class="lead">
                                {l s='This activates Touchize Swipe-2-Buy for your mobile customers on both Mobile and Tablets.' mod='touchize'}
                         </p>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-default tz-btn-default" data-dismiss="modal">{l
                              s='Cancel' mod='touchize'}</button>
                          <button type="button" id="activate" class="btn btn-primary">{l s='Continue'
                              mod='touchize'}</button>
                      </div>
                  </div>
              </div>
          </div>
          <!-- MODAL ENDS -->
            <script>
                var ajaxurl = '{$link->getAdminLink("AdminGetStarted")|escape:'html':'UTF-8'}';
                document.getElementById('activate').addEventListener('click', function () {
                    var data = {
                        is_ajax: true,
                        state: 3
                    };
                    $.ajax({
                        url: ajaxurl,
                        data: data,
                        type: 'POST',
                        dataType: 'json',
                        beforeSend: function () {},
                        error: function (xhr, status, error) {},
                        success: function (data) {},
                        complete: function () {
                            location.reload();
                        }
                    });
                });
            </script>
            {/if}
        </div>
        <!--- TRIAL MODE ENDS---->
        {else}
            {if (Configuration::get('TOUCHIZE_TRIAL_HAS_BEEN_ACTIVATED'))}
                <!--- USED MODE---->
                <div class="panel-heading">
                    {l s='Your trial has experied' mod='touchize'}
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h2 style="color: #f00">
                            {l s='Your free trial has expired!' mod='touchize'}
                        </h2>
                        <h2>
                            {l s='To continue using the Touchize Commerce "Swipe-2-Buy" module you need to start a subscription.' mod='touchize'}
                        </h2>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-lg-12">
                        <a class="btn btn-success btn-lg" target="_blank" style="text-transform: uppercase; padding-left: 60px; padding-right: 60px;"
                            href="https://subscription.touchize.com/prestashop?lang={$lang_iso|escape:'htmlall':'UTF-8'}"
                            style="padding-left: 60px; padding-right: 60px;">
                            {l s='Add payment details' mod='touchize'}
                        </a>
                    </div>
                </div>
                <!--- USED MODE ENDS---->
            {/if}
        {/if}
    </div>
    {else}
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-user"></i>
            {l s='YOUR TOUCHIZE COMMERCE MODULE IS ACTIVE' mod='touchize'}
        </div>
        <div class="row flex-display align-items-center">
            <div class="col-lg-12 text-center">
                <h2>
                    {l s='Your Touchize Commerce ”Swipe-2-Buy” subscription is now active and you have 100 free Go-To-Checkouts every month.' mod='touchize'}
                </h2>
                {if (Configuration::get('TOUCHIZE_ENABLED'))}
                <div class="col-lg-12">
                    <button class="btn btn-danger btn-lg" data-toggle="modal" data-target="#deactivateModal" style="padding-left: 60px; padding-right: 60px; text-transform: uppercase;">
                        {l s='Pause module to customers' mod='touchize'}
                    </button>
                    <span class="help-block">
                        {l s='If you ”PAUSE MODULE TO CUSTOMERS” your mobile customers gets your old mobile solution!
                        This do NOT remove the module from your webshop. Launch again whenever you are ready.' mod='touchize'}
                    </span>
                </div>
                <!-- MODAL -->
                <div class="modal fade" id="deactivateModal" tabindex="-1" role="dialog" aria-labelledby="deactivateModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <p class="lead">
                                {l s='Are you sure that you want to pause the ”Swipe-2-Buy” module for your mobile customers?' mod='touchize'}
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default tz-btn-default" data-dismiss="modal">{l
                                s='Cancel' mod='touchize'}</button>
                            <button type="button" id="deactivate" class="btn btn-danger" style="text-transform: uppercase;">{l s='Pause module'
                                mod='touchize'}</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- MODAL ENDS -->
                <script>
                    var ajaxurl = '{$link->getAdminLink("AdminGetStarted")|escape:'html':'UTF-8'}';
                    document.getElementById('deactivate').addEventListener('click', function () {
                        var data = {
                            is_ajax: true,
                            state: 0
                        };
                        $.ajax({
                            url: ajaxurl,
                            data: data,
                            type: 'POST',
                            dataType: 'json',
                            beforeSend: function () {},
                            error: function (xhr, status, error) {},
                            success: function (data) {},
                            complete: function () {
                                location.reload();
                            }
                        });
                    });
                </script>
                {else}
                <div class="col-lg-12">
                    <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#activateModal" style="padding-left: 60px; padding-right: 60px;">
                        {l s='Launch module to customers' mod='touchize'}
                    </button>
                    <span class="help-block">
                        {l s='This will enable ”Swipe-2-Buy” for your mobile customers.' mod='touchize'}
                    </span>
                </div>
                <!-- MODAL -->
                <div class="modal fade" id="activateModal" tabindex="-1" role="dialog" aria-labelledby="activateModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <p class="lead">
                                    {l s='This activates Touchize Swipe-2-Buy for your mobile customers on both Mobile and Tablets.' mod='touchize'}
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default tz-btn-default" data-dismiss="modal">{l
                                s='Cancel' mod='touchize'}</button>
                            <button type="button" id="activate" class="btn btn-primary">{l s='Continue'
                                mod='touchize'}</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- MODAL ENDS -->
                <script>
                    var ajaxurl = '{$link->getAdminLink("AdminGetStarted")|escape:'html':'UTF-8'}';
                    document.getElementById('activate').addEventListener('click', function () {
                        var data = {
                            is_ajax: true,
                            state: 3
                        };
                        $.ajax({
                            url: ajaxurl,
                            data: data,
                            type: 'POST',
                            dataType: 'json',
                            beforeSend: function () {},
                            error: function (xhr, status, error) {},
                            success: function (data) {},
                            complete: function () {
                                location.reload();
                            }
                        });
                    });
                </script>
                {/if}
                </div>
            </div>
        </div>
        {/if}
    <div class="panel">
        <h3>
            {l s='Try your Swipe-2-Buy shop' mod='touchize'}
        </h3>
        <div class="row flex-display">
            <div class="col-md-6 text-center flex-display" style="flex-direction: column;">
                <div style="flex-grow: 1;">
                    <h2>
                        {l s='Try it on your mobile' mod='touchize'}
                    </h2>
                    <span class="help-block">
                        {l s='Scan this QR-code on your phone.' mod='touchize'}
                    </span>
                    <div id="preview-qrcode" style="padding: 20px 0;">
                    </div>
                    <h2>{l s='Or use this url on your mobile device:' mod='touchize'}</h2>
                    <p class="lead well" id="pagelink">
                        <i class="icon-globe" style="font-size: 1.2rem; padding-right: 5px"></i>
                        {$link->getPageLink('index')|escape:'htmlall':'UTF-8'}?touchize=yes
                    </p>
                    <script>
                        var QRPath = "{$link->getPageLink('index')|escape:'htmlall':'UTF-8'}?touchize=yes&qrcode=true";
                        var qrcode = new QRCode(document.getElementById("preview-qrcode"), {
                            text: QRPath,
                            width: 250,
                            height: 250,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    </script>
                </div>
                 <a class="btn btn-primary btn-lg" target="_blank"
                 href="https://themecreator.touchize.com" style="padding-left: 60px; padding-right: 60px; -webkit-align-self: center;
                 align-self: center; margin-bottom: 30px;">
                 {l s='Advanced Design Settings' mod='touchize'}
                </a>
            </div>
            <div class="col-md-6">
                <div class="text-center">
                    <h2>
                        {l s='Try it in the simulator' mod='touchize'}
                    </h2>
                    <span class="help-block">
                        {l s='Interact by dragging or clicking the products' mod='touchize'}
                    </span>
                </div>
                <div class="phone-template">
                    <iframe src="{$link->getPageLink('index')|escape:'htmlall':'UTF-8'}?botab=1"></iframe>
                </div>
            </div>
        </div>
    </div>
    {if !empty($videos)}
        <div class="panel">
        <h3>{l s='Video tutorials' mod='touchize'}</h3>
            <div class="row">
            {foreach $videos as $video}
                <div class="col-xs-6 col-md-3">
                    <h4 class="text-center">{$video.snippet.title|escape:'htmlall':'UTF-8'}</h4>
                    <a href="https://www.youtube.com/watch?v={$video.snippet.resourceId.videoId|escape:'htmlall':'UTF-8'}" target="_blank" class="thumbnail video-thumbnail">
                        <img src="{$video.snippet.thumbnails.medium.url|escape:'htmlall':'UTF-8'}" data-src="{$video.snippet.thumbnails.medium.url|escape:'htmlall':'UTF-8'}" alt="{$video.snippet.description|escape:'htmlall':'UTF-8'}">
                    </a>
                </div>
            {/foreach }
            </div>
        </div>
    {/if}
    <div class="panel">
        <div class="row flex-display align-items-center">
            <div class="col-md-3">
                <img class="img-responsive" src="{$img_dir|escape:'htmlall':'UTF-8'}touchize-primary.png"
                    alt="link-menu">
            </div>
            <div class="col-md-9">
                <p class="lead">
                    {l s='Touchize Swipe-2-Buy gives your mobile customers a faster, simpler and more enjoyable shopping
                    experience.' mod='touchize'}
                    {l s='Want to know more about what this module have to offer?' mod='touchize'}
                    {l s='Click on this link: ' mod='touchize'}
                    <a href="https://addons.prestashop.com/en/mobile/32475-touchize-commerce.html"
                        alt="Link to marketplace" target="_blank">{l s='PrestaShop Addons Marketplace'
                        mod='touchize'}</a>
                    {l s='or go to' mod='touchize'}
                    <a href="https://touchize.com/">www.touchize.com</a>
                </p>
            </div>
        </div>
    </div>

    {/if}
