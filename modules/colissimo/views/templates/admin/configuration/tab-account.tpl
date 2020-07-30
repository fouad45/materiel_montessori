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
      name="colissimo_account_config_form"
      id="colissimo-account-config-form"
      method="post"
      enctype="multipart/form-data">
  <ps-panel icon="icon-cogs" header="{l s='Configuration' mod='colissimo'}">
    <div class="row">
      <ps-switch class=""
                 name="COLISSIMO_LOGS"
                 label="{l s='Enable logs' mod='colissimo'}"
                 yes="{l s='Yes' mod='colissimo'}"
                 no="{l s='No' mod='colissimo'}"
                 hint="{l s='Your account number is indicated on your contract' mod='colissimo'}"
                 active="{$form_data['COLISSIMO_LOGS']|escape:'htmlall':'utf-8'}">
      </ps-switch>
      <div class="form-group">
        <div class="col-lg-9 col-lg-offset-3">
          <p class="alert-text">
            {l s='Logs files are stored in the logs directory of the module. Files are rotated every month.' mod='colissimo'}
            <br/>
            {l s='You can download the current file by clicking' mod='colissimo'}
            <a target="_blank" href="{$log_url|escape:'htmlall':'utf-8'}">{l s='here' mod='colissimo'}</a>.<br/>
          </p>
        </div>
      </div>
    </div>
    <div class="col-lg-offset-3 colissimo-box">
      <ps-panel>
        <div class="row">
          <p class="colissimo-box-title col-lg-offset-3">{l s='Colissimo Box credentials' mod='colissimo'}</p>
          <ps-input-text label="{l s='Contract number' mod='colissimo'}"
                         value="{$form_data['COLISSIMO_ACCOUNT_LOGIN']|escape:'htmlall':'utf-8'}"
                         prefix-icon="user"
                         name="COLISSIMO_ACCOUNT_LOGIN"
                         hint="{l s='Your account number is indicated on your contract' mod='colissimo'}"
                         fixed-width="xxl">
          </ps-input-text>
          <ps-password label="{l s='Password' mod='colissimo'}"
                       value="{$form_data['COLISSIMO_ACCOUNT_PASSWORD']|escape:'htmlall':'utf-8'}"
                       name="COLISSIMO_ACCOUNT_PASSWORD"
                       hint="{l s='The password was sent by email to the contract address' mod='colissimo'}"
                       fixed-width="xl">
          </ps-password>
          <ps-checkboxes label="{l s='Account type' mod='colissimo'}"
                         hint="{l s='Check your product list on your contract' mod='colissimo'}">
            <ps-checkbox name="COLISSIMO_ACCOUNT_TYPE[]"
                         value="FRANCE"
                         {if isset($form_data['COLISSIMO_ACCOUNT_TYPE']['FRANCE'])}checked="true"{/if}>{l s='France (home, PUDO, return)' mod='colissimo'}</ps-checkbox>
            <ps-checkbox name="COLISSIMO_ACCOUNT_TYPE[]"
                         value="OM"
                         {if isset($form_data['COLISSIMO_ACCOUNT_TYPE']['OM'])}checked="true"{/if}>{l s='Overseas (home)' mod='colissimo'}</ps-checkbox>
            <ps-checkbox name="COLISSIMO_ACCOUNT_TYPE[]"
                         value="EUROPE"
                         {if isset($form_data['COLISSIMO_ACCOUNT_TYPE']['EUROPE'])}checked="true"{/if}>{l s='Europe' mod='colissimo'}</ps-checkbox>
            <ps-checkbox name="COLISSIMO_ACCOUNT_TYPE[]"
                         value="WORLDWIDE"
                         {if isset($form_data['COLISSIMO_ACCOUNT_TYPE']['WORLDWIDE'])}checked="true"{/if}>{l s='Worldwide' mod='colissimo'}</ps-checkbox>
          </ps-checkboxes>
        </div>
      </ps-panel>
    </div>
    <ps-panel-footer>
      <ps-panel-footer-submit title="{l s='Save' mod='colissimo'}"
                              icon="process-icon-save"
                              direction="right"
                              name="submitColissimoAccountConfigForm">
      </ps-panel-footer-submit>
    </ps-panel-footer>
  </ps-panel>
