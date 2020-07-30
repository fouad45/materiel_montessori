{*
* GcInvoiceExport
*
* @author    Grégory Chartier <hello@gregorychartier.fr>
* @copyright 2019 Grégory Chartier (https://www.gregorychartier.fr)
* @license   Commercial license see license.txt
* @category  Prestashop
* @category  Module
*}

<script type="text/javascript">
    var gpath="{$gpath|escape:'html':'UTF-8'}";
    var arrValuesForOrder={$arrValuesForOrder|json_encode};
    $(function() {
        i=1;
        $( ".col-lg-9:first .checkbox" ).each(function( index ) {
         $(this).attr("id",i);
         i++;
        });
        
        var ul = $( ".form-group .col-lg-9 " ).first();
        var items = $(".form-group .col-lg-9:first div");
        for(i=0; i<arrValuesForOrder.length; i++) {
            ul.append( items.get(arrValuesForOrder[i] - 1));
        }
    });
</script>
