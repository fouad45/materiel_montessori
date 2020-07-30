{**
* @license Created by JMango
*}

{extends file="$template_dir/onepage16_new/steps/customer-address-form.tpl"}

{block name='form_field'}
  {*{if $field.name eq "alias"}*}
    {* we don't ask for alias here *}
  {*{else}*}
    {$smarty.block.parent}
  {*{/if}*}
{/block}

{block name='form_fields' append}
  <input type="hidden" name="saveAddress" value="{$type}">
{/block}