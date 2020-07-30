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

var iti_sender
var iti_return

function toggleLabelGenerationMode() {
    if ($('#COLISSIMO_GENERATE_LABEL_PRESTASHOP_on').prop('checked')) {
        $('.colissimo-label-generation-mode-inputs').show(400);
    } else {
        $('.colissimo-label-generation-mode-inputs').hide(200);
    }
}

function toggleReturnOption() {
    if ($('#COLISSIMO_ENABLE_RETURN_on').prop('checked')) {
        $('.colissimo-enable-return-inputs').show(400);
    } else {
        $('.colissimo-enable-return-inputs').hide(200);
    }
}

function toggleReturnAddress() {
    if ($('#COLISSIMO_USE_RETURN_ADDRESS_on').prop('checked')) {
        $('#colissimo-return-address').show(400);
    } else {
        $('#colissimo-return-address').hide(200);
    }
}

function testWSCredentials(colissimoCheckCredentials) {
    var data = {
        controller: 'AdminColissimoTestCredentials',
        ajax: 1,
        token: colissimoCredentialsToken,
        action: 'testWSCredentials'
    };

    colissimoCheckCredentials.find('i').attr('class', 'icon icon-spin icon-spinner');
    colissimoCheckCredentials.addClass('disabled');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php?' + $.param(data),
        data: $('#colissimo-account-config-form').serialize()
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
    }).success(function (data) {
        if (data.errors !== true) {
            showSuccessMessage(data.message);
        } else {
            showErrorMessage(data.message);
        }
    }).always(function (data) {
        colissimoCheckCredentials.find('i').attr('class', 'icon icon-check');
        colissimoCheckCredentials.removeClass('disabled');
    });
}

function testWidgetCredentials(checkWidget) {
    var data = {
        controller: 'AdminColissimoTestCredentials',
        ajax: 1,
        token: colissimoCredentialsToken,
        action: 'testWidgetCredentials'
    };

    checkWidget.find('i').attr('class', 'icon icon-spin icon-spinner');
    checkWidget.addClass('disabled');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php?' + $.param(data),
        data: $('#colissimo-widget-config-form, #colissimo-account-config-form').serialize()
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
    }).success(function (data) {
        if (data.errors !== true) {
            showSuccessMessage(data.message);
        } else {
            showErrorMessage(data.message);
        }
    }).always(function (data) {
        checkWidget.find('i').attr('class', 'icon icon-check');
        checkWidget.removeClass('disabled');
    });
}

function bindSwapSaveColissimo() {
    if ($('#selectedSwap option').length !== 0)
        $('#selectedSwap option').attr('selected', 'selected');
    else
        $('#availableSwap option').attr('selected', 'selected');
}

function bindSwapButtonColissimo(prefix_button, prefix_select_remove, prefix_select_add) {
    $('#' + prefix_button + 'Swap').on('click', function (e) {
        e.preventDefault();
        $('#' + prefix_select_remove + 'Swap option:selected').each(function () {
            $('#' + prefix_select_add + 'Swap').append("<option value='" + $(this).val() + "'>" + $(this).text() + "</option>");
            $(this).remove();
        });
        $('#selectedSwap option').prop('selected', true);
    });
}

function processMigrationStep(processes, modulesToMigrate) {
    var process = processes.pop();
    var step = $('.colissimo-migration-step2 .step-' + process);

    if (process) {
        step.toggleClass('current');
        step.find('i').toggleClass('icon-spinner icon-spin icon-check');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoMigration',
                ajax: 1,
                token: colissimoTokenMigration,
                action: 'migrateStep',
                step: process,
                modules_to_migrate: modulesToMigrate
            }
        }).fail(function (jqXHR, textStatus) {
        }).success(function (data) {
            step.toggleClass('current done');
            step.find('i').toggleClass('icon-spinner icon-spin icon-check');
            processMigrationStep(processes, modulesToMigrate);
        }).always(function (data) {
        });
    } else {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoMigration',
                ajax: 1,
                token: colissimoTokenMigration,
                action: 'endMigration',
                modules_to_migrate: modulesToMigrate
            }
        }).fail(function (jqXHR, textStatus) {
        }).success(function (data) {
            $('#colissimo-migration-result').html(data.html_result);
        }).always(function (data) {
        });
    }
}

function startMigration(modulesToMigrate) {
    var processes = ['credentials', 'carriers', 'configuration', 'data', 'documents'];

    processes.reverse();
    processMigrationStep(processes, modulesToMigrate);
}

function countDown($source, $target) {
    var max = $source.attr("data-maxchar");
    $target.html(max - $source.val().length);

    $source.keyup(function () {
        $target.html(max - $source.val().length);
    });
}

function initSenderPhoneField(iso) {
    var input = document.querySelector('#sender-phone');
    var element = $('#sender-phone');

    iti_sender = initPhoneField(iti_sender, input, element, iso);
}

