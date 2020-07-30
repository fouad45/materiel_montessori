{**
* @license Created by JMango
*}

<div id="hook-display-before-carrier">
    {$hookDisplayBeforeCarrier}
</div>

<div class="delivery-options-list">
    {if $delivery_options|count}
        <form
                class="clearfix"
                id="js-delivery"
                data-url-update="{url entity='order' params=['ajax' => 1, 'action' => 'selectDeliveryOption']}"
                method="post"
        >
            <div class="form-fields">
                {block name='delivery_options'}
                    <div class="delivery-options">
                        {foreach from=$delivery_options item=carrier key=carrier_id}
                            <div class="row delivery-option">
                                <div class="col-sm-1">
                      <span class="custom-radio float-xs-left">
                        <input type="radio" name="delivery_option[{$id_address|escape:'html':'UTF-8'}]"
                               id="delivery_option_{$carrier.id|escape:'html':'UTF-8'}"
                               value="{$carrier_id|escape:'html':'UTF-8'}"{if $delivery_option == $carrier_id} checked{/if}>
                        <span></span>
                      </span>
                                </div>
                                <label for="delivery_option_{$carrier.id|escape:'html':'UTF-8'}"
                                       class="col-sm-11 delivery-option-2">
                                    <div class="row">
                                        <div class="col-sm-5 col-xs-12">
                                            <div class="row">
                                                {if $carrier.logo}
                                                    <div class="col-xs-3">
                                                        <img src="{$carrier.logo|escape:'html':'UTF-8'}"
                                                             alt="{$carrier.name|escape:'html':'UTF-8'}"/>
                                                    </div>
                                                {/if}
                                                <div class="{if $carrier.logo}col-xs-9{else}col-xs-12{/if}">
                                                    <span class="h6 carrier-name">{$carrier.name|escape:'html':'UTF-8'}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-xs-12">
                                            <span class="carrier-delay">{$carrier.delay|escape:'html':'UTF-8'}</span>
                                        </div>
                                        <div class="col-sm-3 col-xs-12">
                                            <span class="carrier-price">{$carrier.price|escape:'html':'UTF-8'}</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="row carrier-extra-content"{if $delivery_option != $carrier_id} style="display:none;"{/if}>
                                {$carrier.extraContent}
                            </div>
                            <div class="clearfix"></div>
                        {/foreach}
                    </div>
                {/block}
                <div class="order-options">
                    <div id="delivery">
                        <label for="delivery_message">{l s='If you would like to add a comment about your order, please write it in the field below.' mod='jmango360api'}</label>
                        <textarea rows="2" cols="120" id="delivery_message"
                                  name="delivery_message">{$delivery_message|escape:'html':'UTF-8'}</textarea>
                    </div>

                    {if $recyclablePackAllowed}
                        <span class="custom-checkbox">
                <input type="checkbox" id="input_recyclable" name="recyclable"
                       value="1" {if $recyclable} checked {/if}>
                <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
                <label for="input_recyclable">{l s='I would like to receive my order in recycled packaging.' mod='jmango360api'}</label>
              </span>
                    {/if}

                    {if $gift.allowed}
                        <span class="custom-checkbox">
                <input class="js-gift-checkbox" id="input_gift" name="gift" type="checkbox"
                       value="1" {if $gift.isGift}checked="checked"{/if}>
                <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
                <label for="input_gift">{$gift.label|escape:'html':'UTF-8'}</label>
              </span>
                        <div id="gift" class="collapse{if $gift.isGift} in{/if}">
                            <label for="gift_message">{l s='If you\'d like, you can add a note to the gift:' mod='jmango360api'}</label>
                            <textarea rows="2" cols="120" id="gift_message"
                                      name="gift_message">{$gift.message|escape:'html':'UTF-8'}</textarea>
                        </div>
                    {/if}

                </div>
            </div>
            <button type="submit" class="continue btn btn-primary float-xs-right"
                    name="confirmDeliveryOption" value="1">
                {l s='Continue' mod='jmango360api'}
            </button>
        </form>
    {else}
        <p class="alert alert-danger">{l s='Unfortunately, there are no carriers available for your delivery address.' mod='jmango360api'}</p>
    {/if}
</div>

<div id="hook-display-after-carrier">
    {$hookDisplayAfterCarrier}
</div>

<div id="extra_carrier"></div>
