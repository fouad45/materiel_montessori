<div class="text-center p-2 helper-text grey">
    * {l s='Please fill in all required fields' mod='jmango360api'}
</div>
<form id="shipping-address-form" class="checkout-form">
    <div class="billing-section">
        <div class="billing-body">
            <div id="shipping-address">
                {include file = "$template_dir/onepage16_new/steps/form-fields-shipping.tpl"}
            </div>
            {*<div class="jm-radio">*}
                {*<div class="my-3">*}
                    {*<label class="jm-radio-check full-width mb-0">*}
                        {*Ship to this address*}
                        {*<input type="radio" name="use_for_shipping" checked="checked" value="1">*}
                        {*<span class="radio-check float-right"></span>*}
                    {*</label>*}
                {*</div>*}
                {*<div class="my-3">*}
                    {*<label class="jm-radio-check full-width mb-0">*}
                        {*Ship to different address*}
                        {*<input type="radio" name="use_for_shipping"  value="0">*}
                        {*<span class="radio-check float-right"></span>*}
                    {*</label>*}
                {*</div>*}
            {*</div>*}
        </div>
        {if $is_logged}
            <div class="form-submit">
                <input type="button" onclick="shipping.save(false, '{l s='Edit address details' mod='jmango360api'}')" value="{l s='Next' mod='jmango360api'}" class="btn btn-primary full-width submit-btn"/>
            </div>
        {else}
            <div class="form-submit fixed" id="shipping-address-detail-button">
                <div class="row row-sm" id="shipping-address-buttons">
                    <div class="col-6 col-xs-6">
                        <input id="shipping-address-back" type="button" value="{l s='Previous' mod='jmango360api'}" class="btn btn-default prev-btn" onclick="shipping.back()"/>
                    </div>
                    <div class="col-6 col-xs-6">
                        <input id="submit-shipping-address" type="button" value="{l s='Next' mod='jmango360api'}" class="btn btn-primary submit-btn" onclick="shipping.save(!{$is_logged})"/>
                    </div>
                </div>
            </div>
        {/if}
    </div>
</form>
