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

function createScriptElement(e, t) {
    var i = document.createElement("script");
    return i.type = "text/javascript", i.src = e, i.defer = t, i
}

jQuery.extend(jQuery.fn, {
    frameColissimoOpen: function (params) {
        var returnCode = 0;
        var bstrap = createScriptElement(params.URLColissimo + '/widget-point-retrait/resources/js/bootstrap.min.js', true);
        var scroll = createScriptElement(params.URLColissimo + '/widget-point-retrait/resources/js/jquery.jscrollpane.min.js', true);
        var mouse = createScriptElement(params.URLColissimo + '/widget-point-retrait/resources/js/jquery.mousewheel.js', true);
        var scrollbar = createScriptElement(params.URLColissimo + '/widget-point-retrait/resources/js/jquery.scrollbar.js', true);
        var mapbox = createScriptElement('https://api.mapbox.com/mapbox.js/v2.2.1/mapbox.js', true);
        var widgetUrl = params.URLColissimo + '/widget-point-retrait/index.htm';

        if (params.ceCountryList == null || params.ceCountryList == '') {
            returnCode = 10;
        }
        if (params.ceCountry == null || params.ceCountry == '') {
            returnCode = 20;
        }
        if (params.ceLang == null || params.ceLang == '') {
            returnCode = 30;
        }
        if (params.dyPreparationTime == null || params.dyPreparationTime == '') {
            returnCode = 40;
        }

        $('head').append(mapbox);
        $('head').append('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
        $('head').append('<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />');
        $('head').append('<meta name="apple-mobile-web-app-capable" content="yes">');
        $('head').append(bstrap);
        $('head').append(scroll);
        $('head').append(mouse);
        $('head').append(scrollbar);
        $.ajax({
            method: 'POST',
            url: widgetUrl,
            data: 'h1=' + params.ceLang + '&callBackFrame=' + params.callBackFrame + '&domain=' + params.URLColissimo + '&ceCountryList=' + params.ceCountryList + '&codeRetour=' + returnCode + '&dyPreparationTime=' + params.dyPreparationTime + '&ceCountry=' + params.ceCountry + '&ceZipCode=' + params.ceZipCode + '&token=' + params.token,
            success: function (data) {
                $('#colissimo-widget-container').html(
                    data.replace('var colissimojQuery = jQuery.noConflict();', 'var colissimojQuery = jQuery;')
                );
                if (params.ceCountry != null && params.ceCountry != '') {
                    setTimeout(function () {
                        jQuery.ajax({
                            type: 'POST',
                            encoding: 'UTF-8',
                            contentType: 'application/x-www-form-urlencoded; charset=utf-8',
                            url: params.URLColissimo + '/widget-point-retrait/GetPays.htm',
                            data: 'lang=' + params.ceLang + '&ceCountryList=' + params.ceCountryList + '&token=' + params.token,
                            success: function (msg) {
                                var data = msg.split(';');
                                for (var i = 0; i < data.length - 1; i++) {
                                    if (data[i] == params.ceCountry) {
                                        jQuery('#colissimo_widget_listePays').append('<option value="' + data[i] + '" selected="selected">' + data[i + 1] + '</option>');
                                    } else {
                                        jQuery('#colissimo_widget_listePays').append('<option value="' + data[i] + '">' + data[i + 1] + '</option>');
                                    }
                                    i++;
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {

                            }
                        });
                    }, 1000)
                }
                setTimeout(function () {
                    if (params.ceAddress != null && params.ceAddress != '') {
                        jQuery('#colissimo_widget_Adresse1').val(params.ceAddress);
                    }
                    if (params.ceZipCode != null && params.ceZipCode != '') {
                        jQuery('#colissimo_widget_CodePostal').val(params.ceZipCode);
                    }
                    if (params.ceTown != null && params.ceTown != '') {
                        jQuery('#colissimo_widget_Ville').val(params.ceTown);
                        setTimeout(function () {
                            colissimo_widget_getPointsRetrait();
                        }, 1000)
                    }
                }, 500)
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return this;
    },
    frameColissimoClose: function () {
        return this;
    }
});
