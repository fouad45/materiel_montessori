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

{capture name=path}
  <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
    {l s='My account' mod='colissimo'}
  </a>
  <span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'utf-8'}</span>
  <span class="navigation_page">{l s='Colissimo shipment tracking' mod='colissimo'}</span>
{/capture}
<h1 class="page-heading bottom-indent">{l s='Colissimo shipment tracking' mod='colissimo'}</h1>

{if $no_labels}
  <div class="alert alert-info">
    {l s='There are no Colissimo shipments yet. Please come back later.' mod='colissimo'}
  </div>
{else}
  <div class="colisismo-tracking-loader">
    <p>{l s='Please wait while we are retrieving last tracking details...' mod='colissimo'}</p>
    <img src="{$colissimo_img_path|escape:'htmlall':'utf-8'}loading_tracking.svg"/>
  </div>
  <div class="colissimo-error" style="display: none;">
    <div class="alert alert-danger">
      <p>{l s='An error occurred while retrieving tracking details. Please try again later.' mod='colissimo'}</p>
    </div>
  </div>
  <div id="colissimo-timeline" class="colissimo-shipments colissimo-16" style="display: none;"></div>
{/if}

{literal}
  <script type="text/javascript">
    var colissimoAjaxTracking = baseDir;
    var colissimoTrackingReference = "{/literal}{$order_reference|escape:'htmlall':'utf-8'}{literal}";
    var colissimoTrackingHash = "{/literal}{$order_hash|escape:'htmlall':'utf-8'}{literal}";
    var noLabels = {/literal}{$no_labels|intval}{literal};
  </script>
{/literal}
