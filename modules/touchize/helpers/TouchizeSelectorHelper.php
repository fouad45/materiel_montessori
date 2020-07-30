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
 * Page helper.
 */

class TouchizeSelectorHelper extends TouchizeBaseHelper
{
    /**
     * [getSelectors description]
     *
     * @param  mixed $pageId
     *
     * @return array
     */
    public function getSelectors()
    {
        $botab = false;
        if (strpos($_SERVER['HTTP_REFERER'], 'botab=1') !== false) {
            $botab = true;
        } else {
            $botab = false;
        }
        $response = array(
            //If stores should be selectable in menu enable below
            'Stores' => null, //$this->getAllStores($botab),
            'Currencies' => $this->getCurrencies($botab),
            'Languages' => $this->getLanguages($botab),
            'BackButtonTitle' => $this->l('Back')
        );
        return $response;
    }
    protected function getAllStores($botab = false)
    {
        $data = array();
        $shops = Shop::getShops(true);
        if (count($shops) <= 1) {
            return $data;
        }
        $currentShopId = (int)Context::getContext()->shop->id;
        foreach ($shops as $shop) {
            $shopInstance = new Shop($shop['id_shop']);
            $switchUrl =  $shopInstance->getBaseURL(true);
            if ($botab) {
                if (strpos($switchUrl, '?') !== false) {
                    $switchUrl .= '&botab=1';
                } else {
                    $switchUrl .= '?botab=1';
                }
            }
            $data[] = array(
                'Url' => $switchUrl,
                'Name' => $shop['name'],
                'Selected' => (((int)$shop['id_shop']) == $currentShopId ? true : false)
            );
        }
        return $data;
    }

    protected function getCurrencies($botab = false)
    {
        if ($botab) {
            return null;
        }
        $data = array();
        $currencies = Currency::getCurrencies();
        if (count($currencies) <= 1) {
            return $data;
        }
        $currentCurrency = $this->context->currency;
        $currentCurrencyId = null;
        if (is_array($currentCurrency)) {
            $currentCurrencyId = $currentCurrency['id'];
        } elseif (is_object($currentCurrency)) {
            $currentCurrencyId = $currentCurrency->id;
        } else {
            return $data;
        }
        foreach ($currencies as $currency) {
            $switchUrl =  $this->context->link->getModuleLink(
                'touchize',
                'selector',
                array('id_currency' => $currency['id_currency'])
            );
            if ($botab) {
                if (strpos($switchUrl, '?') !== false) {
                    $switchUrl .= '&botab=1';
                } else {
                    $switchUrl .= '?botab=1';
                }
            }
            $data[] = array(
                'Url' => $switchUrl,
                'Name' => $currency['name'],
                'Selected' => (((int)$currency['id_currency']) == ((int)$currentCurrencyId) ? true : false),
                'ISOCode' => $currency['iso_code']
            );
        }
        return $data;
    }

    protected function getLanguages($botab = false)
    {
        $data = array();
        $languages = Language::getLanguages(true, $this->context->shop->id);
        if (count($languages) <= 1) {
            return $data;
        }
        foreach ($languages as $language) {
            $switchUrl = $this->context->link->getPageLink('index', true, $language['id_lang']);
            if ($botab) {
                if (strpos($switchUrl, '?') !== false) {
                    $switchUrl .= '&botab=1';
                } else {
                    $switchUrl .= '?botab=1';
                }
            }
            $data[] = array(
                'Url' => $switchUrl,
                'Name' => $language['name'],
                'Selected' => ($language['id_lang'] == $this->context->language->id ? true : false),
                'ISOCode' => $language['iso_code']
            );
        }
        return $data;
    }
}
