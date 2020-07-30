/*
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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function(){
	var catid_array = [];
	$('#pstcategorytabs .product_slider_grid').each(function(){
		var catid = $(this).data('catid');
		var owlcarouselid = $('#pstcategory' + catid + '-carousel');
		
		owlcarouselid.owlCarousel({
			items : 4, //10 items above 1000px browser width
			itemsDesktop : [1200,3], 
			itemsDesktopSmall : [991,3], 
			itemsTablet: [767,2], 
			itemsMobile : [480,1],
			pagination:false
		});	
		$('#tab_' + catid + ' .pstcategory_next').click(function(){
			owlcarouselid.trigger('owl.next');
		})
		$('#tab_' + catid + ' .pstcategory_prev').click(function(){
			owlcarouselid.trigger('owl.prev');
		});		
	});	
});
