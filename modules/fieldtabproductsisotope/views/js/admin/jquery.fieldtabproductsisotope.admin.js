$(document).ready(function(){

    // Hide main tab_content input
    $('#tab_content').parent().hide();
    $('#tab_content').parent().prev().hide();

    productAutocomplete.init();
    hideCategorySelector();
    hideAutocompleteBox();
    hideManSelector();

    // Show correct element according to the selected tab_type when the page first loaded
    if ($('#tab_type option:selected').val() == 'category')
    {
        showCategorySelector();
        $('#select_category').val($('#tab_content').val());
    }
    else if ($('#tab_type option:selected').val() == 'custom')
    {
        showAutocompleteBox();
    }
    else if ($('#tab_type option:selected').val() == 'manufacturers')
    {
        showManSelector();
        $('#select_manufacturers').val($('#tab_content').val());
    }
    else
    {
        $('#tab_content').val('');
    }

    // Attach category id to the tab_content when category selector changed
    $('#select_category').on('change', function()
    {
        $('#tab_content').val($('#select_category option:selected').val());
    });

    // Attach manufacturers id to the tab_content when manufacturers selector changed
    $('#select_manufacturers').on('change', function()
    {
        $('#tab_content').val($('#select_manufacturers option:selected').val());
    });

    // Show or hide elements according to the selected tab_type
    $('#tab_type').on('change', function()
    {
        if ($('#tab_type option:selected').val() == 'category')
        {
            hideAutocompleteBox();
            hideManSelector()
            showCategorySelector();
            $('#tab_content').val($('#select_category').val());
        }
        else if ($('#tab_type option:selected').val() == 'manufacturers')
        {
            hideCategorySelector();
            hideAutocompleteBox();
            showManSelector();
            $('#tab_content').val($('#select_manufacturers').val());
        }
        else if ($('#tab_type option:selected').val() == 'custom')
        {
            hideCategorySelector();
            hideManSelector()
            showAutocompleteBox();
            productAutocomplete.computeProductIds();
        }
        else
        {
            $('#tab_content').val('');
            hideCategorySelector();
            hideAutocompleteBox();
            hideManSelector();
        }
    });

    function showCategorySelector(){
        $('#select_category').parent().show();
        $('#select_category').parent().prev().show();
        $('#select_category').closest('.form-group').css({margin: ''});
    }

    function hideCategorySelector(){
        $('#select_category').parent().hide();
        $('#select_category').parent().prev().hide();
        $('#select_category').closest('.form-group').css({margin: 0});
    }

    function showManSelector(){
        $('#select_manufacturers').parent().show();
        $('#select_manufacturers').parent().prev().show();
        $('#select_manufacturers').closest('.form-group').css({margin: ''});
    }

    function hideManSelector(){
        $('#select_manufacturers').parent().hide();
        $('#select_manufacturers').parent().prev().hide();
        $('#select_manufacturers').closest('.form-group').css({margin: 0});
    }

    function showAutocompleteBox(){
        $('#fieldproductautocomplete').show();
        $('#fieldproductautocomplete').parent().prev().show();
        $('#fieldproductautocomplete').closest('.form-group').css({margin: ''});
    }

    function hideAutocompleteBox(){
        $('#fieldproductautocomplete').hide();
        $('#fieldproductautocomplete').parent().prev().hide();
        $('#fieldproductautocomplete').closest('.form-group').css({margin: 0});

    }

});

var productAutocomplete = new function (){
    var self = this;

    this.init = function(){

        $('#product_autocomplete_input')
            .autocomplete('ajax_products_list.php', {
                minChars: 1,
                autoFill: true,
                max:20,
                matchContains: true,
                mustMatch:true,
                scroll:false,
                cacheLength:0,
                formatItem: function(item) {
                    return item[1]+' - '+item[0];
                }
            }).result(self.addProduct);

        $('#product_autocomplete_input').setOptions({
            extraParams: {
                excludeIds : -1
            }
        });

        $('#product_list').on('click', '.delProduct', function(){
            self.delProduct($(this).attr('data-pid'));
        });
    };

    this.addProduct = function(event, data, formatted)
    {
        if (data == null)
            return false;
        var productId = data[1];
        var productName = data[0];

        $('#product_list ul').append('<li data-pid="' + productId + '">' + productName + '<span class="delProduct" data-pid="' + productId + '" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span></li>');

        $('#tab_content').val('');

        self.computeProductIds();

        $('#product_autocomplete_input').val('');
        $('#product_autocomplete_input').setOptions({
            extraParams: { excludeIds : -1 }
        });
    };

    this.delProduct = function(id)
    {
        $('#product_list ul').find('li[data-pid=' + id +']').remove();

        self.computeProductIds();

        $('#product_autocomplete_input').setOptions({
            extraParams: { excludeIds : -1 }
        });
    };

    this.computeProductIds = function()
    {
        if ($('#product_list ul').find('li').length == 0) {
            $('#tab_content').val('');
        }
        else {
            $('#product_list ul').find('li').each(function(index){
                if (index != 0){
                    $('#tab_content').val($('#tab_content').val() + ',' + $(this).attr('data-pid'));
                } else {
                    $('#tab_content').val($(this).attr('data-pid'));
                }
            });
        }
    };

    this.getProductIds = function()
    {
        return  $('#tab_content').val();
    };
};