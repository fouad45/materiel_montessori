$(window).load(function() {
    TitleCenter();
});
$(window).resize(TitleCenter);
function TitleCenter() {
    $('.title_center').each(function() {
	var t = $(this);
	var widthtitle = t.find('.title_text').width(),
	    marginButton = Math.round(widthtitle/2+20, 2);
	if($(window).width() <= marginButton*2+60){
	    marginButton = $(window).width()/2-70;
	    t.find('.bx-controls-direction .prev').addClass('excess');
	    t.find('.bx-controls-direction .next').addClass('excess');
	} else {
	    t.find('.bx-controls-direction .prev').removeClass('excess');
	    t.find('.bx-controls-direction .next').removeClass('excess');
	}
	t.find('.owl-buttons .owl-prev').css({'margin-right': marginButton});
	t.find('.owl-buttons .owl-next').css({'margin-left': marginButton});
	t.find('.bx-controls-direction .prev').css({'margin-right': marginButton+10});
	t.find('.bx-controls-direction .next').css({'margin-left': marginButton+10});
    });
}