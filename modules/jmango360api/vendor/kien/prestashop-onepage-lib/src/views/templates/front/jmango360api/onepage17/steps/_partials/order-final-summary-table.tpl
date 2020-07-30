{**
* @license Created by JMango
*}
<div id="order-items" class="col-md-12">
    {block name='order_items_table_head'}
        <h3 class="card-title h3">{l s='Order items' mod='jmango360api'}</h3>
    {/block}
    <div class="order-confirmation-table">
        {block name='order_confirmation_table'}
            {foreach from=$products item=product}
                <div class="order-line row">
                    <div class="col-sm-2 col-3">
                        <span class="image">
                          <img src="{$product.cover.medium.url|escape:'html':'UTF-8'}" class="over-view-img"/>
                        </span>
                    </div>
                    <div class="col-sm-4 col-9 details">
                        {if $add_product_link}<a href="{$product.url|escape:'html':'UTF-8'}" target="_blank">{/if}
                            <span class="order-item-name">{$product.name|escape:'html':'UTF-8'}</span>
                        {if $add_product_link}</a>{/if}
                        {if $product.customizations|count}
                            {foreach from=$product.customizations item="customization"}
                                <div class="customizations">
                                    <a href="#" data-toggle="modal"
                                       data-target="#product-customizations-modal-{$customization.id_customization|escape:'html':'UTF-8'}">{l s='Product customization' d='Shop.Theme.Checkout'}</a>
                                </div>
                                <div class="modal fade customization-modal"
                                     id="product-customizations-modal-{$customization.id_customization|escape:'html':'UTF-8'}"
                                     tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close"
                                                        data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Checkout'}</h4>
                                            </div>
                                            <div class="modal-body">
                                                {foreach from=$customization.fields item="field"}
                                                    <div class="product-customization-line row">
                                                        <div class="col-sm-3 col-xs-4 label">
                                                            {$field.label|escape:'html':'UTF-8'}
                                                        </div>
                                                        <div class="col-sm-9 col-xs-8 value">
                                                            {if $field.type == 'text'}
                                                                {if (int)$field.id_module}
                                                                    {$field.text|escape:'html':'UTF-8'}
                                                                {else}
                                                                    {$field.text|escape:'html':'UTF-8'}
                                                                {/if}
                                                            {elseif $field.type == 'image'}
                                                                <img src="{$field.image.small.url|escape:'html':'UTF-8'}">
                                                            {/if}
                                                        </div>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        {/if}
                        {hook h='displayProductPriceBlock' product=$product type="unit_price"}
                        <div class="row">
                            <div class="col-xs-5 text-sm-right text-xs-left">{$product.price|escape:'html':'UTF-8'}</div>
                            <div class="col-xs-1">{$product.quantity|escape:'html':'UTF-8'}</div>
                            <div class="col-xs-5 text-xs-right bold">{$product.total|escape:'html':'UTF-8'}</div>
                        </div>
                    </div>
                </div>
            {/foreach}
            <hr>
            <table>
                {foreach $subtotals as $subtotal}
                    {if $subtotal.type !== 'tax' && $subtotal.value}
                        <tr>
                            <td>{$subtotal.label|escape:'html':'UTF-8'}</td>
                            <td>{$subtotal.value|escape:'html':'UTF-8'}</td>
                        </tr>
                    {/if}
                {/foreach}
                {if $subtotals.tax.label !== null}
                    <tr class="sub">
                        <td>{$subtotals.tax.label|escape:'html':'UTF-8'}</td>
                        <td>{$subtotals.tax.value|escape:'html':'UTF-8'}</td>
                    </tr>
                {/if}
                <tr class="font-weight-bold">
                    <td>
                        <span class="text-uppercase">{$totals.total.label|escape:'html':'UTF-8'}</span> {$labels.tax_short|escape:'html':'UTF-8'}
                    </td>
                    <td>{$totals.total.value|escape:'html':'UTF-8'}</td>
                </tr>
            </table>
        {/block}
    </div>
</div>
