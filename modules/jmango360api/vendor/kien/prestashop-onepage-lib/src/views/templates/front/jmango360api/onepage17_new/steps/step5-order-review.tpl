<div class="checkout-form step5">
        <div class="radio-option payment mt-2 mb-2 clearfix">
            <div class="option-info">
                <h4 class="info-title bigger">{l s='Billing Information' mod='jmango360api'}</h4>
                <span class="info-desc">{$billing_address['firstname']|escape:'html':'UTF-8'} {$billing_address['lastname']|escape:'html':'UTF-8'}</span>
                <span class="info-desc">{$billing_address['address1']|escape:'html':'UTF-8'} {if 'address2'|array_key_exists:$billing_address}{$billing_address['address2']|escape:'html':'UTF-8'}{/if}</span>

                {*<span class="info-desc">{if 'postcode'|array_key_exists:$billing_address}{$billing_address['postcode']|escape:'html':'UTF-8'}{/if} {if 'state'|array_key_exists:$billing_address}{$billing_address['state']|escape:'html':'UTF-8'}{/if} {$billing_address['city']|escape:'html':'UTF-8'} {$billing_address['country']|escape:'html':'UTF-8'}</span>*}
                {if $billing_address['country']|in_array:["Netherlands", "Spain", "Italy", "Germany", "Portugal", "Sweden", "Denmark"]}
                    <span class="info-desc">{if 'postcode'|array_key_exists:$billing_address}{$billing_address['postcode']|escape:'html':'UTF-8'}{/if} {if 'state'|array_key_exists:$billing_address}{$billing_address['state']|escape:'html':'UTF-8'}{/if} {$billing_address['city']|escape:'html':'UTF-8'}</span>
                {elseif $billing_address['country']|in_array:["United Kingdom", "Vietnam"]}
                    <span class="info-desc">{$billing_address['city']|escape:'html':'UTF-8'}</span>
                    <span class="info-desc">{if 'postcode'|array_key_exists:$billing_address}{$billing_address['postcode']|escape:'html':'UTF-8'}{/if} {if 'state'|array_key_exists:$billing_address}{$billing_address['state']|escape:'html':'UTF-8'}{/if}</span>
                {elseif $billing_address['country']|in_array:["United States", "Australia"]}
                    <span class="info-desc">{$billing_address['city']|escape:'html':'UTF-8'} {if 'state'|array_key_exists:$billing_address}{$billing_address['state']|escape:'html':'UTF-8'}{/if} {if 'postcode'|array_key_exists:$billing_address}{$billing_address['postcode']|escape:'html':'UTF-8'}{/if}</span>
                {elseif $billing_address['country']|in_array:["China", "France", "Israel"]}
                    <span class="info-desc">{if 'postcode'|array_key_exists:$billing_address}{$billing_address['postcode']|escape:'html':'UTF-8'}{/if} {if 'state'|array_key_exists:$billing_address}{$billing_address['state']|escape:'html':'UTF-8'}{/if} {$billing_address['city']|escape:'html':'UTF-8'}</span>
                {else}
                    <span class="info-desc">{$billing_address['city']|escape:'html':'UTF-8'}</span>
                    <span class="info-desc">{if 'state'|array_key_exists:$billing_address}{$billing_address['state']|escape:'html':'UTF-8'}{/if} {if 'postcode'|array_key_exists:$billing_address}{$billing_address['postcode']|escape:'html':'UTF-8'}{/if}</span>
                {/if}

                <span class="info-desc">{$billing_address['country']|escape:'html':'UTF-8'}</span>
                <a href="tel:{$billing_address['phone']|escape:'html':'UTF-8'}" class="info-desc">{$billing_address['phone']|escape:'html':'UTF-8'}</a>
                {if !$billing_address['phone'] && 'phone_mobile'|array_key_exists:$billing_address}
                    <a href="tel:{$billing_address['phone_mobile']|escape:'html':'UTF-8'}" class="info-desc">{$billing_address['phone_mobile']|escape:'html':'UTF-8'}</a>
                {/if}
            </div>
            <div class="option-select align-top">
                <a class="blue" onclick="review.editBillingAddress({$billing_address|@json_encode|escape:'html':'UTF-8'}, {$is_logged})">{l s='Edit' mod='jmango360api'}</a>
            </div>
        </div>
        <div class="radio-option payment mb-2 clearfix">
            <div class="option-info">
                <h4 class="info-title bigger">{l s='Shipping Information' mod='jmango360api'}</h4>
                <span class="info-desc">{$shipping_address['firstname']|escape:'html':'UTF-8'} {$shipping_address['lastname']|escape:'html':'UTF-8'}</span>
                <span class="info-desc">{$shipping_address['address1']|escape:'html':'UTF-8'} {if 'address2'|array_key_exists:$shipping_address}{$shipping_address['address2']|escape:'html':'UTF-8'}{/if}</span>

                {if $shipping_address['country']|in_array:["Netherlands", "Spain", "Italy", "Germany", "Portugal", "Sweden", "Denmark"]}
                    <span class="info-desc">{if 'postcode'|array_key_exists:$shipping_address}{$shipping_address['postcode']|escape:'html':'UTF-8'}{/if} {if 'state'|array_key_exists:$shipping_address}{$shipping_address['state']|escape:'html':'UTF-8'}{/if} {$shipping_address['city']|escape:'html':'UTF-8'}</span>
                {elseif $shipping_address['country']|in_array:["United Kingdom", "Vietnam"]}
                    <span class="info-desc">{$shipping_address['city']|escape:'html':'UTF-8'}</span>
                    <span class="info-desc">{if 'postcode'|array_key_exists:$shipping_address}{$shipping_address['postcode']|escape:'html':'UTF-8'}{/if} {if 'state'|array_key_exists:$shipping_address}{$shipping_address['state']|escape:'html':'UTF-8'}{/if}</span>
                {elseif $shipping_address['country']|in_array:["United States", "Australia"]}
                    <span class="info-desc">{$shipping_address['city']|escape:'html':'UTF-8'} {if 'state'|array_key_exists:$shipping_address}{$shipping_address['state']|escape:'html':'UTF-8'}{/if} {if 'postcode'|array_key_exists:$shipping_address}{$shipping_address['postcode']|escape:'html':'UTF-8'}{/if}</span>
                {elseif $shipping_address['country']|in_array:["China", "France", "Israel"]}
                    <span class="info-desc">{if 'postcode'|array_key_exists:$shipping_address}{$shipping_address['postcode']|escape:'html':'UTF-8'}{/if} {if 'state'|array_key_exists:$shipping_address}{$shipping_address['state']|escape:'html':'UTF-8'}{/if} {$shipping_address['city']|escape:'html':'UTF-8'}</span>
                {else}
                    <span class="info-desc">{$shipping_address['city']|escape:'html':'UTF-8'}</span>
                    <span class="info-desc">{if 'state'|array_key_exists:$shipping_address}{$shipping_address['state']|escape:'html':'UTF-8'}{/if} {if 'postcode'|array_key_exists:$shipping_address}{$shipping_address['postcode']|escape:'html':'UTF-8'}{/if}</span>
                {/if}

                <span class="info-desc">{$shipping_address['country']|escape:'html':'UTF-8'}</span>
                <a href="tel:{$shipping_address['phone']|escape:'html':'UTF-8'}" class="info-desc">{$shipping_address['phone']|escape:'html':'UTF-8'}</a>
                {if !$shipping_address['phone']  && 'phone_mobile'|array_key_exists:$shipping_address}
                    <a href="tel:{$shipping_address['phone_mobile']|escape:'html':'UTF-8'}" class="info-desc">{$shipping_address['phone_mobile']|escape:'html':'UTF-8'}</a>
                {/if}
            </div>
            <div class="option-select align-top">
                <a class="blue" onclick="review.editShippingAddress({$shipping_address|@json_encode|escape:'html':'UTF-8'}, {$is_logged})">{l s='Edit' mod='jmango360api'}</a>
            </div>
        </div>
        <div class="radio-option payment mb-2 clearfix">
            <div class="option-info">
                <h4 class="info-title bigger">{l s='Shipping Method' mod='jmango360api'}</h4>
                <span id="review-delivery-method-title" class="info-desc">{$delivery_option.name|escape:'html':'UTF-8'}</span>
                <span class="info-desc">{$delivery_option.delay|escape:'html':'UTF-8'}</span>
                <span class="info-desc">{$delivery_option.price|escape:'html':'UTF-8'}</span>
            </div>
            <div class="option-select align-top">
                <a class="blue" onclick="review.editShippingMethod()">{l s='Edit' mod='jmango360api'}</a>
            </div>
        </div>
        <div class="radio-option payment mb-2 clearfix">
            <div class="option-info" id="review-payment-method">
                <h4 class="info-title bigger">{l s='Payment Method' mod='jmango360api'}</h4>
                <span class="info-desc" id="review-payment-method-title"></span>
            </div>
            <div class="option-select align-top">
                <a class="blue" onclick="review.editPaymentMethod()">{l s='Edit' mod='jmango360api'}</a>
            </div>
        </div>
    <div class="order-summary mt-2">
        <h4>{l s='Order Summary' mod='jmango360api'}</h4>
        <div class="product-list">
            {foreach from=$cart.products item=product}
                <div class="product-item clearfix">
                    <div class="product-image">
                        <img src="{$product.cover.large.url}" alt="title"/>
                    </div>
                    <div class="product-details">
                        <h5 class="name">{$product.name}</h5>
                        <div class="product-summary">
                            <div class="product-attr"><span>{l s='Qty' mod='jmango360api'}: </span>{$product.cart_quantity}</div>
                            <div class="product-attr"><span>{l s='Item Price' mod='jmango360api'}: </span>{$product.price}</div>
                            <div class="product-attr"><span>{l s='Subtotal' mod='jmango360api'}: </span>{$product.total}</div>
                        </div>
                        {if $product.attributes}
                            <div class="product-summary">
                                {foreach from=$product.attributes key=name item=value}
                                    <div class="product-attr"><span>{$name}: </span>{$value}</div>
                                {/foreach}
                            </div>
                        {/if}
                    </div>
                </div>
            {/foreach}
        </div>
		<div class="price-summary">
			<div class="summary-fields">
				<div class="summary-field">
					<div class="field-label">{$cart.subtotals.products.label|escape:'html':'UTF-8'} ({$cart.products_count} {if products_count == 1}{l s='item' mod='jmango360api'}{else}{l s='items' mod='jmango360api'}{/if} ):</div>
					<div class="field-value">{$cart.subtotals.products.value|escape:'html':'UTF-8'}</div>
				</div>
				<div class="summary-field">
					<div class="field-label">{$cart.subtotals.shipping.label|escape:'html':'UTF-8'}:<br>
						<div class="field-label-extra">
						<span class="">{$delivery_option.name|escape:'html':'UTF-8'}</span><br>
						{$delivery_option.delay|escape:'html':'UTF-8'}
						</div>
					</div>
					<div class="field-value">{$cart.subtotals.shipping.value|escape:'html':'UTF-8'}</div>
				</div>
				<div class="summary-field">
					<div class="field-label">{$cart.subtotals.tax.label|escape:'html':'UTF-8'}</div>
					<div class="field-value">{$cart.subtotals.tax.value|escape:'html':'UTF-8'}</div>
				</div>

                {if $cart.subtotals.discounts}
                    <div class="summary-field" onclick="coupon.toggleShowDiscountCode(event)">
                        <div class="field-label">{$cart.subtotals.discounts.label|escape:'html':'UTF-8'}:</div>
                        <div class="field-value">{$cart.subtotals.discounts.value|escape:'html':'UTF-8'}</div>
                        {if $enable_coupon_onepage !== '0'}
                            <div class="arrow"></div>
                        {/if}
                    </div>
                {/if}

                {if $enable_coupon_onepage !== '0'}
                    <div id="order-review-coupon">
                        {include file = "$module_template_dir/onepage17_new/steps/coupon.tpl"}
                    </div>
                    <div class="summary-field add-coupon-box">
                        <a id="btn-discount" class="blue" onclick="coupon.showAddCodeDiscount()">{l s='Add Coupon Code' mod='jmango360api'}</a>
                        <div id="discount-field" style="display: none;">
                            <input id="addcode-discount" class="addcode-discount" placeholder="{l s='Enter coupon code here' mod='jmango360api'}">
                            <div id="addcode-buttons">
                                <button class="btnReset" type="button" onclick="$('#addcode-discount').val('')"></button>
                                <input class="btnCancel" type="button" onclick="coupon.showBtnDiscount()" id="cancel-coupon" value="{l s='Cancel' mod='jmango360api'}">
                            </div>
                            <div id="addcode-loading" style="display: none;">
                                <span class="validText">{l s='Validating Coupon' mod='jmango360api'}</span>
                            </div>
                        </div>
                    </div>
                {/if}

				<div class="summary-field">
				<div class="field-label">{l s='Total Amount Payable' mod='jmango360api'}</div>
				<div class="field-value"><span class="totalPrice">{$cart.totals.total.value|escape:'html':'UTF-8'}</span></div>
				</div>
			</div>
			<textarea id="order-review-note" class="note" placeholder="{l s='Please enter your comment or special instruction here' mod='jmango360api'}"></textarea>
		</div>
    </div>
        <div class="clearfix"></div>
        <div class="form-submit">
            <div class="checkout-confirmation pt-2">
                <label class="jm-checkbox mb-2 clearfix">
                    <input type="checkbox" name="subscribe">
                    <span class="checkbox"></span>
                    <span class="label">{l s='Subscribe to our newsletter' mod='jmango360api'}</span>
                </label>
                {if $conditions_to_approve|count}
                    <div class="error-tooltip">
                        {l s='Please spend time to read and acknowledge the general terms & conditions' mod='jmango360api'}
                    </div>
                    {foreach from=$conditions_to_approve item="condition" key="condition_name"}
                        <label class="jm-checkbox mb-2 clearfix">
                            <input id="term-and-condition-checkbox" class="term-and-condition" type="checkbox" name="agree">
                            <span class="checkbox"></span>
                            <span class="label">{$condition}</span>
                        </label>
                    {/foreach}
                {/if}
            </div>
            <input id="submit-order" type="button" onclick="review.submitOrder()" value="{l s='Place Order' mod='jmango360api'}" class="btn btn-primary submit-btn {if $conditions_to_approve|count}disabled{/if} no-arrow"/>
        </div>
