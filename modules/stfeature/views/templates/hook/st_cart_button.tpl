{* 
* @Module Name: ST Feature
* @Website: splashythemes.com.com - prestashop template provider
* @author Splashythemes <splashythemes@gmail.com>
* @copyright  2007-2017 splashythemes
* @description: ST feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}

<div class="button-container cart">
	<form action="{$link_cart}" method="post">
		<input type="hidden" name="token" value="{$static_token}">
		<input type="hidden" value="{$leo_cart_product.quantity}" class="quantity_product quantity_product_{$leo_cart_product.id_product}" name="quantity_product">
		<input type="hidden" value="{if isset($leo_cart_product.product_attribute_minimal_quantity) && $leo_cart_product.product_attribute_minimal_quantity>$leo_cart_product.minimal_quantity}{$leo_cart_product.product_attribute_minimal_quantity}{else}{$leo_cart_product.minimal_quantity}{/if}" class="minimal_quantity minimal_quantity_{$leo_cart_product.id_product}" name="minimal_quantity">
		<input type="hidden" value="{$leo_cart_product.id_product_attribute}" class="id_product_attribute id_product_attribute_{$leo_cart_product.id_product}" name="id_product_attribute">
		<input type="hidden" value="{$leo_cart_product.id_product}" class="id_product" name="id_product">
		<input type="hidden" name="id_customization" value="{if $leo_cart_product.id_customization}{$leo_cart_product.id_customization}{/if}" class="product_customization_id">
		
		{if isset($leo_cart_product.combinations) && count($leo_cart_product.combinations) > 0}		
			<div class="dropdown leo-pro-attr-section">
			  <button class="btn btn-secondary dropdown-toggle leo-bt-select-attr dropdownListAttrButton_{$leo_cart_product.id_product}" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				{$leo_cart_product.attribute_designation}
			  </button>
			  <div class="dropdown-menu leo-dropdown-attr">
				{foreach from=$leo_cart_product.combinations item=attribute}
					<a class="dropdown-item leo-select-attr{if $attribute.id_product_attribute == $leo_cart_product.id_product_attribute} selected{/if}{if $attribute.add_to_cart_url == ''} disable{/if}" href="#" data-id-product="{$attribute.id_product}" data-id-attr="{$attribute.id_product_attribute}" data-qty-attr="{$attribute.quantity}" data-min-qty-attr="{$attribute.minimal_quantity}">{$attribute.attribute_designation}</a>
				{/foreach}
			  </div>
			</div>
		{/if}
		<input type="{if $show_input_quantity}number{else}hidden{/if}" class="input-group form-control qty qty_product qty_product_{$leo_cart_product.id_product}" name="qty" value="{if isset($leo_cart_product.wishlist_quantity)}{$leo_cart_product.wishlist_quantity}{else}{if $leo_cart_product.product_attribute_minimal_quantity && $leo_cart_product.product_attribute_minimal_quantity>$leo_cart_product.minimal_quantity}{$leo_cart_product.product_attribute_minimal_quantity}{else}{$leo_cart_product.minimal_quantity}{/if}{/if}" data-min="{if $leo_cart_product.product_attribute_minimal_quantity && $leo_cart_product.product_attribute_minimal_quantity>$leo_cart_product.minimal_quantity}{$leo_cart_product.product_attribute_minimal_quantity}{else}{$leo_cart_product.minimal_quantity}{/if}">
		  <button class="btn btn-primary btn-product add-to-cart st-bt-cart leo-bt-cart_{$leo_cart_product.id_product}{if !$leo_cart_product.add_to_cart_url} disabled{/if}" data-button-action="add-to-cart" type="submit">
			<span class="st-bt-cart-content">
				<i class="fa fa-shopping-basket shopping-cart"></i>
				{l s='Add to cart' mod='stfeature'}
			</span>
		  </button>
	</form>
</div>

