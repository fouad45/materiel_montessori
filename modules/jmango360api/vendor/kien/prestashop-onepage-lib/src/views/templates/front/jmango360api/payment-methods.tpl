{**
* @license Created by JMango
*}

<form action="{$link->getModuleLink("jmango360api", "jmcheckout")|escape:'quotes':'UTF-8'}" id="co-payment-form">
    <fieldset>
        <dl class="sp-methods" id="checkout-payment-method-load">
            {foreach from=$payment_methods item='payment_method'}
                <dt class="payment-module-{$payment_method['name']|escape:'html':'UTF-8'}">
                    <input type="hidden" id="{$payment_method['id_module']|intval}_name"
                           value="{$payment_method['payment_module_url']|escape:'htmlall':'UTF-8'}"/>
                    <label id="payment_lbl_{$payment_method['id_module']|intval}">
                        <input type="radio" class="myopccheckout_payment_options" name="payment_method"
                               value="{$payment_method['id_module']|intval}"
                               data-url="{$payment_method['payment_module_url']|escape:'htmlall':'UTF-8'}"
                               id="{$payment_method['name']|escape:'htmlall':'UTF-8'}"/>
                        <img style="{if !$payment_method['payment_image_url']}display:none{/if}"
                             src='{$payment_method['payment_image_url']|escape:'htmlall':'UTF-8'}'
                             alt='{$payment_method['payment_image_url']|escape:'htmlall':'UTF-8'}'/>
                        <span id='payment_method_name_{$payment_method['id_module']|intval}'>{$payment_method['display_name']|escape:'htmlall':'UTF-8'}</span>
                    </label>
                </dt>
            {/foreach}
        </dl>
    </fieldset>
</form>

<div id="selected_payment_method_html"></div>
<div id="payment_method_html" style="display:none;">
    {foreach from=$payment_methods item='payment_method'}
        <div id="payment_method_{$payment_method['id_module']|intval}">
            {$payment_method['html']}{*Variable contains html content, escape not required*}
        </div>
    {/foreach}
</div>

<div class="buttons-set" id="payment-buttons-container">
    <button id="payment-button" type="button" class="ladda-button" onclick="paymentMethod.save()" data-style="slide-up"
            data-color="jmango" data-size="s">
        <span class="ladda-label">{l s='Continue' mod='jmango360api'}</span>
    </button>
</div>
