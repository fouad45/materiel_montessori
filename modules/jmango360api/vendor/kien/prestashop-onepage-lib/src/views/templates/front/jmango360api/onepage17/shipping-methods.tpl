{**
* @license Created by JMango
*}

<div id="hook-display-before-carrier">
    {$hookDisplayBeforeCarrier}
</div>

<form id="js-delivery" action="">
    <div id="checkout-shipping-method-load" class="shipping-methods">
        {if $delivery_options|count}
            <dl class="sp-methods delivery-options">
                {foreach from=$delivery_options item=carrier key=carrier_id}
                    <dt class="delivery-option carrier-id-{$carrier_id|intval}">
                        <span class="custom-radio">
                            <input class="myopccheckout_shipping_option" type="radio"
                                   name="delivery_option[{$id_address|escape:'html':'UTF-8'}]"
                                   id="delivery_option_{$carrier.id|escape:'html':'UTF-8'}"
                                   value="{$carrier_id|escape:'html':'UTF-8'}"
                                   {if $delivery_option == $carrier_id}checked{/if}>
                            <span></span>
                        </span>
                        <label for="delivery_option_{$carrier.id|escape:'html':'UTF-8'}">
                            {if $carrier.logo}
                                <img src="{$carrier.logo|escape:'htmlall':'UTF-8'}"
                                     alt="{$carrier.name|escape:'htmlall':'UTF-8'}"
                                     {if isset($carrier.width) && $carrier.width != ""}width="{$carrier.width|escape:'htmlall':'UTF-8'}"{/if} {if isset($carrier.height) && $carrier.height != ""}height="{$carrier.height|escape:'htmlall':'UTF-8'}"{/if}/>
                            {/if}
                            <strong class="name-shipping">{$carrier.name|escape:'htmlall':'UTF-8'}</strong>
                            {if isset($carrier.delay)}
                                <span class="desc-shipping">{l s='Delivery time:' mod='jmango360api'}
                                    &nbsp;{$carrier.delay|escape:'htmlall':'UTF-8'}</span>
                            {/if}
                            <span class="price-shipping">{$carrier.price|escape:'html':'UTF-8'}</span>
                        </label>
                    </dt>
                {/foreach}
            </dl>
            <div class="order-options">
                {if isset($isDisplayCommentBox) && $isDisplayCommentBox}
                    <div class="delivery_message">
                        <label for="delivery_message">{l s='If you would like to add a comment about your order, please write it in the field below.' d='Shop.Theme.Checkout'}</label>
                        <div class="form-list">
                            <textarea rows="2" cols="40" id="delivery_message" name="delivery_message"></textarea>
                        </div>
                    </div>
                {/if}
            </div>
        {else}
            <label for="delivery_message">{l s='No Delivery Method Available' mod='jmango360api'}</label>
        {/if}
    </div>
    <div class="buttons-set" id="shipping-method-buttons-container">
        <button id="shipping-method-button" type="submit" class="ladda-button" onclick="shippingMethod.save()"
                data-style="slide-up" data-color="jmango" data-size="s" name="confirmDeliveryOption">
            <span class="ladda-label">{l s='Continue' mod='jmango360api'}</span>
            <span class="ladda-spinner"></span>
            <div class="ladda-progress" style="width: 0px;"></div>
        </button>
    </div>
</form>

<div id="hook-display-after-carrier">
    {$hookDisplayAfterCarrier}
</div>

{*<script type="text/javascript">*}
{*var opc_accordion =  new Accordion('checkoutSteps', '.step-title', true);*}
{*var shippingMethod = new ShippingMethod('co-shipping-method-form', "{$myopc_checkout_url}");*}
{*var isLogged = {$is_logged|intval};*}
{*var checkout = new Checkout(opc_accordion);*}
{*</script>*}

{if $initSoFlexibiliteEngine}
    <script type="text/javascript">
        if (typeof initSoFlexibiliteEngine === 'function'){
            $('#soflex-search').unbind();
            initSoFlexibiliteEngine();
        }
    </script>
{/if}

<script type="text/javascript">
    var myopc_checkout_url = '{url entity='module' name='jmango360api' controller='jmcheckoutold' relative_protocol=false}';
    var shippingMethod = new ShippingMethod('js-delivery',myopc_checkout_url);
    {if $initChronopost}
        shippingMethod.triggerChronopost();
    {/if}
</script>