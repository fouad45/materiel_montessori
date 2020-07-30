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

<div class="colissimo-mailbox-return-result">
  {if !$has_error}
    <div class="alert alert-success">
      {l s='The mailbox pickup is confirmed' mod='colissimo'}
    </div>
  {else}
    <div class="alert alert-danger">
      {if $error_message}
        {$error_message}
      {else}
        {l s='An error occurred. Please try again later or request a pickup on Colissimo website at this url:' mod='colissimo'}
        <p>
          <a href="http://colissimo.fr/retourbal" target="_blank">http://colissimo.fr/retourbal</a>
        </p>
      {/if}
    </div>
  {/if}
  <button class="btn btn-primary colissimo-result-close" data-dismiss="modal" aria-label="{l s='Close' mod='colissimo'}">
    <i class="material-icons">close</i>
    {l s='Close' mod='colissimo'}
  </button>
</div>
