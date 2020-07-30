
{**
* AccountPdfQuotationIndex Template
* 
* @author Empty
* @copyright  Empty
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{extends file='customer/page.tpl'}

{block name='page_title'}
    {l s='My Quotations' mod='pdfquotation'}
{/block}

{block name='page_content'}
    <h6>{l s='You find here a page who permit to manage all your quotation' mod='pdfquotation'}</h6>

    {if $quotationList}
        <table id="quotation-list" class="table table-striped table-bordered table-labeled">
            <thead class="thead-default">
                <tr>
                    <th>{l s='Reference' mod='pdfquotation'}</th>
                    <th class="hide-mobile">{l s='Products' mod='pdfquotation'}</th>
                    <th>{l s='Total' mod='pdfquotation'}</th>
                    <th class="hide-mobile">{l s='Date Add' mod='pdfquotation'}</th>
                    <th>{l s='Action' mod='pdfquotation'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$quotationList item=quotation}
                    <tr>
                        <td>{$quotation.ref_quotation|escape:'html':'UTF-8'}</td>
                        <td class="hide-mobile">
                            <ul>
                                {foreach from=$quotation.products item=product}
                                    <li>- <strong>{$product.name|escape:'html':'UTF-8'}</strong> ({$product.attributes|escape:'html':'UTF-8'})</li>
                                {/foreach}
                            </ul>
                        </td>
                        <td>{$quotation.total|escape:'html':'UTF-8'}</td>
                        <td class="hide-mobile">{$quotation.date_add|escape:'html':'UTF-8'}</td>
                        <td>
                            <a href="{$link->getModuleLink('pdfquotation', 'accountpdfquotation', ['action' => 'see', 'id_quotation' => $quotation.id_quotation])|escape:'html':'UTF-8'}">
                                <span>
                                    {l s='See Quotation' mod='pdfquotation'}
                                    <i class="icon-chevron-right right"></i>
                                </span>
                            </a>&nbsp;&nbsp;
                            <a href="{$link->getModuleLink('pdfquotation', 'accountpdfquotation', ['action' => 'delete', 'id_quotation' => $quotation.id_quotation])|escape:'html':'UTF-8'}">
                                <span>
                                    {l s='Delete Quotation' mod='pdfquotation'}
                                    <i class="icon-chevron-right right"></i>
                                </span>
                            </a>&nbsp;&nbsp;
                            <a href="{$link->getModuleLink('pdfquotation', 'accountpdfquotation', ['action' => 'add', 'id_quotation' => $quotation.id_quotation])|escape:'html':'UTF-8'}">
                                <span>
                                    {l s='Add Product to cart' mod='pdfquotation'}
                                    <i class="icon-chevron-right right"></i>
                                </span>
                            </a>&nbsp;&nbsp;
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {/if}
{/block}
