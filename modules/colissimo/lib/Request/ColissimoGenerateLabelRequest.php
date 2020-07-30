<?php

/**
 * Class ColissimoGenerateLabelRequest
 */
class ColissimoGenerateLabelRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/sls-ws/SlsServiceWSRest/2.0/generateLabel?';
    const WS_CONTENT_TYPE = 'application/json';


    /** @var array $output */
    protected $output;

    /** @var array $senderAddress */
    protected $senderAddress;

    /** @var array $addresseeAddress */
    protected $addresseeAddress;

    /** @var array $shipmentOptions */
    protected $shipmentOptions;

    /** @var array $shipmentServices */
    protected $shipmentServices;

    /** @var array $customsOptions */
    protected $customsOptions;

    /** @var array $fields */
    protected $fields;

    /**
     * @param array $output
     * @return ColissimoGenerateLabelRequest
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @param array $senderAddress
     * @return ColissimoGenerateLabelRequest
     */
    public function setSenderAddress($senderAddress)
    {
        $this->senderAddress = $senderAddress;

        return $this;
    }

    /**
     * @param array $addresseeAddress
     * @return ColissimoGenerateLabelRequest
     */
    public function setAddresseeAddress($addresseeAddress)
    {
        $this->addresseeAddress = $addresseeAddress;

        return $this;
    }

    /**
     * @param array $shipmentOptions
     * @return ColissimoGenerateLabelRequest
     */
    public function setShipmentOptions($shipmentOptions)
    {
        $this->shipmentOptions = $shipmentOptions;

        return $this;
    }

    /**
     * @param array $shipmentServices
     * @return ColissimoGenerateLabelRequest
     */
    public function setShipmentServices($shipmentServices)
    {
        $this->shipmentServices = $shipmentServices;

        return $this;
    }

    /**
     * @param array $customsOptions
     * @return ColissimoGenerateLabelRequest
     */
    public function setCustomsOptions($customsOptions)
    {
        $this->customsOptions = $customsOptions;

        return $this;
    }

    /**
     * @param array $customField
     * @return ColissimoGenerateLabelRequest
     */
    public function addCustomField($customField)
    {
        foreach ($customField as $key => $field) {
            $this->fields['customField'][$key] = array(
                'key' => $field['key'],
                'value' => $field['value'],
            );
        }

        return $this;
    }

    /**
     * @return mixed|void
     */
    public function buildRequest()
    {
        $this->request['outputFormat'] = $this->output;
        $this->request['letter']['service'] = $this->shipmentServices;
        $this->request['letter']['parcel'] = $this->shipmentOptions;
        if (!empty($this->customsOptions)) {
            $this->request['letter']['customsDeclarations'] = $this->customsOptions;
        }
        $this->request['letter']['sender'] = $this->senderAddress;
        $this->request['letter']['addressee'] = $this->addresseeAddress;
        $this->request['fields'] = $this->fields;
    }

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public function buildResponse($responseHeader, $responseBody)
    {
        return ColissimoGenerateLabelResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
