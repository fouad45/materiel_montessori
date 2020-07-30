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

<div id="psttestimonialcmsblock" class="testimonial hb-animate-element left-to-right">
	<div class="container">
	{if $psttestimonialcmsblockinfos.text != ""}
		{$psttestimonialcmsblockinfos.text nofilter}
	{else}		     
		<div class="pst_testimonial_wrapper">
			<div class="block">
				<h4 class="block_title">{l s='Testimonial' mod='pst_testimonialcmsblock'}</h4>

				<ul id="psttestimonial-carousel" class="psttestimonial-carousel">
					<li class="item">
					
							<!--<div class="item cms_face">
								<div class="testmonial-image">
									<img alt="testmonial" title="testmonial" src="{$image_url}/person1.jpg" width="100" height="100" />
								</div>
							</div>	 -->
							
							<div class="product_inner_cms">
							<div class="title-wrapper">
							<h2 class="h1 products-section-title text-uppercase hb-animate-element top-to-bottom hb-in-viewport"></h2>
							</div>
								<div class="desc">
									<p>Majority have suffered alteration in aome from, by injected humor , or randomized words which dont look  </p>
								 </div>
								 <div class="desc1">
									<p>passenger randomized words which dont look even slightly believable.</p>
								 </div>
								  <div class="name"><a href="#">Mrs. Mack Jeckno</a></div>
								 <div class="designation"><a title="Customer" href="#">Web Designer</a></div> 
							</div>
					
					</li>
					<li class="item">
						
							<!--<div class="item cms_face">
								<div class="testmonial-image">
									<img alt="testmonial" title="testmonial" src="{$image_url}/person2.jpg" width="100" height="100" />
								</div>
							</div>	 -->
							
							<div class="product_inner_cms">
								<div class="title-wrapper">
							<h2 class="h1 products-section-title text-uppercase hb-animate-element top-to-bottom hb-in-viewport"></h2>
							</div>
								<div class="desc">
									<p>Here are many variations of passages of Lorem Ipsum available, but the majority have suffered </p>
								 </div>
								 <div class="desc1">
									<p>passenger randomized words which dont look even slightly believable.</p>
								 </div>
								 <div class="name"><a href="#">Linda M. Rosario</a></div>
								 <div class="designation"><a title="Customer" href="#">Web Designer</a></div> 
							</div>
						
					</li>
					<li class="item">
						
							<!--<div class="item cms_face">
								<div class="testmonial-image">
									<img alt="testmonial" title="testmonial" src="{$image_url}/person3.jpg" width="100" height="100" />
								</div>
							</div>	 -->
							<div class="title-wrapper">
							<h2 class="h1 products-section-title text-uppercase hb-animate-element top-to-bottom hb-in-viewport"></h2>
							</div>
							<div class="product_inner_cms">
								
								<div class="desc">
									<p>Majority have suffered alteration in aome from, by injected humor , or randomized words which dont look </p>
								 </div>
								<div class="desc1">
									<p>passenger randomized words which dont look even slightly believable.</p>
								 </div>
								  <div class="name"><a href="#">Mary F. Kwon</a></div>
								 <div class="designation"><a title="Customer" href="#">Web Designer</a></div> 
							</div>
						
					</li>
				</ul>
			</div>
		</div>  
	{/if}
</div>
</div>
