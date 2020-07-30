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

$(document).on('click', '.colissimo-migration-step1 button', function (e) {
    e.preventDefault();

    var btn = $(this);
    var migration = btn.attr('data-migrate');

    btn.toggleClass('disabled');
    btn.find('i').toggleClass('icon-spin icon-spinner');

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminColissimoMigration',
            ajax: 1,
            token: colissimoTokenMigration,
            action: 'startMigration',
            migrate: migration
        }
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericMigrationErrorMessage);
        btn.toggleClass('disabled');
        btn.find('i').toggleClass('icon-times icon-spin icon-spinner');
    }).success(function (data) {
        if (!data.migrate) {
            window.location.reload();
        } else {
            $('#colissimo-migration-content').fadeOut('slow', function () {
                $(this).html(data.html_result);
                $(this).fadeIn('slow');
                startMigration(modulesToMigrate);
            });
        }
    }).always(function (data) {
    });
});
