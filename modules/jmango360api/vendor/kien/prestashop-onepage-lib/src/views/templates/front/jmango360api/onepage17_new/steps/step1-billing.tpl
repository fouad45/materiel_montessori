<div class="checkoutStep1" id="checkoutStep1">
    <div class="">
        <form id="billing-address-form-list" class="checkout-form" style="{if !$is_logged || $addresses|@count === 0}display:none;{/if}">
            <div class="text-center p-3 helper-text grey">
                {l s='Please select a billing address' mod='jmango360api'}
            </div>
            <div class="container-fluid">
                <div class="saved-address mb-2 clearfix" id="invoice-address-list">
                    {foreach from=$addresses key=k item=address}
                    <div class="row" id="invoice-address-{$address.id_address}">
                        <div class="col-10">
                            <h4 class="address-name">{$address.firstname|escape:'html':'UTF-8'} {$address.lastname|escape:'html':'UTF-8'}</h4>
                            {if 'company'|array_key_exists:$address}
                                <span class="address-company">{$address.company|escape:'html':'UTF-8'}</span>
                            {/if}
                            {if 'vat_number'|array_key_exists:$address}
                                <span class="address-vat">{$address.vat_number|escape:'html':'UTF-8'}</span>
                            {/if}
                            <span class="address-location">{$address.address1|escape:'html':'UTF-8'}</span>
                            {if 'address2'|array_key_exists:$address}<span class="address-location">{$address.address2|escape:'html':'UTF-8'}</span>{/if}
                            {if $address.country|in_array:["Netherlands", "Spain", "Italy", "Germany", "Portugal", "Sweden", "Denmark"]}
                                <span class="address-location">{$address.postcode|escape:'html':'UTF-8'} {$address.state|escape:'html':'UTF-8'} {$address.city|escape:'html':'UTF-8'}</span>
                            {elseif $address.country|in_array:["United Kingdom", "Vietnam"]}
                                <span class="address-location">{$address.city|escape:'html':'UTF-8'}</span>
                                <span class="address-location">{$address.postcode|escape:'html':'UTF-8'} {$address.state|escape:'html':'UTF-8'}</span>
                            {elseif $address.country|in_array:["United States", "Australia"]}
                                <span class="address-location">{$address.city|escape:'html':'UTF-8'} {$address.state|escape:'html':'UTF-8'} {$address.postcode|escape:'html':'UTF-8'}</span>
                            {elseif $address.country|in_array:["China", "France", "Israel"]}
                                <span class="address-location">{$address.postcode|escape:'html':'UTF-8'} {$address.city|escape:'html':'UTF-8'} {$address.state|escape:'html':'UTF-8'}</span>
                            {else}
                                <span class="address-location">{$address.city|escape:'html':'UTF-8'}</span>
                                <span class="address-location">{$address.state|escape:'html':'UTF-8'} {$address.postcode|escape:'html':'UTF-8'}</span>
                            {/if}
                            <span class="address-country">{$address.country|escape:'html':'UTF-8'}</span>
                            {if 'phone'|array_key_exists:$address}
                                <a href="tel:{$address.phone|escape:'html':'UTF-8'}" class="address-mobile">{$address.phone|escape:'html':'UTF-8'}</a>
                            {/if}
                            {if 'phone_mobile'|array_key_exists:$address}
                                <div>
                                    <a href="tel:{$address.phone_mobile|escape:'html':'UTF-8'}" class="address-mobile">{$address.phone_mobile|escape:'html':'UTF-8'}</a>
                                </div>
                            {/if}
                            <div>
                                <a class="blue address-edit" onclick="billing.edit({$address|@json_encode|escape:'html':'UTF-8'}, false)">{l s='Edit address details' mod='jmango360api'}</a>
                            </div>
                        </div>
                        <div class="col-2 text-center">
                            <label class="jm-radio-check mb-0">
                                <input type="radio" name="selected-billing-address" value="{$address.id_address}" {if $address.id_address|intval === $selected_invoice_address|intval}checked{/if}>
                                <span class="radio-check"></span>
                            </label>
                        </div>
                    </div>
                    {/foreach}
                </div>
            </div>
            <div class="new-address">
                <a class="blue address-edit" onclick="billing.edit(null, false, {$default_country_id})">{l s='Use different billing address' mod='jmango360api'}</a>
            </div>
            <div class="form-submit fixed" id="billing-address-buttons">
                <input id="submit-billing-address" type="button" value="{l s='Next' mod='jmango360api'}" class="btn btn-primary full-width submit-btn" onclick="billing.submit()"/>
            </div>
        </form>
    </div>
    <div id="billing-address-details" class="billing-address-details {if $is_logged && $addresses|@count != 0}overlay-window{/if}" style="{if $is_logged && $addresses|@count !== 0}display:none{else}position: inherit {/if}">
        <header id="billing-address-details-header" class="overlay-header" style="{if !$is_logged || $addresses|@count === 0}display:none;{/if}">
            <a class="back-btn" onclick="$('#billingConfirmDialog').show()"></a>
            <h1 class="header-title">{l s='Billing Address' mod='jmango360api'}</h1>
        </header>
        {include file="$template_dir/onepage17_new/steps/billing-address.tpl"}
    </div>
</div>

<div class="confirmDialog" id="billingConfirmDialog" onclick="$('#billingConfirmDialog').hide()">
    <div class="confirmDialogInner" >
        <div class="confirmDialogContent">
            <div class="confirmDialogMsg">
                {l s='Any unsaved changes will be lost' mod='jmango360api'}<br/>
                {l s='Do you want to proceed?' mod='jmango360api'}
            </div>
            <div class="confirmDialogBtn">
                <button type="button" class="btn btn-secondary" onclick="$('#billingConfirmDialog').hide()">{l s='No' mod='jmango360api'}</button>
                <button type="button" class="btn btn-primary" onclick="$('#parent-div').removeClass('showAddressForm');$('body').removeClass('showAddressForm');$('#billingConfirmDialog').hide(); billing.closeAddressFrom()">{l s='Yes' mod='jmango360api'}</button>
            </div>
        </div>
    </div>
</div>

<div class="confirmDialog" id="editBillingConfirmDialog" onclick="$('#editBillingConfirmDialog').hide()">
    <div class="confirmDialogInner" >
        <div class="confirmDialogContent">
            <div class="confirmDialogMsg">
                {l s='Any unsaved changes will be lost' mod='jmango360api'}<br/>
                {l s='Do you want to proceed?' mod='jmango360api'}
            </div>
            <div class="confirmDialogBtn">
                <button type="button" class="btn btn-secondary" onclick="$('#editBillingConfirmDialog').hide()">{l s='No' mod='jmango360api'}</button>
                <button type="button" class="btn btn-primary" onclick="$('#editBillingConfirmDialog').hide(); billing.closeEditBillingAddressForm({$is_logged})">{l s='Yes' mod='jmango360api'}</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var myopc_checkout_url = '{url entity='module' name='jmango360api' controller='jmcheckout' relative_protocol=false}';
    var billing = new Billing(myopc_checkout_url);
    var personalInformation = new PersonalInformation(myopc_checkout_url);
    {if !$is_logged || $addresses|@count === 0}billing.edit(null, false, {$default_country_id}, true);{/if}
    $('#invoice-address-list').prepend($('#invoice-address-'+{$selected_invoice_address}));
</script>