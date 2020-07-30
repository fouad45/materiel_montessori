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

if (!defined('_PS_VERSION_')) {
    exit;
}

class GcInvoiceExport extends Module
{
    public $output;

    public function __construct()
    {
        $this->name                          = 'gcinvoiceexport';
        $this->tab                           = 'export';
        $this->version                       = '3.1.0';
        $this->bootstrap                     = true;
        $this->display                       = 'view';
        $this->ps_versions_compliancy['min'] = '1.5.0.0';
        $this->author_address                = '0x5cD3FdcEF023E7ebeAb44aA7140c992f668973eB';
        $this->module_key                    = 'ba2d5357dd6eb197edfae64a404da1b8';
        $this->need_instance                 = 0;
        $this->author                        = 'Grégory Chartier';
        $this->is_eu_compatible              = 1;

        parent::__construct();

        $this->displayName = $this->l('Invoice - Credit slip Export (CRON / FTP / EMAIL)');
        $this->description = $this->l('Export invoices and credit slips into CSV and automatically send them by email and/or on FTP server');
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHook('backOfficeHeader') || !$this->installDB()) {
            return false;
        }

        if (!Configuration::updateValue('GCIE', '')
            || !Configuration::updateValue('GCIE_START', date('Y-m-d') . ' 00:00:00')
            || !Configuration::updateValue('GCIE_END', date('Y-m-d') . ' 23:59:59')
            || !Configuration::updateValue('GCIE_TAXESDETAILS', '')
            || !Configuration::updateValue('GCIE_ADDRESSDETAILS', '')
            || !Configuration::updateValue('GCIE_HEADER', '')
            || !Configuration::updateValue('GCIE_ROW_TOTAL_FOOTER', '')
            || !Configuration::updateValue('GCIE_CREDITSLIP', '')
            || !Configuration::updateValue('GCIE_DATEVALID', '')
            || !Configuration::updateValue('GCIE_STOPRATING', 0)
            || !Configuration::updateValue('GCIE_CSVDELIMITER', ';')
            || !Configuration::updateValue('GCIE_TOEMAIL', '')
            || !Configuration::updateValue('GCIE_CCEMAIL', '')
            || !Configuration::updateValue('GCIE_FILETYPE', 2)
            || !Configuration::updateValue('GCIE_MAILTEXT', '')
            || !Configuration::updateValue('GCIE_INVOICEAUTOEXPORTTOEMAIL', 0)
            || !Configuration::updateValue('GCIE_TOFTP', '')
            || !Configuration::updateValue('GCIE_TOFTPSERVER', '')
            || !Configuration::updateValue('GCIE_TOFTPUSERNAME', '')
            || !Configuration::updateValue('GCIE_TOFTPUSERPASSWORD', '')
            || !Configuration::updateValue('GCIE_AUTO_PERIOD', 1)
            || !Configuration::updateValue('GCIE_TOFTPFOLDER', '')) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->deleteTables()
            || !Configuration::deleteByName('GCIE')
            || !Configuration::deleteByName('GCIE_START')
            || !Configuration::deleteByName('GCIE_END')
            || !Configuration::deleteByName('GCIE_TAXESDETAILS')
            || !Configuration::deleteByName('GCIE_ADDRESSDETAILS')
            || !Configuration::deleteByName('GCIE_HEADER')
            || !Configuration::deleteByName('GCIE_ROW_TOTAL_FOOTER')
            || !Configuration::deleteByName('GCIE_CREDITSLIP')
            || !Configuration::deleteByName('GCIE_DATEVALID')
            || !Configuration::deleteByName('GCIE_STOPRATING')
            || !Configuration::deleteByName('GCIE_CSVDELIMITER')
            || !Configuration::deleteByName('GCIE_TOEMAIL')
            || !Configuration::deleteByName('GCIE_CCEMAIL')
            || !Configuration::deleteByName('GCIE_FILETYPE')
            || !Configuration::deleteByName('GCIE_MAILTEXT')
            || !Configuration::deleteByName('GCIE_INVOICEAUTOEXPORTTOEMAIL')
            || !Configuration::deleteByName('GCIE_TOFTP')
            || !Configuration::deleteByName('GCIE_TOFTPSERVER')
            || !Configuration::deleteByName('GCIE_TOFTPUSERNAME')
            || !Configuration::deleteByName('GCIE_TOFTPUSERPASSWORD')
            || !Configuration::deleteByName('GCIE_AUTO_PERIOD')
            || !Configuration::deleteByName('GCIE_TOFTPFOLDER')) {
            return false;
        }

        return true;
    }

    public function installDB()
    {
        $res = (bool)Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gcinvoiceexport` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `field` varchar(255) NOT NULL,
                    `position` int(10) unsigned NOT NULL DEFAULT \'0\',
                    PRIMARY KEY (`id`)) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;');

        $res &= Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'gcinvoiceexport` ( `field` , `position`) VALUES ("number", 1),
            ("order_reference", 2), ("order_status", 3), ("date_add", 4), ("customer_name", 5), ("payment", 6), ("currency", 7), ("id_cart", 8),
            ("total_products", 9), ("total_products_wt", 10), ("total_discount_tax_excl", 11),
            ("total_discount_tax_incl", 12), ("total_shipping_tax_excl", 13),
            ("total_shipping_tax_incl", 14), ("total_wrapping_tax_excl", 15), ("total_wrapping_tax_incl", 16),
            ("total_paid_tax_excl", 17), ("total_paid_tax_incl", 18);');

        return $res;
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'gcinvoiceexport`;');
    }

    public function hookbackOfficeHeader()
    {
        if ((Tools::getValue('module_name') == $this->name) || (Tools::getValue('configure') == $this->name)) {
            $this->context->controller->addJquery();
            $this->context->controller->addJqueryUi('ui.datepicker');
            $this->context->controller->addJqueryUi('ui.sortable');
            $this->context->controller->addJS($this->_path . 'views/js/gcinvoiceexport.js');
        }
    }

    public function getContent()
    {
        $output = $this->postProcess();
        $output .= $this->renderForm();
        $output .= $this->getFieldsOrder();

        return $output;
    }

    public function getFieldsOrder()
    {
        $sql               = 'SELECT * FROM `' . _DB_PREFIX_ . 'gcinvoiceexport` ORDER BY id ASC';
        $arrValuesForOrder = array();
        if ($res = Db::getInstance()->ExecuteS($sql)) {
            foreach ($res as $row) {
                $arrValuesForOrder[] = $row['position'];
            }
        }

        $this->context->smarty->assign(
            array(
                'gpath'             => Tools::getShopDomainSsl(true) . __PS_BASE_URI__,
                'arrValuesForOrder' => $arrValuesForOrder,
            )
        );

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/field_sort.tpl');

        return $output;
    }

    public function getRating()
    {
        $stop_rating = (int)Configuration::get('GCIE_STOPRATING');

        if ($stop_rating != 1) {
            return $this->display(__FILE__, 'views/templates/admin/rating.tpl');
        }
    }

    public function renderForm()
    {
        $this->context->smarty->assign(
            array(
                'ps15'        => version_compare(_PS_VERSION_, '1.6', '<'),
                'myToken'     => Tools::encrypt($this->name),
                'rating'      => $this->getRating(),
                'settings'    => $this->renderColumnSettings(),
                'filter'      => $this->renderFilterSettings(),
                'export'      => $this->renderFormExport(),
                'auto_export' => $this->renderAutoExportSettings(),
            )
        );

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
    }

    public function renderColumnSettings()
    {
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $type = 'radio';
        } else {
            $type = 'switch';
        }
        $exp_fields  = $this->getExportFields();
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Content'),
                    'icon'  => 'icon-cogs'
                ),
                'input'  => array(
                    array(
                        'type'   => 'checkbox',
                        'name'   => 'gcinvoiceexport',
                        'label'  => $this->l('Fields to export'),
                        'values' => array(
                            'query' => $exp_fields,
                            'id'    => 'id',
                            'name'  => 'name'
                        )
                    ),
                    array(
                        'type'    => $type,
                        'name'    => 'invoice_header',
                        'label'   => $this->l('Header with columns name'),
                        'is_bool' => true,
                        'class'   => 't',
                        'values'  => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type'    => $type,
                        'name'    => 'row_total_footer',
                        'label'   => $this->l('Total'),
                        'is_bool' => true,
                        'class'   => 't',
                        'values'  => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type'    => $type,
                        'name'    => 'invoice_datevalid',
                        'label'   => $this->l('Payment accept date'),
                        'is_bool' => true,
                        'class'   => 't',
                        'values'  => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type'    => $type,
                        'name'    => 'invoice_taxesdetails',
                        'label'   => $this->l('Details tax'),
                        'is_bool' => true,
                        'class'   => 't',
                        'values'  => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type'     => $type,
                        'label'    => $this->l('Eco tax'),
                        'name'     => 'GCIE_ECOTAX',
                        'required' => false,
                        'is_bool'  => true,
                        'class'    => 't',
                        'values'   => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type'     => $type,
                        'label'    => $this->l('Invoice address'),
                        'name'     => 'invoice_addressdetails',
                        'required' => false,
                        'is_bool'  => true,
                        'class'    => 't',
                        'values'   => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type'   => 'checkbox',
                        'name'   => 'GCIE_FIELDSADDRESS',
                        'label'  => $this->l('Invoice Address Fields to export'),
                        'values' => array(
                            'query' => $this->getAddressFields(),
                            'id'    => 'id',
                            'name'  => 'name'
                        )
                    ),
                    array(
                        'type'     => $type,
                        'label'    => $this->l('Delivery address'),
                        'name'     => 'GCIE_DELIVERYADDRESS',
                        'required' => false,
                        'is_bool'  => true,
                        'class'    => 't',
                        'values'   => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type'   => 'checkbox',
                        'name'   => 'GCIE_FIELDSDLVADDRESS',
                        'label'  => $this->l('Delivery address Fields to export'),
                        'values' => array(
                            'query' => $this->getAddressDvlFields(),
                            'id'    => 'id',
                            'name'  => 'name'
                        )
                    ),
                    array(
                        'type'     => 'radio',
                        'label'    => $this->l('Delimiter'),
                        'name'     => 'GCIE_CSVDELIMITER',
                        'required' => true,
                        'class'    => 't',
                        'values'   => array(
                            array(
                                'id'    => 'deli_comma',
                                'value' => ',',
                                'label' => $this->l('Comma') . '(,)'
                            ),
                            array(
                                'id'    => 'deli_semicolon',
                                'value' => ';',
                                'label' => $this->l('Semicolon') . '(;)'
                            ),
                            array(
                                'id'    => 'deli_dot',
                                'value' => '.',
                                'label' => $this->l('Dot') . '(.)'
                            )
                        ),
                    ),
                    array(
                        'type'  => 'textarea',
                        'label' => $this->l('Custom fields'),
                        'name'  => 'GCIE_CUSTOMTABLECOLUM',
                        'lang'  => false,
                        'cols'  => 40,
                        'rows'  => 10,
                        'desc'  => $this->l('One line per field') . '<br>' .
                                   $this->l('Format: [table_name].[column_name].[position].[label]') . '<br>' .
                                   $this->l('Example: order.reference.3.Order reference')
                    ),
                    array(
                        'type'    => $type,
                        'name'    => 'invoice_creditslip',
                        'desc'    => $this->l('Generate CSV for credit slips too'),
                        'label'   => $this->l('Credit slip'),
                        'is_bool' => true,
                        'class'   => 't',
                        'values'  => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id'    => 'active_on',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                ),
                'submit' => array(
                    'name'  => 'submitGcInvoiceExportColumn',
                    'title' => $this->l('Save'),
                )
            )
        );

        $helper                           = new HelperForm();
        $helper->title                    = $this->l('Invoice Export Configuration');
        $helper->show_toolbar             = true;
        $helper->table                    = $this->table;
        $lang                             = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language    = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form                = array();
        $helper->toolbar_btn              = array(
            'save' => array('href' => '#', 'desc' => $this->l('Save'))
        );

        $helper->identifier    = $this->identifier;
        $helper->submit_action = 'submitGcInvoiceExportColumn';
        $helper->currentIndex  = $this->context->link->getAdminLink('AdminModules', false)
                                 . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token         = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars      = array(
            'languages'   => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        $helper->fields_value['invoice_header']         = (Configuration::get('GCIE_HEADER')) ? 1 : 0;
        $helper->fields_value['row_total_footer']       = (Configuration::get('GCIE_ROW_TOTAL_FOOTER')) ? 1 : 0;
        $helper->fields_value['invoice_creditslip']     = (Configuration::get('GCIE_CREDITSLIP')) ? 1 : 0;
        $helper->fields_value['invoice_taxesdetails']   = (Configuration::get('GCIE_TAXESDETAILS')) ? 1 : 0;
        $helper->fields_value['invoice_addressdetails'] = (Configuration::get('GCIE_ADDRESSDETAILS')) ? 1 : 0;
        $helper->fields_value['invoice_datevalid']      = (Configuration::get('GCIE_DATEVALID')) ? 1 : 0;
        $helper->fields_value['GCIE_CUSTOMTABLECOLUM']  = Configuration::get('GCIE_CUSTOMTABLECOLUM');
        $helper->fields_value['GCIE_ECOTAX']            = Configuration::get('GCIE_ECOTAX');
        $helper->fields_value['GCIE_DELIVERYADDRESS']   = Configuration::get('GCIE_DELIVERYADDRESS');
        $helper->fields_value['GCIE_CSVDELIMITER']      = Configuration::get('GCIE_CSVDELIMITER', ';');

        $invoice_export_tab = (Configuration::get('GCIE')) ?
            (Tools::unserialize(Configuration::get('GCIE'))) : ('');
        if ($invoice_export_tab) {
            foreach ($invoice_export_tab as $invoice_export) {
                $helper->fields_value[$this->name . '_' . $invoice_export] = 1;
            }
        }

        $invoice_export_tab = (Configuration::get('GCIE_FIELDSADDRESS')) ?
            (Tools::unserialize(Configuration::get('GCIE_FIELDSADDRESS'))) : ('');
        if ($invoice_export_tab) {
            foreach ($invoice_export_tab as $invoice_export) {
                $helper->fields_value['GCIE_FIELDSADDRESS_' . $invoice_export] = 1;
            }
        }

        $dlv_export_tab = (Configuration::get('GCIE_FIELDSDLVADDRESS')) ?
            (Tools::unserialize(Configuration::get('GCIE_FIELDSDLVADDRESS'))) : ('');
        if ($dlv_export_tab) {
            foreach ($dlv_export_tab as $dlv_export) {
                $helper->fields_value['GCIE_FIELDSDLVADDRESS_' . $dlv_export] = 1;
            }
        }

        return $helper->generateForm(array($fields_form));
    }

    public function renderFilterSettings()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Filters'),
                    'icon'  => 'icon-cogs'
                ),
                'input'  => array(
                    array(
                        'type'   => 'checkbox',
                        'name'   => 'gcorderstatesexport',
                        'label'  => $this->l('Orders status to export'),
                        'values' => array(
                            'query' => $this->getOrderStatusFormated(),
                            'id'    => 'id',
                            'name'  => 'name'
                        )
                    ),
                    array(
                        'type'   => 'checkbox',
                        'name'   => 'gcordergroupssexport',
                        'label'  => $this->l('Customers groups to export'),
                        'values' => array(
                            'query' => $this->getGroupsFormated(),
                            'id'    => 'id',
                            'name'  => 'name'
                        )
                    ),
                    array(
                        'type'     => 'text',
                        'id'       => 'datepicker',
                        'label'    => $this->l('Start Date :'),
                        'name'     => 'invoice_start',
                        'required' => true,
                        'class'    => 'fixed-width-md'
                    ),
                    array(
                        'type'     => 'text',
                        'id'       => 'datepicker2',
                        'label'    => $this->l('End Date :'),
                        'name'     => 'invoice_end',
                        'required' => true,
                        'class'    => 'fixed-width-md'
                    ),
                ),
                'submit' => array(
                    'name'  => 'submitGcInvoiceExportFilters',
                    'title' => $this->l('Save'),
                )
            )
        );

        $helper                           = new HelperForm();
        $helper->title                    = $this->l('Invoice Export Configuration');
        $helper->show_toolbar             = true;
        $helper->table                    = $this->table;
        $lang                             = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language    = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form                = array();
        $helper->toolbar_btn              = array(
            'save' => array('href' => '#', 'desc' => $this->l('Save'))
        );

        $helper->identifier    = $this->identifier;
        $helper->submit_action = 'submitGcInvoiceExportFilters';
        $helper->currentIndex  = $this->context->link->getAdminLink('AdminModules', false)
                                 . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token         = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars      = array(
            'languages'   => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        $helper->fields_value['invoice_start']       = date("Y-m-d", strtotime(Configuration::get('GCIE_START')));
        $helper->fields_value['invoice_end']         = date("Y-m-d", strtotime(Configuration::get('GCIE_END')));
        $helper->fields_value['gcorderstatesexport'] = date("Y-m-d", strtotime(Configuration::get('GCIE_START')));

        $state_export_tab = (Configuration::get('GCIE_ORDERSTATESEXPORT')) ?
            (Tools::unserialize(Configuration::get('GCIE_ORDERSTATESEXPORT'))) : ('');
        if ($state_export_tab) {
            foreach ($state_export_tab as $id_state) {
                $helper->fields_value['gcorderstatesexport_' . (int)$id_state] = 1;
            }
        }

        $gcordergroupssexport = (Configuration::get('GCIE_ORDERGROUPSEXPORT')) ?
            (Tools::unserialize(Configuration::get('GCIE_ORDERGROUPSEXPORT'))) : ('');
        if ($gcordergroupssexport) {
            foreach ($gcordergroupssexport as $id_group) {
                $helper->fields_value['gcordergroupssexport_' . (int)$id_group] = 1;
            }
        }

        return $helper->generateForm(array($fields_form));
    }

    public function renderAutoExportSettings()
    {
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $type = 'radio';
        } else {
            $type = 'switch';
        }

        $html = '';

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Automation'),
                    'icon'  => 'icon-cogs'
                ),
                'input'  => array(
                    array(
                        'type'  => 'free',
                        'id'    => 'cron-url',
                        'label' => $this->l('Cron url'),
                        'name'  => 'GCIE_CRON_URL',
                    ),
                    array(
                        'type'     => $type,
                        'label'    => $this->l('Export to Email'),
                        'name'     => 'GCIE_INVOICEAUTOEXPORTTOEMAIL',
                        'required' => false,
                        'is_bool'  => true,
                        'class'    => 't',
                        'values'   => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type'     => 'text',
                        'prefix'   => '<i class="icon icon-envelope"></i>',
                        'label'    => $this->l('To email'),
                        'name'     => 'GCIE_TOEMAIL',
                        'required' => true,
                    ),
                    array(
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'label'  => $this->l('CC email'),
                        'name'   => 'GCIE_CCEMAIL',
                    ),
                    array(
                        'type'     => 'radio',
                        'label'    => $this->l('File to send'),
                        'name'     => 'GCIE_FILETYPE',
                        'required' => true,
                        'class'    => 't',
                        'values'   => array(
                            array(
                                'id'    => 'send_invoice',
                                'value' => '0',
                                'label' => $this->l('Invoice')
                            ),
                            array(
                                'id'    => 'send_credit_slip',
                                'value' => '1',
                                'label' => $this->l('Credit slip')
                            ),
                            array(
                                'id'    => 'send_both',
                                'value' => '2',
                                'label' => $this->l('Both')
                            )
                        ),
                    ),
                    array(
                        'type'         => 'textarea',
                        'label'        => $this->l('Custom mail text'),
                        'name'         => 'GCIE_MAILTEXT',
                        'autoload_rte' => true,
                        'cols'         => '60',
                        'rows'         => '6',
                    ),
                    array(
                        'type'     => $type,
                        'label'    => $this->l('Export to FTP'),
                        'name'     => 'GCIE_TOFTP',
                        'required' => false,
                        'is_bool'  => true,
                        'class'    => 't',
                        'values'   => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type'     => 'text',
                        'label'    => $this->l('Ftp Server'),
                        'name'     => 'GCIE_TOFTPSERVER',
                        'required' => true,
                    ),
                    array(
                        'prefix'   => '@',
                        'type'     => 'text',
                        'label'    => $this->l('Ftp User name'),
                        'name'     => 'GCIE_TOFTPUSERNAME',
                        'required' => true,
                    ),
                    array(
                        'prefix'   => '<i class="icon icon-key"></i>',
                        'type'     => 'text',
                        'name'     => 'GCIE_TOFTPUSERPASSWORD',
                        'label'    => $this->l('Ftp User Password'),
                        'required' => true,
                    ),
                    array(
                        'type'     => 'text',
                        'label'    => $this->l('Ftp folder'),
                        'name'     => 'GCIE_TOFTPFOLDER',
                        'required' => false,
                    ),
                    array(
                        'type'     => 'select',
                        'label'    => $this->l('Export data from'),
                        'name'     => 'GCIE_AUTO_PERIOD',
                        'class'    => '',
                        'required' => true,
                        'options'  => array(
                            'query' => array(
                                array(
                                    'id'   => 0,
                                    'name' => $this->l('Current month'),
                                ),
                                array(
                                    'id'   => 1,
                                    'name' => $this->l('Last 30 days'),
                                ),
                                array(
                                    'id'   => 2,
                                    'name' => $this->l('Last month'),
                                ),
                                array(
                                    'id'   => 3,
                                    'name' => $this->l('Current week'),
                                ),
                                array(
                                    'id'   => 4,
                                    'name' => $this->l('Last 7 days'),
                                ),
                                array(
                                    'id'   => 5,
                                    'name' => $this->l('Previous week'),
                                ),
                                array(
                                    'id'   => 6,
                                    'name' => $this->l('Last 90 days'),
                                ),
                            ),
                            'id'    => 'id',
                            'name'  => 'name',
                        ),
                    ),
                ),
                'submit' => array(
                    'name'  => 'submitInvoiceAutoExport',
                    'title' => $this->l('Save'),
                )
            )
        );

        $helper                           = new HelperForm();
        $helper->title                    = $this->l('Invoice Export Configuration');
        $helper->show_toolbar             = true;
        $helper->table                    = $this->table;
        $lang                             = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language    = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form                = array();
        $helper->toolbar_btn              = array(
            'save' => array('href' => '#', 'desc' => $this->l('Save'))
        );

        $helper->identifier    = $this->identifier;
        $helper->submit_action = 'submitGcInvoiceExportFilters';
        $helper->currentIndex  = $this->context->link->getAdminLink('AdminModules', false)
                                 . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token         = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars      = array(
            'languages'   => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        $helper->fields_value['GCIE_TOEMAIL']                  = Configuration::get('GCIE_TOEMAIL');
        $helper->fields_value['GCIE_CCEMAIL']                  = Configuration::get('GCIE_CCEMAIL');
        $helper->fields_value['GCIE_FILETYPE']                 = Configuration::get('GCIE_FILETYPE');
        $helper->fields_value['GCIE_MAILTEXT']                 = Configuration::get('GCIE_MAILTEXT');
        $helper->fields_value['GCIE_TOFTP']                    = Configuration::get('GCIE_TOFTP');
        $helper->fields_value['GCIE_INVOICEAUTOEXPORTTOEMAIL'] = Configuration::get('GCIE_INVOICEAUTOEXPORTTOEMAIL');
        $helper->fields_value['GCIE_TOFTPSERVER']              = Configuration::get('GCIE_TOFTPSERVER');
        $helper->fields_value['GCIE_TOFTPUSERNAME']            = Configuration::get('GCIE_TOFTPUSERNAME');
        $helper->fields_value['GCIE_TOFTPUSERPASSWORD']        = Configuration::get('GCIE_TOFTPUSERPASSWORD');
        $helper->fields_value['GCIE_TOFTPFOLDER']              = Configuration::get('GCIE_TOFTPFOLDER');
        $helper->fields_value['GCIE_AUTO_PERIOD']              = Configuration::get('GCIE_AUTO_PERIOD');
        $helper->fields_value['GCIE_CRON_URL']              = Tools::getShopDomain(true, true) . $this->_path . 'cron_invoice_export.php?secure_key=' . md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME')) . '&id_shop=' . (int)$this->context->shop->id;

        $html .= $helper->generateForm(array($fields_form));

        return $html;
    }

    public function br2nl($text)
    {
        return preg_replace('/<br\\s*?\/??>/i', '', $text);
    }

    public function renderFormExport()
    {
        $fields_form   = array();
        $fields_form[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Generate locally'),
                    'icon'  => 'icon-cogs'
                ),
                'submit' => array(
                    'name'  => 'submitInvoiceGenerate',
                    'title' => $this->l('Generate and download'),
                    'icon'  => 'process-icon-download',
                )
            )
        );

        if (Configuration::get('GCIE_TOFTP')) {
            $fields_form[] = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Generate to FTP folder'),
                        'icon'  => 'icon-cogs'
                    ),
                    'submit' => array(
                        'name'  => 'submitInvoiceFtpGenerate',
                        'title' => $this->l('Generate and send to FTP'),
                        'icon'  => 'process-icon-export',
                    )
                )
            );
        }

        $helper                           = new HelperForm();
        $helper->title                    = $this->l('Invoice Export Configuration');
        $helper->show_toolbar             = true;
        $helper->table                    = $this->table;
        $lang                             = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language    = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form                = array();
        $helper->toolbar_btn              = array(
            'save' => array('href' => '#', 'desc' => $this->l('Save'))
        );

        $helper->identifier    = $this->identifier;
        $helper->submit_action = 'submitInvoiceGenerate';
        $helper->currentIndex  = $this->context->link->getAdminLink('AdminModules', false)
                                 . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token         = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars      = array(
            'fields_value' => array(),
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id
        );

        return $helper->generateForm($fields_form);
    }

    public function postValidation()
    {
        $output = '';

        if (Tools::isSubmit('submitInvoiceAutoExport')) {
            $email = Tools::getValue('GCIE_TOEMAIL');
            if ((Tools::getValue('GCIE_INVOICEAUTOEXPORTTOEMAIL') && !$email) || ($email && !Validate::isEmail($email))) {
                $output .= $this->displayError($this->l('Please enter a valid email address for To Email'));
            } else {
                Configuration::updateValue('GCIE_TOEMAIL', $email);
            }

            $ccemail = Tools::getValue('GCIE_CCEMAIL');
            if (Tools::getValue('GCIE_INVOICEAUTOEXPORTTOEMAIL') && $ccemail && !Validate::isEmail($ccemail)) {
                $output .= $this->displayError($this->l('Please enter a valid email address for CC Email'));
            } else {
                Configuration::updateValue('GCIE_CCEMAIL', $ccemail);
            }
            if (!$output) {
                Configuration::updateValue('GCIE_INVOICEAUTOEXPORTTOEMAIL', (int)Tools::getValue('GCIE_INVOICEAUTOEXPORTTOEMAIL'));
            }

            Configuration::updateValue('GCIE_FILETYPE', (int)Tools::getValue('GCIE_FILETYPE'));
            Configuration::updateValue('GCIE_AUTO_PERIOD', (int)Tools::getValue('GCIE_AUTO_PERIOD'));
            Configuration::updateValue('GCIE_MAILTEXT', $this->br2nl(Tools::getValue('GCIE_MAILTEXT')));

            $ftp_server = Tools::getValue('GCIE_TOFTPSERVER');
            $ftp_user   = Tools::getValue('GCIE_TOFTPUSERNAME');
            $ftp_pswd   = Tools::getValue('GCIE_TOFTPUSERPASSWORD');
            $ftp_folder = Tools::getValue('GCIE_TOFTPFOLDER');
            if (Validate::isUrl($ftp_server) && $ftp_user && $ftp_pswd) {
                Configuration::updateValue('GCIE_TOFTP', (int)Tools::getValue('GCIE_TOFTP'));
                Configuration::updateValue('GCIE_TOFTPSERVER', $ftp_server);
                Configuration::updateValue('GCIE_TOFTPUSERNAME', $ftp_user);
                Configuration::updateValue('GCIE_TOFTPUSERPASSWORD', $ftp_pswd);
                Configuration::updateValue('GCIE_TOFTPFOLDER', $ftp_folder);
            } elseif (Tools::getValue('GCIE_TOFTP')) {
                $output .= $this->displayError($this->l('Invalid ftp parameters'));
            } elseif (!$ftp_user && !$ftp_server) {
                Configuration::updateValue('GCIE_TOFTP', 0);
                Configuration::updateValue('GCIE_TOFTPSERVER', '');
                Configuration::updateValue('GCIE_TOFTPUSERNAME', '');
                Configuration::updateValue('GCIE_TOFTPUSERPASSWORD', '');
                Configuration::updateValue('GCIE_TOFTPFOLDER', '');
            }
            if (empty($output)) {
                $output .= $this->displayConfirmation($this->l('Configuration saved.'));
            }
        } elseif (Tools::isSubmit('submitGcInvoiceExportFilters')) {
            $statuses = OrderState::getOrderStates((int)$this->context->language->id);
            $ids      = array();
            foreach ($statuses as $state) {
                if (Tools::getValue('gcorderstatesexport_' . (int)$state['id_order_state'])) {
                    $ids[] = (int)$state['id_order_state'];
                }
            }
            Configuration::updateValue('GCIE_ORDERSTATESEXPORT', serialize($ids));

            $groups = Group::getGroups((int)$this->context->language->id);
            $ids    = array();
            foreach ($groups as $group) {
                if (Tools::getValue('gcordergroupssexport_' . (int)$group['id_group'])) {
                    $ids[] = (int)$group['id_group'];
                }
            }
            Configuration::updateValue('GCIE_ORDERGROUPSEXPORT', serialize($ids));

            $date_start = Tools::getValue('invoice_start') ? Tools::getValue('invoice_start') : '';
            $date_end   = Tools::getValue('invoice_end') ? Tools::getValue('invoice_end') : '';

            if (empty($date_start) || empty($date_end)) {
                $output .= $this->displayError($this->l('Dates are required'));
            }

            $date_start_tab = explode('-', $date_start);
            $start          = mktime('00', '00', '00', $date_start_tab[1], $date_start_tab[2], $date_start_tab[0]);
            $date_end_tab   = explode('-', $date_end);
            $end            = mktime('23', '59', '59', $date_end_tab[1], $date_end_tab[2], $date_end_tab[0]);
            $start_sql      = $date_start. ' 00:00:00';
            $end_sql        = $date_end. ' 23:59:59';

            if ($end < $start) {
                $output .= $this->displayError($this->l('Please enter a valid date interval'));
            } else {
                $output .= $this->displayConfirmation($this->l('Configuration saved.'));
            }

            Configuration::updateValue('GCIE_START', $start_sql);
            Configuration::updateValue('GCIE_END', $end_sql);
        } elseif (Tools::isSubmit('submitGcInvoiceExportColumn')) {
            $invoice_export_tab = $invoice_address_export_tab = $dlv_address_export_tab = array();
            $post_complete      = filter_input_array(INPUT_POST);
            foreach (array_keys($post_complete) as $key) {
                if (strpos($key, $this->name . '_') !== false) {
                    $invoice_export_tab[] = str_replace($this->name . '_', '', $key);
                }
                if (strpos($key, 'GCIE_FIELDSADDRESS_') !== false) {
                    $invoice_address_export_tab[] = str_replace('GCIE_FIELDSADDRESS_', '', $key);
                }
                if (strpos($key, 'GCIE_FIELDSDLVADDRESS_') !== false) {
                    $dlv_address_export_tab[] = str_replace('GCIE_FIELDSDLVADDRESS_', '', $key);
                }
            }
            Configuration::updateValue('GCIE', serialize($invoice_export_tab));
            Configuration::updateValue('GCIE_FIELDSADDRESS', serialize($invoice_address_export_tab));
            Configuration::updateValue('GCIE_FIELDSDLVADDRESS', serialize($dlv_address_export_tab));
            Configuration::updateValue('GCIE_HEADER', Tools::getValue('invoice_header'));
            Configuration::updateValue('GCIE_ROW_TOTAL_FOOTER', Tools::getValue('row_total_footer'));
            Configuration::updateValue('GCIE_CREDITSLIP', Tools::getValue('invoice_creditslip'));
            Configuration::updateValue('GCIE_DATEVALID', Tools::getValue('invoice_datevalid'));
            Configuration::updateValue('GCIE_TAXESDETAILS', Tools::getValue('invoice_taxesdetails'));
            Configuration::updateValue('GCIE_ADDRESSDETAILS', Tools::getValue('invoice_addressdetails'));
            Configuration::updateValue('GCIE_DELIVERYADDRESS', Tools::getValue('GCIE_DELIVERYADDRESS'));
            Configuration::updateValue('GCIE_ECOTAX', Tools::getValue('GCIE_ECOTAX'));
            Configuration::updateValue('GCIE_CSVDELIMITER', Tools::getValue('GCIE_CSVDELIMITER', ';'));
            Configuration::updateValue('GCIE_CUSTOMTABLECOLUM', Tools::getValue('GCIE_CUSTOMTABLECOLUM'));

            $output .= $this->displayConfirmation($this->l('Configuration saved.'));
        }

        return $output;
    }

    public function postProcess()
    {
        if (Tools::getIsset('stop_rating')) {
            Configuration::updateValue('GCIE_STOPRATING', 1);
            die;
        }
        $output = '';

        $id_shop = (int)$this->context->shop->id;

        $output .= $this->postValidation();

        if (Tools::isSubmit('submitInvoiceFtpGenerate')) {
            if ($this->generateInvoiceList()) {
                $this->sendExportedFileFtp();
            }
        } elseif (Tools::isSubmit('submitInvoiceGenerate')) {
            if ($this->generateInvoiceList()) {
                $currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
                $currentIndex .= '&token=' . Tools::getAdminTokenLite('AdminModules');
                $output       .= $this->displayConfirmation($this->l('Invoice :') . ' '
                                                            . $this->l('You can download your export ')
                                                            . ' <a href="' . $currentIndex
                                                            . '&downloadInvoice" target="_blank">' . $this->l('here') . '</a><br>');

                if (Configuration::get('GCIE_CREDITSLIP') == 1) {
                    $output .= $this->displayConfirmation($this->l('Credit Slip :') . ' '
                                                          . $this->l('You can download your export ')
                                                          . '<a href="' . $currentIndex
                                                          . '&downloadCreditSlips" target="_blank"> ' . $this->l('here') . '</a>');
                }
            } else {
                $output .= $this->displayError($this->l('Error during generation.'));
            }
        } elseif (Tools::isSubmit('downloadInvoice')) {
            $this->downloadFile('invoices' . $id_shop . '.csv');
        } elseif (Tools::isSubmit('downloadCreditSlips')) {
            $this->downloadFile('credit_slips' . $id_shop . '.csv');
        }

        return $output;
    }

    private function generateInvoiceList($auto = false, $id_shop = null)
    {
        $id_lang = (int)$this->context->language->id;

        if ($id_shop == null) {
            $id_shop = (int)$this->context->shop->id;
        }

        if ($auto) {
            $auto_period = (int)Configuration::get('GCIE_AUTO_PERIOD');
            switch ($auto_period) {
                case 0: // Current month
                    $start_date = date("Y-m-01 00:00:00");
                    $end_date   = date("Y-m-d H:i:s");
                    break;
                case 1: // Last 30 days
                    $end_date   = date("Y-m-d H:i:s");
                    $start_date = date("Y-m-d 00:00:00", strtotime($end_date . " -30 days"));
                    break;
                case 2: // previous month
                    $start_date = date("Y-m-01 00:00:00", strtotime('previous month'));
                    $end_date   = date("Y-m-t 23:59:59", strtotime('previous month'));
                    break;
                case 3: //current week
                    $monday     = strtotime("last monday");
                    $monday     = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
                    $sunday     = strtotime(date("Y-m-d", $monday) . " +6 days");
                    $start_date = date("Y-m-d", $monday) . ' 00:00:00';
                    $end_date   = date("Y-m-d", $sunday) . ' 23:59:59';
                    break;
                case 4: // Last 7 days
                    $end_date   = date("Y-m-d H:i:s");
                    $start_date = date("Y-m-d 00:00:00", strtotime($end_date . " -7 days"));
                    break;
                case 5: // previous week
                    $monday     = strtotime("last monday");
                    $monday     = date('W', $monday) == date('W') ? $monday - 7 * 86400 : $monday;
                    $sunday     = strtotime(date("Y-m-d", $monday) . " +6 days");
                    $start_date = date("Y-m-d", $monday) . ' 00:00:00';
                    $end_date   = date("Y-m-d", $sunday) . ' 23:59:59';
                    break;
                case 6: // Last 90 days
                    $end_date   = date("Y-m-d H:i:s");
                    $start_date = date("Y-m-d 00:00:00", strtotime($end_date . " -90 days"));
                    break;
                default:
                    $start_date = date("Y-m-d 00:00:00", strtotime(Configuration::get('GCIE_START')));
                    $end_date   = date("Y-m-d 23:59:59", strtotime(Configuration::get('GCIE_END')));
            }
        } else {
            $start_date = date("Y-m-d 00:00:00", strtotime(Configuration::get('GCIE_START')));
            $end_date   = date("Y-m-d 23:59:59", strtotime(Configuration::get('GCIE_END')));
        }

        $address_details     = (Configuration::get('GCIE_ADDRESSDETAILS') == 1) ? (true) : (false);
        $dlv_address_details = (Configuration::get('GCIE_DELIVERYADDRESS') == 1) ? (true) : (false);
        $date_valid          = (Configuration::get('GCIE_DATEVALID') == 1) ? (true) : (false);
        $taxes_details       = false;
        $inv_fields          = $dvl_fields = array();

        $invoice_export_tab = Tools::unserialize(Configuration::get('GCIE'));
        if (!is_array($invoice_export_tab)) {
            $invoice_export_tab = array();
        }

        if ($address_details) {
            $address_details = Tools::unserialize(Configuration::get('GCIE_FIELDSADDRESS'));
            $inv_fields      = $this->getAddressFields();
        }

        if ($dlv_address_details) {
            $dlv_address_details = Tools::unserialize(Configuration::get('GCIE_FIELDSDLVADDRESS'));
            $dvl_fields          = $this->getAddressDvlFields();
        }

        $state_export_tab = Tools::unserialize(Configuration::get('GCIE_ORDERSTATESEXPORT'));
        if (!is_array($state_export_tab)) {
            $state_export_tab = array();
        } else {
            $state_export_tab = array_map(create_function('$i', 'return (int)$i;'), $state_export_tab);
        }

        $group_export_tab = Tools::unserialize(Configuration::get('GCIE_ORDERGROUPSEXPORT'));
        if (!is_array($group_export_tab)) {
            $group_export_tab = array();
        } else {
            $group_export_tab = array_map(create_function('$i', 'return (int)$i;'), $group_export_tab);
        }

        $custom_columns = Configuration::get('GCIE_CUSTOMTABLECOLUM');
        if ($custom_columns) {
            $custom_columns = preg_split("/[\r\n]+/", trim($custom_columns));
            for ($i = 0; $i < count($custom_columns); $i++) {
                $custom_columns[$i] = preg_split("/\.\s*/", $custom_columns[$i]);
            }
        }

        $select = 'SELECT oi.id_order, o.*, ';
        $from   = 'FROM ' . _DB_PREFIX_ . 'order_invoice oi';
        $join   = 'LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON (o.id_order = oi.id_order) ';
        $where  = $id_shop ? 'WHERE o.id_shop = ' . (int)$id_shop . ' ' : '';
        foreach ($invoice_export_tab as $invoice_export) {
            if ($invoice_export == 'customer_name') {
                $select .= 'c.lastname, c.firstname, ';
                $join   .= 'LEFT JOIN ' . _DB_PREFIX_ . 'customer c ON (c.id_customer = o.id_customer) ';
            } elseif ($invoice_export == 'payment') {
                $select .= 'o.payment, ';
            } elseif ($invoice_export == 'order_reference') {
                $select .= 'o.reference, ';
            } elseif ($invoice_export == 'currency') {
                $select .= 'o.id_currency, ';
            } elseif ($invoice_export == 'id_cart') {
                $select .= 'o.id_cart, ';
            } elseif ($invoice_export == 'order_status') {
                $select .= 'osl.name order_status, ';
                $join   .= 'LEFT JOIN ' . _DB_PREFIX_ . 'order_state os ON (o.current_state = os.id_order_state) ';
                $join   .= 'LEFT JOIN ' . _DB_PREFIX_ . 'order_state_lang osl '
                           . 'ON (osl.id_order_state = os.id_order_state AND osl.id_lang = ' . (int)$id_lang . ') ';
            } else {
                $select .= 'oi.' . pSQL($invoice_export) . ', ';
            }
        }

        if (!$address_details) {
            $select .= 'cl.name, ';
            $join   .= 'LEFT JOIN ' . _DB_PREFIX_ . 'address a ON (a.id_address = o.id_address_invoice) ';
            $join   .= 'LEFT JOIN ' . _DB_PREFIX_
                       . 'country_lang cl ON (cl.id_country = a.id_country AND cl.id_lang = ' . (int)$id_lang . ') ';
        } else {
            $select .= 'a.company, cl.name, a.address1, a.address2, a.postcode, a.city, ';
            $select .= 'a.vat_number, a.dni, sl.name as state, a.phone, a.phone_mobile, ';
            $join   .= 'LEFT JOIN ' . _DB_PREFIX_ . 'address a ON (a.id_address = o.id_address_invoice) ';
            $join   .= 'LEFT JOIN ' . _DB_PREFIX_
                       . 'country_lang cl ON (cl.id_country = a.id_country AND cl.id_lang = ' . (int)$id_lang . ') ';
            $join   .= 'LEFT JOIN ' . _DB_PREFIX_ . 'state sl ON (sl.id_state = a.id_state) ';
        }
        if (!$dlv_address_details) {
            $select .= 'cl.name as dlv_country, ';
            $join   .= 'LEFT JOIN ' . _DB_PREFIX_ . 'address dla ON (dla.id_address = o.id_address_delivery) ';
            $join   .= 'LEFT JOIN ' . _DB_PREFIX_ . 'country_lang dlcl '
                       . 'ON (dlcl.id_country = dla.id_country AND dlcl.id_lang = ' . (int)$id_lang . ') ';
        } else {
            $select .= 'dla.company as dlv_company, dlcl.name as dlv_country, dla.address1 as dlv_address1, ';
            $select .= 'dla.address2 dlv_address2, dla.postcode dlv_postcode, dla.vat_number dlv_vat_number,';
            $select .= 'dla.phone as dlv_phone, dla.phone_mobile as dlv_phone_mobile, ';
            $select .= 'dla.dni as dlv_dni, dlsl.name as dlv_state, dla.city as dlv_city, ';
            $join   .= 'LEFT JOIN ' . _DB_PREFIX_ . 'address dla ON (dla.id_address = o.id_address_delivery) ';
            $join   .= 'LEFT JOIN ' . _DB_PREFIX_ . 'country_lang dlcl '
                       . 'ON (dlcl.id_country = dla.id_country AND dlcl.id_lang = ' . (int)$id_lang . ') ';
            $join   .= 'LEFT JOIN ' . _DB_PREFIX_ . 'state dlsl ON (dlsl.id_state = dla.id_state) ';
        }

        if ($custom_columns && is_array($custom_columns) && is_array($custom_columns[0])) {
            foreach ($custom_columns as $index => $column) {
                if ($column[0] == 'customer') {
                    $select .= 'c' . pSQL($index) . '.' . pSQL($column[1]) . ', ';
                    $join   .= 'LEFT JOIN ' . _DB_PREFIX_ . 'customer c' . pSQL($index) . ' ON (c' . pSQL($index) . '.id_customer = o.id_customer) ';
                } elseif ($column[0] == 'orders') {
                    $select .= 'o.' . pSQL($column[1]) . ', ';
                }
            }
        }

        $select_trim = rtrim($select, ', ');

        if (empty($where)) {
            $where .= 'WHERE oi.date_add >= "' . pSQL($start_date) . '" AND oi.date_add <= "' . pSQL($end_date) . '" ';
        } else {
            $where .= 'AND oi.date_add >= "' . pSQL($start_date) . '" AND oi.date_add <= "' . pSQL($end_date) . '" ';
        }

        if (!empty($state_export_tab) && is_array($state_export_tab)) {
            $where .= 'AND o.current_state IN (' . pSQL(implode(',', $state_export_tab)) . ') ';
        }

        if (!empty($group_export_tab) && is_array($group_export_tab)) {
            $join  .= 'LEFT JOIN ' . _DB_PREFIX_ . 'customer_group cg ON (o.id_customer = cg.id_customer) ';
            $where .= 'AND cg.id_group IN (' . pSQL(implode(',', $group_export_tab)) . ') ';
        }

        $final = $select_trim . ' ' . $from . ' ' . $join . ' ' . $where;

        $order_reference   = (in_array('order_reference', $invoice_export_tab)) ? (true) : (false);
        $order_status      = (in_array('order_status', $invoice_export_tab)) ? (true) : (false);
        $date_add          = (in_array('date_add', $invoice_export_tab)) ? (true) : (false);
        $customer_name     = (in_array('customer_name', $invoice_export_tab)) ? (true) : (false);
        $payment           = (in_array('payment', $invoice_export_tab)) ? (true) : (false);
        $currency          = (in_array('currency', $invoice_export_tab)) ? (true) : (false);
        $id_cart           = (in_array('id_cart', $invoice_export_tab)) ? (true) : (false);
        $total_products    = (in_array('total_products', $invoice_export_tab)) ? (true) : (false);
        $total_products_wt = (in_array('total_products_wt', $invoice_export_tab)) ? (true) : (false);
        $export_ecotax = (int)Configuration::get('GCIE_ECOTAX');

        if (Configuration::get('GCIE_TAXESDETAILS')) {
            $title_csv = '';

            if ($export_ecotax) {
                $title_csv .= $this->l('Eco tax tax incl') . ';';
            }

            $taxes_details = true;
            $sqltax        = 'SELECT id_tax,rate FROM `' . _DB_PREFIX_ . 'tax` WHERE active = "1" ORDER BY id_tax ASC';
            if ($details_taxes = Db::getInstance()->ExecuteS($sqltax)) {
                $id_taxes  = array();
                foreach ($details_taxes as $details_tax) {
                    $title_csv  .= $this->l('Products tax exclude') . ' '
                                   . number_format($details_tax['rate'], 2, '.', '') . '%;';
                    $title_csv  .= $this->l('Products tax amount') . ' '
                                   . number_format($details_tax['rate'], 2, '.', '') . '%;';
                    $id_taxes[] = $details_tax['id_tax'];
                }
            }
            $title_csv .= $this->l('Products without tax') . ';';

            $title_csv .= $this->l('Total discount tax amount') . ';';
            $title_csv .= $this->l('Total shipping tax amount') . ';';
            $title_csv .= $this->l('Total wrapping tax amount') . ';';
            $title_csv .= $this->l('Total paid tax amount') . ';';
        }

        $total_discount_tax_excl = (in_array('total_discount_tax_excl', $invoice_export_tab)) ? (true) : (false);
        $total_discount_tax_incl = (in_array('total_discount_tax_incl', $invoice_export_tab)) ? (true) : (false);
        $total_shipping_tax_excl = (in_array('total_shipping_tax_excl', $invoice_export_tab)) ? (true) : (false);
        $total_shipping_tax_incl = (in_array('total_shipping_tax_incl', $invoice_export_tab)) ? (true) : (false);
        $total_wrapping_tax_excl = (in_array('total_wrapping_tax_excl', $invoice_export_tab)) ? (true) : (false);
        $total_wrapping_tax_incl = (in_array('total_wrapping_tax_incl', $invoice_export_tab)) ? (true) : (false);
        $total_paid_tax_excl     = (in_array('total_paid_tax_excl', $invoice_export_tab)) ? (true) : (false);
        $total_paid_tax_incl     = (in_array('total_paid_tax_incl', $invoice_export_tab)) ? (true) : (false);

        if (Configuration::get('GCIE_TAXESDETAILS')) {
            $total_discount_tax_amount = true;
            $total_shipping_tax_amount = true;
            $total_wrapping_tax_amount = true;
            $total_paid_tax_amount     = true;
        }

        chmod(dirname(__FILE__) . '/invoices' . $id_shop . '.csv', 0644);
        $file = fopen(dirname(__FILE__) . '/invoices' . $id_shop . '.csv', 'w');

        $export_table_header = array();
        if (Configuration::get('GCIE_HEADER') == 1) {
            foreach ($invoice_export_tab as $index => $field) {
                if ($custom_columns && is_array($custom_columns) && is_array($custom_columns[0])) {
                    foreach ($custom_columns as $column) {
                        if ($index == ($column[2] - 1)) {
                            $export_table_header[0][] = $column[3];
                        }
                    }
                }
                if ($field == 'number') {
                    $export_table_header[0][] = $this->l('Invoice #');
                }

                if ($field == 'order_reference' && $order_reference) {
                    $export_table_header[0][] = $this->l('Order Reference');
                }

                if ($field == 'order_status' && $order_status) {
                    $export_table_header[0][] = $this->l('Order status');
                }

                if ($field == 'date_add') {
                    if ($date_add) {
                        $export_table_header[0][] = $this->l('Date');
                    }
                    if ($date_valid) {
                        $export_table_header[0][] = $this->l('Date Payment Accepted');
                    }
                }

                if ($field == 'customer_name' && $customer_name) {
                    $export_table_header[0][] = $this->l('Customer name');
                }

                if ($field == 'payment' && $payment) {
                    $export_table_header[0][] = $this->l('Payment method');
                }

                if ($field == 'currency' && $currency) {
                    $export_table_header[0][] = $this->l('Currency');
                }

                if ($field == 'id_cart' && $id_cart) {
                    $export_table_header[0][] = $this->l('ID Cart');
                }

                if ($field == 'total_products' && $total_products) {
                    $export_table_header[0][] = $this->l('Total products tax excl');
                }

                if ($field == 'total_products_wt' && $total_products_wt) {
                    $export_table_header[0][] = $this->l('Total products tax incl');
                }

                if ($field == 'total_discount_tax_excl' && $total_discount_tax_excl) {
                    $export_table_header[0][] = $this->l('Total discount tax excl');
                }

                if ($field == 'total_discount_tax_incl' && $total_discount_tax_incl) {
                    $export_table_header[0][] = $this->l('Total discount tax incl');
                }

                if ($field == 'total_shipping_tax_excl' && $total_shipping_tax_excl) {
                    $export_table_header[0][] = $this->l('Total shipping tax excl');
                }

                if ($field == 'total_shipping_tax_incl' && $total_shipping_tax_incl) {
                    $export_table_header[0][] = $this->l('Total shipping tax incl');
                }

                if ($field == 'total_wrapping_tax_excl' && $total_wrapping_tax_excl) {
                    $export_table_header[0][] = $this->l('Total wrapping tax excl');
                }

                if ($field == 'total_wrapping_tax_incl' && $total_wrapping_tax_incl) {
                    $export_table_header[0][] = $this->l('Total wrapping tax incl');
                }

                if ($field == 'total_paid_tax_excl' && $total_paid_tax_excl) {
                    $export_table_header[0][] = $this->l('Total paid tax excl');
                }

                if ($field == 'total_paid_tax_incl' && $total_paid_tax_incl) {
                    $export_table_header[0][] = $this->l('Total paid tax incl');
                }
            }

            if ($address_details && is_array($address_details)) {
                foreach ($address_details as $value) {
                    $export_table_header[0][] = $inv_fields[$value]['name'];
                }
            }
            if ($dlv_address_details && is_array($dlv_address_details)) {
                foreach ($dlv_address_details as $value) {
                    $export_table_header[0][] = $dvl_fields[$value]['name'];
                }
            }

            if ($export_ecotax) {
                $export_table_header[0][] = $this->l('Eco tax');
            }

            if ($taxes_details) {
                $title_csv = explode(';', $title_csv);
                if (is_array($export_table_header) && count($export_table_header) > 0) {
                    $export_table_header[0] = array_merge($export_table_header[0], $title_csv);
                } else {
                    $export_table_header[0] = $title_csv;
                }
            }
        }

        $export_table_body = array();
        if ($invoices = Db::getInstance()->ExecuteS($final)) {
            foreach ($invoices as $invoice) {
                $line     = array();
                $my_order = new Order($invoice['id_order']);
                if ($date_valid) {
                    $my_histories = $my_order->getHistory(
                        $id_lang,
                        Configuration::get('PS_OS_PAYMENT')
                    );
                    if (count($my_histories)) {
                        foreach ($my_histories as $my_history) {
                            $my_history_date = $my_history['date_add'];
                            break;
                        }
                    } else {
                        $my_history_date = '';
                    }
                }

                $ecotax    = 0;
                $ecotax_wt = 0;
                if ($export_ecotax) {
                    $ecotax_details = $my_order->getEcoTaxTaxesBreakdown();
                    foreach ($ecotax_details as $value) {
                        $ecotax    += (float)$value['ecotax_tax_excl'];
                        $ecotax_wt += (float)$value['ecotax_tax_incl'];
                    }
                }

                foreach ($invoice_export_tab as $index => $field) {
                    if ($custom_columns && is_array($custom_columns) && is_array($custom_columns[0])) {
                        foreach ($custom_columns as $column) {
                            if ($index == ($column[2] - 1)) {
                                $line[] = $invoice[$column[1]];
                            }
                        }
                    }
                    if ($field == 'number') {
                        $invoice_prefix = Configuration::get('PS_INVOICE_PREFIX', $id_lang);
                        if (!empty($invoice_prefix)) {
                            $line[] = $this->getInvoiceNumberFormatted($invoice['number'], $invoice['date_add']);
                        } else {
                            $line[] = sprintf('%06d', $invoice['number']);
                        }
                    }
                    if ($field == 'order_reference') {
                        $line[] = $invoice['reference'];
                    }

                    if ($field == 'order_status') {
                        $line[] = $invoice['order_status'];
                    }

                    if ($field == 'date_add' && $date_add) {
                        $line[] = Tools::displayDate($invoice['date_add'], null, false);
                        if ($date_valid) {
                            if (!empty($my_history_date)) {
                                $line[] = Tools::displayDate($my_history_date, null, false);
                            } else {
                                $line[] = '';
                            }
                        }
                    }

                    if ($field == 'customer_name' && $customer_name) {
                        $line[] = $invoice['lastname'] . ' ' . $invoice['firstname'];
                    }

                    if ($field == 'payment' && $payment) {
                        $line[] = utf8_decode($invoice['payment']);
                    }

                    if ($field == 'currency' && $currency) {
                        $line[] = utf8_decode(Currency::getCurrencyInstance($invoice['id_currency'])->iso_code);
                    }

                    if ($field == 'id_cart' && $id_cart) {
                        $line[] = (int)$invoice['id_cart'];
                    }

                    if ($field == 'total_products' && $total_products) {
                        $line[] = (float)$invoice['total_products'];
                    }

                    if ($field == 'total_products_wt' && $total_products_wt) {
                        $line[] = (float)$invoice['total_products_wt'];
                    }

                    if ($field == 'total_discount_tax_incl' && $total_discount_tax_incl) {
                        $line[] = (float)$invoice['total_discount_tax_incl'];
                    }

                    if ($field == 'total_shipping_tax_excl' && $total_shipping_tax_excl) {
                        $line[] = (float)$invoice['total_shipping_tax_excl'];
                    }

                    if ($field == 'total_shipping_tax_incl' && $total_shipping_tax_incl) {
                        $line[] = (float)$invoice['total_shipping_tax_incl'];
                    }

                    if ($field == 'total_wrapping_tax_excl' && $total_wrapping_tax_excl) {
                        $line[] = (float)$invoice['total_wrapping_tax_excl'];
                    }

                    if ($field == 'total_wrapping_tax_incl' && $total_wrapping_tax_incl) {
                        $line[] = (float)$invoice['total_wrapping_tax_incl'];
                    }

                    if ($field == 'total_paid_tax_excl' && $total_paid_tax_excl) {
                        $line[] = (float)$invoice['total_paid_tax_excl'];
                    }

                    if ($field == 'total_paid_tax_incl' && $total_paid_tax_incl) {
                        $line[] = (float)$invoice['total_paid_tax_incl'];
                    }

                    if ($field == 'total_discount_tax_excl' && $total_discount_tax_excl) {
                        $line[] = (float)$invoice['total_discount_tax_excl'];
                    }
                }

                if ($address_details && is_array($address_details)) {
                    foreach ($address_details as $value) {
                        $line[] = $invoice[$value];
                    }
                }
                if ($dlv_address_details && is_array($dlv_address_details)) {
                    foreach ($dlv_address_details as $value) {
                        $line[] = $invoice[$value];
                    }
                }

                if ($export_ecotax) {
                    $line[] = (float)$ecotax;
                }

                if ($taxes_details) {
                    if ($export_ecotax) {
                        $line[] = (float)$ecotax_wt;
                    }
                    $taxed = 0;
                    foreach ($id_taxes as $id_tax) {
                        $sql = 'SELECT odt.total_amount, odt.id_tax, od.total_price_tax_excl FROM '
                               . _DB_PREFIX_ . 'order_detail_tax as odt LEFT JOIN '
                               . _DB_PREFIX_ . 'order_detail od ON (od.id_order_detail = odt.id_order_detail)
			    WHERE odt.id_tax = ' . (int)$id_tax . ' AND od.id_order=' . (int)$invoice['id_order'] . '';

                        if ($taxes = Db::getInstance()->ExecuteS($sql)) {
                            $taxed++;
                            $amount               = 0;
                            $total_price_tax_excl = 0;
                            foreach ($taxes as $tax) {
                                $amount               += $tax['total_amount'];
                                $total_price_tax_excl += $tax['total_price_tax_excl'];
                            }
                            $line[] = Tools::ps_round($total_price_tax_excl, 2);
                            $line[] = Tools::ps_round($amount, 2);
                        } else {
                            $line[] = '0';
                            $line[] = '0';
                        }
                    }

                    if ($invoice['total_paid_tax_incl'] - $invoice['total_paid_tax_excl'] == 0) {
                        $line[] = 'X';
                    } else {
                        $line[] = '';
                    }

                    if ($total_discount_tax_amount) {
                        $line[] = (float)$invoice['total_discounts_tax_incl'] - $invoice['total_discounts_tax_excl'];
                    }

                    if ($total_shipping_tax_amount) {
                        $line[] = (float)$invoice['total_shipping_tax_incl'] - $invoice['total_shipping_tax_excl'];
                    }

                    if ($total_wrapping_tax_amount) {
                        $line[] = (float)$invoice['total_wrapping_tax_incl'] - $invoice['total_wrapping_tax_excl'];
                    }

                    if ($total_paid_tax_amount) {
                        $line[] = (float)$invoice['total_paid_tax_incl'] - $invoice['total_paid_tax_excl'];
                    }
                }
                $export_table_body[] = $line;
            }
        }

        $export_table_footer = array(
            0 => array()
        );

        if (Configuration::get('GCIE_ROW_TOTAL_FOOTER') == 1) {
            foreach ($export_table_body as $line) {
                for ($i = 0; $i < count($line); $i++) {
                    if (Validate::isFloat($line[$i])) {
                        if (isset($export_table_footer[0][$i])) {
                            $export_table_footer[0][$i] += (float)$line[$i];
                        } else {
                            $export_table_footer[0][$i] = (float)$line[$i];
                        }
                    } elseif (!isset($export_table_footer[0][$i])) {
                        $export_table_footer[0][$i] = 'X';
                    }
                }
            }
        }

        $export_table = array_merge($export_table_header, $export_table_body, $export_table_footer);
        $export_table = $this->utf8Converter($export_table);
        $csv          = $this->arrayToCsv($export_table, Configuration::get('GCIE_CSVDELIMITER', ';'));
        fwrite($file, $csv);
        fclose($file);

        if (Configuration::get('GCIE_CREDITSLIP') == 1) {
            if (file_exists(dirname(__FILE__) . '/credit_slips' . $id_shop . '.csv')) {
                chmod(dirname(__FILE__) . '/credit_slips' . $id_shop . '.csv', 0644);
            }
            $file2               = fopen(dirname(__FILE__) . '/credit_slips' . $id_shop . '.csv', 'w');
            $export_table_header = $export_table_body = $export_table_footer = array();

            if (Configuration::get('GCIE_HEADER') == 1) {
                foreach ($invoice_export_tab as $index => $field) {
                    if ($custom_columns && is_array($custom_columns) && is_array($custom_columns[0])) {
                        foreach ($custom_columns as $column) {
                            if ($index == ($column[2] - 1)) {
                                $export_table_header[] = $column[3];
                            }
                        }
                    }
                    if ($field == 'number') {
                        $export_table_header[] = $this->l('Credit Slips #');
                    }

                    if ($field == 'order_reference') {
                        $export_table_header[] = $this->l('Order Reference');
                    }

                    if ($field == 'date_add') {
                        $export_table_header[] = $this->l('Date');
                    }

                    if ($field == 'customer_name') {
                        $export_table_header[] = $this->l('Customer name');
                    }

                    if ($field == 'total_products') {
                        $export_table_header[] = $this->l('Total products tax excl');
                    }

                    if ($field == 'total_products_wt') {
                        $export_table_header[] = $this->l('Total products tax incl');
                    }

                    if ($field == 'total_shipping_tax_incl') {
                        $export_table_header[] = $this->l('Total shipping tax incl');
                    }

                    if ($field == 'total_paid_tax_incl') {
                        $export_table_header[] = $this->l('Total');
                    }
                }

                if ($address_details && is_array($address_details)) {
                    foreach ($address_details as $value) {
                        $export_table_header[] = $inv_fields[$value]['name'];
                    }
                }
                if ($dlv_address_details && is_array($dlv_address_details)) {
                    foreach ($dlv_address_details as $value) {
                        $export_table_header[] = $dvl_fields[$value]['name'];
                    }
                }

                $export_table_header = array($export_table_header);
            }

            $select2 = 'SELECT os.*, o.reference, os.amount, SUM(osd.amount_tax_excl) as total_products,
                SUM(osd.amount_tax_incl) as total_products_wt, c.lastname, c.firstname, a.company,
                cl.name, a.address1, a.address2, a.phone, a.postcode, a.city, a.vat_number, a.dni, s.name as state,';
            $select2 .= 'dla.company as dlv_company, dlcl.name as dlv_country, '
                        . 'dla.address1 as dlv_address1, dla.city as dlv_city, ';
            $select2 .= 'dla.address2 as dlv_address2, dla.postcode as dlv_postcode, dla.vat_number as dlv_vat_number,';
            $select2 .= 'dla.phone as dlv_phone, dla.phone_mobile as dlv_phone_mobile, ';
            $select2 .= 'dla.dni as dlv_dni, dlsl.name as dlv_state  ';

            $from2  = 'FROM ' . _DB_PREFIX_ . 'order_slip os';
            $join2  = 'LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON (o.id_order = os.id_order)
		LEFT JOIN ' . _DB_PREFIX_ . 'customer c ON (c.id_customer = o.id_customer)
		LEFT JOIN ' . _DB_PREFIX_ . 'address a ON (a.id_address = o.id_address_invoice)
		LEFT JOIN ' . _DB_PREFIX_ . 'country_lang cl ON (cl.id_country = a.id_country)
		INNER JOIN ' . _DB_PREFIX_ . 'order_slip_detail osd ON (osd.id_order_slip = os.id_order_slip)';
            $join2  .= 'LEFT JOIN ' . _DB_PREFIX_ . 'state s ON (s.id_state = a.id_state) ';
            $join2  .= 'LEFT JOIN ' . _DB_PREFIX_ . 'address dla ON (dla.id_address = o.id_address_delivery) ';
            $join2  .= 'LEFT JOIN ' . _DB_PREFIX_ . 'country_lang dlcl '
                       . 'ON (dlcl.id_country = dla.id_country AND dlcl.id_lang = ' . (int)$id_lang . ') ';
            $join2  .= 'LEFT JOIN ' . _DB_PREFIX_ . 'state dlsl ON (dlsl.id_state = dla.id_state) ';
            $where2 = 'WHERE o.id_shop = ' . (int)$id_shop . '
		AND cl.id_lang = ' . (int)$id_lang . '
		AND os.date_add >= "' . pSQL($start_date) . '" AND os.date_add <= "'
                      . pSQL($end_date) . '" GROUP BY os.id_order_slip';

            if ($custom_columns && is_array($custom_columns) && is_array($custom_columns[0])) {
                foreach ($custom_columns as $index => $column) {
                    if ($column[0] == 'customer') {
                        $select2 .= ', c.' . pSQL($column[1]);
                    } elseif ($column[0] == 'orders') {
                        $select2 .= ', o.' . pSQL($column[1]);
                    }
                }
            }

            $final2 = $select2 . ' ' . $from2 . ' ' . $join2 . ' ' . $where2;

            if ($order_slips = Db::getInstance()->ExecuteS($final2)) {
                if (!empty($order_slips[0]['id_order_slip'])) {
                    foreach ($order_slips as $order_slip) {
                        $line = array();
                        foreach ($invoice_export_tab as $index => $field) {
                            if ($custom_columns && is_array($custom_columns) && is_array($custom_columns[0])) {
                                foreach ($custom_columns as $column) {
                                    if ($index == ($column[2] - 1)) {
                                        $line[] = $invoice[$column[1]];
                                    }
                                }
                            }
                            if ($field == 'number') {
                                $invoice_prefix = Configuration::get('PS_CREDIT_SLIP_PREFIX', $id_lang);
                                if (!empty($invoice_prefix)) {
                                    $line[] = Configuration::get('PS_CREDIT_SLIP_PREFIX', $id_lang) . '-' .
                                              sprintf('%06d', $order_slip['id_order_slip']);
                                } else {
                                    $line[] = sprintf('%06d', $order_slip['id_order_slip']);
                                }
                            }

                            if ($field == 'order_reference') {
                                $line[] = $order_slip['reference'];
                            }

                            if ($field == 'date_add') {
                                $line[] = date('d/m/Y', strtotime($order_slip['date_add']));
                            }

                            if ($field == 'customer_name') {
                                $line[] = $order_slip['lastname'] . ' ' . $order_slip['firstname'];
                            }

                            if ($field == 'total_products') {
                                $line[] = $order_slip['total_products'];
                            }

                            if ($field == 'total_products_wt') {
                                $line[] = $order_slip['total_products_wt'];
                            }

                            if ($field == 'total_shipping_tax_incl') {
                                $line[] = $order_slip['total_shipping_tax_incl'];
                            }

                            if ($field == 'total_paid_tax_incl') {
                                $line[] = $order_slip['total_products_wt'] + $order_slip['total_shipping_tax_incl'];
                            }
                        }


                        if ($address_details && is_array($address_details)) {
                            foreach ($address_details as $value) {
                                $line[] = $order_slip[$value];
                            }
                        }
                        if ($dlv_address_details && is_array($dlv_address_details)) {
                            foreach ($dlv_address_details as $value) {
                                $line[] = $order_slip[$value];
                            }
                        }

                        $export_table_body[] = $line;
                    }
                }
            }

            if (Configuration::get('GCIE_ROW_TOTAL_FOOTER') == 1) {
                foreach ($export_table_body as $line) {
                    for ($i = 0; $i < count($line); $i++) {
                        if (Validate::isFloat($line[$i])) {
                            if (isset($export_table_footer[$i])) {
                                $export_table_footer[$i] += (float)$line[$i];
                            } else {
                                $export_table_footer[$i] = (float)$line[$i];
                            }
                        } elseif (!isset($export_table_footer[$i])) {
                            $export_table_footer[$i] = 'X';
                        }
                    }
                }
            }

            $export_table_footer = array($export_table_footer);
            $export_table        = array_merge($export_table_header, $export_table_body, $export_table_footer);
            $export_table        = $this->utf8Converter($export_table);
            $csv                 = $this->arrayToCsv($export_table, Configuration::get('GCIE_CSVDELIMITER', ';'));
            fwrite($file2, $csv);
            fclose($file2);
        }

        return true;
    }

    public function utf8Converter($array)
    {
        array_walk_recursive($array, function (&$item, $key) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });

        return $array;
    }

    public function getExportFields()
    {
        return array(
            array(
                'id'   => 'number',
                'name' => $this->l('Number'),
                'val'  => '1'
            ),
            array(
                'id'   => 'order_reference',
                'name' => $this->l('Order Reference'),
                'val'  => '1'
            ),
            array(
                'id'   => 'order_status',
                'name' => $this->l('Order status'),
                'val'  => '1'
            ),
            array(
                'id'   => 'date_add',
                'name' => $this->l('Date'),
                'val'  => '1'
            ),
            array(
                'id'   => 'customer_name',
                'name' => $this->l('Customer name') . ' ' . $this->l('(Firstname & Lastname)'),
                'val'  => '1'
            ),
            array(
                'id'   => 'payment',
                'name' => $this->l('Payment method'),
                'val'  => '1'
            ),
            array(
                'id'   => 'currency',
                'name' => $this->l('Currency'),
                'val'  => '1'
            ),
            array(
                'id'   => 'id_cart',
                'name' => $this->l('ID Cart'),
                'val'  => '1'
            ),
            array(
                'id'   => 'total_products',
                'name' => $this->l('Total products tax excl'),
                'val'  => '1'
            ),
            array(
                'id'   => 'total_products_wt',
                'name' => $this->l('Total products tax incl'),
                'val'  => '1'
            ),
            array(
                'id'   => 'total_discount_tax_excl',
                'name' => $this->l('Total discount tax excl'),
                'val'  => '1'
            ),
            array(
                'id'   => 'total_discount_tax_incl',
                'name' => $this->l('Total discount tax incl'),
                'val'  => '1'
            ),
            array(
                'id'   => 'total_shipping_tax_excl',
                'name' => $this->l('Total shipping tax excl'),
                'val'  => '1'
            ),
            array(
                'id'   => 'total_shipping_tax_incl',
                'name' => $this->l('Total shipping tax incl'),
                'val'  => '1'
            ),
            array(
                'id'   => 'total_wrapping_tax_excl',
                'name' => $this->l('Total wrapping tax excl'),
                'val'  => '1'
            ),
            array(
                'id'   => 'total_wrapping_tax_incl',
                'name' => $this->l('Total wrapping tax incl'),
                'val'  => '1'
            ),
            array(
                'id'   => 'total_paid_tax_excl',
                'name' => $this->l('Total paid tax excl'),
                'val'  => '1'
            ),
            array(
                'id'   => 'total_paid_tax_incl',
                'name' => $this->l('Total paid tax incl'),
                'val'  => '1'
            )
        );
    }

    public function getAddressFields()
    {
        return array(
            'company'      => array(
                'id'   => 'company',
                'name' => $this->l('Invoice Company'),
                'val'  => '1'
            ),
            'vat_number'   => array(
                'id'   => 'vat_number',
                'name' => $this->l('Invoice vat number'),
                'val'  => '1'
            ),
            'dni'          => array(
                'id'   => 'dni',
                'name' => $this->l('Invoice Identification Number'),
                'val'  => '1'
            ),
            'address1'     => array(
                'id'   => 'address1',
                'name' => $this->l('Invoice Address line 1'),
                'val'  => '1'
            ),
            'address2'     => array(
                'id'   => 'address2',
                'name' => $this->l('Invoice Address line 2'),
                'val'  => '1'
            ),
            'postcode'     => array(
                'id'   => 'postcode',
                'name' => $this->l('Invoice Postcode'),
                'val'  => '1'
            ),
            'phone'        => array(
                'id'   => 'phone',
                'name' => $this->l('Invoice Phone'),
                'val'  => '1'
            ),
            'phone_mobile' => array(
                'id'   => 'phone_mobile',
                'name' => $this->l('Invoice Mobile phone'),
                'val'  => '1'
            ),
            'name'         => array(
                'id'   => 'name',
                'name' => $this->l('Invoice Country'),
                'val'  => '1'
            ),
            'state'        => array(
                'id'   => 'state',
                'name' => $this->l('Invoice State'),
                'val'  => '1'
            ),
            'city'         => array(
                'id'   => 'city',
                'name' => $this->l('Invoice City'),
                'val'  => '1'
            ),
        );
    }

    public function getAddressDvlFields()
    {
        return array(
            'dlv_company'      => array(
                'id'   => 'dlv_company',
                'name' => $this->l('Delivery Company'),
                'val'  => '1'
            ),
            'dlv_vat_number'   => array(
                'id'   => 'dlv_vat_number',
                'name' => $this->l('Delivery vat number'),
                'val'  => '1'
            ),
            'dlv_dni'          => array(
                'id'   => 'dlv_dni',
                'name' => $this->l('Delivery Identification Number'),
                'val'  => '1'
            ),
            'dlv_address1'     => array(
                'id'   => 'dlv_address1',
                'name' => $this->l('Delivery Address line 1'),
                'val'  => '1'
            ),
            'dlv_address2'     => array(
                'id'   => 'dlv_address2',
                'name' => $this->l('Delivery Address line 2'),
                'val'  => '1'
            ),
            'dlv_postcode'     => array(
                'id'   => 'dlv_postcode',
                'name' => $this->l('Delivery Postcode'),
                'val'  => '1'
            ),
            'dlv_phone'        => array(
                'id'   => 'dlv_phone',
                'name' => $this->l('Delivery Phone'),
                'val'  => '1'
            ),
            'dlv_phone_mobile' => array(
                'id'   => 'dlv_phone_mobile',
                'name' => $this->l('Delivery Mobile phone'),
                'val'  => '1'
            ),
            'dlv_country'      => array(
                'id'   => 'dlv_country',
                'name' => $this->l('Delivery Country'),
                'val'  => '1'
            ),
            'dlv_state'        => array(
                'id'   => 'dlv_state',
                'name' => $this->l('Delivery State'),
                'val'  => '1'
            ),
            'dlv_city'         => array(
                'id'   => 'dlv_city',
                'name' => $this->l('Delivery City'),
                'val'  => '1'
            ),
        );
    }

    public function getOrderStatusFormated($id_lang = false)
    {
        if (!$id_lang) {
            $id_lang = $this->context->language->id;
        }
        $status_formated = array();
        $statuses        = OrderState::getOrderStates($id_lang);
        foreach ($statuses as $status) {
            $status_formated[] = array(
                'id'   => (int)$status['id_order_state'],
                'name' => $status['name'],
                'val'  => 1,
            );
        }

        return $status_formated;
    }

    public function getGroupsFormated($id_lang = false)
    {
        if (!$id_lang) {
            $id_lang = $this->context->language->id;
        }
        $group_formated = array();
        $groups         = Group::getGroups($id_lang);
        foreach ($groups as $group) {
            $group_formated[] = array(
                'id'   => (int)$group['id_group'],
                'name' => $group['name'],
                'val'  => 1,
            );
        }

        return $group_formated;
    }

    /**
     *
     * @param type $number
     * @param type $date_add
     * @param type $id_lang
     * @param type $id_shop
     *
     * @return type
     */
    public function getInvoiceNumberFormatted($number, $date_add = '', $id_lang = false, $id_shop = false)
    {
        $format = '%1$s%2$06d';

        if (!$id_lang) {
            $id_lang = (int)$this->context->language->id;
        }

        if (Configuration::get('PS_INVOICE_USE_YEAR')) {
            $format = Configuration::get('PS_INVOICE_YEAR_POS') ? '%1$s%3$s/%2$06d' : '%1$s%2$06d/%3$s';
        }

        $prefix = Configuration::get('PS_INVOICE_PREFIX', (int)$id_lang, null, (int)$id_shop);

        return sprintf($format, $prefix, $number, date('Y', strtotime($date_add)));
    }

    public function arrayToCsv(array $fields, $delimiter = ';', $enclosure = '"', $encloseAll = false, $n_t_n = false)
    {
        $delimiter_esc   = preg_quote($delimiter, '/');
        $enclosure_esc   = preg_quote($enclosure, '/');
        $nullToMysqlNull = $n_t_n;

        $outputString = "";
        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $tempFields) {
                $output = array();
                if (is_array($tempFields) && count($tempFields) > 0) {
                    foreach ($tempFields as $field) {
                        if (gettype($field) == 'integer' || gettype($field) == 'double') {
                            $field = (string)$field;
                        }

                        if ($field === null && $nullToMysqlNull) {
                            $output[] = 'NULL';
                            continue;
                        }
                        if ($encloseAll || preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field)) {
                            $field = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
                        }
                        $output[] = $field . "";
                    }
                    $outputString .= implode($delimiter, $output) . "\r\n";
                }
            }
        }

        return $outputString;
    }

    public function downloadFile($filename)
    {
        $filename = $this->local_path . $filename;
        if (!file_exists($filename)) {
            echo $filename;
            die('File not found');
        }

        if (ob_get_level() && ob_get_length() > 0) {
            ob_end_clean();
        }

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename=' . time() . '.csv');
        header('Pragma: no-cache');

        readfile($filename);
        exit;
    }

    public function sendExportedFiles($id_shop = null)
    {
        if ($id_shop == null) {
            $id_shop = (int)$this->context->language->id;
        }

        $type              = (int)Configuration::get('GCIE_FILETYPE');
        $file_attachements = $file_attachement = array();
        if ($type == 0 || $type == 2) {
            $filename = $this->local_path . 'invoices' . $id_shop . '.csv';
            if (Tools::file_get_contents($filename) != false) {
                $file_attachement['content'] = Tools::file_get_contents($filename);
                $file_attachement['name']    = 'invoices' . $id_shop . time() . '.csv"';
                $file_attachement['mime']    = 'text/csv';
                $file_attachements[0]        = $file_attachement;
            }
        }

        if ($type == 1 || $type == 2) {
            $file_attachement = array();
            $filename         = $this->local_path . 'credit_slips' . $id_shop . '.csv';
            if (Tools::file_get_contents($filename) != false) {
                $file_attachement['content'] = Tools::file_get_contents($filename);
                $file_attachement['name']    = 'credit_slips' . $id_shop . time() . '.csv"';
                $file_attachement['mime']    = 'text/csv';
                $file_attachements[1]        = $file_attachement;
            }
        }

        $template = 'invoice-exported';

        $to        = Configuration::getMultiple(array('GCIE_TOEMAIL', 'GCIE_CCEMAIL'));
        $to_name   = '';
        $from      = null;
        $from_name = Configuration::get('PS_SHOP_NAME');

        $template_vars = array(
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{mail_text}' => nl2br(Configuration::get('GCIE_MAILTEXT'))
        );

        $iso = Language::getIsoById($this->context->language->id);
        $dir = $this->local_path . '/mails/';

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            if (file_exists($dir . $iso . '/' . $template . '.txt')
                && file_exists($dir . $iso . '/' . $template . '.html')) {
                Mail::Send(
                    (int)$this->context->language->id,
                    $template,
                    Mail::l('Invoice Exported', (int)$this->context->language->id),
                    $template_vars,
                    $to['GCIE_TOEMAIL'],
                    $to_name,
                    $from,
                    $from_name,
                    $file_attachements,
                    null,
                    $dir
                );
                if ($to['GCIE_CCEMAIL']) {
                    Mail::Send(
                        (int)$this->context->language->id,
                        $template,
                        Mail::l('Invoice Exported', (int)$this->context->language->id),
                        $template_vars,
                        $to['GCIE_CCEMAIL'],
                        $to_name,
                        $from,
                        $from_name,
                        $file_attachements,
                        null,
                        $dir
                    );
                }

                return true;
            }

            return false;
        }

        if (file_exists($dir . $iso . '/' . $template . '.txt')
            && file_exists($dir . $iso . '/' . $template . '.html')) {
            $cc = $to['GCIE_CCEMAIL'];
            Mail::Send(
                (int)$this->context->language->id,
                $template,
                Mail::l('Invoice Exported', (int)$this->context->language->id),
                $template_vars,
                $to['GCIE_TOEMAIL'],
                $to_name,
                $from,
                $from_name,
                $file_attachements,
                null,
                $dir,
                false,
                null,
                $cc
            );
        }
    }

    public function sendExportedFileFtp($id_shop = null)
    {
        if ($id_shop == null) {
            $id_shop = (int)$this->context->language->id;
        }

        $type = (int)Configuration::get('GCIE_FILETYPE');

        $files = array();
        if ($type == 0 || $type == 2) {
            $files[] = $this->local_path . 'invoices' . $id_shop . '.csv';
        }

        if ($type == 1 || $type == 2) {
            $files[] = $this->local_path . 'credit_slips' . $id_shop . '.csv';
        }

        $ftp_server    = Configuration::get('GCIE_TOFTPSERVER');
        $ftp_user_name = Configuration::get('GCIE_TOFTPUSERNAME');
        $ftp_user_pass = Configuration::get('GCIE_TOFTPUSERPASSWORD');
        $conn_id = ftp_connect($ftp_server) or die($this->l('Cannot connect to host'));
        ftp_login($conn_id, $ftp_user_name, $ftp_user_pass) or die($this->l('You do not have access to this ftp server!'));
        ftp_set_option($conn_id, FTP_TIMEOUT_SEC, 1000);
        ftp_pasv($conn_id, true);

        $output = '';
        foreach ($files as $file) {
            if (!file_exists($file)) {
                $output .= Tools::displayError('File not found');
                continue;
            }
            $remote_file = time() . '_' . basename($file);
            $ftp_folder  = Configuration::get('GCIE_TOFTPFOLDER');

            $remote_file = rtrim($ftp_folder, '/\\') . '/' . $remote_file;
            if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
                $this->output .= $this->displayConfirmation($this->l('Successfully uploaded to ftp server'));
            } else {
                $this->output .= $this->displayError($this->l('There was a problem while uploading.'));
            }
        }

        ftp_close($conn_id);
    }

    public function processMailExport($id_shop = null)
    {
        $this->generateInvoiceList(true, $id_shop);
        if (Configuration::get('GCIE_INVOICEAUTOEXPORTTOEMAIL')) {
            $this->sendExportedFiles($id_shop);
        }
        if (Configuration::get('GCIE_TOFTP')) {
            $this->sendExportedFileFtp($id_shop);
        }
    }
}
