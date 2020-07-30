{* 
* @Module Name: ST Feature
* @Website: splashythemes.com.com - prestashop template provider
* @author Splashythemes <splashythemes@gmail.com>
* @copyright  2007-2017 splashythemes
* @description: ST feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
<div class="compare">
	<a class="st-compare-button btn-product btn{if $added} added{/if}" href="#" data-id-product="{$leo_compare_id_product}" title="{if $added}{l s='Remove from Compare' mod='stfeature'}{else}{l s='Add to Compare' mod='stfeature'}{/if}">
		<span class="st-compare-bt-content">
			<i class="fa fa-area-chart"></i>
			{l s='Add to Compare' mod='stfeature'}
		</span>
	</a>
</div>