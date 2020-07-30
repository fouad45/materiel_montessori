<div class="checkout-form step5">

        <div class="radio-option payment mt-2 mb-2 clearfix">
            <div class="option-info">
                <h4 class="info-title bigger">{l s='Billing Information' mod='jmango360api'}</h4>
                <span class="info-desc">{$billing_address->firstname|escape:'html':'UTF-8'} {$billing_address->lastname|escape:'html':'UTF-8'}</span>
                {*{if $billing_address.email}*}
                    {*<span class="info-desc">customerData.billingAddress.email</span>*}
                {*{/if}*}
                <a href="tel:{$billing_address->phone|escape:'html':'UTF-8'}" class="info-desc">{$billing_address->phone|escape:'html':'UTF-8'}</a>
                {if !$billing_address->phone}
                    <a href="tel:{$billing_address->phone_mobile|escape:'html':'UTF-8'}" class="info-desc">{$billing_address->phone_mobile|escape:'html':'UTF-8'}</a>
                {/if}
                <span class="info-desc">{$billing_address->address1|escape:'html':'UTF-8'}</span>
                <span class="info-desc">{$billing_address->postcode|escape:'html':'UTF-8'} {$billing_address->state|escape:'html':'UTF-8'} {$billing_address->city|escape:'html':'UTF-8'} {$billing_address->country|escape:'html':'UTF-8'}</span>
            </div>
            <div class="option-select align-top">
                <a class="blue" onclick="review.editBillingAddress()">{l s='Edit' mod='jmango360api'}</a>
            </div>
        </div>
        <div class="radio-option payment mb-2 clearfix">
            <div class="option-info">
                <h4 class="info-title bigger">{l s='Shipping Information' mod='jmango360api'}</h4>
                <span class="info-desc">{$shipping_address->firstname|escape:'html':'UTF-8'} {$shipping_address->lastname|escape:'html':'UTF-8'}</span>
                <a href="tel:{$shipping_address->phone|escape:'html':'UTF-8'}" class="info-desc">{$shipping_address->phone|escape:'html':'UTF-8'}</a>
                {if !$billing_address->phone}
                    <a href="tel:{$shipping_address->phone_mobile|escape:'html':'UTF-8'}" class="info-desc">{$shipping_address->phone_mobile|escape:'html':'UTF-8'}</a>
                {/if}
                <span class="info-desc">{$shipping_address->address1|escape:'html':'UTF-8'}</span>
                <span class="info-desc">{$shipping_address->postcode|escape:'html':'UTF-8'} {$shipping_address->state|escape:'html':'UTF-8'} {$shipping_address->city|escape:'html':'UTF-8'} {$shipping_address->country|escape:'html':'UTF-8'}</span>
            </div>
            <div class="option-select align-top">
                <a class="blue" onclick="review.editShippingAddress()">{l s='Edit' mod='jmango360api'}</a>
            </div>
        </div>
        <div class="radio-option payment mb-2 clearfix">
            <div class="option-info">
                <h4 class="info-title bigger">{l s='Shipping Method' mod='jmango360api'}</h4>
                {foreach $delivery_option.carrier_list as $carrier}
                    <span class="info-desc">{$carrier.instance->name|escape:'htmlall':'UTF-8'}</span>
                    {if isset($carrier.instance->delay[$cookie->id_lang])}
                        <span class="info-desc">{$carrier.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}</span>
                    {/if}
                {/foreach}
                <span class="info-desc">
                    {if $delivery_option.total_price_with_tax && (isset($delivery_option.is_free) && $delivery_option.is_free == 0) && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
                        {if $use_taxes == 1}
                            {if $priceDisplay == 1}
                                {convertPrice price=$delivery_option.total_price_without_tax} {l s='(Tax excl.)' mod='jmango360api'}
                            {else}
                                {convertPrice price=$delivery_option.total_price_with_tax} {l s='(Tax incl.)' mod='jmango360api'}
                            {/if}
                        {else}
                            {convertPrice price=$delivery_option.total_price_without_tax}
                        {/if}
                    {else}
                        {l s='Free' mod='jmango360api'}
                    {/if}
                </span>
            </div>
            <div class="option-select align-top">
                <a class="blue" onclick="review.editShippingMethod()">{l s='Edit' mod='jmango360api'}</a>
            </div>
        </div>
        <div class="radio-option payment mb-2 clearfix">
            <div class="option-info">
                <h4 class="info-title bigger">{l s='Payment Method' mod='jmango360api'}</h4>
                <span class="info-desc" id="review-payment-method"></span>
            </div>
            <div class="option-select align-top">
                <a class="blue" onclick="review.editPaymentMethod()">{l s='Edit' mod='jmango360api'}</a>
            </div>
        </div>

    <div class="order-summary mt-2">
        <h4>{l s='Order Summary' mod='jmango360api'}</h4>
        <div class="product-list">
            {foreach $products as $product}
                <div class="product-item clearfix">
                    <div class="product-image">
                        <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'large_default')|escape:"html":"UTF-8"}" alt="title"/>
                    </div>
                    <div class="product-details">
                        <h5 class="name">{$product.name}</h5>
                        <div class="product-summary">
                            <div class="product-attr"><span>{l s='Qty' mod='jmango360api'}: </span>{$product.cart_quantity}</div>
                            <div class="product-attr"><span>{l s='Item Price' mod='jmango360api'}: </span>{displayPrice price=$product.price|round:2}</div>
                            <div class="product-attr"><span>{l s='Subtotal' mod='jmango360api'}: </span>{displayPrice price=$product.total|round:2}</div>
                        </div>

                        {if $product.attributes}
                            <div class="product-summary">
                                <div class="product-attr">{$product.attributes}<br/></div>
                            </div>
                        {/if}
                    </div>
                </div>
            {/foreach}
        </div>
		<div class="price-summary">
                <div class="summary-fields">
					<div class="summary-field">
						<div class="field-label">{l s='Subtotal' mod='jmango360api'} ({$cart_count_label}):</div>
						{if $use_taxes}
							{if $priceDisplay}
								<div class="field-value">{displayPrice price=$total_products|round:2}</div>
							{else}
								<div class="field-value">{displayPrice price=$total_products_wt|round:2}</div>
							{/if}
						{else}
							<div class="field-value">{displayPrice price=$total_products|round:2}</div>
						{/if}
					</div>

					<div class="summary-field">
						<div class="field-label">{l s='Shipping' mod='jmango360api'}:<br>

							{foreach $delivery_option.carrier_list as $carrier}
								<div class="field-label-extra">
									<span class="">{$carrier.instance->name|escape:'htmlall':'UTF-8'}</span><br>
									{if isset($carrier.instance->delay[$cookie->id_lang])}
										{$carrier.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}
									{/if}
								</div>
							{/foreach}
						</div>

						{if $total_shipping_tax_exc <= 0 && (!isset($isVirtualCart) || !$isVirtualCart) && $free_ship}
							<div class="field-value">{l s='Free Shipping' mod='jmango360api'}</div>
						{else}
							{if $use_taxes && $total_shipping_tax_exc != $total_shipping}
								{if $priceDisplay}
									<div class="field-value">{displayPrice price=$total_shipping_tax_exc|round:2}</div>
								{else}
									<div class="field-value">{displayPrice price=$total_shipping|round:2}</div>
								{/if}
							{else}
								<div class="field-value">{displayPrice price=$total_shipping_tax_exc|round:2}</div>
							{/if}
						{/if}
					</div>

                    {if $use_taxes && $show_taxes && $total_tax != 0 }
						<div class="summary-field">
							<div class="field-label">{l s='Tax' mod='jmango360api'}: </div>
							<div class="field-value">{displayPrice price=$total_tax|round:2}</div>
						</div>
                    {/if}

                    {if $total_discounts_tax_exc > 0}
                        <div class="summary-field total-discount" onclick="coupon.toggleShowDiscountCode(event)">
                            <div class="field-label">{l s='Discount' mod='jmango360api'}:</div>
                            {if $use_taxes}
                                {if $priceDisplay}
                                    <div class="field-value">{displayPrice price=$total_discounts_tax_exc|round:2}</div>
                                {else}
                                    <div class="field-value">{displayPrice price=$total_discounts|round:2}</div>
                                {/if}
                            {else}
                                <div class="field-value">{displayPrice price=$total_discounts_tax_exc|round:2}</div>
                            {/if}
                            {if $enable_coupon_onepage !== '0'}
                                <div class="arrow"></div>
                            {/if}
                        </div>
                    {/if}
                    {if $enable_coupon_onepage !== '0'}
                        <div id="order-review-coupon">
                            {include file = "$module_template_dir/onepage16_new/steps/coupon.tpl"}
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

					<div class="summary-field total-payable">
						<div class="field-label">{l s='Total Amount Payable' mod='jmango360api'}</div>
						<div class="field-value">
							{if $use_taxes}
								<div class="totalPrice">{displayPrice price=$total_price|round:2}</div>
							{else}
								<div class="totalPrice">{displayPrice price=$total_price_without_tax|round:2}</div>
							{/if}
						</div>
					</div>
                </div>
                <textarea id="order-review-note" class="note" placeholder="{l s='Please enter your comment or special instruction here' mod='jmango360api'}"></textarea>
            </div>
        </div>
        <div class="clearfix"></div>

	<div class="form-submit">
            <div class="checkout-confirmation pt-2">
                {if $newsletter}
                    <label class="jm-checkbox mb-2 clearfix">
                        <input type="checkbox" name="subscribe">
                        <span class="lbl_label">{l s='Subscribe to our newsletter' mod='jmango360api'}</span>
                    </label>
                {/if}
                {if $conditions}
                    <div class="error-tooltip">
                        {l s='Please spend time to read and acknowledge the general terms & conditions' mod='jmango360api'}
                    </div>
                    <label class="jm-checkbox mb-2 clearfix">
                        <input id="term-and-condition-checkbox" class="term-and-condition" type="checkbox" name="agree">
                        {*<span class="checkbox"></span>*}
                        <span class="lbl_label">
                            {l s='I acknowledge that I have an obligation to pay for this item and agree with' mod='jmango360api'}
                            <a href="{$link_conditions}" id="term-and-condition-link" class="blue">{l s='the general terms & conditions' mod='jmango360api'}</a>
                        </span>
                    </label>
                {/if}
            </div>

            <div id="paymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" aria-label="Close" onclick='$("#paymentModal").modal("hide");'>
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h1 class="modal-title" id="paymentModalLabel">
                                {l s='Payment Method' mod='jmango360api'}
                            </h1>
                        </div>
                        <div class="modal-body">
                        </div>
                        <div class="modal-footer">
                            <button id="payment_dialog_close" type="button" class="btn btn-default" onclick='$("#paymentModal").modal("hide");'>{l s='Close' mod='jmango360api'}</button>
                            <button id="payment_dialog_proceed" type="button" class="btn btn-primary">{l s='Proceed' mod='jmango360api'}</button>
                        </div>
                    </div>
                </div>
            </div>

            <input id="submit-order" type="button" onclick="review.submitOrder()" value="{l s='Place Order' mod='jmango360api'}" class="btn btn-primary submit-btn {if $conditions}disabled{/if} no-arrow"/>
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
    var loadingMsg = "{l s='Loading' mod='jmango360api' js=1}";
    var myopc_checkout_url = "{$linkJm->getModuleLink("jmango360api", "jmcheckout")|escape:'quotes':'UTF-8'}";
    var review = new Review(myopc_checkout_url, {$is_logged});
    {*console.log({$payment_methods|json_encode});*}
    var coupon = new Coupon(myopc_checkout_url);
    review.setPaymentOption();

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
            $.get(url + ' body', function (content) {
                $('#term-and-condition-page').show();
                $('#term-and-condition-content').html(content);
            }).fail(function(resp){
                console.log('error loading term and condition');
            });
        }
    });

    $(document).click(function() {
        $("[id^=remove]").hide();
        $("[id^=icon-trash-]").show();
        $("[id^=price-]").show();
    });
</script>