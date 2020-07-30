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

var colissimoBalModal = $('.colissimo-bal');

function onShowModal(idColissimoLabel) {
    if (idColissimoLabel === undefined) {
        idColissimoLabel = $(this).attr('data-colissimo-label-id');
    }

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: colissimoAjaxReturn + 'index.php',
        data: {
            fc: 'module',
            module: 'colissimo',
            controller: 'return',
            ajax: 1,
            action: 'showReturnAddress',
            id_colissimo_label: idColissimoLabel
        }
    }).fail(function (jqXHR, textStatus) {
        $('.colissimo-bal .modal-body-error').text(genericErrorMessage).show();
    }).success(function (data) {
        if (!data.error) {
            $('.colissimo-bal .modal-body-content').html(data.html);
        } else {
            $('.colissimo-bal .modal-body-error').text(data.message).show();
        }
    }).always(function (data) {
    });
}

function onHideModal() {
    $('.colissimo-bal .modal-body-content').html('');
    $('.colissimo-bal .modal-body-error').hide();
}

$(document).on('submit', '#colissimo-mailbox-return-address', function (e) {
    e.preventDefault();

    var submitBtn = $('.colissimo-submit-availability');
    var spinnerIcon = $('.colissimo-submit-availability i');
    var data = {
        fc: 'module',
        module: 'colissimo',
        controller: 'return',
        ajax: 1,
        action: 'checkAvailability'
    };

    spinnerIcon.toggleClass('icon-spinner-off')
        .toggleClass('icon-spinner-on')
        .toggleClass('icon-spin icon-spinner')
        .toggleClass('icon-check');
    submitBtn.toggleClass('disabled');
    $('.colissimo-bal .modal-body-error').hide();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: colissimoAjaxReturn + 'index.php?' + $.param(data),
        data: $('#colissimo-mailbox-return-address').serialize()
    }).fail(function (jqXHR, textStatus) {
        $('.colissimo-bal .modal-body-error').text(genericErrorMessage).show();
    }).success(function (data) {
        if (!data.error) {
            $('.colissimo-bal .modal-body-content').html(data.html);
        } else {
            $('.colissimo-bal .modal-body-error').text(data.message).show();
        }
    }).always(function (data) {
        spinnerIcon.toggleClass('icon-spinner-off')
            .toggleClass('icon-spinner-on')
            .toggleClass('icon-spin icon-spinner')
            .toggleClass('icon-check');
        submitBtn.toggleClass('disabled');
    });
});

$(document).on('submit', '#colissimo-mailbox-return-confirm', function (e) {
    e.preventDefault();

    var submitBtn = $('.colissimo-submit-confirm');
    var spinnerIcon = $('.colissimo-submit-confirm i');
    var data = {
        fc: 'module',
        module: 'colissimo',
        controller: 'return',
        ajax: 1,
        action: 'confirmPickup'
    };

    spinnerIcon.toggleClass('icon-spinner-off')
        .toggleClass('icon-spinner-on')
        .toggleClass('icon-spin icon-spinner')
        .toggleClass('icon-check');
    submitBtn.toggleClass('disabled');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: colissimoAjaxReturn + 'index.php?' + $.param(data),
        data: $('#colissimo-mailbox-return-confirm').serialize()
    }).fail(function (jqXHR, textStatus) {
        $('.colissimo-bal .modal-body-error').text(genericErrorMessage).show();
        $('.colissimo-bal .modal-body-content').html('');
    }).success(function (data) {
        if (!data.error) {
            $('#colissimo-returns').find("[data-colissimo-label-id='" + data.id_colissimo_label + "']").parent().text(data.text_result);
        }
        $('.colissimo-bal .modal-body-content').html(data.html);
    }).always(function (data) {
        spinnerIcon.toggleClass('icon-spinner-off')
            .toggleClass('icon-spinner-on')
            .toggleClass('icon-spin icon-spinner')
            .toggleClass('icon-check');
        submitBtn.toggleClass('disabled');
    });
});

colissimoBalModal.on('hide.bs.modal', onHideModal);

colissimoBalModal.on('show.bs.modal', function () {
    onShowModal($(this).attr('data-colissimo-label-id'));
});

$('.colissimo-request-pickup').on('click', function (e) {
    e.preventDefault();

    var idColissimoLabel = $(this).attr('data-colissimo-label-id');

    colissimoBalModal.attr('data-colissimo-label-id', idColissimoLabel);
    colissimoBalModal.modal('show');
});
