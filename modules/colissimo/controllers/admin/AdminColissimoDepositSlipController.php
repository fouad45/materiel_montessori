<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class AdminColissimoDepositSlipController
 *
 * Ajax processes:
 *  - generateDepositSlip
 *  - displayResult
 *
 * Processes:
 *  - download
 *  - downloadSelected
 *  - delete
 *
 */
class AdminColissimoDepositSlipController extends ModuleAdminController
{
    /** @var Colissimo $module */
    public $module;

    /** @var string $header */
    public $header;

    /** @var string $html */
    public $html;

    /** @var string $page */
    public $page;

    /** @var ColissimoGenerateBordereauRequest $depositSlipRequest */
    public $depositSlipRequest;

    /**
     * AdminColissimoDepositSlipController constructor.
     * @throws Exception
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'ColissimoDepositSlip';
        parent::__construct();
        $this->module->logger->setChannel('DepositSlip');
        $this->initPage();
    }

    /**
     *
     */
    public function initPage()
    {
        $this->page = Tools::getValue('render');
        if (!$this->page) {
            $this->page = 'form';
        }
        if (Tools::isSubmit('submitBulkprintSelectedcolissimo_deposit_slip')) {
            $this->action = 'downloadSelected';
        }
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function initForm()
    {
        $select = array(
            'col.`id_colissimo_label`',
            'col.`id_colissimo_order`',
            'col.`date_add` AS label_date_add',
            'col.`shipping_number`',
            'o.`id_order`',
            'o.`current_state`',
            'o.`date_add` AS order_date_add',
            'o.`reference`',
            'CONCAT(a.`lastname`, " ", a.`firstname`) AS customer',
        );
        $excludedStatuses = array(
            Configuration::get('PS_OS_CANCELED'),
        );
        $dbQuery = new DbQuery();
        //@formatter:off
        $dbQuery->select(implode(',', $select))
                ->from('colissimo_label', 'col')
                ->leftJoin('colissimo_order', 'cor', 'cor.`id_colissimo_order` = col.`id_colissimo_order`')
                ->leftJoin('orders', 'o', 'o.`id_order` = cor.`id_order`')
                ->leftJoin('address', 'a', 'a.`id_address` = o.`id_address_delivery`')
                ->where('col.`return_label` = 0')
                ->where('col.`id_colissimo_deposit_slip` = 0')
                ->where('o.current_state NOT IN('.implode(',', array_map('intval', $excludedStatuses)).')')
                ->orderBy('col.date_add DESC');
        //@formatter:on
        $labels = Db::getInstance(_PS_USE_SQL_SLAVE_)
                    ->executeS($dbQuery);
        foreach ($labels as $key => &$label) {
            $order = new Order((int) $label['id_order']);
            $isShipped = $order->getHistory($this->context->language->id, Configuration::get('PS_OS_SHIPPING'));
            $isDelivered = $order->getHistory($this->context->language->id, Configuration::get('PS_OS_DELIVERED'));
            if ($isShipped || $isDelivered) {
                unset($labels[$key]);
                continue;
            }
            $orderState = new OrderState((int) $label['current_state'], $this->context->language->id);
            $label['state_color'] = Tools::getBrightness($orderState->color) < 128 ? 'white' : '#383838';
            $label['state_bg'] = $orderState->color;
            $label['state_name'] = $orderState->name;
        }
        $this->context->smarty->assign(
            array(
                'labels' => $labels,
            )
        );
        $this->html = $this->createTemplate('deposit-slip-form.tpl')
                           ->fetch();
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function initHistory()
    {
        $this->identifier = 'id_colissimo_deposit_slip';
        $this->table = 'colissimo_deposit_slip';
        //@formatter:off
        $this->toolbar_title = $this->module->l('Colissimo deposit slip history', 'AdminColissimoDepositSlipController');
        //@formatter:on
        $this->list_id = 'colissimo-deposit-slip-history';
        $this->list_no_link = true;
        $this->list_simple_header = false;
        $this->actions = array('print', 'delete');
        $this->shopLinkType = false;
        $this->show_toolbar = true;
        $this->token = Tools::getAdminTokenLite('AdminColissimoDepositSlip');
        $this->_select = 'a.id_colissimo_deposit_slip, a.number, a.nb_parcel, a.date_add';
        $this->_where = 'AND a.file_deleted = 0';
        $this->_orderBy = 'a.date_add';
        $this->_orderWay = 'DESC';
        $this->fields_list = array(
            'number'    => array(
                'title' => $this->module->l('Deposit slip #', 'AdminColissimoDepositSlipController'),
            ),
            'nb_parcel' => array(
                'title' => $this->module->l('Parcels count', 'AdminColissimoDepositSlipController'),
            ),
            'date_add'  => array(
                'title' => $this->module->l('Creation date', 'AdminColissimoDepositSlipController'),
                'type'  => 'datetime',
            ),
        );

        $this->html = $this->createTemplate('deposit-slip-history.tpl')
                           ->fetch();
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function initProcess()
    {
        $this->header = $this->module->setColissimoControllerHeader();
        if (method_exists($this, 'init'.Tools::toCamelCase($this->page))) {
            $this->{'init'.$this->page}();
        }
        parent::initProcess();
    }

    /**
     * @param bool $isNewTheme
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin('typewatch');
        $this->addJS($this->module->getLocalPath().'views/js/colissimo.depositslip.js');
    }

    /**
     * @param string $url
     */
    public function setRedirectAfter($url)
    {
        parent::setRedirectAfter($url.'&render='.$this->page);
    }

    /**
     * @return false|ObjectModel|void
     * @throws PrestaShopException
     */
    public function processDelete()
    {
        parent::processDelete();
        $this->setRedirectAfter(self::$currentIndex.'&conf=1&token='.$this->token);
    }

    /**
     *
     */
    public function initContent()
    {
        //@formatter:off
        if (extension_loaded('soap') == false) {
            $this->warnings[] = $this->module->l('You need to enable the SOAP extension to generate deposit slips.', 'AdminColissimoDepositSlipController');
        }
        //@formatter:on
        $this->content = $this->header.$this->html;
        parent::initContent();
    }

    /**
     * @param Helper $helper
     */
    public function setHelperDisplay(Helper $helper)
    {
        parent::setHelperDisplay($helper);
        $this->helper->currentIndex .= '&render=history';
        $this->helper->force_show_bulk_actions = true;
    }

    /**
     * @param int  $idLang
     * @param null $orderBy
     * @param null $orderWay
     * @param int  $start
     * @param null $lim
     * @param bool $idLangShop
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getList($idLang, $orderBy = null, $orderWay = null, $start = 0, $lim = null, $idLangShop = false)
    {
        parent::getList($idLang, $orderBy, $orderWay, $start, $lim, $idLangShop);
        if ($this->_listTotal > 0) {
            $this->bulk_actions = array(
                'printSelected' => array(
                    'text' => $this->module->l('Print selected', 'AdminColissimoDepositSlipController'),
                    'icon' => 'icon-print',
                ),
            );
        }
    }

    /**
     *
     */
    public function processDownload()
    {
        $idDepositSlip = Tools::getValue('id_deposit_slip');
        $depositSlip = new ColissimoDepositSlip((int) $idDepositSlip);
        if (Validate::isLoadedObject($depositSlip)) {
            try {
                $depositSlip->download();
            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }
    }

    /**
     *
     */
    public function processDownloadSelected()
    {
        $depositSlipIds = Tools::getValue('colissimo-deposit-slip-historyBox');
        if ($depositSlipIds) {
            $files = array();
            foreach ($depositSlipIds as $depositSlipId) {
                $depositSlip = new ColissimoDepositSlip((int) $depositSlipId);
                if (Validate::isLoadedObject($depositSlip)) {
                    $files[] = $depositSlip;
                }
            }
            $filename = 'colissimo_deposit_slips_'.date('Ymd_His').'.zip';
            try {
                ColissimoTools::downloadDocuments($files, $filename);
            } catch (Exception $e) {
                $this->module->logger->error(sprintf('Error while downloading zip: %s', $e->getMessage()));
                //@formatter:off
                $this->context->controller->errors[] = $this->module->l('Cannot generate zip file.', 'AdminColissimoDepositSlipController');
                //@formatter:on
            }
        }
    }

    /**
     * @param array $parcelNumbers
     * @return int
     * @throws Exception
     */
    public function generateDepositSlip($parcelNumbers)
    {
        $this->depositSlipRequest = new ColissimoGenerateBordereauRequest(ColissimoTools::getCredentials());
        $this->depositSlipRequest->setParcelsNumbers($parcelNumbers);
        $this->module->logger->infoXml('Log XML request', $this->depositSlipRequest->getRequest(true));
        $client = new ColissimoClient();
        $client->setRequest($this->depositSlipRequest);
        /** @var ColissimoGenerateBordereauResponse $response */
        $response = $client->request();
        $this->module->logger->info('Log response', array('obj' => $response));
        if (!$response->messages[0]['id']) {
            $filename = str_pad($response->bordereauHeader['bordereauNumber'], 8, '0', STR_PAD_LEFT);
            $filename .= date('YmdHis').'.pdf';
            $depositSlip = new ColissimoDepositSlip();
            $depositSlip->filename = pSQL($filename);
            $depositSlip->number = (int) $response->bordereauHeader['bordereauNumber'];
            $depositSlip->nb_parcel = (int) $response->bordereauHeader['numberOfParcels'];
            $depositSlip->file_deleted = 0;
            $depositSlip->save();
            $depositSlip->writeDepositSlip(base64_decode($response->bordereau));

            return (int) $depositSlip->id;
        } else {
            $message = $response->messages[0];
            throw new Exception(sprintf('%s (%s) - %s', $message['id'], $message['type'], $message['messageContent']));
        }
    }

    /**
     * @param string $token
     * @param int    $id
     * @param string $name
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function displayPrintLink($token, $id, $name)
    {
        $this->context->smarty->assign(
            array(
                'token' => $token,
                'id'    => $id,
                'name'  => $name,
            )
        );

        return $this->createTemplate('_partials/deposit-slip-print-action.tpl')
                    ->fetch();
    }

    /**
     *
     */
    public function ajaxProcessGenerateDepositSlip()
    {
        $this->module->logger->setChannel('GenerateDepositSlip');
        $labelIds = json_decode(Tools::getValue('label_ids'), true);
        $parcelNumbers = array();
        foreach ($labelIds as $labelId) {
            $label = new ColissimoLabel((int) $labelId);
            $parcelNumbers[] = $label->shipping_number;
        }
        try {
            $idDepositSlip = $this->generateDepositSlip($parcelNumbers);
        } catch (Exception $e) {
            $this->module->logger->error($e->getMessage());
            $return = array(
                'error'         => true,
                'result_html'   => $this->module->displayError($e->getMessage()),
                'depositSlipId' => 0,
            );
            $this->ajaxDie(json_encode($return));
        }
        $data = array('id_colissimo_deposit_slip' => (int) $idDepositSlip);
        $where = 'id_colissimo_label IN ('.implode(',', array_map('intval', $labelIds)).')';
        Db::getInstance()
          ->update('colissimo_label', $data, $where);
        if (Configuration::get('COLISSIMO_USE_HANDLED_BY_CARRIER')) {
            foreach ($labelIds as $labelId) {
                $label = new ColissimoLabel((int) $labelId);
                $colissimoOrder = new ColissimoOrder((int) $label->id_colissimo_order);
                $idOrder = $colissimoOrder->id_order;
                $order = new Order((int) $idOrder);
                $idHandledByCarrierOS = Configuration::get('COLISSIMO_OS_HANDLED_BY_CARRIER');
                $handledByCarrierOS = new OrderState((int) $idHandledByCarrierOS);
                if (Validate::isLoadedObject($handledByCarrierOS)) {
                    if (!$order->getHistory($this->context->language->id, (int) $idHandledByCarrierOS) &&
                        $order->current_state != $idHandledByCarrierOS
                    ) {
                        $history = new OrderHistory();
                        $history->id_order = (int) $order->id;
                        $history->changeIdOrderState($idHandledByCarrierOS, (int) $order->id);
                        try {
                            $history->add();
                        } catch (Exception $e) {
                            $this->module->logger->error(sprintf('Cannot change status of order #%d', $order->id));
                        }
                    }
                } else {
                    $this->module->logger->error('Handled by Carrier order state is not valid');
                }
            }
        }
        $return = array(
            'error'         => false,
            'message'       => '',
            'depositSlipId' => $idDepositSlip,
        );
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function ajaxProcessDisplayResult()
    {
        $this->module->logger->setChannel('DisplayDepositSlip');
        $depositSlipIds = json_decode(Tools::getValue('deposit_slip_ids'), true);
        $data = array();
        $labelIds = array();
        foreach ($depositSlipIds as $depositSlipId) {
            $depositSlip = new ColissimoDepositSlip((int) $depositSlipId);
            $data[(int) $depositSlipId] = array(
                'number' => $depositSlip->number,
            );
            $labelIds += array_map(
                function ($element) {
                    return $element['id_colissimo_label'];
                },
                $depositSlip->getLabelIds()
            );
        }
        $this->context->smarty->assign(array('data' => $data, 'nb_deposit_slip' => count($data)));
        $html = $this->createTemplate('_partials/deposit-slip-result.tpl')
                     ->fetch();
        $return = array(
            'error'       => false,
            'message'     => '',
            'result_html' => $html,
            'label_ids'   => $labelIds,
        );
        $this->ajaxDie(json_encode($return));
    }
}
