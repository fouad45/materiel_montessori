<?php

class AdminFieldBrandSliderController extends ModuleAdminController {

    public function __construct()
    {
        parent::__construct();
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules').'&configure=fieldbrandslider');
    }
}
