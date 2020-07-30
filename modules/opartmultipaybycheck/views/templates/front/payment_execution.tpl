{**
* @category Prestashop
* @category Module
* @author Olivier CLEMENCE <manit4c@gmail.com>
* @copyright  Op'art
* @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
**}
{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='opartmultipaybycheck'}">{l s='Checkout' mod='opartmultipaybycheck'}</a><span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s='Check payment' mod='opartmultipaybycheck'}
{/capture}

<h1 class="page-heading">{l s='Order summary' mod='opartmultipaybycheck'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if isset($nbProducts) && $nbProducts <= 0}
	<p class="warning">{l s='Your shopping cart is empty.' mod='opartmultipaybycheck'}</p>
{else}
<form action="{$link->getModuleLink('opartmultipaybycheck', 'validation', [], true)|escape:'html':'UTF-8'}" method="post">
	<div class="box ompbc_box">
		<h3 class="page-subheading">{l s='Check payment in %d installments' sprintf=$number_payment mod='opartmultipaybycheck'}</h3>
		<p>
			<strong class="dark">{l s='You have chosen to pay by check in %d installments.' sprintf=$number_payment mod='opartmultipaybycheck'}</strong>
			<br />
			{l s='The total amount of your order comes to:' mod='opartmultipaybycheck'}
			<span id="amount" class="price">{displayPrice price=$total}</span>
			{if $use_taxes == 1}
				{l s='(tax incl.)' mod='opartmultipaybycheck'}
			{/if}
		</p>
		<p>
			<br />
			<strong class="dark">{l s='Please confirm your order by clicking \'I confirm my order\'.' mod='opartmultipaybycheck'}</strong>
			<br />
			({l s='The detail of each check, owner and address information will be displayed on the next page.' mod='opartmultipaybycheck'})
		</p>
		<p class="cart_navigation clearfix" id="cart_navigation">
			<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" class="button-exclusive btn btn-default">
				<i class="icon-chevron-left"></i>{l s='Other payment methods' mod='opartmultipaybycheck'}
			</a>
			<button type="submit" class="button btn btn-default button-medium">
				<span>{l s='I confirm my order' mod='opartmultipaybycheck'}<i class="icon-chevron-right right"></i></span>
			</button>
		</p>
	</div>
</form>
{/if}
