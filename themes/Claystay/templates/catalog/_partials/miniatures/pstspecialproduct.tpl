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
{block name='product_miniature_item'}
	<div class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
	   <div class="thumbnail-container">
	   </div>
	   <div class="product-description-wrapper">
			<div class="product-description">
			  <!--{block name='product_name'}
				<span class="h3 product-title" itemprop="name"><a href="{$product.url}" title="{$product.name}">{$product.name|truncate:50:'...'}</a></span>
			  {/block}
			  {block name='product_reviews'}
			  {hook h='displaystProductListReview' product=$product}
			  {/block}-->
			  {block name='product_description_short'}
				<div class="product-detail" itemprop="description">{$product.description_short|truncate:180:'......' nofilter}</div>
			  {/block}
			  <!--{block name='product_price_and_shipping'}
			  {if $product.show_price}
			  <div class="product-price-and-shipping">
				 <span itemprop="price" class="price">{$product.price}</span>
				 {if $product.has_discount}
					 {hook h='displayProductPriceBlock' product=$product type="old_price"}
					 <span class="regular-price">{$product.regular_price}</span>
					 
				{/if}
				 {hook h='displayProductPriceBlock' product=$product type="before_price"}           
				 {hook h='displayProductPriceBlock' product=$product type='unit_price'}
				 {hook h='displayProductPriceBlock' product=$product type='weight'}
			  </div>
			  {/if}
			  {/block}-->
			  <div class="highlighted-informations{if !$product.main_variants} no-variants{/if} hidden-sm-down">
				 {block name='product_variants'}
				 {if $product.main_variants}
				 {include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
				 {/if}
				 {/block}
			  </div>
			</div>
			  
			  	<div class="product-counter">
				  {hook h='PSProductCountdown' id_product=$product.id_product}
			   </div>
			   <div class="productcount_action">
					<a href="#">Buy Now</a>
			  </div>
		  
	   </div>
	</div>
{/block}