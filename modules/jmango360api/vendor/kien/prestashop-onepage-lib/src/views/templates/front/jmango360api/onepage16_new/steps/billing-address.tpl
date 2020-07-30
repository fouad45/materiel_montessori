<div class="text-center p-2 helper-text grey {if !$is_logged || $addresses|@count == 0}notLogin{/if}">
    * {l s='Please fill in all required fields' mod='jmango360api'}
</div>
{**
* @license Created by JMango
*}

<form id="billing-address-form" class="checkout-form">
    <div class="billing-section">
        {if !$is_logged}
            <h3 class="form-title">{l s='Biller Details' mod='jmango360api'}</h3>
        {else}
            {*<h3 class="form-title">Billing Address</h3>*}
        {/if}
        <div class="billing-body">
                <div id="billing-address">
                {include file = "$template_dir/onepage16_new/steps/form-fields.tpl"}
                </div>
            <div class="jm-radio">
                <div class="my-3">
                    <label class="jm-radio-check full-width mb-0">
                        {l s='Ship to this address' mod='jmango360api'}
                        <input type="radio" name="billing[use_for_shipping]" checked="checked" value="1">
                        <span class="radio-check float-right"></span>
                    </label>
                </div>
                <div class="my-3">
                    <label class="jm-radio-check full-width mb-0">
                        {l s='Ship to different address' mod='jmango360api'}
                        <input type="radio" name="billing[use_for_shipping]"  value="0">
                        <span class="radio-check float-right"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-submit" id="billing-address-detail-button">
            <input type="button" onclick="billing.save(!{$is_logged}, {$default_country_id}, '{l s='Edit address details' mod='jmango360api'}')" value="{l s='Next' mod='jmango360api'}" class="btn btn-primary full-width submit-btn"/>
        </div>
    </div>
</form>