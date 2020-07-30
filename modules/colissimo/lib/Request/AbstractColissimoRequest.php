<?php

/**
 * Class AbstractColissimoRequest
 */
abstract class AbstractColissimoRequest
{
    /** @var array $request */
    protected $request;

    /** @var string $xmlLocation */
    protected $xmlLocation;

    /** @var string $forceEndpoint */
    public $forceEndpoint;

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     */
    abstract public function buildResponse($responseHeader, $responseBody);

    /**
     * @return mixed
     */
    abstract public function buildRequest();

    /**
     * AbstractColissimoRequest constructor.
     * @param array $credentials
     * @throws Exception
     */
    public function __construct(array $credentials)
    {
        if (!isset($credentials['contract_number']) || !isset($credentials['password'])) {
            throw new Exception('Bad credentials.');
        }
        $this->setIdentification($credentials);
        $this->xmlLocation = dirname(__FILE__).'/../xml/';
    }

    /**
     * @param array $credentials
     */
    final private function setIdentification(array $credentials)
    {
        $this->request = array();
        $this->request['contractNumber'] = $credentials['contract_number'];
        $this->request['password'] = $credentials['password'];
        if (isset($credentials['force_endpoint'])) {
            $this->forceEndpoint = $credentials['force_endpoint'];
        }
    }

    /**
     * @param bool $obfuscatePassword
     * @return array|string
     */
    public function getRequest($obfuscatePassword = false)
    {
        if ($obfuscatePassword) {
            $request = $this->request;
            $request['password'] = '*****';

            return json_encode($request);
        }

        return json_encode($this->request);
    }
}
