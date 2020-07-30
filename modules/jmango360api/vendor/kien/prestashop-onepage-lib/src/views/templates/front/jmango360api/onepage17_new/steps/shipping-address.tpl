<div class="text-center p-2 helper-text grey">
    * {l s='Please fill in all required fields' mod='jmango360api'}
</div>
<form id="shipping-address-form" class="checkout-form">
    <div class="billing-section">

        <div class="billing-body">
            <div id="shipping-address">
                {render file              = "$template_dir/onepage17_new/steps/address-form.tpl"
                ui                        = $address_form
                type                      = "delivery"
                }
            </div>
        </div>
        {if $is_logged}
            <div class="form-submit">
                <input type="button" onclick="shipping.save(false,'{l s='Edit address details' mod='jmango360api'}')" value="{l s='Next' mod='jmango360api'}" class="btn btn-primary full-width submit-btn"/>
            </div>
        {else}
            <div class="form-submit fixed" id="shipping-address-detail-button">
                <div class="row row-sm" id="shipping-address-buttons">
                    <div class="col-6">
                        <input id="shipping-address-back" type="button" value="{l s='Previous' mod='jmango360api'}" class="btn btn-default prev-btn" onclick="shipping.back()"/>
                    </div>
                    <div class="col-6">
                        <input id="submit-shipping-address" type="button" value="{l s='Next' mod='jmango360api'}" class="btn btn-primary submit-btn" onclick="shipping.save(true, '{l s='Edit address details' mod='jmango360api'}')"/>
                    </div>
                </div>
            </div>
        {/if}
    </div>
</form>
