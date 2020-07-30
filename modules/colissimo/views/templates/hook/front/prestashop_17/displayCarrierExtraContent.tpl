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
    color: {$widget_color_1}!important;
  }

  .couleur2 {
    color: {$widget_color_2}!important;
  }

  .police {
    font-family: "{$widget_police}", sans-serif!important;
  }
</style>
<script type="text/javascript">
    var colissimoAjaxWidget = prestashop.urls.base_url;
    var colissimoToken = '{$colissimo_widget_token}';
    var colissimoPreparationTime = {$preparation_time};
    var widgetLang = "{$colissimo_widget_lang}";
    var colissimoDeliveryAddress = {
        address: '{$delivery_addr.address|strip_tags|addslashes nofilter}',
        zipcode: '{$delivery_addr.zipcode}',
        city: '{$delivery_addr.city|strip_tags|addslashes nofilter}',
        isoCountry: '{$delivery_addr.iso_country}'
    };
</script>

<div class="colissimo-front-widget colissimo-front-widget-17 modal fade" style="display:none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' mod='colissimo'}">
          <span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title h6">{l s='Pickup point selection' mod='colissimo'}</h4>
      </div>
      <div class="modal-body">
        <div id="colissimo-widget-container"></div>
      </div>
    </div>
  </div>
</div>
<div class="colissimo-pickup-point-address">
  {include file="./_partials/pickup-point-address.tpl"}
</div>
