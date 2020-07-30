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

{if !$order.relais}
  <i class="addr-status icon {if $order.address_valid}icon-check{else}icon-times{/if}"></i>
  <i class="icon icon-pencil"></i>
{/if}
<div>
  <b>{$order.delivery_addr->lastname|escape:'html':'utf-8'|upper} {$order.delivery_addr->firstname|escape:'html':'utf-8'|upper}</b><br/>
  {$order.delivery_addr->company|escape:'html':'utf-8'}<br/>
  {$order.delivery_addr->address1|escape:'html':'utf-8'}<br/>
  {if $order.delivery_addr->address2}
    {$order.delivery_addr->address2|escape:'html':'utf-8'}
    <br/>
  {/if}
  {$order.delivery_addr->postcode|escape:'html':'utf-8'} {$order.delivery_addr->city|escape:'html':'utf-8'}{if $order.delivery_state}, {$order.delivery_state|escape:'html':'utf-8'}{/if}
  <br/>
  {$order.delivery_addr->country|escape:'html':'utf-8'}
  {if $order.relais}
    <p>{l s='Mobile phone provided by customer' mod='colissimo'}</p>
    <input type="text"
           autocomplete="tel-national"
           name="colissimo_pickup_mobile_{$key|intval}"
           id="colissimo-pickup-mobile_{$key|intval}"
           value="{$order.delivery_addr->phone_mobile|escape:'html':'utf-8'}"
           class="colissimo-pickup-mobile-phone input fixed-width-lg"/>
  {/if}
</div>
