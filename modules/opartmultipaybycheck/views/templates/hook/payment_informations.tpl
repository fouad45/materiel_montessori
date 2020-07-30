{**
* @category Prestashop
* @category Module
* @author Olivier CLEMENCE <manit4c@gmail.com>
* @copyright  Op'art
* @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
**}
{*
<section>
  <p>{l s='Please send us your check following these rules:' d='Modules.Checkpayment.Shop'}
    <dl>
      <dt>{l s='Amount' d='Modules.Checkpayment.Shop'}</dt>
      <dd>{$checkTotal}</dd>
      <dt>{l s='Payee' d='Modules.Checkpayment.Shop'}</dt>
      <dd>{$checkOrder}</dd>
      <dt>{l s='Send your check to this address' d='Modules.Checkpayment.Shop'}</dt>
      <dd>{$checkAddress nofilter}</dd>
    </dl>
  </p>
</section>
*}
<strong class="dark">{l s='You have chosen to pay by check in %d installments.' sprintf=[$number_payment] mod='opartmultipaybycheck'}</strong>
<br />
{l s='The total amount of your order comes to:' mod='opartmultipaybycheck'}<br />
<span id="amount" class="price">{$total|escape:'htmlall':'UTF-8'}</span>
{if $use_taxes == 1}
    {l s='(tax incl.)' mod='opartmultipaybycheck'}
{/if}