{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div id="js-product-list">
  <div class="products horizontal_mode">
      <div id="box-product-grid">
       <div class="row">
            {foreach from=$listing.products item="product"}
              {block name='product_miniature'}
                {include file='catalog/_partials/miniatures/product.tpl' product=$product}
              {/block}
            {/foreach}
       </div>
      </div>
      <div id="box-product-list">
                {foreach from=$listing.products item="product"}
                	<div class="item-product-list">
<div class="js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
                    <div class="left-product">
                        <a href="{$product.url}" class="thumbnail product-thumbnail">
                            <span class="cover_image">
                                <img class="img-responsive"
                             src = "{$product.cover.bySize.home_default.url}"
                              data-full-size-image-url = "{$product.cover.large.url}" alt=""
                                   {if isset($size_home_default.width)}width="{$size_home_default.width}"{/if}
                              {if isset($size_home_default.height)}height="{$size_home_default.height}"{/if} 
                                >
                            </span>
                            {if isset($product.images[1])}
                            <span class="hover_image">
                                <img  class="img-responsive"
                                 src = "{$product.images[1].bySize.home_default.url}"
                              data-full-size-image-url = "{$product.images[1].bySize.home_default.url}" alt=""
                                   {if isset($size_home_default.width)}width="{$size_home_default.width}"{/if}
                              {if isset($size_home_default.height)}height="{$size_home_default.height}"{/if} 
                                > 
                            </span>
                        {/if}               
                        </a> 
                        <div class="conditions-box">
                        {if isset($product.show_condition) && $product.condition.type == "new" && $product.show_condition == 1  && isset($product.new) && $product.new == 1 }
                            <span class="new_product">{l s='New'}</span>
                            {/if}
                            {if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price }
                            <span class="sale_product">{l s='Sale'}</span>
                        {/if}  
                        </div> 
                    </div>  
                   <div class="right-product">       
                        <div class="description-product">
                            <div class="product_name"><a href="{$product.url}">{$product.name|truncate:30:'...'}</a></div>          
                            {if $product.show_price}
                              <div class="product-price-and-shipping">
                                {hook h='displayProductPriceBlock' product=$product type="before_price"}
                                <span class="price">{$product.price}</span>
                    			{if $product.has_discount}
                                  {hook h='displayProductPriceBlock' product=$product type="old_price"}
                                  <span class="regular-price">{$product.regular_price}</span>
                                  {if $product.discount_type === 'percentage'}
                                    <span class="discount-percentage-product">{$product.discount_percentage}</span>
                                  {/if}
                                {/if}
                                {hook h='displayProductPriceBlock' product=$product type='unit_price'}
                    
                                {hook h='displayProductPriceBlock' product=$product type='weight'}
                              </div>
                            {/if}
                        </div>
                        <div class="decriptions-short">
                        {$product.description_short nofilter}
                        </div>
                       <div class="product-availability-list">
                          {if $product.show_availability && $product.availability_message}
                            {if $product.availability == 'available'}
                              <i class="fa fa-check"></i>
                            {elseif $product.availability == 'last_remaining_items'}
                              <i class="fa fa-warning"></i>
                            {else}
                              <i class="fa fa-ban"></i>
                            {/if}
                            {$product.availability_message}
                          {/if}
                        </div>
                        <div class="variant-links-list">
                          {foreach from=$product.main_variants item=variant}
                            <a href="{$variant.url}"
                               class="{$variant.type}"
                               title="{$variant.name}"
                               {*
                                  TODO:
                                    put color in a data attribute for use with attr() as soon as browsers support it,
                                    see https://developer.mozilla.org/en/docs/Web/CSS/attr
                                *}
                              {if $variant.html_color_code} style="background-color: {$variant.html_color_code}" {/if}
                              {if $variant.texture} style="background-image: url({$variant.texture})" {/if}
                            ><span class="sr-only">{$variant.name}</span></a>
                          {/foreach}
                        </div>
                        
                        <form action="{Context::getContext()->link->getPageLink('cart')}" method="post">
                            <input type="hidden" name="token" value="{Tools::getToken(false)}">
                            <input type="hidden" name="id_product" value="{$product.id}">
                              <button class="add-to-cart" data-button-action="add-to-cart" type="submit" {if !$product.quantity}disabled{/if}>
                              {if $product.quantity}
                                {l s='Add to cart' d='Shop.Theme.Actions'}
                              {else}
                                {l s='Out of stock'}
                              {/if}  
                              </button>
                        </form>
						<div class="quick-view-product-list">
                          <a href="javascript:void(0)" class="quick-view" data-link-action="quickview">
                            <i class="fa fa-search"></i>
                          </a>
                        </div>
                    </div>               
                            </div>
                    </div>
                {/foreach}
        </div>
      </div>

  {block name='pagination'}
    {include file='_partials/pagination.tpl' pagination=$listing.pagination}
  {/block}
<script type="text/javascript">
$(document).ready(function () {
	$(".click-product-grid").click(function(e) {
		$("#wrapper").removeClass("active_list");
        $("#wrapper").addClass("active_grid");
		setCookie('status_list_product','active_grid',1);
    });
	$(".click-product-list").click(function(e) {
        $("#wrapper").removeClass("active_grid");
		$("#wrapper").addClass("active_list");
		setCookie('status_list_product','active_list',1);
    });
		if(getCookie('status_list_product')!=="" && getCookie('status_list_product')!=="active_grid"){
			$("#wrapper").removeClass("active_grid");
			$("#wrapper").addClass("active_list");
		}
});

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}
</script>
</div>

