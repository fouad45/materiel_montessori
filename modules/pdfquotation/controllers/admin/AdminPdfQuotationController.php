<?php
/**
 * Class AdminPdfQuotationController
 *
 * @author    Empty
 * @copyright Empty
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class AdminPdfQuotationController extends ModuleAdminController {

    /**
     * AdminController::__construct() override
     * @see AdminController::__construct()
     */
    public function __construct() {

        parent:: __construct();

        $this->table = 'quotation';
        $this->bootstrap = true;
        $this->lang = false;
        $this->className = 'QuotationObject';
        $this->identifier = 'id_quotation';
        $this->context = Context::getContext();

        $this->addRowAction('previewQuotation');
        $this->addRowAction('previewCart');

        $this->_defaultOrderBy = 'id_quotation';
        $this->position_identifier = 'id_quotation';

        $this->fields_list = array(
            'id_quotation' => array(
                'title'   => $this->module->getTranslator()->trans('Id', array(), 'Modules.PDFQuotation'),
                'width' => 50,
            ),
            'ref_quotation' => array(
                'title' => $this->module->getTranslator()->trans('Ref Quotation', array(), 'Modules.PDFQuotation'),
            ),
            'first_name' => array(
                'title' => $this->module->getTranslator()->trans('First Name', array(), 'Modules.PDFQuotation'),
            ),
            'last_name' => array(
                'title' => $this->module->getTranslator()->trans('Last Name', array(), 'Modules.PDFQuotation'),
            ),
            'email' => array(
                'title' => $this->module->getTranslator()->trans('Email', array(), 'Modules.PDFQuotation'),
            ),
            'phone' => array(
                'title' 	=> $this->module->getTranslator()->trans('Phone', array(), 'Modules.PDFQuotation'),
                'orderby' => false,
            ),
            'contacted' => array(
                'title' 	=> $this->module->getTranslator()->trans('To be contacted again', array(), 'Modules.PDFQuotation'),
                'width' => 50,
                'callback' => 'printStatusText',
                'orderby' => false,
                'type' => 'bool'
            ),
            'deleted' => array(
                'title' 	=> $this->module->getTranslator()->trans('Deleted by customer', array(), 'Modules.PDFQuotation'),
                'width' => 50,
                //'callback' => 'printStatusText',
                'active' => 'deleted',
                'orderby' => false,
                'type' => 'bool'
            ),
            'date_add' => array(
                'title' => $this->module->getTranslator()->trans('Date', array(), 'Modules.PDFQuotation'),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            )
        );

        $this->list_no_link = true;
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function printStatusText($echo, $row)
    {
        if ($echo == 0) {
            return $this->module->getTranslator()->trans('No', array(), 'Modules.PDFQuotation');
        }
        else {
            return $this->module->getTranslator()->trans('Yes', array(), 'Modules.PDFQuotation');
        }
    }

    public function displayPreviewQuotationLink($token = null, $id, $name = null)
    {
        $shop = new Shop(Context::getContext()->shop->id);
        $quotationObj = new QuotationObject($id);
        return '<a target="_blank" href="'.$shop->getBaseURL().'img/quotation/'.$quotationObj->ref_quotation.'.pdf">'.$this->module->getTranslator()->trans('See Quotation', array(), 'Modules.PDFQuotation')."</a>";
    }

    public function displayPreviewCartLink($token = null, $id, $name = null)
    {
        $link = new Link();
        $quotationObj = new QuotationObject($id);
        return '<a href="'.$link->getAdminLink("AdminCarts").'&id_cart='.$quotationObj->id_cart.'&viewcart">'.$this->module->getTranslator()->trans('See Cart', array(), 'Modules.PDFQuotation').'</a>';
    }
}
