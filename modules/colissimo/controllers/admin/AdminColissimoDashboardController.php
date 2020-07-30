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
 * Class AdminColissimoDashboardController
 *
 * Ajax processes:
 *  - orderDetails
 *  - updateOrderTracking
 *  - updateAllOrderTracking
 *
 */
class AdminColissimoDashboardController extends ModuleAdminController
{
    /** Time range between which we don't request update for an order */
    const TIME_RANGE_UPDATE = 2;

    /** @var Colissimo $module */
    public $module;

    /** @var string $header */
    private $header;

    /** @var array $ordersToUpdate */
    private $ordersToUpdate = array();

    /**
     * AdminColissimoDashboardController constructor.
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->module->logger->setChannel('Dashboard');
        $forceUpdate = Tools::isSubmit('submitUpdateAllTracking');
        $this->setNeedTrackingUpdate($forceUpdate);
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function initProcess()
    {
        $this->header = $this->module->setColissimoControllerHeader();
        $this->initDashboard();
        parent::initProcess();
    }

    /**
     * @param bool $isNewTheme
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS($this->module->getLocalPath().'views/js/colissimo.dashboard.js');
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function initContent()
    {
        $trackingUpdate = '';
        $intro = '';
        if (!empty($this->ordersToUpdate)) {
            $this->context->smarty->assign(array(
                'img_path'     => $this->module->getPathUri().'views/img/',
                'orders_count' => count($this->ordersToUpdate),
                'force_update' => Tools::isSubmit('submitUpdateAllTracking'),
            ));
            $trackingUpdate = $this->createTemplate('tracking-update.tpl')->fetch();
        } else {
            $intro = $this->createTemplate('intro.tpl')->fetch();
        }
        $this->content = $this->header.$intro.$this->content.$trackingUpdate;
        parent::initContent();
    }

    /**
     * @throws PrestaShopDatabaseException
     */
    public function initDashboard()
    {
        if (!empty($this->ordersToUpdate)) {
            return;
        }
        $idLang = $this->context->language->id;
        $statusesList = array();
        $statuses = OrderState::getOrderStates((int) $idLang);
        foreach ($statuses as $status) {
            $statusesList[$status['id_order_state']] = $status['name'];
        }
        $colissimoServicesList = array();
        $colissimoServices = ColissimoService::getAll();
        foreach ($colissimoServices as $colissimoService) {
            $colissimoServicesList[$colissimoService['commercial_name']] = $colissimoService['commercial_name'];
        }
        ksort($colissimoServicesList, SORT_ASC);
        $countriesList = array();
        $countries = Country::getCountries((int) $idLang);
        foreach ($countries as $country) {
            $countriesList[$country['id_country']] = $country['name'];
        }
        $dateTime90Days = new DateTime(date('Y-m-d H:i:s'));
        $dateTime90Days->sub(new DateInterval('P90D'));
        $select = array(
            'o.`reference`',
            'o.`id_order`',
            'COUNT(cola.`id_colissimo_label`) AS `nb_label`',
            'MIN(cola.`date_add`) AS `date_exp`',
            'DATEDIFF(NOW(), MIN(cola.`date_add`)) AS `risk_value`',
            'CONCAT(IF (o.delivery_date = "0000-00-00 00:00:00", 2, 1), " ", DATEDIFF(  NOW(), MIN(cola.date_add))) as `risk`',
            'CONCAT(LEFT(c.`firstname`, 1), ". ", c.`lastname`) AS `customer`',
            'osl.`name` AS `osname`',
            'os.`color`',
            'o.`date_add`',
            'cs.`commercial_name`',
            'cl.`name` AS `country`',
        );
        //@formatter:off
        $join = array(
            'LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = a.`id_order`',
            'LEFT JOIN `'._DB_PREFIX_.'address` ad ON ad.`id_address` = o.`id_address_delivery`',
            'LEFT JOIN `'._DB_PREFIX_.'country` co ON co.`id_country` = ad.`id_country`',
            'LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = ad.`id_country` AND cl.`id_lang` = '.(int) $idLang.')',
            'LEFT JOIN `'._DB_PREFIX_.'customer` c ON c.`id_customer` = o.`id_customer`',
            'LEFT JOIN `'._DB_PREFIX_.'order_state` os ON os.`id_order_state` = o.`current_state`',
            'LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (osl.`id_order_state` = os.`id_order_state` AND osl.`id_lang` = '.(int) $idLang.')',
            'LEFT JOIN `'._DB_PREFIX_.'colissimo_service` cs ON cs.`id_colissimo_service` = a.`id_colissimo_service`',
            'LEFT JOIN `'._DB_PREFIX_.'colissimo_label` cola ON cola.`id_colissimo_order` = a.`id_colissimo_order`',
        );
        //@formatter:on
        $this->identifier = 'id_colissimo_order';
        $this->table = 'colissimo_order';
        $this->className = 'ColissimoOrder';
        $this->list_id = 'colissimo_dashboard';
        $this->_select = implode(',', $select);
        $this->_join = implode(' ', $join);
        $this->_where .= 'AND o.date_add > "'.$dateTime90Days->format('Y-m-d H:i:s').'"';
        $this->_where .= Shop::addSqlRestriction(false, 'o');
        $this->_group = 'GROUP BY a.`id_colissimo_order`';
        $this->_having = ' AND COUNT(cola.`id_colissimo_label`) > 0';
        $this->_filterHaving = true;
        $this->list_no_link = true;
        $this->_orderBy = 'risk';
        $this->_orderWay = 'desc';
        $this->fields_list = array(
            'reference'       => array(
                'title'          => $this->module->l('Reference', 'AdminColissimoDashboardController'),
                'remove_onclick' => true,
                'class'          => 'pointer col-reference-plus',
            ),
            'id_order' => array(
                'title' => $this->module->l('ID', 'AdminColissimoDashboardController'),
                'havingFilter' => true,
                'type' => 'int',
                'filter_key' => 'o!id_order',
                'remove_onclick' => true,
            ),
            'customer'        => array(
                'title'          => $this->module->l('Customer', 'AdminColissimoDashboardController'),
                'havingFilter'   => true,
                'remove_onclick' => true,
            ),
            'date_add'        => array(
                'title'          => $this->module->l('Date', 'AdminColissimoDashboardController'),
                'remove_onclick' => true,
                'type'           => 'datetime',
                'filter_key'     => 'o!date_add',
            ),
            'osname'          => array(
                'title'          => $this->module->l('Order state', 'AdminColissimoDashboardController'),
                'remove_onclick' => true,
                'type'           => 'select',
                'color'          => 'color',
                'list'           => $statusesList,
                'filter_key'     => 'os!id_order_state',
                'filter_type'    => 'int',
                'order_key'      => 'osname',
            ),
            'commercial_name' => array(
                'title'          => $this->module->l('Colissimo Service', 'AdminColissimoDashboardController'),
                'remove_onclick' => true,
                'type'           => 'select',
                'list'           => $colissimoServicesList,
                'filter_key'     => 'cs!commercial_name',
                'filter_type'    => 'string',
                'order_key'      => 'commercial_name',
            ),
            'country'         => array(
                'title'          => $this->module->l('Delivery country', 'AdminColissimoDashboardController'),
                'remove_onclick' => true,
                'type'           => 'select',
                'list'           => $countriesList,
                'filter_key'     => 'co!id_country',
                'filter_type'    => 'int',
                'order_key'      => 'country',
            ),
            'nb_label'        => array(
                'title'          => $this->module->l('Number of', 'AdminColissimoDashboardController').'<br />'.$this->module->l('shipment(s)', 'AdminColissimoDashboardController'),
                'remove_onclick' => true,
                'align'          => 'text-center',
            ),
            'risk'            => array(
                'title'          => $this->module->l('Risk', 'AdminColissimoDashboardController'),
                'search'         => false,
                'remove_onclick' => true,
                'align'          => 'text-center',
                'callback'       => 'printRisk',
                'class'          => 'td-risk',
            ),
        );
    }

