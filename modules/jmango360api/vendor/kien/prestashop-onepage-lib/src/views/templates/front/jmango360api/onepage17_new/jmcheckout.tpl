{**
* @license Created by JMango
*}
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, viewport-fit=cover">
    {block name='head'}
        {include file='_partials/head.tpl'}
    {/block}
<!--
    {literal}
        <style type="text/css">
            body {
                padding: 15px;
            }
        </style>
    {/literal}
-->
    {if $custom_css}
        <style type="text/css">{$custom_css}</style>
    {/if}
    {if $custom_js}
        <script type="text/javascript">{$custom_js}</script>
    {/if}
</head>
<body>
<div class="ps-onepage-checkout" id="parent-div">
    <div class="checkout-navbar-wrapper" id="checkout-navbar">
        <div class="checkout-topnav">
            <h1 id="navbar-title" class="page-title float-left">1. {l s='Billing Address' mod='jmango360api'}</h1>
            <div style="display: none;">
                <p id="navbar-title-1">1. {l s='Billing Address' mod='jmango360api'}</p>
                <p id="navbar-title-2">2. {l s='Shipping Address' mod='jmango360api'}</p>
                <p id="navbar-title-3">3. {l s='Shipping Method' mod='jmango360api'}</p>
                <p id="navbar-title-4">4. {l s='Payment Method' mod='jmango360api'}</p>
                <p id="navbar-title-5">5. {l s='Order Review' mod='jmango360api'}</p>
            </div>
            <a class="blue cancel-checkout float-right" onclick="cancelCheckout()">{l s='Cancel Checkout' mod='jmango360api'}</a>
        </div>
        <div class="steps">
            <a id="navbar-step1" class="step active" title="Step 1">
                <span class="step-number">1</span>
                <span class="step-title">{l s='Billing Information' mod='jmango360api'}</span>
            </a>
            <a id="navbar-step2" class="step" title="Step 2">
                <span class="step-number">2</span>
                <span class="step-title">{l s='Shipping Address nav' mod='jmango360api'}</span>
            </a>
            <a id="navbar-step3" class="step"  title="Step 3">
                <span class="step-number">3</span>
                <span class="step-title">{l s='Shipping Method nav' mod='jmango360api'}</span>
            </a>
            <a id="navbar-step4" class="step" title="Step 4">
                <span class="step-number">4</span>
                <span class="step-title">{l s='Payment Method nav' mod='jmango360api'}</span>
            </a>
            <a id="navbar-step5" class="step" title="Step 5">
                <span class="step-number">5</span>
                <span class="step-title">{l s='Order Review nav' mod='jmango360api'}</span>
            </a>
        </div>
    </div>
    <div id="step1">
        {include file="$template_dir/onepage17_new/steps/step1-billing.tpl" }
    </div>
    <div id="step2" style="display: none">
        {include file="$template_dir/onepage17_new/steps/step2-shipping.tpl" }
    </div>
    <div id="step3" style="display: none"></div>
    <div id="step4" style="display: none"></div>
    <div id="step5" style="display: none"></div>
    <div class="confirmDialog" id="checkout-error-dialog" onclick="$('#checkout-error-dialog').hide()" style="display: none;">
        <div class="confirmDialogInner" >
            <div class="confirmDialogContent">
                <div class="confirmDialogMsg" id="checkout-error-content">
                    
                </div>
                <div class="confirmDialogBtn">
                    <button type="button" class="btn btn-primary" onclick="$('#checkout-error-dialog').hide()">OK</button>
                </div>
            </div>
        </div>
    </div>
    <div class="confirmDialog" id="cancelConfirmDialog" onclick="$('#cancelConfirmDialog').hide()">
        <div class="confirmDialogInner" >
            <div class="confirmDialogContent">
                <div class="confirmDialogMsg">
                    {l s='Do you want to cancel the checkout and go back to shopping cart?' mod='jmango360api'}
                </div>
                <div class="confirmDialogBtn">
                    <button type="button" class="btn btn-secondary" onclick="$('#cancelConfirmDialog').hide()">{l s='No' mod='jmango360api'}</button>
                    <button type="button" class="btn btn-primary" onclick="goBack();">{l s='Yes' mod='jmango360api'}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    var isMobile = /JM360/.test(navigator.userAgent);
    if (isIOS && !isMobile) {
        function iOSversion() {
            if (/iP(hone|od|ad)/.test(navigator.platform)) {
                // supports iOS 2.0 and later: <http://bit.ly/TJjs1V>
                var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
                return [parseInt(v[1], 10), parseInt(v[2], 10), parseInt(v[3] || 0, 10)];
            }
        }
        var iosVersion = iOSversion();
        if (iosVersion && (iosVersion[0] <12 || (iosVersion[0] >= 12 && iosVersion[1] <3))) {
            $('#cancelConfirmDialog').html("<div class=\"confirmDialogInner\" >\n" +
                "            <div class=\"confirmDialogContent\">\n" +
                "                <div class=\"confirmDialogMsg\">\n" +
                "                    {l s='Click in the upper left side to return to the shopping cart' mod='jmango360api'}\n" +
                "                </div>\n" +
                "                <div class=\"confirmDialogBtn\">\n" +
                "                    <button type=\"button\" class=\"btn btn-primary\" onclick=\"$('#cancelConfirmDialog').hide()\">OK</button>\n" +
                "                </div>\n" +
                "            </div>\n" +
                "        </div>");
        }
    }
    function goBack() {
        $('#cancelConfirmDialog').hide();
        window.close();
        if (window.location.href.indexOf('?') !== -1){
            window.location.href = window.location.href + '&jmango_continue_shopping=true';
        }else {
            window.location.href = window.location.href + '?jmango_continue_shopping=true';
        }
    };
    var backButton = new HandelBackButton();
    backButton.catchBackButtonEvent();
    var myopc_checkout_url = '{url entity='module' name='jmango360api' controller='jmcheckout' relative_protocol=false}';
    var billing = new Billing(myopc_checkout_url);
    var shipping = new Shipping(myopc_checkout_url);
    var shippingMethod = new ShippingMethod(myopc_checkout_url);
    var paymentMethod = new PaymentMethod(myopc_checkout_url);
    var cancelCheckout = function() {
        $('#cancelConfirmDialog').show();
    };
</script>
</body>
</html>