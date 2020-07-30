{* 
* @Module Name: ST Feature
* @Website: splashythemes.com.com - prestashop template provider
* @author Splashythemes <splashythemes@gmail.com>
* @copyright  2007-2017 splashythemes
* @description: ST feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
{extends file=$layout}

{block name='content'}
	<section id="main">
		<div id="mywishlist">
			<div class="account-page-title">
				<h1 class="h1">{l s='New wishlist' mod='stfeature'}</h1>
			</div>
			<div class="new-wishlist">
				<div class="form-group">
					<label for="wishlist_name">{l s='Name' mod='stfeature'}</label>
					<input type="text" class="form-control" id="wishlist_name" placeholder="{l s='Enter name of new wishlist' mod='stfeature'}">
				</div>
				<div class="form-group has-success">
					<div class="form-control-feedback"></div>			 
				</div>
				<div class="form-group has-danger">		 
				  <div class="form-control-feedback"></div>		 
				</div>
				<button type="submit" class="btn btn-primary st-save-wishlist-bt">
					<span class="st-save-wishlist-loading cssload-speeding-wheel"></span>
					<span class="st-save-wishlist-bt-text">
						{l s='Save' mod='stfeature'}
					</span>
				</button>
			</div>
			
				<div class="list-wishlist">
					<table class="table table-striped">
					  <thead class="wishlist-table-head">
						<tr>
						  <th>{l s='Name' mod='stfeature'}</th>
						  <th>{l s='Quantity' mod='stfeature'}</th>
						  <th>{l s='Viewed' mod='stfeature'}</th>
						  <th class="wishlist-datecreate-head">{l s='Created' mod='stfeature'}</th>
						  <th>{l s='Direct Link' mod='stfeature'}</th>
						  <th>{l s='Default' mod='stfeature'}</th>
						  <th>{l s='Delete' mod='stfeature'}</th>
						</tr>
					  </thead>
					  <tbody>
						{if $wishlists}
							{foreach from=$wishlists item=wishlists_item name=for_wishlists}
								<tr>					 
									<td><a href="#" class="view-wishlist-product" data-name-wishlist="{$wishlists_item.name}" data-id-wishlist="{$wishlists_item.id_wishlist}"><i class="material-icons">&#xE8EF;</i>{$wishlists_item.name}</a><div class="st-view-wishlist-product-loading st-view-wishlist-product-loading-{$wishlists_item.id_wishlist} cssload-speeding-wheel"></div></td>
									<td class="wishlist-numberproduct wishlist-numberproduct-{$wishlists_item.id_wishlist}">{$wishlists_item.number_product|intval}</td>
									<td>{$wishlists_item.counter|intval}</td>
									<td class="wishlist-datecreate">{$wishlists_item.date_add}</td>							
									<td><a class="view-wishlist" data-token="{$wishlists_item.token}" target="_blank" href="{$view_wishlist_url}{if $leo_is_rewrite_active}?{else}&{/if}token={$wishlists_item.token}" title="{l s='View' mod='stfeature'}">{l s='View' mod='stfeature'}</a></td>
									<td>
										
											<label class="form-check-label">
												<input class="default-wishlist form-check-input" data-id-wishlist="{$wishlists_item.id_wishlist}" type="checkbox" {if $wishlists_item.default == 1}checked="checked"{/if}>
											</label>
									
									</td>
									<td><a class="delete-wishlist" data-id-wishlist="{$wishlists_item.id_wishlist}" href="#" title="{l s='Delete' mod='stfeature'}"><i class="material-icons">&#xE872;</i></a></td>
								</tr>
							{/foreach}
						{/if}
					  </tbody>
					</table>
				</div>
			<div class="send-wishlist">
				<a class="st-send-wishlist-button btn btn-info" href="#" title="{l s='Send this wishlist' mod='stfeature'}">
					<i class="material-icons">&#xE163;</i>
					{l s='Send this wishlist' mod='stfeature'}
				</a>
			</div>
			<section id="products">
				<div class="st-wishlist-product products row">
				
				</div>
			</section>
			<footer class="page-footer">
				<a class="account-link" href="{$link->getPageLink('my-account', true)|escape:'html'}"><i class="material-icons">&#xE314;</i>{l s='Back to Your Account' mod='stfeature'}</a>
				<a class="account-link" href="{$urls.base_url}"><i class="material-icons">&#xE88A;</i>{l s='Home' mod='stfeature'}</a>
			</ul>
		</div>
	</section>
{/block}