</form>
<ps-panel icon="icon-envelope" header="{l s='Colissimo Address' mod='colissimo'}" class="clearfix">
  <div class="clearfix">
    <div class="alert alert-info">
      {l s='Please enter your physical address for your returns in case of non-delivery (no CEDEX admitted)' mod='colissimo'}
    </div>
    <div class="row colissimo-return-address">
      <ps-switch class="colissimo-set-return-address"
                 name="COLISSIMO_USE_RETURN_ADDRESS"
                 label="{l s='Use a different address for the reception of return shipement' mod='colissimo'}"
                 yes="{l s='Yes' mod='colissimo'}"
                 no="{l s='No' mod='colissimo'}"
                 hint=""
                 help=""
                 active="{$form_data['COLISSIMO_USE_RETURN_ADDRESS']|escape:'htmlall':'utf-8'}">
      </ps-switch>
    </div>
    <div class="col-md-6 col-sm-12">
      <form class="form-horizontal adr-form"
            action="#"
            name="colissimo_sender_config_form"
            id="colissimo-sender-config-form"
            method="post"
            enctype="multipart/form-data">
        <ps-panel header="{l s='Shipping address' mod='colissimo'}">
          <div class="row">
            <ps-input-text name="sender_company"
                           label="{l s='Company' mod='colissimo'}"
                           autocomplete="organization"
                           size="10"
                           value="{$form_data['sender_company']|escape:'htmlall':'utf-8'}"
                           idcounter="sender-company"
                           required-input="true"
                           counter="35"
                           fixed-width="lg">
            </ps-input-text>
            <ps-input-text name="sender_lastname"
                           label="{l s='Lastname' mod='colissimo'}"
                           autocomplete="family-name"
                           size="10"
                           value="{$form_data['sender_lastname']|escape:'htmlall':'utf-8'}"
                           idcounter="sender-lastname"
                           required-input="true"
                           counter="35"
                           fixed-width="lg">
            </ps-input-text>
            <ps-input-text name="sender_firstname"
                           label="{l s='Firstname' mod='colissimo'}"
                           autocomplete="given-name"
                           size="10"
                           value="{$form_data['sender_firstname']|escape:'htmlall':'utf-8'}"
                           idcounter="sender-firstname"
                           required-input="true"
                           counter="29"
                           fixed-width="lg">
            </ps-input-text>
            <ps-input-text name="sender_address1"
                           label="{l s='Address 1' mod='colissimo'}"
                           autocomplete="address-line1"
                           size="10"
                           value="{$form_data['sender_address1']|escape:'htmlall':'utf-8'}"
                           idcounter="sender-address1"
                           required-input="true"
                           counter="35"
                           fixed-width="xxl">
            </ps-input-text>
            <ps-input-text name="sender_address2"
                           label="{l s='Address 2' mod='colissimo'}"
                           autocomplete="address-line2"
                           size="10"
                           value="{$form_data['sender_address2']|escape:'htmlall':'utf-8'}"
                           idcounter="sender-address2"
                           counter="35"
                           fixed-width="xxl">
            </ps-input-text>
            <ps-input-text name="sender_address3"
                           label="{l s='Address 3' mod='colissimo'}"
                           autocomplete="address-line3"
                           size="10"
                           value="{$form_data['sender_address3']|escape:'htmlall':'utf-8'}"
                           idcounter="sender-address3"
                           counter="35"
                           fixed-width="xxl">
            </ps-input-text>
            <ps-input-text name="sender_address4"
                           label="{l s='Address 4' mod='colissimo'}"
                           size="10"
                           value="{$form_data['sender_address4']|escape:'htmlall':'utf-8'}"
                           idcounter="sender-address4"
                           counter="35"
                           fixed-width="xxl">
            </ps-input-text>
            <ps-input-text name="sender_city"
                           label="{l s='City' mod='colissimo'}"
                           autocomplete="address-level2"
                           size="10"
                           value="{$form_data['sender_city']|escape:'htmlall':'utf-8'}"
                           idcounter="sender-city"
                           required-input="true"
                           counter="35"
                           fixed-width="xxl">
            </ps-input-text>
            <ps-input-text name="sender_zipcode"
                           label="{l s='Zipcode' mod='colissimo'}"
                           autocomplete="postal-code"
                           size="5"
                           value="{$form_data['sender_zipcode']|escape:'htmlall':'utf-8'}"
                           required-input="true"
                           fixed-width="sm">
            </ps-input-text>
            <ps-select label="{l s='Country' mod='colissimo'}"
                       name="sender_country"
                       chosen="true"
                       required-input="true"
                       fixed-width="lg">
              {foreach from=$address_countries key=iso item=name}
                <option value="{$iso|escape:'html':'utf-8'}" {if $form_data['sender_country'] == $iso }selected{/if}>{$name|escape:'html':'utf-8'}</option>
              {/foreach}
            </ps-select>
            <div class="validate-phone">
              <div class="form-group">
                <label class="control-label col-lg-3 required"> {l s='Phone' mod='colissimo'}</label>
                <div class="col-lg-9">
                  <input type="text"
                         id="sender-phone"
                         name="sender_phone[main]"
                         class="input fixed-width-xl"
                         value="{$form_data['sender_phone']|escape:'htmlall':'utf-8'}"
                         required="required">
                </div>
              </div>
              <img src="{$colissimo_img_path|escape:'html':'utf-8'}icons/icon_valid.png"
                   class="colissimo-mobile-valid js-colissimo-mobile-valid"/>
              <img src="{$colissimo_img_path|escape:'html':'utf-8'}icons/icon_invalid.png"
                   class="colissimo-mobile-invalid js-colissimo-mobile-invalid"/>
            </div>
            <input type="hidden" class="js-colissimo-is-mobile-valid" name="colissimo_is_mobile_valid" value=""/>
            <ps-input-text name="sender_email"
                           label="{l s='Email' mod='colissimo'}"
                           autocomplete="email"
                           size="10"
                           value="{$form_data['sender_email']|escape:'htmlall':'utf-8'}"
                           idcounter="sender-email"
                           required-input="true"
                           counter="80"
                           fixed-width="xxl">
            </ps-input-text>
          </div>
          <ps-panel-footer>
            <ps-panel-footer-submit title="{l s='Save' mod='colissimo'}"
                                    icon="process-icon-save"
                                    direction="right"
                                    name="submitColissimoSenderAddressConfigForm">
            </ps-panel-footer-submit>
          </ps-panel-footer>
        </ps-panel>
      </form>
    </div>
    <div class="col-md-6 col-sm-12" id="colissimo-return-address">
      <form class="form-horizontal adr-form"
            action="#"
            name="colissimo_return_config_form"
            id="colissimo-return-config-form"
            method="post"
            enctype="multipart/form-data">
        <ps-panel header="{l s='Return address' mod='colissimo'}">
          <div class="row">
            <ps-input-text name="return_company"
                           label="{l s='Company' mod='colissimo'}"
                           autocomplete="organization"
                           size="10"
                           value="{$form_data['return_company']|escape:'htmlall':'utf-8'}"
                           idcounter="return-company"
                           required-input="true"
                           counter="35"
                           fixed-width="lg">
            </ps-input-text>
            <ps-input-text name="return_lastname"
                           label="{l s='Lastname' mod='colissimo'}"
                           autocomplete="family-name"
                           size="10"
                           value="{$form_data['return_lastname']|escape:'htmlall':'utf-8'}"
                           idcounter="return-lastname"
                           required-input="true"
                           counter="35"
                           fixed-width="lg">
            </ps-input-text>
            <ps-input-text name="return_firstname"
                           label="{l s='Firstname' mod='colissimo'}"
                           autocomplete="given-name"
                           size="10"
                           value="{$form_data['return_firstname']|escape:'htmlall':'utf-8'}"
                           idcounter="return-firstname"
                           required-input="true"
                           counter="29"
                           fixed-width="lg">
            </ps-input-text>
            <ps-input-text name="return_address1"
                           label="{l s='Address 1' mod='colissimo'}"
                           autocomplete="address-line1"
                           size="10"
                           value="{$form_data['return_address1']|escape:'htmlall':'utf-8'}"
                           idcounter="return-address1"
                           required-input="true"
                           counter="35"
                           fixed-width="xxl">
            </ps-input-text>
            <ps-input-text name="return_address2"
                           label="{l s='Address 2' mod='colissimo'}"
                           autocomplete="address-line2"
                           size="10"
                           value="{$form_data['return_address2']|escape:'htmlall':'utf-8'}"
                           idcounter="return-address2"
                           counter="35"
                           fixed-width="xxl">
            </ps-input-text>
            <ps-input-text name="return_address3"
                           label="{l s='Address 3' mod='colissimo'}"
                           autocomplete="address-line3"
                           size="10"
                           value="{$form_data['return_address3']|escape:'htmlall':'utf-8'}"
                           idcounter="return-address3"
                           counter="35"
                           fixed-width="xxl">
            </ps-input-text>
            <ps-input-text name="return_address4"
                           label="{l s='Address 4' mod='colissimo'}"
                           size="10"
                           value="{$form_data['return_address4']|escape:'htmlall':'utf-8'}"
                           idcounter="return-address4"
                           counter="35"
                           fixed-width="xxl">
            </ps-input-text>
            <ps-input-text name="return_city"
                           label="{l s='City' mod='colissimo'}"
                           autocomplete="address-level2"
                           size="10"
                           value="{$form_data['return_city']|escape:'htmlall':'utf-8'}"
                           idcounter="return-city"
                           required-input="true"
                           counter="35"
                           fixed-width="xxl">
            </ps-input-text>
            <ps-input-text name="return_zipcode"
                           label="{l s='Zipcode' mod='colissimo'}"
                           autocomplete="postal-code"
                           size="5"
                           value="{$form_data['return_zipcode']|escape:'htmlall':'utf-8'}"
                           required-input="true"
                           fixed-width="sm">
            </ps-input-text>
            <ps-select label="{l s='Country' mod='colissimo'}"
                       name="return_country"
                       chosen="true"
                       required-input="true"
                       fixed-width="lg">
              {foreach from=$address_countries key=iso item=name}
                <option value="{$iso|escape:'html':'utf-8'}" {if $form_data['return_country'] == $iso }selected{/if}>{$name|escape:'html':'utf-8'}</option>
              {/foreach}
            </ps-select>
            <div class="validate-phone">
              <div class="form-group">
                <label class="control-label col-lg-3 required"> {l s='Phone' mod='colissimo'}</label>
                <div class="col-lg-9">
                  <input type="text"
                         id="return-phone"
                         name="return_phone[main]"
                         class="input fixed-width-xl"
                         value="{$form_data['return_phone']|escape:'htmlall':'utf-8'}"
                         required="required">
                </div>
              </div>
              <img src="{$colissimo_img_path|escape:'html':'utf-8'}icons/icon_valid.png"
                   class="colissimo-mobile-valid js-colissimo-mobile-valid"/>
              <img src="{$colissimo_img_path|escape:'html':'utf-8'}icons/icon_invalid.png"
                   class="colissimo-mobile-invalid js-colissimo-mobile-invalid"/>
            </div>
            <input type="hidden" class="js-colissimo-is-mobile-valid" name="colissimo_is_mobile_valid" value=""/>
            <ps-input-text name="return_email"
                           label="{l s='Email' mod='colissimo'}"
                           autocomplete="email"
                           size="10"
                           value="{$form_data['return_email']|escape:'htmlall':'utf-8'}"
                           idcounter="return-email"
                           required-input="true"
                           counter="80"
                           fixed-width="xxl">
            </ps-input-text>
          </div>
          <ps-panel-footer>
            <ps-panel-footer-submit title="{l s='Save' mod='colissimo'}"
                                    icon="process-icon-save"
                                    direction="right"
                                    name="submitColissimoReturnAddressConfigForm">
            </ps-panel-footer-submit>
          </ps-panel-footer>
        </ps-panel>
      </form>
    </div>
  </div>
</ps-panel>

{literal}
<script type="text/javascript">
    var genericErrorMessage = "{/literal}{l s='Webservice connexion could not be checked' mod='colissimo'}{literal}";
    var colissimoCredentialsToken = '{/literal}{getAdminToken tab='AdminColissimoTestCredentials'}{literal}';
    var addressErrorMessage = "{/literal}{l s='An error occured. Please try again.' mod='colissimo'}{literal}";
    var token = '{/literal}{getAdminToken tab='AdminModules'}{literal}';
    var colissimoSenderPhone = '{/literal}{$form_data['sender_country']|escape:'htmlall':'utf-8'}{literal}';
    var colissimoReturnPhone = '{/literal}{$form_data['return_country']|escape:'htmlall':'utf-8'}{literal}';
    var onlyCountries = {/literal}{$address_countries|array_keys|json_encode}{literal};

    $(document).ready(function () {
        $(document).on('click', '#colissimo-check-credentials', function () {
            var colissimoCheckCredentials = $('#colissimo-check-credentials');

            testWSCredentials(colissimoCheckCredentials);
        });
    });
</script>
{/literal}
