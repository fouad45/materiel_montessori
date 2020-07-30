<option value="{$node.id}" {if $current_category == $node.id || $node.id==$category_filter}selected="selected"{/if}>
    {$node.prefix} {$node.name|escape:'html':'UTF-8'}
</option>
{if $node.children|@count > 0}
    {foreach from=$node.children item=child name=fieldblocksearchTreeBranch}
        {if $smarty.foreach.fieldblocksearchTreeBranch.last}
            {include file="$fieldbranche_tpl_path" node=$child last='true'}
        {else}
            {include file="$fieldbranche_tpl_path" node=$child last='false'}
        {/if}
    {/foreach}
{/if}
