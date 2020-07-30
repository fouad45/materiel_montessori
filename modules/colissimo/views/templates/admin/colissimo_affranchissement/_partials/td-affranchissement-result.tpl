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

{if !$result['label']['error']}
  <div class="results-line">
    <span>{l s='Label' mod='colissimo'}</span>
    <div class="results-btn">
      <a target="_blank"
         href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'utf-8'}&action=downloadLabel&id_label={$result['label']['id']|intval}">
        <i class="icon icon-download"></i>
      </a>
      {if $result['label']['view']}
        <a title="{l s='Print PDF' mod='colissimo'}"
           onclick="printJS({literal}{
                   printable: baseAdminDir + 'index.php?controller=AdminColissimoLabel&action=viewLabel&id_label={/literal}{$result['label']['id']|intval}{literal}&token=' + tokenLabel,
                   showModal: true,
                   fallbackPrintable: `data:application/pdf;base64,{/literal}{$result['label']['base64']|escape:'html':'utf-8'}{literal}`,
                   modalMessage: {/literal}'{l s='Preparing printing' mod='colissimo'}'{literal},
                   }{/literal});">
          <i class="icon icon-print"></i>
        </a>
      {/if}
    </div>
  </div>
  {if $result['label']['cn23']}
    <div class="results-line">
      <span>{l s='CN23' mod='colissimo'}</span>
      <div class="results-btn">
        <a target="_blank"
           href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'utf-8'}&action=downloadCN23&id_label={$result['label']['id']|intval}">
          <i class="icon icon-download"></i>
        </a>
        <a title="{l s='Print PDF' mod='colissimo'}"
           onclick="printJS({literal}{
                   printable: baseAdminDir + 'index.php?controller=AdminColissimoLabel&action=viewCN23&id_label={/literal}{$result['label']['id']|intval}{literal}&token=' + tokenLabel,
                   showModal: true,
                   fallbackPrintable: `data:application/pdf;base64,{/literal}{$result['label']['cn23_base64']|escape:'html':'utf-8'}{literal}`,
                   modalMessage: {/literal}'{l s='Preparing printing' mod='colissimo'}'{literal},
                   }{/literal});">
          <i class="icon icon-print"></i>
        </a>
      </div>
    </div>
  {/if}
  {if $result['return_label']}
    {if !$result['return_label']['error']}
      <div class="results-line">
        <span>{l s='Return Label' mod='colissimo'}</span>
        <div class="results-btn">
          <a target="_blank"
             href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'utf-8'}&action=downloadLabel&id_label={$result['return_label']['id']|intval}">
            <i class="icon icon-download"></i>
          </a>
          <a title="{l s='Print PDF' mod='colissimo'}"
             onclick="printJS({literal}{
                     printable: baseAdminDir + 'index.php?controller=AdminColissimoLabel&action=viewLabel&id_label={/literal}{$result['return_label']['id']|intval}{literal}&token=' + tokenLabel,
                     showModal: true,
                     fallbackPrintable: `data:application/pdf;base64,{/literal}{$result['return_label']['base64']|escape:'html':'utf-8'}{literal}`,
                     modalMessage: {/literal}'{l s='Preparing printing' mod='colissimo'}'{literal},
                     }{/literal});">
            <i class="icon icon-print"></i>
          </a>
        </div>
      </div>
      {if $result['return_label']['cn23']}
        <div class="results-line">
          <span>{l s='Return CN23' mod='colissimo'}</span>
          <div class="results-btn">
            <a target="_blank"
               href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'utf-8'}&action=downloadCN23&id_label={$result['return_label']['id']|intval}">
              <i class="icon icon-download"></i>
            </a>
            <a title="{l s='Print PDF' mod='colissimo'}"
               onclick="printJS({literal}{
                       printable: baseAdminDir + 'index.php?controller=AdminColissimoLabel&action=viewCN23&id_label={/literal}{$result['return_label']['id']|intval}{literal}&token=' + tokenLabel,
                       showModal: true,
                       fallbackPrintable: `data:application/pdf;base64,{/literal}{$result['return_label']['cn23_base64']|escape:'html':'utf-8'}{literal}`,
                       modalMessage: {/literal}'{l s='Preparing printing' mod='colissimo'}'{literal},
                       }{/literal});">
              <i class="icon icon-print"></i>
            </a>
          </div>
        </div>
      {/if}
    {else}
      <div class="results-line">
        <a href="#"
           class="colissimo-error-modal"
           data-id="return-label-error-modal-{$result['id_colissimo_order']|intval}">
          <i class="icon icon-times-circle">&nbsp;</i>{l s='Error with return label' mod='colissimo'}
          <br/>{l s='(see details)' mod='colissimo'}
        </a>
        <div id="return-label-error-modal-{$result['id_colissimo_order']|intval}" class="modal fade">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-body">
                <div class="alert alert-danger clearfix">
                  {$result['return_label']['message']|escape:'htmlall':'utf-8'}
                  <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">
                    <i class="icon-remove"></i>&nbsp;
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    {/if}
  {/if}
{else}
  <div>
    <a href="#" class="colissimo-error-modal" data-id="label-error-modal-{$result['id_colissimo_order']|intval}">
      <i class="icon icon-times-circle">&nbsp;</i>{l s='Error with label' mod='colissimo'}
      <br/>{l s='(see details)' mod='colissimo'}
    </a>
    <div id="label-error-modal-{$result['id_colissimo_order']|intval}" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <div class="alert alert-danger clearfix">
              {$result['label']['message']|escape:'htmlall':'utf-8'}
              <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">
                <i class="icon-remove"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/if}
