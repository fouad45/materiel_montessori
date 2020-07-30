<?php

/**
 * Class ColissimoCheckGenerateLabelResponse
 */
class ColissimoCheckGenerateLabelResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $checkGenerateLabelResponse = new self();
        $responseArray = json_decode($responseBody, true);
        if (!empty($responseArray)) {
            $checkGenerateLabelResponse->messages = $responseArray['messages'];
            $checkGenerateLabelResponse->response = $responseArray;
        }

        return $checkGenerateLabelResponse;
    }
}
