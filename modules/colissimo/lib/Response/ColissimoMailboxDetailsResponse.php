<?php

/**
 * Class ColissimoMailboxDetailsResponse
 */
class ColissimoMailboxDetailsResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    /** @var string $maxPickingHour */
    public $maxPickingHour;

    /** @var string $validityTime */
    public $validityTime;

    /** @var array $pickingDates */
    public $pickingDates;

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $mailboxDetailsResponse = new self();
        $responseArray = json_decode($responseBody, true);
        if (!empty($responseArray)) {
            $mailboxDetailsResponse->messages = $responseArray['messages'];
            $mailboxDetailsResponse->response = $responseArray;
            if (isset($responseArray['mailBoxPickingDateMaxHour'])) {
                $mailboxDetailsResponse->maxPickingHour = $responseArray['mailBoxPickingDateMaxHour'];
            }
            if (isset($responseArray['validityTime'])) {
                $mailboxDetailsResponse->validityTime = $responseArray['validityTime'];
            }
            if (isset($responseArray['mailBoxPickingDates'])) {
                foreach ($responseArray['mailBoxPickingDates'] as $mailBoxPickingDate) {
                    $mailboxDetailsResponse->pickingDates[] = $mailBoxPickingDate / 1000;
                }
            }
        }

        return $mailboxDetailsResponse;
    }
}
