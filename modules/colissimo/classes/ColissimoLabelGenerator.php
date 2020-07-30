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
 * Class ColissimoLabelGenerator
 */
class ColissimoLabelGenerator
{
    const CATEGORY_GIFT = 1;
    const CATEGORY_SAMPLES = 2;
    const CATEGORY_COMMERCIAL = 3;
    const CATEGORY_DOC = 4;
    const CATEGORY_OTHER = 5;
    const CATEGORY_MERCHANDISE_RETURN = 6;
    const COLISHIP_CATEGORY_GIFT = 2;
    const COLISHIP_CATEGORY_SAMPLES = 0;
    const COLISHIP_CATEGORY_COMMERCIAL = 4;
    const COLISHIP_CATEGORY_DOC = 1;
    const COLISHIP_CATEGORY_OTHER = 5;
    const COLISHIP_CATEGORY_MERCHANDISE_RETURN = 3;

    /** @var array $data */
    private $data;

    /** @var ColissimoLogger $logger */
    private $logger;

    /** @var ColissimoGenerateLabelRequest $request */
    private $request;

    /**
     * ColissimoLabelGenerator constructor.
     * @param ColissimoLogger $logger
     */
    public function __construct($logger)
    {
        $this->logger = $logger;
        $this->logger->setChannel('GenerateLabel');
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getProductsForCustomsDeclaration()
    {
        /** @var Order $order */
        $order = $this->data['order'];
        $currency = new Currency((int) $order->id_currency);
        $products = $order->getProducts();
        $totalWeight = $order->getTotalWeight();
        $shortDescription = Configuration::get('COLISSIMO_DEFAULT_CONTENTS');
        $originCountry = 'FR';
        $hsCode = Configuration::get('COLISSIMO_DEFAULT_HS_CODE');
        $articles = array();
        foreach ($products as $product) {
            $productCustomDetails = ColissimoCustomProduct::getByIdProduct((int) $product['product_id']);
            $categoryCustomDetails = ColissimoCustomCategory::getByIdCategory((int) $product['id_category_default']);

            if ($productCustomDetails->short_desc) {
                $shortDescription = $productCustomDetails->short_desc;
            } elseif ($categoryCustomDetails->short_desc) {
                $shortDescription = $categoryCustomDetails->short_desc;
            }
            if ($productCustomDetails->hs_code) {
                $hsCode = $productCustomDetails->hs_code;
            } elseif ($categoryCustomDetails->hs_code) {
                $hsCode = $categoryCustomDetails->hs_code;
            }
            if ($productCustomDetails->id_country_origin) {
                $originCountry = Country::getIsoById((int) $productCustomDetails->id_country_origin);
            } elseif ($categoryCustomDetails->id_country_origin) {
                $originCountry = Country::getIsoById((int) $categoryCustomDetails->id_country_origin);
            }
            $weight = (float) $product['product_weight'] ? ColissimoTools::weightInKG($product['product_weight']) : 0.05;
            $articles[] = array(
                'description'   => $shortDescription,
                'quantity'      => $product['product_quantity'],
                'weight'        => $weight,
                'value'         => (float) $product['unit_price_tax_excl'],
                'hsCode'        => $hsCode,
                'originCountry' => $originCountry,
                'currency'      => $currency->iso_code,
                'artref'        => $product['product_reference'],
                'originalIdent' => 'A',
            );
        }

        return $articles;
    }

    /**
     * @param bool $forcePdf
     */
    public function setLabelOutput($forcePdf = false)
    {
        $output = array(
            'x'                  => 0,
            'y'                  => 0,
            'outputPrintingType' => $forcePdf ? 'PDF_A4_300dpi' : Configuration::get('COLISSIMO_LABEL_FORMAT'),
        );
        $this->request->setOutput($output);
    }

    /**
     * @throws Exception
     */
    public function setLabelService()
    {
        $isoEUCountriesWithCN23 = ColissimoTools::$isoEUCountriesWithCN23;
        $isoEUCountries = ColissimoTools::$isoEUCountries;
        if (Configuration::get('COLISSIMO_ENABLE_BREXIT')) {
            $isoEUCountries = array_diff($isoEUCountries, array('GB'));
        }
        /** @var Order $order */
        $order = $this->data['order'];
        /** @var ColissimoService $colissimoService */
        $colissimoService = $this->data['colissimo_service'];
        if ($colissimoService->type == ColissimoService::TYPE_RELAIS) {
            $this->logger->info('Shipping type: PICKUP POINT DELIVERY');
            $pickupPoint = new ColissimoPickupPoint((int) $this->data['colissimo_order']->id_colissimo_pickup_point);
            $services = array(
                'productCode' => $pickupPoint->getProductCodeForAffranchissement(),
                'depositDate' => date('Y-m-d'),
            );
        } else {
            $this->logger->info('Shipping type: HOME DELIVERY');
            $services = array(
                'productCode' => $colissimoService->product_code,
                'depositDate' => date('Y-m-d'),
            );
        }
        $shippingAmountEUR = ColissimoTools::convertInEUR(
            $order->total_shipping_tax_excl,
            new Currency($order->id_currency)
        );
        $shippingAmountEUR = (float) $shippingAmountEUR ? $shippingAmountEUR : 0.01;
        $services['transportationAmount'] = (int) ($shippingAmountEUR * 100);
        $merchantAddress = new ColissimoMerchantAddress('sender');
        /** @var Address $customerAddress */
        $customerAddress = $this->data['customer_addr'];
        $isoTo = Country::getIsoById((int) $customerAddress->id_country);
        $isoFrom = $merchantAddress->countryCode;
        if (ColissimoTools::needCN23($isoFrom, $isoTo, $customerAddress->postcode)) {
            $services['totalAmount'] = $services['transportationAmount'];
            $services['returnTypeChoice'] = 3;
        }
        $services['orderNumber'] = $this->data['order']->reference;
        if ($colissimoService->type == ColissimoService::TYPE_RELAIS) {
            $services['commercialName'] = $this->data['customer_addr']->company;
        }
        $this->request->setShipmentServices($services);
    }

    /**
     *
     */
    public function setLabelCustomsDeclarations()
    {
        $merchantAddress = new ColissimoMerchantAddress('sender');
        /** @var Address $customerAddress */
        $customerAddress = $this->data['customer_addr'];
        $isoTo = Country::getIsoById((int) $customerAddress->id_country);
        $isoFrom = $merchantAddress->countryCode;
        if (ColissimoTools::needCN23($isoFrom, $isoTo, $customerAddress->postcode)) {
            $customsDeclaration = array(
                'includeCustomsDeclarations' => 1,
                'contents'                   => array(
                    'article'  => $this->getProductsForCustomsDeclaration(),
                    'category' => array(
                        'value' => self::CATEGORY_COMMERCIAL,
                    ),
                ),
            );
            $this->logger->info('Include customs declarations', array('data', $customsDeclaration));
            $this->request->setCustomsOptions($customsDeclaration);
        }
    }

    /**
     * @param ColissimoLabel $colissimoLabel
     * @throws Exception
     */
    public function setReturnLabelCustomsDeclarations($colissimoLabel)
    {
        $merchantAddress = new ColissimoMerchantAddress('sender');
        /** @var Address $customerAddress */
        $customerAddress = $this->data['customer_addr'];
        $isoTo = Country::getIsoById((int) $customerAddress->id_country);
        $isoFrom = $merchantAddress->countryCode;
        if (ColissimoTools::needCN23($isoFrom, $isoTo, $customerAddress->postcode)) {
            if (Tools::substr($this->data['order']->invoice_date, 0, 10) != '0000-00-00') {
                $invoiceDate = Tools::substr($this->data['order']->invoice_date, 0, 10);
            } else {
                $invoiceDate = date('Y-m-d');
            }
            $customsDeclaration = array(
                'includeCustomsDeclarations' => 1,
                'contents'                   => array(
                    'article'  => $this->getProductsForCustomsDeclaration(),
                    'category' => array(
                        'value' => self::CATEGORY_MERCHANDISE_RETURN,
                    ),
                    'original' => array(
                        array(
                            'originalIdent'         => 'A',
                            'originalInvoiceNumber' => 'IN'.$this->data['order']->invoice_number,
                            'originalInvoiceDate'   => $invoiceDate,
                            'originalParcelNumber'  => $colissimoLabel->shipping_number,
                        ),
                    ),
                ),
            );
            $this->logger->info('Include customs declarations', array('data', $customsDeclaration));
            $this->request->setCustomsOptions($customsDeclaration);
        }
    }

    /**
     *
     */
    public function setLabelAddresses()
    {
        /** @var ColissimoService $colissimoService */
        $colissimoService = $this->data['colissimo_service'];
        /** @var ColissimoMerchantAddress $senderAddrObj */
        $senderAddrObj = $this->data['merchant_addr'];
        $senderAddr = array(
            'senderParcelRef' => $this->data['order']->reference,
            'address'         => array(
                'companyName' => $senderAddrObj->companyName,
                'lastName'    => $senderAddrObj->lastName,
                'firstName'   => $senderAddrObj->lastName,
                'line0'       => $senderAddrObj->line0,
                'line1'       => $senderAddrObj->line1,
                'line2'       => $senderAddrObj->line2,
                'line3'       => $senderAddrObj->line3,
                'countryCode' => $senderAddrObj->countryCode,
                'city'        => $senderAddrObj->city,
                'zipCode'     => $senderAddrObj->zipCode,
                'phoneNumber' => $senderAddrObj->phoneNumber,
                'email'       => $senderAddrObj->email,
            ),
        );
        $this->request->setSenderAddress($senderAddr);
        /** @var Address $customerAddrDb */
        $customerAddrDb = $this->data['customer_addr'];
        $customerAddr = array(
            'addresseeParcelRef'  => $this->data['order']->reference,
            'codeBarForReference' => true,
            'address'             => array(
                'companyName' => $customerAddrDb->company,
                'lastName'    => $customerAddrDb->lastname,
                'firstName'   => $customerAddrDb->firstname,
                'line2'       => $customerAddrDb->address1,
                'line3'       => $customerAddrDb->address2,
                'countryCode' => Country::getIsoById($customerAddrDb->id_country),
                'city'        => $customerAddrDb->city,
                'zipCode'     => $customerAddrDb->postcode,
                'phoneNumber' => $customerAddrDb->phone,
            ),
        );
        if ($colissimoService->type == ColissimoService::TYPE_RELAIS) {
            $customerAddr['address']['mobileNumber'] = $this->data['form_options']['mobile_phone'];
        } else {
            $customerAddr['address']['mobileNumber'] = $customerAddrDb->phone_mobile;
        }
        $customerAddr['address']['email'] = $this->data['customer']->email;
        $this->request->setAddresseeAddress($customerAddr);
    }

    /**
     *
     */
    public function setReturnLabelAddresses()
    {
        /** @var ColissimoMerchantAddress $merchantAddrObj */
        $merchantAddrObj = $this->data['merchant_addr'];
        if ($this->data['colissimo_service_initial']->type == ColissimoService::TYPE_RELAIS) {
            /** @var Address $customerAddrDb */
            $customerAddrDb = new Address((int) $this->data['order']->id_address_invoice);
            $mobilePhone = $this->data['form_options']['mobile_phone'];
        } else {
            /** @var Address $customerAddrDb */
            $customerAddrDb = $this->data['customer_addr'];
            $mobilePhone = $customerAddrDb->phone_mobile;
        }

        $customerAddr = array(
            'senderParcelRef' => $this->data['order']->reference,
            'address'         => array(
                'companyName'  => $customerAddrDb->company,
                'lastName'     => $customerAddrDb->lastname,
                'firstName'    => $customerAddrDb->firstname,
                'line2'        => $customerAddrDb->address1,
                'line3'        => $customerAddrDb->address2,
                'countryCode'  => Country::getIsoById($customerAddrDb->id_country),
                'city'         => $customerAddrDb->city,
                'zipCode'      => $customerAddrDb->postcode,
                'mobileNumber' => $mobilePhone,
                'email'        => $this->data['customer']->email,
            ),
        );
        $this->request->setSenderAddress($customerAddr);
        $merchantAddr = array(
            'addresseeParcelRef'  => $this->data['order']->reference,
            'codeBarForReference' => true,
            'address'             => array(
                'companyName' => $merchantAddrObj->companyName,
                'lastName'    => $merchantAddrObj->lastName,
                'firstName'   => $merchantAddrObj->firstName,
                'line0'       => $merchantAddrObj->line0,
                'line1'       => $merchantAddrObj->line1,
                'line2'       => $merchantAddrObj->line2,
                'line3'       => $merchantAddrObj->line3,
                'countryCode' => $merchantAddrObj->countryCode,
                'city'        => $merchantAddrObj->city,
                'zipCode'     => $merchantAddrObj->zipCode,
                'phoneNumber' => $merchantAddrObj->phoneNumber,
                'email'       => $merchantAddrObj->email,
            ),
        );
        $this->request->setAddresseeAddress($merchantAddr);
    }

    /**
     * @throws Exception
     */
    public function setLabelOptions()
    {
        /** @var ColissimoService $colissimoService */
        $colissimoService = $this->data['colissimo_service'];
        $labelOptions = array();
        //@formatter:off
        if ($this->data['form_options']['insurance']) {
            /** @var Order $order */
            $order = $this->data['order'];
            $insuredAmount = $order->total_products;
            $insuredAmountEUR = ColissimoTools::convertInEUR($insuredAmount, new Currency($order->id_currency));
            $labelOptions['insuranceValue'] = $insuredAmountEUR * 100;
            $this->logger->info('Shipment is insured ('.$insuredAmountEUR.' EUR)');
        }
        //@formatter:on
        $labelOptions['weight'] = $this->data['form_options']['weight'];
        if ($this->data['form_options']['d150']) {
            if ($colissimoService->isMachinableOptionAvailable()) {
                $labelOptions['nonMachinable'] = 1;
                $this->logger->info('Shipment non machinable');
            }
        }
        if ($colissimoService->type == ColissimoService::TYPE_RELAIS) {
            $pickupPoint = new ColissimoPickupPoint((int) $this->data['colissimo_order']->id_colissimo_pickup_point);
            $labelOptions['pickupLocationId'] = $pickupPoint->colissimo_id;
            $this->logger->info('Pickup point ID: '.$pickupPoint->colissimo_id);
        }
        if ($this->data['form_options']['ta']) {
            $labelOptions['ftd'] = 1;
            $this->logger->info('Shipment free of taxes and fees');
        }
        $this->request->setShipmentOptions($labelOptions);
    }

    /**
     * @throws Exception
     */
    public function setReturnLabelOptions()
    {
        /** @var ColissimoService $colissimoService */
        $colissimoService = $this->data['colissimo_service'];
        $labelOptions = array();
        //@formatter:off
        if ($this->data['form_options']['insurance']) {
            /** @var Order $order */
            $order = $this->data['order'];
            $insuredAmount = $order->total_products;
            $insuredAmountEUR = ColissimoTools::convertInEUR($insuredAmount, new Currency($order->id_currency));
            $labelOptions['insuranceValue'] = $insuredAmountEUR * 100;
            $this->logger->info('Return shipment is insured ('.$insuredAmountEUR.' EUR)');
        }
        //@formatter:on
        $labelOptions['weight'] = $this->data['form_options']['weight'];
        if ($this->data['form_options']['d150']) {
            if ($colissimoService->isMachinableOptionAvailable()) {
                $labelOptions['nonMachinable'] = 1;
                $this->logger->info('Shipment non machinable');
            }
        }
        if ($colissimoService->type == ColissimoService::TYPE_RELAIS) {
            $pickupPoint = new ColissimoPickupPoint((int) $this->data['colissimo_order']->id_colissimo_pickup_point);
            $labelOptions['pickupLocationId'] = $pickupPoint->colissimo_id;
        }
        $labelOptions['ftd'] = 0;
        $this->request->setShipmentOptions($labelOptions);
    }

    /**
     *
     */
    public function setCustomFields()
    {
        $infoText = array(
            'key'   => 'CUSER_INFO_TEXT',
            'value' => sprintf('PS%s;%s', _PS_VERSION_, $this->data['version']),
        );
        $infoEori = array(
            'key' => 'EORI',
            'value' => Configuration::get('COLISSIMO_EORI_NUMBER'),
        );
        $fields = array($infoText, $infoEori);
        $this->request->addCustomField($fields);
    }

    /**
     * @param bool|int $returnLabel
     * @return ColissimoLabel
     * @throws Exception
     * @throws PrestaShopException
     */
    private function generateLabel($returnLabel = false)
    {
        $this->request->buildRequest();
        $this->logger->info('Request', array('json' => json_decode($this->request->getRequest(true), true)));
        $client = new ColissimoClient();
        $client->setRequest($this->request);
        /** @var ColissimoGenerateLabelResponse $response */
        $response = $client->request();
        $this->logger->info('Response', array('response' => $response->response));
        if (!$response->messages[0]['id']) {
            $label = new ColissimoLabel();
            $label->id_colissimo_order = (int) $this->data['colissimo_order']->id;
            $label->id_colissimo_deposit_slip = 0;
            $extension = (int) $returnLabel ? 'pdf' : Tools::substr(Configuration::get('COLISSIMO_LABEL_FORMAT'), 0, 3);
            $label->label_format = pSQL(Tools::strtolower($extension));
            $label->return_label = (int) $returnLabel;
            $label->cn23 = $response->cn23 ? 1 : 0;
            $label->shipping_number = pSQL($response->parcelNumber);
            $label->coliship = 0;
            $label->migration = 0;
            $label->insurance = $this->data['form_options']['insurance'] ? '1' : '0';
            $label->file_deleted = 0;
            $label->save();
            $label->writeLabel(base64_decode($response->label));
            if (null !== $response->cn23) {
                $label->writeCN23File(base64_decode($response->cn23));
                $this->logger->info('CN23 generated.');
            }

            return $label;
        } else {
            $message = $response->messages[0];
            $this->logger->error(
                'Exception thrown: '.$message['messageContent'],
                array('details' => $response->messages)
            );
            throw new Exception(sprintf('%s (%s) - %s', $message['id'], $message['type'], $message['messageContent']));
        }
    }

    /**
     * @param ColissimoLabel $colissimoLabel
     * @return ColissimoLabel
     * @throws Exception
     * @throws PrestaShopException
     */
    public function generateReturn($colissimoLabel)
    {
        $this->logger->info(
            'Generate return label for order #'.$this->data['order']->id.' ('.$this->data['order']->reference.')'
        );
        $this->request = new ColissimoGenerateLabelRequest(
            ColissimoTools::getCredentials($this->data['order']->id_shop)
        );
        $this->setLabelOutput(true);
        $this->setLabelService();
        $this->setReturnLabelCustomsDeclarations($colissimoLabel);
        $this->setReturnLabelAddresses();
        $this->setReturnLabelOptions();
        $this->setCustomFields();

        return $this->generateLabel($colissimoLabel->id);
    }

    /**
     * @return ColissimoLabel
     * @throws Exception
     * @throws PrestaShopException
     */
    public function generate()
    {
        $this->logger->info(
            'Generate label for order #'.$this->data['order']->id.' ('.$this->data['order']->reference.')'
        );
        $this->request = new ColissimoGenerateLabelRequest(
            ColissimoTools::getCredentials($this->data['order']->id_shop)
        );
        $this->setLabelOutput();
        $this->setLabelService();
        $this->setLabelCustomsDeclarations();
        $this->setLabelAddresses();
        $this->setLabelOptions();
        $this->setCustomFields();

        return $this->generateLabel();
    }
}
