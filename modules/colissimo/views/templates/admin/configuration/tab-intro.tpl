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

<div class="row colissimo-background">
  <div class="col-lg-12 col-md-12">
    <h1>{l s='Colissimo Official' mod='colissimo'}</h1>
    <p class="text-intro">
      {l s='Offer your customers the services of Colissimo France\'s No. 1 home delivery service.' mod='colissimo'}<br/>
      {l s='La Poste Group\'s parcel delivery operator, Colissimo helps you in managing your shipments in France and abroad.' mod='colissimo'}
    </p>
    <p class="colissimo-link">
      <a href="{$colissimo_links.delivery_details|escape:'htmlall':'utf-8'}"
         target="_blank">
        {l s='More details on our delivery solutions ' mod='colissimo'}
      </a>
    </p>
    <div class="colissimo-step">
      <img src="{$colissimo_img_path|escape:'htmlall':'utf-8'}intro/colissimo-1.png"/>
      <p class="step-title">{l s='A contract is required to use this module' mod='colissimo'}</p>
      <p class="step-text">{l s='This module works for clients under "Facilité" & "Privilège" contract.' mod='colissimo'}</p>
    </div>
    <a href="{$colissimo_links.subscribe|escape:'htmlall':'utf-8'}"
       class="btn colissimo-btn">
      {l s='Subscribe to Colissimo' mod='colissimo'}
    </a>
    <div class="colissimo-step">
      <img src="{$colissimo_img_path|escape:'htmlall':'utf-8'}intro/colissimo-2.png"/>
      <p class="step-title">{l s='Synchronize your store with Colissimo services by logging on the following page' mod='colissimo'}</p>
    </div>
    <div class="colissimo-step">
      <img src="{$colissimo_img_path|escape:'htmlall':'utf-8'}intro/colissimo-3.png"/>
      <p class="step-title">{l s='Set up and customize your PUDO display' mod='colissimo'}</p>
      <p class="step-text">{l s='This module works for clients under "Facilité" & "Privilège" contract.' mod='colissimo'}</p>
    </div>
    <div class="colissimo-step">
      <img src="{$colissimo_img_path|escape:'htmlall':'utf-8'}intro/colissimo-4.png"/>
      <p class="step-title">{l s='Save time in your order management' mod='colissimo'}</p>
      <ul>
        <li>{l s='Edit labels from your merchant back office' mod='colissimo'}</li>
        <li>{l s='Export your orders in Coliship' mod='colissimo'}</li>
        <li>{l s='Ready-to-print customs documents for your internal shipments' mod='colissimo'}</li>
        <li>{l s='Enable Colissimo return label service' mod='colissimo'}</li>
        <li>{l s='Edit your deposit slip' mod='colissimo'}</li>
      </ul>
    </div>
  </div>
</div>
