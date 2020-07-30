<?php

/**
 * Class ColissimoTrackingSimpleResponse
 */
class ColissimoTrackingSimpleResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    public $errorMessage;
    public $errorCode;
    public $eventCode;
    public $eventDate;
    public $eventLibelle;
    public $eventSite;
    public $recipientCity;
    public $recipientCountryCode;
    public $recipientZipCode;
    public $skybillNumber;

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $trackingSimpleResponse = new self();
        $xml = new SimpleXMLElement($responseBody);
        $xml->registerXPathNamespace('ns1', 'http://chargeur.tracking.geopost.com/');
        $return = $xml->xpath('soap:Body/ns1:trackResponse/return');
        $array = json_decode(json_encode($return[0]), true);
        $trackingSimpleResponse->errorMessage = isset($array['errorMessage']) ? $array['errorMessage'] : null;
        $trackingSimpleResponse->errorCode = isset($array['errorCode']) ? $array['errorCode'] : null;
        $trackingSimpleResponse->eventCode = isset($array['eventCode']) ? $array['eventCode'] : null;
        $trackingSimpleResponse->eventDate = isset($array['eventDate']) ? $array['eventDate'] : null;
        $trackingSimpleResponse->eventLibelle = isset($array['eventLibelle']) ? $array['eventLibelle'] : null;
        $trackingSimpleResponse->eventSite = isset($array['eventSite']) ? $array['eventSite'] : null;
        $trackingSimpleResponse->recipientCity = isset($array['recipientCity']) ? $array['recipientCity'] : null;
        $trackingSimpleResponse->recipientCountryCode = isset($array['recipientCountryCode']) ? $array['recipientCountryCode'] : null;
        $trackingSimpleResponse->recipientZipCode = isset($array['recipientZipCode']) ? $array['recipientZipCode'] : null;
        $trackingSimpleResponse->skybillNumber = isset($array['skybillNumber']) ? $array['skybillNumber'] : null;

        return $trackingSimpleResponse;
    }
}
