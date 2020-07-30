{if isset($fieldblockcategories.arrayCategory) && !empty($fieldblockcategories.arrayCategory)}
    <div id="fieldblockcategories" class="block horizontal_mode clearfix">
        <div class="container">
                {if isset($fieldblockcategories.field_title) && !empty($fieldblockcategories.field_title)}
                    <div class="text2-border">
                    {if isset($fieldblockcategories.field_sub_title) && !empty($fieldblockcategories.field_sub_title)}
                            <span class="title-text-top title_font">{$fieldblockcategories.field_sub_title}</span>
                        {/if}
                    <div class="title_block title_font">
                        <a class="title_text">
                        {$fieldblockcategories.field_title}
                        </a>
                    </div>
                    </div>
                {/if}
                <div class="box_categories">
            <div class="row">
                <div id="field_content" class="carousel-grid owl-carousel"> 
                
                {assign var="i" value="0"} 
                {assign var="y" value="2"}
                   {foreach from=$fieldblockcategories.arrayCategory item='items'}
                       {if $i mod $y eq 0}         
                       <div class="item">
                       {/if}
                        <div class="item-inner">
                        <div class="box-item-inner">
                        {*<div class="left-block-cate">
                         <a href="{$items.link|escape:'html'}" title="{$items.name|escape:html:'UTF-8'}" >
                            <img  src="{$items.thumbnails|escape:'html'}" alt="{$items.name|escape:html:'UTF-8'}"/>
                         </a>
                        </div>*}
                        <div class="right-block-cate">
                        <a class="name-block " href="{$items.link|escape:'html'}" title="{$items.name|escape:html:'UTF-8'}">
                                  {$items.name|escape:html:'UTF-8'}
                           </a>
						   {foreach from=$items.sub_categories item='item_sub'}
                                 <div class="name_item">
                                    <a class="name_block" href="{$item_sub.link|escape:'html'}" title="{$item_sub.name|escape:html:'UTF-8'}">
                                        {$item_sub.name|escape:html:'UTF-8'}
                                     </a> 
                                 </div>
                               {/foreach}  
							<a class="more-cate" href="{$items.link|escape:'html'}" title="{$items.name|escape:html:'UTF-8'}" >
                                {l s='See More' mod='fieldblockcategories'}
							</a>
							
                        </div>
						   </div>
                             
                             
                        </div>
                        
						
                        {assign var="i" value="`$i+1`"}
                        {if $i mod $y eq 0 || $i eq count($fieldblockcategories.arrayCategory)}
                      </div>
                        {/if}
                    {/foreach}
                  </div>
            </div>
            </div>
        </div>
    </div>
<script type="text/javascript"> 
				$(window).load(function() {
                    $('#field_content').owlCarousel({
                        itemsCustom: [ [0, 1], [320, 1], [480, 1], [600, 1], [992, 1], [1200, 2] ],
                        responsiveRefreshRate: 50,
                        slideSpeed: 200,
                        paginationSpeed: 500,
                        rewindSpeed: 600,
                        autoPlay: false,
                        stopOnHover: false,
                        rewindNav: true,
                        pagination: false,
                        navigation: true,
                        navigationText: ['<div class="carousel-previous disable-select"><span class="fa fa-angle-left"></span></div>', '<div class="carousel-next disable-select"><span class="fa fa-angle-right"></span></div>']
                    });
				     });	
                </script>
{/if}

