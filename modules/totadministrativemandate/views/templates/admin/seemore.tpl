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

<ps-panel icon="icon-cogs" img="../img/t/AdminBackup.gif" header="{l s='These modules might interest you' mod='totadministrativemandate'}">
    <p>{l s='Choose another module developed by 202 for your e-commerce store is to choose a perfect integration of all the essential functionalities to manage your stocks.' mod='totadministrativemandate'}</p>

    <div class="row">
        {foreach from=$seemore.recommended item=module}
            <div class="col-sm-6 col-xs-12">
                <fieldset class="totmodule">
                    <div class="panel-body panel">
                        <div class="totmodule_img">
                            <img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/seemore/{$module.short_name|escape:'htmlall':'UTF-8'}.png" alt="Sample Image">
                        </div>
                        <div class="totmodule_text">
                            <h4>{$module.name|escape:'htmlall':'UTF-8'}</h4>
                            <p>{$module.descr|escape:'htmlall':'UTF-8'}</p>
                        </div>
                        <div class="totmodule_button">
                            {if $module.installed}
                                <a href="{$module.link|escape:'htmlall':'UTF-8'}" class="button configure" role="button" target="_blank">
                                    {l s='Configuring' mod='totadministrativemandate'}</a>
                            {else}
                                <a href="{$module.link|escape:'htmlall':'UTF-8'}" class="button discover" role="button" target="_blank">
                                    {l s='Discover on Addons' mod='totadministrativemandate'}</a>
                            {/if}
                        </div>
                    </div>
                </fieldset>
            </div>
        {/foreach}
    </div>
</ps-panel>