/**
 * 2018 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 */
$(document).ready(function() {
    $(".confirm-box").change(function() {
        if ($('.confirm-box:checked').length == $('.confirm-box').length) {
            $('#confirmation-theme').removeAttr('disabled');
        } else {
            $('#confirmation-theme').prop('disabled', true);
        }
    });
});
(function() {
    'use strict';
})();
$(document).ready(function() {
    var confirmationStep = $('#confirmation-theme') || null;
    if (confirmationStep) {
        confirmationStep.on('click', function(event) {
            var form = $('#confirmation-form');
            event.preventDefault();
            if (validate(form[0])) {
                processConfirmation();
            }
        });
    }
});
function validate(form) {
    var c = [],
        btn = [],
        r,
        invalidInput = false;
    for (var i = 0; i < form.elements.length; i++) {
        if (form.elements[i].name === '') {
            continue;
        }
        form.elements[i].parentNode.parentNode.classList.remove('has-error');
        switch (form.elements[i].nodeName) {
            case 'INPUT':
                switch (form.elements[i].type) {
                    case 'text':
                        if (!form.elements[i].value || form.elements[i].value == 'undefined') {
                            invalidInput = true;
                        }
                        break;
                    case 'email':
                        r = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                        if (!r.test(form.elements[i].value)) {
                            invalidInput = true;
                        }
                        break;
                }
                break;
        }
        if (invalidInput) {
            form.elements[i].parentNode.parentNode.classList.add('has-error');
            break;
        } else {
            invalidInput = false;
        }
    }
    if (!invalidInput) {
        return true;
    }
}
function processConfirmation() {
    var data = {
        action: 'UpdateConfirmation'
    };
    data.touchize_ps_shop_email = $("input[name='touchize_ps_shop_email']").val();
    data.touchize_ps_shop_name = $("input[name='touchize_ps_shop_name']").val();
    data.touchize_domain_name = $("input[name='touchize_domain_name']").val();
    var request = this.sendRequest(location.href.replace(/#/g, '') + '&ajax', 'POST', data, 'json');
    request.then(function(data) {
        if (!data.success) {
            displayError(data);
        } else {
            window.open(data.url, '_blank');
            location.reload();
        }
    });
}
function displayError(data) {
    $("#create-user-error").removeClass('hidden');
    $("#alert-message").html(data.errormsg);
    $("#heading-message").html(data.errormsg);
    $("#confirmation-form").addClass('hidden');
    $("#start-subscription").attr('href', data.url);
}
function sendRequest(url, type, data, dataType, file) {
    var params = {
        type: type,
        url: url,
        data: data,
    };
    if (file) {
        params.cache = false;
        params.contentType = false;
        params.processData = false;
    } else {
        params.dataType = dataType;
    }
    return $.when($.ajax(params));
}
