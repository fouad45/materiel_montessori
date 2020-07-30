<?php
/**
* Class  HTMLTemplateQuotation
*
* @author    Empty
* @copyright 2007-2016 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class HTMLTemplateQuotation extends HTMLTemplate
{

	public function __construct(QuotationObject $quotation, $smarty)
    {
        $this->quotation = $quotation;
        $this->smarty = $smarty;
        $this->title = HTMLTemplateQuotation::l('Estimate / Order');
        $this->shop = new Shop(Context::getContext()->shop->id);
    }
    
    protected function getLogo()
	{
		$logo = '';
        
        $shopId = (int)Context::getContext()->shop->id;
        
		if (Configuration::get('PS_LOGO_INVOICE', null, null, $shopId) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $shopId)))
			$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $shopId);
		elseif (Configuration::get('PS_LOGO', null, null, $shopId) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $shopId)))
			$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $shopId);
		return $logo;
	}
    
    public function getHeader()
	{
		$path_logo = $this->getLogo();

		$width = 0;
		$height = 0;
		if (!empty($path_logo))
			list($width, $height) = getimagesize($path_logo);

		$this->smarty->assign(array(
            'shop_name' => Configuration::get('PS_SHOP_NAME'),
            'first_name' => $this->quotation->first_name,
            'last_name' => $this->quotation->last_name,
            'reference' => $this->quotation->ref_quotation,
			'logo_path' => $path_logo,
			'width_logo' => $width,
			'height_logo' => $height,
            'customer_id' => (int)Context::getContext()->customer->id,
            'date' => date("d-m-Y", time()),
            'header' => htmlentities (Configuration::get('PDFQUOTATION_HEADER', Context::getContext()->language->id))
		));
        
        if (file_exists(_PS_MODULE_DIR_ . 'pdfquotation/views/templates/front/pdf/'.Context::getContext()->language->iso_code.'/header.tpl')) {
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'pdfquotation/views/templates/front/pdf/'.Context::getContext()->language->iso_code.'/header.tpl');
        }
		else {
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'pdfquotation/views/templates/front/pdf/en/header.tpl');
        }
	}
    
    /**
     * Returns the template filename
     * @return string filename
     */
    public function getFooter()
    {//d(Configuration::get('PDFQUOTATION_FOOTER', Context::getContext()->language->id));
        $this->smarty->assign(array(
            'footer' => htmlentities (Configuration::get('PDFQUOTATION_FOOTER', Context::getContext()->language->id))
        ));

        if (file_exists(_PS_MODULE_DIR_ . 'pdfquotation/views/templates/front/pdf/'.Context::getContext()->language->iso_code.'/footer.tpl')) {
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'pdfquotation/views/templates/front/pdf/'.Context::getContext()->language->iso_code.'/footer.tpl');
        }
		else {
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'pdfquotation/views/templates/front/pdf/en/footer.tpl');
        }
    }
 
    /**
     * Returns the template's HTML content
     * @return string HTML content
     */
    public function getContent()
    {
        $products = Context::getContext()->cart->getProducts();
        foreach($products as $i=>$product) {
            $productObj = new Product($product['id_product'], false, Context::getContext()->language->id, Context::getContext()->shop->id);
            
            //Features**********************************************************
            $products[$i]['features_name'] = "";
            foreach($product['features'] as $feature) {
                $featureValue = new FeatureValue($feature['id_feature_value']);
                $products[$i]['features_name'] .= $featureValue->value[Context::getContext()->language->id].", ";
            }
            if (!empty($products[$i]['features_name'])) {
                $products[$i]['features_name'] = Tools::substr($products[$i]['features_name'], 0, -2);
            }
            
            //Combinations******************************************************
            $combinations = $productObj->getAttributeCombinationsById($product['id_product_attribute'], Context::getContext()->language->id);
            $products[$i]['combination'] = "";
            foreach($combinations as $combination) {
                $products[$i]['combination'] .= $combination['group_name'].": ".$combination['attribute_name'].", ";
            }
            if (!empty($products[$i]['combination'])) {
                $products[$i]['combination'] = Tools::substr($products[$i]['combination'], 0, -2);
            }
            $products[$i]['price_without_reduction'] = Product::getPriceStatic($products[$i]['id_product'], false, $products[$i]['id_product_attribute'], _PS_PRICE_COMPUTE_PRECISION_, null, false, false);
            $products[$i]['reduction'] = Product::getPriceStatic($products[$i]['id_product'], false, $products[$i]['id_product_attribute'], _PS_PRICE_COMPUTE_PRECISION_, null, true);
        }
        
        $cartInfo = Context::getContext()->cart->getSummaryDetails(Context::getContext()->language->id);

        $this->smarty->assign(array(
            'before' => htmlentities (Configuration::get('PDFQUOTATION_BEFORE', Context::getContext()->language->id)),
            'after' => htmlentities (Configuration::get('PDFQUOTATION_AFTER', Context::getContext()->language->id)),
            'products' => $products,
            'cart_info' => $cartInfo
        ));
        
        if (file_exists(_PS_MODULE_DIR_ . 'pdfquotation/views/templates/front/pdf/'.Context::getContext()->language->iso_code.'/quotation.tpl')) {
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'pdfquotation/views/templates/front/pdf/'.Context::getContext()->language->iso_code.'/quotation.tpl');
        }
		else {
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'pdfquotation/views/templates/front/pdf/en/quotation.tpl');
        }
        
    }
    
    /**
     * Returns the template filename
     * @return string filename
     */
    public function getFilename()
    {
		return $this->quotation->ref_quotation.'.pdf';
	}
    
    /**
     * Returns the template filename when using bulk rendering
     * @return string filename
     */
    public function getBulkFilename()
    {
        return 'quotation.pdf';
    } 
    
}

