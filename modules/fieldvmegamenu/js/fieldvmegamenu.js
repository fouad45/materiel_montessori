$(document).ready(function () {
	var wdth = $( window ).width();	
	if (wdth < 992){
		fieldVmegamenu_mobile();
	} else {
		fieldVmegamenu();
	}
	/* carousels */
	var vm_rp = $(".v-right-section-products").data("pquant");
	if (vm_rp > 1) {
		$(".v-right-section-products").flexisel({
                    pref: "vm-pr",
                    visibleItems: 1,
                    animationSpeed: 500,
                    autoPlay: true,
                    autoPlaySpeed: 3500,
                    pauseOnHover: true,
                    enableResponsiveBreakpoints: false,
                    clone : true
		});  
	}  
	$(".more-vmegamenu").click(function() {
		$(".more_here").slideToggle();
		if($(".more-vmegamenu a span i").attr("class")=="icon-plus"){
			$(".more-vmegamenu a span").html('<i class="icon-minus"></i>' + CloseVmenu);
		}else{
			$(".more-vmegamenu a span").html('<i class="icon-plus"></i>' + MoreVmenu);
		}
	});
});

function fieldVmegamenu_mobile(){
	$('.v-megamenu .opener').click(function(){
		var el = $(this).next('.dd-section');
		var switcher = $(this);
	        el.animate({
	            "height": "toggle"
	        }, 
	        500,
	        function(){
	        	if (el.is(':visible')) {
	                el.addClass("act");
	                switcher.addClass('opn');
	            } else {
	            	switcher.removeClass('opn');
	                el.removeClass("act");
	            }
	        });
		return false;
	});
}

function fieldVmegamenu(){
	$( ".main-section-sublinks > li" ).hover(function() {$(this).find("ul").stop().slideDown("slow");}, function() {$(this).find("ul").stop().delay(100).slideUp("fast");});
	$( ".v-megamenuitem" ).hover(function() {
		var el = $(this).find('.submenu');
		el.stop(true, true).slideDown(450).addClass("showmenu");
	}, function() {
		var el = $(this).find('.submenu');
		el.delay(100).slideUp(0).removeClass("showmenu");
	});
}