{*
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    202-ecommerce
 *  @copyright 2010-2016 202-ecommerce
 *  @license   LICENSE.txt
 *
*}

<ps-tabs position="top">
    <ps-tab label="{l s='General' mod='totadministrativemandate'}" active="true" id="general" icon="icon-cog" img="../img/t/AdminBackup.gif" fa="cogs">


      {include file='./form.tpl'}
      {include file='./rib_zone.tpl'}

      <div id="position"></div>


    </ps-tab>
    {if $PSVersion != '17'}
        <ps-tab label="{l s='Customization' mod='totadministrativemandate'}" id="custom">
            {include file='./custom.tpl'}
        </ps-tab>
    {/if}


    <ps-tab label="{l s='See more' mod='totadministrativemandate'}" id="seemore" icon="icon-AdminParentModules"
            img="../img/t/AdminBackup.gif">

        {include file='./seemore.tpl'}
    </ps-tab>
</ps-tabs>