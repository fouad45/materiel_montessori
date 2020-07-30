{**
* @category Prestashop
* @category Module
* @author Olivier CLEMENCE <manit4c@gmail.com>
* @copyright  Op'art
* @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
**}
{if $minimum_amount<=$order_total}
<p class="payment_module">
	<a href="{$link->getModuleLink('opartmultipaybycheck', 'payment')|escape:'html':'UTF-8'}" title="{l s='payment in %d installments by check' sprintf=$number_payment mod='opartmultipaybycheck'}">
		<img src="{$module_dir|escape:'html':'UTF-8'}views/img/multiple_checks.jpg" alt="{l s='payment in %d installments by check' sprintf=$number_payment mod='opartmultipaybycheck'}" width="86" height="49" />
		{l s='payment in %d installments by check' sprintf=$number_payment mod='opartmultipaybycheck'}
	</a>
</p>
{/if}