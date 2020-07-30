<script>
    var fieldmm_ajax_url = '{$fieldmm_ajax_url}';
    var fieldmm_id_fieldmegamenu = {$fieldmm_id_fieldmegamenu};

    // Rich Text Editor related
    var iso = '{$iso|addslashes}';
    var pathCSS = '{$smarty.const._THEME_CSS_DIR_|addslashes}';
    var ad = '{$ad|addslashes}';
</script>

<div class="left-column col-lg-4">

    {* CUSTOM LINK FORM *}
    <form id="fieldmm_customlink" class="fieldmm_addmenuitemform form-horizontal">

        <input type="hidden" name="menu_type" value="1" />

        <div class="panel">
            <div class="panel-heading">
                <i class="icon-edit"></i> {l s='Add Custom Link / Text'}
            </div>

            <div class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                            <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                                <label for="fieldmm_customlink_title_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Title'}</label>
                                <div class="col-lg-7">
                                    <input type="text" id="fieldmm_customlink_title_{$language.id_lang}" name="fieldmm_customlink_title_{$language.id_lang}" class="clearable" />
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

            <div class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                            <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                                <label for="fieldmm_customlink_link_{$language.id_lang}" class="control-label col-lg-3">{l s='Link'}</label>
                                <div class="col-lg-7">
                                    <input type="text" id="fieldmm_customlink_link_{$language.id_lang}" name="fieldmm_customlink_link_{$language.id_lang}" class="clearable" />
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

            <div class="panel-footer">
                <button type="submit" value="1" id="fieldmm_customlink_submit" name="fieldmm_customlink_submit" class="btn btn-default pull-right">
                    <i class="process-icon-new"></i> {l s='Add Custom Link / Text'}
                </button>
            </div>
        </div>

    </form>
    {* END - CUSTOM LINK FORM *}



    {* CATEGORY LINK FORM *}
    <form id="fieldmm_categorylink" class="fieldmm_addmenuitemform form-horizontal">

        <input type="hidden" name="menu_type" value="2" />

        <div class="panel">
            <div class="panel-heading">
                <i class="icon-edit"></i> {l s='Add Category Link'}
            </div>

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                            <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                                <label for="fieldmm_categorylink_title_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Title'}</label>
                                <div class="col-lg-7">
                                    <input type="text" id="fieldmm_categorylink_title_{$language.id_lang}" name="fieldmm_categorylink_title_{$language.id_lang}" class="fieldmm_categorylink_title" />
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

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                            <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                                <label for="fieldmm_categorylink_link_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Link'}</label>
                                <div class="col-lg-7">
                                    <input type="text" id="fieldmm_categorylink_link_{$language.id_lang}" name="fieldmm_categorylink_link_{$language.id_lang}" class="fieldmm_categorylink_link" />
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

            <div class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">

                        <label for="fieldmm_categorylink_category" class="control-label col-lg-3">{l s='Category'}</label>
                        <div class="col-lg-7">
                            <select id="fieldmm_categorylink_category" name="fieldmm_categorylink_category">
                                {foreach $fieldmmCategories as $fieldmmCategory}
                                    <option value="{$fieldmmCategory.value}">{$fieldmmCategory.name}</option>
                                {/foreach}
                            </select>
                        </div>

                    </div>

                </div>
            </div>

            <div class="panel-footer">
                <button type="submit" value="1" id="fieldmm_categorylink_submit" name="fieldmm_categorylink_submit" class="btn btn-default pull-right">
                    <i class="process-icon-new"></i> {l s='Add Category Link'}
                </button>
            </div>
        </div>

    </form>
    {* END - CATEGORY LINK FORM *}



    {* PRODUCT LINK FORM *}
    <form id="fieldmm_productlink" class="fieldmm_addmenuitemform form-horizontal">

        <input type="hidden" name="menu_type" value="3" />

        <div class="panel">
            <div class="panel-heading">
                <i class="icon-edit"></i> {l s='Add Product Link'}
            </div>

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_productlink_title_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Title'}</label>
                            <div class="col-lg-7">
                                <input type="text" id="fieldmm_productlink_title_{$language.id_lang}" name="fieldmm_productlink_title_{$language.id_lang}"  class="fieldmm_productlink_title" />
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

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_productlink_link_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Link'}</label>
                            <div class="col-lg-7">
                                <input type="text" id="fieldmm_productlink_link_{$language.id_lang}" name="fieldmm_productlink_link_{$language.id_lang}" class="fieldmm_productlink_link" />
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

            <div class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="fieldmm_productlink_product" class="control-label col-lg-3">{l s='Product'}</label>
                        <div class="col-lg-7">
                            <input type="text" id="fieldmm_productlink_product" name="fieldmm_productlink_product" class="clearable" />
                            <p class="help-block">{l s='Start typing a product name...'}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-footer">
                <button type="submit" value="1" id="fieldmm_productlink_submit" name="fieldmm_productlink_submit" class="btn btn-default pull-right">
                    <i class="process-icon-new"></i> {l s='Add Product Link'}
                </button>
            </div>
        </div>

    </form>
    {* END - PRODUCT LINK FORM *}



    {* MANUFACTURER LINK FORM *}
    <form id="fieldmm_manufacturerlink" class="fieldmm_addmenuitemform form-horizontal">

        <input type="hidden" name="menu_type" value="4" />

        <div class="panel">
            <div class="panel-heading">
                <i class="icon-edit"></i> {l s='Add Manufacturer Link'}
            </div>

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_manufacturerlink_title_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Title'}</label>
                            <div class="col-lg-7">
                                <input type="text" id="fieldmm_manufacturerlink_title_{$language.id_lang}" name="fieldmm_manufacturerlink_title_{$language.id_lang}" class="fieldmm_manufacturerlink_title" />
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

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_manufacturerlink_link_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Link'}</label>
                            <div class="col-lg-7">
                                <input type="text" id="fieldmm_manufacturerlink_link_{$language.id_lang}" name="fieldmm_manufacturerlink_link_{$language.id_lang}" class="fieldmm_manufacturerlink_link" />
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

            <div class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">

                        <label for="fieldmm_manufacturerlink_manufacturer" class="control-label col-lg-3">{l s='Manufacturer'}</label>
                        <div class="col-lg-7">
                            <select id="fieldmm_manufacturerlink_manufacturer" name="fieldmm_manufacturerlink_manufacturer">
                                {foreach $fieldmmManufacturers as $fieldmmManufacturer}
                                    <option value="{$fieldmmManufacturer.value}">{$fieldmmManufacturer.name}</option>
                                {/foreach}
                            </select>
                        </div>

                    </div>

                </div>
            </div>

            <div class="panel-footer">
                <button type="submit" value="1" id="fieldmm_manufacturerlink_submit" name="fieldmm_manufacturerlink_submit" class="btn btn-default pull-right">
                    <i class="process-icon-new"></i> {l s='Add Manufacturer Link'}
                </button>
            </div>
        </div>

    </form>
    {* END - MANUFACTURER LINK FORM *}



    {* SUPPLIER LINK FORM *}
    <form id="fieldmm_supplierlink" class="fieldmm_addmenuitemform form-horizontal">

        <input type="hidden" name="menu_type" value="5" />

        <div class="panel">
            <div class="panel-heading">
                <i class="icon-edit"></i> {l s='Add Supplier Link'}
            </div>

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_supplierlink_title_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Title'}</label>
                            <div class="col-lg-7">
                                <input type="text" id="fieldmm_supplierlink_title_{$language.id_lang}" name="fieldmm_supplierlink_title_{$language.id_lang}" class="fieldmm_supplierlink_title" />
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

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_supplierlink_link_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Link'}</label>
                            <div class="col-lg-7">
                                <input type="text" id="fieldmm_supplierlink_link_{$language.id_lang}" name="fieldmm_supplierlink_link_{$language.id_lang}" class="fieldmm_supplierlink_link" />
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

            <div class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">

                        <label for="fieldmm_supplierlink_category" class="control-label col-lg-3">{l s='Supplier'}</label>
                        <div class="col-lg-7">
                            <select id="fieldmm_supplierlink_supplier" name="fieldmm_supplierlink_supplier">
                                {foreach $fieldmmSuppliers as $fieldmmSupplier}
                                    <option value="{$fieldmmSupplier.value}">{$fieldmmSupplier.name}</option>
                                {/foreach}
                            </select>
                        </div>

                    </div>

                </div>
            </div>

            <div class="panel-footer">
                <button type="submit" value="1" id="fieldmm_supplierlink_submit" name="fieldmm_supplierlink_submit" class="btn btn-default pull-right">
                    <i class="process-icon-new"></i> {l s='Add Supplier Link'}
                </button>
            </div>
        </div>

    </form>
    {* END - SUPPLIER LINK FORM *}



    {* CMS PAGE LINK FORM *}
    <form id="fieldmm_cmspagelink" class="fieldmm_addmenuitemform form-horizontal">

        <input type="hidden" name="menu_type" value="6" />

        <div class="panel">
            <div class="panel-heading">
                <i class="icon-edit"></i> {l s='Add CMS Page Link'}
            </div>

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_cmspagelink_title_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Title'}</label>
                            <div class="col-lg-7">
                                <input type="text" id="fieldmm_cmspagelink_title_{$language.id_lang}" name="fieldmm_cmspagelink_title_{$language.id_lang}" class="fieldmm_cmspagelink_title" />
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

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_cmspagelink_link_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Link'}</label>
                            <div class="col-lg-7">
                                <input type="text" id="fieldmm_cmspagelink_link_{$language.id_lang}" name="fieldmm_cmspagelink_link_{$language.id_lang}" class="fieldmm_cmspagelink_link" />
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

            <div class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="fieldmm_cmspagelink_cmspage" class="control-label col-lg-3">{l s='CMS Page'}</label>
                        <div class="col-lg-7">
                            <select id="fieldmm_cmspagelink_cmspage" name="fieldmm_cmspagelink_cmspage">
                                {$fieldmmCMSPages}
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-footer">
                <button type="submit" value="1" id="fieldmm_cmspagelink_submit" name="fieldmm_cmspagelink_submit" class="btn btn-default pull-right">
                    <i class="process-icon-new"></i> {l s='Add CMS Page Link'}
                </button>
            </div>
        </div>

    </form>
    {* END - CMS PAGE LINK FORM *}



    {* ADD CUSTOM CONTENT FORM *}
    <form id="fieldmm_customcontent" class="fieldmm_addmenuitemform form-horizontal">

        <input type="hidden" name="menu_type" value="7" />

        <div class="panel">
            <div class="panel-heading">
                <i class="icon-edit"></i> {l s='Add Custom Content'}
            </div>

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_customcontent_title_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Title'}</label>
                            <div class="col-lg-7">
                                <input type="text" id="fieldmm_customcontent_title_{$language.id_lang}" name="fieldmm_customcontent_title_{$language.id_lang}" value="Custom Content" />
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

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_customcontent_link_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Link'}</label>
                            <div class="col-lg-7">
                                <input type="text" id="fieldmm_customcontent_link_{$language.id_lang}" name="fieldmm_customcontent_link_{$language.id_lang}" value="customcontent" />
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

            <div class="form-group">
                <div class="col-lg-12">
                    <button type="submit" value="1"  id="fieldmm_advanced_customcontent" name="fieldmm_advanced_customcontent" class="btn btn-default fieldmm_advanced_customcontent">
                        <i class="process-icon-new"></i> {l s='Add Custom Content'}
                    </button>
                </div>
            </div>

        </div>

    </form>
    {* END - ADD CUSTOM CONTENT FORM *}




    {* ADD DIVIDER FORM *}
    <form id="fieldmm_divider" class="fieldmm_addmenuitemform form-horizontal">

        <input type="hidden" name="menu_type" value="8" />

        <div class="panel">
            <div class="panel-heading">
                <i class="icon-edit"></i> {l s='Add Divider'}
            </div>

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_divider_title_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Title'}</label>
                            <div class="col-lg-7">
                                <input type="text" id="fieldmm_divider_title_{$language.id_lang}" name="fieldmm_divider_title_{$language.id_lang}" value="Divider" />
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

            <div class="form-group hidden">
                <div class="col-lg-12">
                    <div class="form-group">
                        {foreach from=$languages item=language}

                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_default_lang}style="display:none"{/if}>
                            {/if}

                            <label for="fieldmm_divider_link_{$language.id_lang}" class="control-label col-lg-3 required">{l s='Link'}</label>
                            <div class="col-lg-7">
                                <input type="text" id="fieldmm_divider_link_{$language.id_lang}" name="fieldmm_divider_link_{$language.id_lang}" value="divider" />
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

            <div class="form-group">
                <div class="col-lg-12">
                    <button type="submit" value="1"  id="fieldmm_advanced_divider" name="fieldmm_advanced_divider" class="btn btn-default fieldmm_advanced_divider">
                        <i class="process-icon-new"></i> {l s='Add Divider'}
                    </button>
                </div>
            </div>

        </div>

    </form>
    {* END - ADD DIVIDER FORM *}

</div>

<div class="right-column col-lg-8">

    <div class="panel">
        <div class="panel-heading">
            <i class="icon-list"></i> {l s='Menu Structure'}
        </div>

        <div id="fieldmmMenuBuilder">
            {include file='./menu_builder.tpl' fieldmmMenuItems=$fieldmmMenuItems}
        </div>
    </div>

</div>




