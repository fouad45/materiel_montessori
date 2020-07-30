$(document).ready(function(){
    /* Init product autocomplete box */
    productAutocomplete.init();
});


/* Product autocomplete function */
var productAutocomplete = new function (){
    var self = this;

    this.init = function()
    {
        $('#FIELD_TABCATEPSL_PR_AUTO')
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

        $('#FIELD_TABCATEPSL_PR_AUTO').setOptions({
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
		$('#FIELD_TABCATEPSL_PR_AUTO').val(productName);
        $('#FIELD_TABCATEPSL_ID_PR').val(productId);
    };
};
