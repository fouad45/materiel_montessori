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

<div class="alert alert-info">
  {l s='All orders have been processed.' mod='colissimo'}
</div>
<div class="colissimo-download-result-buttons">
  {if $input_label || $input_return_label || $input_cn23}
    <div class="form-group">
      <form method="post"
            id="colissimo-form-documents"
            action="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'utf-8'}&action=downloadDocuments">
        <label for="colissimo-file-list">{l s='Documents' mod='colissimo'}</label>
        <select name="colissimo_file_type" id="colissimo-file-list">
          <option selected="selected" value="all">{l s='All documents' mod='colissimo'}</option>
          {if $input_label}
            <option value="labels">{l s='Labels' mod='colissimo'}</option>
          {/if}
          {if $input_return_label}
            <option value="return_labels">{l s='Return labels' mod='colissimo'}</option>
          {/if}
          {if $input_cn23}
            <option value="cn23">{l s='CN23' mod='colissimo'}</option>
          {/if}
        </select>
        <input type="hidden" name="colissimo_label_ids" value="{$input_label|escape:'htmlall':'utf-8'}"/>
      </form>
    </div>
    <button type="submit" class="btn btn-primary btn-print" value="1" name="print">
      <i class="icon icon-print"></i>
      {l s='Print PDF' mod='colissimo'}
    </button>
    <button type="submit" class="btn btn-primary btn-download" value="1" name="download">
      <i class="icon icon-download"></i>
      {l s='Download' mod='colissimo'}
    </button>
  {/if}
</div>
{if !$input_label && !$input_return_label && !$input_cn23}
  <div class="alert alert-warning">
    {l s='No labels or documents have been generated. Please review errors.' mod='colissimo'}
  </div>
{/if}
