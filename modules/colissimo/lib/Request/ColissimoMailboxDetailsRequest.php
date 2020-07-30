<?php

/**
 * Class ColissimoMailboxDetailsRequest
 */
class ColissimoMailboxDetailsRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/sls-ws/SlsServiceWSRest/2.0/getListMailBoxPickingDates?';
    const WS_CONTENT_TYPE = 'application/json';

    /** @var array $senderAddress */
    protected $senderAddress;

    /**
     * @param mixed $senderAddress
     * @return ColissimoMailboxDetailsRequest
     */
    public function setSenderAddress($senderAddress)
    {
        $this->senderAddress = $senderAddress;

        return $this;
    }

    /**
     * @return void
     */
    public function buildRequest()
    {
        $this->request['sender'] = $this->senderAddress;
    }

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public function buildResponse($responseHeader, $responseBody)
    {
        return ColissimoMailboxDetailsResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
