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

<div class="container">
<div class="block_newsletter-wrapper">
<div class="block_newsletter">
  <div class="row">  	
    <div class="newsletter-title col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 hb-animate-element left-to-right">
		<div class="newsletter-details">
			<div class="title">{l s='Join the Community to be Updated Firstly ?' d='Shop.Theme.Global'}</div>
			<div class="newsletter-desc">Signup for our newletter and promotion</div>
			{if $conditions}
			<!--<span class="newsletter-desc">{$conditions}</span>-->
			{/if}
		</div>
	</div>
    <div class="newsletter-block col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 hb-animate-element right-to-left">
		<form action="{$urls.pages.index}#footer" method="post">
		<div class="row">
		  <div class="col-xs-12">
		  <div class="block_newsletter_inner">                       
			  <input
				name="email"
				type="text"
				value="{$value}"
				placeholder="{l s='Enter your e-mail' d='Shop.Forms.Labels'}"
			  >          
			<input
			  class="btn btn-primary float-xs-right hidden-lg-down"
			  name="submitNewsletter"
			  type="submit"
			  value="{l s='Subscribe' d='Shop.Theme.Actions'}"
			>
			<input
			  class="btn btn-primary float-xs-right hidden-xl-up"
			  name="submitNewsletter"
			  type="submit"
			  value="{l s='OK' d='Shop.Theme.Actions'}"
			>
			<input type="hidden" name="action" value="0">
			<div class="clearfix"></div>
			</div>
		  </div>
		  <div class="col-xs-12">
			 
			  {if $msg}
				<p class="alert {if $nw_error}alert-danger{else}alert-success{/if}">
				  {$msg}
				</p>
			  {/if}
		  </div>
		</div>
		</form>
    </div>
  </div>
</div>
{hook h='displayBanner'}
</div>
</div>