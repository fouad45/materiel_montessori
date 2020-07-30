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
*  @version   3.0.10
*  @author    E-Transactions <support@e-transactions.fr>
*  @copyright 2012-2016 E-Transactions
*  @license   http://opensource.org/licenses/OSL-3.0
*  @link      http://www.e-transactions.fr/
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_0_10($object)
{
    if (version_compare(_PS_VERSION_, '1.5', '>=')) {
        $sql = array();
        $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'etransactions_cart_locker` CHANGE `id_transaction` `id_transaction` VARCHAR(20) NOT NULL';
        $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'etransactions_cart_locker` DROP PRIMARY KEY';
        $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'etransactions_cart_locker` ADD PRIMARY KEY (`id_cart`, `id_transaction`)';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
    }

    return true;
}
