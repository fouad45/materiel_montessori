<!-- MODULE News Products Products -->
<div class="special_block_right horizontal_mode clearfix">
{if isset($fieldspecialpsl.FIELD_SPECIALPLS_TITLE) && $fieldspecialpsl.FIELD_SPECIALPLS_TITLE != ""}
        <div class="title_block title_font">
                <a href="{$link->getPageLink('prices-drop')|escape:'html'}" title="{$fieldspecialpsl.FIELD_SPECIALPLS_TITLE}" class="title_text">
                    {$fieldspecialpsl.FIELD_SPECIALPLS_TITLE}
                </a>
        </div>
{/if}        
	{if isset($specials) AND $specials}
		    <div class="row">
		<div class="special_products carousel-grid owl-carousel">
        {assign var="i" value="0"}
        {if isset($fieldspecialpsl.FIELD_SPECIALPLS_COLUMNITEM) && $fieldspecialpsl.FIELD_SPECIALPLS_COLUMNITEM}{assign var="y" value=$fieldspecialpsl.FIELD_SPECIALPLS_COLUMNITEM}{else}{assign var="y" value=1}{/if}<!--Number Row-->
      {foreach from=$specials item='product'}
           {if $i mod $y eq 0}         
           <div class="item">
           {/if}
            <div class="item-inner">
                 <div class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
                 <div class="left-product">
                      <a href="{$product.url}" class="thumbnail product-thumbnail">
                      	<span class="cover_image">
                            <img
                              src = "{$product.cover.bySize.home_default.url}"
                              data-full-size-image-url = "{$product.cover.large.url}" alt=""
                                   {if isset($size_home_default.width)}width="{$size_home_default.width}"{/if}
                              {if isset($size_home_default.height)}height="{$size_home_default.height}"{/if} 
                            >
                        </span>
                        {if isset($product.images[1])}
                        <span class="hover_image">
                            <img 
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
                            <span class="new_product">{l s='New' mod='fieldspecialproduct'}</span>
                            {/if}
                            {if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price }
                            <span class="sale_product">{l s='Sale' mod='fieldspecialproduct'}</span>
                            {/if}  
                            </div> 
                   {if isset($FIELD_enableCountdownTimer) && $FIELD_enableCountdownTimer && isset($product.specific_prices.to) && $product.specific_prices.to != '0000-00-00 00:00:00'}
					    <span class="item-countdown">
						<span class="bg_tranp"></span>
						<span class="item-countdown-time" data-time="{$product.specific_prices.to}"></span>
					    </span>
					{/if}
                    <div class="button-action">
                        
                          <a href="javascript:void(0)" class="quick-view" data-link-action="quickview" title="{l s='Quick view' mod='fieldspecialproduct'}"> 
                            <i class="fa fa-search"></i>
                          </a>
                    </div>
                    </div>  
                    <div class="right-product">       
                        <div class="product-description">
                            <div class="product_name"><a href="{$product.url}">{$product.name|truncate:30:'...'}</a></div>          
                            {if $product.show_price}
                              <div class="product-price-and-shipping">
                              	<span class="price">{$product.price}</span>
                                {if $product.has_discount}
                                  {hook h='displayProductPriceBlock' product=$product type="old_price"}
                                  <span class="regular-price">{$product.regular_price}</span>
                                  {if $product.discount_type === 'percentage'}
                                    <span class="price-percent-reduction">{$product.discount_percentage}</span>
                                  {/if}
                                {/if}
                                {hook h='displayProductPriceBlock' product=$product type="before_price"}
                                
                    
                                {hook h='displayProductPriceBlock' product=$product type='unit_price'}
                    
                                {hook h='displayProductPriceBlock' product=$product type='weight'}
                              </div>
							  <form action="{$urls.pages.cart}" method="post">
                            <input type="hidden" name="token" value="{$static_token}">
                            <input type="hidden" name="id_product" value="{$product.id}">
                              <button class="add-to-cart" data-button-action="add-to-cart" type="submit" {if !$product.quantity}disabled{/if}>
                              {l s='Add to cart' d='Shop.Theme.Actions'}
                              </button>
                        </form>
                            {/if}
                        </div>
                    </div>
                </div>
                
           </div>
           {assign var="i" value="`$i+1`"}
                {if $i mod $y eq 0 || $i eq count($specials)}
                   </div>
                {/if}
        {/foreach}
		</div></div>
	{else}
		<p>{l s='No products at this time.' mod='fieldspecialproduct'}</p>
	{/if}
</div>  








<script type="text/javascript">
{if ($fieldspecialpsl.FIELD_SPECIALPLS_MAXITEM)}
	var fieldspecialpsl_maxitem = {$fieldspecialpsl.FIELD_SPECIALPLS_MAXITEM};
	{else}
	var fieldspecialpsl_maxitem=3;
{/if}	
{if ($fieldspecialpsl.FIELD_SPECIALPLS_MEDIUMITEM)}
	var fieldspecialpsl_mediumitem={$fieldspecialpsl.FIELD_SPECIALPLS_MEDIUMITEM};
	{else}
	var fieldspecialpsl_mediumitem=2;
{/if}
{if ($fieldspecialpsl.FIELD_SPECIALPLS_MINITEM)}
	var fieldspecialpsl_minitem={$fieldspecialpsl.FIELD_SPECIALPLS_MINITEM}
	{else}
	var fieldspecialpsl_minitem=1;
{/if}
{if ($fieldspecialpsl.FIELD_SPECIALPLS_AUTOSCROLL)}
	var fieldspecialpsl_autoscroll={$fieldspecialpsl.FIELD_SPECIALPLS_AUTOSCROLLDELAY}
	{else}
	var fieldspecialpsl_autoscroll=false;
{/if}
{if ($fieldspecialpsl.FIELD_SPECIALPLS_PAUSEONHOVER)}
	var fieldspecialpsl_pauseonhover=true;
	{else}
	var fieldspecialpsl_pauseonhover=false;
{/if}
{if ($fieldspecialpsl.FIELD_SPECIALPLS_PAGINATION)}
	var fieldspecialpsl_pagination=true;
	{else}
	var fieldspecialpsl_pagination=false;
{/if}
{if ($fieldspecialpsl.FIELD_SPECIALPLS_NAVIGATION)}
	var fieldspecialpsl_navigation=true;
	{else}
	var fieldspecialpsl_navigation=false;
{/if}

$(window).load(function() {
    $('.special_block_right .special_products').each(function(){
	    $(this).owlCarousel({
		    itemsCustom: [ [0, 1], [320, 1], [480, fieldspecialpsl_minitem], [768, fieldspecialpsl_mediumitem], [992, fieldspecialpsl_maxitem], [1200, fieldspecialpsl_maxitem] ],
		    responsiveRefreshRate: 50,
		    slideSpeed: 200,
		    paginationSpeed: 500,
		    rewindSpeed: 600,
		    autoPlay: fieldspecialpsl_autoscroll,
		    stopOnHover: fieldspecialpsl_pauseonhover,
		    rewindNav: true,
		    pagination: fieldspecialpsl_pagination,
		    navigation: fieldspecialpsl_navigation,
		    navigationText: ['<div class="carousel-previous disable-select"><span class="fa fa-angle-left"></span></div>', '<div class="carousel-next disable-select"><span class="fa fa-angle-right"></span></div>']
	    });
    });
});

</script>
<!-- /MODULE News Products -->