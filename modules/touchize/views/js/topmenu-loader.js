/**
 * 2018 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 */
jQuery(function ($) {
    let mb = null;
    $.fn.topMenuBuilder = {
        options:{},
        config:{
            group: 'top-menu',
            maxDepth: 20,
            drop: false,
            threshold: 10
        },
        sourceSelector:'#allowed-items',
        sourceContainer:null,
        targetSelector:'#selected-items',
        targetContainer:null,
        trashSelector:'#trash-items',
        trashContainer: null,
        saveButtonSelector:'#top-menu-save',
        allowedItems:null,
        selectedItems:null,
        list_element: 'ol',
        item_element: 'li',
        title_element: 'div',
        list_class: 'dd-list',
        item_class: 'dd-item',
        title_class: 'dd-handle',
        trash_class: 'dd-trash',
        empty_class: 'dd-empty',
        init:function (options) {
            mb = this;
            mb.options = $.extend(mb.options, options);
            mb.sourceContainer = document.querySelector(mb.sourceSelector);
            mb.targetContainer = document.querySelector(mb.targetSelector);
            mb.trashContainer = document.querySelector(mb.trashSelector);

            mb.sourceContainer.appendChild(
                mb.buildTree(mb.options.allowed_items)
            );
            mb.targetContainer.appendChild(
                mb.buildTree(mb.options.selected_items)
            );

            mb.allowedItems = $(mb.sourceContainer).nestable(mb.config);
            mb.config.drop = true;
            mb.selectedItems = $(mb.targetContainer).nestable(mb.config);
            mb.trash = $(mb.trashContainer).nestable(mb.config);

            $(mb.saveButtonSelector).on('click', mb.save);
        },
        buildTree:function (list) {
            if(list === undefined || list.length === 0) {
                let empty = document.createElement('div');
                empty.setAttribute('class', mb.empty_class);
                return empty;
            }

            let branch = document.createElement(mb.list_element);
            branch.setAttribute('class', mb.list_class);

            for(let i = 0; i < list.length; i++) {
                branch.appendChild(
                    mb.processNode(list[i])
                );
            }

            return branch;
        },
        processNode:function (item) {
            let element = document.createElement(mb.item_element);
            let title = document.createElement(mb.title_element);

            title.setAttribute('class', mb.title_class);
            title.innerHTML = item.name;
            element.setAttribute('data-id', item.id);
            element.setAttribute('data-name', item.name);
            element.setAttribute('class', mb.item_class);
            element.appendChild(title);

            if(undefined !== item.children) {
                element.appendChild(
                    mb.buildTree(item.children)
                );
            }

            return element;
        },
        save:function () {
            let items = mb.selectedItems.nestable('serialize');
            let item_string = JSON.stringify(items);
            $.ajax({
                url: mb.options.url,
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax:true,
                    menu_items: item_string,
                    action: 'saveTopMenu'
                }
            }).done(function (data) {
                if (!data.error) {
                    showSuccessMessage(data.message);
                } else {
                    showErrorMessage(data.message);
                }
            }).then(function () {
                $('#next-step').trigger('click');
            });
        }
    };
    $.fn.topMenuBuilder.init(top_menu_options);
});