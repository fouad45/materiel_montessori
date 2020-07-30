  <div id="cart_block_top" class="sticky_top">
  <div class="blockcart cart-preview {if $cart.products_count > 0}active{else}inactive{/if}" data-refresh-url="{$refresh_url}">
        <div class="click-cart">
        <span class="unline_cart">
            
			<span class="cart-products-item">
				<span class="cart-products-count">{$cart.products_count}</span>
				<span class="cart-item-top">{if $cart.products_count > 1}{l s='Items'} -{else}{l s='Item'} -{/if}</span>
				<span class="cart_item_top">{$cart.subtotals.products.value}</span>
			</span>
         </span>
        <span class="shopping-cart">
        <span class="fa fa-shopping-cart">
        </span>
        
        </span>
        </div>
<div class="cart_top_ajax">
{if $cart.products}
<div class="box-item">
  {foreach from=$cart.products item="product"}
	<div class="cart-item-product">
          <a class="cart-item-product-left" href="{$product.url}">
          	<img class="img-responsive" src="{$product.cover.bySize.cart_default.url}" alt="{$product.name|escape:'quotes'}" 
            {if isset($size_cart_default.width)}width="{$size_cart_default.width}"{/if}
           {if isset($size_cart_default.height)}height="{$size_cart_default.height}"{/if} 
            >
          </a>
          <div class="cart-item-product-right">
          	  <div class="product-name-ajax">
              	<span class="qtt-ajax">{$product.quantity} x </span><a href="{$product.url}">{$product.name}</a>
              </div>
              <div class="product-line-info-top">
                  <span class="value price">{$product.price}</span>
                  {if $product.unit_price_full}
                    <div class="unit-price-cart">{$product.unit_price_full}</div>
                  {/if}
               </div>
              {foreach from=$product.attributes key="attribute" item="value"}
              <div class="product-line-info-top">
                <span class="label-top">{$attribute}:</span>
                <span class="value-top">{$value}</span>
              </div>
              {/foreach}
          </div>
          <a
                  class                       = "remove-from-cart"
                  rel                         = "nofollow"
                  href                        = "{$product.remove_from_cart_url}"
                  data-link-action            = "delete-from-cart"
                  data-id-product             = "{$product.id_product|escape:'javascript'}"
                  data-id-product-attribute   = "{$product.id_product_attribute|escape:'javascript'}"
                  data-id-customization   	  = "{$product.id_customization|escape:'javascript'}">
            {if !isset($product.is_gift) || !$product.is_gift}
            <i class="fa fa-times-circle"></i>
            {/if}
            </a>
    </div>
   {/foreach} 
   </div>
   <div class="card-block-top">
       <div class="totals-top">
           <span class="label-top">{$cart.summary_string}</span>
           <span class="value-top price">{$cart.subtotals.products.value}</span>
       </div>
       {if $cart.subtotals.shipping.type === 'shipping'}
           <div class="totals-top">
               <span class="label-top">{$cart.subtotals.shipping.label}</span>
               <span class="value-top price">{$cart.subtotals.shipping.value}</span>
           </div>
       {/if}
   </div>
    {else}
      <div class="card-block-top" style="border-top:none">
      {l s='There are no more items in your cart' d='Shop.Theme.Checkout'}
      </div>
    {/if}
  <div class="card-block-top">
    <div class="totals-top">
      <span class="label-top">{$cart.totals.total.label} {$cart.labels.tax_short}</span>
      <span class="value-top price">{$cart.totals.total.value}</span>
    </div>
    <div class="totals-top">
      <span class="label-top">{$cart.subtotals.tax.label}</span>
      <span class="value-top price">{$cart.subtotals.tax.value}</span>
    </div>
  </div>
  <div class="card-block-top">
  <a href="{$cart_url}" class="view-cart">{l s='View Cart' d='Shop.Theme.Actions'}</a>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function () {
	$(".click-cart").click(function(e) {
        $(".cart_top_ajax").slideToggle();
		$(".click-cart").toggleClass('active');
		
		e.stopPropagation();
    });
	$("body").click(function(e) {
         $(".cart_top_ajax").slideUp();
		 $(".click-cart").removeClass('active');
    });
});
</script>
</div>
</div>

