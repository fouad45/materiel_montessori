$(document).ready(function(){

    /* Add menu item */
    $('.fieldmm_addmenuitemform').on('submit', function(e){
        e.preventDefault();
        var self = $(this);
        var serializedForm = $(this).serialize();

        $.ajax({
            type: 'POST',
            cache: false,
            url: fieldmm_ajax_url,
            dataType: 'json',
            data: {
                controller: 'AdminFieldMegamenu',
                action: 'addMenuItem',
                ajax: true,
                formData: serializedForm,
                id_fieldmegamenu: fieldmm_id_fieldmegamenu
            },
            success: function(result){
                if (result.error){
                    alert(result.errorMessage);
                } else {
                    if(result.successMessage !== 1){
                        alert(result.successMessage);
                    } else {
                        // Clear the fields
                        self.find('.clearable').each(function(){
                           $(this).val("");
                        });

                        reloadSortable();
                    }
                }
            }
        });
    });

    /* Update menu item */
    $('#fieldmmMenuBuilder').on('submit', '.fieldmm_editmenuitemform', function(e){
        e.preventDefault();
        var serializedForm = $(this).serialize();

        $.ajax({
            type: 'POST',
            cache: false,
            url: fieldmm_ajax_url,
            dataType: 'json',
            data: {
                controller: 'AdminFieldMegamenu',
                action: 'updateMenuItem',
                ajax: true,
                formData: serializedForm,
                id_fieldmegamenu: fieldmm_id_fieldmegamenu
            },
            success: function(result){
                if (result.error){
                    alert(result.errorMessage);
                } else {
                    if(result.successMessage !== 1){
                        alert(result.successMessage);
                    }

                    reloadSortable();
                }
            }
        });
    });

    /* Delete menu item */
    $('#fieldmmMenuBuilder').on('click', '.fieldmm_editmenu_delete', function(e){
        e.preventDefault();
        var itemIds = [];

        var parent = $(this).closest('.menuItem');
        var parentId = parent.attr('id').split('_');

        itemIds.push(parentId[1]);

        parent.find('.menuItem').each(function(){
           var id = $(this).attr('id').split('_');
            itemIds.push(id[1]);
        });

        $.ajax({
            type: 'POST',
            cache: false,
            async: true,
            url: fieldmm_ajax_url,
            dataType: 'json',
            data: {
                controller: 'AdminFieldMegamenu',
                action: 'deleteMenuItem',
                ajax: true,
                itemIds: itemIds
            },
            success: function(){
                reloadSortable();
            }
        });

    });

    /* Edit button */
    $('#fieldmmMenuBuilder').on('click', '.edit-menu-item', function(){
       $(this).parents('.menuitem-container').find('.inline-editor-container').slideToggle(250);
    });

    /* Make sortable after document load */
    makeSortable();

    /* Init product autocomplete box */
    productAutocomplete.init();

    /* Init Rich Text Editor */
    initEditor();

    /* Select boxes */
    /* Category */
    $('.fieldmm_categorylink_title').val($.trim($('#fieldmm_categorylink_category option:selected').text()));
    $('.fieldmm_categorylink_link').val($('#fieldmm_categorylink_category option:selected').val());

    $('#fieldmm_categorylink_category').on('change', function(){
        $('.fieldmm_categorylink_title').val($.trim($('#fieldmm_categorylink_category option:selected').text()));
        $('.fieldmm_categorylink_link').val($('#fieldmm_categorylink_category option:selected').val());
    });

    /* Manufacturer */
    $('.fieldmm_manufacturerlink_title').val($('#fieldmm_manufacturerlink_manufacturer option:selected').text());
    $('.fieldmm_manufacturerlink_link').val($('#fieldmm_manufacturerlink_manufacturer option:selected').val());

    $('#fieldmm_manufacturerlink_manufacturer').on('change', function(){
        $('.fieldmm_manufacturerlink_title').val($('#fieldmm_manufacturerlink_manufacturer option:selected').text());
        $('.fieldmm_manufacturerlink_link').val($('#fieldmm_manufacturerlink_manufacturer option:selected').val());
    });

    /* Supplier */
    $('.fieldmm_supplierlink_title').val($('#fieldmm_supplierlink_supplier option:selected').text());
    $('.fieldmm_supplierlink_link').val($('#fieldmm_supplierlink_supplier option:selected').val());

    $('#fieldmm_supplierlink_supplier').on('change', function(){
        $('.fieldmm_supplierlink_title').val($('#fieldmm_supplierlink_supplier option:selected').text());
        $('.fieldmm_supplierlink_link').val($('#fieldmm_supplierlink_supplier option:selected').val());
    });

    /* CMS Page */
    $('.fieldmm_cmspagelink_title').val($.trim($('#fieldmm_cmspagelink_cmspage option:selected').text()));
    $('.fieldmm_cmspagelink_link').val($('#fieldmm_cmspagelink_cmspage option:selected').val());

    $('#fieldmm_cmspagelink_cmspage').on('change', function(){
        $('.fieldmm_cmspagelink_title').val($.trim($('#fieldmm_cmspagelink_cmspage option:selected').text()));
        $('.fieldmm_cmspagelink_link').val($('#fieldmm_cmspagelink_cmspage option:selected').val());
    });

});

function makeSortable(){

    $('.sortable').nestedSortable({
        handle: '.title',
        items: '.menuItem',
        toleranceElement: '.menuitem-container',
        forcePlaceholderSize: true,
        opacity: .6,
        placeholder: 'placeholder',
        revert: 250,
        tabSize: 15,
        tolerance: 'pointer',
        isTree: true,
        expandOnHover: 700,
        update: serializeSortable
    });

}

function serializeSortable(){

    var menuArray = $('ol.sortable').nestedSortable('toArray', {startDepthCount: 0});

    $.ajax({
        type: 'POST',
        cache: false,
        async: true,
        url: fieldmm_ajax_url,
        dataType: 'json',
        data: {
            controller: 'AdminFieldMegamenu',
            action: 'saveSortable',
            ajax: true,
            menuArray: menuArray,
            id_fieldmegamenu: fieldmm_id_fieldmegamenu
        }
    });


}

function reloadSortable(){

    $.ajax({
        type: 'POST',
        cache: false,
        async: true,
        url: fieldmm_ajax_url,
        dataType: 'json',
        data: {
            controller: 'AdminFieldMegamenu',
            action: 'reloadSortable',
            ajax: true,
            id_fieldmegamenu: fieldmm_id_fieldmegamenu
        },
        success: function(result){
            $('#fieldmmMenuBuilder').html(result);
            makeSortable();
            initEditor();
        }
    });

}

/* Product autocomplete function */
var productAutocomplete = new function (){
    var self = this;

    this.init = function()
    {
        $('#fieldmm_productlink_product')
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

        $('#fieldmm_productlink_product').setOptions({
            extraParams: {
                excludeIds : -1
            }
        });
    };

    this.addProduct = function(event, data, formatted)
    {
        if (data == null)
            return false;
        var productId = data[1];
        var productName = data[0];

        $('.fieldmm_productlink_title').val(productName);
        $('.fieldmm_productlink_link').val(productId);
    };
};

/* Setup rich text editor */
function initEditor(){

    // First remove any instances of the editor
    var mceLength = tinyMCE.editors.length;
    if (mceLength > 0){
        for (var i=mceLength; i > 0; i--) {
            tinyMCE.editors[i-1].remove();
        }
    }

    tinySetup({
        editor_selector: "custom_content",
        inline: true
    });
}