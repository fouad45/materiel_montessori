<!-- MODULE Featured Products Products -->
<div id="onecate_products_block" class="horizontal_mode clearfix">
<div class="container"> 
	{if isset($fieldonecatepsl.FIELD_ONECATEPSL_TITLE) && $fieldonecatepsl.FIELD_ONECATEPSL_TITLE != ""}
            <div class="title_block title_font">
                <span class="title_text">{$fieldonecatepsl.FIELD_ONECATEPSL_TITLE}</span>
            </div>
        {/if}      
	{if isset($products) AND $products}
		    <div class="row">
		<div id="onecate_products" class="carousel-grid owl-carousel">
        {assign var="i" value="0"}
        {if isset($fieldonecatepsl.FIELD_ONECATEPSL_COLUMNITEM) && $fieldonecatepsl.FIELD_ONECATEPSL_COLUMNITEM}{assign var="y" value=$fieldonecatepsl.FIELD_ONECATEPSL_COLUMNITEM}{else}{assign var="y" value=1}{/if}<!--Number Row-->
      {foreach from=$products item='product'}
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
                            <span class="new_product">{l s='New' mod='fieldonecateproductslider'}</span>
                            {/if}
                            {if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price }
                            <span class="sale_product">{l s='Sale' mod='fieldonecateproductslider'}</span>
                            {/if}  
                     </div>
                      
                    <div class="button-action">
                        <form action="{$urls.pages.cart}" method="post">
                            <input type="hidden" name="token" value="{$static_token}">
                            <input type="hidden" name="id_product" value="{$product.id}">
                              <button class="add-to-cart" data-button-action="add-to-cart" type="submit" {if !$product.quantity}disabled{/if}>
                              {if $product.quantity}
                                <i class="fa fa-shopping-cart"></i>
                              {else}
                                <i class="fa fa-ban"></i>
                              {/if}  
                              </button>
                        </form>
                          <a href="javascript:void(0)" class="quick-view" data-link-action="quickview" title="{l s='Quick view' mod='fieldonecateproductslider'}"> 
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
							  
                            {/if}
                        </div>
						{if isset($FIELD_enableCountdownTimer) && $FIELD_enableCountdownTimer && isset($product.specific_prices.to) && $product.specific_prices.to != '0000-00-00 00:00:00'}
								<span class="item-countdown">
								<span class="bg_tranp"></span>
								<span class="countdown_time"><i class="fa fa-clock-o"></i></span>
								<span class="item-countdown-time" data-time="{$product.specific_prices.to}">
								
								</span>
								
								</span>
							{/if}
                    </div>
                </div>
                
           </div>
           {assign var="i" value="`$i+1`"}
                {if $i mod $y eq 0 || $i eq count($products)}
                   </div>
                {/if}
        {/foreach}
		</div></div>
	{else}
		<p>{l s='No products at this time.' mod='fieldonecateproductslider'}</p>
	{/if}
</div>  
</div>







<script type="text/javascript">
{if ($fieldonecatepsl.FIELD_ONECATEPSL_MAXITEM)}
	var fieldonecatepsl_maxitem = {$fieldonecatepsl.FIELD_ONECATEPSL_MAXITEM};
	{else}
	var fieldonecatepsl_maxitem=3;
{/if}	
{if ($fieldonecatepsl.FIELD_ONECATEPSL_MEDIUMITEM)}
	var fieldonecatepsl_mediumitem={$fieldonecatepsl.FIELD_ONECATEPSL_MEDIUMITEM};
	{else}
	var fieldonecatepsl_mediumitem=2;
{/if}
{if ($fieldonecatepsl.FIELD_ONECATEPSL_MINITEM)}
	var fieldonecatepsl_minitem={$fieldonecatepsl.FIELD_ONECATEPSL_MINITEM}
	{else}
	var fieldonecatepsl_minitem=1;
{/if}
{if ($fieldonecatepsl.FIELD_ONECATEPSL_AUTOSCROLL)}
	var fieldonecatepsl_autoscroll={$fieldonecatepsl.FIELD_ONECATEPSL_AUTOSCROLLDELAY}
	{else}
	var fieldonecatepsl_autoscroll=false;
{/if}
{if ($fieldonecatepsl.FIELD_ONECATEPSL_PAUSEONHOVER)}
	var fieldonecatepsl_pauseonhover=true;
	{else}
	var fieldonecatepsl_pauseonhover=false;
{/if}
{if ($fieldonecatepsl.FIELD_ONECATEPSL_PAGINATION)}
	var fieldonecatepsl_pagination=true;
	{else}
	var fieldonecatepsl_pagination=false;
{/if}
{if ($fieldonecatepsl.FIELD_ONECATEPSL_NAVIGATION)}
	var fieldonecatepsl_navigation=true;
	{else}
	var fieldonecatepsl_navigation=false;
{/if}



$(window).load(function() {
    $('#onecate_products').owlCarousel({
        itemsCustom: [ [0, 1], [320, 1], [480, fieldonecatepsl_minitem], [768, fieldonecatepsl_mediumitem], [992, fieldonecatepsl_maxitem], [1200, fieldonecatepsl_maxitem] ],
        responsiveRefreshRate: 50,
        slideSpeed: 200,
        paginationSpeed: 500,
        rewindSpeed: 600,
        autoPlay: fieldonecatepsl_autoscroll,
        stopOnHover: fieldonecatepsl_pauseonhover,
        rewindNav: true,
        pagination: fieldonecatepsl_pagination,
        navigation: fieldonecatepsl_navigation,
        navigationText: ['<div class="carousel-previous disable-select"><span class="fa fa-angle-left"></span></div>', '<div class="carousel-next disable-select"><span class="fa fa-angle-right"></span></div>']
    });
});
</script>
<!-- /MODULE Featured Products -->