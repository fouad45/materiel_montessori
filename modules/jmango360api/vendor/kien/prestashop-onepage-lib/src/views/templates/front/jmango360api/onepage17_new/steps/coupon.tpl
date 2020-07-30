{*{json_encode($suggested_cart_rules)}*}
{foreach from=$suggested_cart_rules item=suggested_cart_rule}
    <div class="item-code-wrap wait-apply">
        <div class="item-code">
            <div class="item-inner">
                <div class="field-label">{$suggested_cart_rule['name']}{if $suggested_cart_rule['description'] != ''} - {$suggested_cart_rule['description']}{/if}</div>
                <div class="field-value">
                    <div id="apply"><input name="apply" type="button" value="{l s='Apply' mod='jmango360api'}"
                                           onclick="coupon.applySuggestedCoupon('{$suggested_cart_rule['code']}')">
                    </div>
                </div>
            </div>
        </div>
    </div>
{/foreach}

{foreach from=$added_cart_rules item=added_cart_rule}
    {*{json_encode($added_cart_rule['obj']->description)}*}
    <div class="item-code-wrap">
        <div class="item-code">
            <div class="item-inner">
                <div class="item-label">
                    <div id="icon-trash-{$added_cart_rule['code']}" class="jm-icon-trash">
                        <span class="btnRemove" onclick="coupon.showDeleteButton(event, '{$added_cart_rule["code"]}');">
                            <i aria-hidden="true" class="fa fa-trash"></i>
                        </span>

                    </div>
                    <div class="field-label" style="padding-left: 40px">{$added_cart_rule['name']}{if $added_cart_rule['obj']->description != ''} - {$added_cart_rule['obj']->description}{/if}</div>
                </div>
                <div class="field-value">
                    <div id="price-{$added_cart_rule['code']}" class="price">
                        {if $added_cart_rule['reduction_percent']|intval}-{$added_cart_rule['reduction_percent']}%{/if}
                        {if $added_cart_rule['reduction_amount']}-{$added_cart_rule['reduction_amount']}{/if}
                    </div>
                    <div id="remove-{$added_cart_rule['code']}" class="btn-remove" style="display: none">
                        <input name="remove" type="button" value="{l s='Remove' mod='jmango360api'}" onclick="coupon.remove(event, '{$added_cart_rule['id_cart_rule']}')">
                    </div>
                </div>
            </div>
        </div>
    </div>
{/foreach}
{*{json_encode($added_cart_rules)}*}