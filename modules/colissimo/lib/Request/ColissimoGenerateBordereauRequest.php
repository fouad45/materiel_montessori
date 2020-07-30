<?php

/**
 * Class ColissimoGenerateBordereauRequest
 */
class ColissimoGenerateBordereauRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'SOAP';
    const WS_METHOD = 'generateBordereauByParcelsNumbers';
    const WS_CONTENT_TYPE = 'application/xml';

    /** @var SimpleXMLElement $xml */
    public $xml;

    /**
     * ColissimoGenerateBordereauRequest constructor.
     * @param array $credentials
     * @throws Exception
     */
    public function __construct(array $credentials)
    {
        parent::__construct($credentials);
        $this->xml = simplexml_load_file($this->xmlLocation.self::WS_METHOD.'.xml');
        $this->xml->registerXPathNamespace('sls', 'http://sls.ws.coliposte.fr');
        $this->setCredentials();
    }

    /**
     * @param array $numbers
     */
    public function setParcelsNumbers(array $numbers)
    {
        $list = $this->xml->xpath('soapenv:Body/sls:generateBordereauByParcelsNumbers/generateBordereauParcelNumberList');
        foreach ($numbers as $number) {
            $list[0]->addChild('parcelsNumbers', $number);
        }
    }

    /**
     *
     */
    public function setCredentials()
    {
        $parcels = $this->xml->xpath('soapenv:Body/sls:generateBordereauByParcelsNumbers');
        $parcels[0]->contractNumber = $this->request['contractNumber'];
        $parcels[0]->password = $this->request['password'];
    }

    /**
     * @return mixed|void
     */
    public function buildRequest()
    {
        return;
    }

    /**
     * @param bool $obfuscatePassword
     * @return string
     */
    public function getRequest($obfuscatePassword = false)
    {
        if ($obfuscatePassword) {
            $requestXml = new SimpleXMLElement($this->xml->asXML());
            $requestXml->registerXPathNamespace('sls', 'http://sls.ws.coliposte.fr');
            $parcels = $requestXml->xpath('soapenv:Body/sls:generateBordereauByParcelsNumbers');
            $parcels[0]->password = '****';
            $requestJsonString = json_encode($parcels);

            return $requestJsonString;
        }

        return $this->xml->asXML();
    }

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public function buildResponse($responseHeader, $responseBody)
    {
        return ColissimoGenerateBordereauResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
