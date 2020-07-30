// Template Start
$(document).ready(function() {
	bindGrid();
	/*var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent);
	if (!isMobile) {
		if ($(".parallax").length) {
			$(".parallax").sitManParallex({
				invert: false
			});
		};
    } else {
        $(".parallax").sitManParallex({
            invert: true
		});
	}*/


        $(function(){
var maxSlides,
width = $(window).width();

if ((width <= 991) && (width >= 320)) {
maxSlides = 1;
minSlides = 1;
} else {
maxSlides = 2;
minSlides = 2;
}

/*$('.bxslider').bxSlider({
mode: 'vertical',
captions: false,
slideWidth: 584,
minSlides: minSlides,
maxSlides: maxSlides,
controls: false,
pager: false,
responsive: true,
auto: true,
moveSlides: 1
});*/
});
    
	// ---------------- home page blog slider setting ----------------------
    
	var psblog = $("#blog-carousel");
    psblog.owlCarousel({
        items: 3, //10 items above 1000px browser width
        itemsDesktop: [1249, 3],
        itemsDesktopSmall: [991, 2],
        itemsTablet: [480, 1],
        itemsMobile: [320, 1],
        pagination: false
    });
    // Custom Navigation Events
    $(".blog_next").click(function() {
        psblog.trigger('owl.next');
    })
    $(".blog_prev").click(function() {
        psblog.trigger('owl.prev');
    });
	
	// ---------------- start more menu setting ----------------------
	
    if (jQuery(window).width() >= 1300) {
        var max_elem = 7;
        var items = $('.menu ul#top-menu > li');
        var surplus = items.slice(max_elem, items.length);
        surplus.wrapAll('<li class="category more_menu" id="more_menu"><div id="top_moremenu" class="popover sub-menu js-sub-menu collapse"><ul class="top-menu more_sub_menu">');
        $('.menu ul#top-menu .more_menu').prepend('<a href="#" class="dropdown-item" data-depth="0"><span class="float-xs-right hidden-md-up"><span data-target="#top_moremenu" data-toggle="collapse" class="navbar-toggler collapse-icons"><i class="material-icons add">&#xE313;</i><i class="material-icons remove">&#xE316;</i></span></span></span>More</a>');
        $('.menu ul#top-menu .more_menu').mouseover(function() {
        	$(this).children('div').css('display', 'block');
		})
        .mouseout(function() {
        	$(this).children('div').css('display', 'none');
		});
    } else if ((jQuery(window).width() >= 992) && (jQuery(window).width() <= 1249)) {
        var max_elem = 7;
        var items = $('.menu ul#top-menu > li');
        var surplus = items.slice(max_elem, items.length);
        surplus.wrapAll('<li class="category more_menu" id="more_menu"><div id="top_moremenu" class="popover sub-menu js-sub-menu collapse"><ul class="top-menu more_sub_menu">');
        $('.menu ul#top-menu .more_menu').prepend('<a href="#" class="dropdown-item" data-depth="0"><span class="float-xs-right hidden-md-up"><span data-target="#top_moremenu" data-toggle="collapse" class="navbar-toggler collapse-icons"><i class="material-icons add">&#xE313;</i><i class="material-icons remove">&#xE316;</i></span></span></span>More</a>');
        $('.menu ul#top-menu .more_menu').mouseover(function() {
        	$(this).children('div').css('display', 'block');
		})
        .mouseout(function() {
        	$(this).children('div').css('display', 'none');
		});
    }
    // ---------------- end more menu setting ----------------------
    /* ---------------- start more menu setting ---------------------- */
    var max_elem = 10;
          if (jQuery(window).width() >= 1650) {
    var items = $('.vertical-menu ul#top-vertical-menu[data-depth="0"] > li');
    var surplus = items.slice(max_elem, items.length);
    surplus.wrapAll('<li class="category more_menu" id="more_menu"><div id="top_moremenu" class="popover sub-menu js-sub-menu collapse"><ul class="top-menu more_sub_menu">');
    $('.vertical-menu ul#top-vertical-menu .more_menu').prepend('<a href="#" class="dropdown-item" data-depth="0"><span class="float-xs-right hidden-md-up"><span data-target="#top_moremenu" data-toggle="collapse" class="navbar-toggler collapse-icons"><i class="material-icons add">&#xE313;</i><i class="material-icons remove">&#xE316;</i></span></span></span>More Menu</a>');
    $('.header-top .vertical-menu ul#top-vertical-menu .more_menu').mouseover(function() {
		$(this).children('div').css('display', 'block');
	})
    .mouseout(function() {
    	$(this).children('div').css('display', 'none');
	});
        }
	
	if ((jQuery(window).width() >= 992) && (jQuery(window).width() <= 1649)) {
		 var max_elem = 8;
    var items = $('.vertical-menu ul#top-vertical-menu[data-depth="0"] > li');
    var surplus = items.slice(max_elem, items.length);
    surplus.wrapAll('<li class="category more_menu" id="more_menu"><div id="top_moremenu" class="popover sub-menu js-sub-menu collapse"><ul class="top-menu more_sub_menu">');
    $('.vertical-menu ul#top-vertical-menu .more_menu').prepend('<a href="#" class="dropdown-item" data-depth="0"><span class="float-xs-right hidden-md-up"><span data-target="#top_moremenu" data-toggle="collapse" class="navbar-toggler collapse-icons"><i class="material-icons add">&#xE313;</i><i class="material-icons remove">&#xE316;</i></span></span></span>More Menu</a>');
    $('.header-top .vertical-menu ul#top-vertical-menu .more_menu').mouseover(function() {
		$(this).children('div').css('display', 'block');
	})
    .mouseout(function() {
    	$(this).children('div').css('display', 'none');
	});
	}
	
	
	
	
	
	
	
	
    /* ---------------- end more menu setting ----------------------	*/
});

