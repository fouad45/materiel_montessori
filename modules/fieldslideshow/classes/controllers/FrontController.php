<?php

class fieldslideshowModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
	    parent::initContent();
	    $this->context->smarty->assign('HOOK_FIELDSLIDESHOW', Hook::exec('fieldSlideShow'));
        }
}