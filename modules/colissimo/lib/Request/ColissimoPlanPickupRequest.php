<?php

/**
 * Class ColissimoPlanPickupRequest
 */
class ColissimoPlanPickupRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/sls-ws/SlsServiceWSRest/2.0/planPickup?';
    const WS_CONTENT_TYPE = 'application/json';

    /** @var string $parcelNumber */
    protected $parcelNumber;

    /** @var string $mailboxPickingDate */
    protected $mailboxPickingDate;

    /** @var array $senderAddress */
    protected $senderAddress;

    /**
     * @param string $parcelNumber
     * @return ColissimoPlanPickupRequest
     */
    public function setParcelNumber($parcelNumber)
    {
        $this->parcelNumber = $parcelNumber;

        return $this;
    }

    /**
     * @param string $mailboxPickingDate
     * @return ColissimoPlanPickupRequest
     */
    public function setMailboxPickingDate($mailboxPickingDate)
    {
        $this->mailboxPickingDate = $mailboxPickingDate;

        return $this;
    }

    /**
     * @param array $senderAddress
     * @return ColissimoPlanPickupRequest
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
        $this->request['parcelNumber'] = $this->parcelNumber;
        $this->request['mailBoxPickingDate'] = $this->mailboxPickingDate;
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
        return ColissimoPlanPickupResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
