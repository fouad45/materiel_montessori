<?php

/**
 * Class ColissimoTrackingEnrichiResponse
 */
class ColissimoTrackingEnrichiResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    /** @var array $parcel */
    public $parcel;

    /** @var array $error */
    public $error;

    /** @var array $message */
    public $message;

    /** @var string $code */
    public $code;

    /** @var string $messageCode */
    public $messageCode;

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $trackingEnrichiResponse = new self();
        $responseArray = json_decode($responseBody, true);
        if (!empty($responseArray)) {
            $trackingEnrichiResponse->response = $responseArray;
            if (isset($responseArray['code'])) {
                $trackingEnrichiResponse->code = $responseArray['code'];
            }
            if (isset($responseArray['messageCode'])) {
                $trackingEnrichiResponse->messageCode = $responseArray['messageCode'];
            }
            if (isset($responseArray['message'])) {
                $trackingEnrichiResponse->message = $responseArray['message'];
            }
            if (isset($responseArray['error'])) {
                $trackingEnrichiResponse->error = $responseArray['error'];
            }
            if (isset($responseArray['parcel'])) {
                $trackingEnrichiResponse->parcel = $responseArray['parcel'];
            }
        }

        return $trackingEnrichiResponse;
    }
}
