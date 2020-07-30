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
<div id="fieldsizechart-show" class="buttons_bottom_block additional_button">{l s='Size Chart' mod='fieldsizechart'}</div>
<div id="fieldsizechart">
<div class="fieldsizechart_content">
	<span class="cross" title="{l s='Close window' mod='fieldsizechart'}">x</span>
	<div class="fieldsizechart-content">
		<span class="page-heading">{l s='Size Chart' mod='fieldsizechart'}</span>
                 {if $sh_measure}   
		<ul class="nav nav-tabs">
			{if isset($guide->description) && isset($guide->description) !=''}
			<li class="active"><a href="#fieldsizechart-guide"  title="{$guide->title} " data-toggle="tab">{$guide->title}</a></li>
			{if $sh_global}<li><a href="#fieldsizechart-global"  title="{l s='Guide' mod='fieldsizechart'}" data-toggle="tab">{l s='Guide' mod='fieldsizechart'}</a></li>{/if}
			{else}
			{if $sh_global}<li class="active"><a href="#fieldsizechart-global"  title="{l s='Guide' mod='fieldsizechart'}" data-toggle="tab">{l s='Guide' mod='fieldsizechart'}</a></li>{/if}
			{/if}
			{if $sh_measure}<li><a href="#fieldsizechart-how"  title="{l s='How to measure' mod='fieldsizechart'}" data-toggle="tab">{l s='How to measure' mod='fieldsizechart'}</a></li>{/if}
		</ul>
                {/if}
		<div class="tab-content">
			

			{if isset($guide->description) && isset($guide->description) !=''}
			
			<div id="fieldsizechart-guide"  class="tab-pane rte fade active in">
				{$guide->description nofilter} 
			</div>

			{if $sh_global}
			<div id="fieldsizechart-global"  class="tab-pane rte fade">
				{$global|stripslashes nofilter}
			</div>

			{/if}
			{else}
			{if $sh_global}
			<div id="fieldsizechart-global"  class="tab-pane rte fade active in">
				{$global|stripslashes nofilter}
			</div>
			{/if}
			{/if}

			{if $sh_measure}
			<div id="fieldsizechart-how"  class="tab-pane rte fade">
				{$howto|stripslashes nofilter}
			</div>
			{/if}

		</div>

	</div>
</div>
</div> <!-- #layer_cart -->
<div id="fieldsizechart-overlay"></div>
