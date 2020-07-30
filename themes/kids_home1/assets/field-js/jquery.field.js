$(document).ready(function() {
	$(".click-nav2").on('click', function() {
  $(".content-nav2").toggle();
  });
    Field_fullwidth();
	field_maps();
    $(window).resize(Field_fullwidth);
    /* INIT STICKY MENU */var _topSticky = $('#header').innerHeight();
    var _margintopSticky = $('#header_menu').height();
    var sticky_all = 0;
    var sticky_Cart = 0;
    var sticky_Search = 0;
    if (typeof (FIELD_stickySearch) != 'undefined' && FIELD_stickySearch && typeof (FIELD_stickyCart) != 'undefined' && FIELD_stickyCart) {
	var sticky_all = 1;
    } else {
	if (typeof (FIELD_stickySearch) != 'undefined' && FIELD_stickySearch) {
	    var sticky_Search = 1;
	}
	if (typeof (FIELD_stickyCart) != 'undefined' && FIELD_stickyCart) {
	    var sticky_Cart = 1;
	}
    }
    if (typeof (FIELD_stickyMenu) != 'undefined' && FIELD_stickyMenu) {
	if($(window).width() >= 992){	
	$(window).scroll(function() {
		 $('.ui-widget-content').slideUp();
	    if ($(this).scrollTop() >= _topSticky) {
		$('#header_menu').addClass('fieldmegamenu-sticky');
		$('#header_menu').css({'position': 'fixed', 'z-index': '1030', 'top': '0', 'left': '0', 'right': '0'});
		//$('#header').css({'margin-bottom': _margintopSticky});
		if (sticky_all) {
		    $('#sticky_top').addClass('sticky-fixed-top');
		} else {
		    if (sticky_Search) {
			$('#search_block_top').addClass('sticky-fixed-top');
		    }
		    if (sticky_Cart) {
			$('#cart_block_top').addClass('sticky-fixed-top');
		    }
		}
		if (sticky_all || sticky_Cart || sticky_Search) {
		    var _containerPaddingRight = parseInt($('.header-top .container').css('padding-right'));
		    var _containerPaddingLeft = parseInt($('.header-top .container').css('padding-left'));
		    if (LANG_RTL != 1){
			$('.sticky-fixed-top').css({'right': ($('body').outerWidth() - $('.container').outerWidth()) / 2 + _containerPaddingRight});
		    } else {
			$('.sticky-fixed-top').css({'right': 'auto', 'left': ($('body').outerWidth() - $('.container').outerWidth()) / 2 + _containerPaddingLeft});
		    }
		}
	    } else {
		$('.sticky-fixed-top').attr('style','');
		$('#sticky_top').removeClass('sticky-fixed-top');
		$('#search_block_top').removeClass('sticky-fixed-top');
		$('#cart_block_top').removeClass('sticky-fixed-top');
		$('#header_menu').removeClass('fieldmegamenu-sticky');
		$('#header_menu').attr('style','');
		//$('#header').css({'margin-bottom': 0});
	    }
	});
    }
}
    /* INIT GO TO TOP BUTTON */initScrollTop();
    ;
});
$(window).load(function() {
    /* ITEM COUNTDOWNS */if (typeof (FIELD_enableCountdownTimer) != 'undefined' && FIELD_enableCountdownTimer) {
		$('.item-countdown-time').each(function() {
			initCountdown($(this));
		});
		$('.item-countdown-time-circle').each(function() {
			initCountdowncircle($(this));
		});
		$('.item-countdown-time-square').each(function() {
			initCountdownsquare($(this));
		});
		
    }
    /* Load percent CMS */
    if (LANG_RTL != 1){
	LoadPercCMS();
    } else {
	LoadPercCMS_rtl();
    }
});
function initCountdown(el) {
    el.countdown(el.attr('data-time')).on('update.countdown', function(event) {
	var format = '';
	if (event.offset.totalDays > 1) {
	    format = format + '<span class="section_cout"><span class="Days">%D </span><span class="text">' + countdownDays + '</span></span>';
	} else {
	    format = format + '<span class="section_cout"><span class="Days">%D </span><span class="text">' + countdownDay + '</span></span>';
	}
	if (event.offset.hours > 1) {
	    format = format + '<span class="section_cout"><span class="Hours">%H </span><span class="text">' + countdownHours + '</span></span>';
	} else {
	    format = format + '<span class="section_cout"><span class="Hours">%H </span><span class="text">' + countdownHour + '</span></span>';
	}
	if (event.offset.minutes > 1) {
	    format = format + '<span class="section_cout"><span class="Minutes">%M </span><span class="text">' + countdownMinutes + '</span></span>';
	} else {
	    format = format + '<span class="section_cout"><span class="Minutes">%M </span><span class="text">' + countdownMinute + '</span></span>';
	}
	if (event.offset.seconds > 1) {
	    format = format + '<span class="section_cout"><span class="Seconds">%S </span><span class="text">' + countdownSeconds + '</span></span>';
	} else {
	    format = format + '<span class="section_cout"><span class="Seconds">%S </span><span class="text">' + countdownSecond + '</span></span>';
	}
	el.html(event.strftime(format)).fadeIn();
    });
}
function initCountdownsquare(el) {
    el.countdown(el.attr('data-time')).on('update.countdown', function(event) {
	var format = '';
	
	var start=new Date(el.attr('data-time-from'));
	var end=new Date(el.attr('data-time'));
	var sumdays=new Date(end - start);
	sumdays=sumdays/1000/60/60/24;
	if (event.offset.totalDays > 1) {
		format = format + AddCountdownSquare(sumdays,event.offset.totalDays,'Days','%D',countdownDays);
	} else {
		format = format + AddCountdownSquare(sumdays,event.offset.totalDays,'Days','%D',countdownDay);
	}
	if (event.offset.hours > 1) {
		format = format + AddCountdownSquare(24,event.offset.hours,'Hours','%H',countdownHours);
	} else {
		format = format + AddCountdownSquare(24,event.offset.hours,'Hours','%H',countdownHour);
	}
	if (event.offset.minutes > 1) {		
		format = format + AddCountdownSquare(60,event.offset.minutes,'Minutes','%M',countdownMinutes);
	} else {
		format = format + AddCountdownSquare(60,event.offset.minutes,'Minutes','%M',countdownMinute);
	}
	if (event.offset.seconds > 1) {
		format = format + AddCountdownSquare(60,event.offset.seconds,'Seconds','%S',countdownSeconds);
	} else {
		format = format + AddCountdownSquare(60,event.offset.seconds,'Seconds','%S',countdownSecond);
	}
	el.html(event.strftime(format)).fadeIn();
    });
}
function initCountdowncircle(el) {
    el.countdown(el.attr('data-time')).on('update.countdown', function(event) {
	var format = '';
	
	var start=new Date(el.attr('data-time-from'));
	var end=new Date(el.attr('data-time'));
	var sumdays=new Date(end - start);
	sumdays=sumdays/1000/60/60/24;
	if (event.offset.totalDays > 1) {
		format = format + AddCountdownCircle(sumdays,event.offset.totalDays,'Days','%D',countdownDays);
	} else {
		format = format + AddCountdownCircle(sumdays,event.offset.totalDays,'Days','%D',countdownDay);
	}
	if (event.offset.hours > 1) {
		format = format + AddCountdownCircle(24,event.offset.hours,'Hours','%H',countdownHours);
	} else {
		format = format + AddCountdownCircle(24,event.offset.hours,'Hours','%H',countdownHour);
	}
	if (event.offset.minutes > 1) {		
		format = format + AddCountdownCircle(60,event.offset.minutes,'Minutes','%M',countdownMinutes);
	} else {
		format = format + AddCountdownCircle(60,event.offset.minutes,'Minutes','%M',countdownMinute);
	}
	if (event.offset.seconds > 1) {
		format = format + AddCountdownCircle(60,event.offset.seconds,'Seconds','%S',countdownSeconds);
	} else {
		format = format + AddCountdownCircle(60,event.offset.seconds,'Seconds','%S',countdownSecond);
	}
	el.html(event.strftime(format)).fadeIn();
    });
}
function AddCountdownCircle(a,b,c,d,e) {
	var half=a/2;
	var deg=180/half;
var format = '';	
if(b>half){	
format ='<div class="pie"><div class="clip1"><div class="slice1" style="transform: rotateZ('+(a-b)*(deg)+'deg);"></div></div><div class="clip2"><div class="slice2"></div></div><div class="pie2"></div><span class="section_cout"><span class="'+c+'">'+d+' </span><span class="text">' + e + '</span></span></div>';
}else{
format ='<div class="pie"><div class="clip1"><div class="slice1" style="transform: rotateZ(180deg);"></div></div><div class="clip2"><div class="slice2" style="transform:rotateZ('+(half-b)*(deg)+'deg)"></div></div><div class="pie2"></div><span class="section_cout"><span class="'+c+'">'+d+' </span><span class="text">' + e + '</span></span></div>';	
}
return format;
}
function AddCountdownSquare(a,b,c,d,e) {
	var half=a/4;
	var width=100/half;
var format = '';	
if(b>half*3){	
format ='<div class="count_square"><div class="half_square1" style="width:'+(a-b)*width+'%;height:0"></div><div class="half_square2" style="display:none"></div><div class="square_hide"></div><span class="section_cout"><span class="'+c+'">'+d+' </span><span class="text">' + e + '</span></span></div>';
}else
if(b>half*2)
{
format ='<div class="count_square"><div class="half_square1" style="height:'+(a-(b+half))*width+'%;width:100%"></div><div class="half_square2" style="display:none"></div><div class="square_hide"></div><span class="section_cout"><span class="'+c+'">'+d+' </span><span class="text">' + e + '</span></span></div>';
}else
if(b>half){
format ='<div class="count_square"><div class="half_square1"></div><div class="half_square2" style="width:'+(a-(b+(half*2)))*width+'%;height:0"></div><div class="square_hide"></div><span class="section_cout"><span class="'+c+'">'+d+' </span><span class="text">' + e + '</span></span></div>';
}else{
format ='<div class="count_square"><div class="half_square1"></div><div class="half_square2" style="height:'+(a-(b+(half*3)))*width+'%;width:100%"></div><div class="square_hide"></div><span class="section_cout"><span class="'+c+'">'+d+' </span><span class="text">' + e + '</span></span></div>';
}
return format;
}
function initScrollTop() {
	var _topSticky1 = $('#header').innerHeight();
    var el = $('#back-top');
    if ($(this).scrollTop() > _topSticky1) {
	el.fadeIn();
    } else {
	el.fadeOut();
    }
    $(window).on('scroll', function() {
	if ($(this).scrollTop() > _topSticky1) {
	    el.fadeIn();
	} else {
	    el.fadeOut();
	}
    });
    el.on('click', function() {
	$('html, body').animate({scrollTop: 0}, '400');
    });
}


