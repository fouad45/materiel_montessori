$(window).load(function() {
   
    var $container = $('.fieldtabproductsisotope-products > .isotope-grid');
    var dataFilter = $('#fieldtabproductsisotope .fieldtabproductsisotope-filter').first().children('a').attr('data-filter');

$('.isotope-grid > div').hide(0);
$('.isotope-grid > div.'+dataFilter).show(0);
    $container.isotope({
        itemSelector : '.isotope-item',
        layoutMode : 'fitRows',
        resizable : false,
        masonry: {
            columnWidth: '.isotope-item.grid-sizer'
        }
    });

    if (dataFilter != '*') {
        dataFilter = '.' + dataFilter;
        $container.isotope({ filter : dataFilter });
    }

    $('.fieldtabproductsisotope-filter > a').click(function(){
        $('.fieldtabproductsisotope-filter > a').removeClass('active');
        $('.isotope-grid > div').hide(0);
        
        var selector = $(this).attr('data-filter');
        if (selector !== '*'){
            selector = '.isotope-grid .' + selector;
        }
        $(selector).show(0);
        $(this).addClass('active');
        $container.isotope({ filter: selector });
        
        return false;
    });
    
    
});