/**
 * 2007-2017 Sttheme
 *
 * NOTICE OF LICENSE
 *
 * St feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
 *
 * DISCLAIMER
 *
 *  @Module Name: St Feature
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2017 Sttheme
 *  @license   http://leotheme.com - prestashop template provider
 */
$(document).ready(function(){
	$.ajax({
		type: 'POST',
		headers: {"cache-control": "no-cache"},
		url: prestashop.urls.base_url + 'modules/stfeature/psajax.php',
		async: true,
		cache: false,
		data: {"action": "render-modal"},
		success: function (result)
		{
			if(result != '')
			{						
				$('body').append(result);
				activeEventModal();
			}
						
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			// alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
		}
	});
	
	leoSelectAttr();
	leoBtCart();
	
	prestashop.on('updateProductList', function() {
		leoSelectAttr();
		leoBtCart();	
	});	
		
	// prestashop.on('updatedCart', function (event) {
		// console.log('aaa111');
		// console.log($('#blockcart-modal'));
		// $('#blockcart-modal').on('hidden.bs.modal', function (e) {
		
			// $('.st-bt-cart.active').find('.st-bt-cart-content').fadeIn('fast');
			// $('.st-bt-cart.active').find('.st-loading').hide();
			// $('.st-bt-cart.active').removeClass('active reset');
		// });
	// });
	
	prestashop.on('updateCart', function (event) {
	
		check_active_modal_cart = setInterval(function(){
			
			if ($('.st-bt-cart.active').length && $('#blockcart-modal').length && $('#blockcart-modal').hasClass('modal fade in'))
			{
				
				//$('.st-bt-cart.active').find('.st-bt-cart-content').fadeIn('fast');
				$('.st-bt-cart.active').find('.st-loading').hide();
				$('.st-bt-cart.active').removeClass('active reset');
				
				clearInterval(check_active_modal_cart);
			}
			
		}, 200);
		
	});
});

//DONGND:: event for button add cart
function leoBtCart()
{
	$('.st-bt-cart').click(function(){
		if ($(this).hasClass('active') || $(this).hasClass('reset') || $('.st-bt-cart.active').length || $(this).hasClass('disabled'))
		{
			return false;
		}
		
		//$(this).find('.st-bt-cart-content').hide();
		$(this).find('.st-loading').css({'display':'block'});
		$(this).addClass('active');
		// console.log('aaa');
		// return false;
		var object_button_container = $(this).parents('.button-container');
		var qty_product = object_button_container.find('.qty_product').val();
		var min_qty = object_button_container.find('.minimal_quantity').val();
		var quantity_product = object_button_container.find('.quantity_product').val();
		// console.log(qty_product);
		// console.log(min_qty);
		// console.log(quantity_product);
		if(Math.floor(qty_product) == qty_product && $.isNumeric(qty_product) && qty_product > 0)
		{
			// return true;
		}
		else
		{
			$(this).addClass('reset');
			// $(this).siblings('.qty_product').val(min_qty);
			$('.st-modal-cart .modal-header').addClass('warning-mess');
			$('.st-modal-cart .st-warning').show();
			$('.st-modal-cart').modal('show');
			return false;
		}
		// $('.leo-modal-cart .modal-header').addClass('block-mess');
		// $('.leo-modal-cart .st-block').show();
		// $('.leo-modal-cart').modal('show');
		if (parseInt(qty_product) < parseInt(min_qty))
		{
			$(this).addClass('reset');
			$('.st-modal-cart .modal-header').addClass('info-mess');
			$('.st-modal-cart .st-info .alert-min-qty').text(min_qty);
			$('.st-modal-cart .st-info').show();
			$('.st-modal-cart').modal('show');
			return false;
		}
		
		if (parseInt(qty_product) > parseInt(quantity_product))
		{
			$(this).addClass('reset');
			// console.log('test');
			$('.st-modal-cart .modal-header').addClass('block-mess');			
			$('.st-modal-cart .st-block').show();
			$('.st-modal-cart').modal('show');
			return false;
		}
		
	});
}

