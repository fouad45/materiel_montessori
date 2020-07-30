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

<div class="bootstrap panel colissimo-quick-access">
  <div class="row">
    <div class="col-md-2 col-sm-6 colissimo-qa-link">
      <a href="{$link->getAdminLink('AdminColissimoDashboard')|escape:'htmlall':'utf-8'}">
        <img src="{$data['img_path']|escape:'htmlall':'utf-8'}icons/icon-dashboard.png"><span>{l s='Dashboard' mod='colissimo'}</span>
      </a>
    </div>
    {if $coliship_enabled}
      <div class="col-md-2 col-sm-6 colissimo-qa-link">
        <a href="{$link->getAdminLink('AdminColissimoColiship')|escape:'htmlall':'utf-8'}">
          <img src="{$data['img_path']|escape:'htmlall':'utf-8'}icons/icon-labels.png"><span>{l s='Coliship' mod='colissimo'}</span>
        </a>
      </div>
    {else}
      <div class="col-md-2 col-sm-6 colissimo-qa-link">
        <a href="{$link->getAdminLink('AdminColissimoAffranchissement')|escape:'htmlall':'utf-8'}">
          <img src="{$data['img_path']|escape:'htmlall':'utf-8'}icons/icon-labels.png"><span>{l s='Postage' mod='colissimo'}</span>
        </a>
      </div>
    {/if}
    <div class="col-md-2 col-sm-6 colissimo-qa-link">
        <a href="{$link->getAdminLink('AdminColissimoColiship')|escape:'htmlall':'utf-8'}&importCsv=1">
          <img src="{$data['img_path']|escape:'htmlall':'utf-8'}icons/icon-import-csv.png"><span>{l s='Shipping numbers import' mod='colissimo'}</span>
        </a>
    </div>
    <div class="col-md-2 col-sm-6 colissimo-qa-link">
      <a href="{$link->getAdminLink('AdminColissimoDepositSlip')|escape:'htmlall':'utf-8'}">
        <img src="{$data['img_path']|escape:'htmlall':'utf-8'}icons/icon-deposit-slip.png"><span>{l s='Deposit slip' mod='colissimo'}</span>
      </a>
    </div>
    <div class="col-md-2 col-sm-6 colissimo-qa-link">
        <a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'utf-8'}&configure=colissimo">
          <img src="{$data['img_path']|escape:'htmlall':'utf-8'}icons/icon-config.png"><span>{l s='Configuration' mod='colissimo'}</span>
        </a>
    </div>
    <div class="col-md-2 col-sm-6 colissimo-qa-link">
      <a href="{$data['coliview_url']|escape:'htmlall':'utf-8'}" target="_blank">
        <img src="{$data['img_path']|escape:'htmlall':'utf-8'}icons/icon-coliview.png">
      </a>
    </div>
  </div>
</div>
