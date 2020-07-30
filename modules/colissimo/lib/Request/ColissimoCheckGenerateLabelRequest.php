<?php

/**
 * Class ColissimoCheckGenerateLabelRequest
 */
class ColissimoCheckGenerateLabelRequest extends ColissimoGenerateLabelRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/sls-ws/SlsServiceWSRest/2.0/checkGenerateLabel?';
    const WS_CONTENT_TYPE = 'application/json';

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public function buildResponse($responseHeader, $responseBody)
    {
        return ColissimoCheckGenerateLabelResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
