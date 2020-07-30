{*
* 2007-2016 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/form/form.tpl"}


{block name="autoload_tinyMCE"}

				tinySetup({
				editor_selector :"autoload_rte",
				content_css : "{$module_path}css/tinymce.css" 
			});
		{/block}

{block name="input"}


	{if $input.type == 'background_image'}



	<p> <input id="{$input.name}" type="text" name="{$input.name}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}"> </p>
	 <a href="filemanager/dialog.php?type=1&field_id={$input.name}" class="btn btn-default iframe-upload"  data-input-name="{$input.name}" type="button">{l s='Background image selector' mod='fieldsizechart'} <i class="icon-angle-right"></i></a>
	{elseif $input.type == 'table_creator'}	

				
					<div class="form-group">
						
					<div class="col-lg-9"><input type="text" name="nrows" class="nrows" placeholder="{l s='no of rows' mod='fieldsizechart'}"> </div> 
					</div> <div class="form-group">
				 
					<div class="col-lg-9"><input type="text" name="ncol" class="ncol"  placeholder="{l s='no of columns' mod='fieldsizechart'}">  </div></div> 
						<div class="form-group" style="margin-left: 20px;"><div class="col-lg-9">
					<label class="checkbox">  
						<input type="checkbox" name="header_row" value="header_row" class="header_row"> {l s='Header row(it will add extra heading column)' mod='fieldsizechart'}
					</label>   
						<label class="checkbox">  
						<input type="checkbox" name="bordered" value="bordered" class="table_bordered" checked> {l s='Bordered' mod='fieldsizechart'} 
					</label> 
					<label class="checkbox">  
						<input type="checkbox" name="striped" value="striped" class="table_striped"> {l s='Striped' mod='fieldsizechart'}
					</label>  
						</div></div>
					<button type="button" class="btn btn-success" name="Submit" id="table_generator">{l s='Generate table' mod='fieldsizechart'}</button>  

					  <div class="span6" id="tbl_display">  
    </div> 
	
	{elseif $input.type == 'attribute_checboxes'}	
	{if isset($input.options.query)}	
		{foreach $input.options.query as $value}
			<div class="checkbox {if isset($input.class)}{$input.class}{/if}">
											{strip}
											<label>
											<input type="checkbox"	name="{$input.name}[]" id="{$value.id_option}" value="{$value.id_option|escape:'html':'UTF-8'}" {if isset($fields_value[$input.name])}{if in_array($value.id_option, $fields_value[$input.name])}  checked="checked"{/if}{/if} />
												{$value.name}
											</label>
											{/strip}
										</div>
								
									{/foreach}{/if}
									
		{if isset($value.p) && $value.p}<p class="help-block">{$value.p}</p>{/if}
	
	{else}
		{$smarty.block.parent}
    {/if}
{/block}



