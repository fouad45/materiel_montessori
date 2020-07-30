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

<div id="pst_serviceblock" class="service_block" >
<div class="container">
<div class="pst-service-wrapper">
{if $pstserviceblockinfos.text != ""}	
		{$pstserviceblockinfos.text nofilter}	
{else}							
			<ul class="row" >
			<li class="pst-service-item first col-xs-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 hb-animate-element left-to-right">
			<div class="pst-service-item-inner">
			<div class="pst-image-block"><span class="pst-image-icon">&nbsp;</span></div>
			<div class="service-right">
				<span class="pst-service-title">Free Shipping Worldwide</span> 
				<span class="pst-service-title1">On order over $150 - 7 days a week</span> 
			</div>
			</div>
			</li>
			<li class="pst-service-item second col-xs-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 hb-animate-element top-to-bottom">
			<div class="pst-service-item-inner">
			<div class="pst-image-block"><span class="pst-image-icon">&nbsp;</span></div>
			<div class="service-right">
				<span class="pst-service-title">24/7 Customer Service</span> 
				<span class="pst-service-title1">Call us 24/7 at 000 - 123 - 456</span> 
			</div>
			</div>
			</li>
			<li class="pst-service-item third col-xs-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 hb-animate-element right-to-left">
			<div class="pst-service-item-inner">
			<div class="pst-image-block"><span class="pst-image-icon">&nbsp;</span></div>
			<div class="service-right">
				<span class="pst-service-title">Money Back Guaratee!</span>
				<span class="pst-service-title1">Send within 30 days</span> 
			</div>
			</div>
			</li>
			
			</ul>				
{/if}
</div>
</div>
</div>