function initReturnPhoneField(iso) {
    var input = document.querySelector("#return-phone");
    var element = $("#return-phone");

    iti_return = initPhoneField(iti_return, input, element, iso);
}

function initPhoneField(iti, input, element, iso) {
    var allowDropDown = true;

    if (input !== null) {
        iti = window.intlTelInput(input, {
            utilsScript: baseDir + 'modules/colissimo/views/js/utils.js',
            initialCountry: iso,
            nationalMode: true,
            separateDialCode: true,
            hiddenInput: 'full',
            preferredCountries: [],
            onlyCountries: onlyCountries,
            allowDropdown: allowDropDown,
        });
        var handleChange = function () {
            if (iti.isValidNumber()) {
                element.closest('.adr-form').find('.js-colissimo-mobile-valid').show();
                element.closest('.adr-form').find('.js-colissimo-mobile-invalid').hide();
                element.closest('.adr-form').find('.js-colissimo-is-mobile-valid').val('1');
            } else {
                element.closest('.adr-form').find('.js-colissimo-mobile-valid').hide();
                element.closest('.adr-form').find('.js-colissimo-mobile-invalid').show();
                element.closest('.adr-form').find('.js-colissimo-is-mobile-valid').val('0');
            }
        };
        input.addEventListener('change', handleChange);
        input.addEventListener('keyup', handleChange);
        input.addEventListener('countrychange', function () {
            handleChange();
        });
        iti.promise.then(function () {
            handleChange();
        });
        return iti;
    }
}

function processUseReturnAddress(val) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminModules',
            ajax: 1,
            configure: 'colissimo',
            token: token,
            action: 'useReturnAddress',
            returnAddress: val,
        }
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(addressErrorMessage);
    }).success(function (data) {
        showSuccessMessage(data.message);
    });
}

$(document).ready(function () {
    if (!$("#sender-company").length) {
        return;
    }

    if (!$("#return-company").length) {
        return;
    }

    initSenderPhoneField(colissimoSenderPhone);
    initReturnPhoneField(colissimoReturnPhone);

    countDown($("#sender-company"), $("#sender-company_counter"));
    countDown($("#sender-lastname"), $("#sender-lastname_counter"));
    countDown($("#sender-firstname"), $("#sender-firstname_counter"));
    countDown($("#sender-address1"), $("#sender-address1_counter"));
    countDown($("#sender-address2"), $("#sender-address2_counter"));
    countDown($("#sender-address3"), $("#sender-address3_counter"));
    countDown($("#sender-address4"), $("#sender-address4_counter"));
    countDown($("#sender-city"), $("#sender-city_counter"));
    countDown($("#sender-email"), $("#sender-email_counter"));

    countDown($("#return-company"), $("#return-company_counter"));
    countDown($("#return-lastname"), $("#return-lastname_counter"));
    countDown($("#return-firstname"), $("#return-firstname_counter"));
    countDown($("#return-address1"), $("#return-address1_counter"));
    countDown($("#return-address2"), $("#return-address2_counter"));
    countDown($("#return-address3"), $("#return-address3_counter"));
    countDown($("#return-address4"), $("#return-address4_counter"));
    countDown($("#return-city"), $("#return-city_counter"));
    countDown($("#return-email"), $("#return-email_counter"));

    toggleLabelGenerationMode();
    toggleReturnOption();
    toggleReturnAddress();

    $(document).on('click', '.colissimo-generate-label-prestashop span', function () {
        toggleLabelGenerationMode();
    });
    $(document).on('click', '.colissimo-enable-return span', function () {
        toggleReturnOption();
    });
    $('input[type=radio][name=COLISSIMO_USE_RETURN_ADDRESS]').change(function () {
        var useReturnAddress = $('input[name=COLISSIMO_USE_RETURN_ADDRESS]:checked').val();

        toggleReturnAddress();
        processUseReturnAddress(useReturnAddress);
    });
    $('select[name="sender_country"]').change(function () {
        if ($(this).val() == 'MC') {
            iti_sender.setCountry('FR');
        } else {
            iti_sender.setCountry($(this).val());
        }
    });
    $('select[name="return_country"]').change(function () {
        if ($(this).val() == 'MC') {
            iti_return.setCountry('FR');
        } else {
            iti_return.setCountry($(this).val());
        }
    });
    if (typeof $('#addSwap') !== undefined && typeof $("#removeSwap") !== undefined &&
        typeof $('#selectedSwap') !== undefined && typeof $('#availableSwap') !== undefined) {
        bindSwapButtonColissimo('add', 'available', 'selected');
        bindSwapButtonColissimo('remove', 'selected', 'available');

        $('button:submit').click(bindSwapSaveColissimo());
    }

    $('#colissimo-modal-upgrade').modal('show');
    $('#colissimo-modal-upgrade').on('hide.bs.modal', function (e) {
        if ($('#colissimo-reopen-modal').prop('checked')) {
            $('#colissimo-reopen-modal-form').submit();
        }
    });
});
