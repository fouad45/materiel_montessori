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
{if $field.type == 'hidden'}

    {block name='form_field_item_hidden'}
        <input type="hidden" name="{$field.name|escape:'html':'UTF-8'}" value="{$field.value|escape:'html':'UTF-8'}">
    {/block}

{else}
    {if $type === 'delivery'}
        <div class="field {if !empty($field.errors)}has-error{/if}">
            {if $field.type === 'password'}

            {else}
                <label class="{if $field.required} required{/if}">
                    {if $field.type !== 'checkbox'}
                        {$field.label|escape:'html':'UTF-8'}
                    {/if}
                </label>
            {/if}
            <div class="{if ($field.type === 'radio-buttons')} form-control-valign{/if}">

                {if $field.type === 'select'}

                    {block name='form_field_item_select'}
                        <select class="form-control" name="{$field.name|escape:'html':'UTF-8'}" id="shipping:{$field.name|escape:'html':'UTF-8'}"
                                {if $field.required}required{/if}>
                            <option value disabled selected>{l s='-- please choose --' d='Shop.Forms.Labels'}</option>
                            {foreach from=$field.availableValues item="label" key="value"}
                                <option value="{$value|escape:'html':'UTF-8'}" {if $value eq $field.value} selected {/if}>{$label|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    {/block}

                {elseif $field.type === 'countrySelect'}

                    {block name='form_field_item_country'}
                        <select class="form-control js-country"
                                name="{$field.name|escape:'html':'UTF-8'}" id="shipping:{$field.name|escape:'html':'UTF-8'}"
                                {if $field.required}required{/if}>
                            <option value disabled selected>{l s='-- please choose --' d='Shop.Forms.Labels'}</option>
                            {foreach from=$field.availableValues item="label" key="value"}
                                <option value="{$value|escape:'html':'UTF-8'}" {if $value eq $field.value} selected {/if}>{$label|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    {/block}

                {elseif $field.type === 'radio-buttons'}

                    {block name='form_field_item_radio'}
                        {foreach from=$field.availableValues item="label" key="value"}
                            <label class="radio-inline">
                                <span class="custom-radio">
                                    <input name="{$field.name|escape:'html':'UTF-8'}" id="shipping:{$field.name|escape:'html':'UTF-8'}"
                                           type="radio"
                                           value="{$value|escape:'html':'UTF-8'}"
                                           {if $field.required}required{/if}
                                            {if $value eq $field.value} checked {/if} >
                                    <span></span>
                                </span>
                                {$label|escape:'html':'UTF-8'}
                            </label>
                        {/foreach}
                    {/block}

                {elseif $field.type === 'checkbox'}

                    {block name='form_field_item_checkbox'}
                        <span class="custom-checkbox">
                            <input name="{$field.name|escape:'html':'UTF-8'}" id="shipping:{$field.name|escape:'html':'UTF-8'}" type="checkbox" value="1"
                                   {if $field.value}checked="checked"{/if} {if $field.required}required{/if}>
                            <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
                            <label>{$field.label|escape:'html':'UTF-8'}</label>
                        </span>
                    {/block}

                {elseif $field.type === 'date'}

                    {block name='form_field_item_date'}
                        <input class="form-control" type="date" value="{$field.value|escape:'html':'UTF-8'}"
                               name="{$field.name|escape:'html':'UTF-8'}" id="shipping:{$field.name|escape:'html':'UTF-8'}"
                               placeholder="{if isset($field.availableValues.placeholder)}{$field.availableValues.placeholder|escape:'html':'UTF-8'}{/if}">
                        {if isset($field.availableValues.comment)}
                            <span class="form-control-comment">
                                {$field.availableValues.comment|escape:'html':'UTF-8'}
                            </span>
                        {/if}
                    {/block}

                {elseif $field.type === 'birthday'}

                    {block name='form_field_item_birthday'}
                        <div class="js-parent-focus" name="{$field.name|escape:'html':'UTF-8'}" id="shipping:{$field.name|escape:'html':'UTF-8'}">
                            {html_select_date
                            field_order=DMY
                            time={$field.value|escape:'html':'UTF-8'}
                            field_array={$field.name|escape:'html':'UTF-8'}
                            prefix=false
                            reverse_years=true
                            field_separator='<br>'
                            day_extra='class="form-control form-control-select"'
                            month_extra='class="form-control form-control-select"'
                            year_extra='class="form-control form-control-select"'
                            day_empty={l s='-- day --' mod='jmango360api'}
                            month_empty={l s='-- month --' mod='jmango360api'}
                            year_empty={l s='-- year --' mod='jmango360api'}
                            start_year={'Y'|date}-100 end_year={'Y'|date}
                            }
                        </div>
                    {/block}

                {else}

                    {block name='form_field_item_other'}
                        {if $field.name == 'firstname'}
                            <input
                                    class="form-control {if $field.required}required{/if}"
                                    data-validation="{if $field.required}required{/if} {if $field.name === 'email'}email{/if}"
                                    name="{$field.name|escape:'html':'UTF-8'}"
                                    id="shipping:{$field.name|escape:'html':'UTF-8'}"
                                    type="{$field.type|escape:'html':'UTF-8'}"
                                    value="{if $customer}{$customer->firstname|escape:'htmlall':'UTF-8'}{/if}"
                                    {if isset($field.availableValues.placeholder)}placeholder="{$field.availableValues.placeholder|escape:'html':'UTF-8'}"{/if}
                                    {if $field.maxLength}maxlength="{$field.maxLength|escape:'html':'UTF-8'}"{/if}
                                    {if $field.required}required{/if} >
                        {elseif $field.name == 'lastname'}
                            <input
                                    class="form-control {if $field.required}required{/if}"
                                    data-validation="{if $field.required}required{/if} {if $field.name === 'email'}email{/if}"
                                    name="{$field.name|escape:'html':'UTF-8'}"
                                    id="shipping:{$field.name|escape:'html':'UTF-8'}"
                                    type="{$field.type|escape:'html':'UTF-8'}"
                                    value="{if $customer}{$customer->lastname|escape:'htmlall':'UTF-8'}{/if}"
                                    {if isset($field.availableValues.placeholder)}placeholder="{$field.availableValues.placeholder|escape:'html':'UTF-8'}"{/if}
                                    {if $field.maxLength}maxlength="{$field.maxLength|escape:'html':'UTF-8'}"{/if}
                                    {if $field.required}required{/if} >
                        {else}
                            <input
                                    class="form-control {if $field.required}required{/if}"
                                    data-validation="{if $field.required}required{/if} {if $field.name === 'email'}email{/if}"
                                    name="{$field.name|escape:'html':'UTF-8'}"
                                    id="shipping:{$field.name|escape:'html':'UTF-8'}"
                                    type="{$field.type|escape:'html':'UTF-8'}"
                                    value="{$field.value|escape:'html':'UTF-8'}"
                                    {if isset($field.availableValues.placeholder)}placeholder="{$field.availableValues.placeholder|escape:'html':'UTF-8'}"{/if}
                                    {if $field.maxLength}maxlength="{$field.maxLength|escape:'html':'UTF-8'}"{/if}
                                    {if $field.required}required{/if} >
                        {/if}
                        {if isset($field.availableValues.comment)}
                            <span class="form-control-comment">
                                {$field.availableValues.comment|escape:'html':'UTF-8'}
                            </span>
                        {/if}
                    {/block}

                {/if}

                {block name='form_field_errors'}
                    {include file='_partials/form-errors.tpl' errors=$field.errors}
                {/block}
            </div>

            <div class="form-control-comment">
                {block name='form_field_comment'}
                    {if (!$field.required && !in_array($field.type, ['radio-buttons', 'checkbox']))}
                        {l s='Optional' d='Shop.Forms.Labels'}
                    {/if}
                {/block}
            </div>
        </div>
    {else}
        <div class="field {if !empty($field.errors)}has-error{/if}">
            {if $field.type === 'password'}

            {else}
                <label class="{if $field.required} required{/if}">
                    {if $field.type !== 'checkbox'}
                        {$field.label|escape:'html':'UTF-8'}
                    {/if}
                </label>
            {/if}
            <div class="{if ($field.type === 'radio-buttons')} form-control-valign{/if}">

                {if $field.type === 'select'}

                    {block name='form_field_item_select'}
                        <select class="form-control" name="{$field.name|escape:'html':'UTF-8'}" {if $field.required}required{/if}>
                            <option value disabled selected>{l s='-- please choose --' d='Shop.Forms.Labels'}</option>
                            {foreach from=$field.availableValues item="label" key="value"}
                                <option value="{$value|escape:'html':'UTF-8'}" {if $value eq $field.value} selected {/if}>{$label|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    {/block}

                {elseif $field.type === 'countrySelect'}

                    {block name='form_field_item_country'}
                        <select class="form-control js-country" name="{$field.name|escape:'html':'UTF-8'}" {if $field.required}required{/if} >
                            <option value disabled selected>{l s='-- please choose --' d='Shop.Forms.Labels'}</option>
                            {foreach from=$field.availableValues item="label" key="value"}
                                <option value="{$value|escape:'html':'UTF-8'}" {if $value eq $field.value} selected {/if}>{$label|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    {/block}

                {elseif $field.type === 'radio-buttons'}

                    {block name='form_field_item_radio'}
                        {foreach from=$field.availableValues item="label" key="value"}
                            <label class="radio-inline">
                              <span class="custom-radio">
                                <input name="{$field.name|escape:'html':'UTF-8'}"
                                       type="radio"
                                       value="{$value|escape:'html':'UTF-8'}"
                                       {if $field.required}required{/if}
                                        {if $value eq $field.value} checked {/if} >
                                <span></span>
                                </span>
                                {$label|escape:'html':'UTF-8'}
                            </label>
                        {/foreach}
                    {/block}

                {elseif $field.type === 'checkbox'}

                    {block name='form_field_item_checkbox'}
                        <span class="custom-checkbox">
                            <input name="{$field.name|escape:'html':'UTF-8'}" type="checkbox" value="1"
                                   data-validation="{if $field.required}required{/if}"
                                   {if $field.value}checked="checked"{/if}
                                    {if $field.required}required{/if}>
                            <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
                            <label id="label-info-{$field.name|escape:'html':'UTF-8'}">{$field.label}</label>
                        </span>
                    {/block}

                {elseif $field.type === 'date'}

                    {block name='form_field_item_date'}
                        <input class="form-control" type="date" value="{$field.value|escape:'html':'UTF-8'}"
                               placeholder="{if isset($field.availableValues.placeholder)}{$field.availableValues.placeholder|escape:'html':'UTF-8'}{/if}">
                        {if isset($field.availableValues.comment)}
                            <span class="form-control-comment">
                              {$field.availableValues.comment|escape:'html':'UTF-8'}
                            </span>
                        {/if}
                    {/block}

                {elseif $field.type === 'birthday'}

                    {block name='form_field_item_birthday'}
                        <div class="js-parent-focus">
                            {html_select_date
                            field_order=DMY
                            time={$field.value|escape:'html':'UTF-8'}
                            field_array={$field.name|escape:'html':'UTF-8'}
                            prefix=false
                            reverse_years=true
                            field_separator='<br>'
                            day_extra='class="form-control form-control-select"'
                            month_extra='class="form-control form-control-select"'
                            year_extra='class="form-control form-control-select"'
                            day_empty={l s='-- day --' mod='jmango360api'}
                            month_empty={l s='-- month --' mod='jmango360api'}
                            year_empty={l s='-- year --' mod='jmango360api'}
                            start_year={'Y'|date}-100 end_year={'Y'|date}
                            }
                        </div>
                    {/block}

                {elseif $field.type === 'password'}

                    {*{block name='form_field_item_password'}*}
                    {*<div class="input-group js-parent-focus">*}
                    {*<input*}
                    {*class="form-control js-child-focus js-visible-password"*}
                    {*name="{$field.name}"*}
                    {*type="password"*}
                    {*value=""*}
                    {*pattern=".{literal}{{/literal}5,{literal}}{/literal}"*}
                    {*{if $field.required}required{/if}*}
                    {*>*}
                    {*</div>*}
                    {*{/block}*}

                {else}

                    {block name='form_field_item_other'}
                        {if $field.name == 'firstname'}
                            <input
                                    class="form-control {if $field.required}required{/if}"
                                    data-validation="{if $field.required}required{/if} {if $field.name === 'email'}email{/if}"
                                    name="{$field.name|escape:'html':'UTF-8'}"
                                    id="shipping:{$field.name|escape:'html':'UTF-8'}"
                                    type="{$field.type|escape:'html':'UTF-8'}"
                                    value="{if $customer}{$customer->firstname|escape:'htmlall':'UTF-8'}{/if}"
                                    {if isset($field.availableValues.placeholder)}placeholder="{$field.availableValues.placeholder|escape:'html':'UTF-8'}"{/if}
                                    {if $field.maxLength}maxlength="{$field.maxLength|escape:'html':'UTF-8'}"{/if}
                                    {if $field.required}required{/if} >
                        {elseif $field.name == 'lastname'}
                            <input
                                    class="form-control {if $field.required}required{/if}"
                                    data-validation="{if $field.required}required{/if} {if $field.name === 'email'}email{/if}"
                                    name="{$field.name|escape:'html':'UTF-8'}"
                                    id="shipping:{$field.name|escape:'html':'UTF-8'}"
                                    type="{$field.type|escape:'html':'UTF-8'}"
                                    value="{if $customer}{$customer->lastname|escape:'htmlall':'UTF-8'}{/if}"
                                    {if isset($field.availableValues.placeholder)}placeholder="{$field.availableValues.placeholder|escape:'html':'UTF-8'}"{/if}
                                    {if $field.maxLength}maxlength="{$field.maxLength|escape:'html':'UTF-8'}"{/if}
                                    {if $field.required}required{/if} >
                        {else}
                            <input
                                    class="form-control {if $field.required}required{/if}"
                                    data-validation="{if $field.required}required{/if} {if $field.name === 'email'}email{/if}"
                                    name="{$field.name|escape:'html':'UTF-8'}"
                                    id="shipping:{$field.name|escape:'html':'UTF-8'}"
                                    type="{$field.type|escape:'html':'UTF-8'}"
                                    value="{$field.value|escape:'html':'UTF-8'}"
                                    {if isset($field.availableValues.placeholder)}placeholder="{$field.availableValues.placeholder|escape:'html':'UTF-8'}"{/if}
                                    {if $field.maxLength}maxlength="{$field.maxLength|escape:'html':'UTF-8'}"{/if}
                                    {if $field.required}required{/if} >
                        {/if}
                        {if isset($field.availableValues.comment)}
                            <span class="form-control-comment">
                              {$field.availableValues.comment|escape:'html':'UTF-8'}
                            </span>
                        {/if}
                    {/block}

                {/if}

                {block name='form_field_errors'}
                    {include file='_partials/form-errors.tpl' errors=$field.errors}
                {/block}

            </div>

            <div class="form-control-comment">
                {block name='form_field_comment'}
                    {if (!$field.required && !in_array($field.type, ['radio-buttons', 'checkbox']))}
                        {l s='Optional' d='Shop.Forms.Labels'}
                    {/if}
                {/block}
            </div>
        </div>
    {/if}
{/if}
