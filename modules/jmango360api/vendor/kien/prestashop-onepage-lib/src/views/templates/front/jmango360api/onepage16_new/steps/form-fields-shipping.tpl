<fieldset class="">
    <input type="hidden" name="shipping[address_id]" id="shipping:address_id"/>
    {assign var="stateExist" value=false}
    {assign var="postCodeExist" value=false}
    {assign var="dniExist" value=false}
    {assign var="homePhoneExist" value=false}
    {assign var="mobilePhoneExist" value=false}
    {assign var="atLeastOneExists" value=false}
    <ul id="address_form">
        {foreach from=$ordered_adr_fields item=field_name}
        {if $field_name eq 'company'}
            <div class="jm-input">
                <input name="shipping[{$field_name}]" id="delivery-{$field_name}" class="full-width" type="text"
                       placeholder=" "
                       {if isset($required_fields) && in_array('company', $required_fields)}data-validation="required"{/if}
                       data-validation-error-msg-required="{l s='Company' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                    {l s='Company' mod='jmango360api'}{if isset($required_fields) && in_array('company', $required_fields)}*{/if}
                                </span>
                </label>
            </div>
        {/if}
        {if $field_name eq 'vat_number'}
            <div class="jm-input">
                <input name="shipping[{$field_name}]" id="delivery-{$field_name}" class="full-width" type="text"
                       placeholder=" "
                       {if isset($required_fields) && in_array('vat_number', $required_fields)}data-validation="required"{/if}
                       data-validation-error-msg-required="{l s='VAT Number' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                    {l s='VAT Number' mod='jmango360api'}{if isset($required_fields) && in_array('vat_number', $required_fields)}*{/if}
                                </span>
                </label>
            </div>
        {/if}
        {if $field_name eq 'dni'}
            {assign var="dniExist" value=true}
            <div class="jm-input" id="shipping_dni">
                <input name="shipping[{$field_name}]" id="delivery-{$field_name}" class="full-width" type="text"
                       placeholder=" " data-validation="required"
                       data-validation-error-msg-required="{l s='Identification Number' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                {l s='Identification Number' mod='jmango360api'}*
                                </span>
                </label>
                <span class="form_info">
                                        {l s='DNI / NIF / NIE' mod='jmango360api'}
                                    </span>
            </div>
        {/if}
        {if $field_name eq 'firstname'}
            <div class="jm-input">
                <input name="shipping[{$field_name}]" id="delivery-{$field_name}" class="full-width" type="text"
                       placeholder=" " data-validation="required"
                       data-validation-error-msg-required="{l s='First Name' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                {l s='First Name' mod='jmango360api'}*
                                </span>
                </label>
            </div>
        {/if}
        {if $field_name eq 'lastname'}
            <div class="jm-input">
                <input name="shipping[{$field_name}]" id="delivery-{$field_name}" class="full-width" type="text"
                       placeholder=" " data-validation="required"
                       data-validation-error-msg-required="{l s='Last Name' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                {l s='Last Name' mod='jmango360api'}*
                                </span>
                </label>
            </div>
        {/if}
        {if $field_name eq 'address1'}
            <div class="jm-input">
                <input name="shipping[{$field_name}]" id="delivery-{$field_name}" class="full-width" placeholder=" "
                       data-validation="required"
                       data-validation-error-msg-required="{l s='Address' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                {l s='Address' mod='jmango360api'}*
                                </span>
                </label>
            </div>
        {/if}
        {if $field_name eq 'address2'}
            <div class="jm-input">
                <input name="shipping[{$field_name}]" id="delivery-{$field_name}" class="full-width" placeholder=" "
                       {if isset($required_fields) && in_array('address2', $required_fields)}data-validation="required"{/if}
                       data-validation-error-msg-required="{l s='Address (Line 2)' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                {l s='Address (Line 2)' mod='jmango360api'}{if isset($required_fields) && in_array('address2', $required_fields)}*{/if}
                                </span>
                </label>
            </div>
        {/if}
        {if $field_name eq 'postcode'}
            {assign var="postCodeExist" value=true}
            <div class="jm-input" id="shipping_Country_zip_code_format">
                <input name="shipping[{$field_name}]" id="delivery-{$field_name}"
                       class="full-width validate-zip-international" placeholder=" "
                       data-validation="required"
                       data-validation-error-msg-required="{l s='Zip/Postal Code' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                {l s='Zip/Postal Code' mod='jmango360api'}*
                                </span>
                </label>
            </div>
        {/if}
        {if $field_name eq 'city'}
            <div class="jm-input">
                <input name="shipping[{$field_name}]" id="delivery-{$field_name}" class="full-width" placeholder=" "
                       data-validation="required"
                       data-validation-error-msg-required="{l s='City' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                {l s='City' mod='jmango360api'}*
                                </span>
                </label>
            </div>
            {*if customer hasn't update his layout address, country has to be verified but it's deprecated*}
        {/if}
        {if $field_name eq 'Country:name' || $field_name eq 'country'
        || $field_name eq 'Country:iso_code'}
            <li id="Country_name" class="fields" style="display: none">
                <div class="field">
                    <label for="shipping:country_id" class="required">
                        {l s='Country' mod='jmango360api'}
                    </label>
                    <div id="shipping_country_id" class="input-box input-country">
                        <select name="shipping[country_id]" id="shipping:country_id"
                                class="validate-select" title="Country"
                                onchange="shipping.onCountryChange(event, 0, 0, {if !$is_logged}1{else}0{/if}, true)">
                            {foreach from=$countries item=v}
                                <option value="{$v.id_country|intval}"
                                        {if $current_country|intval === $v.id_country|intval}selected{/if}>
                                    {$v.name|escape:'html':'UTF-8'}
                                </option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </li>
            <script>
                {foreach from=$countries item=v}
                if ({$v.id_country|intval} ===
                Number($('#shipping\\:country_id[name=shipping\\[country_id\\]]').val())
                )
                {
                    $('#shipping-display-country').val('{$v.name|escape:'html':'UTF-8'}')
                }
                {/foreach}
            </script>
            <div class="jm-input arrow-right">
                <input readonly
                       name="shipping-display-country"
                       onclick="{if !$is_logged}$('body').addClass('showAddressForm');$('#parent-div').addClass('showAddressForm');$('#body').addClass('showAddressForm');{/if}$('#shipping-address-details').addClass('showAddressForm'); shipping.setSelectedCountry(); $('#fullscreen-select-shipping-country').show()"
                       id="shipping-display-country" class="full-width" type="text" placeholder=" "
                       data-validation="required" value=""
                       data-validation-error-msg-required="{l s='Country' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                    <span>{l s='Country' mod='jmango360api'}*</span>
                </label>
                <div id="fullscreen-select-shipping-country" style="display: none">
                    <div class="overlay-window">
                        <header class="overlay-header">
                            <a class="back-btn" onclick="{if !$is_logged}$('body').removeClass('showAddressForm');$('#parent-div').removeClass('showAddressForm');{/if}$('#shipping-address-details').removeClass('showAddressForm'); $('#fullscreen-select-shipping-country').hide()"></a>
                            <h1>{l s='Country' mod='jmango360api'}</h1>
                        </header>
                        <ul id="shipping_country_list" class="options-list">
                            {foreach from=$countries item=v}
                                <li id="shipping_country_{$v.id_country|intval}"
                                    onclick="{if !$is_logged}$('body').removeClass('showAddressForm');$('#parent-div').removeClass('showAddressForm');{/if}$('#shipping-address-details').removeClass('showAddressForm'); $('#shipping\\:country_id[name=shipping\\[country_id\\]]').val({$v.id_country|intval}).change(); $('shipping-{$field_name}').trigger('change'); $('#shipping-display-country').val('{$v.name|escape:'html':'UTF-8'}'); $('#fullscreen-select-shipping-country').hide()">{$v.name|escape:'html':'UTF-8'}</li>
                            {/foreach}
                        </ul>
                    </div>
                </div>
            </div>
        {/if}
        {if $field_name eq 'State:name' || $field_name eq 'State'}
            {assign var="stateExist" value=true}
            <li class="fields" id="State_name" style="display: none">
                <div id="shipping:state" class="field">
                    <label for="shipping:state_id" class="required">
                        {l s='State' mod='jmango360api'}
                    </label>
                    <div class="input-box">
                        <select id="shipping:state_id" name="shipping[state_id]" title="State/Province"
                                class="validate-select">
                            <option value="">
                                {l s='Please select region, state, province' mod='jmango360api'}
                            </option>
                        </select>
                    </div>
                </div>
            </li>
            <div class="jm-input arrow-right" id="shipping-display-state-div">
                <input readonly
                       name="shipping-display-state" onclick="{if !$is_logged}$('body').addClass('showAddressForm');$('#parent-div').addClass('showAddressForm');{/if}$('#shipping-address-details').addClass('showAddressForm');$('#fullscreen-select-shipping-state').show()"
                       id="shipping-display-state" class="full-width" type="text" placeholder=" "
                       data-validation="required" value=""
                       data-validation-error-msg-required="{l s='State' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                    <span>{l s='State' mod='jmango360api'}*</span>
                </label>
                <div id="fullscreen-select-shipping-state" style="display: none">
                    <div class="overlay-window">
                        <header class="overlay-header">
                            <a class="back-btn" onclick="{if !$is_logged}$('body').removeClass('showAddressForm');$('#parent-div').removeClass('showAddressForm');{/if}$('#shipping-address-details').removeClass('showAddressForm'); $('#fullscreen-select-shipping-state').hide()"></a>
                            <h1>{l s='State' mod='jmango360api'}</h1>
                        </header>
                        <ul class="options-list" id="shipping-state-list">
                        </ul>
                    </div>
                </div>
            </div>
        {/if}
        {if $field_name eq 'phone'}
            {assign var="homePhoneExist" value=true}
            <div class="jm-input">
                <input name="shipping[{$field_name}]" type="tel" id="delivery-{$field_name}" class="full-width"
                       placeholder=" " {if isset($required_fields) && in_array('phone', $required_fields)}data-validation="required"{/if}
                       data-validation-error-msg-required="{l s='Home Phone' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                {l s='Home Phone' mod='jmango360api'}{if isset($required_fields) && in_array('phone', $required_fields)}*{/if}
                                </span>
                </label>
            </div>
        {/if}
        {if $field_name eq 'phone_mobile'}
            {assign var="mobilePhoneExist" value=true}
            <div class="jm-input">
                <input name="shipping[{$field_name}]" type="tel" id="delivery-{$field_name}" class="full-width"
                       placeholder=" " {if isset($required_fields) && in_array('phone_mobile', $required_fields)}data-validation="required"{/if}
                       data-validation-error-msg-required="{l s='Mobile Phone' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                {l s='Mobile Phone' mod='jmango360api'}{if isset($required_fields) && in_array('phone_mobile', $required_fields)}*{/if}
                                </span>
                </label>
            </div>
        {/if}
        {if ($field_name eq 'phone_mobile' || $field_name eq 'phone_mobile')
        && !isset($atLeastOneExists)
        && isset($one_phone_at_least)
        && $one_phone_at_least}
            {assign var="atLeastOneExists" value=true}
            <p class="inline-infos required">** {l s='You must register at least one phone number.' mod='jmango360api'}
            </p>
        {/if}
        {/foreach}
        {if !$postCodeExist}
            <div class="jm-input" id="shipping_Country_zip_code_format">
                <input name="shipping[postcode]" id="delivery-postcode"
                       class="full-width validate-zip-international" placeholder=" "
                       data-validation="required"
                       data-validation-error-msg-required="{l s='Zip/Postal Code' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                {l s='Zip/Postal Code' mod='jmango360api'}*
                                </span>
                </label>
            </div>
        {/if}
        {if !$stateExist}
            <li class="fields" id="State_name" style="display: none">
                <div id="shipping:state" class="field">
                    <label for="shipping:state_id" class="required">{l s='State' mod='jmango360api'}</label>
                    <div class="input-box">
                        <select id="shipping:state_id" name="shipping[state_id]" title="State/Province"
                                class="validate-select">
                            <option value="">{l s='Please select region, state, province' mod='jmango360api'}</option>
                        </select>
                    </div>
                </div>
            </li>
            <div class="jm-input" id="shipping-display-state-div">
                <input readonly
                       name="shipping-display-state" onclick="{if !$is_logged}$('body').addClass('showAddressForm');$('#parent-div').addClass('showAddressForm');{/if}$('#shipping-address-details').addClass('showAddressForm'); $('#fullscreen-select-shipping-state').show()"
                       id="shipping-display-state" class="full-width" type="text" placeholder=" "
                       data-validation="required" value=""
                       data-validation-error-msg-required="{l s='State' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                    <span>{l s='State' mod='jmango360api'}*</span>
                </label>
                <div id="fullscreen-select-shipping-state" style="display: none">
                    <div class="overlay-window">
                        <header class="overlay-header">
                            <a class="back-btn" onclick="{if !$is_logged}$('body').removeClass('showAddressForm');$('#parent-div').removeClass('showAddressForm');{/if}$('#shipping-address-details').removeClass('showAddressForm'); $('#fullscreen-select-shipping-state').hide()"></a>
                            <h1>{l s='State' mod='jmango360api'}</h1>
                        </header>
                        <ul class="options-list" id="shipping-state-list">
                        </ul>
                    </div>
                </div>
            </div>
        {/if}
        {if !$dniExist}
            <div class="jm-input" id="shipping_dni">
                <input name="shipping[dni]" id="delivery-dni" class="full-width" placeholder=" "
                       data-validation="required"
                       data-validation-error-msg-required="{l s='Identification Number' mod='jmango360api'} {l s='is required' mod='jmango360api'}">
                <label>
                                <span>
                                {l s='Identification Number' mod='jmango360api'}*
                                </span>
                </label>
                <span class="form_info">
                                        {l s='DNI / NIF / NIE' mod='jmango360api'}
                                    </span>
            </div>
        {/if}
        <div class="jm-input">
            <input name="shipping[other]" id="delivery-other"
                   class="full-width validate-zip-international" placeholder=" "
                   {if isset($required_fields) && in_array('other', $required_fields)}data-validation="required"{/if}
                   data-validation-error-msg-required="{l s='Additional Information' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                   type="text">
            <label>
                                <span>
                                {l s='Additional Information' mod='jmango360api'} {if isset($required_fields) && in_array('other', $required_fields)}*{/if}
                                </span>
            </label>
        </div>
        {if !$homePhoneExist}
            <div class="jm-input">
                <input type="tel" class="input-text  required-entry" name="shipping[phone]" data-validate=""
                       type="text" id="delivery-phone" name="phone" value="" placeholder=" "
                       {if isset($required_fields) && in_array('phone', $required_fields)}data-validation="required"{/if}
                       data-validation-error-msg-required="{l s='Home Phone' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                {l s='Home Phone' mod='jmango360api'}{if isset($required_fields) && in_array('phone', $required_fields)}*{/if}
                                </span>
                </label>
            </div>
        {/if}
        {if !$mobilePhoneExist}
            <div class="jm-input">
                <input type="tel" class="input-text  required-entry" name="shipping[phone_mobile]" data-validate=""
                       type="text" id="deliveery-phone_mobile" name="phone" value="" placeholder=" "
                       {if isset($required_fields) && in_array('phone_mobile', $required_fields)}data-validation="required"{/if}
                       data-validation-error-msg-required="{l s='Mobile Phone' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                >
                <label>
                                <span>
                                {l s='Mobile Phone' mod='jmango360api'}{if isset($required_fields) && in_array('phone_mobile', $required_fields)}*{/if}
                                </span>
                </label>
            </div>
        {/if}
        <li id="shipping_alias" class="fields"
            style="{if $is_logged eq 0}display: none {/if}">
            <div class="field">
                <div class="jm-input">
                    <input name="shipping[alias]" id="delivery-alias" class="full-width" type="text" placeholder=" "
                           data-validation="required"
                           value="{l s='My address' mod='jmango360api'}"
                           data-validation-error-msg-required="{l s='Alias' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                                <span>
                                    {l s='Please assign an address title for future reference.' mod='jmango360api'}*
                                </span>
                    </label>
                </div>
            </div>
        </li>
        <li class="fields" id="register-customer-password"
            style="display: none;">
            <div class="field">
                <label for="shipping:customer_password" class="required">
                    <em>*</em>{l s='Password' mod='jmango360api'}
                </label>
                <div class="input-box">
                    <input type="password" name="shipping[customer_password]"
                           id="shipping:customer_password"
                           title="Password"
                           class="input-text required-entry validate-password">
                </div>
            </div>
            <div class="field">
                <label for="shipping:confirm_password"
                       class="required">
                    <em>*</em>
                    {l s='Confirm Password' mod='jmango360api'}
                </label>
                <div class="input-box">
                    <input type="password" name="shipping[confirm_password]"
                           title="Confirm Password" id="shipping:confirm_password"
                           class="input-text required-entry validate-cpassword">
                </div>
            </div>
        </li>
        <li class="no-display">
            <input type="hidden" id='shipping:save_in_address_book'
                   name="shipping[save_in_address_book]" value="1"/>
        </li>
    </ul>
</fieldset>
<script type="text/javascript">
    $(function() {
        $('input[readonly]').on('focus', function(ev) {
            $(this).trigger('blur');
        });
    });
</script>