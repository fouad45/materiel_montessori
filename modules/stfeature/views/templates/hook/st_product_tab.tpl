{* 
* @Module Name: ST Feature
* @Website: splashythemes.com.com - prestashop template provider
* @author Splashythemes <splashythemes@gmail.com>
* @copyright  2007-2017 splashythemes
* @description: ST feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
{if isset($USE_PTABS) && $USE_PTABS == 'default'}
	<h4 class="title-info-product leo-product-show-review-title">{l s='Reviews' mod='stfeature'}</h4>
{else if isset($USE_PTABS) && $USE_PTABS == 'accordion'}
	<div class="card-header" role="tab" id="headingleofeatureproductreview">
	  <h5 class="h5">
		<a class="collapsed leo-product-show-review-title leofeature-accordion" data-toggle="collapse" data-parent="#accordion" href="#collapseleofeatureproductreview" aria-expanded="false" aria-controls="collapseleofeatureproductreview">
		  {l s='Reviews' mod='stfeature'}
		</a>
	 </h5>
  </div>
{else}
	<li class="nav-item">
	  <a class="nav-link leo-product-show-review-title" data-toggle="tab" href="#leo-product-show-review-content">{l s='Reviews' mod='stfeature'}</a>
	</li>
{/if}

