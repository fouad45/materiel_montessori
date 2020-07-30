{if (isset($fieldmegamenu) && $fieldmegamenu)}

    <nav id="fieldmegamenu{if isset($fieldmegamenumobile) && $fieldmegamenumobile}-mobile{else}-main{/if}" class="fieldmegamenu inactive">
        <ul>
            {foreach $fieldmegamenu as $root}

                <li class="root root-{$root->id_fieldmegamenu} {$root->menu_class}">
                    <div class="root-item{if $root->description == ''} no-description{/if}">

                        {if $root->link != ''}<a href="{$root->link}" {if $root->open_in_new}target="_blank"{/if}>{/if}
                            <div class="title title_font">{if $root->icon_class != ''}<span class="fa {$root->icon_class}"></span>{/if}<span class="title-text">{$root->title}</span>{if isset($root->items)}<span class="icon-has-sub fa fa-angle-down"></span>{/if}</div>
                            {if $root->description != ''}<span class="description">{$root->description nofilter}</span>{/if}
                        {if $root->link != ''}</a>{/if}

                    </div>

                    {if isset($root->items)}
                        <ul class="menu-items {$root->width_popup_class} col-xs-12">

                            {$depth = 1}

                            {foreach $root->items as $menuItem}
                                {if ($menuItem->depth > $depth)}
                                    <ul class="submenu submenu-depth-{$menuItem->depth}">
                                {elseif ($menuItem->depth < $depth)}
                                    {'</ul></li>'|str_repeat:($depth - $menuItem->depth) nofilter}
                                {/if}

                                    <li class="menu-item menu-item-{$menuItem->id} depth-{$menuItem->depth} {$menuItem->menu_type_name} {$menuItem->menu_layout} {$menuItem->menu_class} {if isset($menuItem->image) && $menuItem->image} withimage{/if}">

                                        {if ($menuItem->menu_type_name == 'customlink' || $menuItem->menu_type_name == 'category' || $menuItem->menu_type_name == 'cmspage')}

                                            <div class="title{if $menuItem->depth ==1 && $menuItem->menu_type_name !== 'cmspage'} title_font{/if}">
                                                {if $menuItem->link != ''}<a href="{$menuItem->link}" {if $menuItem->open_in_new}target="_blank"{/if}>{/if}
                                                    {if $menuItem->icon_class != ''}<span class="icon {$menuItem->icon_class}"></span>{/if}{$menuItem->title nofilter}
                                                    {if $menuItem->description != ''}<span class="description">{$menuItem->description nofilter}</span>{/if}
                                                {if $menuItem->link != ''}</a>{/if}
                                            </div>

                                        {elseif ($menuItem->menu_type_name == 'product')}
					    <div class="horizontal_mode">
                            <div class="item-inner">
                            <div class="product-miniature js-product-miniature" data-id-product="{$menuItem->product['id_product']}" data-id-product-attribute="{$menuItem->product['id_product_attribute']}" itemscope itemtype="http://schema.org/Product">
                            <div class="left-product">
                            {if isset($menuItem->image) && $menuItem->image}
                            <a class="thumbnail product-thumbnail" href="{$menuItem->link}" {if $menuItem->open_in_new}target="_blank"{/if}>
                            <span class="cover_image">
                            <img src="{$menuItem->image}" alt=""/>
                            </span>
                            {if isset($menuItem->second_image) && $menuItem->second_image}
                            <span class="hover_image"><img src="{$menuItem->second_image}" alt=""/></span>
                            {/if}
                            
                            </a>
                            {/if}
                            <div class="button-action">
                            <form action="{$urls.pages.cart}" method="post">
                            <input type="hidden" name="token" value="{$static_token}">
                            <input type="hidden" name="id_product" value="{$menuItem->product['id_product']}">
                             <button class="add-to-cart" data-button-action="add-to-cart" type="submit" {if !$menuItem->product['quantity']}disabled{/if}>
                            
                            {if $menuItem->product['quantity']}
                            <i class="fa fa-shopping-cart"></i>
                            {else}
                            <i class="fa fa-ban"></i>
                            {/if}  
                            </button>
                            </form>
                          <a href="javascript:void(0)" class="quick-view" data-link-action="quickview" title="{l s='Quick view' mod='fieldmegamenu'}"> 
                            <i class="fa fa-eye"></i>
                          </a>
                            </div>
                            </div>
                            <div class="right-product">       
                            <div class="product-description">
                            <div class="product_name"><a class="product-name" href="{$menuItem->link|escape:'html':'UTF-8'}" {if $menuItem->open_in_new}target="_blank"{/if}>
                            {if $menuItem->icon_class != ''}<span class="icon {$menuItem->icon_class}"></span>{/if}{$menuItem->title|truncate:45:'...'|escape:'html':'UTF-8'}
                            {if $menuItem->description != ''}<span class="description">{$menuItem->description nofilter}</span>{/if}
                            </a></div> 
                            <div class="product-price-and-shipping">
                            {if isset($menuItem->price) && !empty($menuItem->price)}
                            <span class="price">{$menuItem->price}</span>
                            {/if}
                            {if isset($menuItem->old_price) && !empty($menuItem->old_price)}
                            <span class="regular-price">{$menuItem->old_price}</span>
                            {/if}
                            {if isset($menuItem->reduction_price) && !empty($menuItem->reduction_price)}
                            <span class="discount-percentage-product">-{$menuItem->reduction_price}%</span>
                            {/if}
                            </div>         
                            </div>
                            
                            </div>
                            </div>
                            </div>
                            </div>

                                        {elseif ($menuItem->menu_type_name == 'manufacturer')}

                                            {if isset($menuItem->image) && $menuItem->image}
                                                <a class="white-border-3px img-wrapper" href="{$menuItem->link}" {if $menuItem->open_in_new}target="_blank"{/if}>
                                                    <div class="manufacturer-image"><img src="{$menuItem->image}" /></div>
                                                </a>
                                            {/if}
                                            <div class="title">
                                                <a href="{$menuItem->link}" {if $menuItem->open_in_new}target="_blank"{/if}>
                                                    {if $menuItem->icon_class != ''}<span class="icon {$menuItem->icon_class}"></span>{/if}{$menuItem->title nofilter}
                                                    {if $menuItem->description != ''}<span class="description">{$menuItem->description nofilter}</span>{/if}
                                                </a>
                                            </div>

                                        {elseif ($menuItem->menu_type_name == 'supplier')}

                                            {if isset($menuItem->image) && $menuItem->image}
                                                <a class="white-border-3px img-wrapper" href="{$menuItem->link}" {if $menuItem->open_in_new}target="_blank"{/if}>
                                                    <div class="supplier-image"><img src="{$menuItem->image}" /></div>
                                                </a>
                                            {/if}
                                            <div class="title">
                                                <a href="{$menuItem->link}" {if $menuItem->open_in_new}target="_blank"{/if}>
                                                    {if $menuItem->icon_class != ''}<span class="icon {$menuItem->icon_class}"></span>{/if}{$menuItem->title}
                                                    {if $menuItem->description != ''}<span class="description">{$menuItem->description nofilter}</span>{/if}
                                                </a>
                                            </div>

                                        {elseif ($menuItem->menu_type_name == 'customcontent')}

                                            <div class="normalized">
                                                    {$menuItem->content nofilter}
                                            </div>

                                        {/if}


                                {if ($menuItem@index != (count($root->items) - 1)) && ($root->items[($menuItem@index + 1)]->depth <= $menuItem->depth)}
                                    </li>
                                {/if}

                                {$depth = $menuItem->depth}

                                {if ($menuItem@index == count($root->items) - 1)}
                                    </li>{'</ul></li>'|str_repeat:($depth - 1) nofilter}
                                {/if}

                            {/foreach}
                        </ul>
                    {/if}

                </li>

            {/foreach}
        </ul>
    </nav>

{/if}