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

<div class="colissimo-docs-alert alert alert-warning">
  <p>{l s='We noticed that you reached the limit of the number of Colissimo documents (labels, CN23, deposit slips) stored on the server.' mod='colissimo'}</p>
  <p>{l s='We advise you to free some space by clicking the button below.' mod='colissimo'} {l s='Documents older than %d days will be deleted.' sprintf=$docs_lifetime|intval mod='colissimo'}</p>
  <button class="btn btn-danger" id="colissimo-purge-documents">
    <i class="icon icon-trash"></i>
    {l s='Delete %d file(s)' sprintf=$docs_to_delete|intval mod='colissimo'}
  </button>
</div>
