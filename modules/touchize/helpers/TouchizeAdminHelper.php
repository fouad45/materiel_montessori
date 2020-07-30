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
 * Admin helper.
 */

class TouchizeAdminHelper extends TouchizeBaseHelper
{
    /**
     * [assignMenuVars description]
     *
     * @return string
     */
    public function assignMenuVars()
    {
        $key = Configuration::get('TOUCHIZE_LICENSE_KEY');
        $installed = Configuration::get('TOUCHIZE_TRIAL_ACTIVE');
        $used = Configuration::get('TOUCHIZE_TRIAL_HAS_BEEN_ACTIVATED');
        //right aligned menus has to be entered in revese order due to css (float: right;)
        $this->context->smarty->assign(array(
            'items' => array(array(
                'text' => $this->l('Main Page'),
                'link' => $this->context->link->getAdminLink('AdminGetStarted'),
                'current' => $this->context->controller->controller_name == 'AdminGetStarted' ? true : false,
                'right' => false,
                'license' => false,
                'blank' => false,
                'hide' => false
            ),
            array(
                'text' => $this->l('Setup Menus'),
                'link' => $this->context->link->getAdminLink('AdminMenuBuilder'),
                'current' => $this->context->controller->controller_name == 'AdminMenuBuilder' ? true : false,
                'right' => false,
                'license' => false,
                'blank' => false,
                'hide' => false
            ),
            array(
                'text' => (!$key) ? $this->l('Manage Subscription') : $this->l('Manage Subscription'),
                'link' => $this->context->link->getAdminLink('AdminLicense'),
                'current' => $this->context->controller->controller_name == 'AdminLicense' ? true : false,
                'right' => false,
                'license' => true,
                'blank' => false,
                'hide' => ($installed || $used) ? false : true,
            ),
            array(
                'text' => $this->l('Banners'),
                'link' => $this->context->link->getAdminLink('AdminTouchmaps'),
                'current' => $this->context->controller->controller_name == 'AdminTouchmaps' ? true : false,
                'right' => true,
                'license' => false,
                'blank' => false,
                'hide' => false
            ),
            array(
                'text' => $this->l('Settings'),
                'link' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&config_tab=1',
                'current' => ($this->context->controller->controller_name == 'AdminSettings' ||
                    $this->context->controller->controller_name == 'AdminModules') ? true : false,
                'right' => true,
                'license' => false,
                'blank' => false,
                'hide' => false
            ),
            array(
                'text' => $this->l('Customize Theme'),
                'link' => 'https://themecreator.touchize.com/?lang='.$this->context->language->iso_code,
                'current' => $this->context->controller->controller_name == 'AdminWizard' ? true : false,
                'right' => false,
                'license' => false,
                'blank' => true,
                'hide' => false
            ),
            array(
                'text' => $this->l('Pricing'),
                'link' => 'https://subscription.touchize.com/prestashop?lang='.$this->context->language->iso_code,
                'blank' => true,
                'hide' => false,
                'current' => false,
                'right' => true,
                'license' => false
            ),
            array(
                'text' => $this->l('Contact Us'),
                'link' => $this->context->link->getAdminLink('AdminContactUs'),
                'current' => $this->context->controller->controller_name == 'AdminContactUs' ? true : false,
                'right' => true,
                'license' => false,
                'blank' => false,
                'hide' => false
            ))
        ));
    }
    /**
     * [getTemplate description]
     *
     * @return string
     */
    public function getTemplate($tplName)
    {
        if ($this->getTemplatePath($tplName)) {
            return $this->context->smarty->createTemplate(
                $this->getTemplatePath($tplName),
                $this->context->smarty
            )->fetch();
        }

        return '';
    }

    /**
     * To return the path to the folder with admin templates.
     *
     * @return string
     */
    public function getTemplatePath($tplName)
    {
        return parent::getTemplatePath('views/templates/admin/'.$tplName);
    }
}
