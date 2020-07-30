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

<div class="panel">
  <div class="alert alert-info">
    <p>{l s='On this page, you can edit postage slip for orders that have not been shipped yet.' mod='colissimo'}</p>
    <p>{l s='Select orders for which you want to edit a postage slip:' mod='colissimo'}</p>
    <ul>
      <li>{l s='By ticking orders in the list' mod='colissimo'}</li>
      <li>{l s='By filling manually parcel numbers in the input below' mod='colissimo'}</li>
      <li>{l s='By scanning barcode of labels in the input below' mod='colissimo'}</li>
    </ul>
  </div>
  <div class="row">
    <div class="col-lg-12">
      <div class="pull-right">
        <a href="{$link->getAdminLink('AdminColissimoDepositSlip')|escape:'htmlall':'utf-8'}&render=history"
           class="btn btn-primary">
          <i class="process-icon- icon-time"></i> {l s='Colissimo Postage Slip History' mod='colissimo'}
        </a>
      </div>
      <div class="colissimo-barcode">
        <img src="{$data.img_path|escape:'htmlall':'utf-8'}icons/icon-barcode.png"/>
        <span>{l s='Fill the parcel numbers or scan the labels barcode using a scanner' mod='colissimo'}</span>
        <div class="fixed-width-xl">
          <input type="text" name="colissimo_parcel_number_barcode"
                 id="colissimo-parcel-number-barcode"
                 maxlength="13"
                 class="input fixed-width-xl"/>
        </div>
      </div>
    </div>
  </div>
  <p class="colissimo-shipments-waiting">
    {l s='Shipment(s) waiting' mod='colissimo'}</p>
  <form method="post" class="form-horizontal" id="colissimo-deposit-slip">
    <p class="colissimo-select-unselect">
      <a href="#" class="colissimo-select-all">{l s='Select all' mod='colissimo'}</a>
      /
      <a href="#" class="colissimo-unselect-all">{l s='Unselect all' mod='colissimo'}</a>
    </p>
    <table class="table colissimo-deposit-slip-table">
      <thead>
      <tr>
        <th></th>
        <th><span class="title_box">{l s='Reference' mod='colissimo'}</span></th>
        <th><span class="title_box">{l s='ID' mod='colissimo'}</span></th>
        <th><span class="title_box">{l s='Parcel number' mod='colissimo'}</span></th>
        <th><span class="title_box">{l s='Customer' mod='colissimo'}</span></th>
        <th><span class="title_box">{l s='Order status' mod='colissimo'}</span></th>
        <th><span class="title_box">{l s='Order date' mod='colissimo'}</span></th>
        <th><span class="title_box">{l s='Label generation date' mod='colissimo'}</span></th>
      </tr>
      </thead>
      <tbody>
      {foreach $labels as $info}
        <tr>
          <td>
            <input class="colissimo-label-selection"
                   type="checkbox"
                   name="colissimo_label_{$info.id_colissimo_label|intval}">
          </td>
          <td class="reference">
            <a target="_blank"
               href="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'utf-8'}&vieworder&id_order={$info.id_order|intval}">
              {$info.reference|escape:'htmlall':'utf-8'}
              <sup><i class="icon icon-external-link"></i></sup>
            </a>
          </td>
          <td class="ID">
              {$info.id_order|escape:'htmlall':'utf-8'}
          </td>
          <td class="parcel-number">
            <input class="parcel-number-hidden" type="hidden" name="parcel_number_{$info.shipping_number|escape:'htmlall':'utf-8'}"
                   value="{$info.id_colissimo_label|intval}"/>
            <b>{$info.shipping_number|escape:'htmlall':'utf-8'}</b>
          </td>
          <td class="customer">
            {$info.customer|escape:'htmlall':'utf-8'}
          </td>
          <td class="fixed-width-xxl status-title">
            <span class="label color_field"
                  style="background-color: {$info.state_bg|escape:'htmlall':'utf-8'}; color: {$info.state_color|escape:'htmlall':'utf-8'}">
              {$info.state_name|escape:'htmlall':'utf-8'}
            </span>
          </td>
          <td class="">
            {$info.order_date_add|escape:'htmlall':'utf-8'}
          </td>
          <td class="">
            {$info.label_date_add|escape:'htmlall':'utf-8'}
          </td>
        </tr>
        {foreachelse}
        <tr>
          <td class="list-empty hidden-print" colspan="7">
            <div class="list-empty-msg">
              <i class="icon-warning-sign list-empty-icon"></i>
              {l s='No waiting shipments' mod='colissimo'}
            </div>
          </td>
        </tr>
      {/foreach}
      </tbody>
    </table>
    <p class="colissimo-select-unselect">
      <a href="#" class="colissimo-select-all">{l s='Select all' mod='colissimo'}</a>
      /
      <a href="#" class="colissimo-unselect-all">{l s='Unselect all' mod='colissimo'}</a>
    </p>
    <br/><br/>
    <div class="submit-block">
      <button type="submit" class="btn btn-primary" id="submit-process-colissimo-deposit-slip">
        <i class="process-icon- process-icon-refresh"></i>
        {l s='Generate postage slip' mod='colissimo'}
      </button>
      <br/>
      <img src="{$data.img_path|escape:'htmlall':'utf-8'}loading.svg" style="display: none"/>
    </div>
    <div id="deposit-slip-result"></div>
  </form>
</div>
{literal}
<script type="text/javascript">
    var noLabelSelectedText = "{/literal}{l s='Please select at least one label.' mod='colissimo'}{literal}";
    var noWaitingShipmentsText = "{/literal}{l s='No waiting shipments' mod='colissimo'}{literal}";
    var genericErrorMessage = "{/literal}{l s='An error occured. Please try again.' mod='colissimo'}{literal}";
</script>
{/literal}