$(".products-section-title").wrap("<div class='title-wrapper'></div>");
$(".all-product-link").wrap("<div class='product-more'></div>");

$('.search_button').click(function(event) {
    $(this).toggleClass('active');
    event.stopPropagation();
    $(".searchtoggle").slideToggle("fast");
    $(".ui-autocomplete-input").focus();
});

$('.searchtoggle').click(function() {
        $('.search-container').slideToggle("slow");
        $('.searchtoggle').toggleClass('active');   
    });
/* vertical toggle*/

$('.nav-bar .vertical-menu .top-vertical-menu').css('display', 'none');
$('.nav-bar .vertical-menu .block-title').click(function(event) {
    $('.nav-bar .vertical-menu .top-vertical-menu').toggleClass('active');
    event.stopPropagation();
    $('.nav-bar .vertical-menu .top-vertical-menu').slideToggle("medium");
});
$('#index .nav-bar .vertical-menu .top-vertical-menu').css('display', 'block');
$('#index .nav-bar .vertical-menu .block-title').click(function(event) {
	event.stopPropagation();
	$('#index .nav-bar .vertical-menu .top-vertical-menu').stop();
	event.stopPropagation();
});
/*end */
$(".searchtoggle").on("click", function(event) {
    event.stopPropagation();
});

$('.pst_userinfotitle').click(function(event) {
    $(this).toggleClass('active');
    event.stopPropagation();
    $(".user-info").slideToggle("fast");
});
$(".user-info").on("click", function(event) {
    event.stopPropagation();
});

// Add/Remove acttive class on menu active in responsive
$('#menu-icon').on('click', function() {
    $(this).toggleClass('active');
});

// Loading image before flex slider load
$(window).load(function() {
    $(".loadingdiv").removeClass("spinner");
});

// Flex slider load
$(window).load(function() {
    if ($('.flexslider').length > 0) {
        $('.flexslider').flexslider({
            slideshowSpeed: $('.flexslider').data('interval'),
            pauseOnHover: $('.flexslider').data('pause'),
            animation: "fade"
        });
    }
});

