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
      name="colissimo_bo_config_form"
      id="colissimo-bo-config-form"
      method="post"
      enctype="multipart/form-data">
  <ps-panel icon="icon-cogs" header="{l s='General settings' mod='colissimo'}">
    <div class="row">
      <ps-input-text name="COLISSIMO_ORDER_PREPARATION_TIME"
                     label="{l s='Preparation time' mod='colissimo'}"
                     size="10"
                     suffix="{l s='day(s)' mod='colissimo'}"
                     value="{$form_data['COLISSIMO_ORDER_PREPARATION_TIME']|intval}"
                     hint="{l s='Enter your order preparation time' mod='colissimo'}"
                     fixed-width="xs">
      </ps-input-text>

      <div class="row">
        <div class="col-lg-9 col-lg-offset-3">
          <p class="colissimo-subtitle">{l s='Label generation' mod='colissimo'}</p>
        </div>
      </div>

      <div class="form-group">
        <label class="control-label col-lg-3" for="colissimo-generate-label-statuses[]">
          <span class="label-tooltip"
                data-html="false"
                data-toggle="tooltip"
                data-original-title="{l s='Tip : press + hold Ctrl then click on statuses to select several options quickly' mod='colissimo'}">
            {l s='Statuses that allow to generate labels' mod='colissimo'}
          </span>
        </label>

        <div class="col-lg-9">
          <div class="form-control-static row">
            <div class="col-xs-6">
              <select id="availableSwap"
                      class="availableSwap"
                      name="COLISSIMO_GENERATE_LABEL_STATUSES_available[]"
                      multiple="multiple">
                <optgroup label="{l s='Available statuses' mod='colissimo'}">
                  {foreach $order_states as $id => $state}
                    {if !isset($form_data['COLISSIMO_GENERATE_LABEL_STATUSES'][$id])}
                      <option value="{$id|intval}">
                        {$state|escape:'htmlall':'utf-8'}
                      </option>
                    {/if}
                  {/foreach}
                </optgroup>
              </select>
              <a href="#" class="btn btn-default btn-block addSwap" id="addSwap">
                {l s='Add' mod='colissimo'} <i class="icon-arrow-right"></i>
              </a>
            </div>
            <div class="col-xs-6">
              <select id="selectedSwap" class="selectedSwap"
                      name="COLISSIMO_GENERATE_LABEL_STATUSES[]"
                      multiple="multiple">
                <optgroup label="{l s='Selected statuses' mod='colissimo'}">
                  {foreach $order_states as $id => $state}
                    {if isset($form_data['COLISSIMO_GENERATE_LABEL_STATUSES'][$id])}
                      <option value="{$id|intval}">
                        {$state|escape:'htmlall':'utf-8'}
                      </option>
                    {/if}
                  {/foreach}
                </optgroup>
              </select>
              <a href="#" class="btn btn-default btn-block removeSwap" id="removeSwap">
                <i class="icon-arrow-left"></i> {l s='Remove' mod='colissimo'}
              </a>
            </div>
            <p class="help-block">
              <i class="icon icon-warning"></i> {l s='If no statuses are selected, all statuses will be considered as selected (wording...)' mod='colissimo'}
            </p>
          </div>
        </div>
      </div>
      <ps-switch class="colissimo-use-shipping-in-progress"
                 name="COLISSIMO_USE_SHIPPING_IN_PROGRESS"
                 label="{l s='Update order status after label generation' mod='colissimo'}"
                 yes="{l s='Yes' mod='colissimo'}"
                 no="{l s='No' mod='colissimo'}"
                 hint="{l s='If you choose Yes, orders will change to Shipping in progress after the generation of the first label' mod='colissimo'}"
                 active="{$form_data['COLISSIMO_USE_SHIPPING_IN_PROGRESS']|escape:'htmlall':'utf-8'}">
      </ps-switch>
      <ps-switch class="colissimo-use-handled-by-carrier"
                 name="COLISSIMO_USE_HANDLED_BY_CARRIER"
                 label="{l s='Update order status after deposit slip generation' mod='colissimo'}"
                 yes="{l s='Yes' mod='colissimo'}"
                 no="{l s='No' mod='colissimo'}"
                 hint="{l s='If you choose Yes, orders will change to Handled by carrier after the generation of the first deposit slip' mod='colissimo'}"
                 active="{$form_data['COLISSIMO_USE_HANDLED_BY_CARRIER']|escape:'htmlall':'utf-8'}">
      </ps-switch>
      <ps-switch class="colissimo-generate-label-prestashop"
                 name="COLISSIMO_GENERATE_LABEL_PRESTASHOP"
                 label="{l s='Label printing mode' mod='colissimo'}"
                 yes="{l s='PrestaShop BO' mod='colissimo'}"
                 no="{l s='Coliship' mod='colissimo'}"
                 active="{$form_data['COLISSIMO_GENERATE_LABEL_PRESTASHOP']|escape:'htmlall':'utf-8'}">
      </ps-switch>
      <div class="colissimo-label-generation-mode-inputs">
        <ps-switch class="colissimo-postage-mode-manual"
                   name="COLISSIMO_POSTAGE_MODE_MANUAL"
                   label="{l s='Postage mode in PrestaShop\'s BO' mod='colissimo'}"
                   yes="{l s='Manual' mod='colissimo'}"
                   no="{l s='Auto' mod='colissimo'}"
                   active="{$form_data['COLISSIMO_POSTAGE_MODE_MANUAL']|escape:'htmlall':'utf-8'}">
        </ps-switch>
        <ps-select label="{l s='Label format' mod='colissimo'}" name="COLISSIMO_LABEL_FORMAT" fixed-width="xxl">
          {foreach $label_formats as $id => $name}
            <option {if $id == $form_data['COLISSIMO_LABEL_FORMAT']}selected="selected"{/if}
                    value="{$id|escape:'htmlall':'utf-8'}">
              {$name|escape:'htmlall':'utf-8'}
            </option>
          {/foreach}
        </ps-select>
      </div>
      <div class="row">
        <div class="col-lg-9 col-lg-offset-3">
          <p class="colissimo-subtitle">{l s='International shipments' mod='colissimo'}</p>
        </div>
      </div>
      <ps-input-text name="COLISSIMO_DEFAULT_CONTENTS"
                     label="{l s='Shipments default contents' mod='colissimo'}"
                     size="10"
                     hint="{l s='For single-product online store' mod='colissimo'}"
                     help="{l s='Required for international shipments' mod='colissimo'}"
                     value="{$form_data['COLISSIMO_DEFAULT_CONTENTS']|escape:'htmlall':'utf-8'}"
                     fixed-width="xxl">
      </ps-input-text>
      <ps-input-text name="COLISSIMO_DEFAULT_HS_CODE"
                     label="{l s='Default HS Code of your products' mod='colissimo'}"
                     size="10"
                     hint="{l s='(e.g. 620411)' mod='colissimo'}"
                     help="{l s='For more information:' mod='colissimo'} https://douane.gouv.nc/le-tarif-douanier/nomenclature-tarifaire"
                     value="{$form_data['COLISSIMO_DEFAULT_HS_CODE']|escape:'htmlall':'utf-8'}"
                     fixed-width="xxl">
      </ps-input-text>
      <ps-input-text name="COLISSIMO_EORI_NUMBER"
                     label="{l s='EORI Number' mod='colissimo'}"
                     size="10"
                     help="{l s='For more information:' mod='colissimo'} http://douane.gouv.fr/articles/a10901-numero-eori-economic-operator-registration-and-identification"
                     value="{$form_data['COLISSIMO_EORI_NUMBER']|escape:'htmlall':'utf-8'}"
                     fixed-width="xxl">
      </ps-input-text>
    </div>
    <ps-panel-footer>
      <ps-panel-footer-submit title="{l s='Save' mod='colissimo'}"
                              icon="process-icon-save"
                              direction="right"
                              name="submitColissimoBackConfigForm">
      </ps-panel-footer-submit>
    </ps-panel-footer>
  </ps-panel>
