{*
* GcInvoiceExport
*
* @author    Grégory Chartier <hello@gregorychartier.fr>
* @copyright 2019 Grégory Chartier (https://www.gregorychartier.fr)
* @license   Commercial license see license.txt
* @category  Prestashop
* @category  Module
*}

{if $ps15}
    {literal}
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    {/literal}
    <script type="text/javascript">
        $(document).ready(function(){
            $("#tabs").tabs();
            var myToken = '{$myToken|escape:'html':'UTF-8'}';
        });
    </script>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-0">{l s='Information' mod='gcinvoiceexport'}</a></li>
            <li><a href="#tabs-1">{l s='Content' mod='gcinvoiceexport'}</a></li>
            <li><a href="#tabs-2">{l s='Filters' mod='gcinvoiceexport'}</a></li>
            <li><a href="#tabs-3">{l s='Export' mod='gcinvoiceexport'}</a></li>
            <li><a href="#tabs-4">{l s='Automation' mod='gcinvoiceexport'}</a></li>
        </ul>
        <div id="tabs-0">
        {$rating}
        </div>
        <div id="tabs-1">
        {$settings}
        </div>
        <div id="tabs-2">
        {$filter}
        </div>
        <div id="tabs-3">
        {$export}
        </div>
        <div id="tabs-4">
        {$auto_export}
        </div>
    </div>
{else}
    <div id="modulecontent" class="clearfix">
    <div class="col-lg-2">
        <div class="list-group">
            <a href="#information" class="list-group-item active" data-toggle="tab">{l s='Information' mod='gcinvoiceexport'}</a>
            <a href="#columns_settings" class="list-group-item" data-toggle="tab">{l s='Content' mod='gcinvoiceexport'}</a>
            <a href="#filters_settings" class="list-group-item" data-toggle="tab">{l s='Filters' mod='gcinvoiceexport'}</a>
            <a href="#general_export" class="list-group-item" data-toggle="tab">{l s='Export' mod='gcinvoiceexport'}</a>
            <a href="#auto_export" class="list-group-item" data-toggle="tab">{l s='Automation' mod='gcinvoiceexport'}</a>
        </div>
    </div>
    <div class="tab-content col-lg-10">
        <div class="tab-pane active panel" id="information">
            {$rating}
        </div>
        <div class="tab-pane panel" id="columns_settings">
            {$settings}
        </div>
        <div class="tab-pane panel" id="filters_settings">
            {$filter}
        </div>
        <div class="tab-pane panel" id="general_export">
            {$export}
        </div>
        <div class="tab-pane panel" id="auto_export">
            {$auto_export}
        </div>
    </div>
    </div>
    <script type="text/javascript">
        var myToken = '{$myToken|escape:'html':'UTF-8'}';
    {literal}
        $(document).ready(function(){
            $(".list-group-item").on("click", function() {
                $(".list-group-item").removeClass("active");
                $(this).addClass("active");
            });
        });
    {/literal}
    </script>
{/if}
