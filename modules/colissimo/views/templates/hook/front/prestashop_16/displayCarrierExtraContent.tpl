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

<style>
  .couleur1 {
    color: {$widget_color_1|escape:'htmlall':'utf-8'} !important;
  }

  .couleur2 {
    color: {$widget_color_2|escape:'htmlall':'utf-8'} !important;
  }

  .police {
    font-family: "{$widget_police|escape:'htmlall':'utf-8'}", sans-serif !important;
  }
</style>
<script type="text/javascript">
    var colissimoAjaxWidget = baseDir;
    var colissimoToken = '{$colissimo_widget_token|escape:'htmlall':'utf-8'}';
    var colissimoPreparationTime = {$preparation_time|intval};
    var widgetLang = "{$colissimo_widget_lang|escape:'htmlall':'utf-8'}";
    var colissimoDeliveryAddress = {
        address: "{$delivery_addr.address|strip_tags|addslashes}",
        zipcode: "{$delivery_addr.zipcode|escape:'htmlall':'utf-8'}",
        city: "{$delivery_addr.city|strip_tags|addslashes}",
        isoCountry: "{$delivery_addr.iso_country|escape:'htmlall':'utf-8'}"
    };
    $(document).on('change', 'input.delivery_option_radio', function(){
    	$('.colissimo-pickup-point-address').css('display','none');
    });
    $(document).ready(function(){
        var delivery_option_checked = $('input.delivery_option_radio:checked');
        var delivery_option = delivery_option_checked.closest('.delivery_option');
        if ($('.colissimo-pickup-point-address').size() > 0) {
	    var colissimo_widget = $(".pickup-point").html();
            $(".pickup-point").empty();
            $(colissimo_widget).insertAfter(delivery_option);
	}
        if (!jQuery('body > .colissimo-front-widget').length) {
            jQuery('.colissimo-front-widget').appendTo('body');
        } else {
            jQuery('.hook_extracarrier > .colissimo-front-widget').remove();
        }
        initMobileField();
    });

</script>

<div class="colissimo-front-widget modal fade">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <div id="colissimo-widget-container"></div>
      </div>
    </div>
  </div>
</div>
<div class="pickup-point">
    <div id="colissimo-widget" class="colissimo-pickup-point-address">
        {include file="./_partials/pickup-point-address.tpl"}
    </div>
</div>
