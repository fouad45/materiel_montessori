<?php
/**
* Class PdfQuotationAccountPdfQuotationModuleFrontController
* 
* @author    Empty
* @copyright Empty
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class PdfQuotationAccountPdfQuotationModuleFrontController extends ModuleFrontController {

    public function __construct()
	{
		parent::__construct();
        $this->context = Context::getContext();
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();
        
        $action = Tools::getValue('action');
        switch($action) {
            case 'see':             $this->seePdfQuotation();            break;
            case 'add':
            case 'addToExistingCart':
            case 'addToNewCart':    $this->addPdfQuotation();               break;
            case 'delete':
            case 'deleteConfirm':   $this->deletePdfQuotation();            break;
            default:                $this->indexPdfQuotation();             break;
        }
	}
    
    /**
	 * Check Access function
	 */
    private function checkPdfAccess() {
        //Check Access
        $idQuotation = Tools::getValue('id_quotation');
        $quotationObj = QuotationObject::loadByIdAndCustomer($idQuotation, $this->context->cookie->id_customer);

        if($quotationObj == null) {
            $this->errors[] = $this->module->l('An error occur', 'accountpdfquotation');
            $this->indexPdfQuotation();
            return false;
        }
        else {
            return true;
        }
    }

	/**
	 * Index Pdf Quotation
	 */
	public function indexPdfQuotation()
	{
        $pdfQuotationObj = new QuotationObject();
        
        //Get List of all 
        $quotationList = $pdfQuotationObj->getByIdCustomer($this->context->cookie->id_customer);

        $this->context->smarty->assign('quotationList', $quotationList);
        $this->setTemplate('module:pdfquotation/views/templates/front/accountpdfquotationindex.tpl');
	}
    
    /**
	 * See Pdf Quotation
	 */
    public function seePdfQuotation() {
        //Check Access
        if($this->checkPdfAccess() == false) {
            return;
        }
        
        $idQuotation = Tools::getValue('id_quotation');
        $quotationObj = QuotationObject::loadByIdAndCustomer($idQuotation, $this->context->cookie->id_customer);

        // Get path
        $document = _PS_QUOTATION_DIR_.$quotationObj->ref_quotation.".pdf";
        // Get type mime
        $mime = mime_content_type($document);
        // Send header with mime type
        header('Content-type: '.$mime);
        // Il sera proposé au téléchargement au lieu de s'afficher.
        header('Content-Disposition: attachment; filename="'.$quotationObj->ref_quotation.'.pdf"');
        // La source du fichier est lue et envoyée au navigateur.
        readfile($document);
        die();
    }
    
    /**
	 * Add product to Cart
	 */
    public function addPdfQuotation() {
        //Check Access
        if($this->checkPdfAccess() == false) {
            return;
        }
        
        $action = Tools::getValue('action');
        $idQuotation = Tools::getValue('id_quotation');
        $quotationObj = QuotationObject::loadByIdAndCustomer($idQuotation, $this->context->cookie->id_customer);

        switch($action) {
            case "add" :
                $this->context->smarty->assign('quotationObj', $quotationObj);
                $this->setTemplate('module:pdfquotation/views/templates/front/accountpdfquotationadd.tpl');
                break;
            case "addToExistingCart" :
                if(empty($this->context->cookie->id_cart)) {
                    $cartObj = new Cart(null);
                    $cartObj->id_currency = $this->context->currency->id;
                    $cartObj->add();
                    $this->context->cookie->id_cart = $cartObj->id;
                }
                else {
                    $cartObj = new Cart($this->context->cookie->id_cart);
                }
                
                $this->addProductsInCart($quotationObj, $cartObj);
                $this->indexPdfQuotation();
                break;
            case "addToNewCart" :
                $cartObj = new Cart(null);
                $cartObj->id_currency = $this->context->currency->id;
                $cartObj->add();
                $this->context->cookie->id_cart = $cartObj->id;
                
                $this->addProductsInCart($quotationObj, $cartObj);
                $this->indexPdfQuotation();
                break;
            default :
                $this->context->smarty->assign('quotationObj', $quotationObj);
                $this->setTemplate('module:pdfquotation/views/templates/front/accountpdfquotationadd.tpl');
                break;
        }
    }
    
    /**
     * Insert products to cart
     */
    private function addProductsInCart($quotationObj, $currentCartObj) {
        $cartObj = new Cart($quotationObj->id_cart);
        $produts = $cartObj->getProducts();
        
        foreach($produts as $product) {
            $productObj = new Product($product["id_product"]);
            
            if ($product["id_product_attribute"] != 0) {
                $productObj->id_product_attribute = $product["id_product_attribute"];
            }
            if(Configuration::get('PS_CATALOG_MODE')) {
                $this->errors[] = $this->module->l('The shop is desactivated', 'accountpdfquotation');
            }
            elseif (!$productObj->existsInDatabase($product["id_product"], 'product')) {
                $this->errors[] = $this->module->l('The product', 'accountpdfquotation').' "'.$productObj->name[$this->context->language->id].'" '.$this->module->l('does not exist', 'accountpdfquotation');
            }
            elseif(!$productObj->active) {
                $this->errors[] = $this->module->l('The product', 'accountpdfquotation').' "'.$productObj->name[$this->context->language->id].'" '.$this->module->l('was desactivate', 'accountpdfquotation');
            }
            elseif (!$productObj->available_for_order) {
                $this->errors[] = $this->module->l('The product', 'accountpdfquotation').' "'.$productObj->name[$this->context->language->id].'" '.$this->module->l('was not avalaible for order', 'accountpdfquotation');
            }

            elseif(!$productObj->checkQty($product["cart_quantity"])) {
                $this->errors[] = $this->module->l('The product', 'accountpdfquotation').' "'.$productObj->name[$this->context->language->id].'" '.$this->module->l('has no sufficient stock available', 'accountpdfquotation');
            }
            else {
                $currentCartObj->updateQty($product["cart_quantity"], $product["id_product"], $product["id_product_attribute"]);
                $this->success[] = $this->module->l('The product', 'accountpdfquotation').' "'.$productObj->name[$this->context->language->id].'" '.$this->module->l('was added to cart', 'accountpdfquotation');
            }
        }
    }

    /**
	 * Delete Pdf Quotation
	 */
    public function deletePdfQuotation() {
        //Check Access
        if($this->checkPdfAccess() == false) {
            return;
        }

        $action = Tools::getValue('action');
        $idQuotation = Tools::getValue('id_quotation');
        $quotationObj = QuotationObject::loadByIdAndCustomer($idQuotation, $this->context->cookie->id_customer);

        if($action == "delete") {
            $this->context->smarty->assign('quotationObj', $quotationObj);
            $this->setTemplate('module:pdfquotation/views/templates/front/accountpdfquotationdelete.tpl');
        }
        if($action == "deleteConfirm") {
            $quotationObj->deleted = 0;
            $quotationObj->update();
            $this->success[] = $this->module->l('Quotation deleted', 'accountpdfquotation');

            $this->indexPdfQuotation();
        }
    }

    public function getBreadcrumbLinks() {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        $breadcrumb['links'][] = [
            'title' => $this->module->l('My Quotations', 'accountpdfquotation'),
            'url' => $this->context->link->getModuleLink('pdfquotation', 'accountpdfquotation', ['action' => 'index'])
        ];

        return $breadcrumb;
    }

    public function getTemplateVarPage() {
        $page = parent::getTemplateVarPage();
        $page['body_classes']['page-customer-account'] = true;
        return $page;
    }
}