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
*  @author     PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2019 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($colissimo_pickup_point_error)}
  <div class="alert alert-danger">
    {$colissimo_pickup_point_error|escape:'htmlall':'utf-8'}
  </div>
{else}
  {if isset($colissimo_pickup_point) && $colissimo_pickup_point->id}
    <article id="colissimo-pickup-point-address-selected" class="colissimo-16">
      <input type="hidden" name="id_colissimo_pickup_point" value="{$colissimo_pickup_point->id|intval}"/>
      <header>
        <span class="h4">{l s='Selected pickup-point address' mod='colissimo'}</span>
        <br/>
        <a id="colissimo-select-pickup-point" class="colissimo-edit-point">
          {l s='(Choose another pickup point)' mod='colissimo'}
        </a>
        <div class="colissimo-pickup-point-address">
          {$colissimo_pickup_point->company_name|escape:'htmlall':'utf-8'}<br>
          {$colissimo_pickup_point->address1|escape:'htmlall':'utf-8'}<br>
          {if $colissimo_pickup_point->address2}{$colissimo_pickup_point->address2|escape:'htmlall':'utf-8'}<br>{/if}
          {if $colissimo_pickup_point->address3}{$colissimo_pickup_point->address3|escape:'htmlall':'utf-8'}<br>{/if}
          {$colissimo_pickup_point->zipcode|escape:'htmlall':'utf-8'} {$colissimo_pickup_point->city|escape:'htmlall':'utf-8'}
          <br>
          {$colissimo_pickup_point->country|escape:'htmlall':'utf-8'}<br>
          <p class="colissimo-pickup-point-phone">
            <i class="icon icon-mobile-phone"></i>
            <span>
              {l s='Please confirm your mobile phone number.' mod='colissimo'}<br>
              {l s='You will receive text notifications about the delivery progress.' mod='colissimo'}
            </span>
          </p>
          <input type="tel"
                 class="fixed-width-md colissimo-pickup-mobile-phone"
                 id="colissimo-pickup-mobile-phone"
                 value="{$mobile_phone|escape:'htmlall':'utf-8'}"
                 name="colissimo_pickup_mobile_phone[main]"/>
          <img src="{$colissimo_img_path|escape:'html':'utf-8'}icons/icon_valid.png"
               class="colissimo-mobile-valid js-colissimo-mobile-valid"/>
          <img src="{$colissimo_img_path|escape:'html':'utf-8'}icons/icon_invalid.png"
               class="colissimo-mobile-invalid js-colissimo-mobile-invalid"/>
          <input type="hidden" class="js-colissimo-is-mobile-valid" name="colissimo_is_mobile_valid" value=""/>
          {if $need_mobile_validation}
            <button type="button" class="btn btn-sm btn-primary" id="colissimo-opc-phone-validation">
              <i class="icon icon-check"></i>
              {l s='Confirm' mod='colissimo'}
            </button>
          {/if}
          <p class="js-colissimo-mobile-validation"></p>
        </div>
      </header>
    </article>
  {else}
    <button type="button" class="btn btn-primary" id="colissimo-select-pickup-point" name="colissimoSelectPickupPoint"
            value="1">
      <i class="icon icon-map-marker"></i>
      {l s='Select a pickup point' mod='colissimo'}
    </button>
  {/if}
{/if}
