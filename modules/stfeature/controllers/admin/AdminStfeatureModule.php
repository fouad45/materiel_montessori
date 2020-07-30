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

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class AdminstfeatureModuleController extends ModuleAdminControllerCore
{

    public function __construct()
    {
        parent::__construct();
		
		$url = 'index.php?controller=AdminModules&configure=stfeature&token='.Tools::getAdminTokenLite('AdminModules');
        Tools::redirectAdmin($url);
    }
}
