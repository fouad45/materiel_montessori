{if $fieldmmMenuItems.items}

    {$depth = 1}
    <ol class="sortable">

        {foreach $fieldmmMenuItems.items as $menuItem}

            {if ($menuItem->depth > $depth)}
                <ol>
            {elseif ($menuItem->depth < $depth)}
                {'</ol></li>'|str_repeat:($depth - $menuItem->depth)}
            {/if}

            <li class="menuItem" id="menuItem_{$menuItem->id}">

                <div class="menuitem-container">
                    <div class="title">
                        {if (isset($menuItem->item_info) && $menuItem->item_info)}
                            {$menuItem->item_info_name}
                        {else}
                            {$menuItem->title[$id_default_lang]}
                        {/if}
                        <div class="push-right">
                            <div class="menu-type">{$menuItem->menu_type_name}</div>
                            <a title="Edit Menu" class="edit-menu-item">Edit</a>
                        </div>
                    </div>

                    <div class="inline-editor-container">
                        {include file='./inline_editor.tpl' menuItem=$menuItem}
                    </div>
                </div>

            {if ($menuItem@index != $fieldmmMenuItems.count - 1) && ($fieldmmMenuItems.items[($menuItem@index + 1)]->depth <= $menuItem->depth)}
                </li>
            {/if}

            {$depth = $menuItem->depth}

            {if ($menuItem@index == $fieldmmMenuItems.count - 1)}
                </li></ol>{'</ol></li>'|str_repeat:($depth - 1)}
            {/if}

        {/foreach}

{/if}

