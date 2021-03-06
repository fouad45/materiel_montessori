{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2018 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 " lang="{$lang_iso|escape:'html':'UTF-8'}"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7" lang="{$lang_iso|escape:'html':'UTF-8'}"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8" lang="{$lang_iso|escape:'html':'UTF-8'}"> <![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9" lang="{$lang_iso|escape:'html':'UTF-8'}"> <![endif]-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$lang_iso|escape:'html':'UTF-8'}">
<head>
	<title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
    {if isset($meta_description) AND $meta_description}
		<meta name="description" content="{$meta_description|escape:'html':'UTF-8'}" />
    {/if}
    {if isset($meta_keywords) AND $meta_keywords}
		<meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}" />
    {/if}
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta http-equiv="content-language" content="{$meta_language|escape:'html':'UTF-8'}" />
	<meta name="generator" content="PrestaShop" />
	<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
	<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url|escape:'url'}?{$img_update_time|escape:html:'UTF-8'}" />
	<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url|escape:'url'}?{$img_update_time|escape:html:'UTF-8'}" />
	<script type="text/javascript">
        var baseDir = '{$content_dir|addslashes|escape:'html':'UTF-8'}';
        var baseUri = '{$base_uri|addslashes|escape:'html':'UTF-8'}';
        var static_token = '{$static_token|addslashes|escape:'html':'UTF-8'}';
        var token = '{$token|addslashes|escape:'html':'UTF-8'}';
        var priceDisplayPrecision = {$priceDisplayPrecision*$currency->decimals|escape:'html':'UTF-8'};
        var priceDisplayMethod = {$priceDisplay|escape:'html':'UTF-8'};
        var roundMode = {$roundMode|escape:'html':'UTF-8'};
	</script>
    {if isset($css_files)}
        {foreach from=$css_files key=css_uri item=media}
			<link href="{$css_uri|escape:'url'}" rel="stylesheet" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
        {/foreach}
    {/if}
    {if isset($js_files)}
        {foreach from=$js_files item=js_uri}
			<script type="text/javascript" src="{$js_uri|escape:'url'}"></script>
        {/foreach}
    {/if}
    {$HOOK_HEADER|escape:'html':'UTF-8'}
</head>

