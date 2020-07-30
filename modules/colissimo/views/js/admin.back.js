/*
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2019 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

$.fn.columnCnt = function () {
    return $(this).find('> tbody > tr:first-child td').length;
};

$.fn.expandOrderDetails = function (idColissimoOrder, tableId) {
    var tr = $(this).closest('tr');
    var nbCol = tableId.columnCnt();

    if ($(this).collapseDeliveryAddress(idColissimoOrder)) {
        $(this).removeClass('col-reference-plus').addClass('col-reference-minus');
        $('<tr class="colissimo-pending colissimo-pending-' + idColissimoOrder + '"><td class="text-center" colspan="' + nbCol + '"><img src="' + loaderPath + '"</td></tr>').insertAfter(tr);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoAffranchissement',
                ajax: 1,
                token: token,
                action: 'orderDetails',
                id_colissimo_order: idColissimoOrder,
                nb_col: nbCol
            }
        }).fail(function (jqXHR, textStatus) {
            tr.find('td.col-reference-minus').removeClass('col-reference-minus').addClass('col-reference-plus');
            showErrorMessage(genericErrorMessage);
        }).success(function (data) {
            $(data.html).insertAfter(tr);
            tr.toggleClass('selected');
        }).always(function (data) {
            $('.colissimo-pending-' + idColissimoOrder).remove();
        });
    }
};

$.fn.expandDashboardOrderDetails = function (idColissimoOrder, tableId) {
    var tr = $(this).closest('tr');
    var nbCol = tableId.columnCnt();

    $(this).removeClass('col-reference-plus').addClass('col-reference-minus');
    $('<tr class="colissimo-pending colissimo-pending-' + idColissimoOrder + '"><td class="text-center" colspan="' + nbCol + '"><img src="' + loaderPath + '"</td></tr>').insertAfter(tr);
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminColissimoDashboard',
            ajax: 1,
            token: token,
            action: 'orderDetails',
            id_colissimo_order: idColissimoOrder,
            nb_col: nbCol
        }
    }).fail(function (jqXHR, textStatus) {
        tr.find('td.col-reference-minus').removeClass('col-reference-minus').addClass('col-reference-plus');
        showErrorMessage(genericErrorMessage);
    }).success(function (data) {
        $(data.html).insertAfter(tr);
        tr.toggleClass('selected');
    }).always(function (data) {
        $('.colissimo-pending-' + idColissimoOrder).remove();
    });
};

$.fn.expandDeliveryAddress = function (idColissimoOrder, tableId) {
    var tr = $(this).closest('tr');
    var nbCol = tableId.columnCnt();

    if ($(this).prev('td').collapseOrderDetails(idColissimoOrder) && $(this).prev('td').collapseDeliveryAddress(idColissimoOrder)) {
        $('<tr class="colissimo-pending colissimo-pending-' + idColissimoOrder + '"><td class="text-center" colspan="' + nbCol + '"><img src="' + loaderPath + '"</td></tr>').insertAfter(tr);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoAffranchissement',
                ajax: 1,
                token: token,
                action: 'addressDetails',
                id_colissimo_order: idColissimoOrder,
                nb_col: nbCol
            }
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericErrorMessage);
        }).success(function (data) {
            $(data.html).insertAfter(tr);
        }).always(function (data) {
            $('.colissimo-pending-' + idColissimoOrder).remove();
            tr.toggleClass('selected');
        });
    }
};

$.fn.collapseOrderDetails = function (idColissimoOrder) {
    var nbPending = $('.colissimo-pending-' + idColissimoOrder).length;
    var tr = $(this).closest('tr');
    var orderDetails = $('.colissimo-order-detail-' + idColissimoOrder);

    if (nbPending === 0) {
        if (orderDetails.length) {
            orderDetails.remove();
            $(this).removeClass('col-reference-minus').addClass('col-reference-plus');
            $(this).find('i').removeClass('icon-minus-circle').addClass('icon-plus-circle');
            tr.toggleClass('selected');
        }

        return 1;
    } else {
        return 0;
    }
};

$.fn.collapseDashboardOrderDetails = function (idColissimoOrder) {
    var nbPending = $('.colissimo-pending-' + idColissimoOrder).length;
    var tr = $(this).closest('tr');
    var orderDetails = $('.colissimo-order-detail-' + idColissimoOrder);

    if (nbPending === 0) {
        if (orderDetails.length) {
            orderDetails.remove();
            $(this).removeClass('col-reference-minus').addClass('col-reference-plus');
            $(this).find('i').removeClass('icon-minus-circle').addClass('icon-plus-circle');
            tr.toggleClass('selected');
        }

        return 1;
    } else {
        return 0;
    }
};

$.fn.collapseDeliveryAddress = function (idColissimoOrder) {
    var nbPending = $('.colissimo-pending-' + idColissimoOrder).length;
    var tr = $(this).closest('tr');
    var addressDetails = $('.colissimo-address-detail-' + idColissimoOrder);

    if (nbPending === 0) {
        if (addressDetails.length) {
            addressDetails.remove();
            tr.toggleClass('selected');
        }

        return 1;
    } else {
        return 0;
    }
};

function processColissimoOrders(ordersList, success, ordersCount, form) {
    var percent = ((ordersCount - ordersList.length + 1) / ordersCount) * 100;
    var idColissimoOrder = parseInt(ordersList.pop());

    if (idColissimoOrder) {
        var data = {
            controller: 'AdminColissimoAffranchissement',
            ajax: 1,
            token: token,
            action: 'generateLabel',
            id_colissimo_order: idColissimoOrder
        };
        $('.colissimo-order-result-' + idColissimoOrder).html('<img src="' + loaderPath + '" />');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php?' + $.param(data),
            data: form
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericErrorMessage);
        }).success(function (data) {
            if (data.id_label) {
                success.push(data.id_label);
            }
            if (data.id_return_label) {
                success.push(data.id_return_label);
            }
            $('.js-step-line').css({width: percent + '%'});
            $('.colissimo-order-result-' + idColissimoOrder).html(data.result_html);
            processColissimoOrders(ordersList, success, ordersCount, form);
        });
    } else {
        $('html, body').animate({scrollTop: 0}, 'slow');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoAffranchissement',
                ajax: 1,
                token: token,
                action: 'displayResult',
                label_ids: JSON.stringify(success)
            }
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericErrorMessage);
        }).success(function (data) {
            $('#colissimo-process-result').html(data.result_html);
        }).always(function (data) {
            $('#colissimo-process').hide(200);
            $('#submit-process-colissimo-configuration').toggleClass('disabled');
        });
        $('.step.current').removeClass('current').addClass('complete');
        $('.step.incomplete').removeClass('incomplete').addClass('current');
    }
}

function processColissimoDepositSlip(labelList, success) {
    var labels = labelList.pop();

    if (labels) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoDepositSlip',
                ajax: 1,
                token: token,
                action: 'generateDepositSlip',
                label_ids: JSON.stringify(labels)
            }
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericErrorMessage);
        }).success(function (data) {
            if (data.error) {
                $('#deposit-slip-result').html(data.result_html);
                $('.submit-block img').hide(200);
                $('#submit-process-colissimo-deposit-slip').toggleClass('disabled');
                $('html, body').animate({scrollTop: $(document).height()}, 'slow');
                return;
            } else {
                success.push(data.depositSlipId);
            }
            processColissimoDepositSlip(labelList, success);
        });
    } else {
        displayDepositSlipResult(success);
    }
}

function displayDepositSlipResult(success) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminColissimoDepositSlip',
            ajax: 1,
            token: token,
            action: 'displayResult',
            deposit_slip_ids: JSON.stringify(success)
        }
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
    }).success(function (data) {
        if (data.error) {
            showErrorMessage(data.message);
        } else {
            for (var i = 0; i < data['label_ids'].length; i++) {
                $('input[name="colissimo_label_' + data['label_ids'][i] + '"]').closest('tr').remove();
            }
            if ($('.colissimo-deposit-slip-table tbody > tr').length === 0) {
                $('.colissimo-deposit-slip-table tbody').append(
                    '<tr>' +
                    '  <td class="list-empty hidden-print" colspan="7">' +
                    '    <div class="list-empty-msg">' +
                    '      <i class="icon-warning-sign list-empty-icon"></i>' + noWaitingShipmentsText +
                    '    </div>' +
                    '  </td>' +
                    '</tr>'
                );
            }

            $('#deposit-slip-result').html(data.result_html);
        }
    }).always(function (data) {
        $('.submit-block img').hide(200);
        $('#submit-process-colissimo-deposit-slip').toggleClass('disabled');
        $('html, body').animate({scrollTop: $(document).height()}, 'slow');
    });
}

function startLabelProcess() {
    var ordersList = [];
    var success = [];
    var form = $('#colissimo-affranchissement-configuration').serialize();

    $('#colissimo-process-result').html('');
    $('.colissimo-configuration-table input.colissimo-order-selection[type=checkbox]').each(function () {
        var idColissimoOrder = parseInt(this.name.substr(16, this.name.length - 16)) || 0;

        if (this.checked) {
            $('.colissimo-order-result-' + idColissimoOrder).html('<i>' + queueingText + '</i>');
            ordersList.push(idColissimoOrder);
        }
        $('tr.selected td.colissimo-delivery-addr').collapseDeliveryAddress(idColissimoOrder);
        $('td.col-reference-minus').collapseOrderDetails(idColissimoOrder);
    });
    $('.js-step-line').css({width: '0%'});
    if ($('.step.complete').length === 2) {
        $('.step.current').removeClass('current').addClass('incomplete');
        $('.step-config').removeClass('complete').addClass('current');
    }
    ordersList.reverse();
    if (ordersList.length !== 0) {
        $('#submit-process-colissimo-configuration').toggleClass('disabled');
        $('html, body').animate({scrollTop: 0}, 'slow');
        $('#colissimo-process').show(200);
        processColissimoOrders(ordersList, success, ordersList.length, form);
    } else {
        showErrorMessage(noOrdersText);
    }
}

function submitAddress(form) {
    var data = {
        controller: 'AdminColissimoAffranchissement',
        ajax: 1,
        token: token,
        action: 'addressSave'
    };

    form.find('button > i').removeClass('process-icon-save').addClass('process-icon-loading');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php?' + $.param(data),
        data: form.serialize()
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(addressErrorMessage);
    }).success(function (data) {
        var td = form.closest('tr').prev('tr').find('td.colissimo-delivery-addr');

        if (!data.error) {
            showSuccessMessage(data.message);
            td.html(data.html);
        } else {
            showErrorMessage(data.message);
        }
    }).always(function (data) {
        var idColissimoOrder = form.closest('.sub-tr').find('.btn-close').attr('data-id');
        var td = form.closest('tr').prev('tr').find('td.colissimo-delivery-addr');

        form.find('button > i').removeClass('process-icon-loading').addClass('process-icon-save');
        td.collapseDeliveryAddress(idColissimoOrder);
    });
}

function findLabel() {
    var parcelNumberInput = $('#colissimo-parcel-number-barcode');
    var inputHidden = $('input[name="parcel_number_' + parcelNumberInput.val().toUpperCase() + '"]');

    if (inputHidden.length) {
        var inputCheckbox = $('input[name="colissimo_label_' + inputHidden.val() + '"]');

        inputCheckbox.prop('checked', !inputCheckbox.prop('checked'));
        inputHidden.closest('tr').toggleClass('selected');
        parcelNumberInput.val('');
        parcelNumberInput.typeWatch({
            captureLength: 12,
            highlight: true,
            wait: 50,
            callback: function () {
                findLabel();
            }
        });
    }
}

function purgeDocuments(btn) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminColissimoAffranchissement',
            ajax: 1,
            token: token,
            action: 'purgeDocuments'
        }
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
    }).success(function (data) {
        if (data.error) {
            showErrorMessage(data.message);
        } else {
            showSuccessMessage(data.message);
            $('.colissimo-docs-alert').remove();
        }
    }).always(function (data) {
        btn.find('i').toggleClass('icon-trash icon-spin icon-spinner');
        btn.toggleClass('disabled');
    });
}

function loadColissimoServiceModalUpdate(idOrder) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminColissimoAffranchissement',
            ajax: 1,
            token: tokenAffranchissement,
            id_order: parseInt(idOrder),
            action: 'loadServiceUpdateModal'
        }
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
    }).success(function (data) {
        if (data.error) {
            showErrorMessage(data.message);
        } else {
            $(data.result_html).appendTo('body');
            $('.colissimo-back-widget').modal('show');
        }
    }).always(function (data) {
        $('.colissimo-service-selection').removeClass('disabled');
        $('.colissimo-service-selection').find('i').removeClass('icon-spin icon-spinner').addClass('icon-refresh');
    });
}

function printAllDocuments(form, btn) {
    var data = {
        controller: 'AdminColissimoLabel',
        ajax: 1,
        token: tokenLabel,
        action: 'printDocuments',
    };

    var base64string;

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php?' + $.param(data),
        data: form.serialize(),
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
        btn.toggleClass('disabled');
        btn.find('i').toggleClass('icon-print icon-spin icon-spinner');
    }).success(function (data) {
        if (data.error) {
            showErrorMessage(data.message);
        } else {
            base64string = data.file_string;
            printJS({
                printable: base64string,
                type: 'pdf',
                base64: true,
                showModal: true,
                fallbackPrintable: `data:application/pdf;base64,${data.file_string}`,
                onBrowserIncompatible: function () {
                    var data = base64string;
                    var fileName = 'Colissimo_Documents.pdf';

                    if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                        var byteCharacters = atob(data);
                        var byteNumbers = new Array(byteCharacters.length);
                        for (var i = 0; i < byteCharacters.length; i++) {
                            byteNumbers[i] = byteCharacters.charCodeAt(i);
                        }
                        var byteArray = new Uint8Array(byteNumbers);
                        var blob = new Blob([byteArray], {type: 'application/pdf'});

                        window.navigator.msSaveOrOpenBlob(blob, fileName);
                    } else {
                        window.open('data:application/pdf;base64, ' + data, '');
                    }
                }
            });
        }
    }).always(function (data) {
        btn.toggleClass('disabled');
        btn.find('i').toggleClass('icon-print icon-spin icon-spinner');
    });
}
