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

<p class="item">{l s='Order association & Change of Colissimo service' mod='colissimo'}</p>
<p>
  {l s='From an order page, you can now associate an order placed with another carrier to the module.' mod='colissimo'}<br/>
  {l s='You will be able to select the Colissimo service (with or without proof of delivery, pickup points) during the association.' mod='colissimo'}<br/>
  {l s='When generating your labels, a new button will allow you to change the service with which you want to ship the order.' mod='colissimo'}
</p>
<p class="item">{l s='New status after label and deposit slip generation' mod='colissimo'}</p>
<p>
  {l s='Two new statuses are available: "Shipping in progress" and "Handled by carrier".' mod='colissimo'}<br/>
  {l s='From the module configuration, you can choose weither or not you want to update the order statuses after label and deposit slip generation.' mod='colissimo'}
</p>
<p class="item">{l s='Deleting labels' mod='colissimo'}</p>
<p>
  {l s='Inside the dashboard and inside every order page, a new button to delete labels is displayed next to the shipment numbers.' mod='colissimo'}<br/>
  {l s='Please note that deleting a label will also delete the associated return label and custom documents like CN23.' mod='colissimo'}
  <img class="img img-responsive" src="{$data.img_path|escape:'htmlall':'utf-8'}whatsnew/1-1-0-delete-label.png" />
</p>
<p class="item">{l s='Display of shipment insurance' mod='colissimo'}</p>
<p>
  {l s='Inside the dashboard and inside every order page, an information about the insurance of shipments is now displayed.' mod='colissimo'}<br/>
</p>
