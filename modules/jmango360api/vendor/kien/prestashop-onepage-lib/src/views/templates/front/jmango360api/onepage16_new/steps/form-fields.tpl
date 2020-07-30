<fieldset class="">
    <input type="hidden" name="billing[address_id]" id="billing:address_id"/>
    {if $is_logged} {* register user (aka customer)*}
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
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" type="text"
                           placeholder=" "
                           data-validation-error-msg-required="{l s='Company' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                           {if isset($required_fields) && in_array('company', $required_fields)}data-validation="required"{/if}>
                    <label>
                                <span>
                                    {l s='Company' mod='jmango360api'}{if isset($required_fields) && in_array('company', $required_fields)}*{/if}
                                </span>
                    </label>
                </div>
            {/if}
            {if $field_name eq 'vat_number'}
                <div class="jm-input">
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" type="text"
                           placeholder=" "
                           data-validation-error-msg-required="{l s='VAT Number' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                           {if isset($required_fields) && in_array('vat_number', $required_fields)}data-validation="required"{/if}>
                    <label>
                                <span>
                                    {l s='VAT Number' mod='jmango360api'}{if isset($required_fields) && in_array('vat_number', $required_fields)}*{/if}
                                </span>
                    </label>
                </div>
            {/if}
            {if $field_name eq 'dni'}
                {assign var="dniExist" value=true}
                <div class="jm-input" id="dni">
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" type="text"
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
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" type="text"
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
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" type="text"
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
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" placeholder=" "
                           data-validation="required" type="text"
                           data-validation-error-msg-required="{l s='Address' mod='jmango360api'} {l s='is required' mod='jmango360api'}">
                    <label>
                                <span>
                                {l s='Address' mod='jmango360api'}*
                                </span>
                    </label>
                </div>
            {/if}
            {if $field_name eq 'address2'}
                <div class="jm-input">
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" placeholder=" "
                           {if isset($required_fields) && in_array('address2', $required_fields)}data-validation="required"{/if}
                           data-validation-error-msg-required="{l s='Address (Line 2)' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                           type="text">
                    <label>
                                <span>
                                {l s='Address (Line 2)' mod='jmango360api'}{if isset($required_fields) && in_array('address2', $required_fields)}*{/if}
                                </span>
                    </label>
                </div>
            {/if}
            {if $field_name eq 'postcode'}
                {assign var="postCodeExist" value=true}
                <div class="jm-input" id="Country_zip_code_format">
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}"
                           class="full-width validate-zip-international" placeholder=" "
                           data-validation="required"
                           data-validation-error-msg-required="{l s='Zip/Postal Code' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                           type="text">
                    <label>
                                <span>
                                {l s='Zip/Postal Code' mod='jmango360api'}*
                                </span>
                    </label>
                </div>
            {/if}
            {if $field_name eq 'city'}
                <div class="jm-input">
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" placeholder=" "
                           data-validation="required" type="text"
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
                <li id="Country_name" class="fields" style="display: none;">
                    <div class="field">
                        <label for="billing:country_id" class="required">
                            {l s='Country' mod='jmango360api'}
                        </label>
                        <div id="billing_country_id" class="input-box input-country">
                            <select name="billing[country_id]" id="billing:country_id"
                                    class="validate-select" title="Country"
                                    onchange="billing.onCountryChange(event, 0, 0, true, 0)">
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
                    Number($('#billing\\:country_id[name=billing\\[country_id\\]]').val())
                    )
                    {
                        $('#billing-display-country').val('{$v.name|escape:'html':'UTF-8'}')
                    }
                    {/foreach}
                </script>
                <div class="jm-input arrow-right">
                    <input readonly
                           name="billing-display-country"
                           onclick="$('#billing-address-details').addClass('showAddressForm'); billing.setSelectedCountry(); $('#fullscreen-select-billing-country').show()"
                           id="billing-display-country" class="full-width" type="text" placeholder=" "
                           data-validation="required" value=""
                           data-validation-error-msg-required="{l s='Country' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                        <span>{l s='Country' mod='jmango360api'}*</span>
                    </label>
                    <div id="fullscreen-select-billing-country" style="display: none">
                        <div class="overlay-window">
                            <header class="overlay-header">
                                <a class="back-btn" onclick="$('#billing-address-details').removeClass('showAddressForm'); $('#fullscreen-select-billing-country').hide()"></a>
                                {l s='Country' mod='jmango360api'}
                            </header>
                            <ul id="billing_country_list" class="options-list">
                                {foreach from=$countries item=v}
                                    <li id="billing_country_{$v.id_country|intval}"
                                        onclick="$('#billing-address-details').removeClass('showAddressForm'); $('#billing\\:country_id[name=billing\\[country_id\\]]').val({$v.id_country|intval}).change(); $('billing-{$field_name}').trigger('change'); $('#billing-display-country').val('{$v.name|escape:'html':'UTF-8'}'); $('#fullscreen-select-billing-country').hide()">{$v.name|escape:'html':'UTF-8'}</li>
                                {/foreach}
                            </ul>
                        </div>
                    </div>
                </div>
            {/if}
            {if $field_name eq 'State:name' || $field_name eq 'State'}
                {assign var="stateExist" value=true}
                <li class="fields" id="State_name" style="display: none">
                    <div id="billing:state" class="field">
                        <label for="billing:state_id" class="required">
                            {l s='State' mod='jmango360api'}
                        </label>
                        <div class="input-box">
                            <select id="billing:state_id" name="billing[state_id]" title="State/Province"
                                    class="validate-select">
                                <option value="">
                                    {l s='Please select region, state, province' mod='jmango360api'}
                                </option>
                            </select>
                        </div>
                    </div>
                </li>
                <div class="jm-input arrow-right" id="billing-display-state-div">
                    <input readonly
                           name="billing-display-state" onclick="$('#billing-address-details').addClass('showAddressForm'); $('#fullscreen-select-billing-state').show()"
                           id="billing-display-state" class="full-width" type="text" placeholder=" "
                           data-validation="required" value=""
                           data-validation-error-msg-required="{l s='State' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                        <span>{l s='State' mod='jmango360api'}*</span>
                    </label>
                    <div id="fullscreen-select-billing-state" style="display: none">
                        <div class="overlay-window">
                            <header class="overlay-header">
                                <a class="back-btn" onclick="$('#fullscreen-select-billing-state').hide()"></a>
                                <h1>{l s='State' mod='jmango360api'}</h1>
                            </header>
                            <ul class="options-list" id="billing-state-list">
                                {*<li onclick="$('#billing\\:state_id[name=billing\\[state_id\\]]').val({$v.id_country|intval}).change(); $('#billing-display-state').val('{$v.name|escape:'html':'UTF-8'}'); $('#fullscreen-select-billing-state').hide()">{$v.name|escape:'html':'UTF-8'}</li>*}
                            </ul>
                        </div>
                    </div>
                </div>
            {/if}
            {if $field_name eq 'phone'}
                {assign var="homePhoneExist" value=true}
                <div class="jm-input">
                    <input name="billing[{$field_name}]" type="tel" id="invoice-{$field_name}" class="full-width"
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
                    <input name="billing[{$field_name}]" type="tel" id="invoice-{$field_name}" class="full-width"
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
                <p class="inline-infos required">
                   ** {l s='You must register at least one phone number.' mod='jmango360api'}
                </p>
            {/if}
            {/foreach}
            {if !$postCodeExist}
                <div class="jm-input" id="Country_zip_code_format">
                    <input name="billing[postcode]" id="invoice-postcode" class="full-width" type="text"
                           placeholder=" "
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
                    <div id="billing:state" class="field">
                        <label for="billing:state_id" class="required">{l s='State' mod='jmango360api'}</label>
                        <div class="input-box">
                            <select id="billing:state_id" name="billing[state_id]" title="State/Province"
                                    class="validate-select">
                                <option value="">{l s='Please select region, state, province' mod='jmango360api'}</option>
                            </select>
                        </div>
                    </div>
                </li>
                <div class="jm-input arrow-right" id="billing-display-state-div">
                    <input readonly
                           name="billing-display-state" onclick="$('#billing-address-details').addClass('showAddressForm'); $('#fullscreen-select-billing-state').show()"
                           id="billing-display-state" class="full-width" type="text" placeholder=" "
                           data-validation="required"
                           data-validation-error-msg-required="{l s='State' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                           value="">
                    <label>
                        <span>{l s='State' mod='jmango360api'}*</span>
                    </label>
                    <div id="fullscreen-select-billing-state" style="display: none">
                        <div class="overlay-window">
                            <header class="overlay-header">
                                <a class="back-btn" onclick="$('#billing-address-details').removeClass('showAddressForm'); $('#fullscreen-select-billing-state').hide()"></a>
                                <h1>{l s='State' mod='jmango360api'}</h1>
                            </header>
                            <ul class="options-list" id="billing-state-list">
                                {*<li onclick="$('#billing\\:state_id[name=billing\\[state_id\\]]').val({$v.id_country|intval}).change(); $('#billing-display-state').val('{$v.name|escape:'html':'UTF-8'}'); $('#fullscreen-select-billing-state').hide()">{$v.name|escape:'html':'UTF-8'}</li>*}
                            </ul>
                        </div>
                    </div>
                </div>
            {/if}
            {if !$dniExist}
                <div class="jm-input" id="dni">
                    <input name="billing[dni]" id="invoice-dni" class="full-width" type="text" placeholder=" "
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
                <input name="billing[other]" id="invoice-other"
                       class="full-width validate-zip-international" placeholder=" "
                       {if isset($required_fields) && in_array('other', $required_fields)}data-validation="required"{/if}
                       data-validation-error-msg-required="{l s='Additional Information' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                       type="text">
                <label>
                                <span>
                                {l s='Additional Information' mod='jmango360api'}{if isset($required_fields) && in_array('other', $required_fields)}*{/if}
                                </span>
                </label>
            </div>
            {if !$homePhoneExist}
                <div class="jm-input">
                    <input name="billing[phone]" id="invoice-phone" class="full-width" type="tel" placeholder=" "
                           {if isset($required_fields) && in_array('phone', $required_fields)}data-validation="required"{/if}
                           data-validation-error-msg-required="{l s='Home Phone' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                                <span>
                                    {l s='Home Phone' mod='jmango360api'}{if isset($required_fields) && in_array({$field_name}, $required_fields)}*{/if}
                                </span>
                    </label>
                </div>
            {/if}
            {if !$mobilePhoneExist}
                <div class="jm-input">
                    <input name="billing[phone_mobile]" type="tel" id="invoice-phone_mobile" class="full-width"
                           placeholder=" " {if isset($required_fields) && in_array('phone_mobile', $required_fields)}data-validation="required"{/if}
                           data-validation-error-msg-required="{l s='Mobile Phone' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                                <span>
                                {l s='Mobile Phone' mod='jmango360api'} {if isset($required_fields) && in_array('phone_mobile', $required_fields)}*{/if}
                                </span>
                    </label>
                </div>
            {/if}
            <li id="billing_alias" class="fields"
                style="{if $is_logged eq 0}display: none {/if}">
                <div class="field">
                    <div class="jm-input">
                        <input name="billing[alias]" id="invoice-alias" class="full-width" type="text" placeholder=" "
                            data-validation="required"
                               data-validation-error-msg-required="{l s='Alias' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                               value="{l s='My Address' mod='jmango360api'}">
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
                    <label for="billing:customer_password" class="required">
                        <em>*</em>{l s='Password' mod='jmango360api'}
                    </label>
                    <div class="input-box">
                        <input type="password" name="billing[customer_password]"
                               id="billing:customer_password"
                               title="Password"
                               class="input-text required-entry validate-password">
                    </div>
                </div>
                <div class="field">
                    <label for="billing:confirm_password"
                           class="required">
                        <em>*</em>
                        {l s='Confirm Password' mod='jmango360api'}
                    </label>
                    <div class="input-box">
                        <input type="password" name="billing[confirm_password]"
                               title="Confirm Password" id="billing:confirm_password"
                               class="input-text required-entry validate-cpassword">
                    </div>
                </div>
            </li>
            <li class="no-display">
                <input type="hidden" id='billing:save_in_address_book'
                       name="billing[save_in_address_book]" value="1"/>
            </li>
        </ul>
    {else} {* Guest*}
        <ul id="address_form">
            {if !$is_logged}
                {assign var='stateExist' value=false}
                {assign var="postCodeExist" value=false}
                {assign var="dniExist" value=false}
                <!-- Account -->
                <div class="jm-input">
                    <input name="billing[email]" id="invoice-email" class="full-width" type="text" placeholder=" "
                           data-validation="required"
                           data-validation-error-msg-required="{l s='Email Address' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                                <span>
                                    {l s='Email Address' mod='jmango360api'}*
                                </span>
                    </label>
                </div>
                <li class="wide"
                    style="{if $is_logged eq 1}display:none{/if}">
                    <div class="field field-radio">
                        <div style="position: relative; margin-top: 10px;">
                            <div class="prefix-title-container">
                                <div class="prefix-title-label">{l s='Title' mod='jmango360api'}</div>
                                <div class="prefix-title-options">
                                    {foreach from=$genders key=k item=gender}
                                        <div class="prefix-title">
                                            <input id="id_gender_{$gender->id|intval}"
                                                   type="radio" name="billing[gender_id]" value="{$gender->id|intval}"/>
                                            <label for="id_gender_{$gender->id|intval}">
                                                {$gender->name|escape:'html':'UTF-8'}
                                            </label>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <div class="jm-input">
                    <input name="billing[firstname]" id="invoice-firstname" class="full-width" type="text"
                           placeholder=" " data-validation="required"
                           data-validation-error-msg-required="{l s='First Name' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                                <span>
                                    {l s='First Name' mod='jmango360api'}*
                                </span>
                    </label>
                </div>
                <div class="jm-input">
                    <input name="billing[lastname]" id="invoice-lastname" class="full-width" type="text" placeholder=" "
                           data-validation="required"
                           data-validation-error-msg-required="{l s='Last Name' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                                <span>
                                    {l s='Last Name' mod='jmango360api'}*
                                </span>
                    </label>
                </div>
                <div class="jm-input">
                    <input name="billing[birthday]" id="invoice-birthday" class="full-width" type="date"
                           placeholder=" "
                           data-validation-error-msg-required="{l s='Date of Birth' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                                <span>
                                    {l s='Date of Birth' mod='jmango360api'}
                                </span>
                    </label>
                </div>
                {*{if isset($newsletter) && $newsletter}*}
                {*<li id="newsletter" class="control">*}
                {*<input type="checkbox" name="newsletter"*}
                {*id="newsletter" value="1"*}
                {*{if isset($guestInformations)*}
                {*&& isset($guestInformations.newsletter)*}
                {*&& $guestInformations.newsletter}*}
                {*checked="checked"*}
                {*{/if} autocomplete="off"/>*}
                {*<label id="newsletter_label" class="" for="newsletter">*}
                {*{l s='Sign up for our newsletter!' mod='jmango360api'}*}
                {*</label>*}
                {*</li>*}
                {*{/if}*}

                {*{if isset($optin) && $optin}*}
                {*<li id="optin" class="field control">*}
                {*<input type="checkbox" name="optin" id="optin" value="1"*}
                {*{if isset($guestInformations)*}
                {*&& isset($guestInformations.optin)*}
                {*&& $guestInformations.optin}*}
                {*checked="checked"*}
                {*{/if} autocomplete="off"/>*}
                {*<label id="optin_label" class="" for="optin">*}
                {*{l s='Receive special offers from our partners!' mod='jmango360api'}*}
                {*</label>*}
                {*</li>*}
                {*{/if}*}
                <li class="field control">
                    <input type="hidden" value="{$is_logged}" name="billing[is_logged]">
                </li>
                <div style="background-color: #f9f9f9;
                            margin-left: calc(50% - 50vw);
                            width: 100vw;">
                    <h3 class="form-title">{l s='Billing Address' mod='jmango360api'}</h3>
                </div>
            {foreach from=$dlv_all_fields item=field_name}
            {if $field_name eq "company"}
                <div class="jm-input">
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" type="text"
                           placeholder=" "
                           {if isset($required_fields) && in_array('company', $required_fields)}data-validation="required"{/if}
                           data-validation-error-msg-required="{l s='Company' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                            <span>
                                {l s='Company' mod='jmango360api'}{if isset($required_fields) && in_array({$field_name}, $required_fields)}*{/if}
                            </span>
                    </label>
                </div>
            {elseif $field_name eq "vat_number"}
                <div class="jm-input">
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" type="text"
                           placeholder=" "
                           {if isset($required_fields) && in_array('vat_number', $required_fields)}data-validation="required"{/if}
                           data-validation-error-msg-required="{l s='VAT Number' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                            <span>
                                {l s='VAT Number' mod='jmango360api'}{if isset($required_fields) && in_array({$field_name}, $required_fields)}*{/if}
                            </span>
                    </label>
                </div>
            {elseif $field_name eq "dni"}
                {assign var='dniExist' value=true}
                <div class="jm-input" id="dni">
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" type="text"
                           placeholder=" "
                           data-validation="required"
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
            {elseif $field_name eq "address1"}
                <div class="jm-input">
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" type="text"
                           placeholder=" "
                           {if isset($required_fields) && in_array('address1', $required_fields)}data-validation="required"{/if}
                           data-validation-error-msg-required="{l s='Address' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                            <span>
                                {l s='Address' mod='jmango360api'}{if isset($required_fields) && in_array({$field_name}, $required_fields)}*{/if}
                            </span>
                    </label>
                </div>
            {*{elseif $field_name eq "address2"}*}
                {*<div class="jm-input">*}
                    {*<input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" type="text"*}
                           {*placeholder=" "*}
                           {*{if isset($required_fields) && in_array('address2', $required_fields)}data-validation="required"{/if}*}
                           {*data-validation-error-msg-required="{l s='Address (Line 2)' mod='jmango360api'} {l s='is required' mod='jmango360api'}"*}
                    {*>*}
                    {*<label>*}
                            {*<span>*}
                                {*{l s='Address (Line 2)' mod='jmango360api'}{if isset($required_fields) && in_array({$field_name}, $required_fields)}*{/if}*}
                            {*</span>*}
                    {*</label>*}
                {*</div>*}
            {elseif $field_name eq "postcode"}
                {assign var='postCodeExist' value=true}
                <div class="jm-input" id="Country_zip_code_format">
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" type="text"
                           placeholder=" "
                           data-validation="required"
                           data-validation-error-msg-required="{l s='Zip/Postal Code' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                            <span>
                                {l s='Zip/Postal Code' mod='jmango360api'}*
                            </span>
                    </label>
                </div>
            {elseif $field_name eq "city"}
                <div class="jm-input">
                    <input name="billing[{$field_name}]" id="invoice-{$field_name}" class="full-width" type="text"
                           placeholder=" "
                           {if isset($required_fields) && in_array('city', $required_fields)}data-validation="required"{/if}
                           data-validation-error-msg-required="{l s='City' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                            <span>
                                {l s='City' mod='jmango360api'}{if isset($required_fields) && in_array({$field_name}, $required_fields)}*{/if}
                            </span>
                    </label>
                </div>
                <!-- if customer hasn't update his layout address, country has to be verified but it's deprecated -->
            {elseif $field_name eq "Country:name" || $field_name eq "country"}
                <li id="Country_name" class="fields" style="display: none;">
                    <div class="field">
                        <label for="billing:country_id" class="required">
                            {l s='Country' mod='jmango360api'}
                        </label>
                        <div id="billing_country_id" class="input-box input-country">
                            <select name="billing[country_id]" id="billing:country_id"
                                    class="validate-select" title="Country"
                                    onchange="billing.onCountryChange(event, 0, 0, true, 1)">
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
                    Number($('#billing\\:country_id[name=billing\\[country_id\\]]').val())
                    )
                    {
                        $('#billing-display-country').val('{$v.name|escape:'html':'UTF-8'}')
                    }
                    {/foreach}
                </script>
                <div class="jm-input arrow-right">
                    <input readonly
                           name="billing-display-country" onclick="$('body').addClass('showAddressForm');$('#parent-div').addClass('showAddressForm');$('#billing-address-details').addClass('showAddressForm'); billing.setSelectedCountry(); $('#fullscreen-select-billing-country').show()"
                           id="billing-display-country" class="full-width" type="text" placeholder=" "
                           data-validation="required" value=""
                           data-validation-error-msg-required="{l s='Country' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                        <span>{l s='Country' mod='jmango360api'}*</span>
                    </label>
                    <div id="fullscreen-select-billing-country" style="display: none">
                        <div class="overlay-window">
                            <header class="overlay-header">
                                <a class="back-btn" onclick="$('body').removeClass('showAddressForm');$('#parent-div').removeClass('showAddressForm');$('#billing-address-details').removeClass('showAddressForm'); $('#fullscreen-select-billing-country').hide()"></a>
                                <h1>{l s='Country' mod='jmango360api'}</h1>
                            </header>
                            <ul id="billing_country_list" class="options-list">
                                {foreach from=$countries item=v}
                                    <li id="billing_country_{$v.id_country|intval}"
                                        onclick="$('body').removeClass('showAddressForm');$('#parent-div').removeClass('showAddressForm');$('#billing-address-details').removeClass('showAddressForm'); $('#billing\\:country_id[name=billing\\[country_id\\]]').val({$v.id_country|intval}).change(); $('billing-{$field_name}').trigger('change'); $('#billing-display-country').val('{$v.name|escape:'html':'UTF-8'}'); $('#fullscreen-select-billing-country').hide()">{$v.name|escape:'html':'UTF-8'}</li>
                                {/foreach}
                            </ul>
                        </div>
                    </div>
                </div>
            {elseif $field_name eq "State:name"}
                {assign var='stateExist' value=true}
                <li class="fields" id="State_name" style="display: none">
                    <div id="billing:state" class="field">
                        <label for="billing:state_id" class="required">
                            {l s='State' mod='jmango360api'}
                        </label>
                        <div class="input-box">
                            <select id="billing:state_id" name="billing[state_id]"
                                    title="State/Province" class="validate-select">
                                <option value="">
                                    {l s='Please select region, state, province' mod='jmango360api'}
                                </option>
                            </select>
                        </div>
                    </div>
                </li>
                <div class="jm-input arrow-right" id="billing-display-state-div">
                    <input readonly
                           name="billing-display-state" onclick="$('body').addClass('showAddressForm');$('#parent-div').addClass('showAddressForm');$('#billing-address-details').addClass('showAddressForm'); $('#fullscreen-select-billing-state').show()"
                           id="billing-display-state" class="full-width" type="text" placeholder=" "
                           data-validation="required" value=""
                           data-validation-error-msg-required="{l s='State' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                        <span>{l s='State' mod='jmango360api'}*</span>
                    </label>
                    <div id="fullscreen-select-billing-state" style="display: none">
                        <div class="overlay-window">
                            <header class="overlay-header">
                                <a class="back-btn" onclick="$('body').removeClass('showAddressForm');$('#parent-div').removeClass('showAddressForm');$('#billing-address-details').removeClass('showAddressForm'); $('#fullscreen-select-billing-state').hide()"></a>
                                <h1>{l s='State' mod='jmango360api'}</h1>
                            </header>
                            <ul class="options-list" id="billing-state-list">
                                {*<li onclick="$('#billing\\:state_id[name=billing\\[state_id\\]]').val({$v.id_country|intval}).change(); $('#billing-display-state').val('{$v.name|escape:'html':'UTF-8'}'); $('#fullscreen-select-billing-state').hide()">{$v.name|escape:'html':'UTF-8'}</li>*}
                            </ul>
                        </div>
                    </div>
                </div>
            {*{if $field_name eq 'other'}*}
                {*<div class="jm-input">*}
                    {*<input readonly*}
                           {*name="billing[{$field_name}]" id="invoice-{$field_name}"*}
                           {*class="full-width validate-zip-international" placeholder=" "*}
                           {*{if isset($required_fields) && in_array('other', $required_fields)}data-validation="required"{/if}*}
                           {*data-validation-error-msg-required="{l s='Additional information' mod='jmango360api'} {l s='is required' mod='jmango360api'}"*}
                           {*type="text">*}
                    {*<label>*}
                            {*<span>*}
                            {*{l s='Additional information' mod='jmango360api'}{if isset($required_fields) && in_array('other', $required_fields)}*{/if}*}
                            {*</span>*}
                    {*</label>*}
                {*</div>*}
            {*{/if}*}
            {/if}
            {/foreach}
            {if $stateExist eq false}
                <li class="fields" id="State_name" style="display: none">
                    <div id="billing:state" class="field">
                        <label for="billing:state_id" class="required">
                            {l s='State' mod='jmango360api'}
                        </label>
                        <div class="input-box">
                            <select id="billing:state_id" name="billing[state_id]"
                                    title="State" class="validate-select">
                                <option value="">
                                    {l s='Please select region, state, province' mod='jmango360api'}
                                </option>
                            </select>
                        </div>
                    </div>
                </li>
                <div class="jm-input arrow-right" id="billing-display-state-div">
                    <input readonly
                           name="billing-display-state" onclick="$('body').addClass('showAddressForm');$('#parent-div').addClass('showAddressForm');$('#billing-address-details').addClass('showAddressForm'); $('#fullscreen-select-billing-state').show()"
                           id="billing-display-state" class="full-width" type="text" placeholder=" "
                           data-validation="required" value=""
                           data-validation-error-msg-required="{l s='State' mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                        <span>{l s='State' mod='jmango360api'}*</span>
                    </label>
                    <div id="fullscreen-select-billing-state" style="display: none">
                        <div class="overlay-window">
                            <header class="overlay-header">
                                <a class="back-btn" onclick="$('body').removeClass('showAddressForm');$('#parent-div').removeClass('showAddressForm');$('#billing-address-details').removeClass('showAddressForm'); $('#fullscreen-select-billing-state').hide()"></a>
                                <h1>{l s='State' mod='jmango360api'}</h1>
                            </header>
                            <ul class="options-list" id="billing-state-list">
                                {*<li onclick="$('#billing\\:state_id[name=billing\\[state_id\\]]').val({$v.id_country|intval}).change(); $('#billing-display-state').val('{$v.name|escape:'html':'UTF-8'}'); $('#fullscreen-select-billing-state').hide()">{$v.name|escape:'html':'UTF-8'}</li>*}
                            </ul>
                        </div>
                    </div>
                </div>
            {/if}
            {if $postCodeExist eq false}
                <div class="jm-input" id="Country_zip_code_format">
                    <input name="billing[postcode]" id="invoice-postcode" class="full-width" type="text" placeholder=" "
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
            {if $dniExist eq false}
                <div class="jm-input" id="dni">
                    <input name="billing[dni]" id="invoice-dni" class="full-width" type="text" placeholder=" "
                           data-validation="required"
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
                <div class="clearfix"></div>
                <div class="jm-input">
                    <input name="billing[phone_mobile]" id="invoice-phone_mobile" class="full-width" type="tel"
                       placeholder=" "
                        {if (isset($one_phone_at_least) && $one_phone_at_least)
                        || (isset($required_fields) && in_array('phone_mobile', $required_fields))}
                            data-validation="required"
                        {/if}
                       data-validation-error-msg-required="{l s='Mobile Phone' mod="jmango360api"} {l s='is required' mod='jmango360api'}"
                    >
                    <label>
                                <span>
                                    {l s='Mobile Phone' mod="jmango360api"}{if (isset($one_phone_at_least) && $one_phone_at_least)
                                    || (isset($required_fields) && in_array('phone_mobile', $required_fields))}*{/if}
                                </span>
                    </label>
                </div>
                <input type="hidden" name="alias" id="alias" value="{l s='My address' mod='jmango360api'}"/>
                <input type="hidden" name="is_new_customer" id="is_new_customer" value="0"/>
            {/if}
        </ul>
    {/if}
</fieldset>
<script type="text/javascript">
    $(function() {
        $('input[readonly]').on('focus', function(ev) {
            $(this).trigger('blur');
        });
    });
    var birthdayField = $("#invoice-birthday");
    if (birthdayField.length > 0 ) {
        birthdayField.focus(function(){
            $(this).parent().addClass("onfocus");
        });
        birthdayField.blur(function(){
            if (!birthdayField.val()) {
                $(this).parent().removeClass("onfocus");
            }
        });
    }
</script>