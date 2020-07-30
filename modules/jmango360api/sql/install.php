<?php
/**
 * @author JMango360 Operations BV
 * @copyright 2019 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'jmango360_user` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_user` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'jmango360_order` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_cart` int(10) unsigned NOT NULL,
    `id_order` int(10) unsigned NULL DEFAULT NULL,
    `mobile` tinyint(1) unsigned NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

$checkHookAliasSql = 'SELECT * FROM `' . _DB_PREFIX_ . 'hook_alias` WHERE name="actionAdminOrderPreferencesControllerUpdate_optionsBefore" AND alias="actionAdminOrderPreferencesControllerUpdateOptionsBefore";';
if (!Db::getInstance()->getValue($checkHookAliasSql)) {
    $insertHookAliasSql = 'INSERT INTO `' . _DB_PREFIX_ . 'hook_alias` (`name`, `alias`) VALUES ("actionAdminOrderPreferencesControllerUpdate_optionsBefore", "actionAdminOrderPreferencesControllerUpdateOptionsBefore");';
    Db::getInstance()->execute($insertHookAliasSql);
}
