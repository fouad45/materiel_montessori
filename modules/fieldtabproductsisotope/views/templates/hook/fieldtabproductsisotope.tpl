{if $fieldtabproductsisotope.products}
    <div id="fieldtabproductsisotope" class="horizontal_mode block">
        <ul class="fieldtabproductsisotope-filters title_block">
            {foreach $fieldtabproductsisotope.filters as $key => $filter}
                    <li class="fieldtabproductsisotope-filter title_font">
          	<a href="javascript:void(0)" data-filter="{$filter.filter}" class="{if $filter@first}active{/if}">
			    {if !$filter.title_image != ''}<span class="text">{$filter.title}</span>{/if}
			    {if $filter.title_image != ''}<img src="{$filter.title_image}" alt="" />{/if}
			</a>
                    </li>
            {/foreach}
        </ul>
		<div class="row">
	    <div class="fieldtabproductsisotope-products">
		<div class="isotope-grid">
                    {foreach $fieldtabproductsisotope.banners as $key1 => $banner}
                        {if $banner}
                                <div class="col-xs-12 isotope-item item {$banner.banner_type}">
                                    {if !empty($banner.banner_image)}
                                        {if !empty($banner.banner_link)}
                                            <a href="{$banner.banner_link}"><img class="img-responsive" src="{$banner.banner_image}" alt="" /></a>
                                        {else}
                                            <img class="img-responsive" src="{$banner.banner_image}" alt="" />
                                        {/if}
                                    {/if}
                                    {if isset($banner.countdown_to) && $banner.countdown_to != "0000-00-00 00:00:00"}
                                        <span class="item-countdown">
                                            <span class="bg_tranp"></span>
                                            <span class="item-countdown-time" data-time="{$banner.countdown_to}"></span>
                                        </span>
                                    {/if}
                                </div>
                        {/if}
                    {/foreach}
		    
                              {assign var="i" value="0"} 
                {foreach $fieldtabproductsisotope.filters as $key => $filter}
                    <div class="{$filter.filter} content{$i}">
                        
                        {foreach $fieldtabproductsisotope.products as $key => $product}
			{if $product && $product.type|strstr:$filter.filter}
                         
                            	<div class="item">
			    <div class=" isotope-item  {$product.type}">
			
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
                            <span class="new_product">{l s='New' mod='fieldtabproductsisotope'}</span>
                            {/if}
                            {if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price }
                            <span class="sale_product">{l s='Sale' mod='fieldtabproductsisotope'}</span>
                            {/if}  
                     </div>
                     {if isset($FIELD_enableCountdownTimer) && $FIELD_enableCountdownTimer && isset($product.specific_prices.to) && $product.specific_prices.to != '0000-00-00 00:00:00'}
					    <span class="item-countdown">
						<span class="bg_tranp"></span>
						<span class="item-countdown-time" data-time="{$product.specific_prices.to}"></span>
					    </span>
					{/if} 
                    <div class="button-action">
                        
                          <a href="javascript:void(0)" class="quick-view" data-link-action="quickview" title="{l s='Quick view' mod='fieldtabproductsisotope'}"> 
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
                         </div>
				</div>
			   
			{/if}
                        
                           
		    {/foreach}
                   {assign var="i" value="`$i+1`"}
                    </div>
                    
                    
            {/foreach}
                        {assign var="count" value=$i} 
                   
                    
                    
                    
                    
                    
                    
                    
                    
                    
		    <div class="isotope-item item grid-sizer"></div>
		</div>
	    </div>
	</div>
    </div>
{/if}
<script type="text/javascript"> 
					 	$(window).load(function() {
								for (i=0; i < {$count}; ++i) {
					 
							  $('.content'+i).owlCarousel({
									itemsCustom: [ [0, 1], [320, 1], [480, 2], [600, 3], [992, 4], [1200,5] ],
									responsiveRefreshRate: 50,
									slideSpeed: 200,
									paginationSpeed: 500,
									rewindSpeed: 600,
									autoPlay: false,
									stopOnHover: false,
									rewindNav: true,
									pagination: false,
									navigation: true,
									navigationText: ['<div class="carousel-previous disable-select"><span class="fa fa-angle-left"></span></div>', '<div class="carousel-next disable-select"><span class="fa fa-angle-right"></span></div>']
							});
					}
                    
				     });	
                </script>