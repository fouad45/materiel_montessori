{*
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author     PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2019 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="colissimo-process-update-tracking">
  <p>{l s='Please wait while updating %d order(s) tracking' sprintf=$orders_count|intval mod='colissimo'}</p>
  <img src="{$img_path|escape:'htmlall':'utf-8'}loading_tracking.svg"/>
</div>

{literal}
<script type="text/javascript">
    $(document).ready(function () {
        var colissimoDashboardUrl = baseAdminDir + currentIndex + '&token=' + token;

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoDashboard',
                ajax: 1,
                token: token,
                action: 'updateAllOrderTracking',
                submitUpdateAllTracking: 1
            }
        }).fail(function (jqXHR, textStatus) {
        }).success(function (data) {
        }).always(function () {
            window.location.replace(colissimoDashboardUrl);
        });
    });
</script>
{/literal}
