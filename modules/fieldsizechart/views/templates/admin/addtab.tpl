
<div id="ModuleIqitsizeguide" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="ModuleIqitsizeguide" />
	<h3>{l s='Add or modify customizable properties' mod='fieldsizechart'}</h3>

	{if isset($display_common_field) && $display_common_field}
		<div class="alert alert-info">{l s='Warning, if you change the value of fields with an orange bullet %s, the value will be changed for all other shops for this product'  mod='fieldsizechart' sprintf=$bullet_common_field}</div>
	{/if}
<div class="form-group">		
		<label class="control-label col-lg-3">
			{include file="controllers/products/multishop/checkbox.tpl" field="active" type="radio" onclick=""}
		</label>
	</div>

<script type="text/javascript">
{literal}
$(document).ready(function(){
$('select#id_fieldsizechart').chosen({width: '250px'});
});
{/literal}
</script>
<div class="form-group">
		<label class="control-label col-lg-3" for="id_fieldsizechart">{l s='Select from created guides' mod='fieldsizechart'}</label>
		<div class="col-lg-3">
			<select name="id_fieldsizechart" id="id_fieldsizechart" class="">
				<option value="0">- {l s='Choose (optional)' mod='fieldsizechart'} -</option>
				{if isset($guides)}

				{foreach from=$guides item=guide}
				<option value="{$guide.id_guide}" {if isset($selectedGuide) && ($guide.id_guide == $selectedGuide)}selected="selected"{/if}>{$guide.title}</option>
				{/foreach}
				{/if}
			</select>
		</div>
		<div class="col-lg-4">
			<a class="btn btn-link bt-icon confirm_leave" style="margin-bottom:0" href="{$link->getAdminLink('AdminModules')}&configure=fieldsizechart&addGuide=1">
				<i class="icon-plus-sign"></i> {l s='Create new guide' mod='fieldsizechart'} <i class="icon-external-link-sign"></i>
			</a>
		</div>
	</div>

<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay'}</button>
	</div>
</div>
