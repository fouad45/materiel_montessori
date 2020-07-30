{* 
* @Module Name: ST Feature
* @Website: splashythemes.com.com - prestashop template provider
* @author Splashythemes <splashythemes@gmail.com>
* @copyright  2007-2017 splashythemes
* @description: ST feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
<div class="wishlist">
	{if isset($wishlists) && count($wishlists) > 1}
		<div class="dropdown st-wishlist-button-dropdown">
		  <button class="st-wishlist-button dropdown-toggle show-list btn-product btn{if $added_wishlist} added{/if}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id-wishlist="{$id_wishlist}" data-id-product="{$leo_wishlist_id_product}" data-id-product-attribute="{$leo_wishlist_id_product_attribute}">
			<span class="st-wishlist-bt-content">
				<i class="material-icons">&#xE87D;</i>
				<span>{l s='Add to Wishlist' mod='stfeature'}</span>
			</span>
			
		  </button>
		  <div class="dropdown-menu leo-list-wishlist leo-list-wishlist-{$leo_wishlist_id_product}">
			{foreach from=$wishlists item=wishlists_item}
				<a href="#" class="dropdown-item list-group-item list-group-item-action wishlist-item{if in_array($wishlists_item.id_wishlist, $wishlists_added)} added {/if}" data-id-wishlist="{$wishlists_item.id_wishlist}" data-id-product="{$leo_wishlist_id_product}" data-id-product-attribute="{$leo_wishlist_id_product_attribute}" title="{if in_array($wishlists_item.id_wishlist, $wishlists_added)}{l s='Remove from Wishlist' mod='stfeature'}{else}{l s='Add to Wishlist' mod='stfeature'}{/if}">{$wishlists_item.name}</a>			
			{/foreach}
		  </div>
		</div>
	{else}
		<a class="st-wishlist-button btn-product btn{if $added_wishlist} added{/if}" href="#" data-id-wishlist="{$id_wishlist}" data-id-product="{$leo_wishlist_id_product}" data-id-product-attribute="{$leo_wishlist_id_product_attribute}" title="{if $added_wishlist}{l s='Remove from Wishlist' mod='stfeature'}{else}{l s='Add to Wishlist' mod='stfeature'}{/if}">
			<span class="st-wishlist-bt-content">
				<i class="fa fa-heart" aria-hidden="true"></i>
				{l s='Add to Wishlist' mod='stfeature'}
			</span>
		</a>
	{/if}
</div>