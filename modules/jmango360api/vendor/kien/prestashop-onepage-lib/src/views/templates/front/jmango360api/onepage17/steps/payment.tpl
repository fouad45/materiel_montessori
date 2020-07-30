{**
* @license Created by JMango
*}

{hook h='displayPaymentTop'}

<div class="content">
    <form class="clearfix"
          id="co-payment-form"
          data-url-update="{url entity='order' params=['ajax' => 1, 'action' => 'selectPaymentOption']}"
          method="post">
        <div class="form-content-wrapper" style="padding: 10px 5px; margin-bottom: -25px">
            {if $is_free}
                <p>{l s='No payment needed for this order'  mod='jmango360api'}</p>
            {/if}
            <div class="payment-options {if $is_free}hidden-xs-up{/if}">
                {foreach from=$payment_options item="module_options"}
                    {foreach from=$module_options item="option"}
                        <div class="payment-module-{$option.module_name|escape:'html':'UTF-8'}">
                            <div id="{$option.id|escape:'html':'UTF-8'}-container" class="payment-option clearfix">
                                {* This is the way an option should be selected when Javascript is enabled *}
                                <span class="custom-radio float-xs-left">
                                <input class="ps-shown-by-js {if $option.binary} binary {/if}"
                                       id="{$option.id|escape:'html':'UTF-8'}"
                                       data-module-name="{$option.module_name|escape:'html':'UTF-8'}"
                                       name="payment-option"
                                       type="radio"
                                       required
                                        {if $selected_payment_option == $option.id || $is_free} checked {/if}>
                                <span></span>
                            </span>
                                {* This is the way an option should be selected when Javascript is disabled *}
                                <form method="GET" class="ps-hidden-by-js">
                                    {if $option.id === $selected_payment_option}
                                        {l s='Selected'  mod='jmango360api'}
                                    {else}
                                        <button class="ps-hidden-by-js" type="submit"
                                                name="select_payment_option" value="{$option.id|escape:'html':'UTF-8'}">
                                            {l s='Choose'  mod='jmango360api'}
                                        </button>
                                    {/if}
                                </form>

                                <label for="{$option.id|escape:'html':'UTF-8'}">
                                    <span>{$option.call_to_action_text|escape:'html':'UTF-8'}</span>
                                    {if $option.logo}
                                        <img src="{$option.logo|escape:'html':'UTF-8'}">
                                    {/if}
                                </label>
                            </div>
                            {if $option.additionalInformation}
                                <div id="{$option.id|escape:'html':'UTF-8'}-additional-information"
                                     class="js-additional-information definition-list additional-information{if $option.id != $selected_payment_option} ps-hidden {/if}">
                                    {$option.additionalInformation}
                                </div>
                            {/if}
                            <div id="pay-with-{$option.id|escape:'html':'UTF-8'}-form"
                                 class="js-payment-option-form {if $option.id != $selected_payment_option} ps-hidden {/if}">
                                {if $option.form}
                                    {$option.form}
                                {else}
                                    <form id="payment-form-submit-{$option.id|escape:'html':'UTF-8'}" method="POST" action="{$option.action|escape:'html':'UTF-8'}">
                                        {foreach from=$option.inputs item=input}
                                            <input type="{$input.type|escape:'html':'UTF-8'}" name="{$input.name|escape:'html':'UTF-8'}" value="{$input.value|escape:'html':'UTF-8'}">
                                        {/foreach}
                                        <button style="display:none" id="pay-with-{$option.id|escape:'html':'UTF-8'}" type="submit"></button>
                                    </form>
                                {/if}
                            </div>
                        </div>
                    {/foreach}
                    {foreachelse}
                    <p class="alert alert-danger">{l s='Unfortunately, there are no payment method available.'  mod='jmango360api'}</p>
                {/foreach}
            </div>
        </div>

        {hook h='displayPaymentByBinaries'}

        <div class="buttons-set" id="payment-buttons-container">
            <button id="payment-button" type="button" title="Continue"
                    class="ladda-button submit-payment"
                    data-color="jmango" data-style="slide-up"
                    data-size="s">
                <span class="ladda-label">{l s='Continue' mod='jmango360api'}</span>
                <span class="ladda-spinner"></span></button>
            <div class="ladda-progress" style="width: 0px;"></div>
        </div>
    </form>
</div>

<script type="text/javascript">
    var paymentMethod = new PaymentMethod('co-payment-form', "{url entity='module' name='jmango360api' controller='jmcheckoutold' relative_protocol=false}");

    $('.submit-payment').click(function (e) {
        var paymentSelected = $('input:radio[name="payment-option"]:checked').length > 0;
        if (!paymentSelected) {
            e.preventDefault();
            var errMessage = "{l s='No payment method is selected.' mod='jmango360api' js=1}";
            alert(errMessage);
        } else {
            paymentMethod.save();
        }
    });

    $('input:radio[name="payment-option"]').change(function () {
        $('.additional-information').hide();
        var open_id = $('input:radio[name="payment-option"]:checked').attr('id');
        var panel_id = open_id + '-additional-information';
        $('#' + panel_id).show();
    });
</script>
