<?php

/**
 * Class ColissimoPlanPickupResponse
 */
class ColissimoPlanPickupResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $planPickupResponse = new self();
        $responseArray = json_decode($responseBody, true);
        if (!empty($responseArray)) {
            $planPickupResponse->messages = $responseArray['messages'];
            $planPickupResponse->response = $responseArray;
        }

        return $planPickupResponse;
    }
}
