{**
* @license Created by JMango
*}
<style type="text/css">
    {$customCss}
</style>
<script src="{$baseUrl}/modules/jmango360api/views/js/smartAppBanner/smart-app-banner.js"></script>
<script type="text/javascript">
    {if $smartAppBannerSetting!=null}
    new SmartBanner({
        daysHidden: {$smartAppBannerSetting->days_hidden|escape:'html':'UTF-8'},
        daysReminder: {$smartAppBannerSetting->days_reminder|escape:'html':'UTF-8'},
        appStoreLanguage: '{$smartAppBannerSetting->app_store_language|escape:'html':'UTF-8'}',
        title: '{$smartAppBannerSetting->title|escape:'html':'UTF-8'}',
        author: '{$smartAppBannerSetting->author|escape:'html':'UTF-8'}',
        button: '{$smartAppBannerSetting->button|escape:'html':'UTF-8'}',
        store: {
            ios: '{$smartAppBannerSetting->store_ios|escape:'html':'UTF-8'}',
            android: '{$smartAppBannerSetting->store_android|escape:'html':'UTF-8'}'
        },
        price: {
            ios: '{$smartAppBannerSetting->price_ios|escape:'html':'UTF-8'}',
            android: '{$smartAppBannerSetting->price_android|escape:'html':'UTF-8'}'
        }
    });
    {/if}
</script>
