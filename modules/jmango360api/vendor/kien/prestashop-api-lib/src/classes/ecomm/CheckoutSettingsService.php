<?php
/**
 * @author Jmango360
 * @copyright 2018 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class CheckoutSettingsService
 */
class CheckoutSettingsService extends BaseService
{
    const JM360_CHECKOUT_CUSTOM_CSS = 'JM360_CHECKOUT_CUSTOM_CSS';
    const JM360_CHECKOUT_CUSTOM_JS = 'JM360_CHECKOUT_CUSTOM_JS';
    const JM360_CHECKOUT_DISABLE_MERGE_CSS = 'JM360_CHECKOUT_DISABLE_MERGE_CSS';
    const JM360_CHECKOUT_DISABLE_MERGE_JS = 'JM360_CHECKOUT_DISABLE_MERGE_JS';

    /**
     * Implement business logic
     */
    public function doExecute()
    {
        if ($this->isGetMethod()) {
            // Get setting
            $this->response = $this->getSettings();
        } elseif ($this->isPostMethod()) {
            // Save setting
            $this->response = $this->saveSettings();
        }
    }

    /**
     * Get checkout settings
     *
     * @return CheckoutSettingsResponse
     */
    public function getSettings()
    {
        $response = new CheckoutSettingsResponse();

        $customCss = Configuration::get(self::JM360_CHECKOUT_CUSTOM_CSS);
        $response->css = $customCss ? $customCss : '';

        $customJs = Configuration::get(self::JM360_CHECKOUT_CUSTOM_JS);
        $response->js = $customJs ? $customJs : '';

        $disableMergeCss = Configuration::get(self::JM360_CHECKOUT_DISABLE_MERGE_CSS);
        $response->disable_merge_css = $disableMergeCss ? 1 : 0;

        $disableMergeJs = Configuration::get(self::JM360_CHECKOUT_DISABLE_MERGE_JS);
        $response->disable_merge_js = $disableMergeJs ? 1 : 0;

        return $response;
    }

    /**
     * Set checkout settings
     *
     * @return CheckoutSettingsResponse|JmResponse
     */
    public function saveSettings()
    {
        $shopId = $this->getRequestValue('id_shop');
        $payload = $this->retrievePayload();
        try {
            if (isset($payload['css'])) {
                Configuration::updateValue(self::JM360_CHECKOUT_CUSTOM_CSS, $payload['css'], false, null, $shopId);
            }
            if (isset($payload['js'])) {
                Configuration::updateValue(self::JM360_CHECKOUT_CUSTOM_JS, $payload['js'], false, null, $shopId);
            }
            if (isset($payload['disable_merge_css'])) {
                Configuration::updateValue(self::JM360_CHECKOUT_DISABLE_MERGE_CSS, $payload['disable_merge_css'], false, null, $shopId);
            }
            if (isset($payload['disable_merge_js'])) {
                Configuration::updateValue(self::JM360_CHECKOUT_DISABLE_MERGE_JS, $payload['disable_merge_js'], false, null, $shopId);
            }
        } catch (Exception $e) {
            $response = new JmResponse();
            $response->errors = array('Could not save data!');
            return $response;
        }

        return $this->getSettings();
    }

    /**
     * Get checkout custom CSS
     *
     * @return string
     */
    public static function getCheckoutCustomCss()
    {
        $css = Configuration::get(self::JM360_CHECKOUT_CUSTOM_CSS);
        return $css ? $css : '';
    }

    /**
     * Get checkout custom JS
     *
     * @return string
     */
    public static function getCheckoutCustomJs()
    {
        $js = Configuration::get(self::JM360_CHECKOUT_CUSTOM_JS);
        return $js ? $js : '';
    }

    /**
     * Check if disable merge css
     *
     * @return bool
     */
    public static function getDisableMergeCss()
    {
        return (bool)Configuration::get(self::JM360_CHECKOUT_DISABLE_MERGE_CSS);
    }

    /**
     * Check if disable merge js
     *
     * @return bool
     */
    public static function getDisableMergeJs()
    {
        return (bool)Configuration::get(self::JM360_CHECKOUT_DISABLE_MERGE_JS);
    }
}
