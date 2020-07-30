<?php

/**
 * Class ColissimoTrackingSimpleRequest
 */
class ColissimoTrackingSimpleRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/tracking-chargeur-cxf/TrackingServiceWS/track';
    const WS_CONTENT_TYPE = 'application/xml;charset=UTF-8';

    /** @var SimpleXMLElement $xml */
    public $xml;

    /**
     * ColissimoTrackingSimpleRequest constructor.
     * @param array $credentials
     * @throws Exception
     */
    public function __construct(array $credentials)
    {
        parent::__construct($credentials);
        $this->xml = simplexml_load_file($this->xmlLocation.'track.xml');
        $this->xml->registerXPathNamespace('char', 'http://chargeur.tracking.geopost.com');
        $this->setCredentials();
    }

    /**
     *
     */
    public function setCredentials()
    {
        $track = $this->xml->xpath('soapenv:Body/char:track');
        $track[0]->accountNumber = $this->request['contractNumber'];
        $track[0]->password = $this->request['password'];
    }

    public function setSkybillNumber($skybillNumber)
    {
        $track = $this->xml->xpath('soapenv:Body/char:track');
        $track[0]->skybillNumber = $skybillNumber;
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
            $requestXml->registerXPathNamespace('char', 'http://chargeur.tracking.geopost.com');
            $track = $requestXml->xpath('soapenv:Body/char:track');
            $track[0]->password = '****';
            $requestJsonString = json_encode($track);

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
        return ColissimoTrackingSimpleResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
