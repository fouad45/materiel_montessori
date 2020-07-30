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

{include "../_partials/header.back.tpl"}

<div id="bo-colissimo">
  <div class="form-wrapper">
    {if !$show_migration}
      <ul class="nav nav-tabs">
        <li {if $active == 'intro'}class="active"{/if}>
          <a href="#intro" data-toggle="tab">{l s='Introduction' mod='colissimo'}</a>
        </li>
        <li {if $active == 'account'}class="active"{/if}>
          <a href="#account" data-toggle="tab"><i
                    class="icon icon-user"></i> {l s='My Colissimo account' mod='colissimo'}
          </a>
        </li>
        <li {if $active == 'fo'}class="active"{/if}>
          <a href="#fo" data-toggle="tab"><i class="icon icon-truck"></i> {l s='Front-Office settings' mod='colissimo'}
          </a>
        </li>
        <li {if $active == 'bo'}class="active"{/if}>
          <a href="#bo" data-toggle="tab"><i class="icon icon-cogs"></i> {l s='Back-Office settings' mod='colissimo'}
          </a>
        </li>
        <li {if $active == 'files'}class="active"{/if}>
          <a href="#files" data-toggle="tab"><i class="icon icon-file-pdf-o"></i> {l s='Files management' mod='colissimo'}
          </a>
        </li>
      </ul>
      <div class="tab-content panel">
        <div id="intro" class="tab-pane {if $active == 'intro'}active{/if}">
          {include file="./tab-intro.tpl"}
        </div>
        <div id="account" class="tab-pane {if $active == 'account'}active{/if}">
          {include file="./tab-account.tpl"}
        </div>
        <div id="fo" class="tab-pane {if $active == 'fo'}active{/if}">
          {include file="./tab-front.tpl"}
        </div>
        <div id="bo" class="tab-pane {if $active == 'bo'}active{/if}">
          {include file="./tab-back.tpl"}
        </div>
        <div id="files" class="tab-pane {if $active == 'files'}active{/if}">
          {include file="./tab-files.tpl"}
        </div>
      </div>
    {else}
      {include file="../migration/migration.tpl"}
    {/if}
  </div>
</div>
{include file="../whatsnew/whatsnew.tpl"}
