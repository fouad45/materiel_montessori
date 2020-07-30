$('document').ready(function() {
    /* Active item */unitActiveItem();
	$('#fieldmegamenu-main .root').hover(function(e) {
	    if (LANG_RTL != 1){
		$(this).doTimeout('fieldmenuhover', 100, showMegamenuMenu, e.target);
	    } else {
		$(this).doTimeout('fieldmenuhover', 100, showMegamenuMenu_rtl, e.target);
	    }
	    $(this).addClass('active');
	}, function() {
	    $(this).doTimeout('fieldmenuhover', 100, hideMegamenuMenu);
	    $(this).removeClass('active');
	});
    /* Mobile menu open/close handles */var $_expandElement = $('<span/>', {class: 'fieldmegamenu-mobile-handle fa fa-plus small'});
    var clickHandler = 'click';
    if (Modernizr.touch) {
	clickHandler = 'touchstart';
    }
    $('#header_mobile_menu .fieldmegamenu > ul > li').each(function() {
	if ($(this).children('.menu-items').length > 0 && $(this).children('.fieldmegamenu-mobile-handle').length == 0) {
	    $(this).children('.root-item').after($_expandElement.clone());
	}
    });
    $('#header_mobile_menu .fieldmegamenu .fieldmegamenu-mobile-handle').on(clickHandler, function(e) {
	e.preventDefault();
	if ($(this).next('ul').length > 0) {
	    if ($(this).next('ul').is(':visible')) {
		$(this).next('ul').slideUp('fast');
		$(this).toggleClass('fa-plus fa-minus');
	    } else {
		$(this).next('ul').slideDown('fast');
		$(this).toggleClass('fa-plus fa-minus');
	    }
	}
    });
	fieldmegamm();
});
function showMegamenuMenu(el) {
    /* Calculate menu width (parent row width) */var mWidth = $('#fieldmegamenu-main').closest('.row').width();
    var containerOffset = $('#fieldmegamenu-main').closest('.container').offset();
    var rowOffset = $('#fieldmegamenu-main').closest('.row').offset();
    var pWidth = $(el).closest('.root').children('.menu-items').outerWidth();
    var _mpadding = ($(window).width() - mWidth) / 2;
    /* Calculate correct top position for the menu */var _menuHeight = $('#fieldmegamenu-main').height();
    var mTop = _menuHeight;
    /* Calculate correct right position for the menu */var _containerOffset = $('#fieldmegamenu-main').closest('.container').offset();
    var _containerLeftPadding = parseInt($('#fieldmegamenu-main').closest('.container').css('padding-left'));
    var _containerRightPadding = parseInt($('#fieldmegamenu-main').closest('.container').css('padding-right'));
    var _mainOffset = rowOffset.left - containerOffset.left;
    var _menuPopupOffset = $(el).closest('.root').offset();
    var mLeft = _menuPopupOffset.left - _mpadding;
    if (mLeft + pWidth > mWidth + _mainOffset){
	var xLeft = mWidth - pWidth + _mainOffset - _containerLeftPadding;
    } else {
	var xLeft = _menuPopupOffset.left - _mpadding;
    }
    $(el).closest('.root').children('.menu-items').css({'left': xLeft}).addClass('active');
}
function showMegamenuMenu_rtl(el) {
    /* Calculate menu width (parent row width) */var mWidth = $('#fieldmegamenu-main').closest('.row').width();
    var containerOffset = $('#fieldmegamenu-main').closest('.container').offset();
    var rowOffset = $('#fieldmegamenu-main').closest('.row').offset();
    var pWidth = $(el).closest('.root').children('.menu-items').outerWidth();
    var _mpadding = ($(window).width() - mWidth) / 2;
    /* Calculate correct top position for the menu */var _menuHeight = $('#fieldmegamenu-main').height();
    var mTop = _menuHeight;
    /* Calculate correct right position for the menu */var _containerOffset = $('#fieldmegamenu-main').closest('.container').offset();
    var _containerLeftPadding = parseInt($('#fieldmegamenu-main').closest('.container').css('padding-left'));
    var _containerRightPadding = parseInt($('#fieldmegamenu-main').closest('.container').css('padding-right'));
    var _mainOffset = rowOffset.left - containerOffset.left;
    var liWidth = $(el).closest('.root').outerWidth();
    var _menuPopupOffset = $(el).closest('.root').offset();
    var mRight = $(window).width() - (_menuPopupOffset.left + liWidth) - _mpadding;
    if (mRight + pWidth > mWidth + _mainOffset){
	var mRight = mWidth - pWidth + _mainOffset - _containerRightPadding;
    } else {
	var mRight = $(window).width() - (_menuPopupOffset.left + liWidth) - _mpadding;
    }
    $(el).closest('.root').children('.menu-items').css({'right': mRight, 'left': 'auto'}).addClass('active');
}
function hideMegamenuMenu() {
    $('#fieldmegamenu-main .menu-items.active').removeClass('active');
}
function unitActiveItem() {
    $("#fieldmegamenu-main .root").each(function() {
	var url = document.URL;
	url = url.replace("#", "");
	var url_lang_iso = "/" + langIso.substring(0, 2);
	var url_lang_iso_this = url.substring(url.lastIndexOf(baseUri) + baseUri.length - 1, url.lastIndexOf(baseUri) + baseUri.length + 2);
	if (url_lang_iso_this == url_lang_iso) {
	    var urlx = url.substring(0, url.lastIndexOf(baseUri) + baseUri.length - 1);
	    var urly = url.substring(url.lastIndexOf(baseUri) + baseUri.length + 2, url.length);
	    var url0 = urlx.concat(urly);
	} else {
	    var url0 = url;
	}
	var url1 = url0.replace(url0.substring(0, url0.indexOf("/") + 1), "");
	var url2 = url1.replace(url1.substring(0, url1.indexOf("/") + 1), "");
	var url3 = url2.replace(url2.substring(0, url2.indexOf("/")), "");
	var url4 = url.replace(url.substring(0, url.indexOf("/") + 1), "");
	var url5 = url4.replace(url4.substring(0, url4.indexOf("/") + 1), "");
	var url6 = url5.replace(url5.substring(0, url5.indexOf("/")), "");
	$(".fieldmegamenu .root .root-item a").removeClass("active");
	$('.fieldmegamenu .root .root-item a[href="' + url + '"]').addClass('active');
	$('.fieldmegamenu .root .root-item a[href="' + url0 + '"]').addClass('active');
	$('.fieldmegamenu .root .root-item a[href="' + url3 + '"]').addClass('active');
	$('.fieldmegamenu .root .root-item a[href="' + url6 + '"]').addClass('active');
    });
}
function fieldmegamm()
{
	elementClick1 = '#fieldmm-button';
	elementSlide1 =  'nav#fieldmegamenu-mobile';
	$(elementClick1).on('click', function(e){
		e.stopPropagation();
		var subUl = $(this).next(elementSlide1);
		if(subUl.hasClass('inactive'))
		{
		    subUl.removeClass('inactive');
		    subUl.addClass('active');
		}
		else
		{
		    subUl.removeClass('active');
		    subUl.addClass('inactive');
		}
		e.preventDefault();
	});
	$(elementSlide1).on('click', function(e){
		e.stopPropagation();
	});
	$(document).on('click', function(e){
		e.stopPropagation();
		var elementHide1 = $(elementClick1).next(elementSlide1);
		$(elementHide1).addClass('inactive');
		$(elementHide1).removeClass('active');
	});
}