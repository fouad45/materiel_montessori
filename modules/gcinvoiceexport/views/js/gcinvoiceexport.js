/**
 * GcInvoiceExport
 *
 * @author    Grégory Chartier <hello@gregorychartier.fr>
 * @copyright 2019 Grégory Chartier (https://www.gregorychartier.fr)
 * @license   Commercial license see license.txt
 * @category  Prestashop
 * @category  Module
 */


$(function () {
    $("#datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
    
    $("#datepicker2").datepicker({ dateFormat: 'yy-mm-dd' });

    $(".form-group .col-lg-9 ").first().sortable(
        {
            update: function (event, ui) {
                var order = $(this).sortable("toArray");
                var tmp = [];
                for (i = 0; i < order.length; i++)
                {
                    name = $("#" + order[i] + " input").attr("name").replace('gcinvoiceexport_', '');
                    var item = {
                        "name": name,
                        "position": order[i]
                    };
                    tmp.push(item);
                }

                data = JSON.stringify({data: tmp});
                $.ajax({
                    type: 'POST',
                    url: gpath + 'modules/gcinvoiceexport/savedata.php?token='+myToken,
                    dataType: 'json',
                    data: {data: data},
                    success: function (msg) {

                    }
                });

            }
        }
    );
    
    $(".form-group .col-lg-9 ").first().disableSelection();
});
