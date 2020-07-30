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

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

include_once(dirname(__FILE__).'/stfeature.php');
include_once(dirname(__FILE__).'/classes/StfeatureProduct.php');

$module = new stfeature();

if(Tools::getValue('action') == 'render-modal')
{
	$result = $module->renderModal();
	
	die($result);
};

if(Tools::getValue('action') == 'get-attribute-data')
{
	$result = array();
	$context = Context::getContext();
	$id_product = Tools::getValue('id_product');
	$id_product_attribute = Tools::getValue('id_product_attribute');
	
	$attribute_data = new StfeatureProduct();
	$result = $attribute_data->getTemplateVarProduct2($id_product, $id_product_attribute);
	// echo '<pre>';
	// print_r($result);
	// echo '<pre>';
	// die();
	die(Tools::jsonEncode([
		'product_cover' => $result['cover'],
		'price_attribute'   => $module->renderPriceAttribute($result),
		'product_url' => $context->link->getProductLink(
			$id_product,
			null,
			null,
			null,
			$context->language->id,
			null,
			$id_product_attribute,
			false,
			false,
			true
		),
	]));
};

if(Tools::getValue('action') == 'get-new-review')
{
	// $result = array();
	if (Configuration::get('STFEATURE_PRODUCT_REVIEWS_MODERATE')) {
		$reviews = ProductReview::getByValidate(0, false);
	}
	else
	{
		$reviews = array();
	}
	
	die(Tools::jsonEncode([
		'number_review' => count($reviews)
	]));
}

