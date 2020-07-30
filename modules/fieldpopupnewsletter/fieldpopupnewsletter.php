<?php

if (!defined('_PS_VERSION_'))
	exit;

class FieldPopupNewsletter extends Module
{
	private $_html = '';
	private $_postErrors = array();
	const GUEST_NOT_REGISTERED = -1;
	const CUSTOMER_NOT_REGISTERED = 0;
	const GUEST_REGISTERED = 1;
	const CUSTOMER_REGISTERED = 2;

    function __construct()
    {
		$this->name = 'fieldpopupnewsletter';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'fieldthemes';

		$this->controllers = array('verification');
		
		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Field Popup Newsletter');
		$this->description = $this->l('Shows popup newsletter window with your message');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{		

		$this->context->controller->getLanguages();
                $title = array();
                $content = array();
                foreach ($this->context->controller->_languages as $language){
                    $title[(int)$language['id_lang']] = '<h2>Get 20% off your order if you buy now !</h2>';
                    $content[(int)$language['id_lang']] = '<p>More ways to stay connected :</p>';
                }
                
                $this->_createTab();
		if (
			parent::install() == false
			|| $this->registerHook('header') == false
			|| $this->registerHook('displayHome') == false
			|| Configuration::updateValue('FIELD_TEXT', $content) == false
			|| Configuration::updateValue('FIELD_TITLE', $title) == false
			|| Configuration::updateValue('FIELD_WIDTH', 650) == false
			|| Configuration::updateValue('FIELD_HEIGHT', 437) == false
			|| Configuration::updateValue('FIELD_NEWSLETTER', true) == false
			|| Configuration::updateValue('FIELD_BG', true) == false
			|| Configuration::updateValue('FIELD_BG_IMAGE',  _MODULE_DIR_.$this->name.'/img/background_image1.jpg') == false
			|| Configuration::updateValue('FIELD_COUNTDOWN_POPUP', '2016-08-10 03:38:00') == false
			|| Configuration::updateValue('FIELD_COUNTDOWN_POPUP_START', '0000-00-00 00:00:00') == false
                    )
			return false;
		return true;
	}
	
	public function uninstall()
	{
		$this->context->controller->getLanguages();
		foreach ($this->context->controller->_languages as $language){
			Configuration::deleteByName('FIELD_TEXT_'.(int)$language['id_lang']);
                        Configuration::deleteByName('FIELD_TITLE_'.(int)$language['id_lang']);
                }
                $this->_deleteTab();
		return 
			Configuration::deleteByName('FIELD_WIDTH') &&
			Configuration::deleteByName('FIELD_HEIGHT') &&
			Configuration::deleteByName('FIELD_NEWSLETTER') &&
			Configuration::deleteByName('FIELD_BG') &&
			Configuration::deleteByName('FIELD_BG_IMAGE') &&
			Configuration::deleteByName('FIELD_COUNTDOWN_POPUP') &&
			Configuration::deleteByName('FIELD_COUNTDOWN_POPUP_START') &&
			parent::uninstall();
	}
        
        private function _createTab()
        {
            $response = true;

            // First check for parent tab
            $parentTabID = Tab::getIdFromClassName('AdminFieldMenu');

            if ($parentTabID) {
                $parentTab = new Tab($parentTabID);
            }
            else {
                $parentTab = new Tab();
                $parentTab->active = 1;
                $parentTab->name = array();
                $parentTab->class_name = "AdminFieldMenu";
                foreach (Language::getLanguages() as $lang) {
                    $parentTab->name[$lang['id_lang']] = "FIELDTHEMES";
                }
                $parentTab->id_parent = 0;
                $parentTab->module = $this->name;
                $response &= $parentTab->add();
            }
// Check for parent tab2
			$parentTab_2ID = Tab::getIdFromClassName('AdminFieldMenuSecond');
			if ($parentTab_2ID) {
				$parentTab_2 = new Tab($parentTab_2ID);
			}
			else {
				$parentTab_2 = new Tab();
				$parentTab_2->active = 1;
				$parentTab_2->name = array();
				$parentTab_2->class_name = "AdminFieldMenuSecond";
				foreach (Language::getLanguages() as $lang) {
					$parentTab_2->name[$lang['id_lang']] = "FieldThemes Configure";
				}
				$parentTab_2->id_parent = $parentTab->id;
				$parentTab_2->module = $this->name;
				$response &= $parentTab_2->add();
			}
			// Created tab
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = "AdminPopupNewsletter";
            $tab->name = array();
            foreach (Language::getLanguages() as $lang) {
                $tab->name[$lang['id_lang']] = "Popup newsletter";
            }
            $tab->id_parent = $parentTab_2->id;
            $tab->module = $this->name;
            $response &= $tab->add();

            return $response;
        }

        /* ------------------------------------------------------------- */
        /*  DELETE THE TAB MENU
        /* ------------------------------------------------------------- */
        private function _deleteTab()
        {
            $id_tab = Tab::getIdFromClassName('AdminPopupNewsletter');
            $parentTabID = Tab::getIdFromClassName('AdminFieldMenu');

            $tab = new Tab($id_tab);
            $tab->delete();
// Get the number of tabs inside our parent tab
        // If there is no tabs, remove the parent
		$parentTab_2ID = Tab::getIdFromClassName('AdminFieldMenuSecond');
		$tabCount_2 = Tab::getNbTabs($parentTab_2ID);
        if ($tabCount_2 == 0) {
            $parentTab_2 = new Tab($parentTab_2ID);
            $parentTab_2->delete();
        }
            // Get the number of tabs inside our parent tab
            // If there is no tabs, remove the parent
            $tabCount = Tab::getNbTabs($parentTabID);
            if ($tabCount == 0) {
                $parentTab = new Tab($parentTabID);
                $parentTab->delete();
            }

            return true;
        }

	public function getContent()
	{

		$this->context->controller->getLanguages();
		$output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
                $errors = array();
                
		if (Tools::isSubmit('field_submit')) {
			Configuration::updateValue('FIELD_WIDTH', (int)Tools::getValue('FIELD_WIDTH'));
			Configuration::updateValue('FIELD_HEIGHT', (int)Tools::getValue('FIELD_HEIGHT'));
			Configuration::updateValue('FIELD_NEWSLETTER', (bool)Tools::getValue('FIELD_NEWSLETTER'));
                        Configuration::updateValue('FIELD_BG', Tools::getValue('FIELD_BG'));
                        if (Tools::isSubmit('FIELD_BG_IMAGE')){
                            Configuration::updateValue('FIELD_BG_IMAGE', Tools::getValue('FIELD_BG_IMAGE'));
                        }
                        $message_trads = array();
                        $message_trads2 = array();
                        foreach ($_POST as $key => $value){
                                if (preg_match('/FIELD_TITLE_/i', $key))
                                {
                                        $id_lang = preg_split('/FIELD_TITLE_/i', $key);
                                        $message_trads2[(int)$id_lang[1]] = $value;
                                }
                                if (preg_match('/FIELD_TEXT_/i', $key))
                                {
                                        $id_lang = preg_split('/FIELD_TEXT_/i', $key);
                                        $message_trads[(int)$id_lang[1]] = $value;
                                }
                        }
                        Configuration::updateValue('FIELD_TEXT', $message_trads, true);
                        Configuration::updateValue('FIELD_TITLE', $message_trads2, true);
                        $start = Tools::getValue('FIELD_COUNTDOWN_POPUP_START');
                        if (!$start) {
                            $start = '0000-00-00 00:00:00';
                        }
                        $end = Tools::getValue('FIELD_COUNTDOWN_POPUP');
                        if (!$end) {
                            $end = '0000-00-00 00:00:00';
                        }
                        if ($end != '0000-00-00 00:00:00' && strtotime($end) < strtotime($start)) {
                            $errors[] = $this->l('Invalid date range');
                        } else {
                            Configuration::updateValue('FIELD_COUNTDOWN_POPUP', Tools::getValue('FIELD_COUNTDOWN_POPUP'));
                            Configuration::updateValue('FIELD_COUNTDOWN_POPUP_START', Tools::getValue('FIELD_COUNTDOWN_POPUP_START'));
                        }
                        if (count($errors)){
                            $output .= $this->displayError(implode('<br />', $errors));
                        } else {
                            $output .= $this->displayConfirmation($this->l('Settings updated'));
                        }

			$this->_clearCache($this->name.'.tpl');

		}
		return $output.$this->renderForm();
	}

	public function hookDisplayHome($params)
	{
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index') {
			if ($this->getCacheId($this->name)) {
				$this->context->smarty->assign(array(
					'field_ppp' => $this->getConfigFromDB(),
				));		
			}
			return $this->display(__FILE__, $this->name.'.tpl');
		}
	}

