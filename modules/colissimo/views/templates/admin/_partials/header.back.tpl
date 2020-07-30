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

<div id="header-back-colissimo">
  <p class="colissimo-version"><i class="icon icon-info-circle"></i> v{$data['module_version']|escape:'htmlall':'utf-8'}</p>
  <div class="bootstrap panel">
    <div class="row">
      <div class="col-xs-12 colissimo-header">
        <img class="colissimo-logo" src="{$data['img_path']|escape:'htmlall':'utf-8'}Colissimo_Logo_H.png"/>
        <div class="colissimo-contact">
          <i class="icon icon-question-circle icon-big"></i>
          <p>
            <b>{l s='Do you have a question?' mod='colissimo'}</b><br/>
            {l s='Contact us using ' mod='colissimo'}
            <a href="https://addons.prestashop.com/contact-form.php?id_product={$data['id_product_addons']|intval}"
               target="_blank">
              {l s='this link' mod='colissimo'}
            </a>
            {l s='or by phone at:' mod='colissimo'}<br/>
            <img src="{$data['img_path']|escape:'html':'utf-8'}phone-support.png" class="colissimo-phone-number" />
          </p>
          <a href="{$data['module_path']|escape:'html':'UTF-8'}readme.pdf" target="_blank" class="btn btn-primary">
            <i class="icon-arrow-circle-down"></i> {l s='Download documentation' mod='colissimo'}
          </a>
        </div>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
</div>
