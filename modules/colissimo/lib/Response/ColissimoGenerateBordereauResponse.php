<?php

/**
 * Class ColissimoGenerateBordereauResponse
 */
class ColissimoGenerateBordereauResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    /** @var string $bordereau */
    public $bordereau;

    /** @var array $bordereauHeader */
    public $bordereauHeader;

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $soapResponseParser = new ColissimoResponseParser();
        try {
            $parsedResponse = $soapResponseParser->parseBody($responseBody);
            $parsedHeaders = $soapResponseParser->parseHeaders($responseHeader);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        $generateBordereauResponse = new self();
        $contentType = $soapResponseParser->parseContentType($parsedHeaders);
        if (isset($parsedResponse[$contentType])) {
            $xml = new SimpleXMLElement($parsedResponse[$contentType]);
            $xml->registerXPathNamespace('ns2', 'http://sls.ws.coliposte.fr');
            $xml->registerXPathNamespace('xop', 'http://www.w3.org/2004/08/xop/include');
            $bordereau = $xml->xpath('soap:Body/ns2:generateBordereauByParcelsNumbersResponse/return/bordereau/bordereauDataHandler/xop:Include');
            if (!empty($bordereau)) {
                $cidBordereau = substr($bordereau[0]->attributes()->href->__toString(), 4);
                if (isset($parsedResponse['<'.$cidBordereau.'>'])) {
                    $generateBordereauResponse->bordereau = base64_encode($parsedResponse['<'.$cidBordereau.'>']);
                }
            }
            $bordereauHeader = $xml->xpath('soap:Body/ns2:generateBordereauByParcelsNumbersResponse/return/bordereau/bordereauHeader');
            if (!empty($bordereauHeader)) {
                $generateBordereauResponse->bordereauHeader = json_decode(json_encode($bordereauHeader[0]), true);
                $generateBordereauResponse->response['bordereauHeader'] = $generateBordereauResponse->bordereauHeader;
            }
            $messages = $xml->xpath('soap:Body/ns2:generateBordereauByParcelsNumbersResponse/return/messages');
            $generateBordereauResponse->messages = json_decode(json_encode($messages), true);
            $generateBordereauResponse->response['messages'] = $generateBordereauResponse->messages;
        }

        return $generateBordereauResponse;
    }
}
