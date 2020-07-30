<form id="fieldmm_editmenu_{$menuItem->id}" class="fieldmm_editmenuitemform form-horizontal">
    <div class="panel">

        <div class="panel-heading">
            <i class="icon-edit"></i> {l s='Edit Menu Item'}
        </div>

        {* HIDDEN FIELDS *}
        <input type="hidden" name="id_fieldmegamenuitem" id="id_fieldmegamenuitem_{$menuItem->id}" value="{$menuItem->id}" />
        <input type="hidden" name="menu_type" id="menu_type_{$menuItem->id}" value="{$menuItem->menu_type}" />
        {* END - HIDDEN FIELDS *}



        {* NON-EDITABLE INFO FIELDS *}
        {if (isset($menuItem->item_info) && $menuItem->item_info)}
            <div class="form-group">
                <div class="col-lg-2 control-label">{$menuItem->item_info_label}</div>
                <div class="col-lg-10 fieldmm_item_info"><a href="{$menuItem->item_info_link}" target="_blank"><strong>{$menuItem->item_info_name}</strong></a></div>
            </div>
        {/if}
        {* END - NON-EDITABLE INFO FIELDS *}



        {* TITLE FIELD | only available for Custom Link*}
        {if ($menuItem->menu_type == 1)}
            <div class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach $languages as $language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_editmenu_title_{$menuItem->id}_{$language.id_lang}" class="control-label col-lg-2 required">{l s='Title'}</label>
                            <div class="col-lg-8">
                                <input type="text" id="fieldmm_editmenu_title_{$menuItem->id}_{$language.id_lang}" name="fieldmm_editmenu_title_{$menuItem->id}_{$language.id_lang}" value="{$menuItem->title[$language.id_lang]}" />
                            </div>

                            {if $languages|count > 1}
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                        {$language.iso_code}
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        {foreach from=$languages item=lang}
                                            <li><a href="javascript:hideOtherLanguage({$lang.id_lang});" tabindex="-1">{$lang.name}</a></li>
                                        {/foreach}
                                    </ul>
                                </div>
                            {/if}

                            {if $languages|count > 1}
                                </div>
                            {/if}

                        {/foreach}
                    </div>

                </div>
            </div>
        {/if}
        {* END - TITLE FIELD *}



        {* LINK FIELD | only available for Custom Link *}
        {if ($menuItem->menu_type == 1)}
            <div class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach $languages as $language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_editmenu_link_{$menuItem->id}_{$language.id_lang}" class="control-label col-lg-2">{l s='Link'}</label>
                            <div class="col-lg-8">
                                <input type="text" id="fieldmm_editmenu_link_{$menuItem->id}_{$language.id_lang}" name="fieldmm_editmenu_link_{$menuItem->id}_{$language.id_lang}" value="{$menuItem->link[$language.id_lang]}" />
                            </div>

                            {if $languages|count > 1}
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                        {$language.iso_code}
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        {foreach from=$languages item=lang}
                                            <li><a href="javascript:hideOtherLanguage({$lang.id_lang});" tabindex="-1">{$lang.name}</a></li>
                                        {/foreach}
                                    </ul>
                                </div>
                            {/if}

                            {if $languages|count > 1}
                                </div>
                            {/if}

                        {/foreach}
                    </div>

                </div>
            </div>
        {/if}
        {* END - LINK FIELD *}



        {* DESCRIPTION FIELD | not available for Custom Content and Divider *}
        {if ($menuItem->menu_type != 7) && ($menuItem->menu_type != 8)}
            <div class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach $languages as $language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_editmenu_description_{$menuItem->id}_{$language.id_lang}" class="control-label col-lg-2">{l s='Description'}</label>
                            <div class="col-lg-8">
                                <input type="text" id="fieldmm_editmenu_description_{$menuItem->id}_{$language.id_lang}" name="fieldmm_editmenu_description_{$menuItem->id}_{$language.id_lang}" value="{$menuItem->description[$language.id_lang]}" />
                            </div>

                            {if $languages|count > 1}
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                        {$language.iso_code}
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        {foreach from=$languages item=lang}
                                            <li><a href="javascript:hideOtherLanguage({$lang.id_lang});" tabindex="-1">{$lang.name}</a></li>
                                        {/foreach}
                                    </ul>
                                </div>
                            {/if}

                            {if $languages|count > 1}
                                </div>
                            {/if}

                        {/foreach}
                    </div>

                </div>
            </div>
        {/if}
        {* END - DESCRIPTION FIELD *}



        {* CUSTOM CONTENT FIELD | only available for Custom Content *}
        {if ($menuItem->menu_type == 7)}
            <div class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">
                        {assign var=use_textarea_autosize value=true}
                        {foreach $languages as $language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_editmenu_customcontent_{$menuItem->id}_{$language.id_lang}" class="control-label col-lg-2">{l s='Custom content'}</label>
                            <div class="col-lg-8">
                                {*<input type="hidden" id="fieldmm_editmenu_customcontent_{$menuItem->id}_{$language.id_lang}" name="fieldmm_editmenu_customcontent_{$menuItem->id}_{$language.id_lang}" class="custom_content"/>*}
                                <div id="fieldmm_editmenu_customcontent_{$menuItem->id}_{$language.id_lang}" class="custom_content">
                                    {$menuItem->content[$language.id_lang]}
                                </div>
                            </div>

                            {if $languages|count > 1}
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                        {$language.iso_code}
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        {foreach from=$languages item=lang}
                                            <li><a href="javascript:hideOtherLanguage({$lang.id_lang});" tabindex="-1">{$lang.name}</a></li>
                                        {/foreach}
                                    </ul>
                                </div>
                            {/if}

                            {if $languages|count > 1}
                                </div>
                            {/if}

                        {/foreach}
                    </div>

                </div>
            </div>
        {/if}
        {* END - CUSTOM CONTENT FIELD *}



        {* CUSTOM CLASS FIELD *}
        <div class="form-group">
            <div class="col-lg-12">
                <div class="form-group">
                    <label for="fieldmm_editmenu_class_{$menuItem->id}" class="control-label col-lg-2">{l s='Custom class'}</label>
                    <div class="col-lg-8">
                        <input type="text" id="fieldmm_editmenu_class_{$menuItem->id}" name="fieldmm_editmenu_class_{$menuItem->id}" value="{$menuItem->menu_class}" />
                    </div>
                </div>

            </div>
        </div>
        {* END - CUSTOM CLASS FIELD *}



        {* ICON CLASS FIELD | not available for Custom Content and Divider *}
        {if ($menuItem->menu_type != 7 && $menuItem->menu_type != 8)}
        <div class="form-group">
            <div class="col-lg-12">
                <div class="form-group">
                    <label for="fieldmm_editicon_class_{$menuItem->id}" class="control-label col-lg-2">{l s='Icon class'}</label>
                    <div class="col-lg-8">
                        <input type="text" id="fieldmm_editicon_class_{$menuItem->id}" name="fieldmm_editicon_class_{$menuItem->id}" value="{$menuItem->icon_class}" />
                    </div>
                </div>

            </div>
        </div>
        {* END - ICON CLASS FIELD *}
        {/if}



        {* TARGET FIELD  | not available for Custom Content and Divider *}
        {if ($menuItem->menu_type != 7) && ($menuItem->menu_type != 8)}
            <div class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="fieldmm_editmenu_target_{$menuItem->id}" class="control-label col-lg-2">{l s='Open in new tab'}</label>
                        <div class="col-lg-8">
                            <input type="checkbox" id="fieldmm_editmenu_target_{$menuItem->id}" name="fieldmm_editmenu_target_{$menuItem->id}" {if ($menuItem->open_in_new)}checked="checked"{/if} />
                        </div>
                    </div>
                </div>
            </div>
        {/if}
        {* END - TARGET FIELD *}



        {* SHOW IMAGE FIELD | only available for Manufacturers, Suppliers and Products *}
        {if ($menuItem->menu_type == 3) || ($menuItem->menu_type == 4) || ($menuItem->menu_type == 5) }
            <div class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="fieldmm_editmenu_showimage_{$menuItem->id}" class="control-label col-lg-2">{l s='Show image'}</label>
                        <div class="col-lg-8">
                            <input type="checkbox" id="fieldmm_editmenu_showimage_{$menuItem->id}" name="fieldmm_editmenu_showimage_{$menuItem->id}" {if ($menuItem->show_image)}checked="checked"{/if} />
                        </div>
                    </div>
                </div>
            </div>
        {/if}
        {* END - SHOW IMAGE FIELD *}



        {* COLUMN SIZE FIELD | not available for Divider *}
        {if ($menuItem->menu_type != 8)}
            <div class="form-group fieldmm_editmenu_layout_formgroup">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="fieldmm_editmenu_layout_{$menuItem->id}" class="control-label col-lg-2">{l s='Column size'}</label>
                        <div class="col-lg-8">
                            <label class="fieldmm_editmenu_layout_label"><input type="radio" name="fieldmm_editmenu_layout_{$menuItem->id}" {if ($menuItem->menu_layout == '')}checked{/if} value="auto" /> {l s='Auto'}</label>
                            <label class="fieldmm_editmenu_layout_label"><input type="radio" name="fieldmm_editmenu_layout_{$menuItem->id}" {if ($menuItem->menu_layout == 'menucol-1-1')}checked{/if} value="menucol-1-1" /> {l s='1/1'}</label>
                            <label class="fieldmm_editmenu_layout_label"><input type="radio" name="fieldmm_editmenu_layout_{$menuItem->id}" {if ($menuItem->menu_layout == 'menucol-1-2')}checked{/if} value="menucol-1-2" /> {l s='1/2'}</label>
                            <label class="fieldmm_editmenu_layout_label"><input type="radio" name="fieldmm_editmenu_layout_{$menuItem->id}" {if ($menuItem->menu_layout == 'menucol-1-3')}checked{/if} value="menucol-1-3" /> {l s='1/3'}</label>
                            <label class="fieldmm_editmenu_layout_label"><input type="radio" name="fieldmm_editmenu_layout_{$menuItem->id}" {if ($menuItem->menu_layout == 'menucol-2-3')}checked{/if} value="menucol-2-3" /> {l s='2/3'}</label>
                            <label class="fieldmm_editmenu_layout_label"><input type="radio" name="fieldmm_editmenu_layout_{$menuItem->id}" {if ($menuItem->menu_layout == 'menucol-1-4')}checked{/if} value="menucol-1-4" /> {l s='1/4'}</label>
                            <label class="fieldmm_editmenu_layout_label"><input type="radio" name="fieldmm_editmenu_layout_{$menuItem->id}" {if ($menuItem->menu_layout == 'menucol-3-4')}checked{/if} value="menucol-3-4" /> {l s='3/4'}</label>
                            <label class="fieldmm_editmenu_layout_label"><input type="radio" name="fieldmm_editmenu_layout_{$menuItem->id}" {if ($menuItem->menu_layout == 'menucol-1-5')}checked{/if} value="menucol-1-5" /> {l s='1/5'}</label>
                            <label class="fieldmm_editmenu_layout_label"><input type="radio" name="fieldmm_editmenu_layout_{$menuItem->id}" {if ($menuItem->menu_layout == 'menucol-1-6')}checked{/if} value="menucol-1-6" /> {l s='1/6'}</label>
                            <label class="fieldmm_editmenu_layout_label"><input type="radio" name="fieldmm_editmenu_layout_{$menuItem->id}" {if ($menuItem->menu_layout == 'menucol-1-10')}checked{/if} value="menucol-1-10" /> {l s='1/10'}</label>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
        {* END - COLUMN SIZE FIELD *}

        <div class="panel-footer">
            <button type="submit" value="1" id="fieldmm_editmenu_submit_{$menuItem->id}" name="fieldmm_editmenu_submit_{$menuItem->id}" class="btn btn-default pull-right">
                <i class="process-icon-new"></i> Update Menu Item
            </button>
            <a id="fieldmm_editmenu_delete_{$menuItem->id}" name="fieldmm_editmenu_delete_{$menuItem->id}" class="btn btn-default fieldmm_editmenu_delete">
                <i class="process-icon-delete"></i> Delete Menu Item
            </a>
        </div>

    </div>
</form>