// Scroll page bottom to top
$(window).scroll(function() {
    if ($(this).scrollTop() > 500) {
        $('.top_button').fadeIn(500);
    } else {
        $('.top_button').fadeOut(500);
    }
});
$('.top_button').click(function(event) {
    event.preventDefault();
    $('html, body').animate({
        scrollTop: 0
    }, 800);
});

/*======  Carousel Slider For Feature Product ==== */
var pstfeature = $("#pstfeature-carousel");
pstfeature.owlCarousel({
    items: 4, //10 items above 1000px browser width		
    itemsDesktop: [1249, 4],
    itemsDesktopSmall: [991, 3],
    itemsTablet: [767, 2],
    itemsMobile: [480, 1],
    pagination: false,
    autoplay: true
});

// Custom Navigation Events
$(".pstfeature_next").click(function() {
    pstfeature.trigger('owl.next');
})
$(".pstfeature_prev").click(function() {
    pstfeature.trigger('owl.prev');
});

/*======  Carousel Slider For New Product ==== */
var pstnewproduct = $("#pstnewproduct-carousel");
pstnewproduct.owlCarousel({
    items: 4, //10 items above 1000px browser width
    itemsDesktop: [1249, 3],
    itemsDesktopSmall: [991, 3],
    itemsTablet: [767, 2],
    itemsMobile: [479, 1],
    pagination: false
});

// Custom Navigation Events
$(".pstnewproduct_next").click(function() {
    pstnewproduct.trigger('owl.next');
})
$(".pstnewproduct_prev").click(function() {
    pstnewproduct.trigger('owl.prev');
});

/*======  Carousel Slider For Bestseller Product ==== */
var pstbestseller = $("#pstbestseller-carousel");
pstbestseller.owlCarousel({
    items: 4, //10 items above 1000px browser width
    itemsDesktop: [1249, 3],
    itemsDesktopSmall: [991, 3],
    itemsTablet: [767, 2],
    itemsMobile: [480, 1],
    pagination: false
});

// Custom Navigation Events
$(".pstbestseller_next").click(function() {
    pstbestseller.trigger('owl.next');
})
$(".pstbestseller_prev").click(function() {
    pstbestseller.trigger('owl.prev');
});

/*======  Carousel Slider For Special Product ==== */
var pstspecial = $("#pstspecial-carousel");
pstspecial.owlCarousel({
    items: 1, //10 items above 1000px browser width
    itemsDesktop: [1249, 1],
    itemsDesktopSmall: [991, 1],
    itemsTablet: [767, 1],
    itemsMobile: [480, 1],
    pagination: false,
	navigation: true,
	autoPlay:true
});

// Custom Navigation Events
$(".pstspecial_next").click(function() {
    pstspecial.trigger('owl.next');
})
$(".pstspecial_prev").click(function() {
    pstspecial.trigger('owl.prev');
});

/*======  Carousel Slider For Instagram ==== */
var wtinstagram = $("#carousel_instagram1");
wtinstagram.owlCarousel({
    items: 7, //10 items above 1000px browser width
    itemsDesktop: [1249, 4],
    itemsDesktopSmall: [991, 3],
    itemsTablet: [767, 2],
    itemsMobile: [480, 1],
    pagination: false,
    navigation: false,
    autoPlay: true
});

/*======  Carousel Slider For Accessories Product ==== */
var pstaccessories = $("#pstaccessories-carousel");
pstaccessories.owlCarousel({
    items: 3, //10 items above 1000px browser width
    itemsDesktop: [1249, 3],
    itemsDesktopSmall: [991, 3],
    itemsTablet: [767, 2],
    itemsMobile: [480, 1],
    pagination: false
});

// Custom Navigation Events
$(".pstaccessories_next").click(function() {
    pstaccessories.trigger('owl.next');
})
$(".pstaccessories_prev").click(function() {
    pstaccessories.trigger('owl.prev');
});

