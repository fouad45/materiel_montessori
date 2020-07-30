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

<table class="table">
  <thead>
  <tr>
    <th class="text-center">
      <span class="title_box "><i class="icon icon-info"></i> {l s='Last status known' mod='colissimo'}</span></th>
    <th class="text-center">
      <span class="title_box "><i class="icon icon-barcode"></i> {l s='Label' mod='colissimo'}</span></th>
    <th class="text-center">
      <span class="title_box "><i class="icon icon-euro"></i> {l s='Shipment insured' mod='colissimo'}</span></th>
    <th class="text-center">
      <span class="title_box "><i class="icon icon-barcode"></i> {l s='Return label' mod='colissimo'}</span></th>
    <th class="text-center">
      <span class="title_box "><i class="icon icon-euro"></i> {l s='Return shipment insured' mod='colissimo'}</span>
    </th>
    <th class="text-center">
      <span class="title_box "><i class="icon icon-file-pdf-o"></i> {l s='CN23' mod='colissimo'}</span></th>
    <th class="text-center">
      <span class="title_box "><i class="icon icon-file-pdf-o"></i> {l s='Return CN23' mod='colissimo'}</span></th>
    <th class="text-center">
      <span class="title_box "><i class="icon icon-file-pdf-o"></i> {l s='Deposit Slip' mod='colissimo'}</span></th>
  </tr>
  </thead>
  <tbody>
  {foreach $shipments as $shipment}
    <tr class="product-line-row">
      <td class="tracking-label-{$shipment.id_label|intval}">
        {if $shipment.status_text}
          {$shipment.status_text|escape:'htmlall':'utf-8'}
        {else}
          <span class="text-muted">--</span>
        {/if}
        <br/>
        {if $shipment.status_upd}
          <span class="text-muted">({l s='last update on' mod='colissimo'} {$shipment.status_upd|escape:'htmlall':'utf-8'}
            )</span>
        {/if}
      </td>
      <td class="tracking-number text-center">
        {if $shipment.is_downloadable}
          <div>
            <a target="_blank"
               title="{l s='Download label' mod='colissimo'}"
               href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'utf-8'}&action=downloadLabel&id_label={$shipment.id_label|intval}">
              {$shipment.shipping_number|escape:'htmlall':'utf-8'}
            </a>
          </div>
          <a target="_blank"
             title="{l s='Download label' mod='colissimo'}"
             class="icon-action"
             href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'utf-8'}&action=downloadLabel&id_label={$shipment.id_label|intval}">
            <i class="icon icon-download icon-xl"></i>
          </a>
          {if $shipment.is_printable}
            <a class="icon-action"
               title="{l s='Print label' mod='colissimo'}"
               onclick="printJS({literal}{
                       printable: baseAdminDir + 'index.php?controller=AdminColissimoLabel&action=viewLabel&id_label={/literal}{$shipment.id_label|intval}{literal}&token=' + tokenLabel,
                       showModal: true,
                       fallbackPrintable: `data:application/pdf;base64,{/literal}{$shipment['base64']|escape:'html':'utf-8'}{literal}`,
                       modalMessage: {/literal}'{l s='Preparing printing' mod='colissimo'}'{literal},
                       }{/literal});">
              <i class="icon icon-xl icon-print"></i>
            </a>
          {/if}
        {else}
          <div>
            {$shipment.shipping_number|escape:'htmlall':'utf-8'}
          </div>
        {/if}
        {if $shipment.is_deletable}
          <a data-colissimo-label-id="{$shipment.id_label|intval}"
             data-colissimo-label-number="{$shipment.shipping_number|escape:'htmlall':'utf-8'}"
             title="{l s='Delete label' mod='colissimo'}"
             class="colissimo-delete-label icon-action"
             href="#">
            <i class="icon icon-trash icon-xl"></i>
          </a>
        {/if}
      </td>
      <td class="text-center">
        {if $shipment.insurance === '1'}
          <i class="icon icon-check"></i>
        {elseif $shipment.insurance === '0'}
          <i class="icon icon-times"></i>
        {else}
          --
        {/if}
      </td>
      <td class="tracking-number text-center">
        {if isset($shipment.id_return_label)}
          {if $shipment.return_is_downloadable}
            <div>
              <a target="_blank"
                 title="{l s='Download label' mod='colissimo'}"
                 href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'utf-8'}&action=downloadLabel&id_label={$shipment.id_return_label|intval}">
                {$shipment.return_shipping_number|escape:'htmlall':'utf-8'}
              </a>
            </div>
            <a target="_blank"
               title="{l s='Download label' mod='colissimo'}"
               class="icon-action"
               href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'utf-8'}&action=downloadLabel&id_label={$shipment.id_return_label|intval}">
              <i class="icon icon-download icon-xl"></i>
            </a>
            <a class="icon-action"
               title="{l s='Print label' mod='colissimo'}"
               onclick="printJS({literal}{
                       printable: baseAdminDir + 'index.php?controller=AdminColissimoLabel&action=viewLabel&id_label={/literal}{$shipment.id_return_label|intval}{literal}&token=' + tokenLabel,
                       showModal: true,
                       fallbackPrintable: `data:application/pdf;base64,{/literal}{$shipment['return_base64']|escape:'html':'utf-8'}{literal}`,
                       modalMessage: {/literal}'{l s='Preparing printing' mod='colissimo'}'{literal},
                       }{/literal});">
              <i class="icon icon-xl icon-print"></i>
            </a>
            <a data-colissimo-return-label-id="{$shipment.id_return_label|intval}"
               class="colissimo-mail-return-label icon-action"
               title="{l s='Send by mail to customer' mod='colissimo'}"
               href="#">
              <i class="icon icon-envelope icon-xl"></i>
            </a>
          {else}
            <div>
              {$shipment.return_shipping_number|escape:'htmlall':'utf-8'}
            </div>
          {/if}
          {if $shipment.return_is_deletable}
            <a data-colissimo-label-id="{$shipment.id_return_label|intval}"
               data-colissimo-label-number="{$shipment.return_shipping_number|escape:'htmlall':'utf-8'}"
               title="{l s='Delete label' mod='colissimo'}"
               class="colissimo-delete-label icon-action"
               href="#">
              <i class="icon icon-trash icon-xl"></i></a>
          {/if}
        {else}
          {if $shipment.return_available < 1}
            --
          {else}
            <button class="btn btn-primary colissimo-generate-return-label"
                    data-colissimo-label-id="{$shipment.id_label|intval}">
              <i class="icon icon-refresh"></i>
              {l s='Generate' mod='colissimo'}
            </button>
          {/if}
        {/if}
      </td>
      <td class="text-center">
        {if isset($shipment.id_return_label)}
          {if $shipment.return_insurance === '1'}
            <i class="icon icon-check"></i>
          {elseif $shipment.return_insurance === '0'}
            <i class="icon icon-times"></i>
          {else}
            --
          {/if}

        {else}
          --
        {/if}
      </td>
      <td class="text-center">
        {if isset($shipment.cn23) && $shipment.cn23}
          <a target="_blank"
             class="icon-action"
             href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'utf-8'}&action=downloadCN23&id_label={$shipment.id_label|intval}">
            <i class="icon icon-xl icon-download"></i>
          </a>
          <a class="icon-action"
             title="{l s='Print CN23' mod='colissimo'}"
             onclick="printJS({literal}{
                     printable: baseAdminDir + 'index.php?controller=AdminColissimoLabel&action=viewCN23&id_label={/literal}{$shipment.id_label|intval}{literal}&token=' + tokenLabel,
                     showModal: true,
                     fallbackPrintable: `data:application/pdf;base64,{/literal}{$shipment['cn23_base64']|escape:'html':'utf-8'}{literal}`,
                     modalMessage: {/literal}'{l s='Preparing printing' mod='colissimo'}'{literal},
                     }{/literal});">
            <i class="icon icon-xl icon-print"></i>
          </a>
        {else}
          --
        {/if}
      </td>
      <td class="text-center">
        {if isset($shipment.return_cn23) && $shipment.return_cn23}
          <a target="_blank"
             class="icon-action"
             href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'utf-8'}&action=downloadCN23&id_label={$shipment.id_return_label|intval}">
            <i class="icon icon-xl icon-download"></i>
          </a>
          <a class="icon-action"
             title="{l s='Print CN23' mod='colissimo'}"
             onclick="printJS({literal}{
                     printable: baseAdminDir + 'index.php?controller=AdminColissimoLabel&action=viewCN23&id_label={/literal}{$shipment.id_return_label|intval}{literal}&token=' + tokenLabel,
                     showModal: true,
                     fallbackPrintable: `data:application/pdf;base64,{/literal}{$shipment['return_cn23_base64']|escape:'html':'utf-8'}{literal}`,
                     modalMessage: {/literal}'{l s='Preparing printing' mod='colissimo'}'{literal},
                     }{/literal});">
            <i class="icon icon-xl icon-print"></i>
          </a>
        {else}
          --
        {/if}
      </td>
      <td class="text-center">
        {if isset($shipment.id_deposit_slip) && $shipment.id_deposit_slip}
          <a target="_blank"
             href="{$link->getAdminLink('AdminColissimoDepositSlip')|escape:'htmlall':'utf-8'}&action=download&id_deposit_slip={$shipment.id_deposit_slip|intval}">
            {l s='Deposit slip #%d' sprintf=$shipment.id_deposit_slip mod='colissimo'}<br/>
            <i class="icon icon-xl icon-download"></i>
          </a>
        {else}
          --
        {/if}
      </td>
    </tr>
    {foreachelse}
    <tr>
      <td class="list-empty hidden-print" colspan="6">
        <div class="list-empty-msg">
          <i class="icon-warning-sign list-empty-icon"></i>
          {l s='You have not generated any labels yet.' mod='colissimo'}
        </div>
      </td>
    </tr>
  {/foreach}
  </tbody>
