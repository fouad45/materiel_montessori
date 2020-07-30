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

<div class="colissimo-migration-step2">
  <p class="migration-intro">
    {l s='Please wait modules data are migrating...' mod='colissimo'}
  </p>
  <ul class="steps">
    <li class="step-credentials">
      <i class="icon icon-check"></i>
      {l s='Retrieving credentials' mod='colissimo'}
    </li>
    <li class="step-carriers">
      <i class="icon icon-check"></i>
      {l s='Retrieving carriers pricing' mod='colissimo'}
    </li>
    <li class="step-configuration">
      <i class="icon icon-check"></i>
      {l s='Updating configuration' mod='colissimo'}
    </li>
    <li class="step-data">
      <i class="icon icon-check"></i>
      {l s='Updating existing data' mod='colissimo'}
    </li>
    <li class="step-documents">
      <i class="icon icon-check"></i>
      {l s='Copying shipments documents' mod='colissimo'}
    </li>
  </ul>
  <div id="colissimo-migration-result"></div>
</div>
