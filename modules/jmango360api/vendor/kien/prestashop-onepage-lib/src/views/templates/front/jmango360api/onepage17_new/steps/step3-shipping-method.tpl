<div id="hook-display-before-carrier">
    {$hookDisplayBeforeCarrier}
</div>
{if $delivery_options|count}
    <form id="js-delivery" class="checkout-form">
        <div class="text-center p-3 helper-text grey">
            {l s='Please select a shipping method' mod='jmango360api'}
        </div>
        <div class="delivery-options">
            {foreach from=$delivery_options item=carrier key=carrier_id}
                <div class="delivery-option">
                    <div class="radio-option center mb-2 clearfix">
                        <div class="option-info">
                            <h4 class="info-title">{$carrier.name|escape:'htmlall':'UTF-8'}</h4>
                            <span class="info-desc">
                            {$carrier.delay|escape:'htmlall':'UTF-8'}
                                <span class="grey">{$carrier.price}</span>
                        </span>
                        </div>
                        <div class="option-select">
                            <label class="jm-radio-check mb-0">
                                <span class="custom-radio float-xs-left">
                                    <input type="radio"
                                           name="delivery_option[{$id_address}]"
                                           id="delivery_option_{$carrier.id}"
                                           value="{$carrier_id}"
                                           {if $delivery_option == $carrier_id}checked{/if}>
                                    <span class="radio-check"></span>
                                </span>
                            </label>
                        </div>
                    </div>
            </div>
            {/foreach}
        </div>
        <div class="form-submit fixed" id="shipping-method-buttons">
            <div class="row row-sm">
                <div class="col-6">
                    <input type="button" onclick="shippingMethod.back()" value="{l s='Previous' mod='jmango360api'}" class="btn btn-default prev-btn"/>
                </div>
                <div class="col-6">
                    <input type="button" onclick="shippingMethod.next()" value="{l s='Next' mod='jmango360api'}" class="btn btn-primary submit-btn"/>
                </div>
            </div>
        </div>
    </form>
{else}
    <form id="shipping-method-form" class="checkout-form">
        <div class="text-center p-3 helper-text grey">
            {l s='No Delivery Method Available' mod='jmango360api'}
        </div>
        <div class="form-submit fixed" id="shipping-method-buttons2">
            <div class="row row-sm">
                <div class="col-6">
                    <input type="button" onclick="shippingMethod.back()" value="{l s='Previous' mod='jmango360api'}" class="btn btn-default prev-btn"/>
                </div>
                <div class="col-6">
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

<div id="hook-display-after-carrier">
    {$hookDisplayAfterCarrier}
</div>

<div id="hook-display-carrier-footer">
    {$hookDisplayFooter}
</div>

{if $initSoFlexibiliteEngine}
    <script type="text/javascript">
        if (typeof initSoFlexibiliteEngine === 'function'){
            $('#soflex-search').unbind();
            initSoFlexibiliteEngine();
        }
    </script>
{/if}

<script type="text/javascript">
    var myopc_checkout_url = '{url entity='module' name='jmango360api' controller='jmcheckout' relative_protocol=false}';
    var shippingMethod = new ShippingMethod(myopc_checkout_url);
    {if $initChronopost}
        shippingMethod.triggerChronopost();
    {/if}
</script>