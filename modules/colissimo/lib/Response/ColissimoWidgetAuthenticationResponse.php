<?php

/**
 * Class ColissimoWidgetAuthenticationResponse
 */
class ColissimoWidgetAuthenticationResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    /** @var string $token */
    public $token;

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $widgetAuthenticationResponse = new self();
        $responseArray = json_decode($responseBody, true);
        if (!empty($responseArray)) {
            $widgetAuthenticationResponse->response = $responseArray;
            $widgetAuthenticationResponse->token = $responseArray['token'];
        }

        return $widgetAuthenticationResponse;
    }
}
