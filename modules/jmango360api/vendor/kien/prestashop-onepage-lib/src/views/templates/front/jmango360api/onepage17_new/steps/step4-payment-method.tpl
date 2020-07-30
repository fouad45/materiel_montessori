{hook h='displayPaymentTop'}

<div id="payment-method-form" class="checkout-form">
    <div class="text-center p-3 helper-text grey">
        {l s='Please select a payment method' mod='jmango360api'}
    </div>
    {if $is_free}
        <p>{l s='No payment needed for this order'  mod='jmango360api'}</p>
    {/if}
    {foreach from=$payment_options item="module_options"}
        {foreach from=$module_options item="option"}
            <div class="radio-option center payment mb-2 clearfix">
                <div class="option-info">
                    <h4 class="info-title">{$option.call_to_action_text}</h4>
                    {*{if $option.additionalInformation}*}
                        {*<span class="info-desc">{$option.additionalInformation}</span>*}
                    {*{/if}*}
                </div>
                <div class="option-select">
                    <label class="jm-radio-check mb-0">
                        <input type="radio" name="payment-option" {if $option.binary} binary {/if}
                               id="{$option.id}"
                               data-module-name="{$option.module_name}"
                               name="payment-option"
                               type="radio"
                               value="{$option.module_name}"
                               required
                                {if $selected_payment_option == $option.module_name || $is_free} checked {/if}>
                        <span class="radio-check"></span>
                    </label>
                </div>
            </div>
            {if $option.form}
                {$option.form}
            {else}
                <form id="payment-form-submit-{$option.id}" method="POST"
                      action="{$option.action}">
                    {foreach from=$option.inputs item=input}
                        <input type="{$input.type}" name="{$input.name}"
                               value="{$input.value}">
                    {/foreach}
                    <button style="display:none" id="pay-with-{$option.id}"
                            type="submit"></button>
                </form>
            {/if}
        {/foreach}
        {foreachelse}
        <p class="alert alert-danger">{l s='Unfortunately, there are no payment method available.'  mod='jmango360api'}</p>
    {/foreach}
    {hook h='displayPaymentByBinaries'}
    <div class="form-submit fixed">
        <div class="row row-sm" id="payment-method-buttons">
            <div class="col-6">
                <input type="button" onclick="paymentMethod.back()" value="{l s='Previous' mod='jmango360api'}" class="btn btn-default prev-btn"/>
            </div>
            <div class="col-6" style="display: none">
                <input type="submit" value="Order Review" class="btn btn-primary submit-btn""/>
            </div>
            <div class="col-6">
                <input type="button" onclick="paymentMethod.next()" value="{l s='Next' mod='jmango360api'}" class="btn btn-primary submit-btn disabled no-arrow" id="btn-payment-method-next"/>
            </div>
        </div>
    </div>
</div>
<div class="confirmDialog" id="editPaymentMethodConfirmDialog" onclick="$('#editPaymentMethodConfirmDialog').hide()">
    <div class="confirmDialogInner" >
        <div class="">
            <div class="">
                {l s='Any unsaved changes will be lost' mod='jmango360api'}
                <br/>
                {l s='Do you want to proceed?' mod='jmango360api'}
            </div>
            <div class="">
                <button type="button" class="btn btn-secondary" onclick="$('#editPaymentMethodConfirmDialog').hide()">{l s='No' mod='jmango360api'}</button>
                <button type="button" class="btn btn-primary" onclick="$('#editPaymentMethodConfirmDialog').hide(); paymentMethod.closeEditPaymentMethodForm()">{l s='Yes' mod='jmango360api'}</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var myopc_checkout_url = '{url entity='module' name='jmango360api' controller='jmcheckout' relative_protocol=false}';
    var paymentMethod = new PaymentMethod(myopc_checkout_url);
    {*$("input[name=payment-option][value=" + {$selected_payment_option} + "]").attr('checked', 'checked');*}
</script>
