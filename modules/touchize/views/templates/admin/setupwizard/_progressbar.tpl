{*
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
 *}


<div class="bootstrap">
    <div class="row text">
        <div class="col-xs-12 text-right text-md-right">{l s='Step' mod='touchize'} {$current_step - 1|escape:'htmlall':'UTF-8'} {l s='of 5' mod='touchize'}</div>
    </div>
    <div class="progress">
        <div class="bar" role="progressbar" style="width:{(100/5) * ($current_step - 1)|escape:'htmlall':'UTF-8'}%;"></div>
    </div>
</div>