	public function hookHeader($params)
	{
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index') {
			$this->context->controller->addJS(($this->_path).'js/init.js');
			$this->context->controller->addCSS(($this->_path).'css/styles.css', 'all');
		}
	}
        
         public function setMedia()
        {
                parent::setMedia();
                $this->addJqueryUI('ui.datepicker');
        }

	public function renderForm()
	{

		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Module Appearance'),
					'icon' => 'icon-cogs'
				),
				'input' => array(	
                                        array(
                                                'type' => 'textarea',
                                                'name' => 'FIELD_TITLE',
                                                'label' => $this->l('Popup title'),
						'rows' => 10,
						'cols' => 40,
                                                'required' => false,
                                                'lang' => true,
						'autoload_rte' => true
                                        ),
					array(
						'type' => 'text',
						'label' => $this->l('Width of popup window'),
						'name' => 'FIELD_WIDTH',
						'class' => 'fixed-width-xxl'
					),	
					array(
						'type' => 'text',
						'label' => $this->l('Height of popup window'),
						'name' => 'FIELD_HEIGHT',
						'class' => 'fixed-width-xxl'
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('Popup content'),
						'name' => 'FIELD_TEXT',
						'rows' => 10,
						'cols' => 40,
						'lang' => true,
						'autoload_rte' => true
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Show Newsletter form in popup'),
						'name' => 'FIELD_NEWSLETTER',
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->l('Yes')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->l('No')
									)
								),
						),
					array(
						'type' => 'switch',
						'label' => $this->l('Show background image'),
						'name' => 'FIELD_BG',
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => true,
										'label' => $this->l('Yes')
									),
									array(
										'id' => 'active_off',
										'value' => false,
										'label' => $this->l('No')
									)
								),
						),
                                        array(
						'type' => 'background_image',
						'label' => $this->l('Popup background image'),
						'name' => 'FIELD_BG_IMAGE',
						'size' => 30,
					),
                                        array(
                                                'type' => 'datetime',
                                                'label' => $this->l('Countdown from'),
                                                'name' => 'FIELD_COUNTDOWN_POPUP_START',
                                                'size' => 10,
                                        ),
                                        array(
                                                'type' => 'datetime',
                                                'label' => $this->l('to'),
                                                'name' => 'FIELD_COUNTDOWN_POPUP',
                                                'size' => 10,
                                                'desc' => $this->l('Leave it empty, if you do not to show countdown on popup.'),
                                        )
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);
		

		$languages = Language::getLanguages(false);
		foreach ($languages as $k => $language)
			$languages[$k]['is_default'] = (int)$language['id_lang'] == Configuration::get('PS_LANG_DEFAULT');

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->languages = $languages;
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$helper->allow_employee_form_lang = true;
		$helper->toolbar_scroll = true;
		$helper->toolbar_btn = $this->initToolbar();
		$helper->title = $this->displayName;
		$helper->submit_action = 'field_submit';
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
		);
		return $helper->generateForm(array($fields_form));
	}

	private function initToolbar()
	{
		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Save')
		);

		return $this->toolbar_btn;
	}
	
	public function getConfigFieldsValues()
	{
		$values = array(
			'FIELD_WIDTH' => Tools::getValue('FIELD_WIDTH', Configuration::get('FIELD_WIDTH')),
			'FIELD_HEIGHT' => Tools::getValue('FIELD_HEIGHT', Configuration::get('FIELD_HEIGHT')),
			'FIELD_NEWSLETTER' => Tools::getValue('FIELD_NEWSLETTER', Configuration::get('FIELD_NEWSLETTER')),
			'FIELD_BG' => Tools::getValue('FIELD_BG', Configuration::get('FIELD_BG')),
			'FIELD_BG_IMAGE' => Tools::getValue('FIELD_BG_IMAGE', Configuration::get('FIELD_BG_IMAGE')),
			'FIELD_COUNTDOWN_POPUP' => Tools::getValue('FIELD_COUNTDOWN_POPUP', Configuration::get('FIELD_COUNTDOWN_POPUP')),
			'FIELD_COUNTDOWN_POPUP_START' => Tools::getValue('FIELD_COUNTDOWN_POPUP_START', Configuration::get('FIELD_COUNTDOWN_POPUP_START')),
		);

                foreach (Language::getLanguages(false) as $lang){
                            $values['FIELD_TITLE'][(int)$lang['id_lang']] = Configuration::get('FIELD_TITLE', (int)$lang['id_lang']);
                            $values['FIELD_TEXT'][(int)$lang['id_lang']] = Configuration::get('FIELD_TEXT', (int)$lang['id_lang']);
                }
		return $values;
	}

	public function getConfigFromDB()
	{
                $now = date('Y-m-d H:i:00');
                $start_date = (Configuration::get('FIELD_COUNTDOWN_POPUP_START') ? Configuration::get('FIELD_COUNTDOWN_POPUP_START'): '0000-00-00 00:00:00');
                if (strtotime($start_date) > strtotime($now)){
                    $end_date = "0000-00-00 00:00:00";
                } else {
                    $end_date = (Configuration::get('FIELD_COUNTDOWN_POPUP') ? Configuration::get('FIELD_COUNTDOWN_POPUP'): '0000-00-00 00:00:00');
                }
		return array(
			'FIELD_WIDTH' => (Configuration::get('FIELD_WIDTH') ? Configuration::get('FIELD_WIDTH'): "400"),
			'FIELD_HEIGHT' => (Configuration::get('FIELD_HEIGHT') ? Configuration::get('FIELD_HEIGHT'): "400"),
			'FIELD_NEWSLETTER' => (Configuration::get('FIELD_NEWSLETTER') ? Configuration::get('FIELD_NEWSLETTER'): false),
			'FIELD_TEXT' => (Configuration::get('FIELD_TEXT', $this->context->language->id) ? Configuration::get('FIELD_TEXT', $this->context->language->id): false),
                        'FIELD_TITLE' => (Configuration::get('FIELD_TITLE', $this->context->language->id) ? Configuration::get('FIELD_TITLE', $this->context->language->id): false),
			'FIELD_BG' => (Configuration::get('FIELD_BG') ? Configuration::get('FIELD_BG'): 0),
			'FIELD_BG_IMAGE' => (Configuration::get('FIELD_BG_IMAGE') ? Configuration::get('FIELD_BG_IMAGE'): 0),
			'FIELD_PATH' => Tools::getShopProtocol().Context::getContext()->shop->domain.Context::getContext()->shop->physical_uri.'modules/fieldpopupnewsletter/ajax.php',
                        'FIELD_COUNTDOWN_POPUP' => $end_date
		);
	}

	/**
	 * Check if this mail is registered for newsletters
	 *
	 * @param string $customer_email
	 *
	 * @return int -1 = not a customer and not registered
	 *                0 = customer not registered
	 *                1 = registered in block
	 *                2 = registered in customer
	 */
	private function isNewsletterRegistered($customer_email)
	{
		$sql = 'SELECT `email`
				FROM '._DB_PREFIX_.'emailsubscription
				WHERE `email` = \''.pSQL($customer_email).'\'
				AND id_shop = '.$this->context->shop->id;

		if (Db::getInstance()->getRow($sql))
			return self::GUEST_REGISTERED;

		$sql = 'SELECT `newsletter`
				FROM '._DB_PREFIX_.'customer
				WHERE `email` = \''.pSQL($customer_email).'\'
				AND id_shop = '.$this->context->shop->id;

		if (!$registered = Db::getInstance()->getRow($sql))
			return self::GUEST_NOT_REGISTERED;

		if ($registered['newsletter'] == '1')
			return self::CUSTOMER_REGISTERED;

		return self::CUSTOMER_NOT_REGISTERED;
	}

	/**
	 * Return true if the registered status correspond to a registered user
	 *
	 * @param int $register_status
	 *
	 * @return bool
	 */
	protected function isRegistered($register_status)
	{
		return in_array(
			$register_status,
			array(self::GUEST_REGISTERED, self::CUSTOMER_REGISTERED)
		);
	}


	public function activateGuest($email)
	{
		return Db::getInstance()->execute(
			'UPDATE `'._DB_PREFIX_.'emailsubscription`
						SET `active` = 1
						WHERE `email` = \''.pSQL($email).'\''
		);
	}

	/**
	 * Returns a guest email by token
	 *
	 * @param string $token
	 *
	 * @return string email
	 */
	protected function getGuestEmailByToken($token)
	{
		$sql = 'SELECT `email`
				FROM `'._DB_PREFIX_.'emailsubscription`
				WHERE MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) = \''.pSQL($token).'\'
				AND `active` = 0';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Returns a customer email by token
	 *
	 * @param string $token
	 *
	 * @return string email
	 */
	protected function getUserEmailByToken($token)
	{
		$sql = 'SELECT `email`
				FROM `'._DB_PREFIX_.'customer`
				WHERE MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) = \''.pSQL($token).'\'
				AND `newsletter` = 0';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Subscribe a guest to the newsletter
	 *
	 * @param string $email
	 * @param bool   $active
	 *
	 * @return bool
	 */
	protected function registerGuest($email, $active = true)
	{
		$sql = 'INSERT INTO '._DB_PREFIX_.'emailsubscription (id_shop, id_shop_group, email, newsletter_date_add, ip_registration_newsletter, http_referer, active)
				VALUES
				('.$this->context->shop->id.',
				'.$this->context->shop->id_shop_group.',
				\''.pSQL($email).'\',
				NOW(),
				\''.pSQL(Tools::getRemoteAddr()).'\',
				(
					SELECT c.http_referer
					FROM '._DB_PREFIX_.'connections c
					WHERE c.id_guest = '.(int)$this->context->customer->id.'
					ORDER BY c.date_add DESC LIMIT 1
				),
				'.(int)$active.'
				)';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Return a token associated to an user
	 *
	 * @param string $email
	 * @param string $register_status
	 */
	protected function getToken($email, $register_status)
	{
		if (in_array($register_status, array(self::GUEST_NOT_REGISTERED, self::GUEST_REGISTERED))){
			$sql = 'SELECT MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) as token
					FROM `'._DB_PREFIX_.'emailsubscription`
					WHERE `active` = 0
					AND `email` = \''.pSQL($email).'\'';
		}
		else if ($register_status == self::CUSTOMER_NOT_REGISTERED)
		{
			$sql = 'SELECT MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\' )) as token
					FROM `'._DB_PREFIX_.'customer`
					WHERE `newsletter` = 0
					AND `email` = \''.pSQL($email).'\'';
		}

		return Db::getInstance()->getValue($sql);
	}
	
	/**
	 * Ends the registration process to the newsletter
	 *
	 * @param string $token
	 *
	 * @return string
	 */
	public function confirmEmail($token)
	{
		$activated = false;

		if ($email = $this->getGuestEmailByToken($token))
			$activated = $this->activateGuest($email);
		else if ($email = $this->getUserEmailByToken($token))
			$activated = $this->registerUser($email);

		if (!$activated)
			return $this->l('This email is already registered and/or invalid.');

		if ($discount = Configuration::get('NW_VOUCHER_CODE'))
			$this->sendVoucher($email, $discount);

		if (Configuration::get('NW_CONFIRMATION_EMAIL'))
			$this->sendConfirmationEmail($email);

		return $this->l('Thank you for subscribing to our newsletter.');
	}

	/**
	 * Send a verification email
	 *
	 * @param string $email
	 * @param string $token
	 *
	 * @return bool
	 */
	protected function sendVerificationEmail($email, $token)
	{
		$verif_url = Context::getContext()->link->getModuleLink(
			'ps_emailsubscription', 'verification', array(
				'token' => $token,
			)
		);

		return Mail::Send($this->context->language->id, 'fieldnewsletter_verif', Mail::l('Email verification', $this->context->language->id), array('{verif_url}' => $verif_url), $email, null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
	}

	/**
	 * Subscribe an email to the newsletter. It will create an entry in the newsletter table
	 * or update the customer table depending of the register status
	 *
	 * @param string $email
	 * @param int    $register_status
	 */
	protected function register($email, $register_status)
	{
		if ($register_status == self::GUEST_NOT_REGISTERED)
			return $this->registerGuest($email);

		if ($register_status == self::CUSTOMER_NOT_REGISTERED)
			return $this->registerUser($email);

		return false;
	}

	/**
	 * Subscribe a customer to the newsletter
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	protected function registerUser($email)
	{
		$sql = 'UPDATE '._DB_PREFIX_.'customer
				SET `newsletter` = 1, newsletter_date_add = NOW(), `ip_registration_newsletter` = \''.pSQL(Tools::getRemoteAddr()).'\'
				WHERE `email` = \''.pSQL($email).'\'
				AND id_shop = '.$this->context->shop->id;
		return Db::getInstance()->execute($sql);
	}

	/**
	 * Send an email containing a voucher code
	 *
	 * @param $email
	 * @param $code
	 *
	 * @return bool|int
	 */
	protected function sendVoucher($email, $code)
	{
		return Mail::Send($this->context->language->id, 'fieldnewsletter_voucher', Mail::l('Newsletter voucher', $this->context->language->id), array('{discount}' => $code), $email, null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
	}

	/**
	 * Send a confirmation email
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	protected function sendConfirmationEmail($email)
	{
		return Mail::Send($this->context->language->id, 'fieldnewsletter_conf', Mail::l('Newsletter confirmation', $this->context->language->id), array(), pSQL($email), null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
	}


	/**
	 * Register in block newsletter
	 */
	public function newsletterRegistration($email)
	{
		if (empty($email) || !Validate::isEmail($email)) {
			echo $_GET['callback'].'('.json_encode(array('<p class="alert alert-danger">'.$this->l('Invalid email address.').'</p>')).')';
			return;
		}

		$register_status = $this->isNewsletterRegistered($email);
		if ($register_status > 0) {
			echo $_GET['callback'].'('.json_encode(array('<p class="alert alert-danger">'.$this->l('This email address is already registered.').'</p>')).')';
			return;
		}
		$email = pSQL($email);
		if (!$this->isRegistered($register_status))
		{
			if (Configuration::get('NW_VERIFICATION_EMAIL'))
			{
				// create an unactive entry in the newsletter database
				if ($register_status == self::GUEST_NOT_REGISTERED)
					$this->registerGuest($email, false);

				if (!$token = $this->getToken($email, $register_status)) {
			echo $_GET['callback'].'('.json_encode(array('<p class="alert alert-danger">'.$this->l('An error occurred during the subscription process.').'</p>')).')';
					return;
				}
				$this->sendVerificationEmail($email, $token);
			echo $_GET['callback'].'('.json_encode(array('<p class="alert alert-success">'.$this->l('A verification email has been sent. Please check your inbox.').'</p>')).')';
				return;
			}
			else
			{
				if ($resp = $this->register($email, $register_status)) {
					if ($code = Configuration::get('NW_VOUCHER_CODE'))
						$resp = $this->sendVoucher($email, $code);

					if (Configuration::get('NW_CONFIRMATION_EMAIL'))
						$resp = $this->sendConfirmationEmail($email);

					if ($resp == true) 
			echo $_GET['callback'].'('.json_encode(array('<p class="alert alert-success">'.$this->l('You have successfully subscribed to this newsletter.').'</p>')).')';
					else 
			echo $_GET['callback'].'('.json_encode(array('<p class="alert alert-success">'.$resp.'</p>')).')';
					return;
				}
				else {
			echo $_GET['callback'].'('.json_encode(array('<p class="alert alert-danger">'.$this->l('An error occurred during the subscription process.').'</p>')).')';
					return;
				}
			}
		}
	}

}