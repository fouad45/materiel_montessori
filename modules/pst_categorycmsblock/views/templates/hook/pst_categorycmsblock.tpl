{**
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
<div class="container">
<div id="pst_categorycmsblock">
	{if $pstcategorycmsblockinfos.text != ""}
		{$pstcategorycmsblockinfos.text nofilter}
	{else}  		
		<div class="pst-categorycmsblock-inner row">
			<div class="pst-cat-item-left product_item col-xs-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
				<div class="pst-cat-item cat-item1 hb-animate-element left-to-right">
					<div class="pst-cat-item-inner">
						<a href="#" class="pst-cat-img">
							<img src="{$image_url}/Cat-banner-01.jpg" alt="" />
						</a>
						<span class="pst-cat-details">
						<div class="pst-cat-desc">
							<div class="pst-cat-title">
							<span class="pst-cat-name">Home Decoration Pots</span>
							</div>
						</div>
						</span>
					</div>
				</div>
			</div>
			<div class="pst-cat-item-center product_item col-xs-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
				<div class="pst-cat-item cat-item2 hb-animate-element top-to-bottom">
					<div class="pst-cat-item-inner">
						<a href="#" class="pst-cat-img">
							<img src="{$image_url}/Cat-banner-02.jpg" alt="" />
						</a>
						<span class="pst-cat-details">
						<div class="pst-cat-desc">
							<div class="pst-cat-title">
							<span class="pst-cat-name">Wooden Decor Item</span>
							</div>
						</div>
						</span>
					</div>
				</div>
				<div class="pst-cat-item cat-item2 hb-animate-element bottom-to-top">
					<div class="pst-cat-item-inner1">
						<a href="#" class="pst-cat-img">
							<img src="{$image_url}/Cat-banner-03.jpg" alt="" />
						</a>
						<span class="pst-cat-details">
						<div class="pst-cat-desc">
							<div class="pst-cat-title">
							<span class="pst-cat-name">Sandmade Clay Pots</span>
							</div>
						</div>
						</span>
					</div>
				</div>
			</div>
			<div class="pst-cat-item-right product_item col-xs-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
				<div class="pst-cat-item cat-item2 hb-animate-element right-to-left">
					<div class="pst-cat-item-inner">
						<a href="#" class="pst-cat-img">
							<img src="{$image_url}/Cat-banner-04.jpg" alt="" />
						</a>
						<span class="pst-cat-details">
						<div class="pst-cat-desc">
							<div class="pst-cat-title">
							<span class="pst-cat-name">Home Decoration Pots</span>
							</div>
						</div>
						</span>
					</div>
				</div>
			</div>
		</div>
	{/if}
</div>
</div>