</div>

<div id="order-review-error" class="invalidCoupon">
    <div id="order-review-error-msg" class="notif"> ASD </div>
</div>

<div class="overlay-window" id="term-and-condition-page" style="display: none">
    <header class="overlay-header">
        <a class="back-btn" onclick="$('#term-and-condition-page').hide()"></a>
        <h1>{l s='General Terms & Conditions' mod='jmango360api'}</h1>
    </header>
    <div id="term-and-condition-content" class="content">
    </div>
</div>

<script type="text/javascript">
    var myopc_checkout_url = '{url entity='module' name='jmango360api' controller='jmcheckout' relative_protocol=false}';
    var review = new Review(myopc_checkout_url, {$is_logged});
    review.setPaymentOption({$payment_options|json_encode});
    var coupon = new Coupon(myopc_checkout_url);

    var positionTop = $('#order-review-note').offset().top - 50;
    $('#order-review-note').on('click', function() {
        $('html, body').animate({
            scrollTop: positionTop
        }, 500);
    });


    $('.jm-checkbox a').on('click', function(event) {
        event.preventDefault();
        var url = $(event.target).attr('href');
        if (url) {
            // TODO: Handle request if no pretty URL
            url += '?content_only=1';
            $.get(url, function (content) {
                // console.log($(content).find('.page-cms').contents());
                $('#term-and-condition-content').html($(content).find('.page-cms').contents());
                $('#term-and-condition-page').show();
            }).fail(function(resp){
                console.log('error loading term and condition');
        })
        }
    });
</script>