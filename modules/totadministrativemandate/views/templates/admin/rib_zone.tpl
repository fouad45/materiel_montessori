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


<ps-panel icon="icon-cogs" img="../img/t/AdminBackup.gif" header="{l s='Contact details' mod='totadministrativemandate'}">
    <form class="tot-form form-horizontal" action="{$module_link|escape:'htmlall':'UTF-8'}" method="post">
        <fieldset class="bg_table_">
            <div class="container1">
                <table id="form">
                    <tr>
                        <td>
                            <label for="owner">{l s='Account owner' mod='totadministrativemandate'}</label>
                        </td>
                        <td>
                            <input id="owner" type="text" name="owner" value="{$config.TOTADMINISTRATIVEMANDATE_OWNER|escape:'htmlall':'UTF-8'}"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-top">
                            <label for="details">{l s='Details' mod='totadministrativemandate'}</label>
                        </td>
                        <td>
                            <textarea id="details" name="details" rows="4">{$config.TOTADMINISTRATIVEMANDATE_DETAILS|escape:'htmlall':'UTF-8'}</textarea>
                            <p>{l s='Such as bank branch, IBAN number, BIC, etc.' mod='totadministrativemandate'}</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-top">
                            <label for="rib_address">{l s='Bank address' mod='totadministrativemandate'}</label>
                        </td>
                        <td>
                            <textarea id="rib_address" name="rib_address" rows="4">{$config.TOTADMINISTRATIVEMANDATE_ADDRESS|escape:'htmlall':'UTF-8'}</textarea>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="containerlarge">
                <center><input class="button btn btn-primary" name="btnRIB" value="{l s='Update settings' mod='totadministrativemandate'}" type="submit" /></center>
            </div>
        </fieldset>
    </form>
