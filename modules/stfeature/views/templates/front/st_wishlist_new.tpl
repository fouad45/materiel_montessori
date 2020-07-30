{* 
* @Module Name: ST Feature
* @Website: splashythemes.com.com - prestashop template provider
* @author Splashythemes <splashythemes@gmail.com>
* @copyright  2007-2017 splashythemes
* @description: ST feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
<tr class="new">
	<td>
		<a href="#" class="view-wishlist-product" data-name-wishlist="{$wishlist->name}" data-id-wishlist="{$wishlist->id}">
			<i class="material-icons">&#xE8EF;</i>
			{$wishlist->name}
		</a>
		<div class="st-view-wishlist-product-loading st-view-wishlist-product-loading-{$wishlist->id} cssload-speeding-wheel"></div>
	</td>
	<td class="wishlist-numberproduct wishlist-numberproduct-{$wishlist->id}">0</td>
	<td>0</td>
	<td class="wishlist-datecreate">{$wishlist->date_add}</td>					
	<td><a class="view-wishlist" data-token="{$wishlist->token}" target="_blank" href="{$url_view_wishlist}" title="{l s='View' mod='stfeature'}">{l s='View' mod='stfeature'}</a></td>
	<td>
		<label class="form-check-label">
			<input class="default-wishlist form-check-input" data-id-wishlist="{$wishlist->id}" type="checkbox" {$checked}>
		</label>
	</td>
	<td><a class="delete-wishlist" data-id-wishlist="{$wishlist->id}" href="#" title="{l s='Delete' mod='stfeature'}"><i class="material-icons">&#xE872;</i></a></td>
</tr>

