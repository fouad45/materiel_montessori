/**
 * 2018 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 */
window.addEventListener('message', function (event) {
    if (event.data.id === 'tc_preview') {
        let selector = event.data.id;
        let style = document.querySelector('style#' + selector);
        if (style !== null) {
            style.remove();
        }
        style = document.createElement('style');
        style.id = selector;
        style.innerHTML = event.data.css;
        document.body.appendChild(style);
    }
});

let selector = window.location.href.split('#');
selector = selector.pop();
window.parent.postMessage({id: 'iframe_loaded', selector: selector}, '*');
