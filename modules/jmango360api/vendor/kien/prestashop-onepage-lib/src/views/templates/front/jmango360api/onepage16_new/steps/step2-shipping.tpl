<div class="checkoutStep2">
    <div class="">
        <form class="checkout-form" style="{if !$is_logged}display:none;{/if}">
            <div class="text-center p-3 helper-text grey">
                {l s='Please select a shipping address' mod='jmango360api'}
            </div>
            <div class="saved-address-list">
                <div class="saved-address mb-2 clearfix same-address">
                    <div class="row">
                        <div class="col-10 col-xs-10">
                            <span class="d-inline-block align-middle">{l s='Same as billing address' mod='jmango360api'}</span>
                        </div>
                        <div class="col-2 col-xs-2 text-center">
                            <label class="jm-radio-check mb-0 top-0">
                                <input type="radio" name="selected-shipping-address" id="same-as-billing-address">
                                <span class="radio-check"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div id="delivery-address-list">
                {foreach from=$addresses key=k item=address}
                    <div class="saved-address mb-2 clearfix" id="delivery-address-{$address.id_address}">
                        <div class="row mb-2 positionRelative">
                            <div class="col-10 col-xs-10">
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
                                    <a class="blue address-edit" onclick="shipping.edit({$address|@json_encode|escape:'html':'UTF-8'})">{l s='Edit address details' mod='jmango360api'}</a>
                                </div>
                            </div>
                            <div class="col-2 col-xs-2 positionStatic text-center">
                                <label class="jm-radio-check mb-0" id="shipping-radio-{$address.id_address}">
                                </label>
                            </div>
                        </div>
                    </div>
                {/foreach}
                </div>
            </div>
            <div class="new-address">
                <a class="blue address-edit" onclick="shipping.edit(null, false, {$default_country_id})">{l s='Use different shipping address' mod='jmango360api'}</a>
            </div>
            <div class="form-submit fixed">
                <div class="row row-sm" id="shipping-address-buttons">
                    <div class="col-6 col-xs-6">
                        <input id="shipping-address-back" type="button" value="{l s='Previous' mod='jmango360api'}" class="btn btn-default prev-btn" onclick="shipping.back();  {if $is_logged}$('#billing-address-form-list').show(); $('#billing-address-details').css('position', ''), $('#billing-address-details-header').show(){/if}"/>
                    </div>
                    <div class="col-6 col-xs-6">
                        <input id="submit-shipping-address" type="button" value="{l s='Next' mod='jmango360api'}" class="btn btn-primary submit-btn" onclick="shipping.submit()"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="shipping-address-details" class="shipping-address-details {if $is_logged}overlay-window{/if}" style="{if $is_logged}display:none{else}position: inherit {/if}">
        <header class="overlay-header" style="{if !$is_logged}display:none;{/if}">
            <a class="back-btn" onclick="$('#shippingConfirmDialog').show()"></a>
            <h1>{l s='Shipping Address' mod='jmango360api'}</h1>
        </header>
        {include file="$template_dir/onepage16_new/steps/shipping-address.tpl"}
    </div>
</div>

<div class="confirmDialog" id="shippingConfirmDialog" onclick="$('#shippingConfirmDialog').hide()">
    <div class="confirmDialogInner" >
        <div class="confirmDialogContent">
            <div class="confirmDialogMsg">
                {l s='Any unsaved changes will be lost' mod='jmango360api'}
                <br/>
                {l s='Do you want to proceed?' mod='jmango360api'}
            </div>
            <div class="confirmDialogBtn">
                <button type="button" class="btn btn-secondary" onclick="$('#shippingConfirmDialog').hide()">{l s='No' mod='jmango360api'}</button>
                <button type="button" class="btn btn-primary" onclick="$('#shippingConfirmDialog').hide(); shipping.closeAddressFrom()">{l s='Yes' mod='jmango360api'}</button>
            </div>
        </div>
    </div>
</div>


<div class="confirmDialog" id="editShippingConfirmDialog" onclick="$('#editShippingConfirmDialog').hide()">
    <div class="confirmDialogInner" >
        <div class="">
            <div class="">
                {l s='Any unsaved changes will be lost' mod='jmango360api'}
                <br/>
                {l s='Do you want to proceed?' mod='jmango360api'}
            </div>
            <div class="">
                <button type="button" class="btn btn-secondary" onclick="$('#editShippingConfirmDialog').hide()">{l s='No' mod='jmango360api'}</button>
                <button type="button" class="btn btn-primary" onclick="$('#editShippingConfirmDialog').hide(); shipping.closeEditShippingAddressForm({$is_logged})">{l s='Yes' mod='jmango360api'}</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var countries = {$countries|json_encode};
    var addresses = {$addresses|json_encode};
    var id_address = {$selected_delivery_address_id|intval};
    var customer = {$customer|json_encode};
    var myopc_checkout_url = "{$linkJm->getModuleLink("jmango360api", "jmcheckout")|escape:'quotes':'UTF-8'}";
    var shipping = new Shipping(myopc_checkout_url, countries, addresses, customer);
    $(document).ready(function () {
        shipping.renderCheckbox(addresses, id_address);
    });
</script>