function LoadPercCMS() {
    $('.cms-line').each(function() {
	var t = $(this);
	var dataperc = t.attr('id'), dataperc_int = dataperc.replace("a", ""), barperc = Math.round(dataperc_int);
	t.find('.cms-line-comp').animate({width: barperc + "%"}, dataperc_int * 25);
	t.find('.label').append('<div class="perc"></div>');
	function perc() {
	    var t_length = parseInt($('.cms-line').css('width'));
	    var length = t.find('.cms-line-comp').css('width'), perc_div = (parseInt(length) / t_length * 100), perc = Math.round(parseInt(perc_div)), labelpos = (100 - perc);
	    t.find('.label').css('right', labelpos + '%');
	    t.find('.perc').text(perc + '%');
	}
	perc();
	setInterval(perc, 0);
    });
}
function LoadPercCMS_rtl() {
    $('.cms-line').each(function() {
	var t = $(this);
	var dataperc = t.attr('id'), dataperc_int = dataperc.replace("a", ""), barperc = Math.round(dataperc_int);
	t.find('.cms-line-comp').animate({width: barperc + "%"}, dataperc_int * 25);
	t.find('.label').append('<div class="perc"></div>');
	function perc() {
	    var t_length = parseInt($('.cms-line').css('width'));
	    var length = t.find('.cms-line-comp').css('width'), perc_div = (parseInt(length) / t_length * 100), perc = Math.round(parseInt(perc_div)), labelpos = (100 - perc);
	    t.find('.label').css('left', labelpos + '%');
	    t.find('.perc').text(perc + '%');
	}
	perc();
	setInterval(perc, 0);
    });
}
function Field_fullwidth() {
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

function field_maps() {	
    var active = false;
    $('.field-maps').click(function() {
        $(this).addClass("active");
    });
    $('.field-maps').hover(function() {}, function() {
        if ($(this).hasClass('active')) {
            setTimeout(function() {
                $('.field-maps').removeClass('active');
            }, 1000);
        }
    });
}