/*======  Carousel Slider For Category Product ==== */
var pstproductscategory = $("#pstproductscategory-carousel");
pstproductscategory.owlCarousel({
    items: 3, //10 items above 1000px browser width
    itemsDesktop: [1199, 3],
    itemsDesktopSmall: [991, 3],
    itemsTablet: [767, 2],
    itemsMobile: [480, 1],
    pagination: false,
	navigation: true
});

// Custom Navigation Events
$(".pstproductscategory_next").click(function() {
    pstproductscategory.trigger('owl.next');
})
$(".pstproductscategory_prev").click(function() {
    pstproductscategory.trigger('owl.prev');
});

/*======  Carousel Slider For Viewed Product ==== */
var pstviewed = $("#pstviewed-carousel");
pstviewed.owlCarousel({
    items: 3, //10 items above 1000px browser width
    itemsDesktop: [1199, 3],
    itemsDesktopSmall: [991, 3],
    itemsTablet: [767, 2],
    itemsMobile: [479, 1],
    pagination: false
});

// Custom Navigation Events
$(".pstviewed_next").click(function() {
    pstviewed.trigger('owl.next');
})
$(".pstviewed_prev").click(function() {
    pstviewed.trigger('owl.prev');
});

/*======  Carousel Slider For Crosssell Product ==== */
var pstcrosssell = $("#pstcrosssell-carousel");
pstcrosssell.owlCarousel({
    items: 3, //10 items above 1000px browser width
    itemsDesktop: [1199, 3],
    itemsDesktopSmall: [991, 3],
    itemsTablet: [767, 2],
    itemsMobile: [479, 1],
    pagination: false
});

// Custom Navigation Events
$(".pstcrosssell_next").click(function() {
    pstcrosssell.trigger('owl.next');
})
$(".pstcrosssell_prev").click(function() {
    pstcrosssell.trigger('owl.prev');
});

/*======  Carousel Slider For Manufacture ==== */
var pstbrand = $("#pstbrand-carousel");
pstbrand.owlCarousel({
	autoPlay: true,
	items: 5, //10 items above 1000px browser width
    itemsDesktop: [1249, 5],
    itemsDesktopSmall: [991, 4],
    itemsTablet: [767, 3],
    itemsMobile: [480, 1],
    pagination: false
});

// Custom Navigation Events
$(".pstbrand_next").click(function() {
    pstbrand.trigger('owl.next');
})
$(".pstbrand_prev").click(function() {
    pstbrand.trigger('owl.prev');
});

 var psttestimonial = $("#psttestimonial-carousel");
psttestimonial.owlCarousel({
    autoPlay: true,
    singleItem: true,
    pagination: false,
    navigation: false
});


function bindGrid() {
	var view = $.totalStorage("display");

	if (view && view != 'grid')
		display(view);
	else
		$('.display').find('li#grid').addClass('selected');

	$(document).on('click', '#grid', function(e){
		e.preventDefault();
		display('grid');
	});

	$(document).on('click', '#list', function(e){
		e.preventDefault();
		display('list');		
	});	
}

