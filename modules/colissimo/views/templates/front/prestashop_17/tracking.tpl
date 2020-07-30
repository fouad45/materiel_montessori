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

{extends file='page.tpl'}

{block name='page_title'}
  {l s='Colissimo shipment tracking for order ' mod='colissimo'}
  {$order_reference}
{/block}

{block name='page_content_container'}
  {if $no_labels}
    <div class="alert alert-info">
      {l s='There are no Colissimo shipments yet. Please come back later.' mod='colissimo'}
    </div>
  {else}
    <div class="colisismo-tracking-loader">
      <p>{l s='Please wait while we are retrieving last tracking details...' mod='colissimo'}</p>
      <img src="{$colissimo_img_path}loading_tracking.svg"/>
    </div>
    <div class="colissimo-error" style="display: none;">
      <div class="alert alert-danger">
        <p>{l s='An error occurred while retrieving tracking details. Please try again later.' mod='colissimo'}</p>
      </div>
    </div>
    <div id="colissimo-timeline" class="colissimo-shipments" style="display: none;"></div>
  {/if}

{literal}
  <script type="text/javascript">
      var colissimoAjaxTracking = prestashop.urls.base_url;
      var colissimoTrackingReference = "{/literal}{$order_reference}{literal}";
      var colissimoTrackingHash = "{/literal}{$order_hash}{literal}";
      var noLabels = {/literal}{$no_labels}{literal};
  </script>
{/literal}
{/block}
