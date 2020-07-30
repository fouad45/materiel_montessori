<?php
/**
 * 2007-2017 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/* SSL Management */
$useSSL = true;

include dirname(__FILE__).'/../../config/config.inc.php';

if (version_compare(_PS_VERSION_, '1.5', '>')) {
    $context = Context::getContext();
    // Before 1.5.1 Context link doesn't exists
    if (version_compare(_PS_VERSION_, '1.5.1', '<')) {
        $context->link = new Link();
    }

    if (!defined('_PS_BASE_URL_')) {
        define('_PS_BASE_URL_', '');
    }
    if (!defined('_PS_BASE_URL_SSL_')) {
        define('_PS_BASE_URL_SSL_', '');
    }

    Tools::redirect($context->link->getModuleLink('totadministrativemandate', 'payment'));
}

include dirname(__FILE__).'/../../header.php';

$module = Module::getInstanceByName('totadministrativemandate');

if (!$module->context->cookie->isLogged(true)) {
    Tools::redirect('authentication.php?back=order.php');
}

echo $module->execPayment($module->context->cart);

include_once dirname(__FILE__).'/../../footer.php';
