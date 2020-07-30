<?php

/**
 * Class ColissimoWidgetAuthenticationRequest
 */
class ColissimoWidgetAuthenticationRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/widget-point-retrait/rest/authenticate.rest';
    const WS_CONTENT_TYPE = 'application/x-www-form-urlencoded';

    /**
     * ColissimoWidgetAuthenticationRequest constructor.
     * @param array $credentials
     * @throws Exception
     */
    public function __construct(array $credentials)
    {
        parent::__construct($credentials);
        $this->request['login'] = $this->request['contractNumber'];
        unset($this->request['contractNumber']);
    }

    /**
     * @return void
     */
    public function buildRequest()
    {
        return;
    }

    /**
     * @param bool $obfuscatePassword
     * @return array|string
     */
    public function getRequest($obfuscatePassword = false)
    {
        if ($obfuscatePassword) {
            $request = $this->request;
            $request['password'] = '****';

            return json_encode($request);
        }

        return http_build_query($this->request);
    }

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public function buildResponse($responseHeader, $responseBody)
    {
        return ColissimoWidgetAuthenticationResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
