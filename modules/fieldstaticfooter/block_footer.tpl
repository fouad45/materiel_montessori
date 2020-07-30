{foreach from=$staticblocks key=key item=block}
		{if $block.insert_module == 1}
        {assign var="name" value='insert_module_'|cat:$block.name_module}
        	{if $block.description|strpos:$name !== false}
            	{$block.description|replace:$name:{$block.block_module nofilter} nofilter}
            {else}
            {$block.description nofilter}
            {$block.block_module nofilter}
        	{/if}
        {else}
        	  {$block.description nofilter}
        {/if}
{/foreach}

    
  