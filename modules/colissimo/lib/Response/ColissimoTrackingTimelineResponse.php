<?php

/**
 * Class ColissimoTrackingTimelineResponse
 */
class ColissimoTrackingTimelineResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    /** @var array $status */
    public $status;

    /** @var array $userMessages */
    public $userMessages;

    /** @var array $parcelDetails */
    public $parcelDetails;

    /** @var array $events */
    public $events;

    /** @var array $timeline */
    public $timeline;

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $trackingTimelineResponse = new self();
        $responseArray = json_decode($responseBody, true);
        if (!empty($responseArray)) {
            $trackingTimelineResponse->response = $responseArray;
            if (isset($responseArray['status'])) {
                $trackingTimelineResponse->status = $responseArray['status'];
            }
            if (isset($responseArray['message'])) {
                $trackingTimelineResponse->userMessages = $responseArray['message'];
            }
            if (isset($responseArray['parcel'])) {
                $trackingTimelineResponse->parcelDetails = $responseArray['parcel'];
                if (isset($responseArray['parcel']['event'])) {
                    $trackingTimelineResponse->events = $responseArray['parcel']['event'];
                    unset($trackingTimelineResponse->parcelDetails['event']);
                }
                if (isset($responseArray['parcel']['step'])) {
                    $trackingTimelineResponse->timeline = $responseArray['parcel']['step'];
                    unset($trackingTimelineResponse->parcelDetails['step']);
                }
            }
        }

        return $trackingTimelineResponse;
    }
}
