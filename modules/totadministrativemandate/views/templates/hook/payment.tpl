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

{if version_compare($smarty.const._PS_VERSION_, '1.6', '>')}
<style>
p.payment_module a.totadministrativemandate{
background-color: {if isset($bgCol) && $useCustomStyle}{$bgCol}{else}#fbfbfb{/if};
{if $btnPic && $useCustomStyle}
    background-image: url({$btnPic});
{else}
    background-image: url({$this_path|escape:'htmlall':'UTF-8'}views/img/logo-payment.png);
{/if}
background-repeat: no-repeat;
background-position: 15px 25px;
background-size: auto 38px;
    {if isset($txtCol) && $useCustomStyle}
        color: {$txtCol};
    {/if}
}

p.payment_module a.totadministrativemandate:hover{
    background-color: {if isset($bgColHov) && $useCustomStyle}{$bgColHov}{else}#fbfbfb{/if};
    {if isset($txtColHov) && $useCustomStyle}
        color: {$txtColHov};
    {/if}
}

p.payment_module a.totadministrativemandate:after {
display: block;
content: "\f054";
position: absolute;
right: 15px;
margin-top: -11px;
top: 50%;
font-family: "FontAwesome";
font-size: 25px;
height: 22px;
width: 14px;
color: {if isset($txtCol) && $useCustomStyle}{$txtCol}{else}#777{/if};
}

p.payment_module a.totadministrativemandate span {
    {if isset($txtCol) && $useCustomStyle}
        color: {$txtCol};
    {/if}
}

p.payment_module a.totadministrativemandate:hover span {
    {if isset($txtColHov) && $useCustomStyle}
        color: {$txtColHov};
    {/if}
}
</style>
{/if}

<p class="payment_module">
    <a class="totadministrativemandate" href="{$this_path_ssl|escape:'htmlall':'UTF-8'}" title="{l s='Pay by administrative mandate' mod='totadministrativemandate'}">
      {if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
          <img src="{$this_path|escape:'htmlall':'UTF-8'}/views/img/logo-payment.png" alt="{l s='Pay by  administrative mandate' mod='totadministrativemandate'}" width="86" height="49" />
        {/if}
        {l s='Pay by administrative mandate' mod='totadministrativemandate'} <span>{l s='(order process will be longer)' mod='totadministrativemandate'}</span>
    </a>
</p>
