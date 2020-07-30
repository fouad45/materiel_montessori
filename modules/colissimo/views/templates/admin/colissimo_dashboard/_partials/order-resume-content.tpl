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

<p class="colissimo-order-summary-title">{l s='Order summary' mod='colissimo'}</p>
<div class="row">
  <div class="col-sm-4">
    <p class="colissimo-order-summary">
      <span><b>{l s='Total order (tax incl.):' mod='colissimo'}</b></span>&nbsp;{displayPrice price=$order_totals.amount currency=$order_totals.id_currency}
      <br/>
      <span><b>{l s='Total shipping (tax incl.):' mod='colissimo'}</b></span>&nbsp;{displayPrice price=$order_totals.shipping currency=$order_totals.id_currency}
      <br/>
      <span><b>{l s='Total weight:' mod='colissimo'}</b></span>&nbsp;{$order_totals.weight|floatval} {$order_totals.weight_unit|escape:'htmlall':'utf-8'}
      <br/><br/>
      <a class="btn btn-default colissimo-order-page"
         target="_blank"
         href="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'utf-8'}&vieworder&id_order={$id_order|intval}">
        <i class="icon icon-external-link"></i> {l s='Go to order page' mod='colissimo'}
      </a>
    </p>
  </div>
  <div class="col-sm-4">
    <p class="colissimo-order-summary">
      <span><i class="icon icon-truck"></i>&nbsp;<b>{l s='Delivery address' mod='colissimo'}</b></span><br/>
      <span>
        {if $delivery_addr->company}
          {$delivery_addr->company|escape:'htmlall':'utf-8'}
          <br/>
        {/if}
        {$delivery_addr->firstname|escape:'htmlall':'utf-8'} {$delivery_addr->lastname|escape:'htmlall':'utf-8'}<br/>
        {$delivery_addr->address1|escape:'htmlall':'utf-8'}<br/>
        {if $delivery_addr->address2}
          {$delivery_addr->address2|escape:'htmlall':'utf-8'}
          <br/>
        {/if}
        {$delivery_addr->postcode|escape:'htmlall':'utf-8'} {$delivery_addr->city|escape:'htmlall':'utf-8'}{if $delivery_addr->id_state}, {State::getNameById($delivery_addr->id_state)|escape:'htmlall':'utf-8'}{/if}
        <br/>
        {$delivery_addr->country|escape:'htmlall':'utf-8'}<br/>
      </span>
      {if $pickup_point_id}
        <br/>
        <span class="colissimo-pickup-point-number">
          {l s='Pickup point No.:' mod='colissimo'}
          {$pickup_point_id|escape:'htmlall':'utf-8'}
        </span>
      {/if}
    </p>
  </div>
  <div class="col-sm-4">
    <p class="colissimo-order-summary">
      <span><i class="icon icon-user"></i>&nbsp;<b>{l s='Contact details' mod='colissimo'}</b></span><br/>
      <span>
        {$customer->firstname|escape:'htmlall':'utf-8'} {$customer->lastname|escape:'htmlall':'utf-8'}<br/>
        {l s='Phone:' mod='colissimo'} {$delivery_addr->phone|escape:'htmlall':'utf-8'}<br/>
        {l s='Mobile:' mod='colissimo'} {$delivery_addr->phone_mobile|escape:'htmlall':'utf-8'}<br/>
        <a href="mailto:{$customer->email|escape:'htmlall':'utf-8'}">{$customer->email|escape:'htmlall':'utf-8'}</a>
      </span>
    </p>
  </div>
</div>
<div class="row">
  <div class="col-sm-12 colissimo-create-label {if $coliship_enabled}coliship-enabled{/if}">
    <form method="post" action="{$link->getAdminLink('AdminColissimoAffranchissement')|escape:'htmlall':'utf-8'}">
      <input type="hidden" name="colissimo_orderBox[]" value="{$id_colissimo_order|intval}"/>
      <button name="submitProcessColissimoSelection" type="submit" class="btn btn-primary">
        <i class="icon icon-plus-circle"></i>
        {l s='Create a label' mod='colissimo'}
      </button>
    </form>
  </div>
</div>
<div class="table-responsive colissimo-shipments colissimo-shipments-{$id_colissimo_order|intval}">
  {include file="./shipments-table.tpl"}
</div>
{if $shipments}
  <p class="colissimo-update-order-tracking">
    <button class="btn btn-primary" data-colissimo-order-id="{$id_colissimo_order|intval}">
      <i class="icon icon-refresh"></i> {l s='Update tracking of this order' mod='colissimo'}
    </button>
  </p>
{/if}

{literal}
<script type="text/javascript">
    var genericErrorMessage = "{/literal}{l s='An error occurred while displaying order details.' mod='colissimo'}{literal}";
    var channel = '{/literal}{if isset($colissimo_channel)}{$colissimo_channel|escape:'htmlall':'utf-8'}{/if}{literal}';
    var tokenDashboard = '{/literal}{getAdminToken tab='AdminColissimoDashboard'}{literal}';

    $('.colissimo-update-order-tracking button').off('click').on('click', function (e) {
        e.preventDefault();

        var idColissimoOrder = $(this).attr('data-colissimo-order-id');
        var btn = $(this);
        var shipmentsTable = $('.colissimo-shipments-' + parseInt(idColissimoOrder));

        btn.find('i').attr('class', 'icon icon-spin icon-spinner');
        btn.addClass('disabled');
        shipmentsTable.fadeTo('fast', 0.4);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoDashboard',
                ajax: 1,
                token: tokenDashboard,
                action: 'updateOrderTracking',
                id_colissimo_order: parseInt(idColissimoOrder),
                channel: channel
            }
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericErrorMessage);
        }).success(function (data) {
            if (data.errors.length) {
                for (var i = 0; i < data.errors.length; i++) {
                    showErrorMessage(data.errors[i]);
                }
            }
            if (data.success.length) {
                for (i = 0; i < data.success.length; i++) {
                    showSuccessMessage(data.success[i]);
                }
            }
            shipmentsTable.html(data.html);
        }).always(function (data) {
            btn.find('i').attr('class', 'icon icon-refresh');
            btn.removeClass('disabled');
            shipmentsTable.fadeTo('fast', 1);
        });
    });
</script>
{/literal}
