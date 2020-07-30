<?php
/**
 * GcInvoiceExport
 *
 * @author    Grégory Chartier <hello@gregorychartier.fr>
 * @copyright 2019 Grégory Chartier (https://www.gregorychartier.fr)
 * @license   Commercial license see license.txt
 * @category  Prestashop
 * @category  Module
 */

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
if (strcmp(Tools::getValue('token'), Tools::encrypt('gcinvoiceexport')) != 0) {
    die('Error token');
}
if (version_compare(_PS_VERSION_, '1.7', '<')) {
    $data = (Tools::jsonDecode(Tools::getValue('data'), true));
} else {
    $data = json_decode(Tools::getValue('data', null), true);
}
$sql_truncate = 'TRUNCATE TABLE `'._DB_PREFIX_.'gcinvoiceexport`;';
Db::getInstance()->execute($sql_truncate);
$sql = 'INSERT INTO `'._DB_PREFIX_.'gcinvoiceexport` ( `field` , `position`) VALUES ';
$i = 1;
foreach ($data['data'] as $d) {
    $sql .= '("'.pSQL($d['name']).'",'.(int)$d['position'].')';
    if ($i < count($data['data'])) {
        $sql .= ',';
    }
    $i++;
}
$sql .= ';';
Db::getInstance()->execute($sql);
echo '1';
