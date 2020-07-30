<?php
/**
* Module Quotation
*
* @author    Empty
* @copyright 2007-2016 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_'))
	exit;

define('_PS_QUOTATION_DIR_', _PS_IMG_DIR_.'quotation/');
if(!is_dir(_PS_QUOTATION_DIR_)) {
	mkdir(_PS_QUOTATION_DIR_);
	copy(_PS_IMG_DIR_.'index.php', _PS_QUOTATION_DIR_.'index.php');
}

require_once(dirname(__FILE__) . '/classes/QuotationObject.php');
require_once(dirname(__FILE__) . '/classes/pdf/HTMLTemplateQuotation.php');

class PDFQuotation extends Module
{
	protected $config_form = false;

	public function __construct()
	{
		$this->name = 'pdfquotation';
		$this->tab = 'others';
		$this->version = '1.7.0';
		$this->author = 'Empty';
		$this->need_instance = 0;
		$this->bootstrap = true;
		$this->displayName = $this->l('PDF Quotation Module');
		$this->description = $this->l('Permit to customer to print quotation');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall PDF Quotation module ?');
        $this->module_key = "a0a48c7b6e8387253bd86197d2acf4c0";
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

		parent::__construct();
	}

	public function install()
	{
		include(dirname(__FILE__).'/sql/install.php');

		//Add a menu in "order menu"
		$tab = new Tab();
		$tab->active = 1;
		$tab->class_name = 'AdminPdfQuotation';
		$tab->name = array();
		$arrayLangConf = array();
		foreach (Language::getLanguages(true) as $lang) {
			$tab->name[$lang['id_lang']] = $this->l('Quotation');
			$arrayLangConf[$lang['id_lang']] = "";
		}

		$idOrderTab = Tab::getIdFromClassName('AdminParentOrders');
		$tab->id_parent = $idOrderTab;
		$tab->module = $this->name;
		$tab->add();

		/* Adds Module */
		if (!parent::install() OR
			!$this->registerHook('header') OR
			!$this->registerHook('displayShoppingCartFooter') OR
			!$this->registerHook('displayCustomerAccount') OR
			!Configuration::updateValue('PDFQUOTATION_SEND_MAIL', '0') OR
			!Configuration::updateValue('PDFQUOTATION_MAIL', Configuration::get('PS_SHOP_EMAIL')) OR
			!Configuration::updateValue('PDFQUOTATION_HEADER', $arrayLangConf) OR
			!Configuration::updateValue('PDFQUOTATION_MARGIN_HEADER', '55') OR
			!Configuration::updateValue('PDFQUOTATION_BEFORE', $arrayLangConf) OR
			!Configuration::updateValue('PDFQUOTATION_AFTER', $arrayLangConf) OR
			!Configuration::updateValue('PDFQUOTATION_PREFIX', 'Q') OR
			!Configuration::updateValue('PDFQUOTATION_FOOTER', $arrayLangConf) OR
			!Configuration::updateValue('PDFQUOTATION_MARGIN_FOOTER', '25'))
		{
			//Remove the menu
			$idTab = Tab::getIdFromClassName('AdminPdfQuotation');
			$tab = new Tab($idTab);
			$tab->delete();

			return false;
		}

		return true;
	}

	public function uninstall()
	{
		include(dirname(__FILE__).'/sql/uninstall.php');

		//Remove the menu
		$idTab = Tab::getIdFromClassName('AdminPdfQuotation');
		$tab = new Tab($idTab);
		$tab->delete();

		return parent::uninstall() &&
			Configuration::deleteByName('PDFQUOTATION_SEND_MAIL') &&
			Configuration::deleteByName('PDFQUOTATION_MAIL') &&
			Configuration::deleteByName('PDFQUOTATION_HEADER') &&
			Configuration::deleteByName('PDFQUOTATION_BEFORE') &&
			Configuration::deleteByName('PDFQUOTATION_AFTER') &&
			Configuration::deleteByName('PDFQUOTATION_PREFIX') &&
			Configuration::deleteByName('PDFQUOTATION_FOOTER') &&
			Configuration::deleteByName('PDFQUOTATION_MARGIN_FOOTER') &&
			Configuration::deleteByName('PDFQUOTATION_MARGIN_HEADER');
	}

	/**
	 * Load the configuration form
	 */
	public function getContent()
	{
		/**
		 * If values have been submitted in the form, process.
		 */
		if (((bool)Tools::isSubmit('submitPdfquotationModule')) == true)
		{
			$this->_postProcess();
		}

		$this->context->smarty->assign('module_dir', $this->_path);

		$output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

		return $output.$this->renderForm();
	}

	/**
	 * Set values for the inputs.
	 */
	protected function getConfigFormValues()
	{
		$languages = Language::getLanguages(false);
		$fields = array (
			'PDFQUOTATION_SEND_MAIL' => Configuration::get('PDFQUOTATION_SEND_MAIL', 1),
			'PDFQUOTATION_MAIL' => Configuration::get('PDFQUOTATION_MAIL'),
			'PDFQUOTATION_HEADER' => Configuration::get('PDFQUOTATION_HEADER'),
			'PDFQUOTATION_BEFORE' => Configuration::get('PDFQUOTATION_BEFORE'),
			'PDFQUOTATION_AFTER' => Configuration::get('PDFQUOTATION_AFTER'),
			'PDFQUOTATION_PREFIX' => Configuration::get('PDFQUOTATION_PREFIX'),
			'PDFQUOTATION_FOOTER' => Configuration::get('PDFQUOTATION_FOOTER'),
			'PDFQUOTATION_MARGIN_FOOTER' => Configuration::get('PDFQUOTATION_MARGIN_FOOTER'),
			'PDFQUOTATION_MARGIN_HEADER' => Configuration::get('PDFQUOTATION_MARGIN_HEADER')
		);

		foreach (array_keys($fields) as $field)
		{
			$value = null;
			if(Configuration::isLangKey($field))
			{
				foreach ($languages as $language)
				{
					$value[$language['id_lang']] = Configuration::get($field, $language['id_lang']);
				}
			}
			else
			{
				$value = Configuration::get($field);
			}
			$fields[$field] = $value;
		}

		return $fields;
	}

	/**
	 * Save form data.
	 */
	protected function _postProcess()
	{
		$languages = Language::getLanguages(false);

		$form_values = $this->getConfigFormValues();

		foreach (array_keys($form_values) as $key)
		{
			$value = null;
			foreach ($languages as $language)
			{
				$value[$language['id_lang']] = Tools::getValue($key.'_'.$language['id_lang']);
                $get = Tools::getValue($key.'_'.$language['id_lang']);
				if($get === false) {
					$value = Tools::getValue($key);
				}
			}
			Configuration::updateValue($key, $value, true);
		}
	}

	/**
	 * Create the form that will be displayed in the configuration of your module.
	 */
	protected function renderForm()
	{
		$helper = new HelperForm();

		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitPdfquotationModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);

		return $helper->generateForm(array($this->getConfigForm()));
	}

	/**
	 * Create the structure of your form.
	 */
	protected function getConfigForm()
	{
		return array(
			'form' => array(
				'tinymce' => true,
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'type' => 'radio',
						'name' => 'PDFQUOTATION_SEND_MAIL',
						'label' => $this->l('Send Email to prevent new quotation'),
						'lang' => false,
						'required' => true,
						'is_bool'   => true,
						'values'    => array(                                 // $values contains the data itself.
							array(
								'id'    => 'active_on',                           // The content of the 'id' attribute of the <input> tag, and of the 'for' attribute for the <label> tag.
								'value' => 1,                                     // The content of the 'value' attribute of the <input> tag.
								'label' => $this->l('Enabled')                    // The <label> for this radio button.
							),
							array(
								'id'    => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'text',
						'name' => 'PDFQUOTATION_MAIL',
						'label' => $this->l('Email to send quotation'),
						'lang' => false
					),
					array(
						'type' => 'text',
						'name' => 'PDFQUOTATION_PREFIX',
						'label' => $this->l('Prefix number of quotation'),
						'lang' => false
					),
					array(
						'cols' => 10,
						'rows' => 10,
						'type' => 'textarea',
						'name' => 'PDFQUOTATION_HEADER',
						'label' => $this->l('Text Header'),
						'lang' => true,
						'autoload_rte' => true,
						'cast' => 'strval'
					),
					array(
						'type' => 'text',
						'name' => 'PDFQUOTATION_MARGIN_HEADER',
						'label' => $this->l('Height Header'),
						'desc' => $this->l('Specify height of header. it depends of the length of your header text. You must specify a value between 1 and 100. If you see that quotation text is under your header you must increase this value'),
						'lang' => false,
						'cast' => 'intval'
					),
					array(
						'cols' => 10,
						'rows' => 10,
						'type' => 'textarea',
						'name' => 'PDFQUOTATION_BEFORE',
						'label' => $this->l('Text Header quotation'),
						'lang' => true,
						'autoload_rte' => true,
						'cast' => 'strval'
					),
					array(
						'cols' => 10,
						'rows' => 10,
						'type' => 'textarea',
						'name' => 'PDFQUOTATION_AFTER',
						'label' => $this->l('Text Footer quotation'),
						'lang' => true,
						'autoload_rte' => true,
						'cast' => 'strval'
					),
					array(
						'cols' => 10,
						'rows' => 10,
						'type' => 'textarea',
						'name' => 'PDFQUOTATION_FOOTER',
						'label' => $this->l('Text Footer'),
						'lang' => true,
						'autoload_rte' => true,
						'cast' => 'strval'
					),
					array(
						'type' => 'text',
						'name' => 'PDFQUOTATION_MARGIN_FOOTER',
						'label' => $this->l('Height Footer'),
						'desc' => $this->l('Specify height of footer. it depends of the length of your footer text. You must specify a value between 1 and 50. If you see that footer isn\'t totally displayed, you must increase this value'),
						'lang' => false,
						'cast' => 'intval'
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				),
			),
		);
	}

	public function hookHeader()
	{
		$this->context->controller->addJS($this->_path.'/views/js/vendor/jquery.hoverIntent.minified.js');
		$this->context->controller->addJS($this->_path.'/views/js/front.js');
		$this->context->controller->addCSS($this->_path.'/views/css/front.css');
	}

	public function hookDisplayShoppingCartFooter()
	{
		return $this->display(__FILE__, 'views/templates/hook/shoppingcart.tpl');
	}

    public function hookAjaxCall($params)
	{
		//Redirect Order process if one information required is missing
		if(Tools::getValue('first_name') == "" || Tools::getValue('last_name') == "" || Tools::getValue('email') == "" ||
			Tools::getValue('phone') == "" || Tools::getValue('contacted') == "" || Tools::getValue('spam') != "") {

			Tools::redirect('index.php?controller=order&error-pdf=1');
		}

		//Duplicate cart in order to isolate cart product at the moment where customer generate quotation else customer can
		//add new product to current cart after quotation was generated
		$oldCart = $params['context']->cart;
		$newCart = clone $oldCart;
		$newCart->id = null;
		$newCart->save();

		foreach($oldCart->getProducts() as $product) {
			$newCart->updateQty($product["cart_quantity"], $product["id_product"], $product["id_product_attribute"]);
		}

		//Save Quotation
		$quotation = new QuotationObject();
		$quotation->id_cart = $newCart->id;
		$quotation->id_customer = $params['context']->customer->id;
		$quotation->first_name = Tools::getValue('first_name');
		$quotation->last_name = Tools::getValue('last_name');
		$quotation->email = Tools::getValue('email');
		$quotation->phone = Tools::getValue('phone');
		$quotation->contacted = Tools::getValue('contacted');
		$quotation->date_add = date('Y-m-d H:i:s');
		$quotation->deleted = 1;
		$quotation->add();

		$quotation->ref_quotation = Configuration::get('PDFQUOTATION_PREFIX').sprintf('%07d', $quotation->id);
		$quotation->update();

		//Generate PDF
        $pdf = new PDF($quotation, 'Quotation', $params['context']->smarty);

        /*I Render Method of PDF Class (I can't override this method else it's for all pdf: invoice, OrderReturn... */
        $render = false;
		$pdf->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
		foreach ($pdf->objects as $object)
		{
			$template = $pdf->getTemplateObject($object);
			if (!$template)
				continue;

			if (empty($pdf->filename))
			{
				$pdf->filename = $template->getFilename();
				if (count($pdf->objects) > 1)
					$pdf->filename = $template->getBulkFilename();
			}

			$template->assignHookData($object);

            $pdf->pdf_renderer->setListIndentWidth(4);

			$pdf->pdf_renderer->createHeader($template->getHeader());
			$pdf->pdf_renderer->createFooter($template->getFooter());
			$pdf->pdf_renderer->createContent($template->getContent());
            $pdf->pdf_renderer->SetAutoPageBreak(true, Configuration::get('PDFQUOTATION_MARGIN_FOOTER'));
            $pdf->pdf_renderer->SetFooterMargin(Configuration::get('PDFQUOTATION_MARGIN_FOOTER'));
            $pdf->pdf_renderer->setMargins(10, Configuration::get('PDFQUOTATION_MARGIN_HEADER'), 10);
            $pdf->pdf_renderer->AddPage();
            $pdf->pdf_renderer->writeHTML($pdf->pdf_renderer->content, true, false, true, false, '');

			$render = true;

			unset($template);
		}

		if ($render)
		{
			//Clean the output buffer
			if (ob_get_level() && ob_get_length() > 0)
				ob_clean();

			$shop = new Shop(Context::getContext()->shop->id);
			$contacted = Tools::getValue('contacted')=="1"?$this->l('Yes'):$this->l('No');

			$vars = array(
				'{firstname}' => Tools::getValue('first_name'),
				'{lastname}' => Tools::getValue('last_name'),
				'{phone}' => Tools::getValue('phone'),
				'{email}' => Tools::getValue('email'),
				'{contacted}' => $contacted,
				'{url}' => $shop->getBaseURL()."img/quotation/".$pdf->filename
			);

// 			Before Prestashop 1.6.1.5
//			$content = '<div style="font-size:11px">';
//				$content = "<strong>".$this->l('A new quotation is arrived. All information')." : <br /><br /></strong>";
//				$content .= $this->l('First Name')." : ".Tools::getValue('first_name')."<br />";
//				$content .= $this->l('Last Name')." : ".Tools::getValue('last_name')."<br />";
//				$content .= $this->l('Phone')." : ".Tools::getValue('phone')."<br />";
//				$content .= $this->l('Email')." : ".Tools::getValue('email')."<br />";
//				$content .= $this->l('To be contacted again')." : ".$contacted."<br />";
//				$content .= $this->l('URL')." : ".'<a href="'.$shop->getBaseURL()."img/quotation/".$pdf->filename.'">'.$this->l('See Quotation')."</a><br />";
//			$content .= '</div>';

			if (Configuration::get('PDFQUOTATION_SEND_MAIL') == 1 && Validate::isEmail(Configuration::get('PDFQUOTATION_MAIL'))) {
				//Before Prestashop 1.6.1.5
				//Mail::Send($content, $this->l('New Quotation') . " - " . date("d-m-Y", time()), 'text/html', Configuration::get('PDFQUOTATION_MAIL'), Tools::getValue('email'));

				Mail::Send(
					Context::getContext()->language->id,
					'quotation',
					$this->l('New Quotation'),
					$vars,
					Configuration::get('PDFQUOTATION_MAIL'),
					null,
					null,
					null,
					null,
					null,
					_PS_MODULE_DIR_."pdfquotation/mails/",
					false,
					null
				);
			}

			$pdf2 = clone $pdf->pdf_renderer;
			$pdf->pdf_renderer->render(_PS_QUOTATION_DIR_.$pdf->filename, 'F');
			return $pdf2->render($pdf->filename, 'D');
		}
	}

	public function hookDisplayCustomerAccount() {
		$this->context->smarty->assign('base_dir', _PS_BASE_URL_);
		return $this->display(__FILE__, 'views/templates/hook/customer_account.tpl');
	}
}
