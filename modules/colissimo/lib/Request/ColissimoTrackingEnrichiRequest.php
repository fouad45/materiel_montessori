<?php

/**
 * Class ColissimoTrackingEnrichiRequest
 */
class ColissimoTrackingEnrichiRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/tracking-unified-ws/TrackingUnifiedServiceWSRest/tracking/getTrackingMessagePickupAdressAndDeliveryDate?';
    const WS_CONTENT_TYPE = 'application/json';

    const TRACKING_SUBMISSION_CONTACT = 'TRACKING_PARTNER';

    /** @var string $lang */
    protected $lang;

    /** @var string $ip */
    protected $ip;

    /** @var string $parcelNumber */
    protected $parcelNumber;

    /**
     * ColissimoTrackingEnrichiRequest constructor.
     * @param array $credentials
     * @throws Exception
     */
    public function __construct(array $credentials)
    {
        parent::__construct($credentials);
        $this->request['login'] = $this->request['contractNumber'];
        unset($this->request['contractNumber']);
    }

    /**
     * @param mixed $lang
     * @return ColissimoTrackingEnrichiRequest
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @param mixed $ip
     * @return ColissimoTrackingEnrichiRequest
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @param mixed $parcelNumber
     * @return ColissimoTrackingEnrichiRequest
     */
    public function setParcelNumber($parcelNumber)
    {
        $this->parcelNumber = $parcelNumber;

        return $this;
    }

    /**
     * @return void
     */
    public function buildRequest()
    {
        $this->request['parcelNumber'] = $this->parcelNumber;
        $this->request['ip'] = $this->ip;
        $this->request['lang'] = $this->lang;
        $this->request['parcelNumber'] = $this->parcelNumber;
        $this->request['profil'] = self::TRACKING_SUBMISSION_CONTACT;

        return;
    }

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public function buildResponse($responseHeader, $responseBody)
    {
        return ColissimoTrackingEnrichiResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