    /**
     * @param string $risk
     * @param array  $tr
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function printRisk($risk, $tr)
    {
        $order = new Order((int) $tr['id_order']);
        $history = $order->getHistory($this->context->language->id, (int) Configuration::get('PS_OS_DELIVERED'));
        if ($history) {
            $flag = 0;
        } else {
            $flag = (int) $tr['risk_value'] > 2 ? 1 : 0;
        }
        $this->context->smarty->assign(array('flag' => $flag, 'tr' => $tr));

        return $this->createTemplate('print-risk.tpl')->fetch();
    }

    /**
     * @param bool $forceUpdate
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function setNeedTrackingUpdate($forceUpdate = false)
    {
        if (!$forceUpdate && Configuration::get('COLISSIMO_LAST_TRACKING_UPDATE')) {
            $dateTime = new DateTime(date('Y-m-d H:i:s'));
            $dateTime->sub(new DateInterval('PT'.self::TIME_RANGE_UPDATE.'H'));
            $dateTimeLastUpdate = new DateTime(Configuration::get('COLISSIMO_LAST_TRACKING_UPDATE'));
            $diff = (int) $dateTime->getTimestamp() - $dateTimeLastUpdate->getTimestamp();
            if ($diff < 0) {
                return;
            }
        }
        $filteredStatuses = Configuration::getMultiple(array('PS_OS_DELIVERED', 'PS_OS_ERROR', 'PS_OS_CANCELED'));
        $dateTime = new DateTime(date('Y-m-d H:i:s'));
        if ($forceUpdate) {
            $dateTime->sub(new DateInterval('P90D'));
            $this->module->logger->info('Update tracking up to 90 days ago.');
        } else {
            $dateTime->sub(new DateInterval('P15D'));
            $this->module->logger->info('Update tracking up to 15 days ago.');
        }
        $dateAdd = $dateTime->format('Y-m-d H:i:s');
        $dbQuery = new DbQuery();
        $dbQuery->select('cola.id_colissimo_label')
            ->from('colissimo_label', 'cola')
            ->leftJoin('colissimo_order', 'co', 'co.id_colissimo_order = cola.id_colissimo_order')
            ->leftJoin('orders', 'o', 'o.id_order = co.id_order')
            ->where('o.current_state NOT IN('.implode(',', array_map('intval', $filteredStatuses)).')')
            ->where('o.date_add > "'.pSQL($dateAdd).'"')
            ->where('cola.return_label = 0'.Shop::addSqlRestriction(false, 'o'));

        $labelIds = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($dbQuery);
        if (is_array($labelIds) && !empty($labelIds)) {
            $this->ordersToUpdate = array_map(
                function ($element) {
                    return $element['id_colissimo_label'];
                },
                $labelIds
            );
        }
        Configuration::updateValue('COLISSIMO_LAST_TRACKING_UPDATE', date('Y-m-d H:i:s'));
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function ajaxProcessOrderDetails()
    {
        $idColissimoOrder = Tools::getValue('id_colissimo_order');
        $nbColumn = Tools::getValue('nb_col');
        $orderResumeContent = $this->module->getOrderDetailsHtml((int) $idColissimoOrder);
        $this->context->smarty->assign(array(
            'id_colissimo_order'        => $idColissimoOrder,
            'nb_col'                    => $nbColumn,
            'order_resume_content_html' => $orderResumeContent,
        ));
        $html = $this->createTemplate('_partials/td-order-resume.tpl')->fetch();
        $this->ajaxDie(
            json_encode(
                array(
                    'text' => 'ok',
                    'html' => $html,
                )
            )
        );
    }

    /**
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws SmartyException
     */
    public function ajaxProcessUpdateOrderTracking()
    {
        if (Tools::getValue('channel')) {
            $this->module->logger->setChannel(Tools::getValue('channel'));
        }
        $colissimoOrder = new ColissimoOrder((int) Tools::getValue('id_colissimo_order'));
        $this->module->logger->info('Update tracking for order #'.$colissimoOrder->id_order);
        if (!Validate::isLoadedObject($colissimoOrder)) {
            $return = array(
                'errors'  => true,
                'message' => $this->module->l('Cannot update order tracking.', 'AdminColissimoDashboardController'),
            );
            $this->ajaxDie(json_encode($return));
            die();
        }
        $errors = array();
        $success = array();
        $labelIds = $colissimoOrder->getLabelIds();
        foreach ($labelIds as $labelId) {
            $colissimoLabel = new ColissimoLabel((int) $labelId);
            try {
                $return = $this->module->updateOrderTracking($colissimoLabel);
            } catch (Exception $e) {
                $this->module->logger->error($e->getMessage());
                $errors[] = $e->getMessage();
                continue;
            }
            $success[] = $return;
        }
        $shipments = $colissimoOrder->getShipments($this->context->language->id);
        $this->context->smarty->assign(array('shipments' => $shipments, 'id_colissimo_order' => $colissimoOrder->id));
        $html = $this->createTemplate('_partials/shipments-table.tpl')->fetch();
        $return = array(
            'errors' => $errors,
            'success' => $success,
            'html' => $html,
        );
        $this->ajaxDie(json_encode($return));
    }

    /**
     *
     */
    public function ajaxProcessUpdateAllOrderTracking()
    {
        foreach ($this->ordersToUpdate as $labelId) {
            $colissimoLabel = new ColissimoLabel((int) $labelId);
            try {
                $this->module->updateOrderTracking($colissimoLabel);
            } catch (Exception $e) {
                $this->module->logger->error($e->getMessage());
                continue;
            }
        }
    }
}
