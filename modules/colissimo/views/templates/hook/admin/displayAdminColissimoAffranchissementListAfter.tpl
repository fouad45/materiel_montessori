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

{if $list_total > 0}
  <div class="btn-group bulk-actions pull-right">
    <button class="btn btn-primary" name="submitProcessColissimoSelection">
      <i class="process-icon- icon-arrow-circle-o-right"></i>
      {l s='Configure these shipments' mod='colissimo'}
    </button>
  </div>
{/if}

{block name="after"}
{literal}
  <script type="text/javascript">
      var loaderPath = {/literal}'{$data.img_path|escape:'html':'utf-8'}loading.svg'{literal};
      var addressErrorMessage = "{/literal}{l s='Address could not be saved.' mod='colissimo'}{literal}";
      var genericErrorMessage = "{/literal}{l s='An error occured. Please try again.' mod='colissimo'}{literal}";
  </script>
{/literal}
{/block}
