$(document).ready(function() {
    $('.special_block_right .special_products').each(function(){
	    $(this).owlCarousel({
		    itemsCustom: [ [0, 1], [320, 1], [480, fieldspecialpsl_minitem], [768, fieldspecialpsl_mediumitem], [992, fieldspecialpsl_maxitem], [1200, fieldspecialpsl_maxitem] ],
		    responsiveRefreshRate: 50,
		    slideSpeed: 200,
		    paginationSpeed: 500,
		    rewindSpeed: 600,
		    autoPlay: fieldspecialpsl_autoscroll,
		    stopOnHover: fieldspecialpsl_pauseonhover,
		    rewindNav: true,
		    pagination: fieldspecialpsl_pagination,
		    navigation: fieldspecialpsl_navigation,
		    navigationText: ['<div class="carousel-previous disable-select"><span class="fa fa-angle-left"></span></div>', '<div class="carousel-next disable-select"><span class="fa fa-angle-right"></span></div>']
	    });
    });
    
    if($(window).width()< 991){
        $('#left_column .special_products, #right_column .special_products').css({ 'display':'none' });
	
    }
});