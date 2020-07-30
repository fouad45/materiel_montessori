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
 * Controller helper.
 */

class TouchizeControllerHelper
{
    /**
     * @var string
     **/
    public $context;

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->context = Context::getContext();
    }

    /**
     * [getParam description]
     *
     * @param  string $name
     *
     * @return string
     */
    public static function getParam($name, $default = false)
    {
        $value = Tools::getValue($name, $default);
        # Not in GET or POST(form), check POST(json)
        if (null == $value &&
            'POST' == $_SERVER['REQUEST_METHOD']
        ) {
            $jsonData = json_decode(Tools::file_get_contents('php://input'), true);
            if ($jsonData && array_key_exists($name, $jsonData)) {
                $value = $jsonData[$name];
            }
        }

        return $value;
    }

    /**
     * [getRelativeURL description]
     *
     * @param  string $absoluteUrl
     *
     * @return string
     */
    public static function getRelativeURL($absoluteUrl)
    {
        return str_replace(_PS_BASE_URL_SSL_, '', $absoluteUrl);
    }
}
