<?php

/**
 * Class ColissimoTrackingTimelineRequest
 */
class ColissimoTrackingTimelineRequest extends ColissimoTrackingEnrichiRequest
{
    const WS_PATH = '/tracking-timeline-ws/rest/tracking/timeline';
    const TRACKING_PROFILE = 'TRACKING_BNUM';

    /** @var string $lang */
    protected $lang;

    /** @var string $ip */
    protected $ip;

    /** @var string $parcelNumber */
    protected $parcelNumber;

    /**
     * @return void
     */
    public function buildRequest()
    {
        $this->request['parcelNumber'] = $this->parcelNumber;
        $this->request['ip'] = $this->ip;
        $this->request['lang'] = $this->lang;
        $this->request['parcelNumber'] = $this->parcelNumber;
        $this->request['profil'] = self::TRACKING_PROFILE;

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
        return ColissimoTrackingTimelineResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
