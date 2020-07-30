{*
* 2007-2017 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="pstcategorytabs" class="tabs products_block clearfix hb-animate-element right-to-left"> 	
<div class="container">
		<h2 class="h1 products-section-title text-uppercase">		
            {l s='Latest product' mod='pst_categoryslider'}
        </h2>
	<ul id="pstcategory-tabs" class="nav nav-tabs clearfix">
		{$count=0}
		{foreach from=$pstcategorysliderinfos item=pstcategorysliderinfo}
			<li class="nav-item">
				<a href="#tab_{$pstcategorysliderinfo.id}" data-toggle="tab" class="nav-link {if $count == 0}active{/if}">{$pstcategorysliderinfo.name}</a>
			</li>
			{$count= $count+1}
		{/foreach}
	</ul>
	<div class="tab-content">
		{$tabcount=0}
		{foreach from=$pstcategorysliderinfos item=pstcategorysliderinfo}
			<div id="tab_{$pstcategorysliderinfo.id}" class="tab-pane {if $tabcount == 0}active{/if}">
				{if isset($pstcategorysliderinfo.product) && $pstcategorysliderinfo.product}

					{assign var='sliderFor' value=5}
					{assign var='productCount' value=count($pstcategorysliderinfo.product)}
					
					{if isset($pstcategorysliderinfo.cate_id) && $pstcategorysliderinfo.cate_id}
                        {if $pstcategorysliderinfo.id == $pstcategorysliderinfo.cate_id.id_category}
                            <div class="categoryimage">
                                <img src="{$image_url}/{$pstcategorysliderinfo.cate_id.image}" alt="" class="category_img"/>
                            </div>
                        {/if}
                    {/if}
					
					<div class="products">
						{if $slider == 1 && $productCount >= $sliderFor}
							<div class="product-carousel">
							<ul id="pstcategory{$pstcategorysliderinfo.id}-carousel" class="pst-carousel product_list product_slider_grid" data-catid="{$pstcategorysliderinfo.id}">
						{else}
							<ul id="pstcategory{$pstcategorysliderinfo.id}" class="product_list grid row gridcount product_slider_grid" data-catid="{$pstcategorysliderinfo.id}">
						{/if}
						
							{foreach from=$pstcategorysliderinfo.product item='product'}
								<li class="{if $slider == 1 && $productCount >= $sliderFor}item{else}product_item col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl-3{/if}">
								{include file="catalog/_partials/miniatures/product.tpl" product=$product}
								</li>
							{/foreach}
						</ul>
						
						{if $slider == 1 && $productCount >= $sliderFor}
							<div class="customNavigation">
								<a class="btn prev pstcategory_prev">&nbsp;</a>
								<a class="btn next pstcategory_next">&nbsp;</a>
							</div>
						{/if}
						{if $slider == 1 && $productCount >= $sliderFor}
	  					</div>
	  					{/if}						
					</div>
				{else}
					<div class="alert alert-info">{l s='No Products in current tab at this time.' mod='pst_categoryslider'}</div>
				{/if}
			</div> 
		{$tabcount= $tabcount+1}
		{/foreach}
	</div> 
	</div> 
</div>