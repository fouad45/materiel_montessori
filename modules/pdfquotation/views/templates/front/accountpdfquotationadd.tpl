
{**
* AccountPdfQuotationAdd Template
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
    <p>{l s='Are you sure, you want to add all products of this quotation to cart' mod='pdfquotation'} ?</p>

    <div id="action-quotation">
        <a class="btn" href="{$link->getModuleLink('pdfquotation', 'accountpdfquotation', ['action' => 'index'])|escape:'html':'UTF-8'}">
            <span>{l s='Cancel' mod='pdfquotation'}</span>
        </a>
        <a class="btn" href="{$link->getModuleLink('pdfquotation', 'accountpdfquotation', ['action' => 'addToExistingCart', 'id_quotation' => $quotationObj->id_quotation])|escape:'html':'UTF-8'}">
            <span>{l s='Add all products to existing cart' mod='pdfquotation'}</span>
        </a>
        {*<a class="btn" href="{$link->getModuleLink('pdfquotation', 'accountpdfquotation', ['action' => 'addToNewCart', 'id_quotation' => $quotationObj->id_quotation])|escape:'html':'UTF-8'}">*}
            {*<span>{l s='Add all products to a new cart' mod='pdfquotation'}</span>*}
        {*</a>*}
    </div>
{/block}