</form>
<form class="form-horizontal"
      action="#"
      name="colissimo_shipments_config_form"
      id="colissimo-shipments-config-form"
      method="post"
      enctype="multipart/form-data">
  <ps-panel icon="icon-cogs" header="{l s='Default shipping settings' mod='colissimo'}">
    <div class="row">
      <ps-switch class=""
                 name="COLISSIMO_INSURE_SHIPMENTS"
                 label="{l s='Insure shipments' mod='colissimo'}"
                 yes="{l s='Yes' mod='colissimo'}"
                 no="{l s='No' mod='colissimo'}"
                 hint="{l s='Ad Valorem Insurance' mod='colissimo'}"
                 active="{$form_data['COLISSIMO_INSURE_SHIPMENTS']|escape:'htmlall':'utf-8'}">
      </ps-switch>
      <ps-switch class="colissimo-enable-return"
                 name="COLISSIMO_ENABLE_RETURN"
                 label="{l s='Enable Colissimo merchandises return' mod='colissimo'}"
                 yes="{l s='Yes' mod='colissimo'}"
                 no="{l s='No' mod='colissimo'}"
                 active="{$form_data['COLISSIMO_ENABLE_RETURN']|escape:'htmlall':'utf-8'}">
      </ps-switch>
      <div class="colissimo-enable-return-inputs">
        <ps-switch class=""
                   name="COLISSIMO_AUTO_PRINT_RETURN_LABEL"
                   label="{l s='Print return labels for each shipment' mod='colissimo'}"
                   help="<i class='icon icon-lightbulb'></i> {l s='Choose «Yes» if you want to include return label inside packages.' mod='colissimo'}"
                   yes="{l s='Yes' mod='colissimo'}"
                   no="{l s='No' mod='colissimo'}"
                   hint="{l s='Only scanned parcels will be charged' mod='colissimo'}"
                   active="{$form_data['COLISSIMO_AUTO_PRINT_RETURN_LABEL']|escape:'htmlall':'utf-8'}">
        </ps-switch>
        <ps-switch class=""
                   name="COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER"
                   label="{l s='Display return labels in customer\'s account' mod='colissimo'}"
                   yes="{l s='Yes' mod='colissimo'}"
                   no="{l s='No' mod='colissimo'}"
                   active="{$form_data['COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER']|escape:'htmlall':'utf-8'}">
        </ps-switch>
        <ps-switch class=""
                   name="COLISSIMO_ENABLE_MAILBOX_RETURN"
                   label="{l s='Enable mailbox return' mod='colissimo'}"
                   yes="{l s='Yes' mod='colissimo'}"
                   no="{l s='No' mod='colissimo'}"
                   active="{$form_data['COLISSIMO_ENABLE_MAILBOX_RETURN']|escape:'htmlall':'utf-8'}">
        </ps-switch>
      </div>
    </div>
    <ps-panel-footer>
      <ps-panel-footer-submit title="{l s='Save' mod='colissimo'}"
                              icon="process-icon-save"
                              direction="right"
                              name="submitColissimoShipmentsConfigForm">
      </ps-panel-footer-submit>
    </ps-panel-footer>
  </ps-panel>
</form>
{literal}
  <script type="text/javascript">
      $(document).ready(function () {
      });
  </script>
{/literal}
