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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2019 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


<ps-panel icon="icon-cogs" img="../img/t/AdminBackup.gif" header="{l s='Contact for order slip' mod='totadministrativemandate'}">

    <form class="tot-form form-horizontal" method="POST" action="{$module_link|escape:'htmlall':'UTF-8'}">
        <fieldset class="bg_table_">
            <div class="container1">
            <table>
                
                    <tr>
                        <td>
                            <label for="company">{l s='Company name' mod='totadministrativemandate'}</label>
                        </td>
                        <td>
                            <div>
                                <input type="text" name="company" id="company" value="{$configMessage.TOTADMINISTRATIVEMANDATE_CO_NAME|escape:'htmlall':'UTF-8'}"/>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-top">
                            <label for="address">{l s='Address for receipt of order slip.' mod='totadministrativemandate'}</label>
                        </td>
                        <td>
                            <div>
                                <textarea rows="5" name="address">{$configMessage.TOTADMINISTRATIVEMANDATE_CO_ADDR|escape:'htmlall':'UTF-8'}</textarea>
                            </div>
                        </td>
                    </tr>

            </table>
            </div>
            <div class="container1">
            <table>
                
                    <tr>
                        <td>
                            <label for="phone">
                                {l s='Phone' mod='totadministrativemandate'}
                            </label>
                        </td>
                        <td>
                            <input type="text" name="phone" id="phone" size="40" value="{$configMessage.TOTADMINISTRATIVEMANDATE_PHONE|escape:'htmlall':'UTF-8'}" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="fax">
                                {l s='Fax' mod='totadministrativemandate'}
                            </label>
                        </td>
                        <td>
                            <input type="text" name="fax" id="fax" size="40" value="{$configMessage.TOTADMINISTRATIVEMANDATE_FAX|escape:'htmlall':'UTF-8'}" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="mail">
                                {l s='Mail' mod='totadministrativemandate'}
                            </label>
                        </td>
                        <td>
                            <input type="text" name="mail" id="mail" size="40" value="{$configMessage.TOTADMINISTRATIVEMANDATE_MAIL|escape:'htmlall':'UTF-8'}" />
                        </td>
                    </tr>
                
            </table>
            </div>
            <div class="containerlarge">
                <center><input type="submit" class="button btn btn-primary " name="btnSubmit" value="{l s='Save' mod='totadministrativemandate'}" /></center>
            </div>

        </fieldset>
    </form>
</ps-panel> 
