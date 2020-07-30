$(document).ready(function() {
    function add_backgroundcolor(bgcolor) {
	$('<style type="text/css"> { background-color:#' + bgcolor + '}</style>').appendTo('head');
	$('<style type="text/css">{ color:#' + bgcolor + '}</style>').appendTo('head');
    }
    function add_hovercolor(hcolor) {
	$('<style type="text/css"> .has-discount .discount,#header .dropdown-menu li.current, #header .dropdown-menu li:hover, #header .mobile_links-wrapper .dropdown-menu li:first-child,.btn-primary.focus, .btn-primary:focus, .btn-primary:hover, .btn:hover,.cart-grid .cart-grid-body > a.label:hover, .btn-primary:active,#cms #cms-about-us .page-subheading,#cms #cms-about-us .cms-line .cms-line-comp,#products .item-product-list .right-product .discount-percentage-product,.products.horizontal_mode #box-product-list .quick-view:hover, .products.horizontal_mode #box-product-list .add-to-cart:hover,.pagination .current a, .pagination a:hover,#field_productcates .title_block .title_text:before,#field_productcates .title_block .title_text:after,#blockcart-modal .cart-content .btn:hover,.modal-header .close:hover,.product-actions .add-to-cart,.js-qv-mask.mask .owl-buttons [class^="carousel-"] span:hover,#back-top a,.field-demo-wrap .control.active, .cl-row-reset .cl-reset:hover,.contact_ft ul li .icon .fa:hover,.sdsblog-box-content .sds_blog_post .right_blog_home .r_more:hover,.products-sort-order .select-list:hover,.tab_cates li:before,#fieldtabproductsisotope li a:before,.sale_product,.horizontal_mode .quick-view:hover,.horizontal_mode .add-to-cart:hover,#fieldblockcategories.horizontal_mode .item-inner .right-block-cate .name_item a:before,#fieldblockcategories.horizontal_mode .item-inner .right-block-cate a.more-cate:hover,#onecate_products_block.horizontal_mode .quick-view:hover, #onecate_products_block.horizontal_mode .add-to-cart:hover,#cart_block_top .cart_top_ajax a.view-cart:hover,a.slide-button,.outer-slide [data-u="arrowright"]:hover, .outer-slide [data-u="arrowleft"]:hover,.owl-buttons [class^="carousel-"] span:hover,#search_block_top .current:hover, #search_block_top .current[aria-expanded=true],#search_block_top .btn.button-search:hover,.sticky-fixed-top #cart_block_top:hover,.button_unique,.popup_text .social_footer a:hover,.v-megamenu-title,#search_block_top .btn.button-search:hover{ background-color:#' + hcolor + '}</style>').appendTo('head');
	$('<style type="text/css"> .block-categories .category-sub-menu .category-sub-link:hover,.price-ajax,#header .header-nav #mobile_links .expand-more[aria-expanded=true],.product-price,.has-discount.product-price, .has-discount p,.footer-container .contact_ft ul li a:hover,.product-line-grid-right .cart-line-product-actions,.cart-summary-line .value, .product-line-grid-right .product-price,.info-category span, .info-category span a,#recent_article_smart_blog_block_left .block_content ul li .info,a:hover,.block-categories a:hover,.block-categories .collapse-icons .add:hover, .block-categories .collapse-icons .remove:hover, .block-categories .arrows .arrow-down:hover, .block-categories .arrows .arrow-right:hover,#search_filters .facet .facet-title:hover,#search_filters .facet .facet-label a:hover,.click-product-list-grid > div:hover,.active_grid .click-product-list-grid > div.click-product-grid,.active_list .click-product-list-grid > div.click-product-list,#wrapper .breadcrumb li a:hover,#fieldsizechart-show:hover,.product-cover .layer:hover .zoom-in,.tabs .nav-tabs .nav-link.active, .tabs .nav-tabs .nav-link:hover,#blockcart-modal .divide-right p.price,#blockcart-modal .cart-content p,.footer-container .bullet ul li a:hover,.box-static_content .fa,.content_text h3 a:hover,.footer-top  h3:hover,.right_blog_home .content h3:hover a,.right_blog_home .content .smart-auth,.tab-category-slider .tab_cates li.active, .tab-category-slider .tab_cates li:hover,#fieldtabproductsisotope .fieldtabproductsisotope-filter a:hover, #fieldtabproductsisotope .fieldtabproductsisotope-filter a.active,.horizontal_mode .item-inner .right-product .product_name a:hover,.item-countdown .countdown_time,.item-inner .item-countdown .section_cout .Days,.item-inner .item-countdown .section_cout .Hours,.item-inner .item-countdown .section_cout .Minutes,.item-inner .item-countdown .section_cout .Seconds,#fieldblockcategories.horizontal_mode .item-inner .right-block-cate a:hover,.title_block .title_text:hover,.cart_top_ajax::before,#cart_block_top .product-name-ajax a:hover,.price,#cart_block_top .cart_top_ajax a.remove-from-cart:hover,.v-main-section-links > li > a,.v-main-section-sublinks li a:hover,.v-megamenu .more-vmegamenu a,.contact-link:hover, .contact-link-ft:hover, #header .contact-link-ft a:hover,#header .header-nav #mobile_links:hover .expand-more, .click-nav2:hover,#header_menu .fieldmegamenu .root:hover .root-item > a > .title, #header_menu .fieldmegamenu .root:hover .root-item > .title, #header_menu .fieldmegamenu .root.active .root-item > a > .title, #header_menu .fieldmegamenu .root.active .root-item > .title, #header_menu .fieldmegamenu .root .root-item > a.active > .title,#cart_block_top span.fa:hover,#cart_block_top span.cart-products-count,#cart_block_top span.cart_item_top,.v-megamenu > ul > li:hover > a{ color:#' + hcolor + '}</style>').appendTo('head');
	$('<style type="text/css"> #header_menu.fieldmegamenu-sticky,.right_blog_home .content .smart-auth:before{ border-bottom-color:#' + hcolor + '}</style>').appendTo('head');
	$('<style type="text/css"> .v-megamenu > ul > li div.submenu{ border-left-color:#' + hcolor + '}</style>').appendTo('head');
	$('<style type="text/css"> .content-nav2,.cart_top_ajax,#fieldblockcategories.horizontal_mode .item-inner,.title_block{ border-top-color:#' + hcolor + '}</style>').appendTo('head');
	$('<style type="text/css"> #fieldblockcategories.horizontal_mode .item-inner .right-block-cate a.more-cate:hover,.horizontal_mode .add-to-cart:hover,.sdsblog-box-content .sds_blog_post .right_blog_home .r_more:hover,.pagination .current a, .pagination a:hover{ border-color:#' + hcolor + '}</style>').appendTo('head');
    }
    $('.control').click(function() {

	if ($(this).hasClass('inactive')) {
	    $(this).removeClass('inactive');
	    $(this).addClass('active');
	    if (LANG_RTL == '1') {
		$('.field-demo-wrap').animate({right: '0'}, 500);
	    } else {
		$('.field-demo-wrap').animate({left: '0'}, 500);
	    }
	    $('.field-demo-wrap').css({'box-shadow': '0 0 10px #adadad', 'background': '#fff'});
	    $('.field-demo-option').animate({'opacity': '1'}, 500);
	    $('.field-demo-title').animate({'opacity': '1'}, 500);
	} else {
	    $(this).removeClass('active');
	    $(this).addClass('inactive');
	    if (LANG_RTL == '1') {
		$('.field-demo-wrap').animate({right: '-210px'}, 500);
	    } else {
		$('.field-demo-wrap').animate({left: '-210px'}, 500);
	    }
	    $('.field-demo-wrap').css({'box-shadow': 'none', 'background': 'transparent'});
	    $('.field-demo-option').animate({'opacity': '0'}, 500);
	    $('.field-demo-title').animate({'opacity': '0'}, 500);
	}
    });
    $('#backgroundColor, #hoverColor').each(function() {
	var $el = $(this);
	/* set time */var date = new Date();
	date.setTime(date.getTime() + (1440 * 60 * 1000));
	$el.ColorPicker({color: '#555555', onChange: function(hsb, hex, rgb) {
		$el.find('div').css('backgroundColor', '#' + hex);
		switch ($el.attr("id")) {
		    case 'backgroundColor' :
			add_backgroundcolor(hex);
			$.cookie('background_color_cookie', hex, {expires: date});
			break;
		    case 'hoverColor' :
			add_hovercolor(hex);
			$.cookie('hover_color_cookie', hex, {expires: date});
			break;
		    }
	    }});
    });
    /* set time */var date = new Date();
    date.setTime(date.getTime() + (1440 * 60 * 1000));
    if ($.cookie('background_color_cookie') && $.cookie('hover_color_cookie')) {
	add_backgroundcolor($.cookie('background_color_cookie'));
	add_hovercolor($.cookie('hover_color_cookie'));
	var backgr = "#" + $.cookie('background_color_cookie');
	var activegr = "#" + $.cookie('hover_color_cookie');
	$('#backgroundColor div').css({'background-color': backgr});
	$('#hoverColor div').css({'background-color': activegr});
    }
    /*Theme mode layout*/
    if (!$.cookie('mode_css') && FIELD_mainLayout == "boxed"){
	$('input[name=mode_css][value=box]').attr("checked", true);
    } else if (!$.cookie('mode_css') && FIELD_mainLayout == "fullwidth") {
	$('input[name=mode_css][value=wide]').attr("checked", true);
    } else if ($.cookie('mode_css') == "boxed") {
	$('body').removeClass('fullwidth');
	$('body').removeClass('boxed');
	$('body').addClass('boxed');
	$.cookie('mode_css', 'boxed');
	$.cookie('mode_css_input', 'box');
	$('input[name=mode_css][value=box]').attr("checked", true);
    } else if ($.cookie('mode_css') == "fullwidth") {
	$('body').removeClass('fullwidth');
	$('body').removeClass('boxed');
	$('body').addClass('fullwidth');
	$.cookie('mode_css', 'fullwidth');
	$.cookie('mode_css_input', 'wide');
	$('input[name=mode_css][value=wide]').attr("checked", true);
    }
    $('input[name=mode_css][value=box]').click(function() {
	$('body').removeClass('fullwidth');
	$('body').removeClass('boxed');
	$('body').addClass('boxed');
	$.cookie('mode_css', 'boxed');
        fullwidth_click();
    });
    $('input[name=mode_css][value=wide]').click(function() {
	$('body').removeClass('fullwidth');
	$('body').removeClass('boxed');
	$('body').addClass('fullwidth');
	$.cookie('mode_css', 'fullwidth');
        fullwidth_click();
    });
    $('.cl-td-layout a').click(function() {
	var id_color = this.id;
	$.cookie('background_color_cookie', id_color.substring(0, 6));
	$.cookie('hover_color_cookie', id_color.substring(7, 13));
	add_backgroundcolor($.cookie('background_color_cookie'));
	add_hovercolor($.cookie('hover_color_cookie'));
	var backgr = "#" + $.cookie('background_color_cookie');
	var activegr = "#" + $.cookie('hover_color_cookie');
	$('#backgroundColor div').css({'background-color': backgr});
	$('#hoverColor div').css({'background-color': activegr});
    });
    /*reset button*/$('.cl-reset').click(function() {
	/* Color */$.cookie('background_color_cookie', '');
	$.cookie('hover_color_cookie', '');
	/* Mode layout */$.cookie('mode_css', '');
	location.reload();
    });
    function fullwidth_click(){
        $('.fieldFullWidth').each(function() {
                var t = $(this);
                var fullwidth = $('main').width(),
                    margin_full = fullwidth/2;
        if (LANG_RTL != 1) {
                t.css({'left': '50%', 'position': 'relative', 'width': fullwidth, 'margin-left': -margin_full});
        } else{
                t.css({'right': '50%', 'position': 'relative', 'width': fullwidth, 'margin-right': -margin_full});
        }
    });
    }
});