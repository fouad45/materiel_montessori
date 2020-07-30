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

class stFeatureViewWishlistModuleFrontController extends ModuleFrontController
{
	public $php_self;
	
	public function __construct()
	{
		parent::__construct();
		$this->context = Context::getContext();
		
	}

	public function initContent()
	{
		$this->php_self = 'viewwishlist';
		
		parent::initContent();
		// print_r('testaa');die();
		if (!Configuration::get('STFEATURE_ENABLE_PRODUCTWISHLIST')) {
            return Tools::redirect('index.php?controller=404');
        }
		$token = Tools::getValue('token');		

		if ($token)
		{
			$wishlist = StWishList::getByToken($token);
			
			$wishlists = StWishList::getByIdCustomer((int)$wishlist['id_customer']);
			// print_r();die();
			if (count($wishlists) > 1)
			{
				foreach ($wishlists as $key => $wishlists_item)
				{
					if ($wishlists_item['id_wishlist'] == $wishlist['id_wishlist'])
					{
						unset($wishlists[$key]);
					}
				}
			}
			else
			{
				$wishlists = array();
			}
			
			$products = array();
			$wishlist_product = StWishList::getSimpleProductByIdWishlist((int)$wishlist['id_wishlist']);
			$product_object = new StfeatureProduct();
			if (count($wishlist_product) > 0)
			{
				foreach ($wishlist_product as $wishlist_product_item)
				{
					$list_product_tmp = array();
					$list_product_tmp['wishlist_info'] = $wishlist_product_item;
					$list_product_tmp['product_info'] = $product_object->getTemplateVarProduct2($wishlist_product_item['id_product'], $wishlist_product_item['id_product_attribute']);
					$list_product_tmp['product_info']['wishlist_quantity'] = $wishlist_product_item['quantity'];
					$products[] = $list_product_tmp;
				}
				
			}
			// echo '<pre>';
			// print_r($products);die();
			StWishList::incCounter((int)$wishlist['id_wishlist']);		

			$this->context->smarty->assign(
				array(
					'current_wishlist' => $wishlist,							
					'wishlists' => $wishlists,
					'products' => $products,
					'view_wishlist_url' => $this->context->link->getModuleLink('stfeature', 'viewwishlist'),
					'show_button_cart' => Configuration::get('STFEATURE_ENABLE_AJAXCART'),
					'leo_is_rewrite_active' => (bool)Configuration::get('PS_REWRITING_SETTINGS'),
				)
			);
		}
		$this->setTemplate('module:stfeature/views/templates/front/st_wishlist_view.tpl');
	}
	
	//DONGND:: add meta title, meta description, meta keywords
	public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();		
		
        $page['meta']['title'] = Configuration::get('PS_SHOP_NAME').' - '.$this->l('View Wishlist', 'viewwishlist');
		$page['meta']['keywords'] = $this->l('view-wishlist', 'viewwishlist');
		$page['meta']['description'] = $this->l('view Wishlist', 'viewwishlist');
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
            'title' => $this->l('My Account', 'viewwishlist'),
            'url' => $this->context->link->getPageLink('my-account', true),
        ];
		
        $breadcrumb['links'][] = [
            'title' => $this->l('My Wishlist', 'viewwishlist'),
            'url' => $this->context->link->getModuleLink('stfeature', 'mywishlist'),
        ];

        return $breadcrumb;
    }
	
	//DONGND:: get layout
	public function getLayout()
    {
        $entity = 'module-stfeature-'.$this->php_self;
		
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