<script type="text/javascript">
// <![CDATA[
var idSelectedCountry = {if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{else}{if isset($address->id_state)}{$address->id_state|intval}{else}false{/if}{/if};
var countries = new Array();
var countriesNeedIDNumber = new Array();
var countriesNeedZipCode = new Array();
{foreach from=$countries item='country'}
	{if isset($country.states) && $country.contains_states}
		countries[{$country.id_country|intval}] = new Array();
		{foreach from=$country.states item='state' name='states'}
			countries[{$country.id_country|intval}].push({ldelim}'id' : '{$state.id_state|intval}', 'name' : '{$state.name|addslashes}'{rdelim});
		{/foreach}
	{/if}
	{if $country.need_identification_number}
		countriesNeedIDNumber.push({$country.id_country|intval});
	{/if}
	{if isset($country.need_zip_code)}
		countriesNeedZipCode[{$country.id_country|intval}] = {$country.need_zip_code|intval};
	{/if}
{/foreach}
$(function(){ldelim}
	$('.id_state option[value={if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{else}{if isset($address->id_state)}{$address->id_state|intval}{/if}{/if}]').attr('selected', true);
{rdelim});
{literal}
	$(document).ready(function() {
		$('#company').on('input',function(){
			vat_number();
		});
		vat_number();
		function vat_number()
		{
			if ($('#company').val() != '')
				$('#vat_number').show();
			else
				$('#vat_number').hide();
		}
	});
{/literal}
//]]>
</script>

<body id="address" class="ui-mobile-viewport ui-overlay-c">
<div data-role="page" class="type-interior prestashop-page ui-page ui-body-c ui-page-active" tabindex="0" style="min-height: 736px;">
	<div data-role="header" id="header" class="ui-body-c ui-header ui-bar-a" role="banner">
        {capture name=path}{l s='Your addresses' mod='jmango360api'}{/capture}
	</div><!-- /header -->
	<div data-role="content" id="content" class="ui-content" role="main">
{*{include file="$tpl_dir./breadcrumb.tpl"}*}

<h1>{l s='Your addresses' mod='jmango360api'}</h1>

<h3>
{if isset($id_address) && (isset($smarty.post.alias) || isset($address->alias))}
	{l s='Modify address' mod='jmango360api'}
	{if isset($smarty.post.alias)}
		"{$smarty.post.alias|escape:'html':'UTF-8'}"
	{else}
		{if isset($address->alias)}"{$address->alias|escape:'html'}"{/if}
	{/if}
{else}
	{l s='To add a new address, please fill out the form below.' mod='jmango360api'}
{/if}
</h3>

{include file="$tpl_dir./errors.tpl"}

<p class="required"><sup>*</sup> {l s='Required field' mod='jmango360api'}</p>

<form action="{$link->getModuleLink("jmango360api", "address", array())|escape:'quotes':'UTF-8'}" method="post" class="std" id="add_address">
	<fieldset>
		<h3>{if isset($id_address)}{l s='Your address' mod='jmango360api'}{else}{l s='New address' mod='jmango360api'}{/if}</h3>
	{assign var="stateExist" value=false}
	{assign var="postCodeExist" value=false}
	{assign var="dniExist" value=false}
	{foreach from=$ordered_adr_fields item=field_name}
		{if $field_name eq 'dni'}
		{assign var="dniExist" value=true}
		<p class="text">
			<label for="dni">{l s='Identification number' mod='jmango360api'}</label>
			<input type="text" class="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni|escape:'html':'UTF-8'}{else}{if isset($address->dni)}{$address->dni|escape:'html'}{/if}{/if}" />
			<span class="form_info">{l s='DNI / NIF / NIE' mod='jmango360api'}</span>
		</p>
		{/if}
		{if $field_name eq 'company'}
		<p class="text">
			<label for="company">{l s='Company' mod='jmango360api'}</label>
			<input type="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company|escape:'html':'UTF-8'}{else}{if isset($address->company)}{$address->company|escape:'html'}{/if}{/if}" />
		</p>
		{/if}
		{if $field_name eq 'vat_number'}
			<div id="vat_area">
				<div id="vat_number">
					<p class="text">
						<label for="vat_number">{l s='VAT number' mod='jmango360api'}</label>
						<input type="text" class="text" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number|escape:'html':'UTF-8'}{else}{if isset($address->vat_number)}{$address->vat_number|escape:'html'}{/if}{/if}" />
					</p>
				</div>
			</div>
		{/if}
		{if $field_name eq 'firstname'}
		<p class="required text">
			<label for="firstname">{l s='First name' mod='jmango360api'} <sup>*</sup></label>
			<input type="text" name="firstname" id="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname|escape:'html':'UTF-8'}{else}{if isset($address->firstname)}{$address->firstname|escape:'html'}{/if}{/if}" />
		</p>
		{/if}
		{if $field_name eq 'lastname'}
		<p class="required text">
			<label for="lastname">{l s='Last name' mod='jmango360api'} <sup>*</sup></label>
			<input type="text" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname|escape:'html':'UTF-8'}{else}{if isset($address->lastname)}{$address->lastname|escape:'html'}{/if}{/if}" />
		</p>
		{/if}
		{if $field_name eq 'address1'}
		<p class="required text">
			<label for="address1">{l s='Address' mod='jmango360api'} <sup>*</sup></label>
			<input type="text" id="address1" name="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1|escape:'html':'UTF-8'}{else}{if isset($address->address1)}{$address->address1|escape:'html'}{/if}{/if}" />
		</p>
		{/if}
		{if $field_name eq 'address2'}
		<p class="required text">
			<label for="address2">{l s='Address (Line 2)' mod='jmango360api'}</label>
			<input type="text" id="address2" name="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2|escape:'html':'UTF-8'}{else}{if isset($address->address2)}{$address->address2|escape:'html'}{/if}{/if}" />
		</p>
		{/if}
		{if $field_name eq 'postcode'}
		{assign var="postCodeExist" value=true}
		<p class="required postcode text">
			<label for="postcode">{l s='Zip / Postal Code' mod='jmango360api'} <sup>*</sup></label>
			<input type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode|escape:'html':'UTF-8'}{else}{if isset($address->postcode)}{$address->postcode|escape:'html'}{/if}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
		</p>
		{/if}
		{if $field_name eq 'city'}
		<p class="required text">
			<label for="city">{l s='City' mod='jmango360api'} <sup>*</sup></label>
			<input type="text" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city|escape:'html':'UTF-8'}{else}{if isset($address->city)}{$address->city|escape:'html'}{/if}{/if}" maxlength="64" />
		</p>
		{*
			if customer hasn't update his layout address, country has to be verified
			but it's deprecated
		*}
		{/if}
		{if $field_name eq 'Country:name' || $field_name eq 'country'}
		<p class="required select">
			<label for="id_country">{l s='Country' mod='jmango360api'} <sup>*</sup></label>
			<select id="id_country" name="id_country">{$countries_list|escape:'html':'UTF-8'}</select>
		</p>
		{if $vatnumber_ajax_call}
		<script type="text/javascript">
		var ajaxurl = '{$ajaxurl|escape:'url'}';
		{literal}
				$(document).ready(function(){
					$('#id_country').change(function() {
						$.ajax({
							type: "GET",
							url: ajaxurl+"vatnumber/ajax.php?id_country="+$('#id_country').val(),
							success: function(isApplicable){
								if(isApplicable == "1")
								{
									$('#vat_area').show();
									$('#vat_number').show();
								}
								else
								{
									$('#vat_area').hide();
								}
							}
						});
					});
				});
		{/literal}
		</script>
		{/if}
		{/if}
		{if $field_name eq 'State:name'}
		{assign var="stateExist" value=true}
		<p class="required id_state select">
			<label for="id_state">{l s='State' mod='jmango360api'} <sup>*</sup></label>
			<select name="id_state" id="id_state">
				<option value="">-</option>
			</select>
		</p>
		{/if}
		{/foreach}
		{if !$postCodeExist}
		<p class="required postcode text hidden">
			<label for="postcode">{l s='Zip / Postal Code' mod='jmango360api'} <sup>*</sup></label>
			<input type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode|escape:'html':'UTF-8'}{else}{if isset($address->postcode)}{$address->postcode|escape:'html'}{/if}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
		</p>
		{/if}		
		{if !$stateExist}
		<p class="required id_state select">
			<label for="id_state">{l s='State' mod='jmango360api'} <sup>*</sup></label>
			<select name="id_state" id="id_state">
				<option value="">-</option>
			</select>
		</p>
		{/if}
		{if !$dniExist}
		<p class="required text dni">
			<label for="dni">{l s='Identification number' mod='jmango360api'} <sup>*</sup></label>
			<input type="text" class="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni|escape:'html':'UTF-8'}{else}{if isset($address->dni)}{$address->dni|escape:'html'}{/if}{/if}" />
			<span class="form_info">{l s='DNI / NIF / NIE' mod='jmango360api'}</span>
		</p>
		{/if}
		<p class="textarea">
			<label for="other">{l s='Additional information' mod='jmango360api'}</label>
			<textarea id="other" name="other" cols="26" rows="3">{if isset($smarty.post.other)}{$smarty.post.other|escape:'html':'UTF-8'}{else}{if isset($address->other)}{$address->other|escape:'html'}{/if}{/if}</textarea>
		</p>
		{if isset($one_phone_at_least) && $one_phone_at_least}
			<p class="inline-infos required">{l s='You must register at least one phone number.' mod='jmango360api'}</p>
		{/if}
		<p class="text">
			<label for="phone">{l s='Home phone' mod='jmango360api'}</label>
			<input type="text" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone|escape:'html':'UTF-8'}{else}{if isset($address->phone)}{$address->phone|escape:'html'}{/if}{/if}" />
		</p>
		<p class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}text">
			<label for="phone_mobile">{l s='Mobile phone' mod='jmango360api'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label>
			<input type="text" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile|escape:'html':'UTF-8'}{else}{if isset($address->phone_mobile)}{$address->phone_mobile|escape:'html'}{/if}{/if}" />
		</p>
		<p class="required text" id="adress_alias">
			<label for="alias">{l s='Please assign an address title for future reference.' mod='jmango360api'} <sup>*</sup></label>
			<input type="text" id="alias" name="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias|escape:'html':'UTF-8'}{else if isset($address->alias)}{$address->alias|escape:'html'}{elseif !$select_address}{l s='My address' mod='jmango360api'}{/if}" />
		</p>
	</fieldset>
	<p class="submit2">
		{if isset($id_address)}<input type="hidden" name="id_address" value="{$id_address|intval}" />{/if}
		{*{if isset($back)}<input type="hidden" name="back" value="{$back}" />{/if}*}
		{if isset($mod)}<input type="hidden" name="mod" value="{$mod|escape:'html':'UTF-8'}" />{/if}
		{if isset($select_address)}<input type="hidden" name="select_address" value="{$select_address|intval}" />{/if}
		<input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
		<input type="submit" name="submitAddress" id="submitAddress" value="{l s='Save' mod='jmango360api'}" class="button" />
	</p>
</form>

	</div>
</div>
</body>