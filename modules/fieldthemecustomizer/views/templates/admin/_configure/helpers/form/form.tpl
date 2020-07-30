{extends file="helpers/form/form.tpl"}

{block name="defaultForm"}
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div id="fieldthemecustomizer-tabs" class="col-lg-2">
                    {foreach $fieldtabs as $tabTitle => $tabClass}
                        <span class="tab list-group-item" data-tab="{$tabClass}">
                            {$tabTitle}
                        </span>
                    {/foreach}
                </div>
                <div class="col-lg-10">
                    {$smarty.block.parent}
                </div>
            </div>
        </div>
    </div>
{/block}

{block name="label"}

    {if $input['name'] == "FIELD_bodyBackgroundColor" || $input['name'] == "FIELD_breadcrumbBackgroundImage"}

        <br /><br /><br />
        {$smarty.block.parent}

    {else}
        {$smarty.block.parent}
    {/if}


{/block}

{block name="input"}

    {if $input.name == "FIELD_bodyBackgroundImage" || $input.name == "FIELD_backgroundImage" || $input.name == "FIELD_breadcrumbBackgroundImage"}

        {if isset($fields_value[$input.name]) && $fields_value[$input.name] != ''}
            <img src="{$imagePath}{$fields_value[$input.name]}" style="width:100%;max-width:100px;"/><br />
            <a href="{$current}&{$identifier}={$form_id}&token={$token}&deleteConfig={$input.name}">
                <img src="../img/admin/delete.gif" alt="{l s='Delete'}" /> {l s='Delete'}
            </a>
            <small>{l s='Do not forget to save the options after deleted the image!'}</small>
            <br /><br />
        {/if}

        {$smarty.block.parent}

    {else}

        {$smarty.block.parent}

    {/if}

{/block}