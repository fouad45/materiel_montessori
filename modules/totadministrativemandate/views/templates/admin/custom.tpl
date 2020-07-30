{*
* @author 202 ecommerce <contact@202-ecommerce.com>
* @copyright  Copyright (c) 202 ecommerce 2014
* @license    Commercial license
*}

<ps-panel header="{l s='Button customization' mod='totadministrativemandate'}">
    <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
        <ps-radios label="{l s='Choose button style' mod='totadministrativemandate'}" class="modeCustomStyle">
            <ps-radio name="useCustomStyle" value="0"{if !$useCustomStyle} checked="true" {/if}>{l s='Use default style' mod='totadministrativemandate'}</ps-radio>
            <ps-radio name="useCustomStyle" value="1"{if $useCustomStyle} checked="true" {/if}>{l s='Customize' mod='totadministrativemandate'}</ps-radio>
        </ps-radios>

        <div class="customStyle">
            <ps-color-picker label="Text Color" name="txtCol" {if $txtCol}color="{$txtCol}"{/if} class="colorPicker"></ps-color-picker>
            <ps-color-picker label="Background Color" name="bgCol" {if $bgCol}color="{$bgCol}"{/if} class="colorPicker"></ps-color-picker>
            <ps-color-picker label="Text Color (Hover)" name="txtColHov" {if $txtColHov}color="{$txtColHov}"{/if} class="colorPicker"></ps-color-picker>
            <ps-color-picker label="Background Color (Hover)" name="bgColHov" {if $bgColHov}color="{$bgColHov}"{/if} class="colorPicker"></ps-color-picker>
            <ps-form-group label="Upload Image" class="customImage">
                {if $btnPic}
                    <img src="{$btnPic}" alt="">
                {/if}
                <input type="file" name="btnPic">
            </ps-form-group>
        </div>

        <ps-panel-footer>
            <ps-panel-footer-submit title="save" icon="process-icon-save" direction="right" name="totadm_custom_submit"></ps-panel-footer-submit>
        </ps-panel-footer>
    </form>
</ps-panel>
