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

<form method="post" class="form-horizontal" id="colissimo-coliship-form" enctype="multipart/form-data" novalidate>
  <div class="colissimo-coliship panel collapse in">
    {if isset($import_csv) && $import_csv}
        {include file="./_partials/import-shipping-numbers-help.tpl"}
        {include file="./_partials/shipping-numbers-upload.tpl"}
    {else}
    <div class="coliship-step">
      <p class="coliship-step-title">{l s='1- Launch your Coliship plugin' mod='colissimo'}</p>
      <p class="coliship-step-subtitle">{l s='If you don\'t have the plugin installed, you can download it in the Coliship interface' mod='colissimo'}</p>
    </div>
    <div class="coliship-step">
      <p class="coliship-step-title">{l s='2- Download orders that are ready to be shipped via Coliship' mod='colissimo'}</p>
      <p class="coliship-step-subtitle">
        {l s='Orders that currently have one of the statuses configured in the' mod='colissimo'}
        <a target="_blank" href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'utf-8'}&configure=colissimo">{l s='"Back-office settings"' mod='colissimo'}</a>
        {l s='section of the module will be included.' mod='colissimo'}
      </p>
      <a href="{$link->getAdminLink('AdminColissimoColiship')|escape:'htmlall':'utf-8'}&action=exportCsv"
         class="btn btn-primary">
        <i class="process-icon- process-icon-download-alt"></i> {l s='Download .csv file' mod='colissimo'}
      </a>
      <p class="coliship-tips">
        <i class="icon icon-lightbulb"></i>
        {l s='Tips: if you use the scan import method, right click on the button and choose "Save link as" to download the file directly in the right directory' mod='colissimo'}
      </p>
    </div>
    <div class="coliship-step">
      <p class="coliship-step-title">{l s='3- Navigate to Coliship web interface to import and print labels ("Automated" menu)' mod='colissimo'}</p>
      <a href="{$coliship_url|escape:'htmlall':'utf-8'}" target="_blank" class="btn btn-primary btn-coliship">
        {l s='Go to Coliship' mod='colissimo'} <i class="icon icon-chevron-right"></i>
      </a>
      <div class="alert alert-info">
        {l s='Before you start importing orders in Coliship, make sure you configured the format of import and export as stated in the documentation.' mod='colissimo'}
        <br/>
        {l s='You should upload it in the Coliship interface, "Settings" menu, then "Import" tab.' mod='colissimo'}<br/><br/>
        <i class="icon icon-download-alt"></i>
        <a href="{$admin_url|escape:'htmlall':'utf-8'}&action=downloadFmt"
           target="_blank">
          {l s='Download .fmt file' mod='colissimo'}
        </a>
      </div>
    </div>
    <div class="coliship-step">
      <p class="coliship-step-title">{l s='4- Import the .csv file generated' mod='colissimo'}</p>
      <p class="coliship-step-subtitle">{l s='The .csv file can be found in the location that you configured in Coliship (menu "Settings > Export")' mod='colissimo'}</p>
      {include file="./_partials/shipping-numbers-upload.tpl"}
    </div>
    {/if}
  </div>
</form>
