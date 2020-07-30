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
 * Styling variable controller.
 */

class AdminVariableController extends BaseTouchizeController
{

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->bootstrap = true;

        $this->table = 'touchize_variables';
        $this->identifier = 'id_variable';
        $this->className = 'TouchizeVariable';

        $this->lang = false;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
            ),
        );

        $this->fields_list = array(
            'id_variable' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 50,
                'filter_key' => 'b!name',
            ),
            'desc' => array(
                'title' => $this->l('Description'),
                'width' => '300',
                'filter_key' => 'b!desc',
            ),
            'value' => array(
                'title' => $this->l('Value'),
                'width' => 50,
                'filter_key' => 'b!value',
            )
        );

        parent::__construct();
    }

    /**
     * AdminController::renderForm() override
     *
     * @see AdminController::renderForm()
     */
    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        if (Validate::isLoadedObject($this->object)) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }

        $this->initToolbar();

        $this->fields_form = array(
            'tinymce' => false,
            'legend' => array(
                'title' => $this->l('Variable'),
                'icon' => 'icon-image',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name:'),
                    'name' => 'name',
                    'id' => 'name',
                    'lang' => false,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:').' <>;=#{}',
                    'size' => 40,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Value:'),
                    'name' => 'value',
                    'id' => 'value',
                    'lang' => false,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:').' <>;=#{}',
                    'size' => 40,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Is Color?'),
                    'name' => 'is_color',
                    'required' => true,
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'is_color_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'is_color_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),

            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                'save_and_preview' => array(
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'title' => $this->l('Save and stay'),
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );

        return parent::renderForm();
    }

    /**
     * AdminController::initPageHeaderToolbar() override
     *
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_variable'] = array(
                'href' => self::$currentIndex
                        .'&addtouchize_variables&token='
                        .$this->token,
                'desc' => $this->l('Add new Variable', null, null, false),
                'icon' => 'process-icon-new',
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * AdminController::initProcess() override
     *
     * @see AdminController::initProcess()
     */
    public function initProcess()
    {
        parent::initProcess();
    }
}
