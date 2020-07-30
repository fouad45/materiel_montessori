<?php
/**
* pdfquotation Ajax Call
*
* @author    Empty
* @copyright 2007-2016 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

include_once(dirname(__FILE__).'/pdfquotation.php');
$context = Context::getContext();
$pdfQuotation = new PDFQuotation();
echo $pdfQuotation->hookAjaxCall(array('context' => $context));
?>