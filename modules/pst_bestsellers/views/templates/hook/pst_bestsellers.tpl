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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<section class="bestseller">
	<div class="products tab-container">
		<div class="homeproducts-row">
			{assign var='sliderFor' value=3}
			{if $slider == 1 && $no_prod >= $sliderFor}
				<div class="product-carousel">
					<ul id="pstbestseller-carousel" class="pstbestseller-carousel pst-carousel product_list">
						{foreach from=$products item="product" name=homebestsellerProducts}
						<li class="{if $slider == 1 && $no_prod >= $sliderFor}item{else}product_item col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl-3{/if}">
							{include file="catalog/_partials/miniatures/product.tpl" product=$product}
						</li>
						{/foreach}
					</ul>
				</div>
			{else}
				<ul id="pstbestseller-grid" class="pstbestseller-grid grid row gridcount product_list">
					{foreach from=$products item="product"}
						<li class="{if $slider == 1 && $no_prod >= $sliderFor}item{else}product_item col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl-3{/if}">
							{include file="catalog/_partials/miniatures/product.tpl" product=$product}
						</li>
					{/foreach} 
				</ul>
			<a class="all-product-link float-xs-left pull-md-right h4" href="{$allBestSellers}">
				{l s='View more products' mod='pst_bestsellers'}
			</a>			
			{/if}						
		</div>
		{if $slider == 1 && $no_prod >= $sliderFor}
			<div class="customNavigation">
				<a class="btn prev pstbestseller_prev">&nbsp;</a>
				<a class="btn next pstbestseller_next">&nbsp;</a>
			</div>
		{/if}			
	</div>
</section>