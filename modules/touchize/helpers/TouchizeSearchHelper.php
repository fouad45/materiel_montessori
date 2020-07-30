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

/**
 * Search helper.
 */

class TouchizeSearchHelper extends TouchizeBaseHelper
{
    /**
     * [getSearch description]
     *
     * @param  string  $query
     * @param  int     $n
     *
     * @return array
     */
    public function getSearch($query, $n = 999)
    {
        $orderby = 'position';
        $orderway = 'desc';

        $helper = new TouchizeProductHelper();
        $search = Search::find(
            $this->context->language->id,
            $query,
            1,
            $n,
            $orderby,
            $orderway
        );
        $products = array();
        $count = 0;

        if (array_key_exists('result', $search)) {
            foreach ($search['result'] as $product) {
                array_push(
                    $products,
                    $helper->getProduct($product['id_product'], false)
                );
                $count++;
            }
        }

        $result = array(
            'Count' => $count,
            'List' => $products,
            'SQ' => 'q',
            'Title' => ($count > 0)
                ? sprintf(
                    $this->l('Search results for %s'),
                    $query
                )
                : sprintf(
                    $this->l('No results were found for your search %s'),
                    $query
                )
        );

        # Build main response
        $searchResult = array(
            'SearchResult' => array(
                'Result' => $result,
                'Sorting' => null,
                'Filters' => null,
                'Url' => TouchizeControllerHelper::getRelativeURL(
                    $this->context->link->getPageLink('search')
                    .'?search_query='
                    .$query
                ),
            ),
        );
        
        return $searchResult;
    }
}
