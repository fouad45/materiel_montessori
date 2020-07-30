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
 * Class AdminColissimoLabelController
 *
 * Ajax processes:
 *  - generateReturn
 *
 * Processes:
 *  - downloadLabel
 *  - viewLabel
 *  - downloadCN23
 *  - viewCN23
 *  - downloadAllLabels
 *  - downloadAllReturnLabels
 *  - downloadAllCN23
 *  - deletePDFDocuments
 *  - printAllDocuments
 *
 */
class AdminColissimoLabelController extends ModuleAdminController
{
    /** @var Colissimo $module */
    public $module;

    /**
     *
     */
    public function processDownloadLabel()
    {
        $idLabel = (int) Tools::getValue('id_label');
        $colissimoLabel = new ColissimoLabel((int) $idLabel);
        if (Validate::isLoadedObject($colissimoLabel)) {
            try {
                $colissimoLabel->download();
            } catch (Exception $e) {
                $this->module->logger->error($e->getMessage());
                //@formatter:off
                $this->errors[] = $this->module->l('Label download failed. Please check the module logs.', 'AdminColissimoLabelController');
                //@formatter:on
            }
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function processViewLabel()
    {
        $idLabel = (int) Tools::getValue('id_label');
        $colissimoLabel = new ColissimoLabel((int) $idLabel);
        if (Validate::isLoadedObject($colissimoLabel)) {
            try {
                $colissimoLabel->view();
            } catch (Exception $e) {
                $this->module->logger->error($e->getMessage());
                //@formatter:off
                $this->errors[] = $this->module->l('Label view failed. Please check the module logs.', 'AdminColissimoLabelController');
                //@formatter:on
            }
        }
    }

    /**
     *
     */
    public function processDownloadCN23()
    {
        $idLabel = (int) Tools::getValue('id_label');
        $colissimoLabel = new ColissimoLabel((int) $idLabel);
        if (Validate::isLoadedObject($colissimoLabel)) {
            try {
                $colissimoLabel->downloadCN23();
            } catch (Exception $e) {
                $this->module->logger->error($e->getMessage());
                //@formatter:off
                $this->errors[] = $this->module->l('CN23 download failed. Please check the module logs.', 'AdminColissimoLabelController');
                //@formatter:on
            }
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function processViewCN23()
    {
        $idLabel = (int) Tools::getValue('id_label');
        $colissimoLabel = new ColissimoLabel((int) $idLabel);
        if (Validate::isLoadedObject($colissimoLabel)) {
            try {
                $colissimoLabel->viewCN23();
            } catch (Exception $e) {
                $this->module->logger->error($e->getMessage());
                //@formatter:off
                $this->errors[] = $this->module->l('CN23 view failed. Please check the module logs.', 'AdminColissimoLabelController');
                //@formatter:on
            }
        }
    }

    /**
     *
     */
    public function processDownloadDocuments()
    {
        $fileType = Tools::getValue('colissimo_file_type');
        $labelIds = Tools::getValue('colissimo_label_ids');
        $labelIds = json_decode($labelIds);
        switch ($fileType) {
            case 'labels':
                $documents = $this->getAllLabels($labelIds);
                $filename = 'colissimo_labels_'.date('Ymd_His').'.zip';
                try {
                    ColissimoTools::downloadDocuments($documents['label'], $filename);
                } catch (Exception $e) {
                    $this->module->logger->error(sprintf('Error while downloading zip: %s', $e->getMessage()));
                    //@formatter:off
                    $this->context->controller->errors[] = $this->module->l('Cannot generate zip file.', 'AdminColissimoLabelController');
                    //@formatter:on
                }

                break;
            case 'return_labels':
                $documents = $this->getAllReturnLabels($labelIds);
                $filename = 'colissimo_return_labels_'.date('Ymd_His').'.zip';
                try {
                    ColissimoTools::downloadDocuments($documents['label'], $filename);
                } catch (Exception $e) {
                    $this->module->logger->error(sprintf('Error while downloading zip: %s', $e->getMessage()));
                    //@formatter:off
                    $this->context->controller->errors[] = $this->module->l('Cannot generate zip file.', 'AdminColissimoLabelController');
                    //@formatter:on
                }

                break;
            case 'cn23':
                $documents = $this->getAllCN23($labelIds);
                $filename = 'colissimo_cn23_'.date('Ymd_His').'.zip';
                //@formatter:off
                if (!ColissimoTools::downloadCN23Documents($documents['cn23'], $filename)) {
                    $this->context->controller->errors[] = $this->module->l('Cannot generate zip file.', 'AdminColissimoLabelController');
                }
                //@formatter:on

                break;
            case 'all':
            default:
                $documents = $this->getAllDocuments($labelIds);
                $filename = 'colissimo_documents_'.date('Ymd_His').'.zip';
                try {
                    ColissimoTools::downloadAllDocuments($documents, $filename);
                } catch (Exception $e) {
                    $this->module->logger->error(sprintf('Error while downloading zip: %s', $e->getMessage()));
                    //@formatter:off
                    $this->context->controller->errors[] = $this->module->l('Cannot generate zip file.', 'AdminColissimoLabelController');
                    //@formatter:on
                }

                break;
        }
    }

    /**
     * @param array $labelIds
     * @param bool  $pdfOnly
     * @return array
     */
    public function getAllLabels($labelIds, $pdfOnly = false)
    {
        $documents = array('label' => array());
        foreach ($labelIds as $labelId) {
            try {
                $colissimoLabel = new ColissimoLabel((int) $labelId);
                if (Validate::isLoadedObject($colissimoLabel)) {
                    if (!$pdfOnly || $colissimoLabel->label_format == 'pdf') {
                        $documents['label'][] = $colissimoLabel;
                    }
                }
            } catch (Exception $e) {
                continue;
            }
        }

        return $documents;
    }

    /**
     * @param array $labelIds
     * @return array
     */
    public function getAllReturnLabels($labelIds)
    {
        $documents = array('label' => array());
        foreach ($labelIds as $labelId) {
            try {
                $colissimoLabel = new ColissimoLabel((int) $labelId);
                if (Validate::isLoadedObject($colissimoLabel)) {
                    $returnLabelId = $colissimoLabel->getReturnLabelId();
                    if ($returnLabelId) {
                        $returnLabel = new ColissimoLabel((int) $returnLabelId);
                        if (Validate::isLoadedObject($returnLabel)) {
                            $documents['label'][] = $returnLabel;
                        }
                    }
                }
            } catch (Exception $e) {
                continue;
            }
        }

        return $documents;
    }

    /**
     * @param array $labelIds
     * @return array
     */
    public function getAllCN23($labelIds)
    {
        $documents = array('cn23' => array());
        foreach ($labelIds as $labelId) {
            try {
                $colissimoLabel = new ColissimoLabel((int) $labelId);
                if (Validate::isLoadedObject($colissimoLabel)) {
                    if ($colissimoLabel->cn23) {
                        $documents['cn23'][] = $colissimoLabel;
                    }
                    $returnLabelId = $colissimoLabel->getReturnLabelId();
                    if ($returnLabelId) {
                        $returnLabel = new ColissimoLabel((int) $returnLabelId);
                        if (Validate::isLoadedObject($returnLabel)) {
                            if ($returnLabel->cn23) {
                                $documents['cn23'][] = $returnLabel;
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                continue;
            }
        }

        return $documents;
    }

    /**
     * @param array $labelIds
     * @param bool  $pdfOnly
     * @return array
     */
    public function getAllDocuments($labelIds, $pdfOnly = false)
    {
        $documents = array(
            'label' => array(),
            'cn23'  => array(),
        );
        foreach ($labelIds as $labelId) {
            try {
                $colissimoLabel = new ColissimoLabel((int) $labelId);
                if (Validate::isLoadedObject($colissimoLabel)) {
                    if (!$pdfOnly || $colissimoLabel->label_format == 'pdf') {
                        $documents['label'][] = $colissimoLabel;
                    }
                    if ($colissimoLabel->cn23) {
                        $documents['cn23'][] = $colissimoLabel;
                    }
                    $returnLabelId = $colissimoLabel->getReturnLabelId();
                    if ($returnLabelId) {
                        $returnLabel = new ColissimoLabel((int) $returnLabelId);
                        if (Validate::isLoadedObject($returnLabel)) {
                            $documents['label'][] = $returnLabel;
                            if ($returnLabel->cn23) {
                                $documents['cn23'][] = $returnLabel;
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                continue;
            }
        }

        return $documents;
    }

    /**
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws SmartyException
     */
    public function ajaxProcessGenerateReturn()
    {
        $colissimoLabel = new ColissimoLabel((int) Tools::getValue('id_colissimo_label'));
        $colissimoOrder = new ColissimoOrder((int) $colissimoLabel->id_colissimo_order);
        $order = new Order((int) $colissimoOrder->id_order);
        $customerAddress = new Address((int) $order->id_address_delivery);
        $merchantAddress = ColissimoMerchantAddress::getMerchantReturnAddress();
        $customerCountry = $customerAddress->id_country;
        $returnDestinationType = ColissimoTools::getReturnDestinationTypeByIdCountry($customerCountry);
        if ($returnDestinationType === false) {
            //@formatter:off
            $return = array(
                'id'      => false,
                'error'   => true,
                'message' => $this->module->l('Cannot edit return label for this destination.', 'AdminColissimoLabelController'),
                'cn23'    => false,
            );
            //@formatter:on
            $this->ajaxDie(json_encode($return));
        }
        $idService = ColissimoService::getServiceIdByIdCarrierDestinationType(0, $returnDestinationType);

        $data = array(
            'order'                     => $order,
            'version'                   => $this->module->version,
            'cart'                      => new Cart((int) $order->id_cart),
            'customer'                  => new Customer((int) $order->id_customer),
            'colissimo_order'           => $colissimoOrder,
            'colissimo_service'         => new ColissimoService((int) $idService),
            'colissimo_service_initial' => new ColissimoService((int) $colissimoOrder->id_colissimo_service),
            'customer_addr'             => $customerAddress,
            'merchant_addr'             => $merchantAddress,
            'form_options'              => array(
                'insurance'    => 0,
                'ta'           => 0,
                'd150'         => 0,
                'weight'       => ColissimoTools::getOrderTotalWeightInKg($order),
                'mobile_phone' => $merchantAddress->phoneNumber,
            ),
        );
        $colissimoReturnLabel = false;
        try {
            $this->module->labelGenerator->setData($data);
            $colissimoReturnLabel = $this->module->labelGenerator->generateReturn($colissimoLabel);
        } catch (Exception $e) {
            $this->module->logger->error('Exception throw while generating return label.', $e->getMessage());
            $return = array(
                'id'      => false,
                'error'   => true,
                'message' => $e->getMessage(),
                'cn23'    => false,
            );
            $this->ajaxDie(json_encode($return));
        }
        $warningMessage = false;
        if (Tools::getValue('send_mail') && Validate::isLoadedObject($colissimoReturnLabel)) {
            try {
                $iso = Language::getIsoById($order->id_lang);
                $iso = isset($this->module->returnLabelMailObject[$iso]) ? $iso : 'en';
                ColissimoTools::sendReturnLabelMail($colissimoReturnLabel, $this->module->returnLabelMailObject[$iso]);
            } catch (Exception $e) {
                $this->module->logger->error($e->getMessage());
                //@formatter:off
                $warningMessage = $this->module->l('Could not send label to customer.', 'AdminColissimoLabelController');
                //@formatter:on
            }
        }

        $shipments = $colissimoOrder->getShipments($this->context->language->id);
        $this->context->smarty->assign(
            array(
                'shipments'          => $shipments,
                'link'               => $this->context->link,
                'id_colissimo_order' => $colissimoOrder->id,
            )
        );
        $html = $this->context->smarty->fetch(
            $this->module->getLocalPath().'views/templates/admin/colissimo_dashboard/_partials/shipments-table.tpl'
        );
        $return = array(
            'error'           => false,
            'message'         => $this->module->l('Return label generated.', 'AdminColissimoLabelController'),
            'warning_message' => $warningMessage,
            'success'         => array(),
            'html'            => $html,
        );
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function ajaxProcessDeleteLabel()
    {
        $idLabel = (int) Tools::getValue('id_colissimo_label');
        $colissimoLabel = new ColissimoLabel((int) $idLabel);
        $return = array(
            'error'   => false,
            'message' => $this->module->l('Label deleted successfully.', 'AdminColissimoLabelController'),
        );
        if (Validate::isLoadedObject($colissimoLabel)) {
            $colissimoOrder = new ColissimoOrder((int) $colissimoLabel->id_colissimo_order);
            if (Validate::isLoadedObject($colissimoOrder)) {
                $idColissimoReturnLabel = $colissimoLabel->getReturnLabelId();
                if ($idColissimoReturnLabel) {
                    $colissimoReturnLabel = new ColissimoLabel((int) $idColissimoReturnLabel);
                    if (!Validate::isLoadedObject($colissimoReturnLabel)) {
                        $this->module->logger->warning(
                            'Invalid return label object.',
                            array('colissimo_label' => $idLabel)
                        );
                        //@formatter:off
                        $return = array(
                            'error'   => true,
                            'message' => $this->module->l('Invalid return label. Please refresh the page.', 'AdminColissimoLabelController'),
                        );
                        //@formatter:on
                    } else {
                        try {
                            $colissimoReturnLabel->deleteFile();
                        } catch (Exception $e) {
                            $this->module->logger->warning($e->getMessage());
                        }
                        try {
                            $colissimoReturnLabel->delete();
                        } catch (Exception $e) {
                            $this->module->logger->warning($e->getMessage());
                        }
                    }
                }
                if ($colissimoLabel->cn23) {
                    try {
                        $colissimoLabel->deleteCN23();
                    } catch (Exception $e) {
                        $this->module->logger->warning($e->getMessage());
                    }
                }
                $orderCarrier = ColissimoOrderCarrier::getByIdOrder($colissimoOrder->id_order);
                $newShippingNumber = false;
                if (Validate::isLoadedObject($orderCarrier)) {
                    if ($orderCarrier->tracking_number == $colissimoLabel->shipping_number) {
                        $newShippingNumber = $colissimoLabel->getNextShippingNumber();
                    }
                }
                try {
                    $colissimoLabel->deleteFile();
                } catch (Exception $e) {
                    $this->module->logger->warning($e->getMessage());
                }
                try {
                    $colissimoLabel->delete();
                    if ($newShippingNumber !== false) {
                        $orderCarrier->tracking_number = pSQL($newShippingNumber);
                        $orderCarrier->save();
                    }
                } catch (Exception $e) {
                    $this->module->logger->warning($e->getMessage());
                }
            } else {
                $this->module->logger->warning('Invalid Colissimo order object.', array('colissimo_label' => $idLabel));
                //@formatter:off
                $return = array(
                    'error'   => true,
                    'message' => $this->module->l('Invalid Colissimo order. Please refresh the page', 'AdminColissimoLabelController'),
                    'html'    => '',
                );
                //@formatter:on
                $this->ajaxDie(json_encode($return));
            }
        } else {
            $this->module->logger->warning('Invalid label object.', array('colissimo_label' => $idLabel));
            //@formatter:off
            $return = array(
                'error'   => true,
                'message' => $this->module->l('Invalid label. Please refresh the page', 'AdminColissimoLabelController'),
                'html'    => '',
            );
            //@formatter:on
            $this->ajaxDie(json_encode($return));
        }
        $shipments = $colissimoOrder->getShipments($this->context->language->id);
        $this->context->smarty->assign(
            array(
                'shipments'          => $shipments,
                'link'               => $this->context->link,
                'id_colissimo_order' => $colissimoOrder->id,
            )
        );
        $html =
            $this->context->smarty->fetch(
                $this->module->getLocalPath().'views/templates/admin/colissimo_dashboard/_partials/shipments-table.tpl'
            );
        $return['html'] = $html;
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function ajaxProcessMailReturnLabel()
    {
        $idReturnLabel = (int) Tools::getValue('id_colissimo_return_label');
        $colissimoReturnLabel = new ColissimoLabel((int) $idReturnLabel);
        if (Validate::isLoadedObject($colissimoReturnLabel)) {
            try {
                $colissimoOrder = new ColissimoOrder((int) $colissimoReturnLabel->id_colissimo_order);
                $order = new Order((int) $colissimoOrder->id_order);
                $iso = Language::getIsoById($order->id_lang);
                $iso = isset($this->module->returnLabelMailObject[$iso]) ? $iso : 'en';
                ColissimoTools::sendReturnLabelMail($colissimoReturnLabel, $this->module->returnLabelMailObject[$iso]);
            } catch (Exception $e) {
                $this->module->logger->error($e->getMessage());
                //@formatter:off
                $return = array(
                    'error'   => true,
                    'message' => $this->module->l('Cannot send return label to customer.', 'AdminColissimoLabelController'),
                );
                //@formatter:on
                $this->ajaxDie(json_encode($return));
            }
        } else {
            $return = array(
                'error'   => true,
                'message' => $this->module->l('Invalid return label.', 'AdminColissimoLabelController'),
            );
            $this->ajaxDie(json_encode($return));
        }
        $return = array(
            'error'   => false,
            'message' => $this->module->l('Mail sent successfully.', 'AdminColissimoLabelController'),
        );
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @throws PrestaShopException
     */
    public function ajaxProcessPrintDocuments()
    {
        require_once(dirname(__FILE__).'/../../vendor/autoload.php');

        $fileType = Tools::getValue('colissimo_file_type');
        $labelIds = Tools::getValue('colissimo_label_ids');
        $labelIds = json_decode($labelIds);
        switch ($fileType) {
            case 'labels':
                $documents = $this->getAllLabels($labelIds, true);
                break;
            case 'return_labels':
                $documents = $this->getAllReturnLabels($labelIds);

                break;
            case 'cn23':
                $documents = $this->getAllCN23($labelIds);
                break;
            case 'all':
            default:
                $documents = $this->getAllDocuments($labelIds, true);

                break;
        }

        if (!empty($documents['label']) || !empty($documents['cn23'])) {
            $pdf = new \Clegginabox\PDFMerger\PDFMerger();
            $base64 = '';
            if (isset($documents['label'])) {
                foreach ($documents['label'] as $document) {
                    /** @var ColissimoLabel $document */
                    try {
                        $pdf->addPDF($document->getFilePath());
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
            if (isset($documents['cn23'])) {
                foreach ($documents['cn23'] as $document) {
                    /** @var ColissimoLabel $document */
                    try {
                        $pdf->addPDF($document->getCN23Path());
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
            try {
                $base64 = base64_encode($pdf->merge('string'));
            } catch (Exception $e) {
                $this->module->logger->error($e->getMessage());
                $return = array(
                    'error'   => true,
                    'message' => $this->module->l('Cannot concatenate PDF documents.', 'AdminColissimoLabelController'),
                );
                $this->ajaxDie(json_encode($return));
            }
            $return = array(
                'error'       => false,
                'message'     => '',
                'file_string' => $base64,
            );
            $this->ajaxDie(json_encode($return));
        } else {
            //@formatter:off
            $return = array(
                'error'   => true,
                'message' => $this->module->l('There are no PDF documents to print.', 'AdminColissimoLabelController'),
            );
            //@formatter:on
            $this->ajaxDie(json_encode($return));
        }
    }
}
