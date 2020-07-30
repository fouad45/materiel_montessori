/**
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
* @author PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2019 PrestaShop SA
* @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

var installModule = (e) => {
    $('#gadwords_action-btn #gadwords_installmodule').hide();
    $('#gadwords_action-btn .loader').show();

    $.ajax({
        type: 'POST',
        url: gadwords_controller_url,
        dataType: 'json',
        data: {
            controller : gadwords_controller_name,
            action : 'InstallModule',
            ajax : true
        },
        success: (json) => {
            $('#gadwords_action-btn .loader').hide();

            if (json.installed === true) {
                // redirect to module configuration's page
                window.location.href = json.moduleLink;
            } else {
                $('#gadwords_installmodule').show();
            }
        },
        error: () => {
            $.growl.error({ title: "Notice!", message: gadwords_ajax_error });
        }
    });
}

$(document).on('click', '#gadwords_installmodule', installModule);
