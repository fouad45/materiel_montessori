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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{capture name=path}{l s='Administrative mandate payment' mod='totadministrativemandate'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

{assign var='current_step' value='payment'}
<div class="table_block">

{include file="$tpl_dir./order-steps.tpl"}
<h1>{l s='Order summary' mod='totadministrativemandate'}</h1>
{if isset($nbProducts) && $nbProducts <= 0}
	<p class="warning">{l s='Your shopping cart is empty.' mod='totadministrativemandate'}</p>
{else}

	<h3>{l s='Administrative mandate payment' mod='totadministrativemandate'}</h3>
	<form action="{$this_path_ssl|escape:'htmlall':'UTF-8'}validation.php" method="post">
		<p>
			<img src="{$this_path|escape:'htmlall':'UTF-8'}/img/logo-payment.png" alt="{l s='Administrative mandate payment' mod='totadministrativemandate' mod='totadministrativemandate'}" width="86" height="49" style="float:left; margin: 0px 10px 5px 0px;" />
			{l s='You have chosen to pay by administrative mandate.' mod='totadministrativemandate'}
			{l s='It can only be used by public bodies.' mod='totadministrativemandate'}
			<br/><br />
			{l s='Here is a short summary of your order:' mod='totadministrativemandate'}
		</p>
		<p style="margin-top:20px;">
			- {l s='The total amount of your order is' mod='totadministrativemandate'}
			<span id="amount" class="price">{displayPrice price=$total}</span>
			{if $use_taxes == 1}
				{l s='(tax incl.)' mod='totadministrativemandate'}
			{/if}
		</p>
		<p>
			{l s='The treatment of your order and the delay of reception' mod='totadministrativemandate'} {l s='will take effect in the date of reception' mod='totadministrativemandate'} {l s='of the order form by our services' mod='totadministrativemandate'}.<br /><br />
		</p>
		<p>
			<br /><br />
			<b>{l s='Please confirm your order by clicking \'I confirm my order\'' mod='totadministrativemandate'}.</b>
		</p>
		<p class="cart_navigation">
			<a href="{$link->getPageLink('order.php', true)|escape:'htmlall':'UTF-8'}?step=3" class="button_large hideOnSubmit">{l s='Other payment methods' mod='totadministrativemandate'}</a>
			<input type="submit" name="submit" value="{l s='I confirm my order' mod='totadministrativemandate'}" class="exclusive" />
		</p>
	</form>
	{literal}
	<script type="text/javascript">
		$(function() {
			$(".container_9 .grid_5").css("width", "auto");
		});
	</script>
	{/literal}
{/if}
</div>
