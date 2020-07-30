<div class="text-center p-2 helper-text grey {if !$is_logged || $addresses|@count == 0}notLogin{/if}">
    * {l s='Please fill in all required fields' mod='jmango360api'}
</div>
{if !$is_logged}
    <form id="billing-address-customer-info" class="checkout-form" style="{if $is_logged}display:none;{/if}" >
        <div class="billing-section">
            <h3 class="form-title">{l s='Biller Details' mod='jmango360api'}</h3>
            <div class="billing-body">
                <div id="billing-address-customer-info">
                {render file="$template_dir/onepage17_new/steps/customer-form.tpl"
                    ui=$register_form guest_allowed=$guest_allowed
                    type = "invoice"
                }
                </div>
            </div>
        </div>
    </form>
{/if}
<form id="billing-address-form" class="checkout-form">
    <div class="billing-section">
        {if !$is_logged}
        <h3 class="form-title">{l s='Billing Address' mod='jmango360api'}</h3>
        {/if}
        <div class="billing-body">
            <div id="billing-address">
                {if !$is_logged}
                {/if}
                {render file              = "$template_dir/onepage17_new/steps/address-form.tpl"
                ui                        = $address_form
                type                      = "invoice"
                }
            </div>
            <div class="jm-radio">
                <div class="my-3">
                    <label class="jm-radio-check full-width mb-0">
                        {l s='Ship to this address' mod='jmango360api'}
                        <input type="radio" name="use_for_shipping" checked="checked" value="1">
                        <span class="radio-check float-right"></span>
                    </label>
                </div>
                <div class="my-3">
                    <label class="jm-radio-check full-width mb-0">
                        {l s='Ship to different address' mod='jmango360api'}
                        <input type="radio" name="use_for_shipping"  value="0">
                        <span class="radio-check float-right"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-submit" id="billing-address-detail-button">
            <input type="button" onclick="{if !$is_logged}personalInformation.save({$default_country_id}){else}billing.save(false, {$default_country_id}, '{l s='Edit address details' mod='jmango360api'}'){/if}" value="{l s='Next' mod='jmango360api'}" class="btn btn-primary full-width submit-btn"/>
        </div>
    </div>
</form>
