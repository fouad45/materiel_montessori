<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class PwaSettingService extends BaseService
{
    private $pwaSetting = 'JM_PWA_SETTING';

    public function doExecute()
    {
        if ($this->isGetMethod()){
            $pwaSetting = json_decode(Configuration::get($this->pwaSetting));
            if (!$pwaSetting->environment) {
                $pwaSetting->environment = 'production';
            }
            $this->response = new PwaSettingResponse();
            $this->response->setting = $pwaSetting;
        } else if ($this->isPostMethod()) {
            $jsonRequestBody = CustomRequest::getRequestBody();
            $pwaSetting = json_decode($jsonRequestBody);
            if (!$pwaSetting->environment) {
                $pwaSetting->environment = 'production';
            }
            $jsonRequestBody = json_encode($pwaSetting);
            Configuration::updateValue($this->pwaSetting, $jsonRequestBody);
            $this->response = new PwaSettingResponse();
            $this->response->setting = $pwaSetting;
        } else {
            $this->throwUnsupportedMethodException();
        }
    }
}