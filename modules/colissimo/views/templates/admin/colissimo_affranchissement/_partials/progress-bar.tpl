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

<div class="colissimo-progress-bar">
  <div class="step {$step[1]['state']|escape:'htmlall':'utf-8'}">1<p>{l s='Selection' mod='colissimo'}</p></div>
  <div class="step-line {$step[1]['line']|escape:'htmlall':'utf-8'}"></div>
  <div class="step {$step[2]['state']|escape:'htmlall':'utf-8'} step-config">2<p>{l s='Configuration' mod='colissimo'}</p></div>
  <div class="step-line">
    <div class="js-step-line"></div>
  </div>
  <div class="step incomplete">3<p>{l s='Impression' mod='colissimo'}</p></div>
</div>
