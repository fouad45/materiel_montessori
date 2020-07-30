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

<div class="border-top">
  <div class="panel">
    <p class="colissimo-order-summary-title">{l s='Order summary' mod='colissimo'}</p>
    <p class="colissimo-order-summary">
      <span><b>{l s='Total order (tax incl.):' mod='colissimo'}</b></span>&nbsp;{displayPrice price=$order_totals.amount currency=$order_totals.id_currency}
      <br/>
      <span><b>{l s='Total shipping (tax incl.):' mod='colissimo'}</b></span>&nbsp;{displayPrice price=$order_totals.shipping currency=$order_totals.id_currency}
      <br/>
      <span><b>{l s='Total weight:' mod='colissimo'}</b></span>&nbsp;{$order_totals.weight|floatval} {$order_totals.weight_unit|escape:'htmlall':'utf-8'}
      <br/>
    </p>
    <div class="table-responsive">
      <table class="table">
        <thead>
        <tr>
          <th><span class="title_box ">{l s='Product name' mod='colissimo'}</span></th>
          <th><span class="title_box ">{l s='Reference' mod='colissimo'}</span></th>
          <th><span class="title_box ">{l s='Quantity' mod='colissimo'}</span></th>
          <th>
            <span class="title_box ">{l s='Unit price' mod='colissimo'}</span>
            <small class="text-muted">{l s='(tax excl.)' mod='colissimo'}</small>
          </th>
          <th>
            <span class="title_box ">{l s='Unit weight' mod='colissimo'}</span>
            <small class="text-muted">{l s='(%s)' sprintf=$order_totals.weight_unit|escape:'htmlall':'utf-8' mod='colissimo'}</small>
          </th>
        </tr>
        </thead>
        <tbody>
        {foreach $order_details as $order_detail}
          <tr class="product-line-row">
            <td>{$order_detail.product_name|escape:'htmlall':'utf-8'}</td>
            <td>{$order_detail.product_reference|escape:'htmlall':'utf-8'}</td>
            <td>{$order_detail.product_quantity|escape:'htmlall':'utf-8'}</td>
            <td>{displayPrice price=$order_detail.unit_price_tax_excl|floatval currency=$order_totals.id_currency|intval}</td>
            <td>{$order_detail.product_weight|floatval}</td>
          </tr>
        {/foreach}
        </tbody>
      </table>
    </div>
  </div>
</div>
