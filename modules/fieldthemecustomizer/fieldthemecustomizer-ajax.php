<?php

/*-------------------------------------------------------------*/
/*  Fieldtheme Theme Ajax Requests Handler
/*-------------------------------------------------------------*/

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('fieldthemecustomizer.php');

$context = Context::getContext();
$Fieldtheme = new FieldThemeCustomizer();

$action = Tools::getValue('action');

switch ($action) {
    case 'changeProductListViewType':
        $viewType = Tools::getValue('viewType');
        $context->cookie->category_view_type = $viewType;
        $context->cookie->write();
        die(Tools::jsonEncode($context->cookie->category_view_type));

    default:
        break;
}