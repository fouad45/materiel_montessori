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

<div class="alert alert-info colissimo-dashboard-intro">
  <p>{l s='On this page, you can find tracking of your orders shipped with Colissimo.' mod='colissimo'} {l s='The flag' mod='colissimo'}
    <i class="icon icon-warning"></i> {l s='is shown when an anomaly is detected in one the shipments.' mod='colissimo'}
  </p>
  <p>{l s='Please note that delivered orders are shown for a period of 15 days.' mod='colissimo'}</p>
  <p class="colissimo-tracking-update">
    <b>{l s='Tracking update:' mod='colissimo'}</b><br />
    {l s='Orders statuses are updated automatically when you open the dashboard.' mod='colissimo'}
    {l s='Please note that a delay of 2 hours is needed before automatically updating the statuses again.' mod='colissimo'}
  </p>
  <p>{l s='Statuses can also be updated manually using the button below :' mod='colissimo'}</p>
  <form method="post" id="colissimoUpdateAllTracking">
    <button type="submit" name="submitUpdateAllTracking" class="btn btn-primary">
      <i class="icon icon-refresh"></i> {l s='Update all trackings' mod='colissimo'}
    </button>
  </form>
</div>
