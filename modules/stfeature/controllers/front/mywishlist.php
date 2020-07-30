<?php
/**
 * 2007-2017 Splashythemes
 *
 * NOTICE OF LICENSE
 *
 * St feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
 *
 * DISCLAIMER
 *
 *  @Module Name: ST Feature
 *  @author    splashythemes <splashythemes@gmail.com>
 *  @copyright 2007-2017 splashythemes
 *  @license   http://splashythemes.com - prestashop template provider
 */
require_once(_PS_MODULE_DIR_.'stfeature/classes/WishList.php');
require_once(_PS_MODULE_DIR_.'stfeature/classes/StfeatureProduct.php');

class stFeatureMyWishListModuleFrontController extends ModuleFrontController
{
	public $php_self;
	
	public function displayAjax()
    {
        // Add or remove product with Ajax
        $context = Context::getContext();
		$action = Tools::getValue('action');		
		$id_wishlist = (int)Tools::getValue('id_wishlist');
		$id_product = (int)Tools::getValue('id_product');
		$quantity = (int)Tools::getValue('quantity');
		$id_product_attribute = (int)Tools::getValue('id_product_attribute');

		$array_result = array();
		$errors = array();
		$result = array();
		// Instance of module class for translations
		
		if ($context->customer->isLogged())
		{
			if ($id_wishlist && StWishList::exists($id_wishlist, $context->customer->id) === true)
				$context->cookie->id_wishlist = (int)$id_wishlist;

			if ((int)$context->cookie->id_wishlist > 0 && !StWishList::exists($context->cookie->id_wishlist, $context->customer->id))
				$context->cookie->id_wishlist = '';

			// if (empty($context->cookie->id_wishlist) === true || $context->cookie->id_wishlist == false)
				// $errors[] = true;
			
			if (($action == 'add' || $action == 'remove') && empty($id_product) === false)
			{
				if (!isset($context->cookie->id_wishlist) || $context->cookie->id_wishlist == '')
				{
					$wishlist = new StWishList();
					$wishlist->id_shop = $context->shop->id;
					$wishlist->id_shop_group = $context->shop->id_shop_group;
					$wishlist->default = 1;
					
					$wishlist->name = $this->l('My wishlist','mywishlist');
					$wishlist->id_customer = (int)$context->customer->id;
					list($us, $s) = explode(' ', microtime());
					srand($s * $us);
					$wishlist->token = Tools::strtoupper(Tools::substr(sha1(uniqid(rand(), true)._COOKIE_KEY_.$context->customer->id), 0, 16));
					$wishlist->add();
					$context->cookie->id_wishlist = (int)$wishlist->id;
					$result['id_wishlist'] = $context->cookie->id_wishlist;
				}
				if ($action == 'add')
					StWishList::addProduct($context->cookie->id_wishlist, $context->customer->id, $id_product, $id_product_attribute, $quantity);
				else if ($action == 'remove')
					StWishList::removeProduct($context->cookie->id_wishlist, $context->customer->id, $id_product, $id_product_attribute);
				
				$result[] = true;
			}
			
			if ($action == 'add-wishlist')
			{
				$name_wishlist = Tools::getValue('name_wishlist');
				if (empty($name_wishlist))
					$errors[] = $this->module->l('You must specify a name.', 'mywishlist');
				if (StWishList::isExistsByNameForUser($name_wishlist))
					$errors[] = $this->module->l('This name is already used by another list.', 'mywishlist');
				if (!Validate::isMessage($name_wishlist))
					$errors[] = $this->module->l('This name is is incorrect', 'mywishlist');
				if (!count($errors))
				{
					$wishlist = new StWishList();
					$wishlist->id_shop = $this->context->shop->id;
					$wishlist->id_shop_group = $this->context->shop->id_shop_group;
					$wishlist->name = $name_wishlist;
					$wishlist->id_customer = (int)$this->context->customer->id;
					!$wishlist->isDefault($wishlist->id_customer) ? $wishlist->default = 1 : '';
					list($us, $s) = explode(' ', microtime());
					srand($s * $us);
					$wishlist->token = Tools::strtoupper(Tools::substr(sha1(uniqid(rand(), true)._COOKIE_KEY_.$this->context->customer->id), 0, 16));
					$wishlist->add();
					$leo_is_rewrite_active = (bool)Configuration::get('PS_REWRITING_SETTINGS');
					if ($leo_is_rewrite_active)
					{
						$check_leo_is_rewrite_active = '?';
					}
					else
					{
						$check_leo_is_rewrite_active = '&';
					}
					$checked = '';
					if ($wishlist->default == 1)
						$checked = 'checked="checked"';
					
					$this->context->smarty->assign(array(
							'wishlist' => $wishlist,
							'checked' => $checked,
							'url_view_wishlist' => $this->context->link->getModuleLink('stfeature', 'viewwishlist').$check_leo_is_rewrite_active.'token='.$wishlist->token,
						)
					);
									
					$result['wishlist'] = $this->module->fetch('module:stfeature/views/templates/front/st_wishlist_new.tpl');;
					$result['message'] = $this->module->l('The new wishlist has been created', 'mywishlist');
				}
			}
			
			if ($action == 'delete-wishlist')
			{
				$wishlist = new StWishList((int)$id_wishlist);
				if ($this->context->customer->id != $wishlist->id_customer || !Validate::isLoadedObject($wishlist))
					$errors[] = $this->module->l('Cannot delete this wishlist', 'mywishlist');
				if (!count($errors))
				{
					$wishlist->delete();
					$result[] = true;
				}
			}
			
			if ($action == 'default-wishlist')
			{
				$wishlist = new StWishList((int)$id_wishlist);				
				if ($this->context->customer->id != $wishlist->id_customer || !Validate::isLoadedObject($wishlist))
					$errors[] = $this->module->l('Cannot update this wishlist', 'mywishlist');
				if (!count($errors))
				{
					$wishlist->setDefault();
					$result[] = true;
				}
			}
			
			if ($action == 'show-wishlist-product')
			{
				$wishlist = new StWishList((int)$id_wishlist);
				if ($this->context->customer->id != $wishlist->id_customer || !Validate::isLoadedObject($wishlist))
					$errors[] = $this->module->l('Cannot show the product(s) of this wishlist', 'mywishlist');
				if (!count($errors))
				{
					// echo '<pre>';
					// print_r($this->getSimpleProductByIdWishlist($id_wishlist));
					// die();
					$products = array();
					$show_send_wishlist = 0;
					$wishlist_product = StWishList::getSimpleProductByIdWishlist($id_wishlist);
					$product_object = new StfeatureProduct();
					if (count($wishlist_product) > 0)
					{
						foreach ($wishlist_product as $wishlist_product_item)
						{
							$list_product_tmp = array();
							$list_product_tmp['wishlist_info'] = $wishlist_product_item;
							$list_product_tmp['product_info'] = $product_object->getTemplateVarProduct2($wishlist_product_item['id_product'], $wishlist_product_item['id_product_attribute']);
							$products[] = $list_product_tmp;
						}
						$show_send_wishlist = 1;
					}
					// echo '<pre>';
					// print_r($list_product);
					// die();
					$wishlists = StWishList::getByIdCustomer($this->context->customer->id);
					// echo '<pre>';
					// print_r($wishlists);die();
					foreach ($wishlists as $key => $wishlists_item)
					{
						if ($wishlists_item['id_wishlist'] == $id_wishlist)
						{
							unset($wishlists[$key]);
						}
					}
					$this->context->smarty->assign(array(
							'products' => $products,
							'wishlists' => $wishlists,
						)
					);
					$result['html'] = $this->module->fetch('module:stfeature/views/templates/front/st_my_wishlist_product.tpl');
					$result['show_send_wishlist'] =  $show_send_wishlist;
				}
			}
			
			if ($action == 'send-wishlist')
			{
				$wishlist = new StWishList((int)$id_wishlist);
				if ($this->context->customer->id != $wishlist->id_customer || !Validate::isLoadedObject($wishlist))
					$errors[] = $this->module->l('Invalid wishlist', 'mywishlist');
				if (!count($errors))
				{
					$to = Tools::getValue('email');
					$toName = Tools::safeOutput(Configuration::get('PS_SHOP_NAME'));
					$customer = $context->customer;
					if (Validate::isLoadedObject($customer))
					{
						if(
							Mail::Send(
								$context->language->id,
								'wishlist',
								sprintf(Mail::l('Message from %1$s %2$s', $context->language->id), $customer->lastname, $customer->firstname),
								array(
									'{lastname}' => $customer->lastname,
									'{firstname}' => $customer->firstname,
									'{wishlist}' => $wishlist->name,
									'{message}' => $context->link->getModuleLink('leofeature', 'viewwishlist', array('token' => $wishlist->token))
								),
								$to, $toName, $customer->email, $customer->firstname.' '.$customer->lastname, null, null, $this->module->module_path.'/mails/'
							)
						)
						{
							$result[] = true;
						}
						else
						{
							$errors[] = $this->module->l('Wishlist send error', 'mywishlist');
						}
					}
					else
					{
						$errors[] = $this->module->l('Invalid customer', 'mywishlist');
					}
				}
			}
			
			if ($action == 'delete-wishlist-product')
			{
				$id_wishlist_product = Tools::getValue('id_wishlist_product');
				$wishlist = new StWishList((int)$id_wishlist);
				if ($this->context->customer->id != $wishlist->id_customer || !Validate::isLoadedObject($wishlist) || !Validate::isUnsignedId($id_wishlist_product))
					$errors[] = $this->module->l('Invalid wishlist', 'mywishlist');
				if (!count($errors))
				{
					if (StWishList::removeProductWishlist($id_wishlist, $id_wishlist_product))
					{
						$result[] = true;
 					}
					else
					{
						$errors[] = $this->module->l('Cannot delete', 'mywishlist');
					}
				}
			}
			
			if ($action == 'get-wishlist-info')
			{
				$wishlist = new StWishList((int)$id_wishlist);
				if ($this->context->customer->id != $wishlist->id_customer || !Validate::isLoadedObject($wishlist))
					$errors[] = $this->module->l('Invalid wishlist', 'mywishlist');
				if (!count($errors))
				{
					$wishlist_product = StWishList::getInfosByIdCustomer($this->context->customer->id, $id_wishlist);
					if ($wishlist_product['nbProducts'] && $wishlist_product['nbProducts'] >0)
					{
						$result['number_product'] = $wishlist_product['nbProducts'];
					}
					else
					{
						$result['number_product'] = 0;
					}
				}
			}
			
			if ($action == 'update-wishlist-product')
			{
				$id_wishlist_product = Tools::getValue('id_wishlist_product');
				$priority = Tools::getValue('priority');
				$wishlist = new StWishList((int)$id_wishlist);
				
				if ($this->context->customer->id != $wishlist->id_customer || !Validate::isLoadedObject($wishlist) || !Validate::isUnsignedInt($priority) || !Validate::isUnsignedInt($quantity) || !Validate::isUnsignedId($id_wishlist_product))
					$errors[] = $this->module->l('Invalid wishlist', 'mywishlist');
				if (!count($errors))
				{								
					if (WishList::updateProductWishlist($id_wishlist, $id_wishlist_product, $priority, $quantity))
					{
						$result[] = true;
 					}
					else
					{
						$errors[] = $this->module->l('Cannot update', 'mywishlist');
					}
				}
			}
			
			if ($action == 'move-wishlist-product')
			{
				$id_wishlist_product = Tools::getValue('id_wishlist_product');
				$priority = (int)Tools::getValue('priority');
				$id_old_wishlist = (int)Tools::getValue('id_old_wishlist');
				$id_new_wishlist = (int)Tools::getValue('id_new_wishlist');
				$new_wishlist = new StWishList((int)$id_new_wishlist);
				$old_wishlist = new StWishList((int)$id_old_wishlist);
				if (!Validate::isUnsignedId($id_product) || !Validate::isUnsignedInt($id_product_attribute) || !Validate::isUnsignedInt($quantity) ||
					!Validate::isUnsignedInt($priority) || ($priority < 0 && $priority > 2) || !Validate::isUnsignedId($id_old_wishlist) || !Validate::isUnsignedId($id_new_wishlist) || !Validate::isUnsignedId($id_wishlist_product) ||
					(Validate::isLoadedObject($new_wishlist) && $new_wishlist->id_customer != $this->context->customer->id) ||
					(Validate::isLoadedObject($old_wishlist) && $old_wishlist->id_customer != $this->context->customer->id))
				{
					$errors[] = $this->module->l('Error while moving product to another list', 'mywishlist');
				}
					
				$res = true;
				$check = Db::getInstance()->getRow('SELECT quantity, id_wishlist_product FROM '._DB_PREFIX_.'stfeature_wishlist_product
					WHERE `id_product` = '.(int)$id_product.' AND `id_product_attribute` = '.(int)$id_product_attribute.' AND `id_wishlist` = '.(int)$id_new_wishlist);
				
				$res &= $old_wishlist->removeProductWishlist($id_old_wishlist, $id_wishlist_product);
				if ($check)
				{
					// die('aa');
					$res &= $new_wishlist->updateProductWishlist($id_new_wishlist, $check['id_wishlist_product'], $priority, $quantity + $check['quantity']);
				}
				else
				{
					// die('bbb');	
					$res &= $new_wishlist->addProduct($id_new_wishlist, $this->context->customer->id, $id_product, $id_product_attribute, $quantity);
				}

				if ($res)
				{
					$result[] = true;
				}
				else
				{
					$errors[] = $this->module->l('Error while moving product to another list', 'mywishlist');
				}
			}
			
		} else
			$errors[] = $this->l('You must be logged in to manage your wishlist.', 'mywishlist');
		
		$array_result['result'] = $result;
		$array_result['errors'] = $errors;
		die(Tools::jsonEncode($array_result));
    }
	
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$this->php_self = 'mywishlist';
		
		if (Tools::getValue('ajax')) {
            return;
        }
		parent::initContent();
		if (!Configuration::get('STFEATURE_ENABLE_PRODUCTWISHLIST')) {
            return Tools::redirect('index.php?controller=404');
        }
		
		if ($this->context->customer->isLogged())
		{
			
			$wishlists = StWishList::getByIdCustomer($this->context->customer->id);
			if (count($wishlists)>0)
			{
				foreach ($wishlists as $key => $wishlists_val)
				{
					$wishlist_product = StWishList::getInfosByIdCustomer($this->context->customer->id, $wishlists_val['id_wishlist']);
					$wishlists[$key]['number_product'] = $wishlist_product['nbProducts'];
				}
				
			}
			$this->context->smarty->assign(array(
					'wishlists' => $wishlists,
					// 'nbProducts' => WishList::getInfosByIdCustomer($this->context->customer->id)
					'view_wishlist_url' => $this->context->link->getModuleLink('stfeature', 'viewwishlist'),
					'leo_is_rewrite_active' => (bool)Configuration::get('PS_REWRITING_SETTINGS'),
				)
			);
			// echo '<pre>';
			// print_r($wishlist_product);
			// die();
		}
		else
		{
			Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('stfeature', 'mywishlist')));
		}
		$this->setTemplate('module:stfeature/views/templates/front/st_my_wishlist.tpl');
	}
	
	//DONGND:: add meta title, meta description, meta keywords
	public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();		
		
        $page['meta']['title'] = Configuration::get('PS_SHOP_NAME').' - '.$this->l('My Wishlist', 'mywishlist');
		$page['meta']['keywords'] = $this->l('my-wishlist','mywishlist');
		$page['meta']['description'] = $this->l('My Wishlist','mywishlist');
        // echo '<pre>';
        // print_r($page);die();
        return $page;
    }
	
	//DONGND:: add breadcrumb
	public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
		// $link = StBlogHelper::getInstance()->getFontBlogLink();
        // $config = StBlogConfig::getInstance();	
		$breadcrumb['links'][] = [
            'title' => $this->l('My Account','mywishlist'),
            'url' => $this->context->link->getPageLink('my-account', true),
        ];
		
        $breadcrumb['links'][] = [
            'title' => $this->l('My Wishlist','mywishlist'),
            'url' => $this->context->link->getModuleLink('stfeature', 'mywishlist'),
        ];

        return $breadcrumb;
    }
	
	//DONGND:: get layout
	public function getLayout()
    {
        $entity = 'module-leofeature-'.$this->php_self;
		
        $layout = $this->context->shop->theme->getLayoutRelativePathForPage($entity);
		
        if ($overridden_layout = Hook::exec(
            'overrideLayoutTemplate',
            array(
                'default_layout' => $layout,
                'entity' => $entity,
                'locale' => $this->context->language->locale,
                'controller' => $this,
            )
        )) {
            return $overridden_layout;
        }

        if ((int) Tools::getValue('content_only')) {
            $layout = 'layouts/layout-content-only.tpl';
        }

        return $layout;
    }
	
	
}
