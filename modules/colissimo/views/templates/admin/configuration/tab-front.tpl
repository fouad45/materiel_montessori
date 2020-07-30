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
      name="colissimo_widget_config_form"
      id="colissimo-widget-config-form"
      method="post"
      enctype="multipart/form-data">
  <div class="alert alert-info">
    {l s='While installing the module, 4 carriers have been created.' mod='colissimo'}<br/>
    <a href="{$link->getAdminLink('AdminCarriers')|escape:'htmlall':'utf-8'}"
       target="_blank">{l s='Click here to configure their zones, ranges and prices.' mod='colissimo'}</a><br/><br/>
    <b><i class="icon icon-warning"></i> {l s='In order for the module to work properly, please do not delete these carriers' mod='colissimo'}
    </b>
  </div>
  <ps-panel icon="icon-cogs" header="{l s='Brexit' mod='colissimo'}">
    <div class="row">
      <div class="brexit-intro">
        <img src="{$data['img_path']|escape:'htmlall':'utf-8'}icons/icon_uk.png" />
        <p>{l s='As you probably know, the coming withdrawal of the United Kingdom from the European Union will impact deliveries to the UK. The timeline of the Brexit is still uncertain but you can enable Brexit mode below when the withdrawal will be effective in order to apply changes caused by the Brexit.' mod='colissimo'}</p>
      </div>
      <ps-switch class="colissimo-enable-brexit"
                 name="COLISSIMO_ENABLE_BREXIT"
                 label="{l s='Enable Brexit' mod='colissimo'}"
                 yes="{l s='Yes' mod='colissimo'}"
                 no="{l s='No' mod='colissimo'}"
                 hint=""
                 help=""
                 active="{$form_data['COLISSIMO_ENABLE_BREXIT']|escape:'htmlall':'utf-8'}">
      </ps-switch>
    </div>
    <ps-panel-footer>
      <ps-panel-footer-submit title="{l s='Save' mod='colissimo'}"
                              icon="process-icon-save"
                              direction="right"
                              name="submitColissimoEnableBrexit">
      </ps-panel-footer-submit>
    </ps-panel-footer>
  </ps-panel>
  <ps-panel icon="icon-cogs" header="{l s='Pick-up service' mod='colissimo'}">
    <div class="row">
      <ps-switch class="colissimo-widget-local"
                 name="COLISSIMO_WIDGET_REMOTE"
                 label="{l s='Use default Colissimo widget' mod='colissimo'}"
                 yes="{l s='Yes' mod='colissimo'}"
                 no="{l s='No' mod='colissimo'}"
                 hint="{l s='Choosing "Yes" (default option) will give you access to the latest features of the Widget.' mod='colissimo'}<br />{l s='Choose "No" if you experience any problems with the display of the Widget (conflict with other modules or theme). The module will use local files that you will be able to override if needed.' mod='colissimo'}"
                 help="{l s='Do not forget to empty PrestaShop cache if you change this option' mod='colissimo'}"
                 active="{$form_data['COLISSIMO_WIDGET_REMOTE']|escape:'htmlall':'utf-8'}">
      </ps-switch>
    </div>
    <div class="row">
      <ps-input-text name="COLISSIMO_WIDGET_ENDPOINT"
                     label="{l s='Webservice URL' mod='colissimo'}"
                     size="10"
                     value="{$form_data['COLISSIMO_WIDGET_ENDPOINT']|escape:'htmlall':'utf-8'}"
                     required-input="true"
                     fixed-width="xxl">
      </ps-input-text>
      <a class="btn btn-primary col-lg-offset-3" id="colissimo-check-widget">
        <i class="icon icon-check"></i> {l s='Check availability' mod='colissimo'}
      </a>
      <ps-panel-divider></ps-panel-divider>
      <div class="row">
        <div class="col-lg-9 col-lg-offset-3">
          <p class="colissimo-subtitle">{l s='Display preferences' mod='colissimo'}</p>
        </div>
      </div>
      <ps-color-picker fixed-width="lg"
                       required-input="true"
                       color="{$form_data['COLISSIMO_WIDGET_COLOR_2']|escape:'htmlall':'utf-8'}"
                       label="{l s='Address fields color' mod='colissimo'}"
                       help="{l s='Sections hightlighted in orange in the example below' mod='colissimo'}"
                       name="COLISSIMO_WIDGET_COLOR_2"></ps-color-picker>
      <ps-color-picker fixed-width="lg"
                       required-input="true"
                       color="{$form_data['COLISSIMO_WIDGET_COLOR_1']|escape:'htmlall':'utf-8'}"
                       label="{l s='Pickup list color' mod='colissimo'}"
                       help="{l s='Sections hightlighted in grey in the example below' mod='colissimo'}"
                       name="COLISSIMO_WIDGET_COLOR_1"></ps-color-picker>

      <ps-select label="{l s='Text font' mod='colissimo'}" name="COLISSIMO_WIDGET_FONT" fixed-width="xxl">
        {foreach $widget_fonts as $id => $font}
          <option {if $id == $form_data['COLISSIMO_WIDGET_FONT']|escape:'htmlall':'utf-8'}selected="selected"{/if}
                  value="{$id|intval}">{$font|escape:'htmlall':'utf-8'}</option>
        {/foreach}
      </ps-select>
      <div class="form-group">
        <label class="control-label col-lg-3 ">
          <span>{l s='Display example' mod='colissimo'}</span>
        </label>
        <div class="col-lg-9">
          <img src="{$data['img_path']|escape:'htmlall':'utf-8'}colissimo_widget.png" />
        </div>
      </div>
    </div>
    <ps-panel-footer>
      <ps-panel-footer-submit title="{l s='Save' mod='colissimo'}"
                              icon="process-icon-save"
                              direction="right"
                              name="submitColissimoWidgetConfigForm">
      </ps-panel-footer-submit>
    </ps-panel-footer>
  </ps-panel>
</form>
{literal}
<script type="text/javascript">
    var genericErrorMessage = "{/literal}{l s='Webservice connexion could not be checked' mod='colissimo'}{literal}";
    var colissimoCredentialsToken = '{/literal}{getAdminToken tab='AdminColissimoTestCredentials'}{literal}';

    $(document).ready(function () {
        $(document).on('click', '#colissimo-check-widget', function () {
            var checkWidget = $('#colissimo-check-widget');

            testWidgetCredentials(checkWidget);
        });
    });
</script>
{/literal}
