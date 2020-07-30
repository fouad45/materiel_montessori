<?php
/**
 * 2018 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 */

/**
 * License controller.
 */

class BaseTouchizeController extends ModuleAdminController
{
    const INFO_TEMPLATE = 'info/empty.tpl';

    /**
     * @param string $tplName
     *
     * @return object|Smarty_Internal_Template
     */
    public function createTemplate($tplName)
    {
        if (file_exists($this->getTemplatePath().$tplName) &&
            $this->viewAccess()
        ) {
            return $this->context->smarty->createTemplate(
                $this->getTemplatePath().$tplName,
                $this->context->smarty
            );
        }

        return parent::createTemplate($tplName);
    }

    /**
     * To return the path to the folder with admin templates.
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/';
    }

    /**
     * @return string
     */
    public function getInfoTemplate()
    {
        return static::INFO_TEMPLATE;
    }

    /**
     * AdminController::initContent() override
     *
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign(array(
            'is_multishop_mode' => false
        ));
        if (!$this->isSingleShop()) {
            $this->context->smarty->assign(array(
                'content' => $this->createTemplate('info/multistorewarning.tpl')->fetch(),
                'is_multishop_mode' => true
            ));
            return ;
        }
    }


    /**
     * @param null $value
     * @param null $controller
     * @param null $method
     */
    protected function ajaxDie($value = null, $controller = null, $method = null)
    {
        if (is_callable('parent::ajaxDie')) {
            parent::ajaxDie($value, $controller, $method);
        } else {
            die($value);
        }
    }

    public function isSingleShop()
    {
        $shops = Shop::getContextListShopID();
        if (count($shops) > 1) {
            return false;
        }
        return true;
    }
}
