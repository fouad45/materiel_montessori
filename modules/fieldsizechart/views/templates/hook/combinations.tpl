{if isset($combinations) and $combinations}
	<div class="available-sizes">
		<strong>{l s='Available sizes' mod='fieldsizechart'}</strong>
		<ul> 
			<li>{foreach $combinations as $k=>$v name=foo}{$v.attribute_name}{if not $smarty.foreach.foo.last}, {/if} {/foreach}</li> 
		</ul>
	</div>
{/if}