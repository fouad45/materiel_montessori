{extends file="helpers/form/form.tpl"}
{block name="script"}


$(document).ready(function() {

		 $('.iframe-upload').fancybox({	
			'width'		: 900,
			'height'	: 600,
			'type'		: 'iframe',
      		'autoScale' : false,
      		'autoDimensions': false,
      		 'fitToView' : false,
  			 'autoSize' : false,
  			 onUpdate : function(){ $('.fancybox-iframe').contents().find('a.link').data('field_id', $(this.element).data("input-name"));
			 	 $('.fancybox-iframe').contents().find('a.link').attr('data-field_id', $(this.element).data("input-name"));},
  			 afterShow: function(){
			 	 $('.fancybox-iframe').contents().find('a.link').data('field_id', $(this.element).data("input-name"));
			 	 $('.fancybox-iframe').contents().find('a.link').attr('data-field_id', $(this.element).data("input-name"));
			}
  		  });
});

{/block}
{block name="label"}

    {if $input['name'] == 'tab_content'}

    {else}
        {$smarty.block.parent}
    {/if}

{/block}

{block name="input"}

    {if $input.name == "product_autocomplete"}

        <div id="fieldproductautocomplete">
            <div id="ajax_choose_product">
                <input type="text" value="" id="product_autocomplete_input" />
                <p class="preference_description">{l s='Begin typing the first letters of the product name, then select the product from the drop-down list. Do not forget to save the tab afterwards.'}</p>
            </div>

            <div id="product_list" style="font-weight:bold;">
                <ul>
                    {if (isset($tab_content_products) && $tab_content_products)}
                        {foreach $tab_content_products as $tab_content_product}
                            <li data-pid="{$tab_content_product.id}">{$tab_content_product.name} (ref: {$tab_content_product.ref})<span class="delProduct" data-pid="{$tab_content_product.id}" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span></li>
                        {/foreach}
                    {/if}
                </ul>
            </div>
        </div>
    {elseif $input.name == 'banner_image' || $input.name == 'title_image'}
	<p> <input id="{$input.name}" type="text" name="{$input.name}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}"> </p>
        <a href="filemanager/dialog.php?type=1&field_id={$input.name}" class="btn btn-default iframe-upload"  data-input-name="{$input.name}" type="button">{l s='Banner image selector' mod='fieldtabproductsisotope'} <i class="icon-angle-right"></i></a>
    {else}
        {$smarty.block.parent}
    {/if}

{/block}