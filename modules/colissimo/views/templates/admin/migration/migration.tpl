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

<div id="colissimo-migration">
  <ps-panel>
    <div class="row">
      <p class="colissimo-migration-title">{l s='Migration from other Colissimo modules' mod='colissimo'}</p>
      <div id="colissimo-migration-content">
        <div class="colissimo-migration-step1">
          <div class="alert alert-warning">
            {l s='Before starting a migration, make sure your Colissimo contract allows you to use webservices. If in doubt, please contact your Colissimo sales representative.' mod='colissimo'}<br/>
            {l s='Modules listed below and their associated carriers will simply be disabled.' mod='colissimo'}<br/>
            {l s='Afterwards, you should check carriers prices, module configuration and set your account type(s) into "My Colissimo Account" tab.' mod='colissimo'}<br/>
          </div>
          <p class="colissimo-maintenance">
            <strong>
              <i class="icon icon-warning-sign"></i>
              {l s='We strongly advise you to turn the maintenance mode on.' mod='colissimo'}
            </strong>
          </p>
          <p>{l s='We noticed you currently have the following Colissimo module(s) installed and enabled:' mod='colissimo'}</p>
          <ul>
            {foreach $modules_to_migrate as $module_to_migrate}
              <li>{$module_to_migrate|escape:'htmlall':'utf-8'}</li>
            {/foreach}
          </ul>
          <p class="colissimo-migration-question">
            <strong>{l s='Do you want to migrate data such as module configuration, carrier pricing, labels?' mod='colissimo'}</strong>
          </p>
          <button class="btn btn-danger" data-migrate="0">
            <i class="process-icon- icon-times"></i>
            {l s='No, I will configure the module myself' mod='colissimo'}
          </button>
          <button class="btn btn-primary" data-migrate="1">
            <i class="process-icon- icon-check"></i>
            {l s='Yes, migrate the modules now' mod='colissimo'}
          </button>
        </div>
      </div>
    </div>
  </ps-panel>
</div>

{literal}
<script type="text/javascript">
    var genericMigrationErrorMessage = "{/literal}{l s='A problem occur while migrating from other module.' mod='colissimo'}{literal}";
    var modulesToMigrate = {/literal}{$modules_to_migrate|json_encode}{literal};
    var colissimoTokenMigration = '{/literal}{getAdminToken tab='AdminColissimoMigration'}{literal}';
</script>
{/literal}
