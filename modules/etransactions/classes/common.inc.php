<?php
/**
* E-Transactions PrestaShop Module
*
* Feel free to contact E-Transactions at support@e-transactions.fr for any
* question.
*
* LICENSE: This source file is subject to the version 3.0 of the Open
* Software License (OSL-3.0) that is available through the world-wide-web
* at the following URI: http://opensource.org/licenses/OSL-3.0. If
* you did not receive a copy of the OSL-3.0 license and are unable
* to obtain it through the web, please send a note to
* support@e-transactions.fr so we can mail you a copy immediately.
*
*  @category  Module / payments_gateways
*  @version   3.0.8
*  @author    E-Transactions <support@e-transactions.fr>
*  @copyright 2012-2016 E-Transactions
*  @license   http://opensource.org/licenses/OSL-3.0
*  @link      http://www.e-transactions.fr/
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Base includes with override management
 */
$dir = dirname(__FILE__).'/';
if (!defined('_PS_OVERRIDE_DIR_')) {
    $overrideDir = _PS_ROOT_DIR_.'/override/';
} else {
    $overrideDir = _PS_OVERRIDE_DIR_;
}
$overrideDir .= 'modules/etransactions/classes/';

$classes = array(
    'ETransactionsAbstract',
    'ETransactionsConfig',
    'ETransactionsController',
    'ETransactionsCurl',
    'ETransactionsEncrypt',
    'ETransactionsHelper',
    'ETransactionsInstaller',
    'ETransactionsKwixo',
    'ETransactionsDb',
);

foreach ($classes as $class) {
    if (file_exists($overrideDir.$class.'.php')) {
        require_once($overrideDir.$class.'.php');
    } else {
        require_once($dir.$class.'.php');
    }
}
