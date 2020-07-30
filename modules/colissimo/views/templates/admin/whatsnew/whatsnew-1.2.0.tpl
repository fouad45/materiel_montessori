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

<p class="item">{l s='Brexit update' mod='colissimo'}</p>
<p>
  <img class="img img-responsive img-flag-uk" src="{$data.img_path|escape:'htmlall':'utf-8'}icons/icon_uk.png" />
  {l s='As you probably know, the coming withdrawal of the United Kingdom from the European Union will impact deliveries to the UK.' mod='colissimo'}<br/>
  {l s='The timeline of the Brexit being still uncertain, in order to anticipate it, the module has now a Brexit "mode" that can be enabled when the withdrawal will become effective.' mod='colissimo'}<br/>
  {l s='Please note the following changes regarding the Brexit:' mod='colissimo'}<br />
</p>
<ul>
  <li>{l s='Pickup point delivery option in UK won\'t be offered anymore' mod='colissimo'}</li>
  <li>{l s='The CN23 document will become mandatory' mod='colissimo'}</li>
</ul>
<p class="item">{l s='New Timeline to track shipments' mod='colissimo'}</p>
<p>
  {l s='A brand new tracking page is now offered to your customers.' mod='colissimo'}<br/>
  {l s='The design of the page is closer to the one on laposte.fr and display a clear and understandable timeline in 5 steps.' mod='colissimo'}<br/>
  <img class="img img-responsive img-timeline" src="{$data.img_path|escape:'htmlall':'utf-8'}whatsnew/1-2-0-timeline.png" />
</p>
<p class="item">{l s='HS Code, short description and origin country per product & category' mod='colissimo'}</p>
<p>
  {l s='The module allows you to configure customs informations at a product level and at a category level, in addition to the existing module level.' mod='colissimo'}<br/>
  {l s='For example, for a given product, HS code is taken in priority at the product level. In case the information is not filled, HS code is taken at the category of the product level.' mod='colissimo'}<br/>
  {l s='Finally, if the information is also not filled, the value setted in the configuration of the module will be used.' mod='colissimo'}<br/>
</p>
<p class="item">{l s='Ship orders from Overseas departments' mod='colissimo'}</p>
<p>
  {l s='You can ship your orders from Guadeloupe, French Guiana, Martinique, Reunion, Mayotte, Saint Pierre and Miquelon, Saint Martin or Saint Barthelemy.' mod='colissimo'}<br/>
  {l s='Shipping address can be configured in the "My account" tab.' mod='colissimo'}
</p>
<p class="item">{l s='Address for return shipments' mod='colissimo'}</p>
<p>
  {l s='A different address can be used to receive return shipments from the customers.' mod='colissimo'}<br/>
  {l s='The adress is optional and can be also configured in the "My account" tab.' mod='colissimo'}
</p>
