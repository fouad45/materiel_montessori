<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class PwaEndpointService extends BaseService
{
    private $pwaEndpoint = 'JM_PWA_ENDPOINT';

    public function doExecute()
    {
        if ($this->isGetMethod()){
            $pwaSetting = json_decode(Configuration::get($this->pwaEndpoint));
            $this->response = new PwaEndpointResponse();
            $this->response->endpoint = $pwaSetting->endpoint;
        } else if ($this->isPostMethod()) {
            $jsonRequestBody = CustomRequest::getRequestBody();
            $request = json_decode($jsonRequestBody);
            Configuration::updateValue($this->pwaEndpoint, $jsonRequestBody);
            if(!$request->endpoint || $request->endpoint === '') {
                Configuration::updateValue('JM_PWA_MODE', 'PWA_MODE_OFF');
            }
            $this->response = new PwaEndpointResponse();
            $this->response->endpoint = $request->endpoint;
        } else {
            $this->throwUnsupportedMethodException();
        }
    }
}