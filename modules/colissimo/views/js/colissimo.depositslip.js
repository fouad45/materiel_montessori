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

$(document).ready(function () {
    var barcodeInput = $('#colissimo-parcel-number-barcode');

    $('input[type="checkbox"]').change(function () {
        var tr = $(this).closest('tr');

        tr.toggleClass('selected');
    });

    barcodeInput.focus();

    barcodeInput.typeWatch({
        captureLength: 12,
        highlight: true,
        wait: 50,
        callback: function () {
            findLabel();
        }
    });

    $(document).on('click', '#submit-process-colissimo-deposit-slip', function (e) {
        e.preventDefault();
        var labelList = [];
        var chunks = [];
        var size = 50;
        var success = [];

        $('.colissimo-deposit-slip-table input.colissimo-label-selection[type=checkbox]').each(function () {
            var idLabel = parseInt(this.name.substr(16, this.name.length - 16)) || 0;

            if (this.checked) {
                labelList.push(idLabel);
            }
        });

        if (labelList.length !== 0) {
            $('.submit-block img').show(400);
            $('#deposit-slip-result').html('');
            $(this).toggleClass('disabled');
            while (labelList.length > 0) {
                chunks.push(labelList.splice(0, size));
            }
            processColissimoDepositSlip(chunks, success);
        } else {
            showErrorMessage(noLabelSelectedText);
        }
    });

    $(document).on('click', '.colissimo-select-all', function (e) {
        e.preventDefault();

        $('#colissimo-deposit-slip input[type="checkbox"]').prop('checked', true);
        $('.colissimo-deposit-slip-table tr').addClass('selected');
    });

    $(document).on('click', '.colissimo-unselect-all', function (e) {
        e.preventDefault();

        $('#colissimo-deposit-slip input[type="checkbox"]').prop('checked', false);
        $('.colissimo-deposit-slip-table tr').removeClass('selected');
    });
});