</table>

<div class="colissimo-modal-delete-label colissimo-modal-delete-label-{$id_colissimo_order|intval} modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          {l s='Confirm label deleting' mod='colissimo'}
        </h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="colissimo-modal-order-id" value="{$id_colissimo_order|intval}"/>
        <input type="hidden" name="colissimo-modal-label-id" value=""/>
        <p class="alert alert-info">
          {l s='Documents like CN23 and return labels associated with this label will be deleted as well.' mod='colissimo'}
        </p>
        <p class="modal-confirm">
          {l s='Do you wan\'t to delete label' mod='colissimo'} <span id="colissimo-shipment-number"></span> ?
        </p>
        <div class="modal-actions">
          <button class="btn btn-danger" data-dismiss="modal">
            <i class="icon icon-times"></i>
            {l s='No' mod='colissimo'}
          </button>
          <button class="btn btn-primary colissimo-modal-confirm-delete-label">
            <i class="icon- icon-check"></i>
            {l s='Yes' mod='colissimo'}
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="colissimo-modal-mail-label colissimo-modal-mail-label-{$id_colissimo_order|intval} modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          {l s='The return label is about to be generated' mod='colissimo'}
        </h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="colissimo-modal-order-id" value="{$id_colissimo_order|intval}"/>
        <input type="hidden" name="colissimo-modal-label-id" value=""/>
        <p class="modal-confirm">
          {l s='Do you want to send it by mail to your customer' mod='colissimo'} ?
        </p>
        <div class="modal-actions">
          <button class="btn btn-danger colissimo-modal-no-confirm-mail-label">
            <i class="icon icon-times"></i>
            {l s='No' mod='colissimo'}
          </button>
          <button class="btn btn-primary colissimo-modal-confirm-mail-label">
            <i class="icon- icon-check"></i>
            {l s='Yes' mod='colissimo'}
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