//DONGND:: event for button add cart
function leoSelectAttr()
{
	$('.leo-select-attr').click(function(e){
		e.preventDefault();
		var id_product = $(this).data('id-product');
		var attr_txt = $(this).text();
		var id_attr = $(this).data('id-attr');
		var qty_attr = $(this).data('qty-attr');
		var min_qty_attr = $(this).data('min-qty-attr');
		var parent_e = $(this).parents('.button-container');
		
		// console.log(attr_txt);
		if (!$(this).hasClass('selected'))
		{
			$(this).siblings().removeClass('selected');
			$(this).addClass('selected');
			parent_e.find('.dropdownListAttrButton_'+id_product).text(attr_txt);
			if($(this).hasClass('disable'))
			{
				if(!parent_e.find('.st-bt-cart_'+id_product).hasClass('disabled'))
				{
					parent_e.find('.st-bt-cart_'+id_product).addClass('disabled');
				}
			}
			else
			{
				if(parent_e.find('.st-bt-cart_'+id_product).hasClass('disabled'))
				{
					parent_e.find('.st-bt-cart_'+id_product).removeClass('disabled');
				}
			};
			
			var $product_article_e = $(this).parents('.product-miniature[data-id-product=' + id_product+']');
			// console.log($product_article_e);
			//$product_article_e.find('.st-bt-cart .st-bt-cart-content').hide();
			$product_article_e.find('.st-bt-cart .st-loading').css({'display':'block'});
			$product_article_e.find('.st-bt-cart').addClass('active');
			
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: prestashop.urls.base_url + 'modules/stfeature/psajax.php',
				async: true,
				cache: false,
				data: {
					"action": "get-attribute-data",
					"id_product": id_product,
					"id_product_attribute": id_attr,
				},
				success: function (result)
				{
					if(result != '')
					{						
						var obj = $.parseJSON(result);
						// console.log($(this));
						// console.log($('.product-miniature[data-id-product=' + id_product+']'));
						
						$product_article_e.find('.product-thumbnail img').attr('src', obj.product_cover.bySize.home_default.url).attr('alt', obj.product_cover.legend);
						$product_article_e.find('.product-thumbnail').attr('href', obj.product_url);
						$product_article_e.find('.product-price-and-shipping').empty().append(obj.price_attribute);
						if (typeof enable_product_label != 'undefined' && enable_product_label)
						{
							updatePostionLabel($product_article_e);
						}
					}
					//$('.st-bt-cart.active').find('.st-bt-cart-content').fadeIn('fast');
					$('.st-bt-cart.active').find('.st-loading').hide();
					$('.st-bt-cart.active').removeClass('active reset');
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
				}
			});
		}
		
		// $('#quantity_product_'+id_product).val(qty_attr);
		// $('#id_product_attribute_'+id_product).val(id_attr);
		// $('#minimal_quantity_'+id_product).val(min_qty_attr);
		// $('#qty_product_'+id_product).val(min_qty_attr).attr('min',min_qty_attr);
		
		parent_e.find('.quantity_product_'+id_product).val(qty_attr);
		parent_e.find('.id_product_attribute_'+id_product).val(id_attr);
		parent_e.find('.minimal_quantity_'+id_product).val(min_qty_attr);
		parent_e.find('.qty_product_'+id_product).val(min_qty_attr).data('min',min_qty_attr);
		
		// $('#dropdownListAttrButton_'+id_product).trigger('click');
		parent_e.find('.dropdownListAttrButton_'+id_product).trigger('click');
		
		// leoBtCart();
		// return false;
		
	});
	
	// leoBtCart();
}

//DONGND:: event for module popup after add cart
function activeEventModal()
{
	$('.st-modal-cart').on('hide.bs.modal', function (e) {
		// console.log('test');
		$('.st-modal-cart .modal-header').removeClass('block-mess info-mess warning-mess');
		$('.st-modal-cart .modal-title').hide();
		var min_qty = $('.st-bt-cart.reset').parents('.button-container').find('.minimal_quantity').val(); 
		$('.st-bt-cart.reset').parents('.button-container').find('.qty_product').val(min_qty);
		//$('.st-bt-cart.active').find('.st-bt-cart-content').fadeIn('fast');
		$('.st-bt-cart.active').find('.st-loading').hide();
		$('.st-bt-cart.active').removeClass('active reset');
		
	  // do something...
	});
	
	// $('.leo-modal-cart').on('show.bs.modal', function (e) {
		// $('.st-bt-cart.active').find('.st-bt-cart-content').show();
		// $('.st-bt-cart.active').find('.st-loading').hide();
	// })
}

//DONGND:: update position label
function updatePostionLabel($parent)
{
	var FLAG_MARGIN = 10;
      var $percent = $parent.find('.discount-percentage');
      var $onsale =  $parent.find('.on-sale');
      var $new = $parent.find('.new');
      if($percent.length){
        $new.css('top', $percent.height() * 2 + FLAG_MARGIN);
        $percent.css('top',-$parent.find('.thumbnail-container').height() + $parent.find('.product-description').height() + FLAG_MARGIN);
      }
      if($onsale.length){
        $percent.css('top', parseFloat($percent.css('top')) + $onsale.height() + FLAG_MARGIN);
        $new.css('top', ($percent.height() * 2 + $onsale.height()) + FLAG_MARGIN * 2);
      }
}