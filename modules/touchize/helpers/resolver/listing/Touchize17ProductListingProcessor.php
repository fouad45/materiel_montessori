<?php
/**
 * 2018 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 */

use PrestaShop\PrestaShop\Core\Filter\CollectionFilter;
use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\Pagination;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\Facet;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\FacetsRendererInterface;

class Touchize17ProductListingProcessor extends TouchizeBaseHelper
//class Touchize17ProductListingProcessor extends ProductListingFrontControllerCore
{
    public function getProducts($categoryId, $pageSize, $page)
    {
        $this->category = new Category(
            $categoryId,
            $this->context->language->id
        );
        //Since Ps_Facetedsearch reads the category id from the SERVER VARS!
        $_GET['id_category'] = $categoryId;

        // Code from ProductListingFrontControllerCore::getProductSearchVariables
        $context = $this->getProductSearchContext();
        $query = $this->getProductSearchQuery();
        $query
            ->setResultsPerPage($pageSize)
            ->setPage($page)
        ;

        $provider = $this->getProductSearchProviderFromModules($query);
        if (null === $provider) {
            $provider = $this->getDefaultProductSearchProvider();
        }

        $result = $provider->runQuery(
            $context,
            $query
        );
        return $result->getProducts();
    }

    public function getTotalItems($categoryId)
    {
        $this->category = new Category(
            $categoryId,
            $this->context->language->id
        );
        //Since Ps_Facetedsearch reads the category id from the SERVER VARS!
        $_GET['id_category'] = $categoryId;

        // Code from ProductListingFrontControllerCore::getProductSearchVariables
        $context = $this->getProductSearchContext();
        $query = $this->getProductSearchQuery();
        $provider = $this->getProductSearchProviderFromModules($query);
        if (null === $provider) {
            $provider = $this->getDefaultProductSearchProvider();
        }
        $result = $provider->runQuery(
            $context,
            $query
        );
        return $result->getTotalProductsCount();
    }

    protected function getProductSearchContext()
    {
        return (new ProductSearchContext())
            ->setIdShop($this->context->shop->id)
            ->setIdLang($this->context->language->id)
            ->setIdCurrency($this->context->currency->id)
            ->setIdCustomer(
                $this->context->customer ?
                    $this->context->customer->id :
                    null
            )
        ;
    }
    private function getProductSearchProviderFromModules($query)
    {
        $providers = Hook::exec(
            'productSearchProvider',
            array('query' => $query),
            null,
            true
        );

        if (!is_array($providers)) {
            $providers = array();
        }

        foreach ($providers as $provider) {
            if ($provider instanceof ProductSearchProviderInterface) {
                return $provider;
            }
        }
    }

    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setIdCategory($this->category->id)
            ->setSortOrder(new SortOrder('product', Tools::getProductsOrder('by'), Tools::getProductsOrder('way')))
            // ->setResultsPerPage(30)
            // ->setPage(1)
            // ->setSortOrder(new SortOrder('product', 'date_add', 'desc'))
        ;

        return $query;
    }

    protected function getDefaultProductSearchProvider()
    {
        return new CategoryProductSearchProvider(
            $this->getTranslator(),
            $this->category
        );
    }

    public function getListingLabel()
    {
        return $this->trans(
            'New products',
            array(),
            'Shop.Theme.Catalog'
        );
    }
}
