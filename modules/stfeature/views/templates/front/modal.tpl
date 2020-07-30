{* 
* @Module Name: ST Feature
* @Website: splashythemes.com.com - prestashop template provider
* @author Splashythemes <splashythemes@gmail.com>
* @copyright  2007-2017 splashythemes
* @description: ST feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
<div class="modal st-modal st-modal-cart fade" tabindex="-1" role="dialog" aria-hidden="true">
	<!--
	<div class="vertical-alignment-helper">
	-->
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
			<h4 class="modal-title h6 text-xs-center st-warning st-alert">
				<i class="material-icons">info_outline</i>				
				{l s='You must enter a quantity' mod='stfeature'}		
			</h4>
			
			<h4 class="modal-title h6 text-xs-center st-info st-alert">
				<i class="material-icons">info_outline</i>				
				{l s='The minimum purchase order quantity for the product is ' mod='stfeature'}<strong class="alert-min-qty"></strong>		
			</h4>	
			
			<h4 class="modal-title h6 text-xs-center st-block st-alert">				
				<i class="material-icons">block</i>				
				{l s='There are not enough products in stock' mod='stfeature'}
			</h4>
		  </div>
		  <!--
		  <div class="modal-body">
			...
		  </div>
		  <div class="modal-footer">
			
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary">Save changes</button>
			
		  </div>
		  -->
		</div>
	  </div>
	<!--
	</div>
	-->
</div>