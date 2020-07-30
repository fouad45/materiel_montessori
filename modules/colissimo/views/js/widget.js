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

var iti;

function initMobileField() {
    var input = document.querySelector("#colissimo-pickup-mobile-phone");
    var onlyCountries;
    var allowDropDown;
    var isoDelivery;

    if (typeof colissimoDeliveryAddress === 'undefined') {
        return;
    }
    isoDelivery = colissimoDeliveryAddress['isoCountry'];
    if (isoDelivery == 'MC') {
        isoDelivery = 'FR';
    }
    if (isoDelivery == 'FR') {
        onlyCountries = ['FR'];
        allowDropDown = false;
    } else if (isoDelivery == 'BE') {
        onlyCountries = ['BE'];
        allowDropDown = false;
    } else {
        onlyCountries = ['AD', 'AT', 'BE', 'DE', 'ES', 'FR', 'EE', 'GB', 'LT', 'LU', 'LV', 'NL', 'PL', 'PT', 'SE', 'DK', 'FI', 'CZ', 'SK', 'SI'];
        allowDropDown = true;
    }

    if (input !== null) {
        iti = window.intlTelInput(input, {
            utilsScript: colissimoAjaxWidget + 'modules/colissimo/views/js/utils.js',
            initialCountry: isoDelivery,
            nationalMode: true,
            separateDialCode: true,
            hiddenInput: 'full',
            preferredCountries: [],
            onlyCountries: onlyCountries,
            allowDropdown: allowDropDown,
        });
        var handleChange = function () {
            if (iti.isValidNumber()) {
                $('.js-colissimo-mobile-valid').show();
                $('.js-colissimo-mobile-invalid').hide();
                $('.js-colissimo-is-mobile-valid').val('1');
            } else {
                $('.js-colissimo-mobile-valid').hide();
                $('.js-colissimo-mobile-invalid').show();
                $('.js-colissimo-is-mobile-valid').val('0');
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

function callBackFrame(point) {
    var infoPoint = new Object({
        colissimo_id: point['identifiant'],
        company_name: point['nom'],
        address1: point['adresse1'],
        address2: point['adresse2'],
        address3: point['adress3'],
        city: point['localite'],
        zipcode: point['codePostal'],
        country: point['libellePays'],
        iso_country: point['codePays'],
        product_code: point['typeDePoint'],
        network: point['reseau']
    });
    var mobilePhoneSave;

    if (iti === undefined || !iti.isValidNumber()) {
        mobilePhoneSave = '';
    } else {
        mobilePhoneSave = iti.getNumber();
    }
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: colissimoAjaxWidget + 'index.php',
        data: {
            fc: 'module',
            module: 'colissimo',
            controller: 'widget',
            ajax: 1,
            action: 'selectPickupPoint',
            infoPoint: JSON.stringify(infoPoint),
            mobilePhone: mobilePhoneSave
        }
    }).fail(function (jqXHR, textStatus) {
    }).success(function (data) {
        jQuery('.colissimo-pickup-point-address').html(data.html_result);
        jQuery('#colissimo-pickup-mobile-phone').val(mobilePhoneSave);
        initMobileField();
    }).always(function (data) {
        jQuery('.colissimo-front-widget').modal('hide');
        jQuery('#checkout-delivery-step').addClass('-current js-current-step');
    });
}

jQuery(document).on('click', '#colissimo-pickup-point-address-selected a, #colissimo-select-pickup-point', function () {
    var countryList;

    if (colissimoDeliveryAddress['isoCountry'] == 'MC') {
        countryList = 'FR';
    } else {
        countryList = colissimoDeliveryAddress['isoCountry'];
    }

    jQuery('#colissimo-widget-container').frameColissimoOpen({
        "ceLang": widgetLang,
        "callBackFrame": 'callBackFrame',
        "URLColissimo": "https://ws.colissimo.fr",
        "ceCountryList": countryList,
        "ceCountry": countryList,
        "dyPreparationTime": colissimoPreparationTime,
        "ceAddress": colissimoDeliveryAddress['address'],
        "ceZipCode": colissimoDeliveryAddress['zipcode'],
        "ceTown": colissimoDeliveryAddress['city'],
        "token": colissimoToken
    });
    jQuery('.colissimo-front-widget').modal('show');
});

jQuery(document).on('click', '#colissimo-opc-phone-validation', function () {
    var mobilePhoneSave = iti.getNumber();
    var isMobileValid = iti.isValidNumber();
    var btnValidation = jQuery('#colissimo-opc-phone-validation');
    var result = jQuery('.js-colissimo-mobile-validation');

    if (mobilePhoneSave === undefined) {
        mobilePhoneSave = '';
    }

    btnValidation.find('i').removeClass('icon-check').addClass('icon-spinner icon-spin');
    result.removeClass('colissimo-mobile-validation-success').removeClass('colissimo-mobile-validation-error').text('');

    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: colissimoAjaxWidget + 'index.php',
        data: {
            fc: 'module',
            module: 'colissimo',
            controller: 'widget',
            ajax: 1,
            action: 'saveMobilePhoneOpc',
            mobilePhone: mobilePhoneSave,
            isMobileValid: isMobileValid ? 1 : 0,
        }
    }).fail(function (jqXHR, textStatus) {
    }).success(function (data) {
        result.text(data.text_result);
        if (!data.errors) {
            result.addClass('colissimo-mobile-validation-success');
            location.reload(true);
        } else {
            result.addClass('colissimo-mobile-validation-error');
        }
    }).always(function (data) {
        btnValidation.find('i').addClass('icon-check').removeClass('icon-spinner icon-spin');
    });

});

$(document).ready(function () {
    var colissimoFrontWidget17 = jQuery('.colissimo-front-widget-17');

    colissimoFrontWidget17.appendTo('body');
    if (colissimoFrontWidget17.size() > 0) {
        iti = initMobileField();
    }
});
