{if $carriers_count}
    <form id="shipping-method-form" class="checkout-form method-list">
        <div class="text-center p-3 helper-text grey">
            {l s='Please select a shipping method' mod='jmango360api'}
        </div>
        {foreach $cart->getDeliveryAddressesWithoutCarriers(true) as $address}
            <div class="text-center p-3 helper-text grey">
                {l s='No Delivery Method Available' mod='jmango360api'}
            </div>
        {foreachelse}
            {foreach $delivery_option_list as $id_address => $option_list}
                {foreach $option_list as $key => $option}
                    <div class="radio-option shipping-methods center mb-2 clearfix">
                        {foreach $option.carrier_list as $carrier}
                            <div class="option-info">
                                {*<p>{$key|json_encode}</p><br/>*}
                                {*<p>{$option|json_encode}</p><br/><br/>*}
                                <h4 class="info-title">{$carrier.instance->name|escape:'htmlall':'UTF-8'}</h4>
                                <span class="info-desc">
                                    {if $option.unique_carrier && isset($carrier.instance->delay[$cookie->id_lang])}
                                        <span class="info-delivery-time">{l s='Delivery time:' mod='jmango360api'}</span> {$carrier.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}<br/>
                                    {/if}
                                    {if $option.is_best_grade}
                                        {if $option.is_best_price}
                                            <p class="info-best-price">{l s='The best price and speed' mod='jmango360api'}</p>
                                        {else}
                                            <p class="info-best-price">{l s='The Fastest' mod='jmango360api'}</p>
                                        {/if}
                                    {else}
                                        {if $option.is_best_price}
                                            <p class="info-best-price">{l s='The Best Price' mod='jmango360api'}</p>
                                        {/if}
                                    {/if}
                                    <span class="grey">
                                        {if $option.total_price_with_tax && (isset($option.is_free) && $option.is_free == 0) && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
                                            {if $use_taxes == 1}
                                                {if $priceDisplay == 1}
                                                    {convertPrice price=$option.total_price_without_tax} {l s='(Tax excl.)' mod='jmango360api'}
                                                {else}
                                                    {convertPrice price=$option.total_price_with_tax} {l s='(Tax incl.)' mod='jmango360api'}
                                                {/if}
                                            {else}
                                                {convertPrice price=$option.total_price_without_tax}
                                            {/if}
                                        {else}
                                            {l s='Free' mod='jmango360api'}
                                        {/if}
                                    </span>
                                </span>
                            </div>
                        {/foreach}
                        <div class="option-select">
                            <label class="jm-radio-check mb-0">
                                {*{if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}*}
                                    {*<input class="myopccheckout_shipping_option" type="radio"*}
                                           {*name="delivery_option[{$id_address|intval}]"*}
                                           {*value="{$key|escape:'htmlall':'UTF-8'}"*}
                                           {*id="shipping_method_{$id_address|intval}_{$option@index|intval}"*}
                                           {*checked="checked"/>*}
                                {if isset($default_shipping_method) && $key == $default_shipping_method}
                                    <input class="myopccheckout_shipping_option" type="radio"
                                           name="delivery_option[{$id_address|intval}]"
                                           value="{$key|escape:'htmlall':'UTF-8'}"
                                           id="shipping_method_{$id_address|intval}_{$option@index|intval}"
                                           checked="checked"/>
                                {else}
                                    <input class="myopccheckout_shipping_option" type="radio"
                                           name="delivery_option[{$id_address|intval}]"
                                           value="{$key|escape:'htmlall':'UTF-8'}"
                                           id="shipping_method_{$id_address|intval}_{$option@index|intval}"/>
                                {/if}
                                <span class="radio-check"></span>
                            </label>
                        </div>
                    </div>
                {/foreach}
            {/foreach}
        {/foreach}
        <div class="form-submit fixed" id="shipping-method-buttons">
            <div class="row row-sm">
                <div class="col-6 col-xs-6">
                    <input type="button" onclick="shippingMethod.back()" value="{l s='Previous' mod='jmango360api'}" class="btn btn-default prev-btn"/>
                </div>
                <div class="col-6 col-xs-6">
                    <input type="button" onclick="shippingMethod.next()" value="{l s='Next' mod='jmango360api'}" class="btn btn-primary submit-btn"/>
                </div>
            </div>
        </div>
    </form>
{else}
    <form id="shipping-method-form" class="checkout-form">
        <div class="text-center p-3 helper-text grey">
            {$no_carrier_message}
        </div>
        <div class="form-submit fixed" id="shipping-method-buttons2">
            <div class="row row-sm">
                <div class="col-6 col-xs-6">
                    <input type="button" onclick="shippingMethod.back()" value="{l s='Previous' mod='jmango360api'}" class="btn btn-default prev-btn"/>
                </div>
                <div class="col-6 col-xs-6">
                    <input type="button" onclick="shippingMethod.next()" value="{l s='Next' mod='jmango360api'}" class="btn btn-primary submit-btn disabled no-arrow"/>
                </div>

            </div>
        </div>
    </form>
{/if}

<div class="confirmDialog" id="editShippingMethodConfirmDialog" onclick="$('#editShippingMethodConfirmDialog').hide()">
    <div class="confirmDialogInner" >
        <div class="confirmDialogContent">
            <div class="confirmDialogMsg">
                {l s='Any unsaved changes will be lost' mod='jmango360api'}
                <br/>
                {l s='Do you want to proceed?' mod='jmango360api'}
            </div>
            <div class="confirmDialogBtn">
                <button type="button" class="btn btn-secondary" onclick="$('#editShippingMethodConfirmDialog').hide()">{l s='No' mod='jmango360api'}</button>
                <button type="button" class="btn btn-primary" onclick="$('#editShippingMethodConfirmDialog').hide(); shippingMethod.closeEditShippingMethodForm()">{l s='Yes' mod='jmango360api'}</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var myopc_checkout_url = "{$linkJm->getModuleLink("jmango360api", "jmcheckout")|escape:'quotes':'UTF-8'}";
    var shippingMethod = new ShippingMethod(myopc_checkout_url);
</script>