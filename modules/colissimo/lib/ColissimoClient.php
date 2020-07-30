<?php

/**
 * Class ColissimoClient
 */
class ColissimoClient
{
    const BASE_URL_PRODUCTION = 'https://ws.colissimo.fr';
    const BASE_URL_TEST = 'https://ws.colissimo.fr';
    const COLISSIMO_WSDL = 'https://ws.colissimo.fr/sls-ws/SlsServiceWS/2.0?wsdl';

    /** @var string $baseUrl */
    protected $baseUrl;

    /** @var AbstractColissimoRequest $request */
    private $request;

    /**
     * ColissimoClient constructor.
     * @param int   $mode
     * @param array $urls
     */
    public function __construct($mode = 0, $urls = array())
    {
        if (is_array($urls) && isset($urls['test']) && isset($urls['production'])) {
            $this->baseUrl = (1 === $mode) ? $urls['production'] : $urls['test'];
        } else {
            $this->baseUrl = (1 === $mode) ? self::BASE_URL_PRODUCTION : self::BASE_URL_TEST;
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function request()
    {
        $wsType = constant(get_class($this->request).'::WS_TYPE');
        switch ($wsType) {
            case 'SOAP':
                $method = constant(get_class($this->request).'::WS_METHOD');
                $soapClient = new SoapClient(
                    self::COLISSIMO_WSDL, array(
                        'exceptions'   => 0,
                        'wsdl_cache'   => 0,
                        'trace'        => 1,
                        'soap_version' => SOAP_1_1,
                        'encoding'     => 'UTF-8',
                    )
                );
                $responseBody = $soapClient->__doRequest(
                    $this->request->getRequest(),
                    self::COLISSIMO_WSDL,
                    $method,
                    '2.0',
                    0
                );
                $responseHeader = $soapClient->__getLastResponseHeaders();
                preg_match("/HTTP\/\d\.\d\s*\K[\d]+/", $responseHeader, $matches);
                $httpCode = $matches[0];

                break;
            case 'CURL':
                if ($this->request->forceEndpoint) {
                    $url = $this->request->forceEndpoint;
                } else {
                    $url = $this->baseUrl.constant(get_class($this->request).'::WS_PATH');
                }
                $contentType = array();
                if (defined(get_class($this->request).'::WS_CONTENT_TYPE')) {
                    $contentType = array('Content-Type: '.constant(get_class($this->request).'::WS_CONTENT_TYPE'));
                }
                $body = $this->request->getRequest();
                $curl = curl_init();
                curl_setopt_array(
                    $curl,
                    array(
                        CURLOPT_URL            => $url,
                        CURLOPT_POSTFIELDS     => $body,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING       => "",
                        CURLOPT_MAXREDIRS      => 10,
                        CURLOPT_HEADER         => true,
                        CURLOPT_VERBOSE        => true,
                        CURLOPT_TIMEOUT        => 30,
                        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST  => 'POST',
                        CURLOPT_HTTPHEADER     => $contentType,
                    )
                );
                $response = curl_exec($curl);
                if ($response === false) {
                    throw new Exception('Empty Response.');
                }
                $curlInfo = curl_getinfo($curl);
                $curlError = curl_errno($curl);
                $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
                $responseHeader = substr($response, 0, $headerSize);
                $responseBody = substr($response, $headerSize);
                curl_close($curl);
                $httpCode = $curlInfo['http_code'];
                if ($curlError) {
                    throw new Exception('cURL error: '.$curlError);
                }
                break;
            default:
                throw new Exception('Wrong WS call.');
                break;
        }
        if (!in_array($httpCode, array(200, 201, 400))) {
            throw new Exception('Bad HTTP code: '.$httpCode);
        }

        return $this->request->buildResponse($responseHeader, $responseBody);
    }

    /**
     * @param AbstractColissimoRequest $request
     */
    public function setRequest(AbstractColissimoRequest $request)
    {
        $this->request = $request;
    }
}