function display(view)
{
	if (view == 'list')
	{
		$('#products ul.product_list').removeClass('grid').addClass('list row');
		$('#products .product_list > li').removeClass('col-xs-12 col-sm-5 col-md-5 col-lg-4').addClass('col-xs-12');
		
		$('#products .product_list > li .thumbnail-container').addClass('col-xs-4 col-xs-5 col-md-4');
		$('#products .product_list > li .product-description').addClass('center-block col-xs-4 col-xs-7 col-md-8');
		
		$("#products .product_list > li").find(".button-container.cart").each(function(i){
			$(this).insertAfter($(this).parent().find(".highlighted-informations"));
		});
		
		
		
		$("#products .product_list > li").find(".product-actions").each(function(i){
			$(this).appendTo($(this).parent().parent().parent().find(".product-description"));
		});
		
		$("#products .product_list > li").find(".st-list-product-reviews").each(function(i){
			$(this).insertAfter($(this).parent().find(".product-title"));
		});
		
		
		 /* $('#products .product_list > li').each(function(index, element) {
			var html = '';
			html = '<div class="product-miniature js-product-miniature" data-id-product="'+ $(element).find('.product-miniature').data('id-product') +'" data-id-product-attribute="'+ $(element).find('.product-miniature').data('id-product-attribute') +'" itemscope itemtype="http://schema.org/Product"><div class="row">';
				 html += '<div class="thumbnail-container col-xs-4 col-xs-5 col-md-4">' + $(element).find('.thumbnail-container').html() + '</div>';
				
				 html += '<div class="product-description center-block col-xs-4 col-xs-7 col-md-8">';
					html += '<h3 class="h3 product-title" itemprop="name">'+ $(element).find('h3').html() + '</h3>';
					html +='<div class="button-container cart">'+ $(element).find('.button-container').html() +'</div>';
					var price = $(element).find('.product-price-and-shipping').html();       // check : catalog mode is enabled
					if (price != null) {
						html += '<div class="product-price-and-shipping">'+ price + '</div>';
					}
					html += '<div class="product-detail">'+ $(element).find('.product-detail').html() + '</div>';
					var colorList = $(element).find('.highlighted-informations').html();
					if (colorList != null) {
						html += '<div class="highlighted-informations">'+ colorList +'</div>';
					}
				html += '</div>'; 
			html += '</div></div>';
		$(element).html(html);
		});*/
		 
		$('.display').find('li#list').addClass('selected');
		$('.display').find('li#grid').removeAttr('class');
		$.totalStorage('display', 'list');
	}
	else
	{
		$('#products ul.product_list').removeClass('list').addClass('grid row');
		$('#products .product_list > li').removeClass('col-xs-12').addClass('col-xs-12 col-sm-5 col-md-5 col-lg-4');
		$('#products .product_list > li .thumbnail-container').removeClass('col-xs-4 col-xs-5 col-md-4');
		$('#products .product_list > li .product-description').removeClass('center-block col-xs-4 col-xs-7 col-md-8');
		$('#products .product_list > li ').each(function() {
   			 $(this).parent().append(this);
		});
		$("#products .product_list > li").find(".product-actions").each(function(i){
			$(this).appendTo($(this).parent().parent().find(".thumbnail-container"));
		});
		$("#products .product_list > li").find(".button-container.cart").each(function(i){
			$(this).insertBefore($(this).parent().find(".product-actions"));
		});
		
		$("#products .product_list > li").find(".st-list-product-reviews").each(function(i){
			$(this).insertBefore($(this).parent().find(".product-title"));
		});
		
		
		
		/*  $('#products .product_list > li').each(function(index, element) {
		var html = '';
		html += '<div class="product-miniature js-product-miniature" data-id-product="'+ $(element).find('.product-miniature').data('id-product') +'" data-id-product-attribute="'+ $(element).find('.product-miniature').data('id-product-attribute') +'" itemscope itemtype="http://schema.org/Product">';
			 html += '<div class="thumbnail-container">' + $(element).find('.thumbnail-container').html() +'</div>';
			html += '<div class="product-description">';
				html += '<h3 class="h3 product-title" itemprop="name">'+ $(element).find('h3').html() +'</h3>';
				html +='<div class="button-container cart">'+ $(element).find('.button-container').html() +'</div>';
				var price = $(element).find('.product-price-and-shipping').html();       // check : catalog mode is enabled
				if (price != null) {
					html += '<div class="product-price-and-shipping">'+ price + '</div>';
				}
				html += '<div class="product-detail">'+ $(element).find('.product-detail').html() + '</div>';
				var colorList = $(element).find('.highlighted-informations').html();
				if (colorList != null) {
					html += '<div class="highlighted-informations">'+ colorList +'</div>';
				}
			html += '</div>';
		html += '</div>'; 
		html += '<div class="product-actions">'+ $(element).find('.product-actions').html() +'</div>';
		$(element).html(html);
		}); */
		$('.display').find('li#grid').addClass('selected');
		$('.display').find('li#list').removeAttr('class');
		$.totalStorage('display', 'grid');
	}
}

