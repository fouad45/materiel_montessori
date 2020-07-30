<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class PwaModeService extends BaseService
{
    private $pwaMode = 'JM_PWA_MODE';
    private $pwaModeShowPopUp = 'SHOW_POP_UP';
    private $pwaModeNotShowPopUp = 'NOT_SHOW_POP_UP';
    private $pwaModeOff = 'PWA_MODE_OFF';

    public function doExecute()
    {
        if ($this->isGetMethod()) {
            $this->response = new PwaModeResponse();
            $mode = Configuration::get($this->pwaMode);
            if (!$mode) {
                $this->response->pwaMode = $this->pwaModeOff;
            } else {
                $this->response->pwaMode = $mode;
            }
        } else if ($this->isPostMethod()) {
            $jsonRequestBody = CustomRequest::getRequestBody();
            $this->response = new PwaModeResponse();
            $mode = json_decode($jsonRequestBody);
            if ($mode->pwaMode === $this->pwaModeShowPopUp) {
                Configuration::updateValue($this->pwaMode, $this->pwaModeShowPopUp);
            } else if ($mode->pwaMode === $this->pwaModeNotShowPopUp) {
                Configuration::updateValue($this->pwaMode, $this->pwaModeNotShowPopUp);
            } else {
                Configuration::updateValue($this->pwaMode, $this->pwaModeOff);
            }
            $this->response->pwaMode = $mode->pwaMode;
        } else {
            $this->throwUnsupportedMethodException();
        }
    }
}