{literal}
<script type="text/javascript">
    var tokenLabel = '{/literal}{getAdminToken tab='AdminColissimoLabel'}{literal}';

    $('.colissimo-mail-return-label').off('click').on('click', function (e) {
        e.preventDefault();

        var idColissimoReturnLabel = $(this).attr('data-colissimo-return-label-id');
        var tokenLabel = '{/literal}{getAdminToken tab='AdminColissimoLabel'}{literal}';
        var genericEmailErrorMessage = "{/literal}{l s='An error occurred while sending mail.' mod='colissimo'}{literal}";

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoLabel',
                ajax: 1,
                token: tokenLabel,
                action: 'mailReturnLabel',
                id_colissimo_return_label: parseInt(idColissimoReturnLabel),
                channel: channel
            }
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericEmailErrorMessage);
        }).success(function (data) {
            if (!data.error) {
                showSuccessMessage(data.message);
            } else {
                showErrorMessage(data.message);
            }
        });
    });
    $('.colissimo-generate-return-label').off('click').on('click', function (e) {
        e.preventDefault();

        var idColissimoLabel = parseInt($(this).attr('data-colissimo-label-id'));
        var idColissimoOrder = $(this).closest('.colissimo-shipments').parent().find('.colissimo-update-order-tracking button').attr('data-colissimo-order-id');
        var className = '.colissimo-modal-mail-label-' + idColissimoOrder;
        $(className + ' input[name="colissimo-modal-label-id"]').val(idColissimoLabel);
        $(className + '.colissimo-modal-mail-label').modal('show');
    });

    $('.colissimo-modal-confirm-mail-label').off('click').on('click', function (e) {
        e.preventDefault();
        var sendMail = 1;
        var idColissimoOrder = $(this).closest('.colissimo-modal-mail-label').find('input[name="colissimo-modal-order-id"]').val();
        var idColissimoLabel = $(this).closest('.colissimo-modal-mail-label').find('input[name="colissimo-modal-label-id"]').val();
        generateReturnLabel(sendMail, idColissimoOrder, idColissimoLabel);
    });

    $('.colissimo-modal-no-confirm-mail-label').off('click').on('click', function (e) {
        e.preventDefault();
        var sendMail = 0;
        var idColissimoOrder = $(this).closest('.colissimo-modal-mail-label').find('input[name="colissimo-modal-order-id"]').val();
        var idColissimoLabel = $(this).closest('.colissimo-modal-mail-label').find('input[name="colissimo-modal-label-id"]').val();
        generateReturnLabel(sendMail, idColissimoOrder, idColissimoLabel);
    });

    $('.colissimo-delete-label').off('click').on('click', function (e) {
        e.preventDefault();

        var idColissimoLabel = $(this).attr('data-colissimo-label-id');
        var idColissimoOrder = $(this).closest('.colissimo-shipments').parent().find('.colissimo-update-order-tracking button').attr('data-colissimo-order-id');
        var className = '.colissimo-modal-delete-label-' + idColissimoOrder;
        $(className + ' #colissimo-shipment-number').html($(this).attr('data-colissimo-label-number'));
        $(className + ' input[name="colissimo-modal-label-id"]').val(idColissimoLabel);
        $(className + '.colissimo-modal-delete-label').modal('show');
    });

    $('.colissimo-modal-confirm-delete-label').off('click').on('click', function (e) {
        e.preventDefault();

        var idColissimoOrder = $(this).closest('.colissimo-modal-delete-label').find('input[name="colissimo-modal-order-id"]').val();
        var idColissimoLabel = $(this).closest('.colissimo-modal-delete-label').find('input[name="colissimo-modal-label-id"]').val();
        var shipmentsTable = $('.colissimo-shipments-' + parseInt(idColissimoOrder));
        var tokenLabel = '{/literal}{getAdminToken tab='AdminColissimoLabel'}{literal}';
        var genericDeleteErrorMessage = "{/literal}{l s='An error occurred while deleting label.' mod='colissimo'}{literal}";

        $('.colissimo-modal-delete-label').modal('hide');
        shipmentsTable.fadeTo('fast', 0.4);
        shipmentsTable.css('pointer-events', 'none');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoLabel',
                ajax: 1,
                token: tokenLabel,
                action: 'deleteLabel',
                id_colissimo_label: parseInt(idColissimoLabel),
                channel: channel
            }
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericDeleteErrorMessage);
        }).success(function (data) {
            if (data === null) {
                showErrorMessage(genericDeleteErrorMessage);
                return;
            }
            if (!data.error) {
                showSuccessMessage(data.message);
            } else {
                showErrorMessage(data.message);
            }
            if (data.html) {
                shipmentsTable.html(data.html);
            }
        }).always(function (data) {
            shipmentsTable.fadeTo('fast', 1);
            shipmentsTable.css('pointer-events', 'auto');
        });
    });

    function generateReturnLabel(sendMail, idColissimoOrder, idColissimoLabel) {
        var btn = $('.colissimo-generate-return-label[data-colissimo-label-id=' + idColissimoLabel + ']');
        var shipmentsTable = $('.colissimo-shipments-' + idColissimoOrder);
        var tokenLabel = '{/literal}{getAdminToken tab='AdminColissimoLabel'}{literal}';
        $('.colissimo-modal-mail-label-' + parseInt(idColissimoOrder)).modal('hide');
        btn.find('i').attr('class', 'icon icon-spin icon-spinner');
        btn.addClass('disabled');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoLabel',
                ajax: 1,
                token: tokenLabel,
                action: 'generateReturn',
                id_colissimo_label: parseInt(idColissimoLabel),
                send_mail: sendMail,
                channel: channel
            }
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericErrorMessage);
        }).success(function (data) {
            if (!data.error) {
                showSuccessMessage(data.message);
            } else {
                showErrorMessage(data.message);
            }
            if (data.warning_message !== undefined && data.warning_message != false) {
                showNoticeMessage(data.warning_message);
            }
            shipmentsTable.html(data.html);
        }).always(function (data) {
            btn.find('i').attr('class', 'icon icon-refresh');
            btn.removeClass('disabled');
        });
    }
</script>
{/literal}
