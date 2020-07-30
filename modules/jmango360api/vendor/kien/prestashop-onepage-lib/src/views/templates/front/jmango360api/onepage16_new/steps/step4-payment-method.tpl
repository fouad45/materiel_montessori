<form id="payment-method-form" class="checkout-form method-list">
    <div class="text-center p-3 helper-text grey">
        {l s='Please select a payment method' mod='jmango360api'}
    </div>

    {*<fieldset>*}
        {*<dl class="sp-methods" id="checkout-payment-method-load">*}
            {*{foreach from=$payment_methods item='payment_method'}*}
                {*<dt class="payment-module-{$payment_method['name']}">*}

                    {*<label id="payment_lbl_{$payment_method['id_module']|intval}"*}
                           {*for="{$payment_method['name']|escape:'htmlall':'UTF-8'}">*}
                        {*<img style="display: none" src='{$payment_method['payment_image_url']|escape:'htmlall':'UTF-8'}'*}
                             {*alt='{$payment_method['payment_image_url']|escape:'htmlall':'UTF-8'}'/>*}
                        {*<span id='payment_method_name_{$payment_method['id_module']|intval}'>{$payment_method['display_name']|escape:'htmlall':'UTF-8'}</span>*}
                    {*</label>*}
                {*</dt>*}
            {*{/foreach}*}
        {*</dl>*}
    {*</fieldset>*}
    {foreach from=$payment_methods item='payment_method'}
        <div class="radio-option center payment mb-2 clearfix">
            <div class="option-info">
                <img style="{if !$payment_method['payment_image_url']}display:none{/if}"
                     src='{$payment_method['payment_image_url']|escape:'htmlall':'UTF-8'}'
                     alt='{$payment_method['payment_image_url']|escape:'htmlall':'UTF-8'}'/>
                <span class="info-title" id="payment-method-{$payment_method['id_module']|intval}">{$payment_method['display_name']|escape:'htmlall':'UTF-8'}</span>
                {*{if $option.additionalInformation}*}
                    {*<span class="info-desc">{$option.additionalInformation}</span>*}
                {*{/if}*}
            </div>
            <div class="option-select">
                <label class="jm-radio-check mb-0">
                    <input type="hidden" id="{$payment_method['id_module']|intval}_name"
                           value="{$payment_method['payment_module_url']|escape:'htmlall':'UTF-8'}"/>
                    <input type="radio" class="myopccheckout_payment_options" name="payment_method"
                           value="{$payment_method['id_module']|intval}"
                           data-url="{$payment_method['payment_module_url']|escape:'htmlall':'UTF-8'}"
                           data-label="{$payment_method['display_name']|escape:'htmlall':'UTF-8'}"
                           id="{$payment_method['name']|escape:'htmlall':'UTF-8'}"
                           {if count($payment_methods) == 1}checked="checked"{/if}/>
                    <span class="radio-check"></span>
                </label>
            </div>
        </div>
    {/foreach}



    <div class="form-submit fixed">
        <div class="row row-sm" id="payment-method-buttons">
            <div class="col-6 col-xs-6">
                <input type="button" onclick="paymentMethod.back()" value="{l s='Previous' mod='jmango360api'}" class="btn btn-default prev-btn"/>
            </div>
            <div class="col-6 col-xs-6" style="display: none">
                <input type="submit" value="Order Review" class="btn btn-primary submit-btn""/>
            </div>
            <div class="col-6 col-xs-6">
                <input type="button" onclick="paymentMethod.next()" value="{l s='Next' mod='jmango360api'}" class="btn btn-primary submit-btn {if count($payment_methods) != 1}disabled{/if} no-arrow" id="btn-payment-method-next"/>
            </div>
        </div>
    </div>
</form>
<div class="confirmDialog" id="editPaymentMethodConfirmDialog" onclick="$('#editPaymentMethodConfirmDialog').hide()">
    <div class="confirmDialogInner" >
        <div class="confirmDialogContent">
            <div class="confirmDialogMsg">
                {l s='Any unsaved changes will be lost' mod='jmango360api'}
                <br/>
                {l s='Do you want to proceed?' mod='jmango360api'}
            </div>
            <div class="confirmDialogBtn">
                <button type="button" class="btn btn-secondary" onclick="$('#editPaymentMethodConfirmDialog').hide()">{l s='No' mod='jmango360api'}</button>
                <button type="button" class="btn btn-primary" onclick="$('#editPaymentMethodConfirmDialog').hide(); paymentMethod.closeEditPaymentMethodForm()">{l s='Yes' mod='jmango360api'}</button>
            </div>
        </div>
    </div>
</div>
<div id="payment_method_html" style="display:none;">
    {foreach from=$payment_methods item='payment_method'}
        <div id="payment_method_{$payment_method['id_module']|intval}">
            {$payment_method['html']}{*Variable contains html content, escape not required*}
        </div>
    {/foreach}
</div>
<script type="text/javascript">
    var myopc_checkout_url = "{$linkJm->getModuleLink("jmango360api", "jmcheckout")|escape:'quotes':'UTF-8'}";
    var paymentMethod = new PaymentMethod(myopc_checkout_url);
</script>
