{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- MODULE Tab Cate Products Products -->
<script type="text/javascript">
$(document).ready(function() {
	$(".tab_category_slider").hide();
	$(".tab_category_slider:first").show(); 
	$(".tab-category-container-slider ul.tab_cates li").click(function() {
		$(".tab-category-container-slider ul.tab_cates li").removeClass("active");
		$(this).addClass("active");
		$(".tab_category_slider").hide();
		$(".tab_category_slider").removeClass("animate1");
		var activeTab = $(this).attr("rel"); 
		$("#"+activeTab).addClass("animate1");
		$("#"+activeTab).fadeIn(); 
	});
});

</script>
<script type="text/javascript">
{if ($fieldtabcatepsl.FIELD_TABCATEPSL_MAXITEM)}
	var fieldtabcatepsl_maxitem = {$fieldtabcatepsl.FIELD_TABCATEPSL_MAXITEM};
	{else}
	var fieldtabcatepsl_maxitem=3;
{/if}	
{if ($fieldtabcatepsl.FIELD_TABCATEPSL_MEDIUMITEM)}
	var fieldtabcatepsl_mediumitem={$fieldtabcatepsl.FIELD_TABCATEPSL_MEDIUMITEM};
	{else}
	var fieldtabcatepsl_mediumitem=2;
{/if}
{if ($fieldtabcatepsl.FIELD_TABCATEPSL_MINITEM)}
	var fieldtabcatepsl_minitem={$fieldtabcatepsl.FIELD_TABCATEPSL_MINITEM}
	{else}
	var fieldtabcatepsl_minitem=1;
{/if}
{if ($fieldtabcatepsl.FIELD_TABCATEPSL_AUTOSCROLL)}
	var fieldtabcatepsl_autoscroll={$fieldtabcatepsl.FIELD_TABCATEPSL_AUTOSCROLLDELAY}
	{else}
	var fieldtabcatepsl_autoscroll=false;
{/if}
{if ($fieldtabcatepsl.FIELD_TABCATEPSL_PAUSEONHOVER)}
	var fieldtabcatepsl_pauseonhover=true;
	{else}
	var fieldtabcatepsl_pauseonhover=false;
{/if}
{if ($fieldtabcatepsl.FIELD_TABCATEPSL_PAGINATION)}
	var fieldtabcatepsl_pagination=true;
	{else}
	var fieldtabcatepsl_pagination=false;
{/if}
{if ($fieldtabcatepsl.FIELD_TABCATEPSL_NAVIGATION)}
	var fieldtabcatepsl_navigation=true;
	{else}
	var fieldtabcatepsl_navigation=false;
{/if}
</script>

<div class="tab-category-container-slider horizontal_mode block">
    <div class="tab-category-slider">
        <div class="title_block title_font">
            <p class="title_text">
                {if isset($fieldtabcatepsl.FIELD_TABCATEPSL_TITLE) && $fieldtabcatepsl.FIELD_TABCATEPSL_TITLE != ""}
                        {$fieldtabcatepsl.FIELD_TABCATEPSL_TITLE}
                {/if}
            </p>
            <ul class="tab_cates"> 
                {$count=1}
                {foreach from=$productCates item=productCate name=fieldTabCategorySlider}
                    <li rel="tab_{$productCate.id}" {if $count==1} class="active"  {/if} > {$productCate.name}{if $count< count($productCates)}  {/if}</li>
                    {$count= $count+1}
                {/foreach}	
            </ul>
        </div>
        <div class="tab_cate_container">
            {foreach from=$productCates item=productCate name=fieldTabCategorySlider}
                <div id="tab_{$productCate.id}" class="tab_category_slider">
                    <div class="row">
                        <div class="tabcate_content carousel-grid owl-carousel">
			    {assign var="i" value="0"}
			    {if isset($fieldtabcatepsl.FIELD_TABCATEPSL_ROWITEM) && $fieldtabcatepsl.FIELD_TABCATEPSL_ROWITEM}{assign var="y" value=$fieldtabcatepsl.FIELD_TABCATEPSL_ROWITEM}{else}{assign var="y" value=1}{/if}
                            {foreach from=$productCate.product item=product name=fieldTabCateSlider}
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
                            <span class="new_product">{l s='New' mod='fieldtabcateslider'}</span>
                            {/if}
                            {if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price }
                            <span class="sale_product">{l s='Sale' mod='fieldtabcateslider'}</span>
                            {/if}  
                            </div> 
                   {if isset($FIELD_enableCountdownTimer) && $FIELD_enableCountdownTimer && isset($product.specific_prices.to) && $product.specific_prices.to != '0000-00-00 00:00:00'}
					    <span class="item-countdown">
						<span class="bg_tranp"></span>
						<span class="item-countdown-time" data-time="{$product.specific_prices.to}"></span>
					    </span>
					{/if}
                    <div class="button-action">
                        
                          <a href="javascript:void(0)" class="quick-view" data-link-action="quickview" title="{l s='Quick view' mod='fieldtabcateslider'}"> 
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
			    {if $i mod $y eq 0 || $i eq count($productCate.product)}
				</div>
			    {/if}
                            {/foreach}
                        </div>
                    </div>
                </div>
                <script type="text/javascript"> 
				$(window).load(function() {
                    $('.tab-category-container-slider #tab_{$productCate.id} .tabcate_content').owlCarousel({
                        itemsCustom: [ [0, 1], [320, 1], [480, 2], [600, 3], [992, 4], [1200, fieldtabcatepsl_maxitem] ],
                        responsiveRefreshRate: 50,
                        slideSpeed: 200,
                        paginationSpeed: 500,
                        rewindSpeed: 600,
                        autoPlay: fieldtabcatepsl_autoscroll,
                        stopOnHover: fieldtabcatepsl_pauseonhover,
                        rewindNav: true,
                        pagination: fieldtabcatepsl_pagination,
                        navigation: fieldtabcatepsl_navigation,
                        navigationText: ['<div class="carousel-previous disable-select"><span class="fa fa-angle-left"></span></div>', '<div class="carousel-next disable-select"><span class="fa fa-angle-right"></span></div>']
                    });
				     });	
                </script>
            {/foreach}	
        </div>
    </div>
	</div>
<!-- /MODULE Tab Cate Products Products -->
{*
	{$fieldtabcatepsl.FIELD_TABCATEPSL_CTLINKS nofilter}
                {if isset($fieldtabcatepsl.FIELD_TABCATEPSL_BANNER) && $fieldtabcatepsl.FIELD_TABCATEPSL_BANNER != ""}
                    <div class="image_product block">
                        {if isset($fieldtabcatepsl.FIELD_TABCATEPSL_LINKS) && $fieldtabcatepsl.FIELD_TABCATEPSL_LINKS != ""}
                            <a href="{$fieldtabcatepsl.FIELD_TABCATEPSL_LINKS|escape:'html'}"><img class="img-responsive" alt="" src="{$fieldtabcatepsl.FIELD_TABCATEPSL_BANNER}"></a>
                        {else}
                           <a href="#"><img class="img-responsive" alt="" src="{$fieldtabcatepsl.FIELD_TABCATEPSL_BANNER}"></a>
                        {/if}
                    </div>
                 {/if}
                 *}
<!-- /MODULE Tab Cate Products Products -->

