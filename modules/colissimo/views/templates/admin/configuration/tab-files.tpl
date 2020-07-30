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

<form class="form-horizontal"
      action="#"
      name="colissimo_files_config_form"
      id="colissimo-files-config-form"
      method="post"
      enctype="multipart/form-data">
  <ps-panel icon="icon-cogs" header="{l s='Files management' mod='colissimo'}">
    <div class="row">
      <div class="alert alert-info">
        {l s='In this form, you can configure when to display an alert to delete documents.' mod='colissimo'}<br/>
        {l s='It allows you to free some space periodically depending on the quantity of documents you create.' mod='colissimo'}<br/>
        {l s='Default settings correspond approximately to a volume of 50 orders per day so the alert shows up every two weeks.' mod='colissimo'}<br/>
        {l s='The alert will be shown on the Colissimo Postage page.' mod='colissimo'}
      </div>
      <ps-input-text name="COLISSIMO_FILES_LIMIT"
                     label="{l s='Number of files triggering the alert' mod='colissimo'}"
                     size="10"
                     suffix="{l s='files' mod='colissimo'}"
                     value="{$form_data['COLISSIMO_FILES_LIMIT']|intval}"
                     hint="{l s='Files include labels, customs documents and deposit slips' mod='colissimo'}"
                     fixed-width="xs">
      </ps-input-text>
      <ps-input-text name="COLISSIMO_FILES_LIFETIME"
                     label="{l s='Documents lifetime' mod='colissimo'}"
                     size="10"
                     suffix="{l s='day(s)' mod='colissimo'}"
                     value="{$form_data['COLISSIMO_FILES_LIFETIME']|intval}"
                     hint="{l s='Number of days during which you want to keep documents' mod='colissimo'}"
                     help="{l s='Documents dating more than this number of days will be deleted.' mod='colissimo'}"
                     fixed-width="xs">
      </ps-input-text>
      <div class="row colissimo-docs-disk-usage">
        <label class="control-label col-lg-3 bold">
          <span>{l s='Disk usage of documents directory' mod='colissimo'}</span>
        </label>
        <div class="col-lg-9">
          <span>
            {$form_data['documents_dir_size']|escape:'htmlall':'utf-8'}
            {l s='(%d files)' sprintf=$form_data['documents_dir_count']|intval mod='colissimo'}
          </span>
        </div>
      </div>

    </div>
    <ps-panel-footer>
      <ps-panel-footer-submit title="{l s='Save' mod='colissimo'}"
                              icon="process-icon-save"
                              direction="right"
                              name="submitColissimoFilesConfigForm">
      </ps-panel-footer-submit>
    </ps-panel-footer>
  </ps-panel>
</form>
