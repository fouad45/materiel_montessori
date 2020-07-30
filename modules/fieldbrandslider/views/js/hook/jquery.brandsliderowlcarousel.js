$(window).load(function(){
    $('#fieldbrandslider-manufacturers').owlCarousel({
        itemsCustom: [ [0, 2], [320, fieldbs_minitem], [479, 3], [600, 4], [992, 5], [1023, 6], [1200, fieldbs_maxitem], [1559, fieldbs_maxitem] ],
        responsiveRefreshRate: 50,
        slideSpeed: 200,
        paginationSpeed: 500,
        rewindSpeed: 600,
        autoPlay: fieldbs_autoscroll,
        stopOnHover: fieldbs_pauseonhover,
        rewindNav: true,
        pagination: fieldbs_pagination,
        navigation: fieldbs_navigation,
        navigationText: ['<div class="carousel-previous disable-select"><span class="fa fa-angle-left"></span></div>', '<div class="carousel-next disable-select"><span class="fa fa-angle-right"></span></div>']
    });
});