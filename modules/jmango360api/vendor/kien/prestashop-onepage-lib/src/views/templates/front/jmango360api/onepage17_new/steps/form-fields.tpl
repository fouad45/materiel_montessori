{**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2018 PrestaShop SA
* @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
{*<p>{$field|json_encode}</p>*}
{if $field.type == 'hidden'}
    {block name='form_field_item_hidden'}
        <input type="hidden" name="{$field.name}" value="{$field.value}" id="{$type}-{$field.name}">
    {/block}
{elseif $field.type != 'checkbox'}
    {if $field.type == 'radio-buttons'}
        {block name='form_field_item_radio'}
            <div style="position: relative; margin-top: 10px;">
                <div class="prefix-title-container">
                    <div class="prefix-title-label">{l s='Social title' mod='jmango360api'}</div>
                    <div class="prefix-title-options">
                        {foreach from=$field.availableValues item="label" key="value"}
                            <div class="prefix-title">
                                <input
                                    id="id_gender_{$value}"
                                    name="{$field.name}"
                                    type="radio"
                                    value="{$value}"
                                    {if $field.required}required{/if}
                                    {if $value eq $field.value} checked {/if}>
                                <label for="id_gender_{$value}">
                                    {$label}
                                </label>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        {/block}
    {elseif $field.name === 'birthday'}
        <div class="jm-input">
            {block name='form_field_item_other'}
                <input name="{$field.name}" id="{$type}-{$field.name}" class="full-width" type="date" placeholder=" "
                       data-validation-error-msg-required="{l s=$field.label mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                       data-validation="{if $field.required}required{/if}">
                <label>
                    {if $field.required}
                        <span>{$field.label}*</span>
                    {/if}
                    {if !$field.required}
                        <span>{$field.label}</span>
                    {/if}
                </label>
            {/block}
        </div>
    {else}
        {if ($field.name != 'id_state' && $field.name != 'id_country' && ($field.name == 'phone' || $field.name == 'phone_mobile'))}
            <div class="jm-input">
                {block name='form_field_item_other'}
                    <input name="{$field.name}" id="{$type}-{$field.name}" class="full-width" type="tel" placeholder=" "
                           data-validation-error-msg-required="{l s=$field.label mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                           data-validation="{if $field.required}required{/if}">
                    <label>
                        {if $field.required}
                            <span>{$field.label}*</span>
                        {/if}
                        {if !$field.required}
                            <span>{$field.label}</span>
                        {/if}
                    </label>
                {/block}
            </div>
        {/if}

        {if ($field.name != 'id_state' && $field.name != 'id_country' && $field.name != 'phone' && $field.name != 'phone_mobile')}
            <div class="jm-input">
                {block name='form_field_item_other'}
                    {if $field.name == 'email'}
                        <input name="{$field.name}" id="{$type}-{$field.name}" class="full-width" type="text" placeholder=" " email="{if $field.required}required{/if}"
                               data-validation-error-msg-required="{l s=$field.label mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                               data-validation-error-msg-email="{l s='You have not given a correct e-mail address' mod='jmango360api'}"
                               data-validation="{if $field.required}required{/if} email">
                        <label>
                            {if $field.required}
                                <span>{$field.label}*</span>
                            {/if}
                            {if !$field.required}
                                <span>{$field.label}</span>
                            {/if}
                        </label>
                    {else}
                        <input name="{$field.name}" id="{$type}-{$field.name}" class="full-width" type="text" placeholder=" "
                               data-validation-error-msg-required="{l s=$field.label mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                               data-validation="{if $field.required}required{/if}" value="{$field.value}">
                        <label>
                            {if $field.required}
                                <span>{$field.label}*</span>
                            {/if}
                            {if !$field.required}
                                <span>{$field.label}</span>
                            {/if}
                        </label>
                    {/if}
                {/block}
            </div>
        {/if}

        {if ($field.name == 'id_state' || $field.name == 'id_country')}
            <div class="jm-input arrow-right">
                {if $type === 'invoice'}
                    <div style="position:absolute; left:0; right:0; top:0; bottom:0;" onclick="$('body').addClass('showAddressForm');$('#parent-div').addClass('showAddressForm');$('#billing-address-details').addClass('showAddressForm');$('#fullscreen-select-{$type}-{$field.name}').show()"></div>
                {else}
                    <div style="position:absolute; left:0; right:0; top:0; bottom:0;" onclick="$('body').addClass('showAddressForm');$('#parent-div').addClass('showAddressForm');$('#shipping-address-details').addClass('showAddressForm');$('#fullscreen-select-{$type}-{$field.name}').show()"></div>
                {/if}
                <input name="{$type}-display-{$field.name}" onclick="$('#fullscreen-select-{$type}-{$field.name}').show()" id="{$type}-display-{$field.name}" class="full-width" type="text" placeholder=" "
                       data-validation-error-msg-required="{l s=$field.label mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                       data-validation="{if $field.required}required{/if}" value="">
                {foreach from=$field.availableValues item="label" key="value"}
                    {if $value eq $field.value}
                        <script type="text/javascript">
                            $('#{$type}-display-{$field.name}').val('{$label}');
                        </script>
                    {/if}
                {/foreach}
                <label>
                    {if $field.required}
                        <span>{$field.label}*</span>
                    {/if}
                    {if !$field.required}
                        <span>{$field.label}</span>
                    {/if}
                </label>
                <div id="fullscreen-select-{$type}-{$field.name}" style="display: none">
                    <div class="overlay-window" style="left:0; top:0;">
                        <header class="overlay-header">
                            {if $type === 'invoice'}
                                <a class="back-btn" onclick="restoreScroll();{if !$is_logged}$('body').removeClass('showAddressForm');{/if}$('#parent-div').removeClass('showAddressForm');$('#billing-address-details').removeClass('showAddressForm');$('#fullscreen-select-{$type}-{$field.name}').hide()"></a>
                            {else}
                                <a class="back-btn" onclick="{if !$is_logged}$('body').removeClass('showAddressForm');{/if}$('#parent-div').removeClass('showAddressForm');$('#shipping-address-details').removeClass('showAddressForm');$('#fullscreen-select-{$type}-{$field.name}').hide()"></a>
                            {/if}
                            {if $field.name == 'id_country'}
                                <h1 style="color: #212529">{l s='Country' mod='jmango360api'}</h1>
                            {else}
                                <h1 style="color: #212529">{l s='State' mod='jmango360api'}</h1>
                            {/if}
                        </header>
                        <ul class="options-list">
                            {foreach from=$field.availableValues item="label" key="value"}
                                {if {$type} === 'invoice'}
                                    <li id="{$type}-{$field.name}-{$value}" onclick="restoreScroll();{if !$is_logged}$('body').removeClass('showAddressForm');{/if}$('#parent-div').removeClass('showAddressForm');$('#billing-address-details').removeClass('showAddressForm'); billing.removeTick({$field.availableValues|@json_encode|escape:'html':'UTF-8'}, '{$field.name}', '{$value}'); $('#{$type}-{$field.name}[name={$field.name}]').val({$value}).change(); $('{$type}-{$field.name}').trigger('change'); $('#{$type}-display-{$field.name}').val('{$label}'); $('#fullscreen-select-{$type}-{$field.name}').hide()">
                                        {$label}
                                    </li>
                                {else}
                                    <li id="{$type}-{$field.name}-{$value}" onclick="{if !$is_logged}$('body').removeClass('showAddressForm');{/if}$('#parent-div').removeClass('showAddressForm');$('#shipping-address-details').removeClass('showAddressForm'); shipping.removeTick({$field.availableValues|@json_encode|escape:'html':'UTF-8'}, '{$field.name}', '{$value}'); $('#{$type}-{$field.name}[name={$field.name}]').val({$value}).change(); $('{$type}-{$field.name}').trigger('change'); $('#{$type}-display-{$field.name}').val('{$label}'); $('#fullscreen-select-{$type}-{$field.name}').hide()">
                                        {$label}
                                    </li>
                                {/if}
                            {/foreach}
                        </ul>
                    </div>
                </div>
            </div>
            <div class="jm-input arrow-right" style="display: none;">
                {if $field.type === 'select'}
                    {block name='form_field_item_select'}
                        <select class="form-control" name="{$field.name}" id="{$type}-{$field.name}" {if $field.required}required{/if}
                                data-validation-error-msg-required="{l s=$field.label mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                                data-validation="{if $field.required}required{/if}">
                            <option value disabled selected>{l s='-- please choose --' mod='jmango360api'}</option>
                            {foreach from=$field.availableValues item="label" key="value"}
                                <option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
                            {/foreach}
                        </select>
                    {/block}

                {elseif $field.type === 'countrySelect'}

                    {block name='form_field_item_country'}
                        <select
                                class="form-control js-country"
                                name="{$field.name}" id="{$type}-{$field.name}"
                                {if $field.required}required{/if}
                                data-validation-error-msg-required="{l s=$field.label mod='jmango360api'} {l s='is required' mod='jmango360api'}"
                                data-validation="{if $field.required}required{/if}"
                        >
                            <option value disabled selected>{l s='-- please choose --' mod='jmango360api'}</option>
                            {foreach from=$field.availableValues item="label" key="value"}
                                <option value="{$value}" {if $value eq $field.value} selected {/if}>{$label}</option>
                            {/foreach}
                        </select>
                    {/block}
                {/if}
            </div>
        {/if}
    {/if}
{else}
    {if $field.type == 'checkbox'}
        <div class="custom-checkbox">
            <input name="{$field.name|escape:'html':'UTF-8'}" id="{$field.name|escape:'html':'UTF-8'}"
               type="checkbox" value="1"
               data-validation="{if $field.required}required{/if}"
               {if $field.value}checked="checked"{/if}
               {if $field.required}required{/if}>
            <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
            <label id="label-info-{$field.name|escape:'html':'UTF-8'}" for="{$field.name|escape:'html':'UTF-8'}">{$field.label}</label>
        </div>
    {/if}
{/if}

<script type="text/javascript">
    {if $type === 'invoice'}
        function restoreScroll() {
            if ($('#billing-address-form-list').is(':hidden')) {
                $('body').removeClass('showAddressForm');
            }
        }
    {/if}

    {if $type == 'customer' && $field.name == 'birthday'}
        var birthdayField = $("#customer-birthday");
        if (birthdayField.length > 0) {
            birthdayField.focus(function () {
                $(this).parent().addClass("onfocus");
            });
            birthdayField.blur(function () {
                if (!birthdayField.val()) {
                    $(this).parent().removeClass("onfocus");
                }
            });
        }
    {/if}
</script>