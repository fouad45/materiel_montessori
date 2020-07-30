{if $fieldbrandslider.manufacturers}
    <div id="fieldbrandslider" class="block title_center horizontal_mode">
        {if ($fieldbrandslider.mainTitle != '')}
            <h4 class="title_block title_font"><a class="title_text" href="{$link->getPageLink('manufacturer')|escape:'html'}">{$fieldbrandslider.mainTitle}</a></h4>
        {/if}
        <div class="row">
                <div id="fieldbrandslider-manufacturers" class="grid carousel-grid owl-carousel">
                    {foreach $fieldbrandslider.manufacturers as $manufacturer}
                        <div class="item">
                           <a class="img-wrapper" href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$manufacturer.name|escape:'htmlall':'UTF-8'}">
                                <img class="img-responsive" 
                                src="{$link->getMediaLink($manu_dir)}{$manufacturer.id_manufacturer|escape:'htmlall':'UTF-8'}-field_manufacture.jpg"
                                 alt="{$manufacturer.name|escape:'htmlall':'UTF-8'}" 
                               {if isset($size_field_manufacture.width)}width="{$size_field_manufacture.width}"{/if}
                              {if isset($size_field_manufacture.height)}height="{$size_field_manufacture.height}"{/if}  
                                 />
                                {if isset($fieldbrandslider.mantitle) AND $fieldbrandslider.mantitle == 1}<br/>
                                    <p>{$manufacturer.name|escape:'htmlall':'UTF-8'}</p>
                                {/if}
                            </a>
                        </div>
                    {/foreach}
                </div>
        </div>
    </div>
{/if}    
