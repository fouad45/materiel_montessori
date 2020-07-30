<?php

/**
 * Class ColissimoGenerateLabelResponse
 */
class ColissimoGenerateLabelResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    /** @var string $label */
    public $label;

    /** @var string $cn23 */
    public $cn23;

    /** @var string $parcelNumber */
    public $parcelNumber;

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $jsonResponseParser = new ColissimoResponseParser();
        try {
            $parsedResponse = $jsonResponseParser->parseBody($responseBody);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        $generateLabelResponse = new self();
        if (isset($parsedResponse['<jsonInfos>'])) {
            $generateLabelResponse->response = json_decode($parsedResponse['<jsonInfos>'], true);
            $generateLabelResponse->messages = $generateLabelResponse->response['messages'];
        }
        if (isset($generateLabelResponse->response['labelV2Response']['parcelNumber'])) {
            $generateLabelResponse->parcelNumber = $generateLabelResponse->response['labelV2Response']['parcelNumber'];
        }
        if (isset($parsedResponse['<label>'])) {
            $generateLabelResponse->label = base64_encode($parsedResponse['<label>']);
        }
        if (isset($parsedResponse['<cn23>'])) {
            $generateLabelResponse->cn23 = base64_encode($parsedResponse['<cn23>']);
        }

        return $generateLabelResponse;
    }
}
