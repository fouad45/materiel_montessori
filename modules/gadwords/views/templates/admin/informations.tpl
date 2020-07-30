{*
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2019 PrestaShop SA
*  @version  Release: $Revision: 14985 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="informations panel col-lg-10 col-lg-offset-1">
    <div class="col-lg-10 col-lg-offset-1">
        {* Google present Shopping ads *}
        <div class="row gadwords_header">
            <div class="presentation col-lg-6">
                <div class="logo col-lg-12">
                    <img src="{$module_dir|escape:'html':'UTF-8'}views/img/google_logo.png" alt="{l s='Google AdWords' mod='gadwords'}" />
                </div>
                <div class="col-lg-12">
                    <div class="row">
                        <img src="{$module_dir|escape:'html':'UTF-8'}views/img/for_shopping.png" alt="{l s='for shopping' mod='gadwords'}" />
                        <p>
                            {l s='Make it easy for shoppers to discover and buy your products with a Google Smart Shopping campaign in PrestaShop.' mod='gadwords'}
                        </p>
                    </div>
                </div>
            </div>
            <div class="video col-lg-6 text-center">
                <span class="items-video-promotion"><object type="text/html" data="{l s='//www.youtube.com/embed/FY-rs8qGDYw?rel=0&amp;controls=0&amp;showinfo=0' mod='gadwords'}" width="400" height="225"></object></span>
            </div>
        </div>
        {* Reassurance *}
        <div class="row gadwords-content">
            <div class="col-lg-8 col-lg-offset-2">
                <div class="row">
                    <div class="col-lg-2">
                        <img src="{$module_dir|escape:'html':'UTF-8'}views/img/Ads_icon_benefit01.png"/>
                    </div>
                    <div class="col-lg-10">
                        <p class="title">{l s='Smarter Campaigns. Wider reach.' mod='gadwords'}</p>
                        <p class="content">{l s='Reach shoppers searching for the products you offer at the moment they want to purchase them - across Google Search, YouTube, Gmail and more!' mod='gadwords'}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <img src="{$module_dir|escape:'html':'UTF-8'}views/img/Ads_icon_benefit02.png"/>
                    </div>
                    <div class="col-lg-10">
                        <p class="title">{l s='No time? No problem!' mod='gadwords'}</p>
                        <p class="content">{l s='After installation, all you need to do is setting a budget and Google smart technology does all the rest, like ad placement and bidding. Pay only when shoppers click on your Ads.' mod='gadwords'}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <img src="{$module_dir|escape:'html':'UTF-8'}views/img/Ads_icon_benefit03.png"/>
                    </div>
                    <div class="col-lg-10">
                        <p class="title">{l s='A picture is worth a thousand words' mod='gadwords'}</p>
                        <p class="content">{l s='Many advertisers will see their Click Through Rate double or even triple with Shopping ads compared with text-only ads.' mod='gadwords'}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
