<?php
class AdminAboutUsController extends ModuleAdminController {

    public $asso_type = 'shop';
    public function __construct() {
        $this->module = 'smartblog';
        $this->lang = true;
        $this->context = Context::getContext();
        $this->bootstrap = true;

        parent::__construct();
    }

    public function ajaxProcessGetSmartThemes()
    {

    }

    public function initContent()
    {}
}
