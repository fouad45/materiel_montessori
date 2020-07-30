{**
* @category Prestashop
* @category Module
* @author Olivier CLEMENCE <manit4c@gmail.com>
* @copyright  Op'art
* @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
**}
{if $status == 'ok'}
	<p class="alert alert-success">{l s='Your order is complete.' mod='opartmultipaybycheck'}</p>
	<div class="box order-confirmation">
		<h3 class="page-subheading">{l s='Payment instructions:' mod='opartmultipaybycheck'}</h3>
		<p class="ompbc_detail_check">
			<strong class="dark">{l s='DETAILS:' mod='opartmultipaybycheck'}</strong><br />
            {if $last_check_amount == $check_amount}
                - {l s='%1$d check for %2$s' sprintf=[$number_check, $check_amount] mod='opartmultipaybycheck'}<br />
            {else}
                - {l s='%1$d check for %2$s' sprintf=[$number_check_minus_one,$check_amount] mod='opartmultipaybycheck'} {l s='and 1 check for %s' sprintf=[$last_check_amount] mod='opartmultipaybycheck'}<br />
            {/if}
			- {l s='Your order reference is:' mod='opartmultipaybycheck'} {$reference|escape:'html':'UTF-8'}<br />
			- {l s='Your order total amount is:' mod='opartmultipaybycheck'} {$total_to_pay|escape:'html':'UTF-8'}			
		</p>
		<strong class="dark">{l s='PLEASE FOLLOW THE INSTRUCTION BELOW:' mod='opartmultipaybycheck'}</strong>
		<p>{$text_confirmation nofilter}</p>
	</div>
{else}
	<p class="alert alert-warning">
		{l s='We noticed a problem with your order. If you think this is an error, feel free to contact our' mod='opartmultipaybycheck'}
		<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='customer service department.' mod='opartmultipaybycheck'}</a>.
	</p>
{/if}
