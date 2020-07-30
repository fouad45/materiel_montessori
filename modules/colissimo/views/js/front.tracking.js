/*
 * 2007-2019 PrestaShop
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2019 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

function openCloseStepsDetails(chevron) {
    var stepDetails = chevron.closest('.timeline-steps-details-full').find('.timeline-steps-details-table');

    chevron.toggleClass('up');
    stepDetails.slideToggle();
}

$(document).ready(function () {
    if (!noLabels) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: colissimoAjaxTracking + 'index.php',
            data: {
                fc: 'module',
                module: 'colissimo',
                controller: 'tracking',
                ajax: 1,
                action: 'showTracking',
                order_reference: colissimoTrackingReference,
                hash: colissimoTrackingHash
            }
        }).fail(function (jqXHR, textStatus) {
            $('.colisismo-tracking-loader').fadeOut('slow', function () {
                $('.colissimo-error').fadeIn('slow');
            });
        }).success(function (data) {
            if (data.error) {
                $('.colisismo-tracking-loader').fadeOut('slow', function () {
                    $('.colissimo-error').fadeIn('slow');
                });
            } else {
                $('.colissimo-shipments').html(data.html_result);
                $('.colisismo-tracking-loader').fadeOut('slow', function () {
                    $('.colissimo-shipments').fadeIn('slow');
                    $(document).on('click', '.js-details-accordion', function () {
                        openCloseStepsDetails($(this));
                    });
                });
            }
        });
    }
});

$(document).on('click', '.colissimo-see-more', function () {

});