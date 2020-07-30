/**
 * 2007-2019 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

window.addEventListener('load', defaultColorField);
function defaultColorField() {
    if (txtColHov == ''){
        $('#txtColHov').css('background', 'white');
    }

    if (txtCol == '') {
        $('#txtCol').css('background', 'white');
    }

    if (bgCol == '') {
        $('#bgCol').css('background', 'white');
    }

    if (bgColHov == '') {
        $('#bgColHov').css('background', 'white');
    }
}
$(document).ready(function(){
    checkModeCustomStyle();
    $('.modeCustomStyle').click(checkModeCustomStyle);
    function checkModeCustomStyle() {
        var mode = $('.modeCustomStyle [name="useCustomStyle"]:checked').val();
        if (mode == '1') {
            $('.customStyle').fadeIn();
        } else {
            $('.customStyle').fadeOut();
        }
    }


});