function responsivecolumn() {
    if ($(document).width() <= 991) {
        // ---------------- Fixed header responsive ----------------------
        $(window).bind('scroll', function() {
            if ($(window).scrollTop() > 0) {
                $('.header-nav').addClass('fixed');
            } else {
                $('.header-nav').removeClass('fixed');
            }
        });
    }
    if ($(document).width() <= 991) {
        $('.container #columns_inner #left-column').appendTo('.container #columns_inner');

    } else if ($(document).width() >= 992) {
        $('.container #columns_inner #left-column').prependTo('.container #columns_inner');
    }
}
$(document).ready(function() {
    responsivecolumn();
	// tm_top link
	$('#search_toggle').click(function() {
		$('.search-container').slideToggle("slow");
		$('#search_toggle').toggleClass('active');	
	});
});



/*$(document).ready(function() {
    responsivecolumn();
	// tm_top link
	$('#top-vertical-menu < .dropdown-item').click(function() {
		$('.sub-menu').slideToggle("slow");
		$('#top-vertical-menu < .dropdown-item').toggleClass('active');	
	});
});*/


$(window).resize(function() {
    responsivecolumn();
});

// JS for fixed the header
function HeadFixTop() {
    var num = 210;
    if ($(document).width() >= 992) {
        $(window).bind('scroll', function() {
            if ($(window).scrollTop() > num) {
                $('.header-top').addClass('fixed');
            } else {
                $('.header-top').removeClass('fixed');
            }
        });
    } else {
        $('.header-top').removeClass('fixed');
    }
}
jQuery(document).ready(function() {
    "use strict";
    HeadFixTop();
    $("#top-menu .sub-menu li:has(ul)").parent().parent().parent().addClass("mega");
    $("#top-vertical-menu li:has(ul)").parent().parent().addClass("mega");
});
jQuery(window).resize(function() {
    "use strict";
    HeadFixTop()
});
if (jQuery(window).width() >= 992) {
	$(document).ready(function(){  
		if ($('body').is('#index'))	{
			jQuery(window).scroll(function () { 
				hb_animated_contents(); 
			});
			jQuery(window).load(function () { 
				hb_animated_contents(); 
			});
			function hb_animated_contents() {
				jQuery(".hb-animate-element:in-viewport").each(function (i) {
					var $this = jQuery(this);
					if (!$this.hasClass('hb-in-viewport')) {
						setTimeout(function () {
							$this.addClass('hb-in-viewport');
						}, 150 * i);
					}
				});
			}	
		}
		else{}
	});
}

/*
$("#content-wrapper #main .page-header").find(".account-page-title").each(function(i){
	$(this).insertBefore($(this).parent().parent().parent().find(".nav-title-wrapper ol"));
});
$("#content-wrapper #main #mywishlist").find(".account-page-title").each(function(i){
	$(this).insertBefore($(this).parent().parent().parent().find(".nav-title-wrapper ol"));
});
$("#content-wrapper #main #mycompare").find(".account-page-title").each(function(i){
	$(this).insertBefore($(this).parent().parent().parent().find(".nav-title-wrapper ol"));
});
*/

function sliderheight(){
	$('.sliderbg').css({'min-height': $('.flexslider').height()});
}
$(window).load(function(){sliderheight();});
$(window).resize(function(){sliderheight();});
// Scroll page bottom to top
$(window).scroll(function() {
	if ($(this).scrollTop() > 500) {
		$('.top_button').fadeIn(500);
	} else {
		$('.top_button').fadeOut(500);
	}
});							
$('.top_button').click(function(event) {
	event.preventDefault();		
	$('html, body').animate({scrollTop: 0}, 800);
});
/* vertical-menu toggle

*/



