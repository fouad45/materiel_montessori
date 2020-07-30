<?php
/**
 * Class SmartAppBannerSaveService
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class SmartAppBannerSaveService extends BaseService
{
    private $banner_init_script;
    private $setting = 'SMART_APP_BANNER_SETTING_';
    private $script = 'SMART_APP_BANNER_SCRIPT_';
    private $is_header = 'SMART_APP_BANNER_HEADER_';
    private $custom_css = 'SMART_APP_BANNER_CSS_';

    public function doExecute()
    {
        if ($this->isPostMethod()) {
            $jsonRequestBody = CustomRequest::getRequestBody();
            $smartAppBannerSetting = json_decode($jsonRequestBody);
            $shop_id = $this->getRequestValue('id_shop');
            $id_lang = $this->getRequestValue('id_lang');
            $is_header = $this->getRequestValue('is_header');
            $custom_css = $this->getRequestValue('custom_css');
            $this->setting .= $shop_id . '_' . $id_lang;
            $this->script .= $shop_id . '_' . $id_lang;
            $this->is_header .= $shop_id . '_' . $id_lang;
            $this->custom_css .= $shop_id . '_' . $id_lang;
            if ($jsonRequestBody) {
                Configuration::updateValue($this->setting, $jsonRequestBody);
            }
            if (strcmp($smartAppBannerSetting->enable, "1") == 0) {
                $this->banner_init_script = $this->genereteBannerScript($smartAppBannerSetting);
                Configuration::updateValue($this->script, $this->banner_init_script);
            } else {
                Configuration::updateValue($this->script, null);
            }
            if ($is_header != null) {
                Configuration::updateValue($this->is_header, $is_header);
            }
            if ($custom_css != null) {
                Configuration::updateValue($this->custom_css, $custom_css);
            }
        } else {
            $this->throwUnsupportedMethodException();
        }

        $this->response = null;
        $this->response->errorCode = 0;
        $this->response->errorMsg = 'success';
    }

    public function genereteBannerScript($smartAppBannerSetting)
    {
        $script = 'new SmartBanner({';
        $script .= 'daysHidden:';
        $script .= $smartAppBannerSetting->days_hidden;
        $script .= ',daysReminder:';
        $script .= $smartAppBannerSetting->days_reminder;
        $script .= ',appStoreLanguage:\'';
        $script .= $smartAppBannerSetting->app_store_language;
        $script .= '\',title:\'';
        $script .= $smartAppBannerSetting->title;
        $script .= '\',author:\'';
        $script .= $smartAppBannerSetting->author;
        $script .= '\',button:\'';
        $script .= $smartAppBannerSetting->button;
        $script .= '\',store:{ios:\'';
        $script .= $smartAppBannerSetting->store_ios;
        $script .= '\',android:\'';
        $script .= $smartAppBannerSetting->store_android;
        $script .= '\'},price:{ios:\'';
        $script .= $smartAppBannerSetting->price_ios;
        $script .= '\',android:\'';
        $script .= $smartAppBannerSetting->price_android;
        $script .= '\'}});';

        return $script;
    }
}
