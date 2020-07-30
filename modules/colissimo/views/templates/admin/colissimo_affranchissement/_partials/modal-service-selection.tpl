{*
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author     PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2019 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="bootstrap colissimo-back-widget modal fade">
  <div class="modal-dialog modal-lg">
    <form class="form-horizontal colissimo-form-update-service" method="post">
      <input type="hidden" name="upd_id_order" value="{$id_order|intval}"/>
      <div class="modal-content step-selection">
        {include file="./modal-service-selection-form.tpl"}
      </div>
      <div id="colissimo-widget-container" class="modal-content step-widget" style="display: none">
      </div>
    </form>
  </div>
  <script type="text/javascript">
    {literal}
    var colissimoToken = '{/literal}{$colissimo_widget_token|escape:'htmlall':'utf-8'}{literal}';
    var colissimoPreparationTime = {/literal}{$preparation_time|intval}{literal};
    var widgetLang = "{/literal}{$colissimo_widget_lang|escape:'htmlall':'utf-8'}{literal}";
    var colissimoDeliveryAddress = {
        address: "{/literal}{$delivery_addr.address|strip_tags|addslashes}{literal}",
        zipcode: "{/literal}{$delivery_addr.zipcode|escape:'htmlall':'utf-8'}{literal}",
        city: "{/literal}{$delivery_addr.city|strip_tags|addslashes}{literal}",
        isoCountry: "{/literal}{$delivery_addr.iso_country|escape:'htmlall':'utf-8'}{literal}"
    };
    var phones = [{"mask": "\+\(##\)########[#][#][#][#]"}];

    $('.colissimo-pickup-point-selection').hide();
    $('#colissimo-service-to-associate').on('change', function () {
        if (parseInt($(this).val()) == 0) {
            $('.colissimo-pickup-point-selection').show();
        } else {
            $('.colissimo-pickup-point-selection').hide();
        }
    });

    $('.colissimo-back-widget').on('hidden.bs.modal', function () {
        $(this).remove();
    });

    $('.colissimo-pickup-point-btn').off('click').on('click', function (e) {
        e.preventDefault();

        $('.colissimo-back-widget .modal-content.step-selection').hide();
        $('.colissimo-back-widget .modal-content.step-widget').show();
        $('.colissimo-widget-container').frameColissimoOpen({
            "ceLang": widgetLang,
            "callBackFrame": 'callBackFrame',
            "URLColissimo": "https://ws.colissimo.fr",
            "ceCountryList": colissimoDeliveryAddress['isoCountry'],
            "ceCountry": colissimoDeliveryAddress['isoCountry'],
            "dyPreparationTime": colissimoPreparationTime,
            "ceAddress": colissimoDeliveryAddress['address'],
            "ceZipCode": colissimoDeliveryAddress['zipcode'],
            "ceTown": colissimoDeliveryAddress['city'],
            "token": colissimoToken
        });
        $('.colissimo-back-widget').removeClass('bootstrap');
    });

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

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoAffranchissement',
                ajax: 1,
                token: tokenAffranchissement,
                action: 'selectPickupPoint',
                infoPoint: JSON.stringify(infoPoint),
            }
        }).fail(function (jqXHR, textStatus) {
        }).success(function (data) {
            $('.colissimo-back-widget .modal-content.step-selection .pickup-point-selected').html(data.result_html);
        }).always(function (data) {
            $('.colissimo-back-widget').addClass('bootstrap');
            $('.colissimo-back-widget .modal-content.step-selection').show();
            $('.colissimo-back-widget .modal-content.step-widget').hide();
            $('#colissimo-widget-container').html('');
        });
    }

    {/literal}
  </script>
</div>
