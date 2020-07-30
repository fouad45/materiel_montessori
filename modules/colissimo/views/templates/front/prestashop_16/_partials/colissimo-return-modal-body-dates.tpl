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

<div class="colissimo-mailbox-return-dates">
  <div class="">
    <div class="alert alert-info">
      <p class="strong">{l s='How it works?' mod='colissimo'}</p>
      <ul>
        <li>{l s='Print the return label and paste it on the package.' mod='colissimo'}</li>
        <li>
          {l s='Drop the package into your mailbox the day of the pickup.' mod='colissimo'}<br />
          {l s='Your postman will pick it up during the day and drop a receipt.' mod='colissimo'}
        </li>
      </ul>
      <hr>
      <span>
        {l s='A postman will come and pick up your parcel on' mod='colissimo'} <b>{$picking_date_display|escape:'htmlall':'utf-8'}</b>.<br />
        {l s='Please make sure to drop the parcel inside your mailbox before' mod='colissimo'} <b>{$max_picking_hour|escape:'htmlall':'utf-8'}</b>.
      </span>
    </div>
    <p class="colissimo-mailbox-return-address">
      <span>{l s='Mailbox address:' mod='colissimo'}</span>
      {$picking_address.company|escape:'htmlall':'utf-8'}<br />
      {$picking_address.lastname|escape:'htmlall':'utf-8'} {$picking_address.firstname|escape:'htmlall':'utf-8'}<br />
      {$picking_address.address1|escape:'htmlall':'utf-8'}<br />
      {if $picking_address.address2}
        {$picking_address.address2|escape:'htmlall':'utf-8'}<br />
      {/if}
      {$picking_address.postcode|escape:'htmlall':'utf-8'} {$picking_address.city|escape:'htmlall':'utf-8'}<br />
      FRANCE<br />
      <i class="icon icon-truck"></i>
    </p>

    <form method="post" id="colissimo-mailbox-return-confirm">
      <input type="hidden" name="mailbox_company" value="{$picking_address.company|escape:'htmlall':'utf-8'}" />
      <input type="hidden" name="mailbox_lastname" value="{$picking_address.lastname|escape:'htmlall':'utf-8'}" />
      <input type="hidden" name="mailbox_firstname" value="{$picking_address.firstname|escape:'htmlall':'utf-8'}" />
      <input type="hidden" name="mailbox_address1" value="{$picking_address.address1|escape:'htmlall':'utf-8'}" />
      <input type="hidden" name="mailbox_address2" value="{$picking_address.address2|escape:'htmlall':'utf-8'}" />
      <input type="hidden" name="mailbox_postcode" value="{$picking_address.postcode|escape:'htmlall':'utf-8'}" />
      <input type="hidden" name="mailbox_city" value="{$picking_address.city|escape:'htmlall':'utf-8'}" />
      <input type="hidden" name="mailbox_email" value="{$customer.email|escape:'htmlall':'utf-8'}" />
      <input type="hidden" name="mailbox_date" value="{$picking_date|escape:'htmlall':'utf-8'}" />
      <input type="hidden" name="mailbox_hour" value="{$max_picking_hour|escape:'htmlall':'utf-8'}" />
      <input type="hidden" name="id_colissimo_label" value="{$id_colissimo_label|intval}" />
      <button class="btn btn-primary colissimo-submit-confirm" type="submit" name="submitColissimoPickupConfirm">
        <i class="icon icon-check"></i>
        {l s='Confirm the pick-up request' mod='colissimo'}
      </button>
    </form>
  </div>
</div>
