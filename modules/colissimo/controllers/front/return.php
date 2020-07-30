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
 * Class ColissimoReturnModuleFrontController
 *
 * Ajax processes:
 *  - showReturnAddress
 *  - checkAvailability
 *  - confirmPickup
 *
 */
class ColissimoReturnModuleFrontController extends ModuleFrontController
{
    /** @var bool $auth */
    public $auth = true;

    /** @var string $authRedirection */
    public $authRedirection = 'module-colissimo-return';

    /** @var Colissimo $module */
    public $module;

    /**
     * @return array
     */
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        $page['meta']['robots'] = 'noindex';
        $page['meta']['title'] = $this->module->l('Colissimo returns');

        return $page;
    }

    /**
     * @return bool
     */
    public function checkAccess()
    {
        if (!Configuration::get('COLISSIMO_ENABLE_RETURN') ||
            !Configuration::get('COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER')
        ) {
            $this->redirect_after = $this->context->link->getPageLink('my-account');
            $this->redirect();
        }

        return parent::checkAccess();
    }

    /**
     * @return bool|void
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->module->registerJs(
            'colissimo-module-front-return',
            'front.return.js',
            array('position' => 'bottom', 'priority' => 150)
        );
        $this->module->registerCSS('colissimo-module-front-css', 'colissimo.front.css');
        if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->module->registerCSS('colissimo-module-front-modal', 'colissimo.modal.css');
        }
    }

    /**
     * @param string $template
     * @param array  $params
     * @param null   $locale
     * @throws PrestaShopException
     */
    public function setTemplate($template, $params = array(), $locale = null)
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
            parent::setTemplate($template, $params, $locale);
        } else {
            parent::setTemplate('module:colissimo/views/templates/front/'.$template, $params, $locale);
        }
    }

    /**
     * @return array
     */
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }

    /**
     * @throws PrestaShopException
     */
    public function initContent()
    {
        parent::initContent();
        $shipments = $this->getColissimoOrdersByCustomer();
        $this->context->smarty->assign(
            array(
                'shipments'          => $shipments,
                'colissimo_img_path' => $this->module->getPathUri().'views/img/',
            )
        );
        $this->setTemplate($this->module->psFolder.'/return.tpl');
    }

    /**
     *
     */
    public function postProcess()
    {
        $idLabel = Tools::getValue('id_label');
        if (Tools::getValue('action') == 'downloadLabel' && $idLabel) {
            $this->module->logger->setChannel('FrontReturn');
            $label = new ColissimoLabel((int) $idLabel);
            $colissimoOrder = new ColissimoOrder((int) $label->id_colissimo_order);
            $order = new Order((int) $colissimoOrder->id_order);
            if ($order->id_customer != $this->context->customer->id || !$label->return_label) {
                return;
            }
            try {
                $label->download();
            } catch (Exception $e) {
                $this->module->logger->error(
                    sprintf('Error while downloading return label: %s', $e->getMessage()),
                    array(
                        'id_customer'        => $this->context->customer->id,
                        'id_colissimo_order' => $colissimoOrder->id_colissimo_order,
                        'id_order'           => $colissimoOrder->id_order,
                        'id_label'           => $label->id,
                    )
                );
                //@formatter:off
                $this->context->controller->errors[] = $this->module->l('An error occurred while downloading the return label. Please try again or contact our support.');
                //@formatter:on
            }
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getColissimoOrdersByCustomer()
    {
        //@formatter:off
        $dbQuery = new DbQuery();
        $dbQuery->select('co.id_colissimo_order')
            ->from('colissimo_order', 'co')
            ->leftJoin('orders', 'o', 'o.id_order = co.id_order')
            ->where('o.id_customer = '.(int) $this->context->customer->id.' AND o.id_shop = '.$this->context->shop->id)
            ->orderBy('o.date_add DESC');
        //@formatter:on
        $ids = array_map(
            function ($element) {
                return $element['id_colissimo_order'];
            },
            Db::getInstance(_PS_USE_SQL_SLAVE_)
              ->executeS($dbQuery)
        );
        $mailboxReturn = Configuration::get('COLISSIMO_ENABLE_MAILBOX_RETURN');
        $data = array();
        foreach ($ids as $id) {
            $colissimoOrder = new ColissimoOrder((int) $id);
            $labels = $colissimoOrder->getShipments($this->context->language->id);
            if (empty($labels)) {
                continue;
            }
            foreach ($labels as $label) {
                if (isset($label['id_return_label'])) {
                    $colissimoLabel = new ColissimoLabel((int) $label['id_return_label']);
                    $mailboxReturnText = '';
                    if ($colissimoLabel->hasMailboxPickup()) {
                        $details = $colissimoLabel->getMailboxPickupDetails();
                        if (isset($details['pickup_date']) &&
                            $details['pickup_date'] &&
                            isset($details['pickup_before']) &&
                            $details['pickup_before']
                        ) {
                            $mailboxReturnText = sprintf(
                                $this->module->l('Pickup on %s before %s'),
                                Tools::displayDate(date('Y-m-d', $details['pickup_date'])),
                                $details['pickup_before']
                            );
                        }
                    }
                    $order = new Order((int) $colissimoOrder->id_order);
                    $orderState = new OrderState((int) $order->current_state, $this->context->language->id);
                    $data[] = array(
                        'reference'           => $order->reference,
                        'date'                => Tools::displayDate($order->date_add, null, false),
                        'status'              => array(
                            'name'     => $orderState->name,
                            'contrast' => (Tools::getBrightness($orderState->color) > 128) ? 'dark' : 'bright',
                            'color'    => $orderState->color,
                        ),
                        'return_label'        => array(
                            'id'              => $label['id_return_label'],
                            'shipping_number' => $label['return_shipping_number'],
                        ),
                        'return_file_deleted' => $label['return_file_deleted'],
                        'mailbox_return'      => $mailboxReturn && $colissimoLabel->isFranceReturnLabel(),
                        'mailbox_return_text' => $mailboxReturnText,
                    );
                }
            }
        }

        return $data;
    }

    /**
     * @param ColissimoLabel $colissimoLabel
     * @return bool
     */
    public function checkLabelAccess($colissimoLabel)
    {
        $colissimoOrder = new ColissimoOrder((int) $colissimoLabel->id_colissimo_order);
        $order = new Order((int) $colissimoOrder->id_order);
        if ($order->id_customer != $this->context->customer->id) {
            return false;
        }

        return true;
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function displayAjaxShowReturnAddress()
    {
        $idColissimoLabel = Tools::getValue('id_colissimo_label');
        $colissimoLabel = new ColissimoLabel((int) $idColissimoLabel);
        if (!Validate::isLoadedObject($colissimoLabel) || !$this->checkLabelAccess($colissimoLabel)) {
            $return = array(
                'error'   => true,
                'message' => $this->module->l(
                    'An unexpected error occurred. Please try again or contact our customer service'
                ),
            );
            $this->ajaxDie(json_encode($return));
        }
        $colissimoOrder = new ColissimoOrder((int) $colissimoLabel->id_colissimo_order);
        $colissimoService = new ColissimoService((int) $colissimoOrder->id_colissimo_service);
        $order = new Order((int) $colissimoOrder->id_order);
        if ($colissimoService->is_pickup) {
            $address = new Address((int) $order->id_address_invoice);
        } else {
            $address = new Address((int) $order->id_address_delivery);
        }
        $this->context->smarty->assign(array('address' => $address, 'id_colissimo_label' => $idColissimoLabel));
        $psFolder = $this->module->psFolder;
        $tpl = $this->module->getTemplatePath($psFolder.'/_partials/colissimo-return-modal-body-address.tpl');
        $html = $this->context->smarty->fetch($tpl);
        $return = array(
            'error'   => false,
            'message' => '',
            'html'    => $html,
        );
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function displayAjaxCheckAvailability()
    {
        $this->module->logger->setChannel('MailboxReturn');
        $idColissimoLabel = Tools::getValue('id_colissimo_label');
        $colissimoLabel = new ColissimoLabel((int) $idColissimoLabel);
        if (!$this->checkLabelAccess($colissimoLabel)) {
            $return = array(
                'error'   => true,
                'message' => $this->module->l(
                    'An unexpected error occurred. Please try again or contact our customer service'
                ),
            );
            $this->ajaxDie(json_encode($return));
        }
        $senderAddress = array(
            'line0'       => '',
            'line1'       => '',
            'line2'       => Tools::getValue('colissimo-address1'),
            'line3'       => Tools::getValue('colissimo-address2'),
            'countryCode' => 'FR',
            'zipCode'     => Tools::getValue('colissimo-postcode'),
            'city'        => Tools::getValue('colissimo-city'),
        );
        $mailboxDetailsRequest = new ColissimoMailboxDetailsRequest(ColissimoTools::getCredentials());
        $mailboxDetailsRequest->setSenderAddress($senderAddress)
                              ->buildRequest();
        $client = new ColissimoClient();
        $this->module->logger->info(
            'Request mailbox details',
            array('request' => json_decode($mailboxDetailsRequest->getRequest(true), true))
        );
        $client->setRequest($mailboxDetailsRequest);
        try {
            /** @var ColissimoMailboxDetailsResponse $response */
            $response = $client->request();
        } catch (Exception $e) {
            $this->module->logger->error($e->getMessage());
            $return = array(
                'error'   => true,
                'message' => $e->getMessage(),
            );
            $this->ajaxDie(json_encode($return));
        }
        if ($response->messages[0]['id']) {
            foreach ($response->messages as $message) {
                $this->module->logger->error(
                    'Error found',
                    sprintf('(%s) - %s', $message['id'], $message['messageContent'])
                );
            }
            $return = array(
                'error'   => true,
                'message' => $this->module->l('An error occurred. Please check your address and try again.'),
            );
            $this->ajaxDie(json_encode($return));
        }
        $this->module->logger->info('Mailbox dates response', array('response' => $response));
        $pickingDate = date('Y-m-d', $response->pickingDates[0]).' 00:00:00';

        $this->context->smarty->assign(
            array(
                'max_picking_hour'     => $response->maxPickingHour,
                'validity_time'        => $response->validityTime,
                'picking_date'         => $response->pickingDates[0],
                'picking_date_display' => Tools::displayDate($pickingDate),
                'id_colissimo_label'   => $idColissimoLabel,
                'picking_address'      => array(
                    'company'   => Tools::getValue('colissimo-company'),
                    'lastname'  => Tools::getValue('colissimo-lastname'),
                    'firstname' => Tools::getValue('colissimo-firstname'),
                    'address1'  => Tools::getValue('colissimo-address1'),
                    'address2'  => Tools::getValue('colissimo-address2'),
                    'postcode'  => Tools::getValue('colissimo-postcode'),
                    'city'      => Tools::getValue('colissimo-city'),
                ),
            )
        );
        //@formatter:off
        $psFolder = $this->module->psFolder;
        $tpl = $this->module->getTemplatePath($psFolder.'/_partials/colissimo-return-modal-body-dates.tpl');
        $html = $this->context->smarty->fetch($tpl);
        //@formatter:on
        $return = array(
            'error' => false,
            'html'  => $html,
        );
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws SmartyException
     */
    public function displayAjaxConfirmPickup()
    {
        $this->module->logger->setChannel('MailboxReturn');
        $idColissimoLabel = Tools::getValue('id_colissimo_label');
        $pickupDate = Tools::getValue('mailbox_date');
        $pickupBefore = Tools::getValue('mailbox_hour');
        $colissimoLabel = new ColissimoLabel((int) $idColissimoLabel);
        $errorMessage = '';
        if (Validate::isLoadedObject($colissimoLabel) && $this->checkLabelAccess($colissimoLabel)) {
            if (!$colissimoLabel->hasMailboxPickup()) {
                $senderAddress = array(
                    'companyName' => Tools::getValue('mailbox_company'),
                    'lastName'    => Tools::getValue('mailbox_lastname'),
                    'firstName'   => Tools::getValue('mailbox_firstname'),
                    'line2'       => Tools::getValue('mailbox_address1'),
                    'line3'       => Tools::getValue('mailbox_address2'),
                    'zipCode'     => Tools::getValue('mailbox_postcode'),
                    'city'        => Tools::getValue('mailbox_city'),
                    'countryCode' => 'FR',
                    'email'       => Tools::getValue('mailbox_email'),
                );
                $date = date('Y-m-d', Tools::getValue('mailbox_date'));
                $pickupRequest = new ColissimoPlanPickupRequest(ColissimoTools::getCredentials());
                $pickupRequest->setParcelNumber($colissimoLabel->shipping_number)
                              ->setSenderAddress($senderAddress)
                              ->setMailboxPickingDate($date)
                              ->buildRequest();
                $client = new ColissimoClient();
                $client->setRequest($pickupRequest);
                $this->module->logger->info(
                    'Mailbox pickup request',
                    array('request' => json_decode($pickupRequest->getRequest(true), true))
                );
                try {
                    /** @var ColissimoPlanPickupResponse $response */
                    $response = $client->request();
                } catch (Exception $e) {
                    $this->module->logger->error('Error thrown: '.$e->getMessage());
                }
                if (isset($response) && !$response->messages[0]['id']) {
                    $this->module->logger->info('Mailbox pickup response', $response->response);
                    $insert = Db::getInstance()
                                ->insert(
                                    'colissimo_mailbox_return',
                                    array(
                                        'id_colissimo_label' => (int) $idColissimoLabel,
                                        'pickup_date'        => pSQL($pickupDate),
                                        'pickup_before'      => pSQL($pickupBefore),
                                    )
                                );
                    if ($insert) {
                        $hasError = false;
                    } else {
                        // Cannot insert
                        $this->module->logger->error('Cannot insert mailbox request in DB.');
                        $hasError = true;
                        $errorMessage = '';
                    }
                } else {
                    // Error thrown or error found
                    $this->module->logger->error('Errors found.', array('messages' => $response->messages));
                    $hasError = true;
                    $errorMessage = '';
                }
            } else {
                // Pickup request already sent
                $this->module->logger->error('A pickup request has already been sent for this return.');
                $hasError = true;
                $errorMessage = $this->module->l('A pickup request has already been sent for this return.');
            }
        } else {
            // Invalid label
            $this->module->logger->error('Invalid label');
            $hasError = true;
            $errorMessage = '';
        }
        $this->context->smarty->assign(
            array(
                'has_error'     => $hasError,
                'error_message' => $errorMessage,
            )
        );
        //@formatter:off
        $psFolder = $this->module->psFolder;
        $tpl = $this->module->getTemplatePath($psFolder.'/_partials/colissimo-return-modal-body-result.tpl');
        $html = $this->context->smarty->fetch($tpl);
        //@formatter:on
        $htmlText = sprintf(
            $this->module->l('Pickup on %s before %s'),
            Tools::displayDate(date('Y-m-d', $pickupDate)),
            $pickupBefore
        );
        $return = array(
            'error'              => $hasError,
            'html'               => $html,
            'text_result'        => $htmlText,
            'id_colissimo_label' => $idColissimoLabel,
        );
        $this->ajaxDie(json_encode($return));
    }
}
