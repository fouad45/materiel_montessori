<?php
/**
 * 2007-2017 Sttheme
 *
 * NOTICE OF LICENSE
 *
 * St feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
 *
 * DISCLAIMER
 *
 *  @Module Name: St Feature
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2017 Sttheme
 *  @license   http://leotheme.com - prestashop template provider
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'leofeature` (
    `id_leofeature` int(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY  (`id_leofeature`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
